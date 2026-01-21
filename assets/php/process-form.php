<?php
/**
 * InfinityFlow - Form Processing Logic
 * =====================================
 * Handles form submissions, n8n webhook integration, and WWW redirects
 */

// Redirecionamento de non-www para www
if ($_SERVER['HTTP_HOST'] === 'infinityflowapp.com') {
    header("Location: https://www.infinityflowapp.com" . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}

// Form Processing - n8n Webhook Integration
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

    // Resposta AJAX
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
