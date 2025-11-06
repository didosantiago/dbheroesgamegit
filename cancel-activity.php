<?php
session_start();
require 'init.php';

// Check if user is logged in
if(!isset($_SESSION['user_logado']) || $_SESSION['user_logado'] !== true){
    header('Location: '.BASE.'home');
    exit;
}

// Check if user has an active mission or hunt
$activityCanceled = false;
$redirectTo = BASE.'portal';

// Cancel active mission
if(isset($_SESSION['missao']) && isset($_SESSION['PERSONAGEMID'])) {
    $sql = "UPDATE missoes SET status = 'cancelada', data_conclusao = NOW() 
            WHERE idPersonagem = " . intval($_SESSION['PERSONAGEMID']) . " 
            AND status = 'ativa'";
    
    $stmt = DB::prepare($sql);
    if($stmt->execute()) {
        unset($_SESSION['missao']);
        unset($_SESSION['missao_id']);
        $activityCanceled = true;
        $redirectTo = BASE.'missoes'; // Redirect to missions page
        $core->msg('sucesso', 'Missão cancelada com sucesso!');
    }
}

// Cancel active hunt (cacada)
if(isset($_SESSION['cacada']) && isset($_SESSION['PERSONAGEMID'])) {
    $sql = "UPDATE cacadas SET cancelada = 1 
            WHERE idPersonagem = " . intval($_SESSION['PERSONAGEMID']) . " 
            AND concluida = 0 AND cancelada = 0";
    
    $stmt = DB::prepare($sql);
    if($stmt->execute()) {
        unset($_SESSION['cacada']);
        unset($_SESSION['cacada_id']);
        $activityCanceled = true;
        $redirectTo = BASE.'cacadas'; // Redirect to hunts page
        $core->msg('sucesso', 'Caçada cancelada com sucesso!');
    }
}

if(!$activityCanceled) {
    $core->msg('error', 'Nenhuma atividade ativa para cancelar.');
}

// Redirect to appropriate page
header('Location: ' . $redirectTo);
exit;
?>
