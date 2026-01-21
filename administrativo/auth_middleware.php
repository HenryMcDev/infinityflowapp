<?php
/**
 * InfinityFlow - Authentication Middleware
 * =========================================
 * Helper functions para controle de acesso e permissões
 * 
 * Segurança:
 * - Verificação de sessão ativa
 * - Controle de níveis de permissão (CEO/Admin)
 * - Proteção de rotas administrativas
 * 
 * @author InfinityFlow Team
 * @version 1.0.0
 */

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o usuário está logado
 * Redireciona para login se não estiver autenticado
 * 
 * @return void
 */
function require_login() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: index.php');
        exit();
    }
}

/**
 * Verifica se o usuário tem nível CEO
 * Exibe erro 403 se não tiver permissão
 * 
 * @return void
 */
function require_ceo() {
    require_login();
    
    if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'CEO') {
        http_response_code(403);
        die(show_access_denied('Acesso Negado: Esta área é exclusiva para usuários de nível CEO.'));
    }
}

/**
 * Verifica se o usuário possui o nível de permissão especificado
 * 
 * @param string $required_level Nível necessário ('CEO' ou 'Admin')
 * @return bool
 */
function check_permission($required_level) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    $user_level = $_SESSION['nivel'] ?? 'Admin';
    
    // CEO tem acesso a tudo
    if ($user_level === 'CEO') {
        return true;
    }
    
    // Verificar se o nível do usuário é suficiente
    return $user_level === $required_level;
}

/**
 * Verifica se o usuário logado é CEO
 * 
 * @return bool
 */
function is_ceo() {
    return isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'CEO';
}

/**
 * Verifica se o usuário logado é Admin
 * 
 * @return bool
 */
function is_admin() {
    return isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'Admin';
}

/**
 * Verifica se o usuário pode gerenciar outro usuário
 * Regras:
 * - Ninguém pode gerenciar a própria conta (para operações críticas)
 * - Admin não pode gerenciar CEO
 * 
 * @param int $target_user_id ID do usuário alvo
 * @param string $target_user_nivel Nível do usuário alvo
 * @return bool
 */
function can_manage_user($target_user_id, $target_user_nivel) {
    // Não pode gerenciar a própria conta
    if ($target_user_id == $_SESSION['user_id']) {
        return false;
    }
    
    $user_nivel = $_SESSION['nivel'] ?? 'Admin';
    
    // CEO pode gerenciar qualquer um (exceto a própria conta)
    if ($user_nivel === 'CEO') {
        return true;
    }
    
    // Admin não pode gerenciar CEO
    if ($target_user_nivel === 'CEO') {
        return false;
    }
    
    return true;
}

/**
 * Exibe página de acesso negado com estilo InfinityFlow
 * 
 * @param string $message Mensagem de erro
 * @return string HTML da página de erro
 */
function show_access_denied($message = 'Acesso Negado') {
    return '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InfinityFlow - Acesso Negado</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <style>
            body { font-family: "Poppins", sans-serif; }
            .glass {
                background: rgba(18, 18, 18, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-black via-black to-red-950/20">
        <div class="glass border border-white/10 rounded-3xl p-12 w-full max-w-md shadow-2xl text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-red-600/20 rounded-2xl flex items-center justify-center border border-red-600/50">
                <i data-lucide="shield-off" class="w-10 h-10 text-red-400"></i>
            </div>
            <h1 class="text-3xl font-bold text-red-400 mb-4">403 - Acesso Negado</h1>
            <p class="text-white/70 mb-8">' . htmlspecialchars($message) . '</p>
            <a href="dashboard.php" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-semibold text-white shadow-lg hover:shadow-[0_0_40px_rgba(199,26,29,0.5)] transition-all duration-300">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                Voltar ao Dashboard
            </a>
        </div>
        <script>lucide.createIcons();</script>
    </body>
    </html>';
}

/**
 * Retorna o nível do usuário logado
 * 
 * @return string|null
 */
function get_user_nivel() {
    return $_SESSION['nivel'] ?? null;
}

/**
 * Retorna informações do usuário logado
 * 
 * @return array
 */
function get_current_user() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'nivel' => $_SESSION['nivel'] ?? null
    ];
}
?>
