<?php
// login.php - Secure login with rate limiting

// STEP 1: Set cookie parameters BEFORE starting session
session_set_cookie_params([
    'lifetime' => 3600,           // 1 hour
    'path' => '/',
    'domain' => '',
    'secure' => false,            // Set to true if using HTTPS
    'httponly' => true,           // Prevents JavaScript access
    'samesite' => 'Strict'        // Prevents CSRF
]);

// STEP 2: Now start the session
session_start();

// STEP 3: Include required files
require_once 'includes/security.php';
require_once 'config/database.php';

// Generate CSRF token
$csrf_token = generateCSRFToken();

$error = '';
$locked = false;
$remaining_attempts = 5;

// Check if user is already logged in
if (isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please try again.";
    } else {
        $identifier = sanitizeInput($_POST['identifier']);
        $password = $_POST['password'];
        $ip = getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Find user by email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND is_active = 1");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Check if account is locked
            if ($user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
                $locked = true;
                $locked_until = new DateTime($user['locked_until']);
                $remaining_minutes = ceil((strtotime($user['locked_until']) - time()) / 60);
                $error = "Account is locked. Please try again after " . max(1, $remaining_minutes) . " minutes.";
            } else {
                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Login successful - reset failed attempts
                    $stmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL, last_login = NOW(), last_login_ip = ? WHERE id = ?");
                    $stmt->execute([$ip, $user['id']]);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['logged_in'] = true;
                    
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Log successful attempt
                    logAuthAttempt($pdo, $user['id'], true, $ip, $userAgent);
                    
                    header('Location: dashboard.php');
                    exit();
                } else {
                    // Failed login - increment counter
                    $failed_attempts = $user['failed_attempts'] + 1;
                    $locked_until = null;
                    
                    // Lock account after 5 failed attempts (15 minutes)
                    if ($failed_attempts >= 5) {
                        $locked_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                        $error = "Too many failed attempts. Account locked for 15 minutes.";
                        $locked = true;
                    } else {
                        $remaining_attempts = 5 - $failed_attempts;
                        $error = "Invalid credentials! You have $remaining_attempts attempt(s) remaining.";
                    }
                    
                    $stmt = $pdo->prepare("UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
                    $stmt->execute([$failed_attempts, $locked_until, $user['id']]);
                    
                    // Log failed attempt
                    logAuthAttempt($pdo, $user['id'], false, $ip, $userAgent);
                }
            }
        } else {
            $error = "Invalid credentials!";
            // Still log the attempt (with null user_id)
            logAuthAttempt($pdo, null, false, $ip, $userAgent);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure Student Login</title>
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
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #5a67d8;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
        }
        
        .security-note {
            margin-top: 20px;
            padding: 12px;
            background: #f0f0f0;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        input[disabled], button[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Student Login</h1>
        <div class="subtitle">Access your secure dashboard</div>
        
        <?php if($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label>📧 Email or Username</label>
                <input type="text" name="identifier" required placeholder="Enter your email or username" <?php echo $locked ? 'disabled' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label>🔒 Password</label>
                <input type="password" name="password" required placeholder="Enter your password" <?php echo $locked ? 'disabled' : ''; ?>>
            </div>
            
            <button type="submit" <?php echo $locked ? 'disabled' : ''; ?>>Login</button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
        
        <div class="security-note">
            🔒 This system uses bcrypt password hashing, rate limiting, CSRF protection, and secure session cookies.
        </div>
    </div>
</body>
</html>