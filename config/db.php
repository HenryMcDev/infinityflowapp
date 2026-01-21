<?php
/**
 * InfinityFlow - Database Configuration (PRODUCTION - Easypanel)
 * ================================================================
 * Arquivo de configuração de conexão com banco de dados MariaDB usando PDO
 * 
 * Ambiente: PRODUÇÃO - Easypanel
 * Driver: MySQL (compatível com MariaDB)
 * Charset: UTF-8 (utf8mb4) para suporte completo a emojis e caracteres especiais
 * 
 * @author InfinityFlow Team
 * @version 2.0.0 - Production
 */

// ==================== CONFIGURAÇÕES DE PRODUÇÃO ====================
define('DB_HOST', 'mariadb');
define('DB_NAME', 'infinityflowapp');
define('DB_USER', 'mariadb');
define('DB_PASS', 'Infinity_@Flow');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

// ==================== OPÇÕES DE SEGURANÇA PDO ====================
$options = [
    // Modo de erro: Lança exceções em caso de erro
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Modo de fetch padrão: Array associativo
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Desabilita emulação de prepared statements (mais seguro)
    PDO::ATTR_EMULATE_PREPARES   => false,
    
    // Define charset na conexão
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
    
    // Timeout de conexão (5 segundos)
    PDO::ATTR_TIMEOUT            => 5,
    
    // Persistência de conexão (desabilitada em produção para evitar problemas)
    PDO::ATTR_PERSISTENT         => false
];

// ==================== ESTABELECENDO CONEXÃO ====================
try {
    // Construindo DSN (Data Source Name)
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=%s",
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );
    
    // Criando conexão PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Conexão estabelecida com sucesso
    // Log opcional para monitoramento (descomente se necessário)
    // error_log("[InfinityFlow] Conexão com banco de dados estabelecida - " . date('Y-m-d H:i:s'));
    
} catch (PDOException $e) {
    // ==================== TRATAMENTO DE ERROS ====================
    
    // LOGGING: Registra erro real com detalhes técnicos
    error_log(sprintf(
        "[InfinityFlow DB ERROR] %s | File: %s | Line: %s | Trace: %s",
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    ));
    
    // PRODUÇÃO: Define status HTTP 500 ANTES de qualquer output
    if (!headers_sent()) {
        http_response_code(500);
    }
    
    // Exibe mensagem genérica para o usuário final
    // Não expõe detalhes técnicos por segurança
    die('Erro ao conectar com o servidor');
}

// ==================== FUNÇÕES AUXILIARES ====================

/**
 * Executa uma query preparada de forma segura
 * 
 * @param PDO $pdo Objeto PDO da conexão
 * @param string $sql Query SQL com placeholders
 * @param array $params Parâmetros para bind (proteção contra SQL Injection)
 * @return PDOStatement Statement executado
 * @throws PDOException Em caso de erro na execução
 */
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("[InfinityFlow Query ERROR] " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

/**
 * Retorna o último ID inserido
 * 
 * @param PDO $pdo Objeto PDO da conexão
 * @return string ID do último registro inserido
 */
function getLastInsertId($pdo) {
    return $pdo->lastInsertId();
}

/**
 * Inicia uma transação
 * 
 * @param PDO $pdo Objeto PDO da conexão
 * @return bool True em sucesso
 */
function beginTransaction($pdo) {
    return $pdo->beginTransaction();
}

/**
 * Confirma (commit) uma transação
 * 
 * @param PDO $pdo Objeto PDO da conexão
 * @return bool True em sucesso
 */
function commitTransaction($pdo) {
    return $pdo->commit();
}

/**
 * Desfaz (rollback) uma transação
 * 
 * @param PDO $pdo Objeto PDO da conexão
 * @return bool True em sucesso
 */
function rollbackTransaction($pdo) {
    return $pdo->rollBack();
}

/**
 * Verifica se a conexão está ativa
 * 
 * @param PDO $pdo Objeto PDO da conexão
 * @return bool True se conectado, false caso contrário
 */
function isConnected($pdo) {
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (PDOException $e) {
        error_log("[InfinityFlow] Conexão perdida: " . $e->getMessage());
        return false;
    }
}

// ==================== FIM DA CONFIGURAÇÃO ====================
// A variável $pdo está disponível globalmente para uso em todo o projeto
// Exemplo de uso: require_once 'config/db.php';

?>
