<?php
    require_once "../core/config.php";
    
    require_once "../core/Core.php";
    require_once "../core/Inventario.php";
    require_once "../core/Personagens.php";
    
    $core = new Core();
    $inventario = new Inventario();
    $personagem = new Personagens();

    $inventario->equiparAdesivos(addslashes($_POST['idPersonagem']), addslashes($_POST['id']), addslashes($_POST['idp']));
?>