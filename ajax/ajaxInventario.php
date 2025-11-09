<?php
    session_start();
    
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Inventario.php";
    
    $core = new Core();
    $inventario = new Inventario();
    
    if(isset($_SESSION['PERSONAGEMID'])){
        $inventario->getSlots($_SESSION['PERSONAGEMID']);
    } else if(isset($_POST['idPersonagem'])){
        $inventario->getSlots(addslashes($_POST['idPersonagem']));
    } else {
        echo '<div class="error">Erro: Personagem não encontrado</div>';
    }
?>
