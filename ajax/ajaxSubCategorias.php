<?php
    ob_start();
    session_start();
    
    //Inclusão das Classes
    include_once "../core/config.php";
    include_once "../core/DB.php";
    include_once "../core/Url.php";
    include_once "../core/Core.php";
    include_once "../core/Forum.php";
    
    //Instanciando Objetos
    $core = new Core();
    $forum = new Forum();
    
    $forum->getOptionsSubCategorias(addslashes($_POST['id']));
    
    ob_end_flush();
?>