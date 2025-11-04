<?php
    session_start();
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Usuarios.php";
    require_once "../core/Pagamentos.php";
    
    $core = new Core();
    $pay = new Pagamentos();

    $retorno = addslashes($_POST['retorno']);
    $transaction_id = trim(addslashes($_POST['transaction_id']));
    
    if($retorno == 'abort'){
        $campos = array(
            'status' => 7
        );

        $where = 'transaction_id = "'.$transaction_id.'"';

        $core->update('transacoes', $campos, $where);
    } else {
        $campos = array(
            'pre_order' => 0
        );

        $where = 'transaction_id = "'.$transaction_id.'"';

        $core->update('transacoes', $campos, $where);
    }
?>