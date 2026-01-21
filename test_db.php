<?php
/**
 * InfinityFlow - Teste de Diagnóstico de Conexão ao Banco de Dados
 * ==================================================================
 * Script para identificar problemas de conexão com MariaDB no Easypanel
 * 
 * ⚠️ IMPORTANTE: DELETE este arquivo após diagnosticar o problema!
 * 
 * @author InfinityFlow Team
 * @version 1.0.0
 */

// Habilitar exibição completa de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfinityFlow - Diagnóstico de Conexão DB</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #0a0a0a;
            color: #00ff00;
            padding: 30px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #1a1a1a;
            border: 2px solid #00ff00;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
        }
        h1 {
            color: #00ff00;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        .section {
            background: #0f0f0f;
            border-left: 4px solid #00ff00;
            padding: 15px;
            margin-bottom: 20px;
        }
        .section h2 {
            color: #00ffff;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .success {
            color: #00ff00;
            background: rgba(0, 255, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            color: #ff0000;
            background: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .warning {
            color: #ffaa00;
            background: rgba(255, 170, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            color: #00aaff;
        }
        code {
            background: #000;
            padding: 2px 6px;
            border-radius: 3px;
            color: #ff00ff;
        }
        pre {
            background: #000;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            color: #fff;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #333;
        }
        th {
            color: #00ffff;
            background: #0f0f0f;
        }
        td {
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 DIAGNÓSTICO DE CONEXÃO - MariaDB</h1>
        
        <?php
        // ==================== CREDENCIAIS ====================
        echo '<div class="section">';
        echo '<h2>📋 CREDENCIAIS CONFIGURADAS</h2>';
        echo '<table>';
        echo '<tr><th>Parâmetro</th><th>Valor</th></tr>';
        echo '<tr><td>Host</td><td>mariadb</td></tr>';
        echo '<tr><td>Porta</td><td>3306</td></tr>';
        echo '<tr><td>Database</td><td>infinityflowapp</td></tr>';
        echo '<tr><td>Usuário</td><td>mariadb</td></tr>';
        echo '<tr><td>Senha</td><td>Infinity_@Flow</td></tr>';
        echo '<tr><td>Charset</td><td>utf8mb4</td></tr>';
        echo '</table>';
        echo '</div>';
        
        // ==================== TESTE 1: VERIFICAR EXTENSÃO PDO ====================
        echo '<div class="section">';
        echo '<h2>🔧 TESTE 1: Extensão PDO</h2>';
        
        if (extension_loaded('pdo')) {
            echo '<div class="success">✅ Extensão PDO está instalada</div>';
            
            if (extension_loaded('pdo_mysql')) {
                echo '<div class="success">✅ Driver PDO MySQL está disponível</div>';
            } else {
                echo '<div class="error">❌ Driver PDO MySQL NÃO está instalado!</div>';
                echo '<div class="warning">Solução: Instale php-mysql ou php-pdo-mysql</div>';
            }
        } else {
            echo '<div class="error">❌ Extensão PDO NÃO está instalada!</div>';
            echo '<div class="warning">Solução: Habilite a extensão PDO no php.ini</div>';
        }
        echo '</div>';
        
        // ==================== TESTE 2: RESOLUÇÃO DE HOST ====================
        echo '<div class="section">';
        echo '<h2>🌐 TESTE 2: Resolução de Nome do Host</h2>';
        
        $host = 'mariadb';
        $ip = gethostbyname($host);
        
        if ($ip !== $host) {
            echo '<div class="success">✅ Host "' . $host . '" resolvido para IP: ' . $ip . '</div>';
        } else {
            echo '<div class="error">❌ Não foi possível resolver o host "' . $host . '"</div>';
            echo '<div class="warning">Possíveis causas:</div>';
            echo '<ul>';
            echo '<li>Container MariaDB não está rodando</li>';
            echo '<li>Nome do serviço incorreto (verifique docker-compose.yml)</li>';
            echo '<li>Containers não estão na mesma rede Docker</li>';
            echo '</ul>';
        }
        echo '</div>';
        
        // ==================== TESTE 3: CONEXÃO PDO ====================
        echo '<div class="section">';
        echo '<h2>🔌 TESTE 3: Conexão PDO com MariaDB</h2>';
        
        $db_host = 'mariadb';
        $db_port = '3306';
        $db_name = 'infinityflowapp';
        $db_user = 'mariadb';
        $db_pass = 'Infinity_@Flow';
        $db_charset = 'utf8mb4';
        
        try {
            // Construir DSN
            $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset={$db_charset}";
            
            echo '<div class="info">🔄 Tentando conectar com DSN:</div>';
            echo '<pre>' . htmlspecialchars($dsn) . '</pre>';
            
            // Opções PDO
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 5
            ];
            
            // Tentar conexão
            $pdo = new PDO($dsn, $db_user, $db_pass, $options);
            
            echo '<div class="success">✅ CONEXÃO ESTABELECIDA COM SUCESSO!</div>';
            
            // ==================== TESTE 4: INFORMAÇÕES DO SERVIDOR ====================
            echo '</div>';
            echo '<div class="section">';
            echo '<h2>📊 TESTE 4: Informações do Servidor MariaDB</h2>';
            
            $version = $pdo->query("SELECT VERSION() as version")->fetch();
            $charset = $pdo->query("SHOW VARIABLES LIKE 'character_set_database'")->fetch();
            $collation = $pdo->query("SHOW VARIABLES LIKE 'collation_database'")->fetch();
            $user = $pdo->query("SELECT CURRENT_USER() as user")->fetch();
            
            echo '<table>';
            echo '<tr><th>Informação</th><th>Valor</th></tr>';
            echo '<tr><td>Versão MariaDB/MySQL</td><td>' . $version['version'] . '</td></tr>';
            echo '<tr><td>Usuário Conectado</td><td>' . $user['user'] . '</td></tr>';
            echo '<tr><td>Charset do Banco</td><td>' . $charset['Value'] . '</td></tr>';
            echo '<tr><td>Collation do Banco</td><td>' . $collation['Value'] . '</td></tr>';
            echo '</table>';
            
            // ==================== TESTE 5: LISTAR TABELAS ====================
            echo '</div>';
            echo '<div class="section">';
            echo '<h2>📋 TESTE 5: Tabelas Existentes</h2>';
            
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (count($tables) > 0) {
                echo '<div class="success">✅ Encontradas ' . count($tables) . ' tabela(s):</div>';
                echo '<ul>';
                foreach ($tables as $table) {
                    echo '<li>' . htmlspecialchars($table) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div class="warning">⚠️ Nenhuma tabela encontrada no banco de dados (banco vazio)</div>';
            }
            
            // ==================== TESTE 6: QUERY DE TESTE ====================
            echo '</div>';
            echo '<div class="section">';
            echo '<h2>⚙️ TESTE 6: Execução de Query</h2>';
            
            $result = $pdo->query("SELECT 1 + 1 AS resultado")->fetch();
            echo '<div class="success">✅ Query executada: SELECT 1 + 1 = ' . $result['resultado'] . '</div>';
            
        } catch (PDOException $e) {
            // ==================== ERRO DE CONEXÃO ====================
            echo '<div class="error">❌ ERRO DE CONEXÃO!</div>';
            
            echo '<h3 style="color: #ff6666; margin: 20px 0;">Detalhes do Erro:</h3>';
            echo '<pre>';
            echo 'Mensagem: ' . htmlspecialchars($e->getMessage()) . "\n";
            echo 'Código: ' . $e->getCode() . "\n";
            echo 'Arquivo: ' . $e->getFile() . "\n";
            echo 'Linha: ' . $e->getLine();
            echo '</pre>';
            
            echo '<h3 style="color: #ffaa00; margin: 20px 0;">Possíveis Soluções:</h3>';
            
            $errorMsg = $e->getMessage();
            
            if (strpos($errorMsg, 'SQLSTATE[HY000] [2002]') !== false) {
                if (strpos($errorMsg, 'Connection refused') !== false) {
                    echo '<div class="warning">';
                    echo '<strong>Erro: Connection refused</strong><br>';
                    echo '• MariaDB não está aceitando conexões na porta 3306<br>';
                    echo '• Verifique se o container MariaDB está rodando: <code>docker ps</code><br>';
                    echo '• Verifique se a porta está exposta corretamente<br>';
                    echo '</div>';
                } elseif (strpos($errorMsg, 'No such file or directory') !== false || strpos($errorMsg, 'Unknown host') !== false) {
                    echo '<div class="warning">';
                    echo '<strong>Erro: Host não encontrado</strong><br>';
                    echo '• O nome "mariadb" não pode ser resolvido<br>';
                    echo '• Verifique se os containers estão na mesma rede Docker<br>';
                    echo '• Verifique o nome do serviço no docker-compose.yml<br>';
                    echo '• Tente usar o IP do container ao invés do nome<br>';
                    echo '</div>';
                }
            } elseif (strpos($errorMsg, 'SQLSTATE[HY000] [1045]') !== false) {
                echo '<div class="warning">';
                echo '<strong>Erro: Acesso negado (usuário/senha incorretos)</strong><br>';
                echo '• Verifique o usuário: <code>mariadb</code><br>';
                echo '• Verifique a senha: <code>Infinity_@Flow</code><br>';
                echo '• Verifique as permissões do usuário no banco<br>';
                echo '</div>';
            } elseif (strpos($errorMsg, 'SQLSTATE[HY000] [1049]') !== false) {
                echo '<div class="warning">';
                echo '<strong>Erro: Banco de dados não existe</strong><br>';
                echo '• O banco "infinityflowapp" não foi criado<br>';
                echo '• Crie o banco via phpMyAdmin ou CLI:<br>';
                echo '<pre>CREATE DATABASE infinityflowapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</pre>';
                echo '</div>';
            } else {
                echo '<div class="warning">';
                echo '• Verifique os logs do container MariaDB<br>';
                echo '• Verifique as configurações de rede do Docker<br>';
                echo '• Verifique se há firewall bloqueando a porta 3306<br>';
                echo '</div>';
            }
        }
        echo '</div>';
        
        // ==================== COMANDOS ÚTEIS ====================
        echo '<div class="section">';
        echo '<h2>💡 COMANDOS ÚTEIS PARA DEBUG</h2>';
        echo '<pre>';
        echo '# Listar containers rodando
docker ps

# Ver logs do MariaDB
docker logs mariadb

# Verificar redes Docker
docker network ls

# Inspecionar rede do container
docker network inspect &lt;network_name&gt;

# Conectar ao MariaDB via CLI
docker exec -it mariadb mysql -u mariadb -p

# Testar conexão de dentro do container PHP
docker exec -it &lt;php_container&gt; ping mariadb
';
        echo '</pre>';
        echo '</div>';
        
        // ==================== AVISO DE SEGURANÇA ====================
        echo '<div class="section" style="border-left-color: #ff0000;">';
        echo '<h2 style="color: #ff0000;">⚠️ AVISO DE SEGURANÇA</h2>';
        echo '<div class="error">';
        echo '<strong>ESTE ARQUIVO EXPÕE INFORMAÇÕES SENSÍVEIS!</strong><br><br>';
        echo 'Após diagnosticar o problema, DELETE este arquivo imediatamente:<br>';
        echo '<code>rm test_db.php</code>';
        echo '</div>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
