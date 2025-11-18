<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Personagens.php";
    require_once "../core/Npc.php";
    
    $core = new Core();
    $personagem = new Personagens();
    $npc = new Npc();
    
    $npc->contadorNPC(addslashes($_POST['id']));

    // Handle pause action
    if(isset($_POST['action']) && $_POST['action'] == 'pause' && isset($_POST['npc_id'])){
        $core = new Core();
        
        $campos = array(
            'pausado' => 1,
            'time_pausado' => time() + 300  // 5 minutes to return
        );
        
        $where = 'id = "'.intval($_POST['npc_id']).'"';
        $core->update('npc', $campos, $where);
        exit;
    }

?>