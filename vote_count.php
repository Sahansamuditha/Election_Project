<?php
require_once __DIR__ . '/db.php';
session_start();

// Require admin login
if (empty($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit;
}

// Ensure only the seeded admin user can access this page
$aStmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
$aStmt->execute([$_SESSION['admin_id']]);
$currentAdmin = $aStmt->fetch();
if (!$currentAdmin || ($currentAdmin['username'] ?? '') !== 'srilankan_admin') {
    // Not authorized to view vote totals
    header('Location: adminlogin.php');
    exit;
}

// Fetch party vote counts
$partyStmt = $pdo->query(
    'SELECT p.id, p.name, COUNT(v.id) AS votes
     FROM parties p
     LEFT JOIN votes v ON v.party_id = p.id
     GROUP BY p.id
     ORDER BY votes DESC, p.name'
);
$parties = $partyStmt->fetchAll();

// Fetch candidate vote counts
$candStmt = $pdo->query(
    'SELECT c.id, c.name, c.party_id, p.name AS party_name, COUNT(v.id) AS votes
     FROM candidates c
     LEFT JOIN votes v ON v.candidate_id = c.id
     LEFT JOIN parties p ON c.party_id = p.id
     GROUP BY c.id
     ORDER BY votes DESC, c.id'
);
 $candidates = $candStmt->fetchAll();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin — Vote Totals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <div class="color-bar"></div>
        <h1>Vote Totals</h1>
        <p>Totals by Party and Candidate</p>
    </div>

    <div class="card-body">
        <h2>By Party</h2>
        <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Party</th>
                    <th style="text-align:right;padding:8px;border-bottom:1px solid #eee">Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parties as $p): ?>
                    <tr>
                        <td style="padding:8px;border-bottom:1px solid #f5f5f5"><?php echo h($p['name']); ?></td>
                        <td style="padding:8px;border-bottom:1px solid #f5f5f5;text-align:right"><?php echo (int)$p['votes']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>By Candidate</h2>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Candidate</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Party</th>
                    <th style="text-align:right;padding:8px;border-bottom:1px solid #eee">Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $c): ?>
                    <tr>
                        <td style="padding:8px;border-bottom:1px solid #f5f5f5"><?php echo h($c['name']); ?></td>
                        <td style="padding:8px;border-bottom:1px solid #f5f5f5"><?php echo h($c['party_name'] ?: '—'); ?></td>
                        <td style="padding:8px;border-bottom:1px solid #f5f5f5;text-align:right"><?php echo (int)$c['votes']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="margin-top:18px"><a href="adminlogin.php">Back to admin login</a></p>
    </div>
</div>
</body>
</html>
