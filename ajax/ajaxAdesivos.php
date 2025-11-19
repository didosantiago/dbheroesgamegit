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
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;          // slot ID (personagens_itens_equipados.id)
$idItem = isset($_POST['idItem']) ? (int)$_POST['idItem'] : 0;

// ======= CASE: Unequip adesivo =======
if($id > 0 && $idItem > 0){
    try {
        $result = $inventario->desequiparAdesivo($id, $idPersonagem);
        if($result){
            $inventario->getSlotsAdesivos($idPersonagem);
        } else {
            echo '<div class="error">Erro: Inventário cheio</div>';
            $inventario->getSlotsAdesivos($idPersonagem);
        }
    } catch(Exception $e) {
        echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
        $inventario->getSlotsAdesivos($idPersonagem);
    }
    exit;
}

// ======= CASE: Equip adesivo from inventory =======
if($id == 0){
    echo '<div class="error">Item inválido</div>';
    $inventario->getSlotsAdesivos($idPersonagem);
    exit;
}

try {
    $sql = "SELECT * FROM itens WHERE id = ? AND adesivo = 1";
    $stmt = DB::prepare($sql);
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if(!$item){
        echo '<div class="error">Item não é adesivo</div>';
        $inventario->getSlotsAdesivos($idPersonagem);
        exit;
    }
    $result = $inventario->equiparAdesivo($id, $idPersonagem);
    if($result){
        $inventario->getSlotsAdesivos($idPersonagem);
    } else {
        echo '<div class="error">Sem slot disponível para adesivos</div>';
        $inventario->getSlotsAdesivos($idPersonagem);
    }
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsAdesivos($idPersonagem);
}
?>
