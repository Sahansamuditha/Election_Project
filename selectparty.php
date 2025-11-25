<?php
require_once __DIR__ . '/db.php';
session_start();

// Require login by NIC-based session
if (empty($_SESSION['user_id'])) {
    header('Location: userlogin.php');
    exit;
}

$parties = $pdo->query('SELECT * FROM parties ORDER BY id')->fetchAll();
// Helper to return inline SVG/logo HTML for known parties
function party_logo_html($name) {
    $n = strtolower($name);
    if (strpos($n, 'united national') !== false) {
        return '<div class="party-logo logo-green" aria-hidden="true"><svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="white"/><path d="M6 12h12" stroke="#006747" stroke-width="2" stroke-linecap="round"/></svg></div>';
    }
    if (strpos($n, 'podujana') !== false || strpos($n, 'podujana peramuna') !== false) {
        return '<div class="party-logo logo-maroon" aria-hidden="true"><svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="18" rx="4" fill="white"/><path d="M7 12h10M12 7v10" stroke="#8B1538" stroke-width="2" stroke-linecap="round"/></svg></div>';
    }
    if (strpos($n, 'samagi') !== false) {
        return '<div class="party-logo logo-orange" aria-hidden="true"><svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="white"/><path d="M8 9l4 6 4-6" stroke="#FF6B35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>';
    }
    if (strpos($n, 'jathika') !== false || strpos($n, 'jathika jana') !== false) {
        return '<div class="party-logo logo-yellow" aria-hidden="true"><svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="white"/><path d="M12 6v12M6 12h12" stroke="#FFB81C" stroke-width="2" stroke-linecap="round"/></svg></div>';
    }
    // Default: initial letter
    return '<div class="party-logo logo-green">' . htmlspecialchars(substr($name,0,1)) . '</div>';
}
?>

<?php
// Ensure logged-in user is loaded (fix undefined variable warnings)
$user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT id, fullname, nic, age, address FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Load admin info if available (safe fallback)
$admin = null;
try {
    $admin = $pdo->query('SELECT name, division FROM admins LIMIT 1')->fetch();
} catch (Exception $e) {
    $admin = null;
}
?>

<html>
<head>
    
    <title>Sri Lankan Election Voting System - Select Party</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef3 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .color-bar {
            height: 4px;
            width: 250px;
            background: linear-gradient(to right, #8B1538 0%, #8B1538 33%, #FFB81C 33%, #FFB81C 50%, #FF6B35 50%, #FF6B35 66%, #006747 66%, #006747 100%);
            margin: 0 auto 15px;
            border-radius: 2px;
        }

        .header h1 {
            font-size: 26px;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .info-bar {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .info-left {
            display: flex;
            gap: 25px;
            align-items: center;
            font-size: 14px;
            color: #2c3e50;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item .label {
            color: #7f8c8d;
        }

        .info-item .value {
            font-weight: 500;
        }

        .end-session-btn {
            background: white;
            border: 1px solid #e74c3c;
            color: #e74c3c;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .end-session-btn:hover {
            background: #e74c3c;
            color: white;
        }

        .progress-steps {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            background: white;
            border: 2px solid #e1e8ed;
            color: #95a5a6;
        }

        .step.completed .step-number {
            background: #8B1538;
            border-color: #8B1538;
            color: white;
        }

        .step.active .step-number {
            background: #006747;
            border-color: #006747;
            color: white;
        }

        .step-label {
            font-size: 12px;
            color: #7f8c8d;
            text-align: center;
        }

        .step.active .step-label,
        .step.completed .step-label {
            color: #2c3e50;
            font-weight: 500;
        }

        .voting-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .alert-header {
            background: linear-gradient(135deg, #8B1538 0%, #a01d47 100%);
            color: white;
            padding: 18px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .alert-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .alert-text h2 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .alert-text p {
            font-size: 13px;
            opacity: 0.95;
        }

        .card-body {
            padding: 35px;
        }

        .voter-banner {
            background: #fffbf0;
            border: 1px solid #ffe8a1;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .voter-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #FFB81C 0%, #ffc83c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .voter-info-text {
            flex: 1;
        }

        .voter-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 3px;
        }

        .voter-name {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 600;
        }

        .instruction-text {
            text-align: center;
            font-size: 15px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .party-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .party-card {
            background: white;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .party-card:hover {
            border-color: #006747;
            box-shadow: 0 4px 12px rgba(0, 103, 71, 0.15);
            transform: translateY(-2px);
        }

        .party-card.selected {
            border-color: #006747;
            background: #f0fdf7;
            box-shadow: 0 4px 12px rgba(0, 103, 71, 0.2);
        }

        .party-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
        }

        .logo-green {
            background: linear-gradient(135deg, #006747 0%, #008557 100%);
        }

        .logo-maroon {
            background: linear-gradient(135deg, #8B1538 0%, #a01d47 100%);
        }

        .logo-orange {
            background: linear-gradient(135deg, #FF6B35 0%, #ff8555 100%);
        }

        .logo-yellow {
            background: linear-gradient(135deg, #FFB81C 0%, #ffc83c 100%);
        }

        .party-name {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 600;
        }

        .continue-notice {
            background: #e8f8f5;
            border: 2px solid #27ae60;
            border-radius: 8px;
            padding: 15px 20px;
            text-align: center;
            color: #16a085;
            font-size: 14px;
            font-weight: 500;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            color: #7f8c8d;
            font-size: 12px;
        }

        .footer p {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .info-bar {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .info-left {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
                width: 100%;
            }

            .end-session-btn {
                width: 100%;
                justify-content: center;
            }

            .party-grid {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="color-bar"></div>
            <h1>Sri Lankan Election System</h1>
            <p>Secure Digital Voting Platform</p>
        </div>

        <!-- Info Bar -->
        <div class="info-bar">
            <div class="info-left">
                <div class="info-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="#7f8c8d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="7" r="4" stroke="#7f8c8d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="label">Admin:</span>
                    <span class="value"><?php echo $admin ? h($admin['name']) : 'Priya Wickramasinghe'; ?></span>
                </div>
                <div class="info-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="#7f8c8d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="10" r="3" stroke="#7f8c8d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="label">Division:</span>
                    <span class="value"><?php echo $admin ? h($admin['division']) : 'Kandy District'; ?></span>
                </div>
            </div>
            <button class="end-session-btn" onclick="window.location.href='userlogin.php'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="16 17 21 12 16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                End Session
            </button>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="steps">
                <div class="step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">ID</div>
                </div>
                <div class="step completed">
                    <div class="step-number">2</div>
                    <div class="step-label">Details</div>
                </div>
                <div class="step completed">
                    <div class="step-number">3</div>
                    <div class="step-label">Fingerprint</div>
                </div>
                <div class="step active">
                    <div class="step-number">4</div>
                    <div class="step-label">Party</div>
                </div>
                <div class="step">
                    <div class="step-number">5</div>
                    <div class="step-label">Candidate</div>
                </div>
                <div class="step">
                    <div class="step-number">6</div>
                    <div class="step-label">Confirm</div>
                </div>
            </div>
        </div>

        <!-- Voting Card -->
        <div class="voting-card">
            <div class="alert-header">
                <div class="alert-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="alert-text">
                    <h2>Select Political Party</h2>
                    <p>Choose the party you wish to vote for</p>
                </div>
            </div>

        

            <div class="card-body">
                <!-- Voter Banner -->
                <div class="voter-banner">
                    <div class="voter-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="7" r="4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="voter-info-text">
                        <div class="voter-label">Voting as</div>
                        <span class="voter-name"><?php echo $user ? h($user['fullname']) : 'Sample Voter'; ?></span>
                    </div>
                </div>

                <!-- Instruction -->
                <div class="instruction-text">
                    Please select your preferred party
                </div>

                <!-- Party Grid -->
                <?php if (empty($parties)): ?>
                    <div style="padding:30px;text-align:center;color:#7f8c8d;">
                        <p style="margin-bottom:12px">No parties available yet.</p>
                        <p style="margin-bottom:18px">If you haven't imported the database, run the SQL seed or click the button below to add default parties and candidates.</p>
                        <a href="seed_parties.php" style="display:inline-block;padding:12px 18px;background:#006747;color:#fff;border-radius:8px;text-decoration:none;">Seed Default Parties</a>
                    </div>
                <?php else: ?>
                    <form method="post" action="set_party.php">
                        <div class="party-grid">
                            <?php foreach ($parties as $p): ?>
                                <label class="party-card" data-id="<?= $p['id'] ?>" onclick="markSelected(this)">
                                    <input type="radio" name="party_id" value="<?= $p['id'] ?>" style="display:none">
                                    <?= party_logo_html($p['name']) ?>
                                    <div class="party-name"><?= h($p['name']) ?></div>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- selection submits immediately on click -->
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Powered by Election Commission of Sri Lanka</p>
            <p>Secure • Transparent • Democratic</p>
        </div>
    </div>

    <script>
        function markSelected(card) {
            // remove selected class from all cards
            document.querySelectorAll('.party-card').forEach(c => c.classList.remove('selected'));
            // mark this card selected
            card.classList.add('selected');
            // check its radio
            const input = card.querySelector('input[type="radio"]');
            if (input) input.checked = true;
            // submit the parent form to set the party and move to candidates
            const form = card.closest('form');
            if (form) {
                // small delay to allow UI update
                setTimeout(() => form.submit(), 150);
            }
        }
    </script>

</body>
</html>