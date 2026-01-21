<?php
/**
 * InfinityFlow - Logout Process
 * 
 * Destroys user session and redirects to homepage
 */

// Include authentication configuration
require_once __DIR__ . '/../../config/auth.php';

// Logout user
logoutUser();

// Redirect to homepage
header('Location: /InfinityStore/');
exit;
