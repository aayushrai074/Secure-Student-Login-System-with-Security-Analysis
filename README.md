# Secure-Student-Login-System-with-Security-Analysis

A research prototype that demonstrates layered authentication security controls to protect student information systems from common web vulnerabilities (SQL injection, XSS, brute force, CSRF, session hijacking).

**Author**: Aayush Kumar Rai  
**Project:** BSc (Hons) Computing – Final Year Project  
**Institution:** De Montfort University  

---

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Security Controls Implemented](#security-controls-implemented)
- [Testing](#testing)
- [Limitations](#limitations)
- [Future Work](#future-work)
- [License](#license)

---

## Features

- **User Registration** – Email, username, and strong password enforcement.
- **Secure Login** – bcrypt password verification with rate limiting.
- **Account Lockout** – 5 failed attempts trigger a 15‑minute lockout.
- **CSRF Protection** – Tokens embedded in all forms.
- **Secure Sessions** – HttpOnly and SameSite=Strict cookie flags.
- **XSS Prevention** – Input sanitization using `htmlspecialchars()`.
- **SQL Injection Prevention** – Parameterized queries via PDO.
- **Audit Logging** – All authentication attempts (successful and failed) are logged.

---

## Technology Stack

- **Backend:** PHP 8.2
- **Database:** MySQL / MariaDB
- **Web Server:** Apache (XAMPP)
- **Frontend:** HTML5, CSS3
- **Password Hashing:** bcrypt (cost factor 10)
- **Development Environment:** XAMPP 3.3+, VS Code

---

## Installation

### Prerequisites

- XAMPP (or any Apache + PHP + MySQL stack)
- PHP 8.2 or higher
- MySQL 5.7 or higher
- Git (optional)

### Steps

1. **Clone the repository**  
   `git clone https://github.com/your-username/secure-student-login.git`

2. **Move to XAMPP's htdocs folder**  
   - Windows (default): `C:\xampp\htdocs\secure-student-login`  
   - Mac (MAMP): `/Applications/MAMP/htdocs/secure-student-login`

3. **Start XAMPP services** – Apache and MySQL.

4. **Import the database** (see [Database Setup](#database-setup)).

5. **Access the application** – Open your browser and go to:  
   `http://localhost/secure-student-login/`

---

## Database Setup

1. Open **phpMyAdmin** at `http://localhost/phpmyadmin`.
2. Create a new database named `secure_login_db` (collation: `utf8mb4_general_ci`).
3. Run the following SQL queries to create the required tables:

```sql
-- Users table
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

-- Authentication logs table
CREATE TABLE IF NOT EXISTS auth_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success_flag BOOLEAN NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Password resets table (for future implementation)
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
