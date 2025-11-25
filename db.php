<?php

$DB_HOST = '127.0.0.1';
$DB_NAME = 'voting_app';
$DB_USER = 'root';
$DB_PASS = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, $options);
} catch (Exception $e) {
    error_log($e->getMessage());
    exit('Database connection error');
}

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
