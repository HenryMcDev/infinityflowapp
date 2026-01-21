<?php
/**
 * InfinityFlow - Teste de Conexão com Banco de Dados
 * ====================================================
 * Script de teste para validar a conexão PDO com MariaDB no Easypanel
 * 
 * ⚠️ IMPORTANTE: Remover este arquivo após testar em produção!
 */

// Habilitar exibição de erros (apenas para teste)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers para resposta JSON
header('Content-Type: application/json; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfinityFlow - Teste de Conexão DB</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
            color: #fff;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(30, 30, 30, 0.9);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(220, 38, 38, 0.3);
            border: 1px solid rgba(220, 38, 38, 0.3);
        }
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .subtitle {
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .test-section {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        .test-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .test-result {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.5);
            color: #4ade80;
        }
        .error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #f87171;
        }
        .info {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.5);
            color: #60a5fa;
        }
        .warning {
            background: rgba(251, 146, 60, 0.2);
            border: 1px solid rgba(251, 146, 60, 0.5);
            color: #fb923c;
            margin-top: 30px;
            padding: 15px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th {
            color: #dc2626;
            font-weight: 600;
        }
        .icon { margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ InfinityFlow - Teste de Conexão</h1>
        <p class="subtitle">Validação da configuração de banco de dados MariaDB</p>

        <?php
        // ==================== TESTE 1: INCLUIR ARQUIVO DE CONFIGURAÇÃO ====================
        echo '<div class="test-section">';
        echo '<div class="test-title">📁 Teste 1: Carregamento do Arquivo de Configuração</div>';
        
        try {
            require_once __DIR__ . '/config/db.php';
            echo '<div class="test-result success"><span class="icon">✅</span>Arquivo config/db.php carregado com sucesso</div>';
        } catch (Exception $e) {
            echo '<div class="test-result error"><span class="icon">❌</span>Erro ao carregar config/db.php: ' . $e->getMessage() . '</div>';
            echo '</div></div></body></html>';
            exit;
        }
        echo '</div>';

        // ==================== TESTE 2: VERIFICAR CONEXÃO ====================
        echo '<div class="test-section">';
        echo '<div class="test-title">🔌 Teste 2: Conexão com o Banco de Dados</div>';
        
        if (isset($pdo) && $pdo instanceof PDO) {
            echo '<div class="test-result success"><span class="icon">✅</span>Conexão PDO estabelecida com sucesso</div>';
            
            // ==================== TESTE 3: INFORMAÇÕES DO SERVIDOR ====================
            echo '</div>';
            echo '<div class="test-section">';
            echo '<div class="test-title">📊 Teste 3: Informações do Servidor MariaDB</div>';
            
            try {
                $version = $pdo->query("SELECT VERSION() as version")->fetch();
                $charset = $pdo->query("SHOW VARIABLES LIKE 'character_set_database'")->fetch();
                $collation = $pdo->query("SHOW VARIABLES LIKE 'collation_database'")->fetch();
                
                echo '<table>';
                echo '<tr><th>Parâmetro</th><th>Valor</th></tr>';
                echo '<tr><td>Versão MariaDB</td><td>' . $version['version'] . '</td></tr>';
                echo '<tr><td>Host</td><td>' . DB_HOST . '</td></tr>';
                echo '<tr><td>Porta</td><td>' . DB_PORT . '</td></tr>';
                echo '<tr><td>Banco de Dados</td><td>' . DB_NAME . '</td></tr>';
                echo '<tr><td>Usuário</td><td>' . DB_USER . '</td></tr>';
                echo '<tr><td>Charset Configurado</td><td>' . DB_CHARSET . '</td></tr>';
                echo '<tr><td>Charset do Banco</td><td>' . $charset['Value'] . '</td></tr>';
                echo '<tr><td>Collation do Banco</td><td>' . $collation['Value'] . '</td></tr>';
                echo '</table>';
                
            } catch (PDOException $e) {
                echo '<div class="test-result error"><span class="icon">❌</span>Erro ao obter informações: ' . $e->getMessage() . '</div>';
            }
            
            // ==================== TESTE 4: LISTAR TABELAS ====================
            echo '</div>';
            echo '<div class="test-section">';
            echo '<div class="test-title">📋 Teste 4: Tabelas Existentes no Banco</div>';
            
            try {
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (count($tables) > 0) {
                    echo '<div class="test-result success"><span class="icon">✅</span>Encontradas ' . count($tables) . ' tabela(s):</div>';
                    echo '<ul style="margin-left: 40px; margin-top: 10px;">';
                    foreach ($tables as $table) {
                        echo '<li style="margin: 5px 0;">' . $table . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<div class="test-result info"><span class="icon">ℹ️</span>Nenhuma tabela encontrada (banco vazio)</div>';
                }
                
            } catch (PDOException $e) {
                echo '<div class="test-result error"><span class="icon">❌</span>Erro ao listar tabelas: ' . $e->getMessage() . '</div>';
            }
            
            // ==================== TESTE 5: TESTAR QUERY ====================
            echo '</div>';
            echo '<div class="test-section">';
            echo '<div class="test-title">⚙️ Teste 5: Execução de Query Simples</div>';
            
            try {
                $stmt = $pdo->query("SELECT 1 + 1 AS resultado");
                $result = $stmt->fetch();
                
                if ($result['resultado'] == 2) {
                    echo '<div class="test-result success"><span class="icon">✅</span>Query executada com sucesso: SELECT 1 + 1 = ' . $result['resultado'] . '</div>';
                }
                
            } catch (PDOException $e) {
                echo '<div class="test-result error"><span class="icon">❌</span>Erro ao executar query: ' . $e->getMessage() . '</div>';
            }
            
            // ==================== TESTE 6: VERIFICAR FUNÇÕES AUXILIARES ====================
            echo '</div>';
            echo '<div class="test-section">';
            echo '<div class="test-title">🛠️ Teste 6: Funções Auxiliares</div>';
            
            $functions = ['executeQuery', 'getLastInsertId', 'beginTransaction', 'commitTransaction', 'rollbackTransaction', 'isConnected'];
            $found = 0;
            
            foreach ($functions as $func) {
                if (function_exists($func)) {
                    $found++;
                }
            }
            
            if ($found == count($functions)) {
                echo '<div class="test-result success"><span class="icon">✅</span>Todas as ' . count($functions) . ' funções auxiliares estão disponíveis</div>';
            } else {
                echo '<div class="test-result error"><span class="icon">❌</span>Apenas ' . $found . ' de ' . count($functions) . ' funções encontradas</div>';
            }
            
            // ==================== TESTE 7: TESTAR FUNÇÃO isConnected() ====================
            echo '</div>';
            echo '<div class="test-section">';
            echo '<div class="test-title">🩺 Teste 7: Verificação de Saúde da Conexão</div>';
            
            if (function_exists('isConnected')) {
                if (isConnected($pdo)) {
                    echo '<div class="test-result success"><span class="icon">✅</span>Conexão está ativa e responsiva (isConnected = true)</div>';
                } else {
                    echo '<div class="test-result error"><span class="icon">❌</span>Conexão não está respondendo (isConnected = false)</div>';
                }
            }
            
        } else {
            echo '<div class="test-result error"><span class="icon">❌</span>Variável $pdo não está definida ou não é uma instância PDO</div>';
        }
        
        echo '</div>';
        
        // ==================== RESUMO FINAL ====================
        echo '<div class="test-section" style="border-left-color: #4ade80;">';
        echo '<div class="test-title" style="color: #4ade80;">🎯 Resumo Final</div>';
        echo '<div class="test-result success">';
        echo '<span class="icon">✅</span><strong>Conexão com MariaDB estabelecida com sucesso!</strong><br><br>';
        echo 'Todas as configurações estão corretas e o banco de dados está pronto para uso em produção.';
        echo '</div>';
        echo '</div>';
        
        // ==================== AVISO DE SEGURANÇA ====================
        echo '<div class="warning">';
        echo '<strong>⚠️ IMPORTANTE - SEGURANÇA</strong><br><br>';
        echo 'Este arquivo de teste expõe informações sensíveis sobre o banco de dados.<br>';
        echo '<strong>REMOVA ou RENOMEIE este arquivo (test_db_connection.php) imediatamente após validar a conexão!</strong>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
