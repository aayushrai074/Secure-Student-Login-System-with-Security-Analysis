<?php
// includes/security.php
// All security-related functions go here

/**
 * Sanitize user input to prevent XSS and injection
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate password strength
 * Requirements: min 8 chars, at least 1 uppercase, 1 lowercase, 1 number, 1 special
 */
function isStrongPassword($password) {
    $minLength = 8;
    $hasUppercase = preg_match('/[A-Z]/', $password);
    $hasLowercase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
    
    return (strlen($password) >= $minLength && $hasUppercase && $hasLowercase && $hasNumber && $hasSpecial);
}

/**
 * Generate CSRF token to prevent CSRF attacks
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Check if account is locked due to too many failed attempts
 */
function isAccountLocked($pdo, $identifier) {
    $stmt = $pdo->prepare("SELECT locked_until FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();
    
    if ($user && $user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
        return true;
    }
    return false;
}

/**
 * Log authentication attempt
 */
function logAuthAttempt($pdo, $userId, $success, $ip, $userAgent) {
    $stmt = $pdo->prepare("INSERT INTO auth_logs (user_id, success_flag, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $success ? 1 : 0, $ip, $userAgent]);
}

/**
 * Get client IP address (handles proxies)
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

?>