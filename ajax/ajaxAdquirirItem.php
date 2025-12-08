<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/User.php";
require_once "../core/Personagens.php";

$core = new Core();

$id = $_POST['id'] ?? null;
$valor = $_POST['valor'] ?? null;
$foto = $_POST['foto'] ?? null;
$idUsuario = $_POST['idUsuario'] ?? null;
$idPersonagem = $_POST['idPersonagem'] ?? null;

$user = new User();
$user = $user->getUserInfoByID($idUsuario);

$dadosAnuncio = $core->getDados('adm_loja_itens', "WHERE id = ".$id);

// Deduct coins
$campos = array(
    'coins' => intval($user->coins) - intval($valor)
);
$where = 'id ='.$idUsuario;
$core->update('usuarios', $campos, $where);

// Module 1: Profile Photo
if($dadosAnuncio->modulo == 1){
    $campos_add = array(
        'idUsuario' => $idUsuario,
        'idPersonagem' => $idPersonagem,
        'foto' => $foto,
        'visualizado' => 0
    );
    
    $core->insert('usuarios_personagens_fotos', $campos_add);
    $log = 'Comprou a foto '.$foto.' na Loja de Itens';
}

// Module 2: Name Change  
elseif($dadosAnuncio->modulo == 2) {
    $campos_add = array(
        'idUsuario' => $idUsuario,
        'idProduto' => $id,
        'modulo' => 1
    );
    $core->insert('usuarios_personagens_modulos', $campos_add);
    $log = 'Comprou o item '.$dadosAnuncio->nome.' na Loja de Itens';
}

// Module 3: Game Item
elseif($dadosAnuncio->modulo == 3) {
    require_once "../core/Inventario.php";
    $inventario = new Inventario();
    
    $dadosItemInventario = $core->getDados('itens', "WHERE id = ".$_POST['idItem']);
    
    if($inventario->verificaItemIgual($dadosItemInventario->nome, $idPersonagem)){
        $slot_recebido = $inventario->verificaItemIgual($dadosItemInventario->nome, $idPersonagem);
        
        $campos = array('novo' => 1);
        $where = 'id = "'.$slot_recebido.'"';
        $core->update('personagens_inventario', $campos, $where);
        
        $campos_add = array(
            'idItem' => $dadosItemInventario->id,
            'idSlot' => $slot_recebido,
            'idPersonagem' => $idPersonagem
        );
        $core->insert('personagens_inventario_itens', $campos_add);
    }
    $log = 'Comprou o item '.$dadosAnuncio->nome.' na Loja de Itens';
}

// Log the purchase
$personagem = new Personagens();
$personagem->setLog($idUsuario, $idPersonagem, $id, $log, $valor);

// ✅ RETURN JSON INSTEAD OF REDIRECT!
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Compra realizada com sucesso!']);
exit();
?>
