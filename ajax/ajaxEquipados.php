<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";

$inventario = new Inventario();

// Handle load only/display only
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

// === Desequipar equipamento
if($id > 0 && $idItem > 0){
    try {
        $result = $inventario->desequiparEquipamento($id, $idPersonagem);
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

// ======= CASE: Equip equipamento from inventory =======
if($id == 0){
    echo '<div class="error">Item inválido</div>';
    $inventario->getSlotsEquipados($idPersonagem);
    exit;
}

try {
    $sql = "SELECT * FROM itens WHERE id = ? AND emblema = 1";
    $stmt = DB::prepare($sql);
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if(!$item){
        echo '<div class="error">Item não é equipamento</div>';
        $inventario->getSlotsEquipados($idPersonagem);
        exit;
    }
    $result = $inventario->equiparEquipamento($id, $idPersonagem);
    if($result){
        $inventario->getSlotsEquipados($idPersonagem);
    } else {
        echo '<div class="error">Sem slot disponível para equipamentos</div>';
        $inventario->getSlotsEquipados($idPersonagem);
    }
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsEquipados($idPersonagem);
}
?>
