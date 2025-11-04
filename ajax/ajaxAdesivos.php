<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Inventario.php";
    
    $core = new Core();
    $inventario = new Inventario();
    
    $inventario->atualizaAdesivos(addslashes($_POST['idPersonagem']), addslashes($_POST['id']), addslashes($_POST['idItem']));
?>