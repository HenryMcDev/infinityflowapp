<?php
/**
 * InfinityFlow - Dashboard Administrativo
 * Dashboard dinâmico com dados reais do banco de dados
 */

// Iniciar sessão
session_start();

// Incluir configuração do banco de dados
require_once __DIR__ . '/../config/db.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Se não estiver logado, redirecionar para a página de login
    header("Location: index.php");
    exit();
}

// Dados do usuário logado
$username = $_SESSION['username'] ?? 'Administrador';
$email = $_SESSION['email'] ?? '';

// ====================================================================
// CONSULTAS DINÂMICAS - MÉTRICAS DO DASHBOARD
// ====================================================================

try {
    // 1. Total de Usuários
    $stmt_usuarios = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // 2. Total de Mensagens Enviadas
    $stmt_mensagens = $pdo->query("SELECT COUNT(*) as total FROM mensagens");
    $total_mensagens = $stmt_mensagens->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // 3. Automações Ativas
    $stmt_automacoes = $pdo->query("SELECT COUNT(*) as total FROM automacoes WHERE status = 'ativo'");
    $total_automacoes = $stmt_automacoes->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // 4. Taxa de Sucesso (mensagens entregues/enviadas)
    $stmt_sucesso = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status IN ('entregue', 'lida') THEN 1 ELSE 0 END) as entregues
        FROM mensagens
    ");
    $dados_sucesso = $stmt_sucesso->fetch(PDO::FETCH_ASSOC);
    $taxa_sucesso = ($dados_sucesso['total'] > 0) 
        ? round(($dados_sucesso['entregues'] / $dados_sucesso['total']) * 100, 1) 
        : 0;
    
    // 5. Mensagens Recentes (últimas 5)
    $stmt_recentes = $pdo->query("
        SELECT destinatario, conteudo, status, data_envio 
        FROM mensagens 
        ORDER BY data_envio DESC 
        LIMIT 5
    ");
    $mensagens_recentes = $stmt_recentes->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Log de erro e valores padrão
    error_log('[InfinityFlow Dashboard] Erro ao buscar métricas: ' . $e->getMessage());
    $total_usuarios = 0;
    $total_mensagens = 0;
    $total_automacoes = 0;
    $taxa_sucesso = 0;
    $mensagens_recentes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - InfinityFlow</title>
    
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
                },
            },
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="font-sans bg-black">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-80 glass-strong border-r border-white/10 flex flex-col fixed h-screen left-0 top-0 z-50">
            <!-- Sidebar Header -->
            <div class="p-8 border-b border-white/10">
                <div class="flex items-center gap-4">
                    <img src="./images/logo.png" alt="InfinityFlow Logo" class="w-14 h-14 glow-pulse">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight bg-gradient-to-r from-[#C71A1D] via-red-500 to-[#ff4444] bg-clip-text text-transparent">
                            InfinityFlow
                        </h2>
                        <p class="text-white/40 text-xs tracking-wide">Admin Panel</p>
                    </div>
                </div>
            </div>
            
            <!-- User Info -->
            <div class="px-8 py-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#C71A1D] to-red-600 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium text-sm"><?= htmlspecialchars($username) ?></p>
                        <?php if ($email): ?>
                            <p class="text-white/40 text-xs"><?= htmlspecialchars($email) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="flex-1 py-8 px-4 overflow-y-auto">
                <div class="space-y-2">
                    <a href="dashboard.php" class="nav-item group flex items-center gap-4 px-5 py-4 text-white bg-[#C71A1D]/10 rounded-xl border-l-2 border-[#C71A1D]">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Dashboard</span>
                    </a>
                    
                    <a href="automacoes.php" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="workflow" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Automações</span>
                    </a>
                    
                    <a href="mensagens.php" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Mensagens</span>
                    </a>
                    
                    <a href="banco_dados.php" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="database" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Banco de Dados</span>
                    </a>
                    
                    <a href="administradores.php" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Administradores</span>
                    </a>
                </div>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="p-6 border-t border-white/10">
                <a href="logout.php" class="group w-full flex items-center gap-3 px-5 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white/60 font-medium tracking-wide transition-all duration-300 hover:bg-[#C71A1D]/10 hover:border-[#C71A1D]/50 hover:text-white hover:shadow-[0_0_20px_rgba(199,26,29,0.2)]">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Sair</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 ml-80 p-12">
            <!-- Welcome Section -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-white mb-2">
                    Bem-vindo, <?= htmlspecialchars($username) ?>! 👋
                </h1>
                <p class="text-white/50 text-lg">
                    Aqui está um resumo da sua área administrativa
                </p>
            </div>
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <!-- Card 1: Usuários Ativos -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-6 h-6 text-blue-400"></i>
                        </div>
                        <?php if ($total_usuarios > 0): ?>
                            <span class="text-green-400 text-sm font-medium">Ativo</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1"><?= number_format($total_usuarios, 0, ',', '.') ?></h3>
                    <p class="text-white/50 text-sm">Usuários Cadastrados</p>
                </div>
                
                <!-- Card 2: Mensagens Enviadas -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="message-square" class="w-6 h-6 text-purple-400"></i>
                        </div>
                        <?php if ($total_mensagens > 0): ?>
                            <span class="text-green-400 text-sm font-medium">Ativo</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1"><?= number_format($total_mensagens, 0, ',', '.') ?></h3>
                    <p class="text-white/50 text-sm">Mensagens Enviadas</p>
                </div>
                
                <!-- Card 3: Automações Ativas -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-[#C71A1D]/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="workflow" class="w-6 h-6 text-[#C71A1D]"></i>
                        </div>
                        <?php if ($total_automacoes > 0): ?>
                            <span class="text-green-400 text-sm font-medium">Ativo</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1"><?= number_format($total_automacoes, 0, ',', '.') ?></h3>
                    <p class="text-white/50 text-sm">Automações Ativas</p>
                </div>
                
                <!-- Card 4: Taxa de Sucesso -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-6 h-6 text-green-400"></i>
                        </div>
                        <?php if ($taxa_sucesso >= 90): ?>
                            <span class="text-green-400 text-sm font-medium">Excelente</span>
                        <?php elseif ($taxa_sucesso >= 70): ?>
                            <span class="text-yellow-400 text-sm font-medium">Bom</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1"><?= number_format($taxa_sucesso, 1, ',', '.') ?>%</h3>
                    <p class="text-white/50 text-sm">Taxa de Entrega</p>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="glass border border-white/10 rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <i data-lucide="activity" class="w-6 h-6 text-[#C71A1D]"></i>
                    Atividade Recente
                </h2>
                
                <?php if (empty($mensagens_recentes)): ?>
                    <!-- Mensagem quando não há dados -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="inbox" class="w-8 h-8 text-white/30"></i>
                        </div>
                        <p class="text-white/50 text-lg mb-2">Nenhuma mensagem ainda</p>
                        <p class="text-white/30 text-sm">As mensagens enviadas aparecerão aqui</p>
                    </div>
                <?php else: ?>
                    <!-- Lista de mensagens recentes -->
                    <div class="space-y-4">
                        <?php foreach ($mensagens_recentes as $msg): ?>
                            <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-300">
                                <div class="w-10 h-10 bg-<?= $msg['status'] === 'lida' ? 'green' : ($msg['status'] === 'entregue' ? 'blue' : 'gray') ?>-500/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="message-circle" class="w-5 h-5 text-<?= $msg['status'] === 'lida' ? 'green' : ($msg['status'] === 'entregue' ? 'blue' : 'gray') ?>-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-white font-medium text-sm truncate"><?= htmlspecialchars($msg['destinatario']) ?></p>
                                        <span class="text-white/40 text-xs ml-2 flex-shrink-0"><?= date('d/m H:i', strtotime($msg['data_envio'])) ?></span>
                                    </div>
                                    <p class="text-white/50 text-sm truncate"><?= htmlspecialchars(substr($msg['conteudo'], 0, 50)) ?><?= strlen($msg['conteudo']) > 50 ? '...' : '' ?></p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        <?php
                                        switch($msg['status']) {
                                            case 'lida':
                                                echo 'bg-green-500/10 text-green-400';
                                                break;
                                            case 'entregue':
                                                echo 'bg-blue-500/10 text-blue-400';
                                                break;
                                            case 'enviada':
                                                echo 'bg-purple-500/10 text-purple-400';
                                                break;
                                            default:
                                                echo 'bg-gray-500/10 text-gray-400';
                                        }
                                        ?>">
                                        <?= ucfirst($msg['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Botão Ver Todas -->
                    <div class="mt-6 text-center">
                        <a href="mensagens.php" class="inline-flex items-center gap-2 px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-white/70 font-medium hover:bg-[#C71A1D]/10 hover:border-[#C71A1D]/50 hover:text-white transition-all duration-300">
                            <span>Ver todas as mensagens</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
