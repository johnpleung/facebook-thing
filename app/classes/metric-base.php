<?php
include __DIR__ . './../../vendor/autoload.php';
use JsonPath\JsonObject;

abstract class metric_base
{
    protected $data_raw;
    protected $data_parser;
    public $name;

    protected function parse_data($data_raw)
    {
        try {
            $this->data_parser = new JsonObject($data_raw);
            $this->data_raw = $data_raw;
            return true;
        } catch (Exception $err) {
            _log('error', [
                'message' => $err->getMessage(),
            ]);
        }
        return false;
    }

    protected function has_data()
    {
        return !is_null($this->data_parser);
    }

}
