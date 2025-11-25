<html>
<head>

<title>Sri Lankan Election Voting System - Vote Submitted</title>

<style>
    *{margin:0;padding:0;box-sizing:border-box;}

    body{
        font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        background:linear-gradient(135deg,#f5f7fa 0%,#e8eef3 100%);
        min-height:100vh;
        padding:20px;
    }

    .container{max-width:800px;margin:0 auto;}

    .header{text-align:center;margin-bottom:30px;}

    .color-bar{
        height:4px;width:250px;margin:0 auto 15px;border-radius:2px;
        background:linear-gradient(to right,#8B1538 0%,#8B1538 33%,#FFB81C 33%,#FFB81C 50%,#FF6B35 50%,#FF6B35 66%,#006747 66%,#006747 100%);
    }

    .header h1{font-size:26px;color:#2c3e50;font-weight:600;margin-bottom:8px;}
    .header p{color:#7f8c8d;font-size:14px;}

    .info-bar{
        background:white;border:1px solid #e1e8ed;border-radius:8px;
        padding:15px 20px;margin-bottom:25px;
        display:flex;justify-content:space-between;align-items:center;
        box-shadow:0 2px 8px rgba(0,0,0,0.05);
        font-size:14px;color:#2c3e50;
    }

    .info-left{display:flex;gap:25px;align-items:center;font-size:14px;color:#2c3e50;}

    .info-item{display:flex;align-items:center;gap:8px;}
    .label{color:#7f8c8d;}
    .value{font-weight:500;}

    .end-session-btn{
        background:white;border:1px solid #e74c3c;color:#e74c3c;
        padding:8px 16px;border-radius:6px;font-size:13px;
        display:flex;align-items:center;gap:6px;
        cursor:pointer;transition:.2s;
    }
    .end-session-btn:hover{background:#e74c3c;color:white;}

    /* CARD */
    .success-card{
        background:white;border-radius:12px;
        box-shadow:0 4px 16px rgba(0,0,0,0.08);
        overflow:hidden;
        padding:40px 30px;
        text-align:center;
    }

    .success-icon{
        width:120px;height:120px;margin:0 auto 20px;
        border-radius:50%;border:4px solid #27ae60;
        display:flex;align-items:center;justify-content:center;
        color:#27ae60;font-size:60px;
        animation:pulse 2s infinite;
    }

    @keyframes pulse{
        0%,100%{box-shadow:0 0 0 0 rgba(39,174,96,0.4);}
        50%{box-shadow:0 0 0 20px rgba(39,174,96,0);}
    }

    .success-title{font-size:22px;font-weight:600;color:#2c3e50;margin-bottom:10px;}
    .success-msg{font-size:14px;color:#7f8c8d;line-height:1.5;margin-bottom:25px;}

    .ok-btn{
        background:#27ae60;color:white;border:none;
        padding:12px 22px;font-size:15px;
        border-radius:8px;cursor:pointer;
        transition:.2s;
        font-weight:500;
    }
    .ok-btn:hover{background:#219150;}

    .footer{text-align:center;margin-top:25px;color:#7f8c8d;font-size:12px;}
    .footer p{margin:5px 0;}

</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="color-bar"></div>
        <h1>Sri Lankan Election System</h1>
        <p>Secure Digital Voting Platform</p>
    </div>

    <!-- INFO BAR -->
    <div class="info-bar">
        <div class="info-left">
            <div class="info-item">
                <span class="label">Admin:</span>
                <span class="value">Priya Wickramasinghe</span>
            </div>
            <div class="info-item">
                <span class="label">Division:</span>
                <span class="value">Kandy District</span>
            </div>
        </div>

        <button class="end-session-btn" onclick="window.location.href='userlogin.php'">End Session</button>
    </div>

    <!-- SUCCESS CARD -->
    <div class="success-card">
        <div class="success-icon">✔</div>

        <div class="success-title">Vote Submitted Successfully!</div>

        <div class="success-msg">
            Thank you for participating in the democratic process.<br>
            Your vote has been recorded securely and will be counted.
        </div>

        <button class="ok-btn" onclick="window.location.href='userlogin.php'">
            Your civic duty is complete. Thank you for voting!
        </button>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Powered by Election Commission of Sri Lanka</p>
        <p>Secure • Transparent • Democratic</p>
    </div>

</div>

    <script>
        // Redirect to user login after 3 seconds
        setTimeout(function() {
            window.location.href = 'userlogin.php';
        }, 3000);
    </script>

</body>
</html>
