<?php
// config/database.php
// Database configuration for XAMPP

$host = 'localhost';
$database_name = 'secure_login_db';
$username = 'root';      // XAMPP default
$password = '';          // XAMPP default (empty)

// Create connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database_name", $username, $password);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Uncomment the line below to test connection (remove after testing)
    // echo "Database connected successfully!";
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to create tables if they don't exist (run once)
function createTables($pdo) {
    $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            username VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            last_login_ip VARCHAR(45) NULL,
            failed_attempts INT DEFAULT 0,
            locked_until TIMESTAMP NULL,
            is_active BOOLEAN DEFAULT TRUE
        );
        
        CREATE TABLE IF NOT EXISTS auth_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            success_flag BOOLEAN NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );
        
        CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
    
    $pdo->exec($sql);
    echo "Tables created successfully!";
}

// Uncomment the line below ONLY ONCE to create tables
// createTables($pdo);
?>