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

    // ✅ BUG #6 FIX: Handle pause action with proper timestamp
    if(isset($_POST['action']) && $_POST['action'] == 'pause' && isset($_POST['npc_id'])){
        $core = new Core();
        
        $campos = array(
            'pausado' => 1,
            'time_pausado' => time()  // ✅ FIXED: Save CURRENT time (not +300)
        );
        
        $where = 'id = ?';
        $whereParams = array(intval($_POST['npc_id']));
        $core->update('npc', $campos, $where, $whereParams);
        
        exit;
    }
?>
