<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Missoes.php";
    
    $core = new Core();
    $missoes = new Missoes();
    
    $campos = array(
        'cancelada' => 1
    );

    $where = 'id = "'.addslashes($_POST['id']).'"';

    $core->update('personagens_missoes', $campos, $where);
?>