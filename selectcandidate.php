<?php
require_once __DIR__ . '/db.php';
session_start();

// Require login
if (empty($_SESSION['user_id'])) {
    header('Location: userlogin.php');
    exit;
}

$party_id = $_SESSION['selected_party_id'] ?? null;
$party_name = null;
if ($party_id) {
    $stmt = $pdo->prepare('SELECT name FROM parties WHERE id = ? LIMIT 1');
    $stmt->execute([$party_id]);
    $party_name = $stmt->fetchColumn();
}

// Load logged-in user details for display
$user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT id, fullname, nic, age, address FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Always show the full list of candidates regardless of selected party
$candidates = null;
if ($party_id) {
    $stmt = $pdo->prepare('SELECT * FROM candidates WHERE party_id = ? ORDER BY id');
    $stmt->execute([$party_id]);
    $candidates = $stmt->fetchAll();
} else {
    $candidates = $pdo->query('SELECT * FROM candidates ORDER BY id')->fetchAll();
}
?>

<html>
<head>
    <title>Sri Lankan Election Voting System - Select Candidate</title>
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
            background: #8B1538;
            border-color: #8B1538;
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
            padding: 30px;
        }

        .party-banner {
            background: linear-gradient(135deg, #fffbf0 0%, #fff9e6 100%);
            border: 1px solid #ffe8a1;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .party-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #006747 0%, #008557 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .party-info {
            flex: 1;
        }

        .party-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 4px;
        }

        .party-name {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 600;
        }

        .voter-banner {
            background: #e8f8f5;
            border: 1px solid #a8e6cf;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .voter-label {
            font-size: 13px;
            color: #16a085;
            font-weight: 500;
        }

        .voter-name {
            font-size: 13px;
            color: #16a085;
            font-weight: 600;
        }

        .section-title {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .candidate-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .candidate-item {
            background: white;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 18px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .candidate-item:hover {
            border-color: #27ae60;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.15);
        }

        .candidate-item.selected {
            border-color: #27ae60;
            background: #f0fdf7;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.2);
        }

        .candidate-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid #FFB81C;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .candidate-item.selected .candidate-number {
            border-color: #27ae60;
            background: #27ae60;
            color: white;
        }

        .number-text {
            font-size: 18px;
            font-weight: 700;
            color: #8B1538;
        }

        .candidate-item.selected .number-text {
            color: white;
        }

        .candidate-name {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .back-button {
            padding: 14px 20px;
            background: white;
            color: #7f8c8d;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background: #f8f9fa;
            border-color: #bdc3c7;
        }

        .continue-button {
            padding: 14px;
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            opacity: 0.5;
            pointer-events: none;
        }

        .continue-button.active {
            opacity: 1;
            pointer-events: auto;
        }

        .continue-button.active:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .help-text {
            text-align: center;
            font-size: 13px;
            color: #7f8c8d;
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

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 20px;
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
                    <span class="value">Priya Wickramasinghe</span>
                </div>
                <div class="info-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="#7f8c8d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="10" r="3" stroke="#7f8c8d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="label">Division:</span>
                    <span class="value">Kandy District</span>
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
                <div class="step completed">
                    <div class="step-number">4</div>
                    <div class="step-label">Party</div>
                </div>
                <div class="step active">
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
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="7" r="4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="alert-text">
                    <h2>Select Your Candidate</h2>
                    <p>Choose your preferred candidate<?= $party_name ? ' from ' . h($party_name) : '' ?></p>
                </div>
            </div>

            <div class="card-body">
                <!-- Party Banner -->
                <div class="party-banner">
                    <div class="party-logo">
                        <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L4 6V12C4 16.55 7.16 20.74 12 22C16.84 20.74 20 16.55 20 12V6L12 2Z" fill="white"/>
                        </svg>
                    </div>
                    <div class="party-info">
                        <div class="party-label">Selected Party</div>
                        <div class="party-name"><?= $party_name ? h($party_name) : 'All Parties' ?></div>
                    </div>
                </div>

                <!-- Voter Banner -->
                <div class="voter-banner">
                    <span class="voter-label">Voting as:</span>
                    <span class="voter-name"><?php echo $user ? h($user['fullname']) : 'Sample Voter'; ?></span>
                </div>

                <!-- Section Title -->
                <div class="section-title">Select one candidate:</div>

                <!-- Candidate List -->
                <form method="post" action="confirme.php">
                    <div class="candidate-list">
                        <?php foreach ($candidates as $c): ?>
                            <label class="candidate-item" onclick="selectCandidate(this)">
                                <input type="radio" name="candidate_id" value="<?= $c['id'] ?>" style="display:none">
                                <div class="candidate-number" aria-hidden="true">
                                    <span class="number-text"><?= str_pad($c['id'],2,'0',STR_PAD_LEFT) ?></span>
                                </div>
                                <div class="candidate-name"><?= h($c['name']) ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a class="back-button" href="selectparty.php">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="19" y1="12" x2="5" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="12 19 5 12 12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Parties
                        </a>
                        <button class="continue-button" id="continueBtn" type="submit" disabled>
                            Submit Vote
                        </button>
                    </div>
                </form>

                <!-- Help Text -->
                <div class="help-text">Please select a candidate to continue</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Powered by Election Commission of Sri Lanka</p>
            <p>Secure • Transparent • Democratic</p>
        </div>
    </div>

    <script>
        function selectCandidate(item) {
            // Deselect others
            document.querySelectorAll('.candidate-item').forEach(c => {
                c.classList.remove('selected');
            });

            // Select clicked
            item.classList.add('selected');

            // Check the hidden radio input
            const input = item.querySelector('input[type=radio]');
            if (input) {
                input.checked = true;
            }

            // Enable continue button (both visually and functionally)
            const continueBtn = document.getElementById('continueBtn');
            continueBtn.classList.add('active');
            continueBtn.disabled = false;
        }

        // Optional: enable keyboard navigation (Enter to submit when a candidate is focused)
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const selected = document.querySelector('.candidate-item.selected');
                if (selected) {
                    document.getElementById('continueBtn').click();
                }
            }
        });
    </script>

</body>
</html>
