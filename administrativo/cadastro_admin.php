<?php
/**
 * InfinityFlow - Cadastro de Administradores
 * ============================================
 * Sistema de registro de novos administradores protegido por chave de acesso
 * 
 * Segurança:
 * - Chave de acesso obrigatória: "frango"
 * - Senha criptografada com BCRYPT
 * - Verificação de duplicidade de usuários
 * - Proteção contra SQL Injection via PDO
 * 
 * @author InfinityFlow Team
 * @version 1.0.0
 */

session_start();

// Variáveis de controle
$erro = '';
$sucesso = '';

// ==================== PROCESSAR FORMULÁRIO ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Conectar ao banco
        require_once __DIR__ . '/../config/db.php';
        
        // Capturar e sanitizar dados (PHP 8.1+ compatível)
        $username = isset($_POST['username']) ? trim(htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8')) : '';
        $password = $_POST['password'] ?? '';
        $chave_acesso = isset($_POST['chave_acesso']) ? trim($_POST['chave_acesso']) : '';
        $nivel = isset($_POST['nivel']) ? trim($_POST['nivel']) : 'Admin';
        
        // VALIDAÇÃO 1: Campos obrigatórios
        if (empty($username) || empty($password) || empty($chave_acesso) || empty($nivel)) {
            throw new Exception('Todos os campos são obrigatórios.');
        }
        
        // VALIDAÇÃO 2: Nível válido
        if (!in_array($nivel, ['CEO', 'Admin'], true)) {
            throw new Exception('Nível de permissão inválido.');
        }
        
        // VALIDAÇÃO 3: Chave de Acesso Dinâmica (buscar do banco)
        $keyStmt = $pdo->prepare("SELECT valor FROM configuracoes WHERE chave_nome = 'chave_cadastro' LIMIT 1");
        $keyStmt->execute();
        $chave_valida = $keyStmt->fetchColumn();
        
        if (!$chave_valida || $chave_acesso !== $chave_valida) {
            throw new Exception('Chave de Acesso Inválida');
        }
        
        // VALIDAÇÃO 4: Tamanho mínimo de senha
        if (strlen($password) < 6) {
            throw new Exception('A senha deve ter no mínimo 6 caracteres.');
        }
        
        // VALIDAÇÃO 5: Verificar duplicidade de usuário
        $checkSQL = "SELECT COUNT(*) as total FROM usuarios_admin WHERE username = :username";
        $stmt = $pdo->prepare($checkSQL);
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            throw new Exception('Este nome de usuário já está em uso. Escolha outro.');
        }
        
        // SEGURANÇA: Criptografar senha com BCRYPT
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // INSERIR: Novo administrador no banco
        $insertSQL = "
            INSERT INTO usuarios_admin (username, password_hash, email, nivel, created_at, is_active) 
            VALUES (:username, :password_hash, :email, :nivel, NOW(), 1)
        ";
        
        $stmt = $pdo->prepare($insertSQL);
        $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash,
            'email' => $username . '@infinityflow.local', // Email fictício
            'nivel' => $nivel
        ]);
        
        // SUCESSO: Definir mensagem de sessão e redirecionar
        $_SESSION['cadastro_sucesso'] = "Administrador '$username' cadastrado com sucesso! Faça login para continuar.";
        header('Location: index.php');
        exit;
        
    } catch (PDOException $e) {
        error_log('[InfinityFlow Cadastro] Erro de banco de dados: ' . $e->getMessage());
        $erro = 'Erro ao processar cadastro. Tente novamente.';
        
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfinityFlow - Cadastro de Administrador</title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .glass {
            background: rgba(18, 18, 18, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glow-pulse {
            animation: glow-pulse 3s ease-in-out infinite;
        }
        @keyframes glow-pulse {
            0%, 100% { filter: drop-shadow(0 0 10px rgba(199, 26, 29, 0.5)); }
            50% { filter: drop-shadow(0 0 20px rgba(199, 26, 29, 0.8)); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-black via-black to-red-950/20">
    <div class="glass border border-white/10 rounded-3xl p-12 w-full max-w-md shadow-2xl hover:shadow-[0_0_60px_rgba(199,26,29,0.15)] transition-all duration-500">
        
        <!-- Logo Section -->
        <div class="text-center mb-10">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-red-600 to-red-800 rounded-2xl flex items-center justify-center glow-pulse">
                <i data-lucide="user-plus" class="w-10 h-10 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-[#C71A1D] via-red-500 to-[#ff4444] bg-clip-text text-transparent">
                Novo Administrador
            </h1>
            <p class="text-white/50 text-sm mt-2 font-light tracking-wide">InfinityFlow</p>
        </div>
        
        <!-- Mensagem de Erro -->
        <?php if (!empty($erro)): ?>
        <div class="mb-6 p-4 bg-red-600/20 border border-red-600/50 rounded-xl">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 flex-shrink-0"></i>
                <p class="text-red-300 text-sm font-medium"><?php echo htmlspecialchars($erro); ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Formulário de Cadastro -->
        <form method="POST" action="" class="space-y-6">
            
            <!-- Campo: Usuário -->
            <div class="space-y-2">
                <label for="username" class="block text-sm font-medium text-white/70 tracking-wide">
                    <i data-lucide="user" class="w-4 h-4 inline-block mr-1"></i>
                    Nome de Usuário
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Digite o nome de usuário"
                    required
                    autocomplete="off"
                    class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:border-[#C71A1D] focus:ring-2 focus:ring-[#C71A1D]/50 focus:shadow-[0_0_25px_rgba(199,26,29,0.3)] transition-all duration-300"
                >
            </div>
            
            <!-- Campo: Senha -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-white/70 tracking-wide">
                    <i data-lucide="lock" class="w-4 h-4 inline-block mr-1"></i>
                    Senha
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Digite a senha (mínimo 6 caracteres)"
                    required
                    minlength="6"
                    class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:border-[#C71A1D] focus:ring-2 focus:ring-[#C71A1D]/50 focus:shadow-[0_0_25px_rgba(199,26,29,0.3)] transition-all duration-300"
                >
            </div>
            
            <!-- Campo: Nível de Permissão -->
            <div class="space-y-2">
                <label for="nivel" class="block text-sm font-medium text-white/70 tracking-wide">
                    <i data-lucide="shield" class="w-4 h-4 inline-block mr-1"></i>
                    Nível de Permissão
                </label>
                <select 
                    id="nivel" 
                    name="nivel" 
                    required
                    class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-[#C71A1D] focus:ring-2 focus:ring-[#C71A1D]/50 focus:shadow-[0_0_25px_rgba(199,26,29,0.3)] transition-all duration-300"
                >
                    <option value="" class="bg-gray-900">Selecione o nível</option>
                    <option value="CEO" class="bg-gray-900">🔴 CEO (Acesso Total)</option>
                    <option value="Admin" class="bg-gray-900" selected>🔵 Admin (Operacional)</option>
                </select>
                <p class="text-xs text-white/40 mt-1">
                    <i data-lucide="info" class="w-3 h-3 inline-block"></i>
                    CEO possui acesso total, Admin tem restrições
                </p>
            </div>
            
            <!-- Campo: Chave de Acesso -->
            <div class="space-y-2">
                <label for="chave_acesso" class="block text-sm font-medium text-white/70 tracking-wide">
                    <i data-lucide="key" class="w-4 h-4 inline-block mr-1"></i>
                    Chave de Acesso
                </label>
                <input 
                    type="password" 
                    id="chave_acesso" 
                    name="chave_acesso" 
                    placeholder="Digite a chave de acesso secreta"
                    required
                    autocomplete="off"
                    class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:border-[#C71A1D] focus:ring-2 focus:ring-[#C71A1D]/50 focus:shadow-[0_0_25px_rgba(199,26,29,0.3)] transition-all duration-300"
                >
                <p class="text-xs text-white/40 mt-1">
                    <i data-lucide="info" class="w-3 h-3 inline-block"></i>
                    Apenas pessoas autorizadas possuem esta chave
                </p>
            </div>
            
            <!-- Botão de Cadastro -->
            <button 
                type="submit" 
                class="w-full mt-8 px-6 py-4 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-semibold text-white shadow-lg hover:shadow-[0_0_40px_rgba(199,26,29,0.5)] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300"
            >
                <i data-lucide="user-check" class="w-5 h-5 inline-block mr-2"></i>
                Cadastrar Administrador
            </button>
        </form>
        
        <!-- Link para Voltar -->
        <div class="mt-8 text-center">
            <a href="index.php" class="inline-flex items-center gap-2 text-sm text-white/60 hover:text-red-400 transition-colors duration-300">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Voltar para Login
            </a>
        </div>
        
        <!-- Informação de Segurança -->
        <div class="mt-6 p-4 bg-yellow-600/10 border border-yellow-600/30 rounded-xl">
            <div class="flex items-start gap-3">
                <i data-lucide="shield-alert" class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-yellow-300 text-xs font-medium">Acesso Restrito</p>
                    <p class="text-yellow-400/70 text-xs mt-1">
                        Esta área é exclusiva para cadastro de novos administradores. A chave de acesso é necessária para prosseguir.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
