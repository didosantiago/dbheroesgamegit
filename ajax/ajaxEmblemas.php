<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";

$inventario = new Inventario();

// Validate session
if(!isset($_SESSION['PERSONAGEMID'])){
    echo '<div class="error">Sessão inválida</div>';
    $inventario->getSlotsEmblemas($_SESSION['PERSONAGEMID'] ?? 0);
    exit;
}

$idPersonagem = (int)$_SESSION['PERSONAGEMID'];
$idSlot = isset($_POST['idSlot']) ? (int)$_POST['idSlot'] : 0;

if($idSlot == 0){
    echo '<div class="error">Slot inválido</div>';
    $inventario->getSlotsEmblemas($idPersonagem);
    exit;
}

try {
    // Unequip emblema from slot
    $result = $inventario->desequiparEmblema($idSlot, $idPersonagem);
    
    if($result){
        $inventario->getSlotsEmblemas($idPersonagem);
    } else {
        echo '<div class="error">Inventário cheio ou slot vazio</div>';
        $inventario->getSlotsEmblemas($idPersonagem);
    }
    
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsEmblemas($idPersonagem);
}
?>
