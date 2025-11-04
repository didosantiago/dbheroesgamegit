<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Personagens.php";
    
    $core = new Core();
    $personagem = new Personagens();
    
    $personagem->contadorPunicao(addslashes($_POST['id']));
?>