<?php
class metrics_group extends metric_base
{

    public $name;
    private $endpoint;
    private $metrics;
    private $required_columns;

    public function __construct($properties = null)
    {
        try {
            $this->name = $properties['name'];
            $this->endpoint = $properties['endpoint'];
            $this->metrics = $properties['metrics'];
            $this->required_columns = [];

            // Validate the schema itself. The columns needed for the "enum" metrics are not included in this validation as those require data to glean.
            if (!$this->validate_schema()) {
                throw new Exception('Schema is invalid');
            }
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
            ]);
            die($err->getMessage());
        }
    }

    private function validate_schema()
    {
        try {
            $columns = $this->get_required_columns();
            if (!$columns) {
                throw new Exception('No required columns detected for metrics group ' . $this->name);
            }
            foreach ($columns as &$column) {
                if (strlen($column['name']) > 64) {
                    throw new Exception('Column name "' . $column['name'] . '" exceeds the 64-character limit.');
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

    public function get_required_columns()
    {
        try {
            if ($this->has_data()) {
                return $this->required_columns;
            } else {
                $ret = [];
                if (isset($this->metrics) && count($this->metrics)) {
                    foreach ($this->metrics as &$metric) {
                        if (get_class($metric) == 'shallow_metric') {
                            $ret = array_merge($ret, $metric->get_required_columns());
                        }
                    }
                }
                return $ret;
            }
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
            ]);
        }
        return false;
    }

    private function analyze_results()
    {
        try {
            if (!$this->has_data()) {
                throw new Exception('No data to analyze');
            }

            $value = $this->data_parser->{'$..end_time'};
            if (!$value) {
                throw new Exception('Unable to read $..end_time');
            }
            $dates = array_unique($value, SORT_STRING);

            return [
                'unique_dates' => $dates,
                'num_days' => count($dates), // Note: this is assuming that the item in the same index of all metrics are returned with the same date/time
                'start_date' => $dates[0],
                'start_date_time' => strtotime($dates[0]),
                'end_date' => $dates[count($dates) - 1],
                'end_date_time' => strtotime($dates[count($dates) - 1]),
            ];
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
            ]);
        }
        return false;
    }

    public function get_data($user_name, $auth_token, $date_from, $date_to)
    {
        $data_raw = '';
        try {
            $endpoint = $this->endpoint;
            $endpoint = str_replace('{{AUTH_TOKEN}}', $auth_token, $endpoint);
            $endpoint = str_replace('{{USER_NAME}}', $user_name, $endpoint);
            $endpoint = str_replace('{{DATE_FROM}}', $date_from, $endpoint);
            $endpoint = str_replace('{{DATE_TO}}', $date_to, $endpoint);

            _log('log', [
                'user' => $user_name,
                'message' => 'Querying endpoint',
                'notes' => $endpoint,
            ]);

            $data_raw = @file_get_contents($endpoint);
            if ($data_raw == '' || !$this->parse_data($data_raw)) {
                throw new Exception('Failed to parse data results');
            }

            $data_info = $this->analyze_results();
            if (!$data_info) {
                throw new Exception('Failed to analyze results');
            }
            $num_days = $data_info['num_days'];

            $ret = [];
            if (isset($this->metrics) && count($this->metrics)) {

                $cumulative_required_columns = [];

                for ($i = 0; $i < $num_days; $i++) {
                    $day_metrics = [];

                    foreach ($this->metrics as &$metric) {
                        // Get a particular metric and its submetrics for a particular day
                        $metric_column_values = $metric->get_column_values($this->data_raw, $i);

                        if (!$metric_column_values) {
                            //throw new Exception('Unable to get values for metric ' . $metric->name);
                        } else {
                            if (isset($metric_column_values['name'])) {
                                $metric_column_values = [$metric_column_values];
                            }
                            $day_metrics = array_merge($day_metrics, $metric_column_values);
                            $cumulative_required_columns = array_merge($cumulative_required_columns, $metric->get_required_columns());
                        }
                    }
                    $date = date('Y-m-d H:i:s', strtotime('+' . $i . ' day', $data_info['start_date_time']));
                    $ret[$date] = $day_metrics;
                }

                $this->required_columns = array_merge_recursive($cumulative_required_columns, $this->required_columns);

                // Remove dupes
                $temp = [];
                foreach ($this->required_columns as &$column) {
                    $temp[$column['name']] = $column;
                }
                $this->required_columns = $temp;

            }
            return $ret;
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
                'notes' => $data_raw != '' ? $data_raw : 'No data',
            ]);
        }
        return false;
    }
}
