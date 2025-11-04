<?php
    session_start();
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Usuarios.php";
    require_once "../core/Pagamentos.php";
    
    $core = new Core();
    $pay = new Pagamentos();
    
    $cambio = 1;
    $coins = $cambio * addslashes($_POST['valor']);
    
    $campos = array(
        'idPersonagem' => addslashes($_POST['idPersonagem']),
        'idUsuario' => addslashes($_POST['idUsuario']),
        'valor' => addslashes($_POST['valor']),
        'data' => date('Y-m-d H:i:s'),
        'status' => 1,
        'modulo' => 'Doação',
        'coins' => $coins
    );

    $core->insert('transacoes', $campos);
    
    $pay->setPayment('Doação', addslashes($_POST['idUsuario']), addslashes($_POST['idPersonagem']));
?>