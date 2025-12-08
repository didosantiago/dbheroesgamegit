<?php
// =================================================================
// 1. INITIAL VALIDATION
// =================================================================
if (!isset($_SESSION['PERSONAGEMID'])) {
    header('Location: ' . BASE . 'portal');
    exit;
}
if (!$personagem->existsGuerreiro($user->id)) {
    header('Location: ' . BASE . 'criar-personagem');
    exit;
}

$idInventory = (int)Url::getURL(1); // e.g., 535
$idPersonagem = $_SESSION['PERSONAGEMID'];

// =================================================================
// 2. VALIDATE OWNERSHIP & GET ITEM ID
// =================================================================
$sql_check = "SELECT idItem FROM personagens_inventario_itens WHERE id = ? AND idPersonagem = ? LIMIT 1";
$stmt_check = DB::prepare($sql_check);
$stmt_check->execute([$idInventory, $idPersonagem]);
$idItemType = $stmt_check->fetchColumn(); // Should be 13

if (!$idItemType) {
    header('Location: ' . BASE . 'inventario');
    exit;
}

// =================================================================
// 3. GET CHEST DATA
// =================================================================
$sql_dados = "SELECT * FROM itens WHERE id = ?";
$stmt_dados = DB::prepare($sql_dados);
$stmt_dados->execute([$idItemType]);
$dados = $stmt_dados->fetchObject();

if (!$dados) {
    die("Erro: Item nÃ£o encontrado no banco de dados.");
}

// =================================================================
// 4. MODAL MESSAGE VARIABLE
// =================================================================
$modalMessage = '';

// =================================================================
// 5. PROCESS UNLOCK
// =================================================================
if (isset($_POST['destrancar'])) {
    
    // Get random loot
    $sorteio = $inventario->getSorteio();
    $item_recebido = $inventario->getItemSorteado($dados->id, $sorteio);

    if (!$item_recebido || !isset($item_recebido->id)) {
        $modalMessage = "
        <div class='modal-overlay'>
            <div class='modal-box' style='border-color: #f44336;'>
                <div class='modal-icon' style='color: #f44336;'><i class='fas fa-exclamation-triangle'></i></div>
                <div class='modal-title' style='color: #f44336;'>Erro!</div>
                <div class='modal-text'>Falha ao gerar item. Contate o suporte.</div>
                <button class='modal-btn' style='background:#f44336;' onclick=\"window.location.href='" . BASE . "inventario'\">OK</button>
            </div>
        </div>";
    } else {
        // Check for existing stack
        $slot_recebido = $inventario->verificaItemIgual($item_recebido->nome, $idPersonagem);
        
        // If no stack, find first empty slot
        if (!$slot_recebido) {
            $sql_slot = "SELECT id FROM personagens_inventario WHERE idPersonagem = ? AND (idItem = 0 OR idItem IS NULL) ORDER BY slot ASC LIMIT 1";
            $stmt_slot = DB::prepare($sql_slot);
            $stmt_slot->execute([$idPersonagem]);
            if ($stmt_slot->rowCount() > 0) {
                $slot_recebido = $stmt_slot->fetch()->id;
            }
        }

        if ($slot_recebido) {
            // Add new item to inventory
            $core->insert('personagens_inventario_itens', [
                'idItem'       => $item_recebido->id,
                'idSlot'       => $slot_recebido,
                'idPersonagem' => $idPersonagem
            ]);

            // Mark slot as new
            $core->update('personagens_inventario', ['novo' => 1], "id = '$slot_recebido'");

            // Delete the chest instance
            $core->delete('personagens_inventario_itens', "id = '$idInventory'");

            // SUCCESS MODAL
            $itemName = htmlspecialchars($item_recebido->nome);
            $chestName = htmlspecialchars($dados->nome);
            
            // Assuming your images are in 'assets/itens/'
            $itemImage = BASE . 'assets/itens/' . $item_recebido->imagem; 
            
            $modalMessage = "
            <div class='modal-overlay'>
                <div class='modal-box'>
                    <div class='modal-icon'><i class='fas fa-gift'></i></div>
                    <div class='modal-title'>Recompensa Coletada!</div>
                    <div class='modal-text'>
                        VocÃª abriu o <strong>$chestName</strong> e recebeu:<br>
                        
                        <!-- IMAGE ADDED HERE -->
                        <img src='$itemImage' alt='$itemName' style='width: 60px; height: 60px; margin: 15px 0; border: 2px solid #ffb100; border-radius: 5px; padding: 5px; background: #222;'>
                        <br>
                        
                        <span style='font-size:20px; color:#9a90ef; font-weight:bold;'>$itemName</span>
                    </div>
                    <button class='modal-btn' onclick=\"window.location.href='" . BASE . "inventario'\">OK</button>
                </div>
            </div>";

        }
    }
}
?>

<style>
/* Custom Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.modal-box {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    padding: 40px;
    border-radius: 15px;
    border: 3px solid #6129cf;
    box-shadow: 0 0 30px rgba(76 175 157 / 60%);
    text-align: center;
    color: white;
    min-width: 350px;
    animation: fadeIn 0.4s ease;
}
.modal-icon {
    font-size: 60px;
    color: #4CAF50;
    margin-bottom: 20px;
    animation: bounce 0.6s ease;
}
.modal-title {
    font-size: 28px;
    font-weight: bold;
    color: #ffeb00;
    margin-bottom: 15px;
    text-transform: uppercase;
    text-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
}
.modal-text {
    font-size: 16px;
    margin-bottom: 25px;
    color: #ddd;
    line-height: 1.6;
}
.modal-btn {
    background: linear-gradient(135deg, #4c66af 0%, #45a049 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
}
.modal-btn:hover {
    background: linear-gradient(135deg, #518fad 0%, #4CAF50 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.6);
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}


/* OPTION 37: Fusion Style */
.box-inventario .content-inventory .itens .imagem-bau {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-bottom: 25px !important;
    padding: 15px 30px;
    background: #111;
    /* Orange padding like the vest */
    border: 3px solid #ff8c00; 
    border-radius: 15px;
}

.box-inventario .content-inventory .itens .imagem-bau img {
    width: 68px !important;
    margin-right: 15px !important;
    /* Swaying Dance */
    animation: fusionSway 3s infinite ease-in-out;
}

.box-inventario .content-inventory .itens .imagem-bau h2 {
    margin: 0 !important;
    font-size: 20px;
    color: #ff8c00;
    text-transform: uppercase;
    font-weight: bold;
}

@keyframes fusionSway {
    0% { transform: rotate(-5deg) translateX(-5px); }
    50% { transform: rotate(5deg) translateX(5px); }
    100% { transform: rotate(-5deg) translateX(-5px); }
}







</style>

<!-- Display Modal if Set -->
<?php echo $modalMessage; ?>

<!-- ================================================================= -->
<!-- HTML DISPLAY -->
<!-- ================================================================= -->
<div class="box-inventario">
    <div class="border-horizontal-top"></div>
    <div class="border-vertical-left"></div>
    <div class="border-top-left"></div>
    <div class="border-top-right"></div>
    <div class="border-bottom-left"></div>
    <div class="border-bottom-right"></div>
    <div class="border-vertical-right"></div>
    <div class="border-horizontal-bottom"></div>
    
    <div class="content-inventory">
        <div class="itens">
            <div class="imagem-bau">
                <img src="<?php echo BASE . 'assets/itens/' . $dados->imagem; ?>" alt="<?php echo $dados->nome; ?>" />
                <h2>Itens do <?php echo $dados->nome; ?></h2>
            </div>
            <ul>
                <?php $inventario->getItensBau($dados->id); ?>
            </ul>
        </div>
        <div class="separador"></div>

        <form id="abrirBau" method="post" action="">
            <input type="hidden" name="destrancar" value="1" />
            <button type="submit" id="destrancarBau" class="bts-form"><i class="fas fa-key"></i> Destrancar Baú</button>
        </form>
    </div>
</div>