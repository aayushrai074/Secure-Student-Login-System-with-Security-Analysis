<?php
// dashboard.php - Protected dashboard (requires authentication)

// STEP 1: Set cookie parameters BEFORE starting session
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// STEP 2: Now start the session
session_start();

// STEP 3: Include required files
require_once 'includes/security.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Get user data from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    // User not found in database - force logout
    session_destroy();
    header('Location: login.php');
    exit();
}

// Generate CSRF token for forms
$csrf_token = generateCSRFToken();

// Get recent login attempts
$stmt = $pdo->prepare("SELECT * FROM auth_logs WHERE user_id = ? ORDER BY attempt_time DESC LIMIT 10");
$stmt->execute([$_SESSION['user_id']]);
$recent_logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Secure Student Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar h2 {
            font-size: 20px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .welcome-card h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            color: #666;
        }
        
        .security-badge {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #667eea;
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .stat-card p {
            color: #666;
        }
        
        .logs-table {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .logs-table h3 {
            margin-bottom: 20px;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f5f5f5;
            color: #555;
        }
        
        .success-badge {
            color: #4caf50;
            font-weight: bold;
        }
        
        .failed-badge {
            color: #f44336;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            th, td {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>🔐 Secure Student Login System</h2>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h1>
            <p>You have successfully authenticated using our secure login system.</p>
        </div>
        
        <div class="security-badge">
            <strong>✅ Active Security Protections:</strong> bcrypt password hashing | Rate limiting (5 attempts) | CSRF tokens | Secure session cookies (HttpOnly, SameSite=Strict) | Input sanitization | Authentication logging
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>🔒 bcrypt</h3>
                <p>Password Hashing (Cost Factor 10)</p>
            </div>
            <div class="stat-card">
                <h3>🛡️ CSRF</h3>
                <p>Token Protection Enabled</p>
            </div>
            <div class="stat-card">
                <h3>🚫 5</h3>
                <p>Max Failed Attempts Before Lockout</p>
            </div>
            <div class="stat-card">
                <h3>📊</h3>
                <p>All Authentication Events Logged</p>
            </div>
        </div>
        
        <div class="logs-table">
            <h3>📋 Recent Authentication Activity</h3>
            <?php if(count($recent_logs) > 0): ?>
                <table>
                    <thead>
                        <tr><th>Time</th><th>Status</th><th>IP Address</th><th>User Agent</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['attempt_time']); ?></td>
                                <td class="<?php echo $log['success_flag'] ? 'success-badge' : 'failed-badge'; ?>">
                                    <?php echo $log['success_flag'] ? '✅ Success' : '❌ Failed'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                <td><?php echo htmlspecialchars(substr($log['user_agent'], 0, 50)) . '...'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No authentication logs found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>