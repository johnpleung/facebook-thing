<?php
function _log($level, $info)
{
    try {
        global $db;

        $stacktrace = debug_backtrace();
        $origin = null;
        if (isset($stacktrace[1])) {
            $origin = $stacktrace[1]['function'];
        }

        $info = array_merge([
            'message' => null,
            'origin' => $origin,
            'user' => null,
            'stacktrace' => json_encode(debug_backtrace()),
            'notes' => null,
        ], $info);

        $db->_log($level, $info);
    } catch (Exception $err) {}
}
