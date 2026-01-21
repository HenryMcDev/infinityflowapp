<?php
/**
 * InfinityFlow - Gestão de Administradores
 * =========================================
 * Interface para visualizar e gerenciar usuários administradores
 * 
 * Permissões:
 * - CEO: Acesso total + gerenciar chave de cadastro
 * - Admin: Visualização restrita + sem acesso a CEOs
 * 
 * @author InfinityFlow Team
 * @version 1.0.0
 */

require_once 'auth_middleware.php';
require_once __DIR__ . '/../config/db.php';

// Require login
require_login();

// Obter informações do usuário logado
$current_user = get_current_user();
$is_ceo_user = is_ceo();

// Buscar todos os administradores
try {
    $stmt = $pdo->prepare("
        SELECT 
            id,
            username,
            email,
            nivel,
            created_at,
            last_login,
            is_active
        FROM usuarios_admin 
        ORDER BY 
            CASE nivel WHEN 'CEO' THEN 1 WHEN 'Admin' THEN 2 END,
            created_at ASC
    ");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('[InfinityFlow Admin] Erro ao buscar usuários: ' . $e->getMessage());
    $usuarios = [];
}

// Buscar chave de cadastro atual (somente para CEO)
$chave_cadastro = '';
if ($is_ceo_user) {
    try {
        $stmt = $pdo->prepare("SELECT valor FROM configuracoes WHERE chave_nome = 'chave_cadastro' LIMIT 1");
        $stmt->execute();
        $chave_cadastro = $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log('[InfinityFlow Admin] Erro ao buscar chave: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfinityFlow - Gerenciar Administradores</title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .glass {
            background: rgba(18, 18, 18, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-black via-black to-red-950/20">
    
    <!-- Header -->
    <div class="border-b border-white/10 bg-black/40 backdrop-blur-xl">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="dashboard.php" class="text-white/60 hover:text-red-400 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-[#C71A1D] via-red-500 to-[#ff4444] bg-clip-text text-transparent">
                        Gerenciar Administradores
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-white/50 text-sm"><?= htmlspecialchars($current_user['username']) ?></span>
                    <span class="px-3 py-1 rounded-lg text-xs font-semibold <?= $is_ceo_user ? 'bg-red-600/20 text-red-400 border border-red-600/50' : 'bg-blue-600/20 text-blue-400 border border-blue-600/50' ?>">
                        <?= $is_ceo_user ? '🔴 CEO' : '🔵 Admin' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8 max-w-7xl">
        
        <!-- Seção de Configuração da Chave (Somente CEO) -->
        <?php if ($is_ceo_user): ?>
        <div class="glass rounded-2xl p-6 mb-8 border border-red-600/30">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-600/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="key" class="w-5 h-5 text-red-400"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Chave de Cadastro</h2>
                    <p class="text-white/50 text-sm">Apenas usuários CEO podem alterar esta chave</p>
                </div>
            </div>
            
            <form id="updateKeyForm" class="flex gap-3">
                <input 
                    type="password" 
                    id="nova_chave" 
                    name="nova_chave"
                    value="<?= htmlspecialchars($chave_cadastro) ?>"
                    placeholder="Digite a nova chave de acesso"
                    class="flex-1 px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/50 transition-all"
                    minlength="6"
                    required
                >
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 rounded-xl font-semibold text-white hover:shadow-[0_0_30px_rgba(220,38,38,0.5)] transition-all duration-300 flex items-center gap-2"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Atualizar Chave
                </button>
            </form>
            
            <div id="keyUpdateMessage" class="mt-4 hidden"></div>
        </div>
        <?php endif; ?>

        <!-- Lista de Administradores -->
        <div class="glass rounded-2xl p-6 border border-white/10">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#C71A1D]/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-red-400"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Administradores Cadastrados</h2>
                        <p class="text-white/50 text-sm"><?= count($usuarios) ?> usuário(s) no total</p>
                    </div>
                </div>
                <a 
                    href="cadastro_admin.php" 
                    class="px-5 py-2.5 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-xl font-semibold text-white shadow-lg hover:shadow-[0_0_30px_rgba(199,26,29,0.5)] transition-all duration-300 flex items-center gap-2"
                >
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Novo Administrador
                </a>
            </div>

            <!-- Tabela -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left py-3 px-4 text-white/70 font-medium text-sm">ID</th>
                            <th class="text-left py-3 px-4 text-white/70 font-medium text-sm">Usuário</th>
                            <th class="text-left py-3 px-4 text-white/70 font-medium text-sm">Email</th>
                            <th class="text-left py-3 px-4 text-white/70 font-medium text-sm">Nível</th>
                            <th class="text-left py-3 px-4 text-white/70 font-medium text-sm">Criado em</th>
                            <th class="text-left py-3 px-4 text-white/70 font-medium text-sm">Último Login</th>
                            <th class="text-left py-3 px-4 text-white/70 font-medium text-sm">Status</th>
                            <th class="text-center py-3 px-4 text-white/70 font-medium text-sm">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <?php 
                            $is_current_user = ($usuario['id'] == $current_user['id']);
                            $can_manage = can_manage_user($usuario['id'], $usuario['nivel']);
                            $is_target_ceo = ($usuario['nivel'] === 'CEO');
                        ?>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4 text-white/60 text-sm">#<?= $usuario['id'] ?></td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-white font-medium"><?= htmlspecialchars($usuario['username']) ?></span>
                                    <?php if ($is_current_user): ?>
                                    <span class="px-2 py-0.5 bg-green-600/20 text-green-400 text-xs rounded-md border border-green-600/30">Você</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-white/60 text-sm"><?= htmlspecialchars($usuario['email']) ?></td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold <?= $is_target_ceo ? 'bg-red-600/20 text-red-400 border border-red-600/50' : 'bg-blue-600/20 text-blue-400 border border-blue-600/50' ?>">
                                    <?= $is_target_ceo ? '🔴 CEO' : '🔵 Admin' ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 text-white/60 text-sm"><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></td>
                            <td class="py-4 px-4 text-white/60 text-sm">
                                <?= $usuario['last_login'] ? date('d/m/Y H:i', strtotime($usuario['last_login'])) : 'Nunca' ?>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold <?= $usuario['is_active'] ? 'bg-green-600/20 text-green-400' : 'bg-gray-600/20 text-gray-400' ?>">
                                    <?= $usuario['is_active'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <?php if ($can_manage): ?>
                                    <button 
                                        onclick="toggleStatus(<?= $usuario['id'] ?>, <?= $usuario['is_active'] ? 0 : 1 ?>)"
                                        class="p-2 hover:bg-white/10 rounded-lg transition-colors text-blue-400 hover:text-blue-300"
                                        title="<?= $usuario['is_active'] ? 'Desativar' : 'Ativar' ?>"
                                    >
                                        <i data-lucide="<?= $usuario['is_active'] ? 'user-x' : 'user-check' ?>" class="w-4 h-4"></i>
                                    </button>
                                    <button 
                                        onclick="confirmDelete(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['username'], ENT_QUOTES) ?>', '<?= $usuario['nivel'] ?>')"
                                        class="p-2 hover:bg-white/10 rounded-lg transition-colors text-red-400 hover:text-red-300"
                                        title="Excluir"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    <?php else: ?>
                                    <span class="text-white/30 text-xs px-3 py-1">
                                        <?= $is_current_user ? 'Você mesmo' : 'Sem permissão' ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-white/40">
                                <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                                <p>Nenhum administrador cadastrado</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Informações de Segurança -->
        <div class="mt-6 glass rounded-xl p-4 border border-yellow-600/30">
            <div class="flex items-start gap-3">
                <i data-lucide="shield-alert" class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-yellow-300 text-sm font-medium">Travas de Segurança Ativas</p>
                    <ul class="text-yellow-400/70 text-xs mt-2 space-y-1">
                        <li>• Você não pode excluir sua própria conta</li>
                        <li>• Não é possível excluir o único CEO do sistema</li>
                        <li>• Administradores não podem gerenciar usuários CEO</li>
                        <?php if ($is_ceo_user): ?>
                        <li>• Apenas CEOs podem alterar a chave de cadastro</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="glass border border-red-600/30 rounded-2xl p-8 w-full max-w-md mx-4">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-600/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Confirmar Exclusão</h3>
                <p class="text-white/60 mb-6">
                    Tem certeza que deseja excluir o usuário <span id="deleteUsername" class="text-red-400 font-semibold"></span>?
                    <br><span class="text-xs">Esta ação não pode ser desfeita.</span>
                </p>
                <div class="flex gap-3">
                    <button 
                        onclick="closeDeleteModal()" 
                        class="flex-1 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl font-semibold text-white transition-all"
                    >
                        Cancelar
                    </button>
                    <button 
                        id="confirmDeleteBtn"
                        onclick="executeDelete()" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 rounded-xl font-semibold text-white hover:shadow-[0_0_30px_rgba(220,38,38,0.5)] transition-all"
                    >
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
        
        // Variáveis globais para modal
        let userToDelete = null;
        
        <?php if ($is_ceo_user): ?>
        // Atualizar Chave de Cadastro
        document.getElementById('updateKeyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const nova_chave = document.getElementById('nova_chave').value;
            const messageDiv = document.getElementById('keyUpdateMessage');
            
            if (nova_chave.length < 6) {
                showMessage(messageDiv, 'A chave deve ter no mínimo 6 caracteres', 'error');
                return;
            }
            
            try {
                const response = await fetch('ajax_admin_operations.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update_key',
                        nova_chave: nova_chave
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage(messageDiv, 'Chave de cadastro atualizada com sucesso!', 'success');
                } else {
                    showMessage(messageDiv, data.message || 'Erro ao atualizar chave', 'error');
                }
            } catch (error) {
                showMessage(messageDiv, 'Erro de conexão. Tente novamente.', 'error');
            }
        });
        <?php endif; ?>
        
        // Toggle Status
        async function toggleStatus(userId, newStatus) {
            try {
                const response = await fetch('ajax_admin_operations.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'toggle_status',
                        user_id: userId,
                        status: newStatus
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao alterar status');
                }
            } catch (error) {
                alert('Erro de conexão. Tente novamente.');
            }
        }
        
        // Confirmar Exclusão
        function confirmDelete(userId, username, nivel) {
            userToDelete = { id: userId, nivel: nivel };
            document.getElementById('deleteUsername').textContent = username;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        // Fechar Modal
        function closeDeleteModal() {
            userToDelete = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }
        
        // Executar Exclusão
        async function executeDelete() {
            if (!userToDelete) return;
            
            const btn = document.getElementById('confirmDeleteBtn');
            btn.disabled = true;
            btn.textContent = 'Excluindo...';
            
            try {
                const response = await fetch('ajax_admin_operations.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'delete_user',
                        user_id: userToDelete.id,
                        user_nivel: userToDelete.nivel
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao excluir usuário');
                    btn.disabled = false;
                    btn.textContent = 'Excluir';
                }
            } catch (error) {
                alert('Erro de conexão. Tente novamente.');
                btn.disabled = false;
                btn.textContent = 'Excluir';
            }
        }
        
        // Helper para mostrar mensagens
        function showMessage(element, message, type) {
            element.className = 'mt-4 p-4 rounded-xl border flex items-center gap-3';
            
            if (type === 'success') {
                element.className += ' bg-green-600/20 border-green-600/50';
                element.innerHTML = `
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-400"></i>
                    <p class="text-green-300 text-sm font-medium">${message}</p>
                `;
            } else {
                element.className += ' bg-red-600/20 border-red-600/50';
                element.innerHTML = `
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                    <p class="text-red-300 text-sm font-medium">${message}</p>
                `;
            }
            
            element.classList.remove('hidden');
            lucide.createIcons();
            
            setTimeout(() => {
                element.classList.add('hidden');
            }, 5000);
        }
    </script>
</body>
</html>
