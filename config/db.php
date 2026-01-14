<?php
/**
 * InfinityFlow - Database Configuration
 * =====================================
 * Arquivo de configuração de conexão com banco de dados MySQL usando PDO
 * 
 * @author InfinityFlow Team
 * @version 1.0.0
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'infinityflow_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Opções do PDO para melhor performance e segurança
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
];

try {
    // Criando a conexão PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Conexão estabelecida com sucesso
    // echo "Conexão estabelecida com sucesso!"; // Descomente para debug
    
} catch (PDOException $e) {
    // Tratamento de erro
    error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
    die("Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.");
}

/**
 * Função auxiliar para executar queries preparadas
 * 
 * @param PDO $pdo Objeto PDO
 * @param string $sql Query SQL
 * @param array $params Parâmetros para bind
 * @return PDOStatement
 */
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Erro na execução da query: " . $e->getMessage());
        throw $e;
    }
}

?>
