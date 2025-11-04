<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Missoes.php";
    
    $core = new Core();
    $missoes = new Missoes();
    
    $missoes->contadorMissao(addslashes($_POST['id']));
?>