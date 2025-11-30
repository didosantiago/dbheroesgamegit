<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";

$inventario = new Inventario();

// Check if just loading the display
if(isset($_POST['loadOnly']) && $_POST['loadOnly'] == 1){
    if(isset($_SESSION['PERSONAGEMID'])){
        $inventario->getSlotsAdesivos($_SESSION['PERSONAGEMID']);
    }
    exit;
}

// Validate session
if(!isset($_SESSION['PERSONAGEMID'])){
    echo '<div class="error">Sessão inválida</div>';
    exit;
}

$idPersonagem = (int)$_SESSION['PERSONAGEMID'];
$idInventario = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if($idInventario == 0){
 // Just show slots, don't show error on initial load
    $inventario->getSlotsAdesivos($idPersonagem);
    exit;
}

try {
    // ✅ Get the INVENTORY ITEM with item details
    $sql = "SELECT pii.id as inventoryId, pii.idItem, i.adesivo, i.id as itemId, i.nome
            FROM personagens_inventario_itens as pii
            INNER JOIN itens as i ON i.id = pii.idItem
            WHERE pii.id = ? AND pii.idPersonagem = ?";
    $stmt = DB::prepare($sql);
    $stmt->execute([$idInventario, $idPersonagem]);
    $row = $stmt->fetch();
    
    if(!$row){
     // Just show slots, don't show error on initial load
        $inventario->getSlotsAdesivos($idPersonagem);
        exit;
    }
    
    // ✅ Check if it's an adesivo
    if($row->adesivo != 1){
        echo '<div class="error">Este item não é um adesivo</div>';
        $inventario->getSlotsAdesivos($idPersonagem);
        exit;
    }
    
    // ✅ Check max 10 adesivos limit
    $sql_count = "SELECT COUNT(*) FROM personagens_itens_equipados 
                  WHERE idPersonagem = ? AND adesivo = 1 AND vazio = 0"; 
    $stmt_count = DB::prepare($sql_count);
    $stmt_count->execute([$idPersonagem]);
    $totalEquipped = $stmt_count->fetchColumn();

    if($totalEquipped >= 10){
        echo '<div id="limit-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 999999; display: flex; justify-content: center; align-items: center;">
            <div style="background: #1a1a1a; border: 2px solid #f44336; padding: 30px; border-radius: 10px; text-align: center; color: white; box-shadow: 0 0 30px rgba(244,67,54,0.3); font-family: Arial, sans-serif;">
                <i class="fas fa-hand-paper" style="font-size: 50px; color: #f44336; margin-bottom: 15px;"></i>
                <h2 style="color: #f44336; margin: 0 0 10px; text-transform: uppercase;">LIMITE ATINGIDO</h2>
                <p style="font-size: 16px; color: #ddd; margin-bottom: 20px;">Você não pode equipar mais de 10 adesivos!</p>
                <button onclick="window.location.reload();" style="background: #f44336; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">ENTENDI</button>
            </div>
        </div>';
        exit;
    }
    
    // ✅ Find empty adesivo slot
    $sql_slot = "SELECT * FROM personagens_itens_equipados 
                WHERE idPersonagem = ? 
                AND adesivo = 1 
                AND vazio = 1 
                ORDER BY slot ASC
                LIMIT 1";
    $stmt_slot = DB::prepare($sql_slot);
    $stmt_slot->execute([$idPersonagem]);
    $slotVazio = $stmt_slot->fetch();
    
    if(!$slotVazio){
        echo '<div class="error">Não há slots de adesivo disponíveis</div>';
        $inventario->getSlotsAdesivos($idPersonagem);
        exit;
    }
    
    // ✅ Equip adesivo to slot
    $sql_equip = "UPDATE personagens_itens_equipados 
                SET idItem = ?, vazio = 0 
                WHERE id = ?";
    $stmt_equip = DB::prepare($sql_equip);
    $stmt_equip->execute([$row->itemId, $slotVazio->id]);
    
    // ✅ Remove from inventory using INVENTORY ID (not item ID)
    $sql_delete = "DELETE FROM personagens_inventario_itens WHERE id = ?";
    $stmt_delete = DB::prepare($sql_delete);
    $stmt_delete->execute([$idInventario]);
    
    // ✅ Auto-organize inventory
    $inventario->organizarInventario($idPersonagem);
    
    // ✅ Display updated adesivos
    $inventario->getSlotsAdesivos($idPersonagem);
    
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsAdesivos($idPersonagem);
}
?>
