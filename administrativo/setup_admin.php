<?php
/**
 * InfinityFlow - Setup Administrativo
 * =====================================
 * Script de inicialização do sistema administrativo
 * 
 * Este script:
 * - Cria a tabela de usuários (se não existir)
 * - Cria o usuário admin padrão (se não existir)
 * - Auto-deleta após execução bem-sucedida
 * 
 * ⚠️ ATENÇÃO: Execute apenas UMA vez após deploy em produção!
 * 
 * @author InfinityFlow Team
 * @version 1.0.0
 */

// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers
header('Content-Type: text/html; charset=utf-8');

// Variáveis de configuração
$setupCompleted = false;
$messages = [];
$errors = [];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfinityFlow - Setup Administrativo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
            color: #fff;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 700px;
            width: 100%;
            background: rgba(30, 30, 30, 0.95);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 15px 50px rgba(220, 38, 38, 0.4);
            border: 1px solid rgba(220, 38, 38, 0.3);
        }
        h1 {
            font-size: 36px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #999;
            margin-bottom: 40px;
            font-size: 15px;
        }
        .step {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #333;
        }
        .step.success {
            border-left-color: #4ade80;
            background: rgba(34, 197, 94, 0.1);
        }
        .step.error {
            border-left-color: #f87171;
            background: rgba(239, 68, 68, 0.1);
        }
        .step.info {
            border-left-color: #60a5fa;
            background: rgba(59, 130, 246, 0.1);
        }
        .step-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .step-content {
            font-size: 14px;
            color: #ccc;
            line-height: 1.6;
        }
        .icon {
            font-size: 20px;
        }
        .final-message {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.2), rgba(239, 68, 68, 0.1));
            border: 2px solid #dc2626;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            text-align: center;
        }
        .final-message h2 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #4ade80;
        }
        .final-message p {
            font-size: 16px;
            line-height: 1.8;
            color: #e0e0e0;
        }
        .credentials {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .credentials div {
            margin: 10px 0;
            font-size: 15px;
        }
        .credentials strong {
            color: #dc2626;
        }
        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 15px 35px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(220, 38, 38, 0.6);
        }
        .warning {
            background: rgba(251, 146, 60, 0.2);
            border: 1px solid rgba(251, 146, 60, 0.5);
            color: #fb923c;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Setup Administrativo</h1>
        <p class="subtitle">Inicializando o sistema InfinityFlow</p>

        <?php
        // ==================== PASSO 1: CONECTAR AO BANCO ====================
        try {
            // Ajustado para caminho relativo da pasta administrativo
            require_once __DIR__ . '/../config/db.php';
            $messages[] = [
                'type' => 'success',
                'title' => '✅ Conexão Estabelecida',
                'content' => 'Conectado ao banco de dados MariaDB com sucesso.'
            ];
        } catch (Exception $e) {
            $errors[] = [
                'type' => 'error',
                'title' => '❌ Erro de Conexão',
                'content' => 'Não foi possível conectar ao banco de dados: ' . $e->getMessage()
            ];
        }

        // ==================== PASSO 2: CRIAR TABELA DE USUÁRIOS ====================
        if (empty($errors)) {
            try {
                $createTableSQL = "
                    CREATE TABLE IF NOT EXISTS usuarios_admin (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) NOT NULL UNIQUE,
                        password_hash VARCHAR(255) NOT NULL,
                        email VARCHAR(255),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        last_login TIMESTAMP NULL,
                        is_active TINYINT(1) DEFAULT 1,
                        INDEX idx_username (username)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ";
                
                $pdo->exec($createTableSQL);
                
                $messages[] = [
                    'type' => 'success',
                    'title' => '✅ Tabela Configurada',
                    'content' => 'Tabela "usuarios_admin" criada ou já existente.'
                ];
                
            } catch (PDOException $e) {
                $errors[] = [
                    'type' => 'error',
                    'title' => '❌ Erro ao Criar Tabela',
                    'content' => 'Falha ao criar tabela: ' . $e->getMessage()
                ];
            }
        }

        // ==================== PASSO 3: VERIFICAR SE ADMIN JÁ EXISTE ====================
        if (empty($errors)) {
            try {
                $checkSQL = "SELECT COUNT(*) as total FROM usuarios_admin WHERE username = :username";
                $stmt = $pdo->prepare($checkSQL);
                $stmt->execute(['username' => 'admin']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $adminExists = ($result['total'] > 0);
                
                if ($adminExists) {
                    $messages[] = [
                        'type' => 'info',
                        'title' => 'ℹ️ Usuário Já Existe',
                        'content' => 'O usuário "admin" já está cadastrado no sistema. Nenhuma ação necessária.'
                    ];
                } else {
                    // ==================== PASSO 4: CRIAR USUÁRIO ADMIN ====================
                    $username = 'admin';
                    $password = 'InfinityFlow@2026';
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $insertSQL = "
                        INSERT INTO usuarios_admin (username, password_hash, email, created_at) 
                        VALUES (:username, :password_hash, :email, NOW())
                    ";
                    
                    $stmt = $pdo->prepare($insertSQL);
                    $stmt->execute([
                        'username' => $username,
                        'password_hash' => $passwordHash,
                        'email' => 'admin@infinityflow.com'
                    ]);
                    
                    $messages[] = [
                        'type' => 'success',
                        'title' => '✅ Usuário Criado',
                        'content' => 'Usuário administrador "admin" criado com sucesso!'
                    ];
                    
                    $setupCompleted = true;
                }
                
            } catch (PDOException $e) {
                $errors[] = [
                    'type' => 'error',
                    'title' => '❌ Erro ao Criar Usuário',
                    'content' => 'Falha ao criar usuário admin: ' . $e->getMessage()
                ];
            }
        }

        // ==================== EXIBIR MENSAGENS ====================
        foreach ($messages as $msg) {
            echo '<div class="step ' . $msg['type'] . '">';
            echo '<div class="step-title"><span class="icon">' . explode(' ', $msg['title'])[0] . '</span>' . $msg['title'] . '</div>';
            echo '<div class="step-content">' . $msg['content'] . '</div>';
            echo '</div>';
        }

        foreach ($errors as $err) {
            echo '<div class="step ' . $err['type'] . '">';
            echo '<div class="step-title"><span class="icon">' . explode(' ', $err['title'])[0] . '</span>' . $err['title'] . '</div>';
            echo '<div class="step-content">' . $err['content'] . '</div>';
            echo '</div>';
        }

        // ==================== MENSAGEM FINAL ====================
        if ($setupCompleted) {
            echo '<div class="final-message">';
            echo '<h2>🎉 Sistema Configurado com Sucesso!</h2>';
            echo '<p>O InfinityFlow está pronto para uso. Utilize as credenciais abaixo para acessar a área administrativa:</p>';
            
            echo '<div class="credentials">';
            echo '<div><strong>Usuário:</strong> admin</div>';
            echo '<div><strong>Senha:</strong> InfinityFlow@2026</div>';
            echo '<div><strong>URL de Acesso:</strong> /administrativo/index.php</div>';
            echo '</div>';
            
            echo '<p>Por segurança, este arquivo será deletado automaticamente.</p>';
            echo '<a href="../index.php" class="btn">Ir para o Site</a>';
            echo '<a href="index.php" class="btn">Acessar Admin</a>';
            echo '</div>';
            
            echo '<div class="warning">';
            echo '<strong>⚠️ Importante:</strong> Altere a senha padrão após o primeiro login!';
            echo '</div>';
            
            // ==================== PASSO 5: AUTO-DELETE ====================
            try {
                // Delay de 1 segundo para garantir que a página foi renderizada
                sleep(1);
                
                // Auto-deletar este arquivo
                if (file_exists(__FILE__)) {
                    unlink(__FILE__);
                    error_log('[InfinityFlow Setup] Arquivo administrativo/setup_admin.php deletado com sucesso após configuração.');
                }
            } catch (Exception $e) {
                error_log('[InfinityFlow Setup] Erro ao deletar setup_admin.php: ' . $e->getMessage());
                echo '<div class="step error">';
                echo '<div class="step-title">⚠️ Aviso</div>';
                echo '<div class="step-content">Não foi possível deletar automaticamente o arquivo setup_admin.php. Por favor, delete-o manualmente por segurança.</div>';
                echo '</div>';
            }
            
        } elseif (!empty($errors)) {
            echo '<div class="final-message">';
            echo '<h2 style="color: #f87171;">❌ Setup Incompleto</h2>';
            echo '<p>Ocorreram erros durante a configuração. Verifique os logs acima e tente novamente.</p>';
            echo '</div>';
            
        } else {
            echo '<div class="final-message">';
            echo '<h2 style="color: #60a5fa;">ℹ️ Sistema Já Configurado</h2>';
            echo '<p>O sistema administrativo já foi configurado anteriormente. Nenhuma ação foi realizada.</p>';
            echo '<a href="index.php" class="btn">Acessar Área Admin</a>';
            echo '</div>';
            
            echo '<div class="warning">';
            echo '<strong>⚠️ Segurança:</strong> Você pode deletar este arquivo (setup_admin.php) manualmente, pois ele não é mais necessário.';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
