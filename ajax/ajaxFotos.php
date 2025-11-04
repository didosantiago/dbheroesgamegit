<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Personagens.php";
    
    $core = new Core();
    $personagem = new Personagens();
    
    $campos = array(
        'foto' => addslashes($_POST['foto'])
    );

    $where = 'id = "'.addslashes($_POST['id']).'"';

    $core->update('usuarios_personagens', $campos, $where);
?>