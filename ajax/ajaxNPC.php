<?php
    require_once "../core/config.php";
    require_once "../core/DB.php";
    require_once "../core/Core.php";
    require_once "../core/Personagens.php";
    require_once "../core/Npc.php";
    
    $core = new Core();
    $personagem = new Personagens();
    $npc = new Npc();
    
    // ✅ BUG #6 FIX: Handle pause action with proper timestamp
    if(isset($_POST['action']) && $_POST['action'] == 'pause' && isset($_POST['npc_id'])){
        $campos = array(
            'pausado' => 1,
            'time_pausado' => time()  // ✅ FIXED: Save CURRENT time (not +300)
        );
        
        $where = 'id = ?';
        $whereParams = array(intval($_POST['npc_id']));
        $core->update('npc', $campos, $where, $whereParams);
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    // ✅ Regular timer countdown
    $resultado = $npc->contadorNPC(addslashes($_POST['id']));
    
    // ✅ CRITICAL DEBUG: Log the value being returned
    error_log("contadorNPC returned: " . $resultado);
    
    // ✅ Return the timer value
    echo $resultado;
?>
