<?php
/**
 * InfinityFlow - Logout
 * Encerra a sessão do usuário e redireciona para o login
 */

// Iniciar sessão
session_start();

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header("Location: index.php");
exit();
