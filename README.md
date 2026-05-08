# Secure Student Login System

A research prototype demonstrating layered authentication security controls to protect student information systems from common web vulnerabilities (SQL injection, XSS, brute force, CSRF, session hijacking).

**Author:** Aayush Kumar Rai  
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
   `git clone https://github.com/yourusername/secure-student-login.git`

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
Update the database connection in config/database.php with your credentials (default for XAMPP: username root, password empty).

Usage
Landing page – http://localhost/secure-student-login/
Provides an overview of the system and navigation to registration/login.

Register – Create a new account.
Password must be at least 8 characters with uppercase, lowercase, number, and special character.

Login – After registration, log in with your credentials.
After 5 failed attempts, the account is locked for 15 minutes.

Dashboard – After successful login, you can see your profile, security badges, and recent authentication logs.

Logout – Terminates the session and redirects to the login page.

Security Controls Implemented
bcrypt hashing – Passwords are hashed with a cost factor of 10, including a built‑in salt.

Parameterized queries (PDO) – All database queries use prepared statements, eliminating SQL injection.

Rate limiting – Maximum 5 failed login attempts; upon reaching the limit, the account is locked for 15 minutes.

CSRF tokens – Every form contains a unique token; requests with missing or invalid tokens are rejected.

Secure session cookies – Cookies are marked HttpOnly (prevents JavaScript access) and SameSite=Strict (prevents CSRF).

Session regeneration – After a successful login, the session ID is regenerated to prevent session fixation.

Input sanitization – User input is sanitised with htmlspecialchars() to prevent XSS.

Audit logging – Every authentication attempt (successful or failed) is logged with user ID, timestamp, IP address, and user agent.

Testing
The system was tested with 22 manual test cases, covering:

User registration (valid/invalid inputs, duplicate accounts)

Secure login (correct/wrong passwords, account lockout)

Session management (session cookie flags, logout, regeneration)

CSRF protection (tampered token rejection)

SQL injection prevention (payloads like ' OR '1'='1)

XSS prevention (<script>alert('XSS')</script> rendered as plain text)

All 22 tests passed successfully.
Note: Automated scanning tools (OWASP ZAP, SQLMap) were not executed due to time constraints – listed as a limitation.

Limitations
Local testing only – The system has not been deployed to Azure or any live server.

No HTTPS – Credentials and session cookies would be vulnerable on a live network.

No two‑factor authentication (2FA) – Compared to related work (Ullah & Iqbal, 2022), this is a missing feature.

No automated vulnerability scanning – OWASP ZAP, SQLMap, Burp Suite were not used.

No usability testing with real students – The claim of educational suitability is unvalidated.

No shoulder surfing protection – Credentials are entered directly (in contrast to Ranjan & Kumar, 2016).

Future Work
Deploy to Azure App Service (PHP 8.2 on Linux) with Azure Database for MySQL.

Enable HTTPS with TLS 1.2+.

Implement TOTP-based two‑factor authentication.

Run automated scans using OWASP ZAP and SQLMap.

Conduct usability testing with real students to measure satisfaction and performance.

Add shoulder surfing protection (e.g., encoded credential entry).

Implement continuous authentication using keystroke dynamics (Zamfiroiu et al., 2020).

License
This project is for academic purposes as part of a BSc final year project. You may use the code for reference or educational purposes.

Contact
Aayush Kumar Rai – p2837447@my365.dmu.ac.uk
GitHub: https://github.com/aayushrai074
