<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug logging function
function debug_log($message) {
    $logFile = __DIR__ . '/shop_purchase_debug.txt';
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}

debug_log("=== PURCHASE STARTED ===");

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

debug_log("POST DATA: id=$id, valor=$valor, idUsuario=$idUsuario, idPersonagem=$idPersonagem, modulo=$modulo, idItem=$idItem");

// Validate required data
if(!$id || !$valor || !$idUsuario || !$idPersonagem) {
    debug_log("ERROR: Missing required data");
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

debug_log("User found: ID={$user->id}, Coins={$user->coins}");

if(!$user->id) {
    debug_log("ERROR: User not found");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Usuário não encontrado']);
    exit();
}

// Check if user has enough coins
if(intval($user->coins) < intval($valor)) {
    debug_log("ERROR: Not enough coins");
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
    debug_log("ERROR: Product not found");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Produto não encontrado']);
    exit();
}

debug_log("Product found: {$dadosAnuncio->nome}, Module: {$dadosAnuncio->modulo}");

// Deduct coins
$novoSaldoCoins = intval($user->coins) - intval($valor);
$campos = array('coins' => $novoSaldoCoins);
$where = 'id = ?';
$whereParams = array($idUsuario);
$core->update('usuarios', $campos, $where, $whereParams);

debug_log("Coins deducted: Old={$user->coins}, New={$novoSaldoCoins}");

// Module 1: Profile Photo
if($dadosAnuncio->modulo == 1){
    debug_log("Processing Module 1 (Photo)");

    if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$foto."' AND idUsuario = ".$idUsuario)) {
        debug_log("ERROR: Photo already owned");
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
    debug_log("Photo purchased successfully");
}

// Module 2: Name Change  
elseif($dadosAnuncio->modulo == 2) {
    debug_log("Processing Module 2 (Name Change)");

    if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$id."' AND idUsuario = ".$idUsuario)) {
        debug_log("ERROR: Item already owned");
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
    debug_log("Name change purchased successfully");
}

// Module 3: Game Item
elseif($dadosAnuncio->modulo == 3) {
    debug_log("Processing Module 3 (Game Item)");

    require_once "../core/Inventario.php";
    $inventario = new Inventario();

    debug_log("Getting item from itens table with ID: $idItem");
    $dadosItemInventario = $core->getDados('itens', "WHERE id = ".$idItem);

    if(!$dadosItemInventario) {
        debug_log("ERROR: Item not found in itens table");
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Item não encontrado']);
        exit();
    }

    debug_log("Item found in itens table: ID={$dadosItemInventario->id}, Name={$dadosItemInventario->nome}");

    // Check if item already exists in inventory
    $slot_existente = $inventario->verificaItemIgual($dadosItemInventario->nome, $idPersonagem);
    debug_log("verificaItemIgual returned: " . ($slot_existente ? $slot_existente : "false/empty"));

    if($slot_existente){
        debug_log("Item exists - adding to slot: $slot_existente");

        $campos = array('novo' => 1);
        $where = 'id = ?';
        $whereParams = array($slot_existente);
        $result = $core->update('personagens_inventario', $campos, $where, $whereParams);
        debug_log("Update personagens_inventario result: " . ($result ? "success" : "failed"));

        $campos_add = array(
            'idItem' => $dadosItemInventario->id,
            'idSlot' => $slot_existente,
            'idPersonagem' => $idPersonagem
        );
        $result2 = $core->insert('personagens_inventario_itens', $campos_add);
        debug_log("Insert personagens_inventario_itens result: " . ($result2 ? "success" : "failed"));

    } else {
        debug_log("Item doesn't exist - creating new slot");

        $sql = "SELECT * FROM personagens_inventario WHERE idPersonagem = ? AND idItem IS NULL ORDER BY id ASC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute(array($idPersonagem));

        debug_log("Empty slot query: rowCount=" . $stmt->rowCount());

        if($stmt->rowCount() > 0){
            $slot = $stmt->fetch();
            debug_log("Empty slot found: ID={$slot->id}");

            // Update slot with new item
            $campos = array(
                'idItem' => $dadosItemInventario->id,
                'novo' => 1
            );
            $where = 'id = ?';
            $whereParams = array($slot->id);
            $result = $core->update('personagens_inventario', $campos, $where, $whereParams);
            debug_log("Update slot result: " . ($result ? "success" : "failed"));

            // Insert into personagens_inventario_itens
            $campos_add = array(
                'idItem' => $dadosItemInventario->id,
                'idSlot' => $slot->id,
                'idPersonagem' => $idPersonagem
            );
            $result2 = $core->insert('personagens_inventario_itens', $campos_add);
            debug_log("Insert inventory_itens result: " . ($result2 ? "success" : "failed"));

        } else {
            debug_log("ERROR: No empty slots found");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Inventário cheio']);
            exit();
        }
    }

    $log = 'Comprou o item '.$dadosAnuncio->nome.' na Loja de Itens';
    debug_log("Module 3 completed");
}

// Log the purchase
$personagem = new Personagens();
$personagem->setLog($idUsuario, $idPersonagem, $id, $log, $valor);

debug_log("=== PURCHASE SUCCESS ===");

// Return success with updated coin balance
header('Content-Type: application/json');
echo json_encode([
    'success' => true, 
    'message' => 'Compra realizada com sucesso!',
    'novoSaldo' => $novoSaldoCoins
]);
exit();
?>