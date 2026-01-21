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
                    <div class="flex-1">
                        <p class="text-white font-medium text-sm"><?= htmlspecialchars($username) ?></p>
                        <?php if ($email): ?>
                            <p class="text-white/40 text-xs"><?= htmlspecialchars($email) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php
                    $nivel = $_SESSION['nivel'] ?? 'Admin';
                    $is_ceo = ($nivel === 'CEO');
                    ?>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold <?= $is_ceo ? 'bg-red-600/20 text-red-400 border border-red-600/50' : 'bg-blue-600/20 text-blue-400 border border-blue-600/50' ?>">
                        <?= $is_ceo ? '🔴 CEO' : '🔵 Admin' ?>
                    </span>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="flex-1 py-8 px-4 overflow-y-auto">
                <div class="space-y-2">
                    <a href="#" onclick="showPortal('dashboard'); return false;" id="link-dashboard" class="nav-item group flex items-center gap-4 px-5 py-4 text-white bg-[#C71A1D]/10 rounded-xl border-l-2 border-[#C71A1D]">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Dashboard</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="py-2">
                        <div class="border-b border-white/10"></div>
                        <p class="text-white/40 text-xs uppercase tracking-wider font-semibold px-5 py-3">Portais de Acesso</p>
                    </div>
                    
                    <a href="#" onclick="showPortal('n8n'); return false;" id="link-n8n" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="network" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">n8n Automation</span>
                    </a>
                    
                    <a href="#" onclick="showPortal('easypanel'); return false;" id="link-easypanel" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="server" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Easypanel</span>
                    </a>
                    
                    <a href="#" onclick="showPortal('minio'); return false;" id="link-minio" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="hard-drive" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Minio Storage</span>
                    </a>
                    
                    <a href="#" onclick="showPortal('evolution'); return false;" id="link-evolution" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="smartphone" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Evolution API</span>
                    </a>
                    
                    <a href="#" onclick="showPortal('site'); return false;" id="link-site" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="globe" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">InfinityFlow Site</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="py-2">
                        <div class="border-b border-white/10"></div>
                        <p class="text-white/40 text-xs uppercase tracking-wider font-semibold px-5 py-3">Administração</p>
                    </div>
                    
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
        <main class="flex-1 ml-80 p-12" id="mainContent">
            
            <!-- Dashboard Content (Default) -->
            <div id="content-dashboard">
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
            </div>
            
            <!-- Portal: n8n -->
            <div id="content-n8n" class="hidden">
                <div class="max-w-3xl mx-auto py-20">
                    <div class="glass border border-red-600/30 rounded-3xl p-12 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-red-600 to-red-800 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-red-600/50">
                            <i data-lucide="network" class="w-12 h-12 text-white"></i>
                        </div>
                        <h2 class="text-4xl font-bold text-white mb-4">n8n Automation</h2>
                        <p class="text-white/60 text-lg mb-8">Plataforma de automação visual para conectar serviços e criar workflows poderosos</p>
                        <a 
                            href="https://n8n.infinityflowapp.com" 
                            target="_blank"
                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-bold text-white text-lg shadow-xl hover:shadow-[0_0_50px_rgba(199,26,29,0.6)] hover:-translate-y-1 transition-all duration-300"
                        >
                            <span>Acessar n8n Automation</span>
                            <i data-lucide="external-link" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Portal: Easypanel -->
            <div id="content-easypanel" class="hidden">
                <div class="max-w-3xl mx-auto py-20">
                    <div class="glass border border-red-600/30 rounded-3xl p-12 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-red-600 to-red-800 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-red-600/50">
                            <i data-lucide="server" class="w-12 h-12 text-white"></i>
                        </div>
                        <h2 class="text-4xl font-bold text-white mb-4">Easypanel</h2>
                        <p class="text-white/60 text-lg mb-8">Painel de controle para gerenciar servidores, aplicações e infraestrutura cloud</p>
                        <a 
                            href="https://admin.infinityflowapp.com" 
                            target="_blank"
                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-bold text-white text-lg shadow-xl hover:shadow-[0_0_50px_rgba(199,26,29,0.6)] hover:-translate-y-1 transition-all duration-300"
                        >
                            <span>Acessar Easypanel</span>
                            <i data-lucide="external-link" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Portal: Minio -->
            <div id="content-minio" class="hidden">
                <div class="max-w-3xl mx-auto py-20">
                    <div class="glass border border-red-600/30 rounded-3xl p-12 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-red-600 to-red-800 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-red-600/50">
                            <i data-lucide="hard-drive" class="w-12 h-12 text-white"></i>
                        </div>
                        <h2 class="text-4xl font-bold text-white mb-4">Minio Storage</h2>
                        <p class="text-white/60 text-lg mb-8">Armazenamento de objetos de alto desempenho compatível com Amazon S3</p>
                        <a 
                            href="https://minio.infinityflowapp.com" 
                            target="_blank"
                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-bold text-white text-lg shadow-xl hover:shadow-[0_0_50px_rgba(199,26,29,0.6)] hover:-translate-y-1 transition-all duration-300"
                        >
                            <span>Acessar Minio Storage</span>
                            <i data-lucide="external-link" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Portal: Evolution API -->
            <div id="content-evolution" class="hidden">
                <div class="max-w-3xl mx-auto py-20">
                    <div class="glass border border-red-600/30 rounded-3xl p-12 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-red-600 to-red-800 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-red-600/50">
                            <i data-lucide="smartphone" class="w-12 h-12 text-white"></i>
                        </div>
                        <h2 class="text-4xl font-bold text-white mb-4">Evolution API</h2>
                        <p class="text-white/60 text-lg mb-8">API completa para integração WhatsApp com gerenciamento de instâncias e mensagens</p>
                        <a 
                            href="https://api.infinityflowapp.com/manager" 
                            target="_blank"
                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-bold text-white text-lg shadow-xl hover:shadow-[0_0_50px_rgba(199,26,29,0.6)] hover:-translate-y-1 transition-all duration-300"
                        >
                            <span>Acessar Evolution API</span>
                            <i data-lucide="external-link" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Portal: InfinityFlow Site -->
            <div id="content-site" class="hidden">
                <div class="max-w-3xl mx-auto py-20">
                    <div class="glass border border-red-600/30 rounded-3xl p-12 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-red-600 to-red-800 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-red-600/50">
                            <i data-lucide="globe" class="w-12 h-12 text-white"></i>
                        </div>
                        <h2 class="text-4xl font-bold text-white mb-4">InfinityFlow Site</h2>
                        <p class="text-white/60 text-lg mb-8">Acesse o site institucional da InfinityFlow e conheça nossos serviços</p>
                        <a 
                            href="https://infinityflowapp.com" 
                            target="_blank"
                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-bold text-white text-lg shadow-xl hover:shadow-[0_0_50px_rgba(199,26,29,0.6)] hover:-translate-y-1 transition-all duration-300"
                        >
                            <span>Acessar InfinityFlow Site</span>
                            <i data-lucide="external-link" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
    
    <!-- Portal Switching Script -->
    <script>
        // Portal switching function
        function showPortal(portalName) {
            // Hide all content sections
            const allContents = document.querySelectorAll('[id^="content-"]');
            allContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected content
            const selectedContent = document.getElementById('content-' + portalName);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
            
            // Update navigation active state
            const allLinks = document.querySelectorAll('[id^="link-"]');
            allLinks.forEach(link => {
                link.classList.remove('bg-[#C71A1D]/10', 'border-[#C71A1D]', 'text-white');
                link.classList.add('text-white/60', 'border-transparent');
            });
            
            const selectedLink = document.getElementById('link-' + portalName);
            if (selectedLink) {
                selectedLink.classList.remove('text-white/60', 'border-transparent');
                selectedLink.classList.add('bg-[#C71A1D]/10', 'border-[#C71A1D]', 'text-white');
            }
            
            // Reinitialize Lucide icons for new content
            lucide.createIcons();
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            showPortal('dashboard');
        });
    </script>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
