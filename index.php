<?php

// Redirecionamento de non-www para www
if ($_SERVER['HTTP_HOST'] === 'infinityflowapp.com') {
    header("Location: https://www.infinityflowapp.com" . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}
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

    // URL DO SEU WEBHOOK N8N
    $webhook_url = 'https://n8n.infinityflowapp.com/webhook/infinityflowapp-website';

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

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

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
    <meta name="description" content="InfinityFlow - Especialistas em Automação de WhatsApp e Processos Inteligentes em Araxá/MG. Melhore seu atendimento com tecnologia de ponta.">
    <meta name="keywords" content="automação whatsapp, chatbot araxá, inteligência artificial vendas, n8n automação, suporte whatsapp automático">
    <meta name="author" content="InfinityFlow">
    
    <title>InfinityFlow | Automação Inteligente de Atendimento</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="Imagens/Logo1.png">
    <link rel="shortcut icon" type="image/png" href="Imagens/Logo1.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
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

    <nav class="navbar-blur fixed top-0 left-0 right-0 z-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center animate-pulse-glow p-1.5">
                        <img src="Imagens/Logo1.png" alt="InfinityFlow Logo" class="w-full h-full object-contain">
                    </div>
                    <span class="text-2xl font-bold gradient-text">InfinityFlow</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="hover:text-red-500 transition-colors duration-300">Início</a>
                    <a href="#about" class="hover:text-red-500 transition-colors duration-300">Sobre</a>
                    <a href="#services" class="hover:text-red-500 transition-colors duration-300">Serviços</a>
                    <a href="#pricing" class="hover:text-red-500 transition-colors duration-300">Planos</a>
                    <a href="#benefits" class="hover:text-red-500 transition-colors duration-300">Diferenciais</a>
                    <a href="#faq" class="hover:text-red-500 transition-colors duration-300">FAQ</a>
                    <a href="#contact" class="hover:text-red-500 transition-colors duration-300">Contato</a>
                </div>
                
                <a href="#contact" class="hidden md:block btn-primary px-6 py-3 rounded-full font-semibold text-white">
                    Consultoria Gratuita
                </a>
                
                <button id="mobile-menu-btn" class="md:hidden text-white">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <div id="mobile-menu" class="hidden md:hidden bg-black/95 backdrop-blur-lg">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <a href="#home" class="block py-2 hover:text-red-500 transition-colors">Início</a>
                <a href="#about" class="block py-2 hover:text-red-500 transition-colors">Sobre</a>
                <a href="#services" class="block py-2 hover:text-red-500 transition-colors">Serviços</a>
                <a href="#pricing" class="block py-2 hover:text-red-500 transition-colors">Planos</a>
                <a href="#benefits" class="block py-2 hover:text-red-500 transition-colors">Diferenciais</a>
                <a href="#faq" class="block py-2 hover:text-red-500 transition-colors">FAQ</a>
                <a href="#contact" class="btn-primary block text-center px-6 py-3 rounded-full font-semibold">Consultoria Gratuita</a>
            </div>
        </div>
    </nav>

    <section id="home" class="gradient-hero min-h-screen flex items-center pt-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6">
                        Sua empresa no
                        <span class="gradient-text block">Automático</span>
                        com IA de Verdade
                    </h1>
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                        Pare de perder vendas por demora no atendimento. Criamos fluxos inteligentes no WhatsApp que qualificam leads e atendem seus clientes 24 horas por dia.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#contact" class="btn-primary px-8 py-4 rounded-full font-semibold text-lg text-center flex items-center justify-center gap-2">
                            <i data-lucide="bot" class="w-5 h-5"></i>
                            Conhecer Soluções
                        </a>
                        <a href="#about" class="px-8 py-4 rounded-full font-semibold text-lg text-center border-2 border-red-600 hover:bg-red-600/10 transition-all duration-300">
                            Como Funciona?
                        </a>
                    </div>
                </div>
                
                <div data-aos="fade-left" data-aos-duration="1000" class="relative">
                    <div class="glass-card rounded-3xl p-8 animate-float">
                        <div class="aspect-square bg-gradient-to-br from-red-900/30 to-black rounded-2xl flex items-center justify-center">
                            <i data-lucide="cpu" class="w-48 h-48 text-red-500 opacity-50"></i>
                        </div>
                    </div>
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-red-600/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-red-600/20 rounded-full blur-2xl"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="section-spacer gradient-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    Foco em <span class="gradient-text">Resultados</span>, não apenas robôs.
                </h2>
                <p class="text-xl text-gray-300 leading-relaxed mb-8">
                    A <strong>InfinityFlow</strong> nasceu em Araxá/MG com um propósito claro: democratizar o uso da Inteligência Artificial para pequenas e médias empresas. Não entregamos apenas uma ferramenta, entregamos tempo e processos otimizados.
                </p>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Especialistas em integração de sistemas, utilizamos tecnologias modernas (como n8n e IA Generativa) para criar experiências de atendimento que parecem humanas, mas possuem a eficiência de uma máquina.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 mt-16">
                <div class="glass-card rounded-2xl p-8 text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-5xl font-bold gradient-text mb-2">100%</div>
                    <p class="text-gray-300">Personalizado para seu nicho</p>
                </div>
                <div class="glass-card rounded-2xl p-8 text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-5xl font-bold gradient-text mb-2">0s</div>
                    <p class="text-gray-300">Tempo de espera inicial</p>
                </div>
                <div class="glass-card rounded-2xl p-8 text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-5xl font-bold gradient-text mb-2">+Produtividade</div>
                    <p class="text-gray-300">Foque na estratégia, deixe o chat conosco</p>
                </div>
            </div>
        </div>
    </section>

<section id="faq" class="section-spacer gradient-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Perguntas <span class="gradient-text">Frequentes</span>
                </h2>
                <p class="text-xl text-gray-300">Tudo o que você precisa saber sobre a nossa automação</p>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-4">
                
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">A IA realmente entende o que o meu cliente escreve?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Sim. Diferente de sistemas antigos que só entendem números ou comandos exatos, a nossa IA analisa o texto enviado pelo cliente. Ela identifica a intenção da mensagem e fornece a resposta mais adequada baseada nas informações da sua empresa, tornando a conversa muito mais natural.
                        </p>
                    </div>
                </div>
                
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Eu ainda consigo intervir e conversar manualmente?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Com certeza. A automação serve para filtrar as dúvidas repetitivas e qualificar o contato. Sempre que você ou sua equipe desejarem assumir a conversa manualmente, o sistema permite o transbordo imediato, para que você foque apenas na parte estratégica e no fechamento da venda.
                        </p>
                    </div>
                </div>
                
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Existe risco de o meu número ser banido?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Nós priorizamos a segurança. Configuramos a automação para respeitar os limites de envio e o comportamento humano. Como o foco é o atendimento receptivo (responder quem entra em contato com você), o risco é minimizado, seguindo as boas práticas recomendadas pela plataforma.
                        </p>
                    </div>
                </div>
                
                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Preciso deixar meu celular ou computador ligado?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Não. Todo o sistema da <strong>InfinityFlow</strong> roda em servidores na nuvem de alta performance. Isso garante que sua empresa atenda 24 horas por dia, 7 dias por semana, mesmo que seus dispositivos pessoais estejam desligados ou sem internet.
                        </p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="500">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Posso remover a marca da InfinityFlow das mensagens?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            Sim! No <strong>Plano Infinity Premium</strong>, sua assistente é 100% personalizada com o nome e a marca da sua empresa, sem qualquer menção à nossa plataforma (White Label). Você terá controle total sobre a identidade visual, incluindo nome customizado, foto de perfil com sua logo e zero assinaturas "Powered by InfinityFlow" nas mensagens. Ideal para empresas que desejam oferecer a tecnologia como se fosse 100% própria.
                        </p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="600">
                    <button class="accordion-button w-full text-left p-6 flex items-center justify-between" onclick="toggleAccordion(this)">
                        <h3 class="text-lg font-semibold pr-4">Qual é o principal ganho ao contratar a automação?</h3>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-red-500 flex-shrink-0 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content px-6 pb-0">
                        <p class="text-gray-300 pb-6">
                            O principal ganho é a <strong>recuperação do seu tempo</strong>. Você deixa de ser um "atendente de dúvidas básicas" para se tornar o gestor do seu negócio. Além disso, a velocidade de resposta imediata aumenta drasticamente a satisfação do cliente e as chances de conversão.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="section-spacer gradient-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Diferenciais <span class="gradient-text">InfinityFlow</span>
                </h2>
                <p class="text-xl text-gray-300">Por que não somos "apenas mais um chatbot"</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="100">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Segurança</h3>
                    <p class="text-gray-300 text-sm">Uso de APIs oficiais para garantir a estabilidade do seu número</p>
                </div>
                
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="200">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="map-pin" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Suporte Local</h3>
                    <p class="text-gray-300 text-sm">Empresa de Araxá para o mundo. Atendimento próximo e direto</p>
                </div>
                
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="300">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="code-2" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Sob Medida</h3>
                    <p class="text-gray-300 text-sm">Não usamos modelos prontos. Construímos do zero para sua dor</p>
                </div>
                
                <div class="glass-card rounded-2xl p-6 text-center" data-aos="flip-left" data-aos-delay="400">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="rocket" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Escalabilidade</h3>
                    <p class="text-gray-300 text-sm">Seu sistema pronto para crescer junto com seu volume de vendas</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="section-spacer gradient-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Nossos <span class="gradient-text">Planos</span>
                </h2>
                <p class="text-xl text-gray-300">Escolha a solução ideal para o seu negócio</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto mb-16">
                <!-- PLANO FLOW (Básico) -->
                <div class="glass-card rounded-3xl p-8 relative" data-aos="fade-up" data-aos-delay="100">
                    <div class="inline-block px-4 py-1 bg-red-600/20 rounded-full text-sm font-semibold mb-6">
                        Básico
                    </div>
                    <h3 class="text-3xl font-bold mb-2">Plano Flow</h3>
                    <p class="text-gray-400 mb-6">Para pequenos negócios e autônomos</p>
                    
                    <div class="bg-red-600/10 border border-red-600/30 rounded-xl p-4 mb-8">
                        <p class="text-lg font-semibold text-red-400">💡 O poder da IA acessível para todos</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <i data-lucide="bot" class="w-5 h-5 text-red-500 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong>Assistente Virtual "Flow"</strong>
                                <p class="text-sm text-gray-400">Identidade padrão da InfinityFlow</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="message-square" class="w-5 h-5 text-red-500 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong>Assinatura "Powered by InfinityFlow"</strong>
                                <p class="text-sm text-gray-400">Em todas as mensagens enviadas</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="table" class="w-5 h-5 text-red-500 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong>Integração com Google Sheets</strong>
                                <p class="text-sm text-gray-400">Sincronização de dados em tempo real</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="mail" class="w-5 h-5 text-red-500 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong>Suporte via Ticket/E-mail</strong>
                                <p class="text-sm text-gray-400">Resposta em até 24h úteis</p>
                            </div>
                        </li>
                    </ul>
                    
                    <a href="#contact" class="btn-primary block text-center px-6 py-4 rounded-xl font-semibold text-lg">
                        Começar agora
                    </a>
                </div>
                
                <!-- INFINITY PREMIUM (Destaque) -->
                <div class="glass-card rounded-3xl p-8 relative border-2 border-red-600 animate-pulse-glow" data-aos="fade-up" data-aos-delay="200">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-red-600 to-red-800 px-6 py-2 rounded-full text-sm font-bold shadow-lg">
                        ⭐ Mais Popular
                    </div>
                    
                    <div class="inline-block px-4 py-1 bg-red-600 rounded-full text-sm font-semibold mb-6 mt-4">
                        Premium • White Label
                    </div>
                    <h3 class="text-3xl font-bold mb-2 gradient-text">Infinity Premium</h3>
                    <p class="text-gray-300 mb-6">Para empresas que buscam exclusividade e escala</p>
                    
                    <div class="bg-gradient-to-r from-red-600/20 to-red-800/20 border border-red-600/50 rounded-xl p-4 mb-8">
                        <p class="text-lg font-semibold text-red-300">🎯 Sua marca, nossa tecnologia</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <i data-lucide="sparkles" class="w-5 h-5 text-red-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="text-red-300">IA com Nome e Personalidade 100% personalizada</strong>
                                <p class="text-sm text-gray-400">Totalmente adaptada à identidade da sua marca</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="shield-off" class="w-5 h-5 text-red-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="text-red-300">Sem assinaturas ou marcas da InfinityFlow</strong>
                                <p class="text-sm text-gray-400">Marca Branca total (White Label)</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="database" class="w-5 h-5 text-red-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="text-red-300">Integração com Sistemas</strong>
                                <p class="text-sm text-gray-400">ERP, Agendas, Controle de Estoque e mais</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 text-red-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="text-red-300">Dashboard de Métricas em Tempo Real</strong>
                                <p class="text-sm text-gray-400">Acompanhe performance e conversões</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="headphones" class="w-5 h-5 text-red-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="text-red-300">Suporte VIP via WhatsApp</strong>
                                <p class="text-sm text-gray-400">Atendimento prioritário e personalizado</p>
                            </div>
                        </li>
                    </ul>
                    
                    <a href="#contact" class="btn-primary block text-center px-6 py-4 rounded-xl font-semibold text-lg shadow-2xl">
                        Falar com Consultor
                    </a>
                </div>
            </div>
            
            <!-- TABELA COMPARATIVA -->
            <div class="max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                <h3 class="text-2xl font-bold text-center mb-8">
                    Comparativo de <span class="gradient-text">Transparência</span>
                </h3>
                
                <div class="glass-card rounded-2xl overflow-hidden hidden md:block">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-red-600/10">
                                <tr>
                                    <th class="px-6 py-4 text-left font-semibold">Características</th>
                                    <th class="px-6 py-4 text-center font-semibold">Plano Flow</th>
                                    <th class="px-6 py-4 text-center font-semibold">Infinity Premium</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                <tr class="hover:bg-red-600/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="user" class="w-4 h-4 text-red-500"></i>
                                            <strong>Identidade da IA</strong>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-400">
                                        "Flow" (Padrão)
                                    </td>
                                    <td class="px-6 py-4 text-center text-red-300 font-semibold">
                                        100% Personalizada
                                    </td>
                                </tr>
                                <tr class="hover:bg-red-600/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="message-circle" class="w-4 h-4 text-red-500"></i>
                                            <strong>Assinatura WhatsApp</strong>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-400">
                                        "Powered by InfinityFlow"
                                    </td>
                                    <td class="px-6 py-4 text-center text-red-300 font-semibold">
                                        Sem Assinatura
                                    </td>
                                </tr>
                                <tr class="hover:bg-red-600/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="image" class="w-4 h-4 text-red-500"></i>
                                            <strong>Foto de Perfil</strong>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-400">
                                        Logo Flow (Padrão)
                                    </td>
                                    <td class="px-6 py-4 text-center text-red-300 font-semibold">
                                        Sua Logo
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Versão Mobile (lista) -->
                <div class="md:hidden space-y-4">
                    <div class="glass-card rounded-xl p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="user" class="w-5 h-5 text-red-500"></i>
                            <h4 class="font-bold">Identidade da IA</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Plano Flow:</span>
                                <span class="text-sm">"Flow" (Padrão)</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Infinity Premium:</span>
                                <span class="text-sm text-red-300 font-semibold">100% Personalizada</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-xl p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="message-circle" class="w-5 h-5 text-red-500"></i>
                            <h4 class="font-bold">Assinatura WhatsApp</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Plano Flow:</span>
                                <span class="text-sm">"Powered by..."</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Infinity Premium:</span>
                                <span class="text-sm text-red-300 font-semibold">Sem Assinatura</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-xl p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="image" class="w-5 h-5 text-red-500"></i>
                            <h4 class="font-bold">Foto de Perfil</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Plano Flow:</span>
                                <span class="text-sm">Logo Flow</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Infinity Premium:</span>
                                <span class="text-sm text-red-300 font-semibold">Sua Logo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section id="infinityflow" class="section-spacer bg-black">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Por que a <span class="gradient-text">InfinityFlow</span>?
                </h2>
                <p class="text-xl text-gray-300">Entregamos inteligência, não apenas automação</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass-card rounded-2xl p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-red-600/20 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="map-pin" class="w-8 h-8 text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Presença em Araxá</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Esqueça o suporte impessoal. Estamos em Araxá/MG para entender o seu negócio de perto e oferecer uma solução que realmente se adapte ao público da nossa região.
                    </p>
                </div>
                
                <div class="glass-card rounded-2xl p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-red-600/20 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="user-cog" class="w-8 h-8 text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Automação com "DNA"</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Não usamos modelos genéricos. O Flow é treinado com as informações exclusivas da sua empresa, garantindo que o atendimento tenha o tom de voz da sua marca.
                    </p>
                </div>
                
                <div class="glass-card rounded-2xl p-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-red-600/20 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="target" class="w-8 h-8 text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Qualificação de Leads</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Nossa IA não apenas "responde", ela qualifica. O sistema identifica quem é um cliente potencial e quem é apenas um curioso, economizando o tempo da sua equipe.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section-spacer bg-black">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12" data-aos="fade-up">
                    <div class="w-32 h-32 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center mx-auto mb-6 animate-float">
                        <i data-lucide="bot" class="w-16 h-16 text-white"></i>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">
                        Fale com o <span class="gradient-text">Flow</span>
                    </h2>
                    <p class="text-xl text-gray-300">
                        Preencha os dados e receba um diagnóstico da sua comunicação em até 24h.
                    </p>
                </div>
                
                <form id="infinityForm" class="glass-card rounded-3xl p-8" data-aos="fade-up">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="nome" class="block text-sm font-semibold mb-2">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" required 
                                   class="input-field w-full px-4 py-3 rounded-xl text-white"
                                   placeholder="Seu nome completo">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Cidade</label>
                            <input type="text" name="cidade" class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="Sua cidade">
                        </div>
                        
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
                        
                        <div>
                            <label for="email" class="block text-sm font-semibold mb-2">E-mail *</label>
                            <input type="email" id="email" name="email" required 
                                   class="input-field w-full px-4 py-3 rounded-xl text-white"
                                   placeholder="seu@email.com">
                        </div>
                        
                        <div>
                            <label for="whatsapp" class="block text-sm font-semibold mb-2">WhatsApp (com DDD) *</label>
                            <input type="tel" id="whatsapp" name="whatsapp" required 
                                   class="input-field w-full px-4 py-3 rounded-xl text-white"
                                   placeholder="(34) 99999-9999">
                        </div>
                        
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
                    
                    <div class="mb-6">
                        <label for="descricao" class="block text-sm font-semibold mb-2">Descrição do Projeto</label>
                        <textarea id="descricao" name="descricao" rows="4" 
                                  class="input-field w-full px-4 py-3 rounded-xl text-white resize-none"
                                  placeholder="Conte-nos um pouco sobre seu negócio e o que você espera da automação..."></textarea>
                    </div>
                    
                    <div class="mb-8">
                        <label for="data_visita" class="block text-sm font-semibold mb-2">Data e Hora Preferencial para Reunião</label>
                        <input type="datetime-local" id="data_visita" name="data_visita" 
                               class="input-field w-full px-4 py-3 rounded-xl text-white">
                    </div>
                    
                    <button type="submit" id="submitBtn" class="btn-primary w-full py-4 rounded-xl font-bold text-lg flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        <span id="btnText">Enviar para Análise do Flow</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-black border-t border-red-900/30 py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center p-1">
                            <img src="Imagens/Logo1.png" alt="InfinityFlow Logo" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xl font-bold gradient-text">InfinityFlow</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Especialistas em automação de processos e IA para o mercado brasileiro.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Links Rápidos</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-red-500 transition-colors">Início</a></li>
                        <li><a href="#about" class="hover:text-red-500 transition-colors">Sobre</a></li>
                        <li><a href="#services" class="hover:text-red-500 transition-colors">Serviços</a></li>
                        <li><a href="#contact" class="hover:text-red-500 transition-colors">Contato</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Serviços</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>Automação WhatsApp</li>
                        <li>Integração n8n</li>
                        <li>Treinamento de IA</li>
                        <li>Consultoria de Processos</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Contato</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i>
                            Araxá/MG
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-red-500"></i>
                            henryoficial37@gmail.com
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center text-gray-500 text-sm border-t border-gray-800 pt-8">
                <p>&copy; 2026 InfinityFlow. Sua comunicação, sem limites.</p>
            </div>
        </div>
    </footer>

    <a href="https://wpp.infinityflowapp.com/5534988780557?text=Olá! Vim pelo site e gostaria de um orçamento para automação." 
       target="_blank" 
       class="whatsapp-float"
       aria-label="Entre em contato pelo WhatsApp">
        <i data-lucide="message-circle" class="w-7 h-7"></i>
    </a>

    <script>
        lucide.createIcons();
        AOS.init({ duration: 800, once: true, offset: 100 });
        
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuBtn.addEventListener('click', () => { mobileMenu.classList.toggle('hidden'); });
        
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('i[data-lucide="chevron-down"]');
            content.classList.toggle('active');
            icon.style.transform = content.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        const form = document.getElementById('infinityForm');
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        if (form && btn && btnText) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btn.disabled = true;
                btnText.innerText = "Processando informações...";
                
                const formData = new FormData(form);
                try {
                    const response = await fetch('index.php', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('🚀 Dados enviados! O Flow já está processando sua solicitação. Entraremos em contato em breve.');
                        form.reset();
                    } else {
                        alert('❌ Erro ao entrar em contato com o Flow. Por favor, tente pelo botão do WhatsApp.');
                    }
                } catch (error) {
                    alert('⚠️ Erro de conexão. Verifique se sua internet está ativa ou use o suporte via WhatsApp.');
                } finally {
                    btn.disabled = false;
                    btnText.innerText = "Enviar para Análise do Flow";
                }
            });
        }
    </script>
</body>
</html>

