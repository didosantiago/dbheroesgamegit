<?php
    session_start();
    
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Usuarios.php";
    require_once "../core/Personagens.php";
    require_once "../core/Chat.php";
    
    $core = new Core();
    $user = new Usuarios();
    $personagem = new Personagens();
    $chat = new Chat();
    
    
    if(addslashes($_POST['tipo']) == 'conversar'){
        if(!empty(addslashes($_POST['mensagem']))){
            $idPersonagem = addslashes($_POST['idPersonagem']);
            $idAmigo = addslashes($_POST['idAmigo']);
            $dataHora = date('Y-m-d H:i:s');

            $dadosPersonagem = $core->getDados('usuarios_personagens', "WHERE id = ".$idPersonagem);

            $texto = "<span class='interacao'>".addslashes($_POST['mensagem'])."</span>";

            $campos = array(
                'idPersonagem' => $idPersonagem,
                'idAmigo' => $idAmigo,
                'mensagem' => $texto,
                'data' => $dataHora
            );

            $core->insert('adm_chat', $campos);
        }
    }
    
    if(addslashes($_POST['tipo']) == 'monitora'){
        $idPersonagem = addslashes($_POST['idPersonagem']);
        $idAmigo = addslashes($_POST['idAmigo']);
        
        $chat->getChat($idPersonagem, $idAmigo);
    }
    
    if(addslashes($_POST['tipo']) == 'ler'){
        $idPersonagem = addslashes($_POST['idPersonagem']);
        $idAmigo = addslashes($_POST['idAmigo']);
        
        $chat->getLerMensagens($idPersonagem, $idAmigo);
    }
?>