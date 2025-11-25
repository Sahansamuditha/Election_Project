<?php
require_once __DIR__ . '/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: selectcandi.php');
    exit;
}

if (empty($_SESSION['user_id'])) {
    header('Location: userlogin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$candidate_id = $_POST['candidate_id'] ?? null;
if (!$candidate_id) {
    header('Location: selectcandi.php');
    exit;
}

// Check if user already voted
$stmt = $pdo->prepare('SELECT id FROM votes WHERE user_id = ?');
$stmt->execute([$user_id]);
if ($stmt->fetch()) {
    header('Location: final.php');
    exit;
}

// Insert vote
$party_id = $_SESSION['selected_party_id'] ?? null;
$stmt = $pdo->prepare('INSERT INTO votes (user_id, candidate_id, party_id) VALUES (?, ?, ?)');
$stmt->execute([$user_id, $candidate_id, $party_id]);

// Clear selected_party to avoid confusion
unset($_SESSION['selected_party_id']);

header('Location: final.php');
exit;
?>
