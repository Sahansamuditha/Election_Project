<?php
require_once __DIR__ . '/db.php';

$username = 'voter1';
$password = 'voterpass';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$exists = $stmt->fetch();

if ($exists) {
    $stmt = $pdo->prepare('UPDATE users SET password = ?, full_name = ? WHERE id = ?');
    $stmt->execute([$hash, 'Sample Voter', $exists['id']]);
    echo "Updated user '$username'.\n";
} else {
    $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $hash, 'Sample Voter', 'voter1@example.com']);
    echo "Created user '$username' with password '$password'.\n";
}

echo "Done. Delete this file after seeding or secure it.\n";
?>
