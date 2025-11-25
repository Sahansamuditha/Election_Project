
<?php
require_once __DIR__ . '/db.php';
session_start();


$user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT id, fullname, nic, age, address FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}


$admin = null;
try {
    $admin = $pdo->query('SELECT name, division FROM admins LIMIT 1')->fetch();
} catch (Exception $e) {
    $admin = null;
}
?>

<html>
<head>
    
    <title>Sri Lankan Election Voting System - Fingerprint</title>
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
            background: #FF6B35;
            border-color: #FF6B35;
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

        .verifying-banner {
            background: #fffbf0;
            border: 1px solid #ffe8a1;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 35px;
        }

        .verifying-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .verifying-name {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 600;
        }

        .fingerprint-section {
            text-align: center;
            padding: 40px 20px;
        }

        .fingerprint-circle {
            width: 160px;
            height: 160px;
            margin: 0 auto 30px;
            border: 4px solid #FFB81C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255, 184, 28, 0.4);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(255, 184, 28, 0);
            }
        }

        .fingerprint-icon {
            font-size: 80px;
            color: #FFB81C;
        }

        .instruction-text h3 {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .instruction-text p {
            font-size: 14px;
            color: #7f8c8d;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 15px;
            margin-top: 35px;
        }

        .back-button {
            padding: 14px;
            background: white;
            color: #7f8c8d;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .back-button:hover {
            background: #f8f9fa;
            border-color: #bdc3c7;
        }

        .scan-button {
            padding: 14px;
            background: linear-gradient(135deg, #FF6B35 0%, #ff8555 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .scan-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
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
                padding: 25px 20px;
            }

            .fingerprint-circle {
                width: 140px;
                height: 140px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">

        <div class="header">
            <div class="color-bar"></div>
            <h1>Sri Lankan Election System</h1>
            <p>Secure Digital Voting Platform</p>
        </div>

        
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
                <div class="step active">
                    <div class="step-number">3</div>
                    <div class="step-label">Fingerprint</div>
                </div>
                <div class="step">
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

        
        <div class="voting-card">
            <div class="alert-header">
                <div class="alert-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" fill="white"/>
                    </svg>
                </div>
                <div class="alert-text">
                    <h2>Biometric Verification</h2>
                    <p>Fingerprint Authentication Required</p>
                </div>
            </div>

            <div class="card-body">
                
                <div class="verifying-banner">
                    <div class="verifying-label">Verifying Identity For:</div>
                     <span class="voter-name"><?php echo $user ? h($user['fullname']) : 'Sample Voter'; ?></span>
                </div>

                
                <div class="fingerprint-section">
                    <div class="fingerprint-circle">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" stroke="#FFB81C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="instruction-text">
                        <h3>Place Your Finger on the Scanner</h3>
                        <p>Click the button below to start scanning</p>
                    </div>
                </div>

                
                <div class="action-buttons">
                    <button class="back-button" onclick="alert('Going back...')">
                        Go Back
                    </button>
                    <button class="scan-button" onclick="window.location.href='biosuccess.php'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Start Scan
                    </button>
                </div>
            </div>
        </div>

        
        <div class="footer">
            <p>Powered by Election Commission of Sri Lanka</p>
            <p>Secure • Transparent • Democratic</p>
        </div>
    </div>

</body>
</html>
