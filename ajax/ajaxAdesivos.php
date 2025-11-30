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
$idSlot = isset($_POST['idSlot']) ? (int)$_POST['idSlot'] : 0;

if($idSlot == 0){
    // Just show slots, don't show error on initial load
    $inventario->getSlotsAdesivos($idPersonagem);
    exit;
}

try {
    // ✅ Unequip adesivo from slot
    $result = $inventario->desequiparAdesivo($idSlot, $idPersonagem);
    
    if($result){
        $inventario->getSlotsAdesivos($idPersonagem);
    } else {
        echo '<div class="error">Erro: Inventário cheio ou slot vazio</div>';
        $inventario->getSlotsAdesivos($idPersonagem);
    }
    
} catch(Exception $e) {
    echo '<div class="error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $inventario->getSlotsAdesivos($idPersonagem);
}
?>
