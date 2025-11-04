<?php
    session_start();
    
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Usuarios.php";
    require_once "../core/Personagens.php";
    require_once "../core/Equipes.php";
    
    $core = new Core();
    $user = new Usuarios();
    $personagem = new Personagens();
    $equipes = new Equipes();
    
    
    if(addslashes($_POST['tipo']) == 'conversar'){
        $idEquipe = addslashes($_POST['idEquipe']);
        $idMembro = addslashes($_POST['idMembro']);
        $dataHora = date('Y-m-d H:i:s');

        $dadosPersonagem = $core->getDados('usuarios_personagens', "WHERE id = ".$idMembro);

        $texto = "<span class='interacao'><strong>".$dadosPersonagem->nome."</strong> ".addslashes($_POST['mensagem'])."</span>";

        $campos = array(
            'idEquipe' => $idEquipe,
            'idMembro' => $idMembro,
            'mensagem' => $texto,
            'data_hora' => $dataHora
        );

        $core->insert('equipes_chat_interacoes', $campos);
    }
    
    if(addslashes($_POST['tipo']) == 'monitora'){
        $idEquipe = addslashes($_POST['idEquipe']);
        
        echo $equipes->getInteracoesChat($idEquipe);
    }
?>