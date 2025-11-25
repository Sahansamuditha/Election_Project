<?php
require_once __DIR__ . '/db.php';

// Simple seeder to add default parties and candidates if table is empty
$existing = $pdo->query('SELECT COUNT(*) as c FROM parties')->fetchColumn();
if ($existing > 0) {
    echo "Parties already exist.\n";
    echo "<p><a href=\"selectparty.php\">Back to party selection</a></p>";
    exit;
}

$pdo->beginTransaction();
try {
    $parties = [ 'United National Party', 'Sri Lanka Podujana Peramuna', 'Samagi Jana Balawegaya', 'Jathika Jana Balawegaya' ];
    $stmtP = $pdo->prepare('INSERT INTO parties (name) VALUES (?)');
    $stmtC = $pdo->prepare('INSERT INTO candidates (party_id, name) VALUES (?, ?)');

    foreach ($parties as $i => $name) {
        $stmtP->execute([$name]);
        $pid = $pdo->lastInsertId();
        // Add one sample candidate per party
        $stmtC->execute([$pid, $name . ' Candidate 1']);
        $stmtC->execute([$pid, $name . ' Candidate 2']);
    }

    $pdo->commit();
    echo "Seeded default parties and candidates.\n";
    echo "<p><a href=\"selectparty.php\">Go to party selection</a></p>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error seeding: " . htmlspecialchars($e->getMessage());
}
?>
