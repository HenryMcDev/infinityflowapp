<?php
/**
 * InfinityFlow - Administrative Area
 * Server-side login processing with direct database authentication
 */

// Iniciar sessão (DEVE ser a primeira coisa no arquivo)
session_start();

// Incluir configuração do banco de dados (acessa variável global $pdo)
require_once __DIR__ . '/../config/db.php';

// Variável para armazenar mensagens de erro
$error_message = null;
$success_message = null;

// Processar formulário de login (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    try {
        // Sanitizar entrada do usuário
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST['password'] ?? '';
        
        // Validação básica
        if (empty($username) || empty($password)) {
            $error_message = "Por favor, preencha todos os campos";
        } else {
            // Query preparada usando a variável global $pdo
            $stmt = $pdo->prepare("SELECT * FROM usuarios_admin WHERE username = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar se o usuário existe e a senha está correta
            if ($user && password_verify($password, $user['password_hash'])) {
                // Login bem-sucedido - criar sessão
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Atualizar last_login no banco de dados
                $updateStmt = $pdo->prepare("UPDATE usuarios_admin SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Redirecionar para o dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Credenciais inválidas
                $error_message = "Usuário ou senha inválidos";
            }
        }
    } catch (PDOException $e) {
        // Erro de banco de dados - logar e exibir mensagem genérica
        error_log('[InfinityFlow Login] Erro PDO: ' . $e->getMessage());
        $error_message = "Erro ao processar login. Tente novamente.";
    } catch (Exception $e) {
        // Outros erros
        error_log('[InfinityFlow Login] Erro: ' . $e->getMessage());
        $error_message = "Erro ao processar login. Tente novamente.";
    }
}

// Exibir mensagem de sucesso após cadastro (se existir na sessão)
if (isset($_SESSION['cadastro_sucesso'])) {
    $success_message = $_SESSION['cadastro_sucesso'];
    unset($_SESSION['cadastro_sucesso']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfinityFlow - Área Administrativa</title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'glow-pulse': 'glow-pulse 3s ease-in-out infinite',
                        'fade-in-up': 'fade-in-up 0.6s ease-out',
                    },
                },
            },
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="font-sans">
    <!-- Login View -->
    <div id="loginView" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-black via-black to-red-950/20 fade-in-up">
        <div class="glass border border-white/10 rounded-3xl p-12 w-full max-w-md shadow-2xl hover:shadow-[0_0_60px_rgba(199,26,29,0.15)] transition-all duration-500">
            <!-- Logo Section -->
            <div class="text-center mb-10">
                <img src="./images/logo.png" alt="InfinityFlow Logo" class="w-24 h-24 mx-auto mb-6 glow-pulse">
                <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-[#C71A1D] via-red-500 to-[#ff4444] bg-clip-text text-transparent">
                    InfinityFlow
                </h1>
                <p class="text-white/50 text-sm mt-2 font-light tracking-wide">Área Administrativa</p>
            </div>
            
            <!-- Login Form -->
            <form method="POST" action="" class="space-y-6">
                
                <?php if ($success_message): ?>
                    <div class="mb-6 p-4 bg-green-600/20 border border-green-600/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-400 flex-shrink-0"></i>
                            <p class="text-green-300 text-sm font-medium"><?= htmlspecialchars($success_message) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="mb-6 p-4 bg-red-600/20 border border-red-600/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 flex-shrink-0"></i>
                            <p class="text-red-300 text-sm font-medium"><?= htmlspecialchars($error_message) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-white/70 tracking-wide">Usuário</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Digite seu usuário"
                        required
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:border-[#C71A1D] focus:ring-2 focus:ring-[#C71A1D]/50 focus:shadow-[0_0_25px_rgba(199,26,29,0.3)] transition-all duration-300"
                    >
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-white/70 tracking-wide">Senha</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Digite sua senha"
                        required
                        class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:border-[#C71A1D] focus:ring-2 focus:ring-[#C71A1D]/50 focus:shadow-[0_0_25px_rgba(199,26,29,0.3)] transition-all duration-300"
                    >
                </div>
                
                <button type="submit" class="w-full mt-8 px-6 py-4 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-semibold text-white shadow-lg hover:shadow-[0_0_40px_rgba(199,26,29,0.5)] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                    Entrar
                </button>
            </form>
            
            <!-- Link para Cadastro de Novo Administrador -->
            <div class="mt-8 text-center">
                <div class="border-t border-white/10 pt-6">
                    <a href="cadastro_admin.php" class="inline-flex items-center gap-2 text-sm text-white/60 hover:text-red-400 transition-colors duration-300 group">
                        <i data-lucide="user-plus" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        Novo Administrador
                    </a>
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
