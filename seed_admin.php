<?php
require_once __DIR__ . '/db.php';


$admins = [
    'admin',
    'admin_colombo',
    'admin_kandy',
    'admin_galle',
    'admin_jaffna',
    'srilankan_admin',
];

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

foreach ($admins as $username) {
    $stmt = $pdo->prepare('SELECT id FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $pdo->prepare('UPDATE admins SET password = ? WHERE id = ?');
        $stmt->execute([$hash, $exists['id']]);
        echo "Updated admin password for '$username'.\n";
    } else {
        $stmt = $pdo->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $hash]);
        echo "Created admin '$username' with password '$password'.\n";
    }
}

echo "Done. Please delete or secure this file after use.\n";
?>
