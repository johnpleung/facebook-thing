<?php
class account
{
    public function __construct($user_id, $auth_token_encrypted)
    {
        $this->user_id = $user_id;
        $this->auth_token = $auth_token_encrypted;
    }

    public function get_data(&$db_handler, $date_from, $date_to)
    {

        set_time_limit(600);

        global $settings;

        $metrics_groups = get_metrics_schema();
        $cumulative_data = [];

        foreach ($metrics_groups as &$metric_group) {
            try {

                $data = $metric_group->get_data($this->user_id, $this->auth_token, $date_from, $date_to);

                if (!$data) {
                    throw new Exception('No data for metrics group "' . $metric_group->name . '"');
                }
                $cumulative_data = array_merge_recursive($cumulative_data, $data);

                // Create any missing columns
                $columns = $metric_group->get_required_columns();

                if ($columns) {
                    if (!$db_handler->create_missing_required_columns('metrics', $columns)) {
                        throw new Exception('Unable to create missing required columns for metrics group "' . $metric_group->name . '"');
                    }
                } else {
                    throw new Exception('Unable to get required columns for metrics group "' . $metric_group->name . '"');
                }
            } catch (Exception $err) {
                _log('error', [
                    'user' => $this->user_id,
                    'message' => $err->getMessage(),
                ]);
                return false;
            }
        }

        $cumulative_statements = '';

        try {
            foreach ($cumulative_data as $date => $cols) {
                $values_csv = '';
                $column_names_csv = '';
                $update_csv = '';

                foreach ($cols as &$col) {
                    if (!is_null($col['value'])) {
                        $column_names_csv .= sprintf(", `%s`", $col['name']);
                        if ($col['sprintf_type'] == 'i') {
                            $values_csv .= sprintf(", %d", (string) $col['value']);
                        } else {
                            $values_csv .= sprintf(", '%s'", $col['value']);
                        }

                        $update_csv_partial = ", `%s` = ";
                        if ($col['sprintf_type'] == 'i') {
                            $update_csv_partial .= '%d';
                        } else {
                            $update_csv_partial .= "'%s'";
                        }
                        $update_csv .= sprintf($update_csv_partial, $col['name'], $col['value']);
                    }
                }
                $values_csv = substr($values_csv, 2); // Trim off leading ", "
                $column_names_csv = substr($column_names_csv, 2); // Trim off leading ", "
                $update_csv = substr($update_csv, 2); // Trim off leading ", "

                $statement = "INSERT INTO " . $settings['db']['table_names']['metrics'] . " (`user_id`, `date`, " . $column_names_csv . ") VALUES ('" . $this->user_id . "', '" . $date . "', " . $values_csv . ") ON DUPLICATE KEY UPDATE " . $update_csv;

                $result = $db_handler->query($statement);
                if (!$result) {
                    if (isset($this->db)) {
                        throw new Exception('Failed to insert row for ' . $date . ': ' . $this->db->error);
                    } else {
                        throw new Exception('Failed to insert row for ' . $date);
                    }
                }
            }
        } catch (Exception $err) {
            _log('error', [
                'user' => $this->user_id,
                'message' => $err->getMessage(),
            ]);
            return false;
        }

        return true;

    }
}
