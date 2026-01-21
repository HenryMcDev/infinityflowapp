<?php
/**
 * InfinityFlow - Dashboard Administrativo
 * Página de destino após login bem-sucedido
 */

// Iniciar sessão
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Se não estiver logado, redirecionar para a página de login
    header("Location: index.php");
    exit();
}

// Dados do usuário logado
$username = $_SESSION['username'] ?? 'Administrador';
$email = $_SESSION['email'] ?? '';
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
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white bg-[#C71A1D]/10 rounded-xl border-l-2 border-[#C71A1D]">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Dashboard</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="workflow" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Automações</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Mensagens</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
                        <i data-lucide="database" class="w-5 h-5"></i>
                        <span class="font-medium tracking-wide">Banco de Dados</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 border-l-2 border-transparent hover:border-[#C71A1D]">
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
                <!-- Card 1 -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-6 h-6 text-blue-400"></i>
                        </div>
                        <span class="text-green-400 text-sm font-medium">+12%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1">1,234</h3>
                    <p class="text-white/50 text-sm">Usuários Ativos</p>
                </div>
                
                <!-- Card 2 -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="message-square" class="w-6 h-6 text-purple-400"></i>
                        </div>
                        <span class="text-green-400 text-sm font-medium">+8%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1">5,678</h3>
                    <p class="text-white/50 text-sm">Mensagens Enviadas</p>
                </div>
                
                <!-- Card 3 -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-[#C71A1D]/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="workflow" class="w-6 h-6 text-[#C71A1D]"></i>
                        </div>
                        <span class="text-green-400 text-sm font-medium">+23%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1">42</h3>
                    <p class="text-white/50 text-sm">Automações Ativas</p>
                </div>
                
                <!-- Card 4 -->
                <div class="glass border border-white/10 rounded-2xl p-6 hover:border-[#C71A1D]/30 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-6 h-6 text-green-400"></i>
                        </div>
                        <span class="text-green-400 text-sm font-medium">+15%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1">98.5%</h3>
                    <p class="text-white/50 text-sm">Taxa de Sucesso</p>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="glass border border-white/10 rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-white mb-6">Login realizado com sucesso! 🎉</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-white font-medium">Sistema de autenticação funcionando</p>
                            <p class="text-white/50 text-sm">Você está autenticado como <strong><?= htmlspecialchars($username) ?></strong></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-10 h-10 bg-blue-500/10 rounded-full flex items-center justify-center">
                            <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-white font-medium">Sessão ativa</p>
                            <p class="text-white/50 text-sm">Sua sessão está protegida e será mantida enquanto você estiver ativo</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
