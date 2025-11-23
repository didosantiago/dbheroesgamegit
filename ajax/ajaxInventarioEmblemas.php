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
        $inventario->getSlotsEmblemas($_SESSION['PERSONAGEMID']);
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
    $inventario->getSlotsEmblemas($idPersonagem);
    exit;
}

try {
    // ✅ FIX: Get the ITEM ID from inventory row
    $sql = "SELECT pii.idItem 
            FROM personagens_inventario_itens as pii
            WHERE pii.id = ? AND pii.idPersonagem = ?";
    $stmt = DB::prepare($sql);
    $stmt->execute([$idInventario, $idPersonagem]);
    $row = $stmt->fetch();
    
    if(!$row){
        echo '<div class="error">Item não encontrado no inventário</div>';
        $inventario->getSlotsEmblemas($idPersonagem);
        exit;
    }
    
    $idItem = $row->idItem;
    
    // ✅ Check if it's an emblema
    $sql = "SELECT * FROM itens WHERE id = ? AND emblema = 1";
    $stmt = DB::prepare($sql);
    $stmt->execute([$idItem]);
    $item = $stmt->fetch();
    
    if(!$item){
        echo '<div class="error">Item não é um emblema</div>';
        $inventario->getSlotsEmblemas($idPersonagem);
        exit;
    }
    
    // ✅ Call equiparEmblema with INVENTORY ROW ID (not item ID)
    $result = $inventario->equiparEmblema($idInventario, $idPersonagem);
    
    if($result){
        $inventario->getSlotsEmblemas($idPersonagem);
    } else {
        echo '<div class="error">Sem slot de emblema disponível</div>';
        $inventario->getSlotsEmblemas($idPersonagem);
    }
    
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsEmblemas($idPersonagem);
}
?>
