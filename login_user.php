<?php
require_once __DIR__ . '/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: userlogin.php');
    exit;
}

$nic = trim($_POST['nic'] ?? '');
if ($nic === '') {
    header('Location: userlogin.php?error=1');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE nic = ? LIMIT 1');
$stmt->execute([$nic]);
$user = $stmt->fetch();

if ($user) {
    
    $_SESSION['user_id'] = $user['id'];
    header('Location: userdisplay.php');
    exit;
}

header('Location: userlogin.php?error=1');
exit;
?>
