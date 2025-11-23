<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";

$inventario = new Inventario();

// Load only (for initial page load)
if(isset($_POST['loadOnly']) && $_POST['loadOnly'] == 1){
    if(isset($_SESSION['PERSONAGEMID'])){
        $inventario->getSlotsEquipados($_SESSION['PERSONAGEMID']);
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
    echo '<div class="error">Item inválido</div>';
    $inventario->getSlotsEquipados($idPersonagem);
    exit;
}

try {
    // ✅ FIX: Get the ITEM ID from inventory row
    $sql = "SELECT pii.idItem, i.emblema, i.adesivo
            FROM personagens_inventario_itens as pii
            INNER JOIN itens as i ON i.id = pii.idItem
            WHERE pii.id = ? AND pii.idPersonagem = ?";
    $stmt = DB::prepare($sql);
    $stmt->execute([$idInventario, $idPersonagem]);
    $row = $stmt->fetch();
    
    if(!$row){
        echo '<div class="error">Item não encontrado no inventário</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }
    
    // ✅ Check it's equipment (not emblema or adesivo)
    if($row->emblema == 1){
        echo '<div class="error">Este é um emblema, use a seção de emblemas</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }
    
    if($row->adesivo == 1){
        echo '<div class="error">Este é um adesivo, use a seção de adesivos</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }
    
    // ✅ Call equiparEquipados with INVENTORY ROW ID
    $result = $inventario->equiparEquipados($idInventario, $idPersonagem);
    
    if($result){
        $inventario->getSlotsEquipados($idPersonagem);
    } else {
        echo '<div class="error">Sem slot de equipamento disponível</div>';
        $inventario->getSlotsEquipados($idPersonagem);
    }
    
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsEquipados($idPersonagem);
}
?>
