<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";
require_once "../core/Personagens.php";


$inventario = new Inventario();


// Load display only (for page load)
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
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$idItem = isset($_POST['idItem']) ? (int)$_POST['idItem'] : 0;
$idInventario = isset($_POST['idInventario']) ? (int)$_POST['idInventario'] : 0;

// ============================================
// UNEQUIP: both id and idItem are provided
// ============================================
if($id > 0 && $idItem > 0){
    try {
        $result = $inventario->desequiparItem($id, $idPersonagem);

        if($result){
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

// ============================================
// EQUIP: always requires idInventario (instance) from JS!
// ============================================
if($id == 0 || $idInventario == 0){

    echo '<div class="error">Item inválido</div>';
    $inventario->getSlotsEquipados($idPersonagem);
    exit;
}

try {
    // Get item details
    $sql = "SELECT * FROM itens WHERE id = ?";
    $stmt = DB::prepare($sql);
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if(!$item){
        echo '<div class="error">Item não encontrado</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }

    $consumable_types = ['consumivel', 'capsula', 'comida', 'restauracao'];

    if(in_array(strtolower($item->tipo), $consumable_types)){
        $result = $inventario->equiparItens($id, $idPersonagem, $idInventario);
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }

    if(isset($item->adesivo) && $item->adesivo == 1){
        echo '<div class="error">Use a seção de Adesivos</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }

    $result = $inventario->equiparItens($id, $idPersonagem, $idInventario);

    if($result){
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
