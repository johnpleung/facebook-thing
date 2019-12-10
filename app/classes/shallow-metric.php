<?php
class shallow_metric extends metric_base
{
    private $definition;

    public function __construct($properties = null)
    {

        if (isset($properties['name'])) {
            $this->name = $properties['name'];
        }

        if (isset($properties['path'])) {
            $this->path = $properties['path'];
        }

        if (isset($properties['definition'])) {
            $this->definition = $properties['definition'];
        }

    }

    public function get_required_columns()
    {
        return [[
            'declaration' => $this->definition->col_declaration,
            'name' => $this->definition->col_name,
        ]];
    }

    public function get_column_values($data_raw, $index = null)
    {
        // Prepare an array or array of arrays that represents data to be included in a database row to be written. Return value includes column name, column value, and the sprintf type of the value, used for the SQL statement

        try {
            // Parse data passed in
            if (!$this->parse_data($data_raw)) {
                throw new Exception('Could not parse data for shallow metric ' . $this->name);
            }

            // Parse value from data
            $path = $this->definition->path;
            if (isset($index)) {
                $path = str_replace('{{INDEX}}', $index, $path);
            }
            $value = $this->data_parser->{$path};
            if (!$value && !isset($value[0])) {
                //throw new Exception('Could not get value for shallow metric ' . $this->name);
                return false;
            }

            return [
                'name' => $this->definition->col_name,
                'value' => $value[0],
                'sprintf_type' => $this->definition->sprintf_type,
            ];
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
                'notes' => $data_raw,
            ]);
        }

        return false;
    }

}
