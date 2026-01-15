<?php
/**
 * InfinityFlow - Landing Page (Versão Híbrida com n8n)
 * ===================================================
 */

// 1. BLOCO DE PROCESSAMENTO (O "Pai" da Automação)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Coleta e limpeza dos dados
    $nome = htmlspecialchars(trim($_POST['nome'] ?? ''));
    $cidade = htmlspecialchars(trim($_POST['cidade'] ?? ''));
    $uf = htmlspecialchars(trim($_POST['uf'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $whatsapp = htmlspecialchars(trim($_POST['whatsapp'] ?? ''));
    $volume_mensagens = htmlspecialchars(trim($_POST['volume_mensagens'] ?? ''));
    $descricao = htmlspecialchars(trim($_POST['descricao'] ?? ''));
    $data_visita = htmlspecialchars(trim($_POST['data_visita'] ?? ''));

    // URL DO SEU WEBHOOK N8N (Escondida do usuário final por segurança)
    // Lembre-se: quando ativar o workflow, troque "/webhook-test/" por "/webhook/"
    $webhook_url = 'https://adminn8n.infinityflowapp.com/webhook-test/cb3fd2b5-c0e1-4491-9916-f161bcf087b2';

    // Prepara o pacote de dados para o n8n
    $payload = json_encode([
        'nome' => $nome,
        'cidade' => $cidade,
        'uf' => $uf,
        'email' => $email,
        'whatsapp' => $whatsapp,
        'volume_mensagens' => $volume_mensagens,
        'descricao' => $descricao,
        'data_visita' => $data_visita,
        'origem' => 'Site Oficial InfinityFlow'
    ]);

    // Envia os dados para o n8n via cURL
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Se a requisição vier via JavaScript (AJAX), responde apenas com JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        if ($http_code >= 200 && $http_code < 300) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro no servidor de automação.']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="InfinityFlow - Automação inteligente de WhatsApp e redes sociais em Araxá/MG. Aumente sua produtividade com atendimento automatizado 24/7.">
    <meta name="keywords" content="automação whatsapp, chatbot, automação redes sociais, Araxá, atendimento automatizado">
    <meta name="author" content="InfinityFlow">
    
    <title>InfinityFlow - Automação de WhatsApp | Araxá/MG</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- AOS (Animate On Scroll) Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-black text-white antialiased">

    <!-- Navbar -->
    <nav class="navbar-blur fixed top-0 left-0 right-0 z-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center animate-pulse-glow">
                        <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-2xl font-bold gradient-text">InfinityFlow</span>
                </div>
                
                <!-- Navigation Links - Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="hover:text-red-500 transition-colors duration-300">Início</a>
                    <a href="#about" class="hover:text-red-500 transition-colors duration-300">Sobre</a>
                    <a href="#services" class="hover:text-red-500 transition-colors duration-300">Serviços</a>
                    <a href="#benefits" class="hover:text-red-500 transition-colors duration-300">Benefícios</a>
                    <a href="#testimonials" class="hover:text-red-500 transition-colors duration-300">Depoimentos</a>
                    <a href="#faq" class="hover:text-red-500 transition-colors duration-300">FAQ</a>
                    <a href="#contact" class="hover:text-red-500 transition-colors duration-300">Contato</a>
                </div>
                
                <!-- CTA Button -->
                <a href="#contact" class="hidden md:block btn-primary px-6 py-3 rounded-full font-semibold text-white">
                    Falar com Consultor
                </a>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-white">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-black/95 backdrop-blur-lg">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <a href="#home" class="block py-2 hover:text-red-500 transition-colors">Início</a>
                <a href="#about" class="block py-2 hover:text-red-500 transition-colors">Sobre</a>
                <a href="#services" class="block py-2 hover:text-red-500 transition-colors">Serviços</a>
                <a href="#benefits" class="block py-2 hover:text-red-500 transition-colors">Benefícios</a>
                <a href="#testimonials" class="block py-2 hover:text-red-500 transition-colors">Depoimentos</a>
                <a href="#faq" class="block py-2 hover:text-red-500 transition-colors">FAQ</a>
                <a href="#contact" class="btn-primary block text-center px-6 py-3 rounded-full font-semibold">Falar com Consultor</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="gradient-hero min-h-screen flex items-center pt-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6">
                        Automatize seu
                        <span class="gradient-text block">WhatsApp</span>
                        com Inteligência
                    </h1>
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                        Atenda seus clientes 24/7 com automação inteligente. Aumente suas vendas, reduza custos e escale seu atendimento com a InfinityFlow.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#contact" class="btn-primary px-8 py-4 rounded-full font-semibold text-lg text-center flex items-center justify-center gap-2">
                            <i data-lucide="bot" class="w-5 h-5"></i>
                            Quero Automatizar meu WhatsApp
                        </a>
                        <a href="#about" class="px-8 py-4 rounded-full font-semibold text-lg text-center border-2 border-red-600 hover:bg-red-600/10 transition-all duration-300">
                            Saiba Mais
                        </a>
                    </div>
                </div>
                
                <!-- Hero Image/Illustration -->
                <div data-aos="fade-left" data-aos-duration="1000" class="relative">
                    <div class="glass-card rounded-3xl p-8 animate-float">
                        <div class="aspect-square bg-gradient-to-br from-red-900/30 to-black rounded-2xl flex items-center justify-center">
                            <i data-lucide="message-circle" class="w-48 h-48 text-red-500 opacity-50"></i>
                        </div>
                    </div>
                    <!-- Decorative Elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-red-600/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-red-600/20 rounded-full blur-2xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section-spacer gradient-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    Sobre a <span class="gradient-text">InfinityFlow</span>
                </h2>
                <p class="text-xl text-gray-300 leading-relaxed mb-8">
                    Somos uma empresa de tecnologia especializada em automação inteligente de WhatsApp e redes sociais, localizada em Araxá/MG. Nossa missão é transformar a forma como empresas se comunicam com seus clientes, oferecendo soluções inovadoras que combinam eficiência, tecnologia de ponta e atendimento humanizado.
                </p>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Com anos de expertise em automação e inteligência artificial, desenvolvemos sistemas personalizados que se adaptam perfeitamente às necessidades do seu negócio, garantindo resultados mensuráveis e satisfação total.
                </p>
            </div>
            
            <!-- Stats -->
            <div class="grid md:grid-cols-3 gap-8 mt-16">
                <div class="glass-card rounded-2xl p-8 text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-5xl font-bold gradient-text mb-2">24/7</div>
                    <p class="text-gray-300">Atendimento Contínuo</p>
                </div>
                <div class="glass-card rounded-2xl p-8 text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-5xl font-bold gradient-text mb-2">+500</div>
                    <p class="text-gray-300">Clientes Atendidos/dia</p>
                </div>
                <div class="glass-card rounded-2xl p-8 text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-5xl font-bold gradient-text mb-2">98%</div>
                    <p class="text-gray-300">Satisfação dos Usuários</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section-spacer bg-black">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Nossos <span class="gradient-text">Serviços</span>
                </h2>
                <p class="text-xl text-gray-300">Soluções completas para automatizar sua comunicação</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- WhatsApp Automation -->
                <div class="glass-card rounded-3xl p-8" data-aos="zoom-in" data-aos-delay="100">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-2xl flex items-center justify-center mb-6">
                        <i data-lucide="message-square-text" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Automação de WhatsApp</h3>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Sistema completo de chatbot inteligente para WhatsApp. Atenda clientes, responda perguntas frequentes, processe pedidos e muito mais, tudo automaticamente.
                    </p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-300">Respostas automáticas personalizadas</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-300">Integração com CRM e sistemas</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-300">Analytics e relatórios detalhados</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-300">Disparo de mensagens em massa</span>
                        </li>
                    </ul>
                    <a href="#contact" class="btn-primary px-6 py-3 rounded-full font-semibold inline-block">
                        Começar Agora
                    </a>
                </div>
                
                <!-- Instagram Automation - Coming Soon -->
                <div class="glass-card rounded-3xl p-8 relative overflow-hidden" data-aos="zoom-in" data-aos-delay="200">
                    <div class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                        EM BREVE
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-600 to-gray-800 rounded-2xl flex items-center justify-center mb-6">
                        <i data-lucide="instagram" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Automação de Instagram</h3>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Em breve, você poderá automatizar suas interações no Instagram, responder mensagens diretas, comentários e muito mais com inteligência artificial.
                    </p>
                    <ul class="space-y-3 mb-6 opacity-60">
                        <li class="flex items-start gap-2">
                            <i data-lucide="clock" class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-400">Respostas automáticas de DM</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="clock" class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-400">Agendamento de posts</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="clock" class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-400">Análise de engajamento</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="clock" class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-gray-400">Gestão de comentários</span>
                        </li>
                    </ul>
                    <button disabled class="px-6 py-3 rounded-full font-semibold bg-gray-700 text-gray-400 cursor-not-allowed">
                        Disponível em Breve
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="section-spacer gradient-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Por que escolher a <span class="gradient-text">InfinityFlow</span>?
                </h2>
                <p class="text-xl text-gray-300">Transforme seu atendimento com tecnologia de ponta</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Benefit 1 -->
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="100">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="zap" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Velocidade</h3>
                    <p class="text-gray-300 text-sm">Respostas instantâneas aos seus clientes, sem tempo de espera</p>
                </div>
                
                <!-- Benefit 2 -->
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="200">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="clock" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Disponibilidade 24/7</h3>
                    <p class="text-gray-300 text-sm">Atendimento contínuo, todos os dias da semana, sem interrupções</p>
                </div>
                
                <!-- Benefit 3 -->
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="300">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="trending-down" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Redução de Custos</h3>
                    <p class="text-gray-300 text-sm">Economize até 80% em custos operacionais de atendimento</p>
                </div>
                
                <!-- Benefit 4 -->
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="400">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="trending-up" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Escalabilidade</h3>
                    <p class="text-gray-300 text-sm">Atenda milhares de clientes simultaneamente sem perder qualidade</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="section-spacer bg-black">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    O que dizem nossos <span class="gradient-text">Clientes</span>
                </h2>
                <p class="text-xl text-gray-300">Resultados reais de quem já automatizou</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="glass-card rounded-2xl p-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-1 mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                    </div>
                    <p class="text-gray-300 mb-4 italic">
                        "A automação da InfinityFlow revolucionou nosso atendimento. Conseguimos responder 10x mais clientes sem aumentar a equipe!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">MC</span>
                        </div>
                        <div>
                            <p class="font-semibold">Maria Clara</p>
                            <p class="text-sm text-gray-400">Loja de Roupas - Araxá</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="glass-card rounded-2xl p-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center gap-1 mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                    </div>
                    <p class="text-gray-300 mb-4 italic">
                        "Impressionante! O chatbot é tão inteligente que nossos clientes nem percebem que estão falando com um robô."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">RS</span>
                        </div>
                        <div>
                            <p class="font-semibold">Roberto Silva</p>
                            <p class="text-sm text-gray-400">Restaurante - Araxá</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="glass-card rounded-2xl p-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center gap-1 mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                    </div>
                    <p class="text-gray-300 mb-4 italic">
                        "Reduzi custos em 70% e aumentei a satisfação dos clientes. O ROI foi incrível!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">AP</span>
                        </div>
                        <div>
                            <p class="font-semibold">Ana Paula</p>
                            <p class="text-sm text-gray-400">Clínica Médica - Araxá</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="section-spacer gradient-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Perguntas <span class="gradient-text">Frequentes</span>
                </h2>
                <p class="text-xl text-gray-300">Tire suas dúvidas sobre automação</p>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-4">
                <!-- FAQ Item 1 -->
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Como funciona a automação de WhatsApp?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Nossa automação utiliza inteligência artificial avançada para interpretar mensagens dos clientes e responder de forma natural e personalizada. O sistema aprende com cada interação e pode ser configurado de acordo com as necessidades específicas do seu negócio.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Quanto tempo leva para implementar?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            O processo de implementação geralmente leva de 3 a 7 dias úteis, dependendo da complexidade do projeto. Incluímos configuração, treinamento do sistema e testes completos antes do lançamento.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Posso personalizar as respostas do chatbot?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Absolutamente! Você tem controle total sobre todas as respostas, fluxos de conversa e comportamento do chatbot. Oferecemos uma interface intuitiva para edição e também suporte técnico para personalizações avançadas.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Qual o custo da automação?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Oferecemos planos flexíveis que se adaptam ao tamanho do seu negócio e volume de mensagens. Entre em contato conosco para receber uma proposta personalizada sem compromisso.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="500">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">A automação está de acordo com as políticas do WhatsApp?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Sim! Utilizamos a API oficial do WhatsApp Business e seguimos todas as diretrizes e políticas da plataforma. Sua conta está 100% segura e em conformidade com os termos de uso.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact" class="section-spacer bg-black">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Mascot Introduction -->
                <div class="text-center mb-12" data-aos="fade-up">
                    <div class="w-32 h-32 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-6 animate-float">
                        <i data-lucide="bot" class="w-16 h-16 text-white"></i>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">
                        Olá, eu sou o <span class="gradient-text">Flow</span>!
                    </h2>
                    <p class="text-xl text-gray-300">
                        Preencha os dados abaixo para agendarmos nossa reunião e começar sua automação
                    </p>
                </div>
                
                <!-- Success/Error Messages -->
                <?php if (isset($success_message)): ?>
                <div class="bg-green-900/30 border-2 border-green-500 rounded-2xl p-4 mb-6" data-aos="fade-down">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-400"></i>
                        <p class="text-green-100"><?php echo $success_message; ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($errors) && !empty($errors)): ?>
                <div class="bg-red-900/30 border-2 border-red-500 rounded-2xl p-4 mb-6" data-aos="fade-down">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-400 flex-shrink-0"></i>
                        <div>
                            <?php foreach ($errors as $error): ?>
                            <p class="text-red-100"><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Contact Form -->
                <form id="infinityForm" class="glass-card rounded-3xl p-8" data-aos="fade-up">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <!-- Nome -->
                        <div>
                            <label for="nome" class="block text-sm font-semibold mb-2">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" required 
                                   class="input-field w-full px-4 py-3 rounded-xl text-white"
                                   placeholder="Seu nome completo">
                        </div>
                        
                        <!-- Cidade -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">Cidade</label>
                            <input type="text" name="cidade" class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="Sua cidade">
                        </div>
                        
                        <!-- UF -->
                        <div>
                            <label for="uf" class="block text-sm font-semibold mb-2">UF</label>
                            <select id="uf" name="uf" 
                                    class="input-field w-full px-4 py-3 rounded-xl text-white">
                                <option value="">Selecione</option>
                                <option value="AC">AC - Acre</option>
                                <option value="AL">AL - Alagoas</option>
                                <option value="AP">AP - Amapá</option>
                                <option value="AM">AM - Amazonas</option>
                                <option value="BA">BA - Bahia</option>
                                <option value="CE">CE - Ceará</option>
                                <option value="DF">DF - Distrito Federal</option>
                                <option value="ES">ES - Espírito Santo</option>
                                <option value="GO">GO - Goiás</option>
                                <option value="MA">MA - Maranhão</option>
                                <option value="MT">MT - Mato Grosso</option>
                                <option value="MS">MS - Mato Grosso do Sul</option>
                                <option value="MG">MG - Minas Gerais</option>
                                <option value="PA">PA - Pará</option>
                                <option value="PB">PB - Paraíba</option>
                                <option value="PR">PR - Paraná</option>
                                <option value="PE">PE - Pernambuco</option>
                                <option value="PI">PI - Piauí</option>
                                <option value="RJ">RJ - Rio de Janeiro</option>
                                <option value="RN">RN - Rio Grande do Norte</option>
                                <option value="RS">RS - Rio Grande do Sul</option>
                                <option value="RO">RO - Rondônia</option>
                                <option value="RR">RR - Roraima</option>
                                <option value="SC">SC - Santa Catarina</option>
                                <option value="SP">SP - São Paulo</option>
                                <option value="SE">SE - Sergipe</option>
                                <option value="TO">TO - Tocantins</option>
                            </select>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold mb-2">E-mail *</label>
                            <input type="email" id="email" name="email" required 
                                   class="input-field w-full px-4 py-3 rounded-xl text-white"
                                   placeholder="seu@email.com">
                        </div>
                        
                        <!-- WhatsApp -->
                        <div>
                            <label for="whatsapp" class="block text-sm font-semibold mb-2">WhatsApp (com DDD) *</label>
                            <input type="tel" id="whatsapp" name="whatsapp" required 
                                   class="input-field w-full px-4 py-3 rounded-xl text-white"
                                   placeholder="(34) 99999-9999">
                        </div>
                        
                        <!-- Volume de Mensagens -->
                        <div>
                            <label for="volume_mensagens" class="block text-sm font-semibold mb-2">Volume de Mensagens Diárias</label>
                            <select id="volume_mensagens" name="volume_mensagens" 
                                    class="input-field w-full px-4 py-3 rounded-xl text-white">
                                <option value="">Selecione</option>
                                <option value="0-50">0 - 50 mensagens</option>
                                <option value="51-200">51 - 200 mensagens</option>
                                <option value="201-500">201 - 500 mensagens</option>
                                <option value="500+">Mais de 500 mensagens</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Descrição do Projeto -->
                    <div class="mb-6">
                        <label for="descricao" class="block text-sm font-semibold mb-2">Descrição do Projeto</label>
                        <textarea id="descricao" name="descricao" rows="4" 
                                  class="input-field w-full px-4 py-3 rounded-xl text-white resize-none"
                                  placeholder="Conte-nos um pouco sobre seu negócio e o que você espera da automação..."></textarea>
                    </div>
                    
                    <!-- Data e Hora da Visita -->
                    <div class="mb-8">
                        <label for="data_visita" class="block text-sm font-semibold mb-2">Data e Hora Preferencial para Reunião</label>
                        <input type="datetime-local" id="data_visita" name="data_visita" 
                               class="input-field w-full px-4 py-3 rounded-xl text-white">
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="btn-primary w-full py-4 rounded-xl font-bold text-lg flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        <span id="btnText">Agendar Reunião com o Flow</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black border-t border-red-900/30 py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center">
                            <i data-lucide="zap" class="w-5 h-5 text-white"></i>
                        </div>
                        <span class="text-xl font-bold gradient-text">InfinityFlow</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Automação inteligente para o futuro do seu negócio.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold mb-4">Links Rápidos</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-red-500 transition-colors">Início</a></li>
                        <li><a href="#about" class="hover:text-red-500 transition-colors">Sobre</a></li>
                        <li><a href="#services" class="hover:text-red-500 transition-colors">Serviços</a></li>
                        <li><a href="#contact" class="hover:text-red-500 transition-colors">Contato</a></li>
                    </ul>
                </div>
                
                <!-- Services -->
                <div>
                    <h4 class="font-bold mb-4">Serviços</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>Automação WhatsApp</li>
                        <li>Chatbot Inteligente</li>
                        <li>Integrações API</li>
                        <li>Consultoria Digital</li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="font-bold mb-4">Contato</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i>
                            Araxá/MG
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-red-500"></i>
                            contato@infinityflow.com.br
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4 text-red-500"></i>
                            (34) 9999-9999
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Social Media -->
            <div class="flex justify-center gap-6 mb-8">
                <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-red-600 rounded-full flex items-center justify-center transition-all duration-300">
                    <i data-lucide="facebook" class="w-5 h-5"></i>
                </a>
                <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-red-600 rounded-full flex items-center justify-center transition-all duration-300">
                    <i data-lucide="instagram" class="w-5 h-5"></i>
                </a>
                <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-red-600 rounded-full flex items-center justify-center transition-all duration-300">
                    <i data-lucide="linkedin" class="w-5 h-5"></i>
                </a>
                <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-red-600 rounded-full flex items-center justify-center transition-all duration-300">
                    <i data-lucide="youtube" class="w-5 h-5"></i>
                </a>
            </div>
            
            <!-- Copyright -->
            <div class="text-center text-gray-500 text-sm border-t border-gray-800 pt-8">
                <p>&copy; 2026 InfinityFlow. Todos os direitos reservados.</p>
                <p class="mt-2">Desenvolvido com <i data-lucide="heart" class="w-4 h-4 inline text-red-500 fill-red-500"></i> pela equipe InfinityFlow</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/5534999999999?text=Olá! Gostaria de saber mais sobre automação de WhatsApp" 
       target="_blank" 
       class="whatsapp-float"
       aria-label="Entre em contato pelo WhatsApp">
        <i data-lucide="message-circle" class="w-7 h-7"></i>
    </a>

    <!-- Scripts -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
        
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking on a link
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
        
        // Accordion Toggle Function
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('i[data-lucide="chevron-down"]');
            
            // Toggle active class
            content.classList.toggle('active');
            
            // Rotate icon
            if (content.classList.contains('active')) {
                icon.style.transform = 'rotate(180deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Smooth Scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Navbar background on scroll
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(0, 0, 0, 0.95)';
            } else {
                navbar.style.background = 'rgba(0, 0, 0, 0.85)';
            }
        });
        
        // 2. LÓGICA DE ENVIO MODERNA (JavaScript)
        const form = document.getElementById('infinityForm');
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        if (form && btn && btnText) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Estado de carregamento
                btn.disabled = true;
                btnText.innerText = "Enviando dados ao Flow...";
                btn.classList.add('opacity-50', 'cursor-not-allowed');

                const formData = new FormData(form);
                
                try {
                    // Envia os dados para este mesmo arquivo PHP
                    const response = await fetch('index.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Avisa o PHP que é AJAX
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('🚀 Sucesso! O Flow recebeu sua mensagem e o n8n já iniciou o processo de automação.');
                        form.reset();
                    } else {
                        alert('❌ Ops! O servidor PHP não conseguiu falar com o n8n. Verifique o link do webhook.');
                    }
                } catch (error) {
                    alert('⚠️ Falha crítica: Verifique se sua VPS está online ou se o PHP cURL está ativado.');
                } finally {
                    btn.disabled = false;
                    btnText.innerText = "Agendar Reunião com o Flow";
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });
        }
    </script>
</body>
</html>
