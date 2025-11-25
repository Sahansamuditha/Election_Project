<?php
require_once __DIR__ . '/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: selectparty.php');
    exit;
}

$party_id = $_POST['party_id'] ?? null;
if (!$party_id) {
    header('Location: selectparty.php');
    exit;
}

// store selected party in session and redirect to candidate selection
$_SESSION['selected_party_id'] = (int)$party_id;
header('Location: selectcandidate.php');
exit;
?>
