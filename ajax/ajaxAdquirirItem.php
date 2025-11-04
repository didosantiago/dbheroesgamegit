<?php
    session_start();
    
    require_once "../core/config.php";
    require_once "../core/Core.php";
    require_once "../core/Usuarios.php";
    require_once "../core/Loja.php";
    require_once "../core/Personagens.php";
    require_once "../core/Inventario.php";
    
    $core = new Core();
    $user = new Usuarios();
    $personagem = new Personagens();
    $loja = new Loja();
    $inventario = new Inventario();
    
    $id = addslashes($_POST['id']);
    $valor = addslashes($_POST['valor']);
    $foto = addslashes($_POST['foto']);
    $idPersonagem = addslashes($_POST['idPersonagem']);
    $idUsuario = addslashes($_POST['idUsuario']);
    $idItem = addslashes($_POST['idItem']);
    
    $user->getUserInfoByID($idUsuario);
    
    $dadosAnuncio = $core->getDados('adm_loja_itens', "WHERE id = ".$id);
   
    $campos = array(
        'coins' => intval($user->coins) - intval($valor)
    );

    $where = 'id ='.$idUsuario;
    
    $core->update('usuarios', $campos, $where);

    if($dadosAnuncio->modulo == 1){
        $campos_add= array(
            'idUsuario' => $idUsuario,
            'idPersonagem' => $idPersonagem,
            'foto' => $foto,
            'visualizado' => 0
        );
    
        $core->insert('usuarios_personagens_fotos', $campos_add);

        $log = 'Comprou o item na Loja de Itens';
    } 
    
    if($dadosAnuncio->modulo == 2) {
        $campos_add= array(
            'idUsuario' => $idUsuario,
            'idProduto' => $id,
            'modulo' => 1 
        );
    
        $core->insert('usuarios_personagens_modulos', $campos_add);

        $log = 'Comprou o item '.$dadosAnuncio->nome.' na Loja de Itens';
    }

    if($dadosAnuncio->modulo == 3) {
        $dadosItemInventario = $core->getDados('itens', "WHERE id = ".$idItem);
        
        if($inventario->verificaItemIgual($dadosItemInventario->nome, $idPersonagem)){
            $slot_recebido = $inventario->verificaItemIgual($dadosItemInventario->nome, $idPersonagem);
        }
        
        $campos = array(
            'novo' => 1
        );

        $where = 'id = "'.$slot_recebido.'"';

        $core->update('personagens_inventario', $campos, $where);

        $campos_add = array(
            'idItem' => $dadosItemInventario->id,
            'idSlot' => $slot_recebido,
            'idPersonagem' => $idPersonagem
        );

        $core->insert('personagens_inventario_itens', $campos_add);

        $log = 'Comprou o item '.$dadosAnuncio->nome.' na Loja de Itens';
    }
    
    $personagem->setLog($idUsuario, $idPersonagem, $id, $log, $valor);
?>