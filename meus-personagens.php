<?php
// Initialize personagem object
if (!is_object($personagem) || !method_exists($personagem, 'getMeusPersonagens')) {
    $personagem = new Personagens();
}

// We'll check after getMeusPersonagens() is called
ob_start(); // Start output buffering
$personagem->getMeusPersonagens($user->id);
$charactersList = ob_get_clean(); // Get the output

// Check if there are characters (if output is empty or just whitespace)
$hasCharacters = (trim($charactersList) !== '');
?>

<?php
// ✅ HANDLE NOTIFICATIONS
$showSuccessNotification = false;
$showErrorNotification = false;
$notificationMessage = '';
$characterName = '';
$shouldRedirect = false;

// Check for error parameter
if(isset($_GET['error']) && $_GET['error'] === 'no_character') {
    $showErrorNotification = true;
    $notificationMessage = 'Você precisa selecionar um personagem antes de jogar!';
}

// Check if character was just created
if(isset($_SESSION['character_created']) && $_SESSION['character_created'] === true) {
    $showSuccessNotification = true;
    $characterName = isset($_SESSION['character_name']) ? $_SESSION['character_name'] : 'seu guerreiro';
    $notificationMessage = '<strong>' . htmlspecialchars($characterName) . '</strong> foi criado com sucesso!';
    unset($_SESSION['character_created']);
    unset($_SESSION['character_name']);
}

// Handle character selection (JOGAR button)
if(isset($_POST['jogar'])){
    if(!empty($_POST['idPersonagem'])){
        $idPersonagem = (int)$_POST['idPersonagem'];
        
        // Verify character belongs to user
        $core = new Core();
        $check = $core->getDados('usuarios_personagens', "WHERE id = {$idPersonagem} AND idUsuario = {$user->id}");
        
        if($check){
            $_SESSION['PERSONAGEMID'] = $idPersonagem;
            $showSuccessNotification = true;
            $characterName = htmlspecialchars($check->nome);
            $notificationMessage = 'Personagem <strong>' . $characterName . '</strong> selecionado com sucesso!';
            $shouldRedirect = true;
        } else {
            $showErrorNotification = true;
            $notificationMessage = 'Personagem inválido ou não encontrado!';
        }
    } else {
        $showErrorNotification = true;
        $notificationMessage = 'Você precisa selecionar um personagem antes de jogar!';
    }
}


// Handle character deletion - COMPLETE WORKING VERSION
if(isset($_POST['deletar_personagem'])){
    error_log("DELETE REQUEST RECEIVED for ID: " . $_POST['deletar_personagem']); // Debug log
    
    $idPersonagem = (int)$_POST['deletar_personagem'];
    
    // Verify character belongs to user
    $core = new Core();
    $check = $core->getDados('usuarios_personagens', "WHERE id = {$idPersonagem} AND idUsuario = {$user->id}");
    
    if($check){
        $characterName = htmlspecialchars($check->nome);
        error_log("Character found: $characterName"); // Debug log
        
        try {
            // Start transaction
            $core->conn->beginTransaction();
            error_log("Transaction started"); // Debug log
            
            // 1. Get inventory slot IDs
            $stmt = $core->conn->prepare("SELECT id FROM personagens_inventario WHERE idPersonagem = ?");
            $stmt->execute([$idPersonagem]);
            $inventorySlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("Inventory slots found: " . count($inventorySlots)); // Debug log
            
            // 2. Delete inventory items
            if(!empty($inventorySlots)){
                $placeholders = implode(',', array_fill(0, count($inventorySlots), '?'));
                $stmt = $core->conn->prepare("DELETE FROM personagens_inventario_itens WHERE idSlot IN ($placeholders)");
                $stmt->execute($inventorySlots);
                error_log("Inventory items deleted"); // Debug log
            }
            
            // 3. Delete from related tables
            $tables = ['personagens_inventario', 'personagens_buffs', 'personagens_golpes', 'personagensitensequipados'];
            foreach($tables as $table){
                $stmt = $core->conn->prepare("DELETE FROM $table WHERE idPersonagem = ?");
                $stmt->execute([$idPersonagem]);
                error_log("Deleted from $table"); // Debug log
            }
            
            // 4. Delete character
            $stmt = $core->conn->prepare("DELETE FROM usuarios_personagens WHERE id = ?");
            $stmt->execute([$idPersonagem]);
            error_log("Character deleted"); // Debug log
            
            // Commit
            $core->conn->commit();
            error_log("Transaction committed"); // Debug log
            
            // Success!
            $showSuccessNotification = true;
            $notificationMessage = '✅ **' . $characterName . '** foi deletado com sucesso! 🗑️';
            
            // Clear session
            if(isset($_SESSION['PERSONAGEMID']) && $_SESSION['PERSONAGEMID'] == $idPersonagem){
                unset($_SESSION['PERSONAGEMID']);
            }
            
        } catch(Exception $e) {
            if(isset($core->conn)) $core->conn->rollBack();
            $showErrorNotification = true;
            $notificationMessage = 'Erro: ' . $e->getMessage();
            error_log("DELETE ERROR: " . $e->getMessage()); // Debug log
        }
    } else {
        $showErrorNotification = true;
        $notificationMessage = 'Personagem não encontrado!';
        error_log("Character not found or no permission"); // Debug log
    }
}



?>
<?php if($showErrorNotification): ?>
<div class="notification-error" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; padding: 15px 20px; border-radius: 10px; margin: 20px auto; max-width: 600px; text-align: center; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4); animation: slideDown 0.3s ease;">
    <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-right: 10px;"></i>
    <strong style="font-size: 18px;"><?php echo $notificationMessage; ?></strong>
</div>
<style>
@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
<?php endif; ?>

<?php if($showSuccessNotification): ?>
<div class="notification-success" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; padding: 15px 20px; border-radius: 10px; margin: 20px auto; max-width: 600px; text-align: center; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4); animation: slideDown 0.3s ease;">
    <i class="fas fa-check-circle" style="font-size: 24px; margin-right: 10px;"></i>
    <strong style="font-size: 18px;"><?php echo $notificationMessage; ?></strong>
</div>
<?php endif; ?>


<div class="personagem-atual">

    <div class="foto-personagem">
        <img src="<?php echo BASE; ?>assets/guerreiro_blank.jpg" alt="Selecione seu Guerreiro" />
    </div>
    <div class="info">
        <h3>Não Selecionado</h3>
        <div class="atributos raca">
            <strong>Raça: </strong>
            Não Selecionado
        </div>
        <div class="atributos planeta">
            <strong>Planeta: </strong>
            Não Selecionado
        </div>
        <div class="atributos graduacao">
            <strong>Graduação: </strong>
            Não Selecionado
        </div>
        <div class="atributos nivel">
            <strong>Nível: </strong>
            Não Selecionado
        </div>
        <div class="atributos hp at-meter">
            <strong>HP </strong>
            <div class="meter animate red">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
        <div class="atributos mana at-meter">
            <strong>KI </strong>
            <div class="meter animate blue">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
        <div class="atributos energia at-meter">
            <strong>Energia </strong>
            <div class="meter animate">
                <em>0 / <strong>0</strong></em>
                <span style="width: 2%"><span></span></span>
            </div>
        </div>
    </div>
</div>

<h2 class="title">Escolha um Guerreiro</h2>

<div class="lista-meus-personagens">
    <?php if(!$hasCharacters): ?>
        <!-- NO CHARACTERS MESSAGE -->
        <div class="empty-characters-state" style="text-align: center; padding: 60px 20px; background: rgba(0, 0, 0, 0.4); border-radius: 15px; margin: 20px auto; max-width: 600px;">
            <div style="font-size: 60px; margin-bottom: 20px;">⚠️</div>
            <p style="font-size: 22px; color: #ffcc00; margin-bottom: 30px; font-weight: bold; letter-spacing: 1px;">
                Você ainda não possui nenhum guerreiro!
            </p>
            <a href="<?php echo BASE; ?>criar-personagem" 
               style="display: inline-block; 
                      background: linear-gradient(135deg, #58e945ff 0%, #38c27dff 100%); 
                      color: #ffffff; 
                      padding: 18px 50px; 
                      border-radius: 30px; 
                      text-decoration: none; 
                      font-weight: bold; 
                      font-size: 20px; 
                      text-transform: uppercase; 
                      letter-spacing: 1.5px;
                      box-shadow: 0 5px 20px rgba(196, 228, 203, 0.5); 
                      transition: all 0.3s ease;
                      border: 2px solid rgba(255, 255, 255, 0.1);"
               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(208, 255, 176, 0.7)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(215, 245, 108, 0.5)';">
                🔥 CRIAR GUERREIRO
            </a>
        </div>
    <?php else: ?>
        <!-- EXISTING CHARACTER LIST -->
        <?php echo $charactersList; ?>
    <?php endif; ?>
</div>

<style>
.empty-characters-state a {
    cursor: pointer;
}

.empty-characters-state p {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

@media (max-width: 768px) {
    .empty-characters-state {
        padding: 40px 15px !important;
    }
    
    .empty-characters-state p {
        font-size: 18px !important;
    }
    
    .empty-characters-state a {
        padding: 15px 35px !important;
        font-size: 16px !important;
    }
}
</style>


<script>
// Handle delete button clicks from AJAX-loaded content
$(document).on('click', '.bt-deletar', function(e) {
    e.preventDefault();
    const characterId = $(this).data('id');
    
    if(confirm('Tem certeza que deseja deletar este personagem? Esta ação não pode ser desfeita!')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deletarpersonagem';
        input.value = characterId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
});
</script>

