<?php 
// Security check for admin access
if(!isset($_SESSION['user_logado']) || $_SESSION['user_logado'] != true){
    header('Location: '.BASE.'login');
    exit;
}

// Load user info and check admin permission
if(isset($_SESSION['username'])){
    $user->getUserInfo($_SESSION['username']);
}

// Check if user has admin privileges (perfil = 2)
if(!isset($user->perfil) || $user->perfil != 2){
    echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px; border: 1px solid #f5c6cb;">
            <strong>Acesso Negado!</strong> Você não tem permissão para acessar esta área administrativa.
          </div>';
    echo '<a href="'.BASE.'portal" style="margin: 20px; display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Voltar ao Portal</a>';
    exit;
}

// Get pagination parameter
if(isset($_GET['pagina'])){
    $pc = $_GET['pagina'];
} else {
    $pc = 1;
}

// Get edit ID if present
$editId = null;
$editItem = null;
if(isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    // Get item data for editing
    $sql = "SELECT * FROM adm_loja_itens WHERE id = ? AND loja = 1";
    $stmt = DB::prepare($sql);
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}
?>

<style>
.admin-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px;
}

.admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: center;
}

.admin-nav {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.admin-nav a {
    background: #007bff;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s ease;
    font-size: 14px;
}

.admin-nav a:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.admin-nav a.active {
    background: #28a745;
}

.title {
    color: #343a40;
    margin-bottom: 25px;
    font-size: 28px;
    font-weight: bold;
    border-bottom: 3px solid #007bff;
    padding-bottom: 10px;
}

.lista-geral {
    width: 100%;
    background: white;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.lista-geral th {
    background: #343a40;
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    border-right: 1px solid #495057;
}

.lista-geral th:last-child {
    border-right: none;
}

.lista-geral td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
}

.lista-geral td:last-child {
    border-right: none;
}

.lista-geral tbody tr:hover {
    background: #f8f9fa;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-card .number {
    font-size: 36px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
}

.stat-card .label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.form-container {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group input:required:invalid {
    border-color: #dc3545;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #1e7e34;
}

.btn-danger {
    background: #dc3545;
    color: white;
    font-size: 12px;
    padding: 8px 16px;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-edit {
    background: #ffc107;
    color: #212529;
    font-size: 12px;
    padding: 6px 12px;
    margin-right: 5px;
}

.btn-edit:hover {
    background: #e0a800;
}

.btn-cancel {
    background: #6c757d;
    color: white;
    margin-left: 10px;
}

.btn-cancel:hover {
    background: #545b62;
}

.success-msg {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}

.error-msg {
    background: #f8d7da;
    color: #721c24;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.validation-errors {
    background: #f8d7da;
    color: #721c24;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.validation-errors ul {
    margin: 0;
    padding-left: 20px;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    margin-right: 5px;
}

.badge-novo {
    background: #28a745;
    color: white;
}

.badge-promo {
    background: #dc3545;
    color: white;
}

.badge-flag {
    background: #007bff;
    color: white;
}

.not {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 30px;
}

.flags-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 10px;
}

.flags-section h5 {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 14px;
}

.flags-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.flag-checkbox {
    font-weight: normal;
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.flag-checkbox input {
    width: auto;
    margin-right: 8px;
}
</style>

<div class="admin-container">
    <div class="admin-header">
        <h1>🎮 Painel Administrativo - DB Heroes</h1>
        <p>Sistema de Administração do Jogo</p>
    </div>

    <div class="admin-nav">
        <a href="<?php echo BASE; ?>administrar" <?php echo (!$acao || $acao == 'default') ? 'class="active"' : ''; ?>>
            📊 Dashboard
        </a>
        <a href="<?php echo BASE; ?>administrar/online" <?php echo ($acao == 'online') ? 'class="active"' : ''; ?>>
            👥 Usuários Online
        </a>
        <a href="<?php echo BASE; ?>administrar/faturamento" <?php echo ($acao == 'faturamento') ? 'class="active"' : ''; ?>>
            💰 Faturamento
        </a>
        <a href="<?php echo BASE; ?>administrar/loja" <?php echo ($acao == 'loja') ? 'class="active"' : ''; ?>>
            🛒 Visão Geral Loja
        </a>
        <a href="<?php echo BASE; ?>administrar/itens" <?php echo ($acao == 'itens') ? 'class="active"' : ''; ?>>
            ⚔️ Gerenciar Itens
        </a>
        <a href="<?php echo BASE; ?>administrar/produtos" <?php echo ($acao == 'produtos') ? 'class="active"' : ''; ?>>
            📦 Produtos Diários
        </a>
    </div>

<?php switch($acao) {
    default: ?>
        <h2 class="title">📊 Dashboard Administrativo</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Usuários</h3>
                <div class="number">
                    <?php 
                        $sql = "SELECT COUNT(*) as total FROM usuarios";
                        $stmt = DB::prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch();
                        echo number_format($result->total);
                    ?>
                </div>
                <div class="label">Registrados</div>
            </div>
            
            <div class="stat-card">
                <h3>Personagens</h3>
                <div class="number">
                    <?php 
                        $sql = "SELECT COUNT(*) as total FROM usuarios_personagens";
                        $stmt = DB::prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch();
                        echo number_format($result->total);
                    ?>
                </div>
                <div class="label">Criados</div>
            </div>
            
            <div class="stat-card">
                <h3>Itens da Loja</h3>
                <div class="number">
                    <?php 
                        $sql = "SELECT COUNT(*) as total FROM adm_loja_itens WHERE loja = 1";
                        $stmt = DB::prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch();
                        echo number_format($result->total);
                    ?>
                </div>
                <div class="label">Ativos</div>
            </div>
            
            <div class="stat-card">
                <h3>Receita Mensal</h3>
                <div class="number">
                    <?php 
                        $sql = "SELECT SUM(valor) as total FROM transacoes 
                                WHERE MONTH(data) = MONTH(NOW()) 
                                AND YEAR(data) = YEAR(NOW()) 
                                AND status = 3";
                        $stmt = DB::prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch();
                        echo 'R$ '.number_format($result->total ?: 0, 2, ',', '.');
                    ?>
                </div>
                <div class="label">Este Mês</div>
            </div>
        </div>

        <div class="admin-nav">
            <a href="<?php echo BASE; ?>administrar/itens" class="btn btn-primary">➕ Adicionar Item</a>
            <a href="<?php echo BASE; ?>administrar/produtos" class="btn btn-success">📅 Config. Produtos</a>
            <a href="<?php echo BASE; ?>loja" target="_blank" class="btn btn-primary">👀 Ver Loja</a>
        </div>
    <?php break; ?>
    
    <?php case 'online': ?>
        <h2 class="title">👥 Visitantes Online</h2>
    
        <table class="lista-geral">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>E-mail</th>
                    <th>Data Cadastro</th>
                    <th>Gold</th>
                    <th>Nível</th>
                </tr>
            </thead>
            <tbody>
                <?php $administrar->getListVisitantesOnline($pc, 30); ?>
            </tbody>
        </table>
    <?php break; ?>
        
    <?php case 'faturamento': ?>
        <h2 class="title">💰 Transações no Mês Atual</h2>
    
        <table class="lista-geral">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Data</th>
                    <th>Valor</th>
                    <th>Coins</th>
                </tr>
            </thead>
            <tbody>
                <?php $administrar->getListTransacoes($pc, 30); ?>
            </tbody>
        </table>
    <?php break; ?>

    <?php case 'loja': ?>
        <h2 class="title">🛒 Visão Geral da Loja</h2>
        
        <?php $stats = $administrar->getLojaStats(); ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Itens Ativos</h3>
                <div class="number"><?php echo $stats->total_items; ?></div>
                <div class="label">Total</div>
            </div>
            
            <div class="stat-card">
                <h3>Fotos</h3>
                <div class="number"><?php echo $stats->fotos; ?></div>
                <div class="label">Personagens</div>
            </div>
            
            <div class="stat-card">
                <h3>Módulos</h3>
                <div class="number"><?php echo $stats->modulos; ?></div>
                <div class="label">Acessórios</div>
            </div>
            
            <div class="stat-card">
                <h3>Itens/Baús</h3>
                <div class="number"><?php echo $stats->itens; ?></div>
                <div class="label">Consumíveis</div>
            </div>
        </div>

        <div class="admin-nav">
            <a href="<?php echo BASE; ?>administrar/itens" class="btn btn-primary">Gerenciar Itens</a>
            <a href="<?php echo BASE; ?>administrar/produtos" class="btn btn-success">Configurar Rotação Diária</a>
            <a href="<?php echo BASE; ?>loja" target="_blank" class="btn btn-primary">Ver Loja Pública</a>
        </div>
        
        <h3>Status do Dia Ativo</h3>
        <?php if($stats->active_day): ?>
            <?php 
                $dias = ['', 'Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            ?>
            <div class="success-msg">
                <strong>Dia Ativo:</strong> <?php echo $dias[$stats->active_day]; ?>
            </div>
        <?php else: ?>
            <div class="error-msg">
                <strong>Nenhum dia ativo!</strong> Configure e ative um dia na seção Produtos Diários.
            </div>
        <?php endif; ?>
    <?php break; ?>

    <?php case 'itens': ?>
        <h2 class="title">⚔️ Gerenciar Itens da Loja</h2>
        
        <?php
        $errors = [];
        $success = false;
        
        // Handle form submissions
        if(isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if($action === 'add' || $action === 'edit') {
                // Validation
                if(empty($_POST['nome'])) {
                    $errors[] = "Nome do item é obrigatório";
                }
                
                if(empty($_POST['valor']) || intval($_POST['valor']) <= 0) {
                    $errors[] = "Valor deve ser maior que zero";
                }
                
                if(empty($_POST['modulo'])) {
                    $errors[] = "Tipo do item é obrigatório";
                }
                
                // Process selected flags
                $selectedFlags = [];
                
                // Rarity flags
                if(isset($_POST['flag_comum'])) $selectedFlags[] = 'Comum';
                if(isset($_POST['flag_raro'])) $selectedFlags[] = 'Raro';
                if(isset($_POST['flag_epico'])) $selectedFlags[] = 'Épico';
                if(isset($_POST['flag_lendario'])) $selectedFlags[] = 'Lendário';
                if(isset($_POST['flag_mitico'])) $selectedFlags[] = 'Mítico';
                
                // Special status flags
                if(isset($_POST['flag_limitado'])) $selectedFlags[] = 'Limitado';
                if(isset($_POST['flag_exclusivo'])) $selectedFlags[] = 'Exclusivo';
                if(isset($_POST['flag_evento'])) $selectedFlags[] = 'Evento';
                
                // Item type flags
                if(isset($_POST['flag_poder'])) $selectedFlags[] = 'Poder';
                if(isset($_POST['flag_defesa'])) $selectedFlags[] = 'Defesa';
                if(isset($_POST['flag_velocidade'])) $selectedFlags[] = 'Velocidade';
                if(isset($_POST['flag_especial'])) $selectedFlags[] = 'Especial';
                
                // Custom flag
                if(!empty($_POST['flag_custom'])) {
                    $selectedFlags[] = trim($_POST['flag_custom']);
                }
                
                // Combine flags
                $finalFlag = implode(', ', $selectedFlags);
                
                // If no errors, proceed with add/edit
                if(empty($errors)) {
                    $data = [
                        'nome' => trim($_POST['nome']),
                        'descricao' => trim($_POST['descricao']),
                        'valor' => intval($_POST['valor']),
                        'modulo' => intval($_POST['modulo']),
                        'foto' => trim($_POST['foto']),
                        'idBoneco' => $_POST['idBoneco'] ? intval($_POST['idBoneco']) : null,
                        'idItem' => $_POST['idItem'] ? intval($_POST['idItem']) : null,
                        'novo' => isset($_POST['novo']),
                        'promocao' => isset($_POST['promocao']),
                        'flag' => $finalFlag
                    ];
                        // ⭐ KEY UPDATE: Update photo raridade if this is a photo item
                    if($data['modulo'] == 1 && !empty($data['foto'])) {
                        $administrar->updatePhotoRaridade($data['foto'], $selectedFlags);
                    }
                    
                    if($action === 'add') {
                        if($administrar->addLojaItem($data)) {
                            echo '<div class="success-msg">✅ Item adicionado com sucesso!</div>';
                            $success = true;
                        } else {
                            $errors[] = "Erro ao adicionar item no banco de dados";
                        }
                    } else if($action === 'edit') {
                        $editId = intval($_POST['edit_id']);
                        if($administrar->updateLojaItem($editId, $data)) {
                            echo '<div class="success-msg">✅ Item editado com sucesso!</div>';
                            $success = true;
                            // Clear edit mode
                            $editId = null;
                            $editItem = null;
                        } else {
                            $errors[] = "Erro ao editar item no banco de dados";
                        }
                    }
                }
            }
            
            if($action === 'delete') {
                $id = intval($_POST['id']);
                if($administrar->removeLojaItem($id)) {
                    echo '<div class="success-msg">✅ Item removido com sucesso!</div>';
                } else {
                    echo '<div class="error-msg">❌ Erro ao remover item.</div>';
                }
            }
        }
        
        // Display validation errors
        if(!empty($errors)) {
            echo '<div class="validation-errors">';
            echo '<strong>❌ Corrija os seguintes erros:</strong>';
            echo '<ul>';
            foreach($errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <div class="form-container">
            <h3><?php echo $editItem ? '✏️ Editar Item: ' . htmlspecialchars($editItem->nome) : '➕ Adicionar Novo Item'; ?></h3>
            
            <?php if($editItem): ?>
                <a href="<?php echo BASE; ?>administrar/itens" class="btn btn-cancel">❌ Cancelar Edição</a>
                <hr style="margin: 20px 0;">
            <?php endif; ?>
            
            <form method="post" id="itemForm">
                <input type="hidden" name="action" value="<?php echo $editItem ? 'edit' : 'add'; ?>">
                <?php if($editItem): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editItem->id; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label>Nome do Item <span style="color: red;">*</span></label>
                            <input type="text" name="nome" required placeholder="Ex: Goku SSJ" 
                                   value="<?php echo $editItem ? htmlspecialchars($editItem->nome) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Valor em Coins <span style="color: red;">*</span></label>
                            <input type="number" name="valor" required min="1" placeholder="Ex: 100"
                                   value="<?php echo $editItem ? $editItem->valor : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Tipo do Item <span style="color: red;">*</span></label>
                            <select name="modulo" required>
                                <option value="">Selecione o tipo</option>
                                <option value="1" <?php echo ($editItem && $editItem->modulo == 1) ? 'selected' : ''; ?>>🖼️ Foto de Personagem</option>
                                <option value="2" <?php echo ($editItem && $editItem->modulo == 2) ? 'selected' : ''; ?>>🎨 Módulo/Acessório</option>
                                <option value="3" <?php echo ($editItem && $editItem->modulo == 3) ? 'selected' : ''; ?>>📦 Item/Baú</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Nome da Imagem</label>
                            <input type="text" name="foto" placeholder="Ex: goku_ssj.png"
                                   value="<?php echo $editItem ? htmlspecialchars($editItem->foto) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea name="descricao" placeholder="Descrição do item..."><?php echo $editItem ? htmlspecialchars($editItem->descricao) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label>Personagem (para fotos)</label>
                            <select name="idBoneco">
                                <option value="">Selecione um personagem</option>
                                <?php 
                                    $personagens = $administrar->getPersonagensDisponiveis();
                                    foreach($personagens as $p) {
                                        $selected = ($editItem && $editItem->idBoneco == $p->id) ? 'selected' : '';
                                        echo '<option value="'.$p->id.'" '.$selected.'>'.$p->nome.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Item Base (para baús)</label>
                            <select name="idItem">
                                <option value="">Selecione um item</option>
                                <?php 
                                    $itens = $administrar->getItensDisponiveis();
                                    foreach($itens as $item) {
                                        $selected = ($editItem && $editItem->idItem == $item->id) ? 'selected' : '';
                                        echo '<option value="'.$item->id.'" '.$selected.'>'.$item->nome.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <!-- ENHANCED FLAGS SECTION -->
                        <div class="form-group">
                            <label><strong>🏷️ Flags Predefinidas</strong></label>
                            
                            <div class="flags-section">
                                <h5>🎯 Raridade</h5>
                                <div class="flags-grid">
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_comum">
                                        <span style="color: #28a745;">⬤ Comum</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_raro">
                                        <span style="color: #007bff;">⬤ Raro</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_epico">
                                        <span style="color: #6f42c1;">⬤ Épico</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_lendario">
                                        <span style="color: #fd7e14;">⬤ Lendário</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_mitico">
                                        <span style="color: #dc3545;">⬤ Mítico</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flags-section">
                                <h5>⭐ Status Especial</h5>
                                <div class="flags-grid">
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_limitado">
                                        <span style="color: #ffc107;">🔥 Limitado</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_exclusivo">
                                        <span style="color: #e83e8c;">💎 Exclusivo</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_evento">
                                        <span style="color: #20c997;">🎉 Evento</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flags-section">
                                <h5>⚡ Tipo de Poder</h5>
                                <div class="flags-grid">
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_poder">
                                        <span style="color: #dc3545;">⚡ Poder</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_defesa">
                                        <span style="color: #6c757d;">🛡️ Defesa</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_velocidade">
                                        <span style="color: #17a2b8;">💨 Velocidade</span>
                                    </label>
                                    <label class="flag-checkbox">
                                        <input type="checkbox" name="flag_especial">
                                        <span style="color: #6f42c1;">✨ Especial</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Flag Personalizada (adicional)</label>
                                <input type="text" name="flag_custom" placeholder="Ex: Ultra Raro, Mega Power">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="novo" <?php echo ($editItem && $editItem->novo) ? 'checked' : ''; ?>> 🆕 Marcar como "Novo"
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="promocao" <?php echo ($editItem && $editItem->promocao) ? 'checked' : ''; ?>> 🔥 Marcar como "Promoção"
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <?php echo $editItem ? '✏️ Salvar Edição' : '➕ Adicionar Item'; ?>
                </button>
                
                <?php if($editItem): ?>
                    <a href="<?php echo BASE; ?>administrar/itens" class="btn btn-cancel">❌ Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <h3>📋 Lista de Itens</h3>
        <table class="lista-geral">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome / Descrição</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Custom list for better control over edit links
                $sql = "SELECT ali.*, p.nome as personagem_nome 
                        FROM adm_loja_itens ali 
                        LEFT JOIN personagens p ON p.id = ali.idBoneco 
                        WHERE ali.loja = 1 
                        ORDER BY ali.id DESC 
                        LIMIT 20";
                
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll();
                
                if(count($items) > 0) {
                    foreach($items as $item) {
                        $tipo_texto = '';
                        switch($item->modulo) {
                            case 1: $tipo_texto = 'Foto de Personagem'; break;
                            case 2: $tipo_texto = 'Módulo/Acessório'; break;
                            case 3: $tipo_texto = 'Item/Baú'; break;
                        }
                        
                        $status_badges = '';
                        if($item->novo) $status_badges .= '<span class="badge badge-novo">Novo</span> ';
                        if($item->promocao) $status_badges .= '<span class="badge badge-promo">Promoção</span> ';
                        if($item->flag) {
                            // Split flags and create colored badges
                            $flags = explode(', ', $item->flag);
                            foreach($flags as $flag) {
                                $color = '';
                                switch(trim($flag)) {
                                    case 'Comum': $color = '#28a745'; break;
                                    case 'Raro': $color = '#007bff'; break;
                                    case 'Épico': $color = '#6f42c1'; break;
                                    case 'Lendário': $color = '#fd7e14'; break;
                                    case 'Mítico': $color = '#dc3545'; break;
                                    case 'Limitado': $color = '#ffc107'; break;
                                    case 'Exclusivo': $color = '#e83e8c'; break;
                                    case 'Evento': $color = '#20c997'; break;
                                    case 'Poder': $color = '#dc3545'; break;
                                    case 'Defesa': $color = '#6c757d'; break;
                                    case 'Velocidade': $color = '#17a2b8'; break;
                                    case 'Especial': $color = '#6f42c1'; break;
                                    default: $color = '#6c757d'; break;
                                }
                                $status_badges .= '<span class="badge" style="background: '.$color.'; color: white; margin-right: 4px;">'.trim($flag).'</span>';
                            }
                        }
                        
                        echo '<tr>';
                        echo '<td>'.$item->id.'</td>';
                        echo '<td>'.($item->foto ? '<img src="'.BASE.'assets/cards/'.$item->foto.'" style="width:40px;height:40px;border-radius:4px;" alt="'.$item->nome.'">' : 'N/A').'</td>';
                        echo '<td><strong>'.$item->nome.'</strong><br><small>'.$item->descricao.'</small></td>';
                        echo '<td>'.$item->valor.' coins</td>';
                        echo '<td>'.$tipo_texto.'</td>';
                        echo '<td>'.$status_badges.'</td>';
                        echo '<td>';
                        echo '<a href="'.BASE.'administrar/itens?edit='.$item->id.'" class="btn btn-edit">EDITAR</a>';
                        echo '<form method="post" style="display: inline; margin-left: 5px;">';
                        echo '<input type="hidden" name="action" value="delete">';
                        echo '<input type="hidden" name="id" value="'.$item->id.'">';
                        echo '<button type="submit" class="btn btn-danger" onclick="return confirm(\'Tem certeza que deseja excluir este item?\')">EXCLUIR</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" class="not">Nenhum item encontrado na loja.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    <?php break; ?>

    <?php case 'produtos': ?>
        <h2 class="title">📦 Configurar Produtos Diários</h2>
        
        <?php
        // Handle form submissions for produtos
        if(isset($_POST['update_products'])) {
            $dia = intval($_POST['dia']);
            $positions = [];
            for($i = 1; $i <= 8; $i++) {
                $positions[$i] = $_POST["posicao_$i"] ? intval($_POST["posicao_$i"]) : null;
            }
            
            if($administrar->updateProdutosDiarios($dia, $positions)) {
                echo '<div class="success-msg">✅ Produtos do dia configurados com sucesso!</div>';
            } else {
                echo '<div class="error-msg">❌ Erro ao configurar produtos do dia.</div>';
            }
        }
        
        if(isset($_GET['activate'])) {
            $dia = intval($_GET['activate']);
            if($administrar->activateDiaLoja($dia)) {
                echo '<div class="success-msg">✅ Dia ativado com sucesso!</div>';
            } else {
                echo '<div class="error-msg">❌ Erro ao ativar o dia.</div>';
            }
        }
        ?>

        <div class="form-container">
            <h3>⚙️ Configurar Rotação Diária</h3>
            <form method="post">
                <div class="form-group">
                    <label>Dia da Semana *</label>
                    <select name="dia" required>
                        <option value="">Selecione um dia</option>
                        <option value="1">🌅 Domingo</option>
                        <option value="2">📅 Segunda-feira</option>
                        <option value="3">📅 Terça-feira</option>
                        <option value="4">📅 Quarta-feira</option>
                        <option value="5">📅 Quinta-feira</option>
                        <option value="6">📅 Sexta-feira</option>
                        <option value="7">📅 Sábado</option>
                    </select>
                </div>
                
                <h4>🎯 Posições da Loja (8 slots disponíveis)</h4>
                <div class="form-grid">
                    <?php for($i = 1; $i <= 8; $i++): ?>
                        <div class="form-group">
                            <label>
                                <?php echo $i <= 2 ? '⭐ Posição '.$i.' (Destaque)' : '📦 Posição '.$i.' (Normal)'; ?>
                            </label>
                            <select name="posicao_<?php echo $i; ?>">
                                <option value="">🚫 Vazio</option>
                                <?php 
                                    $sql = "SELECT id, nome, valor FROM adm_loja_itens WHERE loja = 1 ORDER BY nome";
                                    $stmt = DB::prepare($sql);
                                    $stmt->execute();
                                    $items = $stmt->fetchAll();
                                    
                                    foreach($items as $item) {
                                        echo '<option value="'.$item->id.'">'.$item->nome.' ('.$item->valor.' coins)</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    <?php endfor; ?>
                </div>
                
                <button type="submit" name="update_products" class="btn btn-success">💾 Salvar Configuração</button>
            </form>
        </div>

        <h3>📅 Status dos Dias da Semana</h3>
        <table class="lista-geral">
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Status</th>
                    <th>Itens Configurados</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $administrar->getListProdutosDiarios(); ?>
            </tbody>
        </table>
    <?php break; ?>
    
<?php } ?>

</div>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('itemForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            const nome = form.querySelector('input[name="nome"]').value.trim();
            const valor = form.querySelector('input[name="valor"]').value;
            const modulo = form.querySelector('select[name="modulo"]').value;
            
            let errors = [];
            
            if(!nome) errors.push('Nome do item é obrigatório');
            if(!valor || parseInt(valor) <= 0) errors.push('Valor deve ser maior que zero');
            if(!modulo) errors.push('Tipo do item é obrigatório');
            
            if(errors.length > 0) {
                e.preventDefault();
                alert('Por favor, corrija os seguintes erros:\n\n' + errors.join('\n'));
                return false;
            }
        });
    }
});

// Auto-refresh para página de usuários online
if(window.location.href.includes('/online')) {
    setInterval(() => {
        location.reload();
    }, 60000);
}

// Confirm delete actions
document.addEventListener('click', function(e) {
    if(e.target.classList.contains('btn-danger')) {
        return confirm('Tem certeza que deseja realizar esta ação?');
    }
});
</script>
