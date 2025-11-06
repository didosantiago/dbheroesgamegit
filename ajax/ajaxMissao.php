<?php
session_start();
require '../init.php';

// Get mission countdown time - same as cacadas.php
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $sql = "SELECT * FROM personagens_missoes 
            WHERE idPersonagem = $id 
            AND concluida = 0 
            AND cancelada = 0 
            LIMIT 1";
    
    $stmt = DB::prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $missao = $stmt->fetch();
        
        // Calculate remaining time
        $tempo_atual = time();
        $tempo_restante = $missao->tempo_final - $tempo_atual;
        
        // Return remaining seconds (minimum 0)
        echo max(0, intval($tempo_restante));
    } else {
        // No active mission
        echo 0;
    }
}
?>
