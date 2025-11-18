<?php
session_start();

require_once "../core/config.php";
require_once "../core/Core.php";
require_once "../core/Inventario.php";
require_once "../core/Personagens.php";

if(!isset($_SESSION['PERSONAGEMID'])){
    echo json_encode(['success' => false, 'message' => 'Sessão inválida']);
    exit;
}

$idPersonagem = (int)$_SESSION['PERSONAGEMID'];
$idItem = isset($_POST['idItem']) ? (int)$_POST['idItem'] : 0;

if($idItem == 0){
    echo json_encode(['success' => false, 'message' => 'Item inválido']);
    exit;
}

$inventario = new Inventario();
$result = $inventario->equiparItens($idItem, $idPersonagem);

if($result){
    echo json_encode(['success' => true, 'message' => 'Item equipado/desequipado com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao equipar item']);
}
?>