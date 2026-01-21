<?php
/**
 * InfinityFlow - Administrative Area
 * This page is protected and requires authentication
 */

// Include authentication configuration
require_once __DIR__ . '/../config/auth.php';

// Check if user is authenticated
// Note: We keep the login form in this page for a better UX
// but you could also redirect to a separate login page if preferred
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
            <form id="loginForm" class="space-y-6">
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-white/70 tracking-wide">Usuário</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Digite seu usuário"
                        required
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
                
                <div id="errorMessage" class="text-[#C71A1D] text-sm text-center min-h-5 font-medium"></div>
            </form>
        </div>
    </div>

    <!-- Dashboard View -->
    <div id="dashboardView" class="hidden min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-80 glass-strong border-r border-white/10 flex flex-col fixed h-screen left-0 top-0 z-50 slide-in-left">
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
            
            <!-- Navigation Menu -->
            <nav class="flex-1 py-8 px-4 overflow-y-auto">
                <div class="space-y-2">
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 hover:shadow-[0_0_20px_rgba(199,26,29,0.1)] border-l-2 border-transparent hover:border-[#C71A1D]" data-service="easypanel">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                        <span class="font-medium tracking-wide">Easypanel</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 hover:shadow-[0_0_20px_rgba(199,26,29,0.1)] border-l-2 border-transparent hover:border-[#C71A1D]" data-service="n8n">
                        <i data-lucide="workflow" class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                        <span class="font-medium tracking-wide">n8n</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 hover:shadow-[0_0_20px_rgba(199,26,29,0.1)] border-l-2 border-transparent hover:border-[#C71A1D]" data-service="evolution">
                        <i data-lucide="message-square" class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                        <span class="font-medium tracking-wide">Evolution API</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 hover:shadow-[0_0_20px_rgba(199,26,29,0.1)] border-l-2 border-transparent hover:border-[#C71A1D]" data-service="minio">
                        <i data-lucide="database" class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                        <span class="font-medium tracking-wide">Minio</span>
                    </a>
                    
                    <a href="#" class="nav-item group flex items-center gap-4 px-5 py-4 text-white/60 rounded-xl transition-all duration-300 hover:text-white hover:bg-[#C71A1D]/10 hover:shadow-[0_0_20px_rgba(199,26,29,0.1)] border-l-2 border-transparent hover:border-[#C71A1D]" data-service="site">
                        <i data-lucide="globe" class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                        <span class="font-medium tracking-wide">Site InfinityFlow</span>
                    </a>
                </div>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="p-6 border-t border-white/10">
                <button id="logoutBtn" class="group w-full flex items-center gap-3 px-5 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white/60 font-medium tracking-wide transition-all duration-300 hover:bg-[#C71A1D]/10 hover:border-[#C71A1D]/50 hover:text-white hover:shadow-[0_0_20px_rgba(199,26,29,0.2)]">
                    <i data-lucide="log-out" class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                    <span>Sair</span>
                </button>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 ml-80 bg-black min-h-screen">
            <div id="contentArea" class="p-12 fade-in-up">
                <!-- Welcome Message -->
                <div class="text-center py-20">
                    <h1 class="text-5xl font-bold tracking-tight mb-6 bg-gradient-to-r from-white via-white/90 to-white/70 bg-clip-text text-transparent">
                        Bem-vindo ao InfinityFlow
                    </h1>
                    <p class="text-xl text-white/50 font-light tracking-wide">
                        Selecione um serviço no menu lateral para começar
                    </p>
                </div>
            </div>
        </main>
    </div>

    <!-- Custom JavaScript -->
    <script src="./js/scripts.js"></script>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
