<?php
require_once __DIR__ . '/db.php';

$username = 'voter1';
$password = 'voterpass';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$exists = $stmt->fetch();



echo "Done. Delete this file after seeding or secure it.\n";
?>
