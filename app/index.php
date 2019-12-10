<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . './../app/includes/settings.php';
include __DIR__ . './../app/includes/db.php';
include __DIR__ . './../app/includes/common.php';
include __DIR__ . './../app/includes/metrics-schema.php';
include __DIR__ . './../app/classes/account.php';
include __DIR__ . './../app/classes/data-definition.php';
include __DIR__ . './../app/classes/metric-base.php';
include __DIR__ . './../app/classes/metrics-group.php';
include __DIR__ . './../app/classes/shallow-metric.php';
include __DIR__ . './../app/classes/enum-metric.php';

set_time_limit(180);
date_default_timezone_set($settings['timezone']);

// Run
$date_to = strtotime(date('Y-m-d'));
$date_from = date('Y-m-d', strtotime('-' . $settings['app']['days_to_query'] . ' day', $date_to));

echo (date('Y-m-d h:i:s A') . " - Running...\n");

$db = new db_handler($settings['db']);
if (!$db) {
    die('Could not connect to the database');
}
$db->init($settings['timezone']);

try {
    $temp = $db->get_active_accounts();
    if (isset($temp['data']) && count($temp['data'])) {
        foreach ($temp['data'] as &$account) {
            $account->get_data($db, $date_from, $date_to);
        }
    } else {
        echo ('Could not get active account, or no active accounts to process');
    }
} catch (Exception $err) {
    _log('error', [
        'message' => 'Unexpected error',
        'notes' => $err->getMessage(),
    ]);
}

$db->close();
die(date('Y-m-d h:i:s A') . " - Done\n");