<?php
    ob_start();
    session_start();
    
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Treino.php";
    require_once "../core/Batalha.php";
    require_once "../core/Personagens.php";
    require_once "../core/Equipes.php";
    require_once "../core/Inventario.php";
    
    $core = new Core();
    $treino = new Treino();
    $batalha = new Batalha();
    $inventario = new Inventario();
    $personagem = new Personagens();
    
    if(addslashes($_POST['finalizado']) == 0){
        if(addslashes($_POST['round']) == 1){
            $batalha->atack(addslashes($_POST['id']), addslashes($_POST['idGuerreiro']), addslashes($_POST['idPersonagem']), 1);
        } else if(addslashes($_POST['round']) == 0) {
            $batalha->atack(addslashes($_POST['id']), addslashes($_POST['idPersonagem']), addslashes($_POST['idGuerreiro']), 0);
        }
    }
    
    ob_end_flush();
?>