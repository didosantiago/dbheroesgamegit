<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";

// Check if just loading the display
if(isset($_POST['loadOnly']) && $_POST['loadOnly'] == 1){
    if(isset($_SESSION['PERSONAGEMID'])){
        $inventario = new Inventario();
        $inventario->getSlotsEquipados($_SESSION['PERSONAGEMID']);
    }
    exit;
}

// Handle equip/unequip adesivo
if(!isset($_SESSION['PERSONAGEMID'])){
    echo json_encode(['success' => false, 'message' => 'Sessão inválida']);
    exit;
}

$idPersonagem = (int)$_SESSION['PERSONAGEMID'];
$idItem = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// === Unequip do slot
if($idItem == 0){
    echo json_encode(['success' => false, 'message' => 'Item inválido']);
    exit;
}

try {
    $inventario = new Inventario();
    $core = new Core();
    
    // Get item details
    $sql = "SELECT * FROM itens WHERE id = ?";
    $stmt = DB::prepare($sql);
    $stmt->execute([$idItem]);
    $item = $stmt->fetch();
    
    if(!$item || $item->emblema != 1){
        echo json_encode(['success' => false, 'message' => 'Item não é um emblema']);
        exit;
    }

    // Equip/unequip emblema
    $result = $inventario->equiparEmblema($idItem, $idPersonagem);
    if($result){
        $inventario->getSlotsEmblemas($idPersonagem);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao equipar emblema']);
    }
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}


?>
