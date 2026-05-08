<?php
// index.php - Landing page
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Student Login System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 800px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .security-badge {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        .security-badge h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }
        
        .security-badge ul {
            margin-left: 20px;
            color: #555;
        }
        
        .security-badge li {
            margin: 5px 0;
        }
        
        .buttons {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-login {
            background: #667eea;
            color: white;
        }
        
        .btn-register {
            background: #4caf50;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            transition: all 0.3s;
        }
        
        .feature-list {
            margin: 20px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .feature {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
        }
        
        .feature h4 {
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .feature p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Secure Student Login System</h1>
        <div class="subtitle">Research Project: Authentication Security Analysis</div>
        
        <div class="security-badge">
            <h3>✅ Security Features Implemented</h3>
            <ul>
                <li>🔒 bcrypt password hashing (with salt)</li>
                <li>🚫 Rate limiting (5 attempts, 15-min lockout)</li>
                <li>🛡️ CSRF token protection</li>
                <li>🍪 Secure session cookies (HttpOnly, SameSite)</li>
                <li>📝 Input sanitization & XSS prevention</li>
                <li>📊 Authentication logging</li>
                <li>🔑 Strong password policy enforcement</li>
            </ul>
        </div>
        
        <h3>Research Question:</h3>
        <p style="margin: 15px 0; padding: 15px; background: #f0f0f0; border-radius: 8px; font-style: italic;">
            "How can secure authentication techniques protect student information systems 
            from common web vulnerabilities?"
        </p>
        
        <div class="feature-list">
            <div class="feature">
                <h4>📖 Literature Review</h4>
                <p>Based on Ullah & Iqbal (2022), Ranjan & Kumar (2016), and Zamfiroiu et al. (2020)</p>
            </div>
            <div class="feature">
                <h4>🔬 Security Testing</h4>
                <p>OWASP ZAP, SQLMap, penetration testing, performance analysis</p>
            </div>
            <div class="feature">
                <h4>📊 Data Analysis</h4>
                <p>FAR/FRR measurement, vulnerability reduction, attack success rates</p>
            </div>
        </div>
        
        <div class="buttons">
            <a href="login.php" class="btn btn-login">🔐 Login</a>
            <a href="register.php" class="btn btn-register">📝 Register</a>
        </div>
    </div>
</body>
</html>