<?php
session_start();

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";

$inventario = new Inventario();

// Validate session
if(!isset($_SESSION['PERSONAGEMID'])){
    $inventario->getSlotsEquipados(0);
    exit;
}

$idPersonagem = (int)$_SESSION['PERSONAGEMID'];
$idSlot = isset($_POST['idSlot']) ? (int)$_POST['idSlot'] : 0;

if($idSlot == 0){
    $inventario->getSlotsEquipados($idPersonagem);
    exit;
}

try {
    // Unequip equipment
    $result = $inventario->desequiparEquipados($idSlot, $idPersonagem);
    
    // Always show updated slots (even if unequip failed)
    $inventario->getSlotsEquipados($idPersonagem);
    
} catch(Exception $e) {
    error_log("Unequip error: " . $e->getMessage());
    $inventario->getSlotsEquipados($idPersonagem);
}
?>