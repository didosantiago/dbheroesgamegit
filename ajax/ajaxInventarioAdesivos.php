<?php
session_start();

require_once "../core/config.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";
require_once "../core/Personagens.php";

$inventario = new Inventario();

// Load sticker slots (on page load)
if(isset($_POST['loadOnly']) && $_POST['loadOnly'] == 1){
    if(isset($_POST['idPersonagem'])){
        $inventario->getSlotsAdesivos(addslashes($_POST['idPersonagem']));
    }
}
// Equip sticker from inventory
else if(isset($_POST['id']) && isset($_POST['idp'])){
    $inventario->equiparAdesivos(addslashes($_POST['idPersonagem']), addslashes($_POST['id']), addslashes($_POST['idp']));
}
?>
