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
    require_once "../core/Npc.php";
    require_once "../core/Inventario.php";
    
    $core = new Core();
    $treino = new Treino();
    $batalha = new Batalha();
    $personagem = new Personagens();
    $inventario = new Inventario();
    $npc = new Npc();
    
    if(addslashes($_POST['finalizado']) == 0){
        if(addslashes($_POST['round']) == 1){
            $npc->atack(addslashes($_POST['id']), addslashes($_POST['idPersonagem']), addslashes($_POST['idGuerreiro']), 1);
        } else if($_POST['round'] == 0) {
            $npc->atack(addslashes($_POST['id']), addslashes($_POST['idGuerreiro']), addslashes($_POST['idPersonagem']), 0);
        }
    }
    
    ob_end_flush();
?>