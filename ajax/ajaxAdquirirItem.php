<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../core/config.php";
require_once "../core/DB.php";
require_once "../core/Core.php";
require_once "../core/Usuarios.php";
require_once "../core/Personagens.php";

$core = new Core();

// Get POST data
$id = $_POST['id'] ?? null;
$valor = $_POST['valor'] ?? null;
$foto = $_POST['foto'] ?? null;
$idUsuario = $_POST['idUsuario'] ?? null;
$idPersonagem = $_POST['idPersonagem'] ?? null;
$modulo = $_POST['modulo'] ?? null;
$idItem = $_POST['idItem'] ?? null;

// Validate required data
if(!$id || !$valor || !$idUsuario || !$idPersonagem) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'Dados incompletos. Tente novamente.'
    ]);
    exit();
}

// Get user info
$user = new Usuarios();
$user->getUserInfoByID($idUsuario);

if(!$user->id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Usuário não encontrado']);
    exit();
}

// Check if user has enough coins
if(intval($user->coins) < intval($valor)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'Coins insuficientes',
        'coins' => $user->coins,
        'valor' => $valor
    ]);
    exit();
}

// Get product data
$dadosAnuncio = $core->getDados('adm_loja_itens', "WHERE id = ".$id);

if(!$dadosAnuncio) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Produto não encontrado']);
    exit();
}

// ✅ FIX: Deduct coins using correct WHERE format
$novoSaldoCoins = intval($user->coins) - intval($valor);
$campos = array(
    'coins' => $novoSaldoCoins
);
$where = 'id = ?';
$whereParams = array($idUsuario);
$core->update('usuarios', $campos, $where, $whereParams);

// Module 1: Profile Photo
if($dadosAnuncio->modulo == 1){
    // Check if photo already owned
    if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$foto."' AND idUsuario = ".$idUsuario)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Você já possui esta foto']);
        exit();
    }
    
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
    if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$id."' AND idUsuario = ".$idUsuario)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Você já possui este item']);
        exit();
    }
    
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
    
    $dadosItemInventario = $core->getDados('itens', "WHERE id = ".$idItem);
    
    if(!$dadosItemInventario) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Item não encontrado']);
        exit();
    }
    
    if($inventario->verificaItemIgual($dadosItemInventario->nome, $idPersonagem)){
        $slot_recebido = $inventario->verificaItemIgual($dadosItemInventario->nome, $idPersonagem);
        
        $campos = array('novo' => 1);
        $where = 'id = ?';
        $whereParams = array($slot_recebido);
        $core->update('personagens_inventario', $campos, $where, $whereParams);
        
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

// Return success with updated coin balance
header('Content-Type: application/json');
echo json_encode([
    'success' => true, 
    'message' => 'Compra realizada com sucesso!',
    'novoSaldo' => $novoSaldoCoins
]);
exit();
?>
