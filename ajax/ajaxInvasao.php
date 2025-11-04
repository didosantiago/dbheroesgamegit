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
    require_once "../core/Invasao.php";
    
    $core = new Core();
    $treino = new Treino();
    $batalha = new Batalha();
    $inventario = new Inventario();
    $personagem = new Personagens();
    $invasao = new Invasao();
    
    $idPersonagem = addslashes($_POST['idPersonagem']);
    $idBatalha = addslashes($_POST['idBatalha']);
    $idInvasor = addslashes($_POST['idInvasor']);
    $idGolpe = addslashes($_POST['idGolpe']);
    
    $invasao->atacar($idPersonagem, $idInvasor, $idGolpe, $idBatalha);
?>