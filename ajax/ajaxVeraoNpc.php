<?php
require_once '../init.php';

// ✅ CRITICAL FIX: Create VeraoNpc object, not Npc
$veraonpc = new VeraoNpc();
$core = new Core();

// ✅ Handle pause action
if(isset($_POST['action']) && $_POST['action'] == 'pause') {
    $campos = array(
        'pausado' => 1,
        'time_pausado' => time() // ✅ FIXED: Save CURRENT time (not +300)
    );
    
    $where = 'id = ?';
    $whereParams = array(intval($_POST['verao_npc_id']));
    
    $core->update('verao_batalhas', $campos, $where, $whereParams);
    
    echo json_encode(['success' => true]);
    exit;
}

// ✅ Regular timer countdown
$resultado = $veraonpc->contadorNPC(addslashes($_POST['id']));

// ✅ CRITICAL DEBUG: Log the value being returned
error_log("contadorNPC returned: " . $resultado);

// ✅ Return the timer value
echo $resultado;
?>
