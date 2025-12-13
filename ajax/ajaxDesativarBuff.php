<?php
session_start();
require_once "../core/config.php";
require_once "../core/DB.php";

if(!isset($_SESSION['PERSONAGEMID']) || !isset($_POST['tipo'])){
    exit;
}

$idPersonagem = (int)$_SESSION['PERSONAGEMID'];
$tipo = $_POST['tipo'];

// Deactivate expired buff
$sql = "UPDATE personagens_buffs 
        SET ativo = 0 
        WHERE idPersonagem = ? 
        AND tipo = ? 
        AND tempo_fim < NOW()";
$stmt = DB::prepare($sql);
$stmt->execute([$idPersonagem, $tipo]);

echo json_encode(['success' => true]);
?>
