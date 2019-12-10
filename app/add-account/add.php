<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id']) && isset($_GET['name']) && isset($_GET['token'])) {
    $id = trim($_GET['id']);
    $name = trim($_GET['name']);
    $token = trim($_GET['token']);
} else {
    die('Please provide a "name" and a "token".');
}

require realpath(__DIR__ . '/../includes/settings.php');
require realpath(__DIR__ . '/../includes/db.php');

date_default_timezone_set($settings['timezone']);

$db = new db_handler($settings['db']);
if (!$db) {
    die('Could not connect to the database');
}
$db->init($settings['timezone']);

if ($db->add_account($id, $name, $token)) {
    echo ('Success');
}

$db->close();