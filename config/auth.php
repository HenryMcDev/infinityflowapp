<?php
/**
 * InfinityFlow - Authentication Configuration
 * 
 * This file stores the authentication credentials for the administrative area.
 * Passwords are hashed using PHP's password_hash() function for security.
 * 
 * IMPORTANT: Keep this file secure and do not commit plain-text passwords to version control.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication Configuration
define('AUTH_USERNAME', 'admin');

// Password: InfinityFlow@2026
// Generated using password_hash('InfinityFlow@2026', PASSWORD_DEFAULT)
define('AUTH_PASSWORD_HASH', '$2y$10$.3caFvUrkz2TRObuSOye3OwkiIJS8wEAf6bA/JXo9re1aIctzYW7q');

/**
 * Verify login credentials
 * 
 * @param string $username Username to verify
 * @param string $password Password to verify
 * @return bool True if credentials are valid, false otherwise
 */
function verifyCredentials($username, $password) {
    if ($username !== AUTH_USERNAME) {
        return false;
    }
    
    return password_verify($password, AUTH_PASSWORD_HASH);
}

/**
 * Check if user is authenticated
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Require authentication
 * Redirects to login page if user is not authenticated
 * 
 * @param string $loginUrl URL to redirect to if not authenticated
 */
function requireAuth($loginUrl = '/InfinityStore/index.php') {
    if (!isAuthenticated()) {
        header('Location: ' . $loginUrl);
        exit;
    }
}

/**
 * Log in user
 * 
 * @param string $username Username to log in
 */
function loginUser($username) {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
}

/**
 * Log out user
 */
function logoutUser() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Generate a new password hash
 * Helper function to generate password hashes for new credentials
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
