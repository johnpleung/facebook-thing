<?php
class db_handler
{

    private $settings;
    private $db;

    public function __construct($settings)
    {
        try {
            $this->settings = $settings;
            $this->db = new mysqli($this->settings['host'], $this->settings['user_name'], $this->settings['user_password'], $this->settings['name'], $this->settings['port']);
            if ($this->db->connect_errno) {
                throw new Exception($this->db->connect_errno);
            }
        } catch (Exception $err) {
            die('Could not connect to the database: ' . $err->getMessage());
        }
    }

    public function init($timezone)
    {
        try {
            $this->db->set_charset('utf8mb4');
            $this->db->query("SET time_zone = '" . $timezone . "';");

            $statement = "SHOW TABLES LIKE '" . $this->settings['table_names']['metrics'] . "'";

            $query = $this->db->query($statement);

            if (!$query->num_rows) {
                if (!$this->reset_metrics_table()) {
                    throw new Exception('Failed to init database; was unable to create metrics table');
                }
            }
            return true;
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
            ]);
        }
        return false;
    }

    private function reset_metrics_table()
    {
        $statement_create = "CREATE TABLE `" . $this->settings['table_names']['metrics'] . "` (  `user_id` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL, `date` datetime NOT NULL, PRIMARY KEY (`user_id`,`date`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        try {

            $statement = 'DROP TABLE IF EXISTS ' . $this->settings['table_names']['metrics'];
            $cmd = $this->db->prepare($statement);

            if (!$cmd || !$cmd->execute()) {
                _log('error', [
                    'message' => 'Could not drop metrics table',
                    'notes' => $statement,
                ]);
            }

            $cmd = $this->db->prepare($statement_create);

            if (!$cmd || !$cmd->execute()) {
                _log('error', [
                    'message' => 'Could not create metrics table',
                    'notes' => $statement_create,
                ]);
            }
            return true;
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
            ]);
        }
        return false;
    }

    private function get_table_columns($table_id)
    {

        $ret = '';
        $statement = 'SELECT COLUMN_NAME FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="' . $this->settings['name'] . '" AND `TABLE_NAME`="' . $this->settings['table_names'][$table_id] . '";';

        try {

            $cmd = $this->db->prepare($statement);
            $cmd->execute();

            if ($cmd->bind_result($column)) {
                while ($cmd->fetch()) {
                    $ret .= '|' . $column;
                }
                $cmd->close();
                $ret .= '|';

                return $ret;
            } else {
                throw new Exception($this->db->error);
            }
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
                'notes' => $statement,
            ]);
        }

        return null;

    }

    private function add_column($table_id, $name, $declaration)
    {
        $statement = 'ALTER TABLE `' . $this->settings['table_names'][$table_id] . '` ADD COLUMN `' . $name . '` ' . $declaration;
        try {
            $result = $this->db->query($statement);
            return $result == 1;
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
                'notes' => $statement,
            ]);
            return false;
        }
    }

    public function create_missing_required_columns($table_id, $required_columns)
    {
        try {
            $existing_columns = $this->get_table_columns($table_id);
            foreach ($required_columns as &$column) {
                if (strpos($existing_columns, '|' . $column['name'] . '|') === false) {
                    $result = $this->add_column($table_id, $column['name'], $column['declaration']);
                    if (!$result) {
                        throw new Exception('Failed to add column `' . $column['name'] . '`');
                    } else {
                        _log('log', [
                            'message' => 'Added column `' . $column['name'] . '`',
                        ]);
                    }
                }
            }
            return true;
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
            ]);
        }
        return false;
    }

    public function _log($level, $info)
    {
        try {
            $cmd = $this->db->prepare("INSERT INTO " . $this->settings['table_names']['log'] . " (`date`, `level`, `message`, `origin`, `user`, `stacktrace`, `notes`) VALUES(NOW(), ?, ?, ?, ?, ?, ?)");
            if (!$cmd) {
                die($this->db->error);
            } else {
                $cmd->bind_param('ssssss', $level, $info['message'], $info['origin'], $info['user'], $info['stacktrace'], $info['notes']);
                $cmd->execute();
            }
        } catch (Exception $err) {

        }
    }

    public function query($statement)
    {
        try {
            $result = $this->db->multi_query($statement);
            if (!$result) {
                throw new Exception($this->db->error);
            } else {
                return true;
            }
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
                'notes' => $statement,
            ]);
        }
        return false;
    }

    public function get_active_accounts()
    {

        $ret = [
            'success' => false,
        ];

        $statement = 'SELECT id, auth_token_encrypted FROM ' . $this->settings['table_names']['users'] . ' WHERE is_active = 1 ORDER BY id ASC';
        try {
            $accounts = [];
            if ($result = $this->db->query($statement)) {

                while ($row = $result->fetch_assoc()) {
                    $auth_token = null;
                    try {
                        $auth_token = $this->decrypt_string($row['auth_token_encrypted'], $this->get_unique_iv($row['id']));
                        if ($auth_token == '') {
                            throw new Exception('');
                        }
                    } catch (Exception $err) {
                        throw new Exception('Failed to decrypt password for user ' . $row['id']);
                    }
                    $accounts[] = new account($row['id'], $auth_token);
                }
                $result->free();

                $ret = [
                    'success' => true,
                    'data' => $accounts,
                ];

            } else {
                throw new Exception('Query failed');
            }
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
                'notes' => $statement,
            ]);
        }

        return $ret;
    }

    public function decrypt_string($string, $unique_iv_prefix)
    {
        return $this->encrypt_decrypt($string, 'd', $unique_iv_prefix);
    }

    public function encrypt_string($string, $unique_iv_prefix)
    {
        return $this->encrypt_decrypt($string, 'e', $unique_iv_prefix);
    }

    private function encrypt_decrypt($string, $action = 'e', $unique_iv_prefix = null)
    {
        // From http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
        // $unique_iv_prefix must be 4 chrs
        $secret_key = $this->settings['encryption_key'];
        $secret_iv = $this->settings['encryption_iv'] . $unique_iv_prefix;

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'e') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else if ($action == 'd') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function close()
    {
        try {
            $this->db->kill($this->db->thread_id);
            $this->db->close();
        } catch (Exception $err) {

        }
    }

    private function get_unique_iv($id)
    {
        return substr(md5($id), -4);
    }

    public function add_account($id, $name, $auth_token)
    {
        try {
            $cmd = $this->db->prepare("INSERT INTO " . $this->settings['table_names']['users'] . " (`id`, `name`, `is_active`, `auth_token_encrypted`) VALUES(?, ?, 1, ?)");
            if (!$cmd) {
                die($this->db->error);
            } else {
                $auth_token_encrypted = $this->encrypt_string($auth_token, $this->get_unique_iv($id));
                $cmd->bind_param('sss', $id, $name, $auth_token_encrypted);
                if (!$cmd->execute()) {
                    die($this->db->error);
                } else {
                    return true;
                }
            }
        } catch (Exception $err) {
            die($err->getMessage());
        }
    }

}
