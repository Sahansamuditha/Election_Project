<?php
require_once __DIR__ . '/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adminlogin.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
$stmt->execute([$username]);
$admin = $stmt->fetch();

if ($admin) {
    $stored = $admin['password'];
    $ok = false;
    if (password_verify($password, $stored)) {
        $ok = true;
    } elseif ($stored === $password) {
        // Legacy plaintext password in DB — accept it and upgrade to hashed
        $ok = true;
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $u = $pdo->prepare('UPDATE admins SET password = ? WHERE id = ?');
        $u->execute([$newHash, $admin['id']]);
    }

    if ($ok) {
        $_SESSION['admin_id'] = $admin['id'];
        // Only the special admin user should go to the vote totals page
        if (isset($admin['username']) && $admin['username'] === 'srilankan_admin') {
            header('Location: vote_count.php');
        } else {
            header('Location: userlogin.php');
        }
        exit;
    }
}

header('Location: adminlogin.php?error=1');
exit;
?>
