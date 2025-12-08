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
    
    
    if(isset($_POST['tipo']) && $_POST['tipo'] == 'conversar'){
        if(!empty($_POST['mensagem'])){
            $idPersonagem = (int)$_POST['idPersonagem'];
            $idAmigo = (int)$_POST['idAmigo'];
            $dataHora = date('Y-m-d H:i:s');
            $mensagem = htmlspecialchars($_POST['mensagem'], ENT_QUOTES, 'UTF-8');

            $campos = array(
                'idPersonagem' => $idPersonagem,
                'idAmigo' => $idAmigo,
                'mensagem' => $mensagem,
                'data' => $dataHora
            );

            $core->insert('adm_chat', $campos);
            echo json_encode(['success' => true]);
            exit;
        }
    }
    
    if(isset($_POST['tipo']) && $_POST['tipo'] == 'monitora'){
        $idPersonagem = (int)$_POST['idPersonagem'];
        $idAmigo = (int)$_POST['idAmigo'];
        
        echo $chat->getChat($idPersonagem, $idAmigo);  // <-- ECHO the returned HTML
        exit;
    }
    
    if(isset($_POST['tipo']) && $_POST['tipo'] == 'ler'){
        $idPersonagem = (int)$_POST['idPersonagem'];
        $idAmigo = (int)$_POST['idAmigo'];
        
        $chat->getLerMensagens($idPersonagem, $idAmigo);
        exit;
    }
?>
