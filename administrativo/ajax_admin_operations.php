<?php
/**
 * InfinityFlow - AJAX Admin Operations
 * =====================================
 * Processa operações administrativas via AJAX
 * 
 * Ações disponíveis:
 * - update_key: Atualizar chave de cadastro (somente CEO)
 * - toggle_status: Ativar/desativar usuário
 * - delete_user: Excluir usuário (com travas de segurança)
 * 
 * @author InfinityFlow Team
 * @version 1.0.0
 */

require_once 'auth_middleware.php';
require_once __DIR__ . '/../config/db.php';

// Headers para JSON
header('Content-Type: application/json');

// Require login
require_login();

// Capturar dados JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    switch ($action) {
        // ===== ATUALIZAR CHAVE DE CADASTRO (SOMENTE CEO) =====
        case 'update_key':
            // Verificar se é CEO
            if (!is_ceo()) {
                throw new Exception('Acesso Negado: Apenas usuários CEO podem alterar a chave de cadastro.');
            }
            
            $nova_chave = trim($input['nova_chave'] ?? '');
            
            // Validações
            if (empty($nova_chave)) {
                throw new Exception('A nova chave não pode estar vazia.');
            }
            
            if (strlen($nova_chave) < 6) {
                throw new Exception('A chave deve ter no mínimo 6 caracteres.');
            }
            
            // Atualizar no banco
            $stmt = $pdo->prepare("
                UPDATE configuracoes 
                SET valor = :nova_chave, data_atualizacao = NOW() 
                WHERE chave_nome = 'chave_cadastro'
            ");
            $stmt->execute(['nova_chave' => $nova_chave]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Chave de cadastro atualizada com sucesso!'
            ]);
            break;
        
        // ===== ALTERNAR STATUS DO USUÁRIO =====
        case 'toggle_status':
            $user_id = intval($input['user_id'] ?? 0);
            $new_status = intval($input['status'] ?? 0);
            
            if ($user_id <= 0) {
                throw new Exception('ID de usuário inválido.');
            }
            
            // Buscar informações do usuário alvo
            $stmt = $pdo->prepare("SELECT nivel FROM usuarios_admin WHERE id = ?");
            $stmt->execute([$user_id]);
            $target_user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$target_user) {
                throw new Exception('Usuário não encontrado.');
            }
            
            // Verificar permissões
            if (!can_manage_user($user_id, $target_user['nivel'])) {
                if ($user_id == $_SESSION['user_id']) {
                    throw new Exception('Operação Negada: Você não pode alterar o status da sua própria conta.');
                } else {
                    throw new Exception('Acesso Negado: Você não tem permissão para gerenciar este usuário.');
                }
            }
            
            // Atualizar status
            $stmt = $pdo->prepare("UPDATE usuarios_admin SET is_active = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Status atualizado com sucesso!'
            ]);
            break;
        
        // ===== EXCLUIR USUÁRIO (COM TRAVAS DE SEGURANÇA) =====
        case 'delete_user':
            $user_id = intval($input['user_id'] ?? 0);
            $user_nivel = $input['user_nivel'] ?? '';
            
            if ($user_id <= 0) {
                throw new Exception('ID de usuário inválido.');
            }
            
            // TRAVA 1: Bloqueio de Autodeleção
            if ($user_id == $_SESSION['user_id']) {
                throw new Exception('Operação Negada: Você não pode excluir sua própria conta.');
            }
            
            // Buscar informações do usuário alvo
            $stmt = $pdo->prepare("SELECT nivel FROM usuarios_admin WHERE id = ?");
            $stmt->execute([$user_id]);
            $target_user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$target_user) {
                throw new Exception('Usuário não encontrado.');
            }
            
            $target_nivel = $target_user['nivel'];
            
            // TRAVA 2: Proteção do Último CEO
            if ($target_nivel === 'CEO') {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios_admin WHERE nivel = 'CEO'");
                $stmt->execute();
                $ceo_count = $stmt->fetchColumn();
                
                if ($ceo_count <= 1) {
                    throw new Exception('Operação Negada: Não é possível excluir o único CEO do sistema.');
                }
            }
            
            // TRAVA 3: Admin não pode deletar CEO
            if (!is_ceo() && $target_nivel === 'CEO') {
                throw new Exception('Acesso Negado: Administradores não podem excluir usuários CEO.');
            }
            
            // Verificar permissões gerais
            if (!can_manage_user($user_id, $target_nivel)) {
                throw new Exception('Acesso Negado: Você não tem permissão para gerenciar este usuário.');
            }
            
            // Executar exclusão
            $stmt = $pdo->prepare("DELETE FROM usuarios_admin WHERE id = ?");
            $stmt->execute([$user_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Usuário excluído com sucesso!'
            ]);
            break;
        
        default:
            throw new Exception('Ação inválida.');
    }
    
} catch (PDOException $e) {
    error_log('[InfinityFlow AJAX] Erro PDO: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar operação. Tente novamente.'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
