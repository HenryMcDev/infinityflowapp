<?php
/**
 * InfinityFlow - Login Process
 * 
 * Handles login POST requests and validates credentials
 * Returns JSON response for AJAX handling
 */

// Include authentication configuration
require_once __DIR__ . '/../../config/auth.php';

// Set JSON content type
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Fallback to regular POST if JSON decode fails
if ($data === null) {
    $data = $_POST;
}

// Validate required fields
if (empty($data['username']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Usuário e senha são obrigatórios'
    ]);
    exit;
}

$username = trim($data['username']);
$password = $data['password'];

// Verify credentials
if (verifyCredentials($username, $password)) {
    // Login successful
    loginUser($username);
    
    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso',
        'redirect' => '/InfinityStore/administrativo/'
    ]);
} else {
    // Login failed
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Usuário ou senha inválidos'
    ]);
}
