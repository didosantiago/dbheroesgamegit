<?php
session_start();
require_once('../core/config.php');
require_once('../core/DB.php');
require_once('../core/Core.php');
require_once('../core/Inventario.php');
require_once('../core/Personagens.php');

$inventario = new Inventario();

// Get idPersonagem from POST or SESSION
$idPersonagem = 0;
if(isset($_POST['idPersonagem']) && $_POST['idPersonagem'] > 0) {
    $idPersonagem = (int)$_POST['idPersonagem'];
} elseif(isset($_SESSION['PERSONAGEM_ID'])) {
    $idPersonagem = (int)$_SESSION['PERSONAGEM_ID'];
}

// Load display only for page load
if(isset($_POST['loadOnly']) && $_POST['loadOnly'] == 1) {
    if($idPersonagem > 0) {
        $inventario->getSlotsEquipados($idPersonagem);
    }
    exit;
}

// Validate that we have a character ID
if($idPersonagem <= 0) {
    echo '<div class="error">Sessão inválida</div>';
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0; // This is the idItem
$idInventario = isset($_POST['idInventario']) ? (int)$_POST['idInventario'] : 0;
$idItem = isset($_POST['idItem']) ? (int)$_POST['idItem'] : 0;

// UNEQUIP: both id and idItem are provided
if($id > 0 && $idItem > 0) {
    try {
        $result = $inventario->desequiparItem($id, $idPersonagem);
        if($result) {
            $inventario->getSlotsEquipados($idPersonagem);
        } else {
            echo '<div class="error">Erro: Inventário cheio</div>';
            $inventario->getSlotsEquipados($idPersonagem);
        }
    } catch(Exception $e) {
        echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
        $inventario->getSlotsEquipados($idPersonagem);
    }
    exit;
}

// EQUIP: only id is provided
if($id <= 0) {
    echo '<div class="error">Item inválido</div>';
    $inventario->getSlotsEquipados($idPersonagem);
    exit;
}

try {
    // Get item details - INCLUDE emblema column!
    $sql = "SELECT *, COALESCE(emblema, 0) as emblema FROM itens WHERE id = ?";
    $stmt = DB::prepare($sql);
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    
    if(!$item) {
        echo '<div class="error">Item não encontrado</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }
    
    // Define item type categories
    $consumable_types = ['consumivel', 'capsula', 'comida', 'restauracao'];
    
    // Handle consumables (use immediately)
    if(in_array(strtolower($item->tipo), $consumable_types)) {
        $result = $inventario->equiparItens($id, $idPersonagem, $idInventario);
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }
    
    // Handle adesivos (should use adesivos section)
    if(isset($item->adesivo) && $item->adesivo == 1) {
        echo '<div class="error">Use a seção de Adesivos</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }
    
    // Handle equipment (chapéu, equipamento, emblema)
    // This includes EMBLEMS - they will be detected by equiparItens
    $result = $inventario->equiparItens($id, $idPersonagem, $idInventario);
    
    if($result) {
        $inventario->getSlotsEquipados($idPersonagem);
    } else {
        echo '<div class="error">Sem slot disponível</div>';
        $inventario->getSlotsEquipados($idPersonagem);
    }
    
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsEquipados($idPersonagem);
}
?>
