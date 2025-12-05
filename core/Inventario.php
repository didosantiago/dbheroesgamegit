<?php

/* ... (keep your existing requires/includes here at the top) ... */

class Inventario {

    // ==========================================
    // 1. Initialize Equipped Slots (Emblems/Items)
    // ==========================================
    public function inicializaSlotsEquipados($idPersonagem) {
        $core = new Core();
        
        // Check if slots exist
        $sql = "SELECT * FROM personagens_itens_equipados WHERE idPersonagem = ? AND slot IN (1,2,3,4,5,6,7,8)";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);

        // If not, create them (3 emblems, 5 equipment)
        if($stmt->rowCount() == 0){
            for ($i = 1; $i <= 8; $i++) {
                $emblema = ($i <= 3) ? 1 : 0;
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'emblema' => $emblema,
                    'adesivo' => 0,
                    'vazio' => 1
                );
                $core->insert('personagens_itens_equipados', $campos);
            }
        }
        
        // Refresh slots
        $this->getSlotsEquipados($idPersonagem);
    }

    // ==========================================
    // 2. Get Main Inventory Slots (Bag)
    // ==========================================
    public function getSlots($idPersonagem) {
        $core = new Core();
        
        // 1. Check current slot count
        $sqlCount = "SELECT COUNT(*) as total FROM personagens_inventario WHERE idPersonagem = ?";
        $stmtCount = DB::prepare($sqlCount);
        $stmtCount->execute([$idPersonagem]);
        $count = $stmtCount->fetch();
        $currentSlots = $count->total;

        // 2. Auto-add missing slots (up to 36)
        if ($currentSlots < 36) {
            for ($i = $currentSlots + 1; $i <= 36; $i++) {
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'vazio' => 1
                );
                $core->insert('personagens_inventario', $campos);
            }
        }
        
        // 3. Get inventory data
        // Get inventory data with bau field
        $sql = "SELECT pi.*, pii.id as itemStorageId, pii.idItem, i.nome, i.imagem, i.tipo, i.subtipo, i.raridade, 
                COALESCE(i.adesivo, 0) as adesivo, 
                COALESCE(i.emblema, 0) as emblema,
                COALESCE(i.bau, 0) as bau
                FROM personagens_inventario as pi
                LEFT JOIN personagens_inventario_itens as pii ON pii.idSlot = pi.id
                LEFT JOIN itens as i ON i.id = pii.idItem
                WHERE pi.idPersonagem = ?
                ORDER BY pi.slot ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slots = $stmt->fetchAll();

        // Consumable types definition
        $consumable_types = ['consumivel', 'capsula', 'comida', 'restauracao'];

        // Display loop
        foreach($slots as $slot) {
            if(!empty($slot->idItem) && $slot->idItem != 0) {
                // ✅ FIX: Check database bau column, not name
                $isBau = ($slot->bau == 1 || $slot->subtipo == 'bau');
                $isConsumable = in_array(strtolower($slot->tipo ?? ''), $consumable_types);
                $raridadeclass = 'raridade-' . ($slot->raridade ?? 1);
                
                // Background image logic
                if($isBau) {
                    $bgimage = 'slot-bau.png';
                    $slotclass = 'slot-bau slot-item';
                } else {
                    $bgimage = 'slot-item.png';
                    $slotclass = 'slot-item';
                }
                
                echo '<li class="slots ' . $slotclass . ' ' . $raridadeclass . '" ';
                echo 'data-slot="' . $slot->slot . '" ';
                echo 'data-item="' . $slot->idItem . '" ';
                echo 'data-id="' . $slot->id . '" ';
                echo 'data-idItem="' . $slot->idItem . '" ';
                echo 'data-idinventario="' . $slot->itemStorageId . '" ';
                echo 'data-adesivo="' . (isset($slot->adesivo) ? $slot->adesivo : 0) . '" ';
                echo 'data-emblema="' . (isset($slot->emblema) ? $slot->emblema : 0) . '" ';
                echo 'data-isbau="' . ($isBau ? '1' : '0') . '" '; // ✅ ADD THIS!
                echo 'style="background-image: url(\'' . BASE . 'assets/' . $bgimage . '\'); background-size: cover;">';



                    if($isBau) {
                        echo '<span class="bau">';
                        echo '<a href="' . BASE . 'bau/' . $slot->itemStorageId . '">';
                    } else {
                        echo '<span>';
                    }
                    
                    if (!empty($slot->imagem)) {
                        echo '<img src="' . BASE . 'assets/itens/' . $slot->imagem . '" alt="' . ($slot->nome ?? 'Item') . '" title="' . ($slot->nome ?? 'Item') . '" />';
                    }
                    
                    if($isBau) {
                        echo '</a>';
                    }
                    echo '</span>';
                echo '</li>';
            } else {
                // Empty slot
                echo '<li class="slots slot-vazio" data-slot="' . $slot->slot . '" ';
                echo 'style="background-image: url(\'' . BASE . 'assets/slot-vazio.png\'); background-size: cover;">';
                echo '</li>';
            }
        }

    }


    public function usarConsumivel($idInventario, $idPersonagem) {
        $core = new Core();
        $personagem = new Personagens();
        $personagem->getGuerreiro($idPersonagem);
        
        // Get consumable item from inventory (✅ USE CAMELCASE COLUMNS)
        $sql = "SELECT pii.*, i.* FROM personagens_inventario_itens as pii 
                INNER JOIN itens as i ON i.id = pii.idItem 
                WHERE pii.id = ? AND pii.idPersonagem = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idInventario, $idPersonagem]);
        $item = $stmt->fetch();

        
        if(!$item) {
            return ['success' => false, 'message' => 'Item não encontrado'];
        }
        
        // Check if it's a consumable
        $consumable_types = ['consumivel', 'capsula', 'comida', 'restauracao', 'pocao'];
        if(!in_array(strtolower($item->tipo ?? ''), $consumable_types)) {
            return ['success' => false, 'message' => 'Este item não é consumível'];
        }
        
        // Get current character stats from database (fresh data)
        $sql_char = "SELECT * FROM usuarios_personagens WHERE id = ?";
        $stmt_char = DB::prepare($sql_char);
        $stmt_char->execute([$idPersonagem]);
        $char = $stmt_char->fetch();
        
        if(!$char) {
            return ['success' => false, 'message' => 'Personagem não encontrado'];
        }
        
        $level = $char->nivel;
        $hpatual = $char->hp;
        $kiusado = $char->ki_usado;
        $energiausada = $char->energia_usada ?? 0;
                
        // ✅ CORRECT MAX STATS FORMULAS
        $valor_hp_max = ($level * 50) + 100;              // Level 7: (7*50)+100 = 450
        $valor_ki_max = ($level * 50) + 50;               // Level 7: (7*50)+50 = 400
        $valor_energia_max = 100;
        
        // Initialize new values
        $new_hp = $hpatual;
        $new_kiusado = $kiusado;
        $new_energiausada = $energiausada;
        $capsule_buff = null;
        
        // ✅ HP RESTORATION - 50 in DB = 50% of MAX HP
        if(isset($item->efeito_hp) && $item->efeito_hp > 0) {
            $percentage = $item->efeito_hp;
            $heal_amount = floor($valor_hp_max * ($percentage / 100));
            $new_hp = min($hpatual + $heal_amount, $valor_hp_max);
        }
        
        // ✅ KI RESTORATION - 50 in DB = 50% of MAX KI
        if(isset($item->efeito_ki) && $item->efeito_ki > 0) {
            $percentage = $item->efeito_ki;
            $restore_amount = floor($valor_ki_max * ($percentage / 100));
            $new_kiusado = max($kiusado - $restore_amount, 0);
        }
        
        // ✅ ENERGY RESTORATION
        if(isset($item->efeito_energia) && $item->efeito_energia > 0) {
            $restore_amount = $item->efeito_energia;
            $new_energiausada = max($energiausada - $restore_amount, 0);
        }
        
        // CAPSULE BUFF (EXP boost)
        if(strtolower($item->tipo) == 'capsula') {
            if(isset($item->efeito_experiencia) && $item->efeito_experiencia > 0) {
                $buff_percentage = $item->efeito_experiencia;
                $buff_end_time = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                
                $sql_check = "SELECT * FROM personagens_buffs 
                            WHERE idPersonagem = ? AND tipo = 'experiencia' AND ativo = 1";
                $stmt_check = DB::prepare($sql_check);
                $stmt_check->execute([$idPersonagem]);
                $existing_buff = $stmt_check->fetch();
                
                if($existing_buff) {
                    $sql_update = "UPDATE personagens_buffs 
                                SET porcentagem = ?, tempo_fim = ? 
                                WHERE id = ?";
                    $stmt_update = DB::prepare($sql_update);
                    $stmt_update->execute([$buff_percentage, $buff_end_time, $existing_buff->id]);
                } else {
                    $campos_buff = [
                        'idPersonagem' => $idPersonagem,
                        'tipo' => 'experiencia',
                        'porcentagem' => $buff_percentage,
                        'tempo_inicio' => date('Y-m-d H:i:s'),
                        'tempo_fim' => $buff_end_time,
                        'ativo' => 1
                    ];
                    $core->insert('personagens_buffs', $campos_buff);
                }
                $capsule_buff = $buff_percentage;
            }
        }
        
        // Update character stats
        $sql_update = "UPDATE usuarios_personagens SET hp = ?, ki_usado = ?, energia_usada = ? WHERE id = ?";
        $stmt_update = DB::prepare($sql_update);
        $stmt_update->execute([$new_hp, $new_kiusado, $new_energiausada, $idPersonagem]);
                
        // Remove item from inventory
        $sql_delete = "DELETE FROM personagens_inventario_itens WHERE id = ?";
        $stmt_delete = DB::prepare($sql_delete);
        $stmt_delete->execute([$idInventario]);
        
        // Reorganize inventory
        $this->organizarInventario($idPersonagem);
        
        return [
            'success' => true,
            'message' => 'Item usado: ' . htmlspecialchars($item->nome),
            'hp' => $new_hp,
            'hp_max' => $valor_hp_max,
            'kiusado' => $new_kiusado,
            'ki_max' => $valor_ki_max,
            'energiausada' => $new_energiausada,
            'energia_max' => $valor_energia_max,
            'capsule_buff' => $capsule_buff
        ];
    }



        public function moverItem($idInventory, $toSlot) {
        // 1. Get item details
        // (Your existing code to get $item likely exists here)
        $item = $this->getItemById($idInventory); // Example line

        // =========================================================
        // [START] VALIDATION BLOCK (Adjusted for your table structure)
        // =========================================================
        // =========================================================
        // [START] VALIDATION BLOCK
        // =========================================================
        if ($item && $item->tipo == 'adesivo') {
            // 1. Count items in EQUIPPED table
            $sql = "SELECT COUNT(*) FROM personagens_itens_equipados 
                    WHERE idPersonagem = :id AND adesivo = 1 AND vazio = 0"; 
            $stmt = DB::prepare($sql);
            $stmt->execute([':id' => $_SESSION['PERSONAGEMID']]);
            $total = $stmt->fetchColumn();

            // 2. If limit reached
            // [Inside equiparAdesivo or moverItem]
            if ($totalEquipped >= 10) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    // Return HTML that includes a script to reload page on close
                    echo '
                    <div id="limit-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 999999; display: flex; justify-content: center; align-items: center;">
                        <div style="background: #1a1a1a; border: 2px solid #f44336; padding: 30px; border-radius: 10px; text-align: center; color: white; box-shadow: 0 0 30px rgba(244,67,54,0.3); font-family: Arial, sans-serif;">
                            <i class="fas fa-hand-paper" style="font-size: 50px; color: #f44336; margin-bottom: 15px;"></i>
                            <h2 style="color: #f44336; margin: 0 0 10px; text-transform: uppercase;">LIMITE ATINGIDO</h2>
                            <p style="font-size: 16px; color: #ddd; margin-bottom: 20px;">VocÃª nÃ£o pode equipar mais de 10 adesivos!</p>
                            <button onclick="window.location.reload(true);" style="background: #f44336; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">ENTENDI</button>
                        </div>
                    </div>
                    <script>
                    // Optional: You can force reload immediately if you prefer, but button is better
                    </script>';
                    exit;
                }
                return false;
            }


        }
        // =========================================================
        // [END] VALIDATION BLOCK
        // =========================================================

        // =========================================================
        // [END] VALIDATION BLOCK
        // =========================================================

        // ... (Rest of your existing movement logic) ...
    }


    /**
     * Equip adesivo from inventory to adesivo slot
     */
    /**
     * Equip adesivo from inventory to adesivo slot
     */
    public function equiparAdesivo($idItem, $idPersonagem) {
        $core = new Core();
        
        // 1. Verify item is an adesivo
        $sql = "SELECT * FROM itens WHERE id = ? AND adesivo = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idItem]);
        $item = $stmt->fetch();
        
        if(!$item){
            return false; // Not an adesivo
        }

        // =========================================================
        // [START] MAX 10 STICKERS VALIDATION
        // =========================================================
               // =========================================================
        // [START] VALIDATION BLOCK WITH RELOAD FIX
        // =========================================================
        // Count currently equipped stickers
        $sql_count = "SELECT COUNT(*) FROM personagens_itens_equipados 
                      WHERE idPersonagem = ? AND adesivo = 1 AND vazio = 0"; 
        $stmt_count = DB::prepare($sql_count);
        $stmt_count->execute([$idPersonagem]);
        $totalEquipped = $stmt_count->fetchColumn();

        if ($totalEquipped >= 10) {
            // If request is AJAX (Standard for drag-and-drop)
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // Output the Modal HTML directly. The browser will render this.
                echo '
                <div id="limit-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 999999; display: flex; justify-content: center; align-items: center;">
                    <div style="background: #1a1a1a; border: 2px solid #f44336; padding: 30px; border-radius: 10px; text-align: center; color: white; box-shadow: 0 0 30px rgba(244,67,54,0.3); font-family: Arial, sans-serif;">
                        <i class="fas fa-hand-paper" style="font-size: 50px; color: #f44336; margin-bottom: 15px;"></i>
                        <h2 style="color: #f44336; margin: 0 0 10px; text-transform: uppercase;">LIMITE ATINGIDO</h2>
                        <p style="font-size: 16px; color: #ddd; margin-bottom: 20px;">VocÃª nÃ£o pode equipar mais de 10 adesivos!</p>
                        <button onclick="window.location.reload();" style="background: #f44336; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">ENTENDI</button>
                    </div>
                </div>';
                exit; // Stop execution so "false" is not returned
            }
            return false; // Fallback for non-AJAX
        }
        // =========================================================
        // [END] VALIDATION BLOCK
        // =========================================================

        
        // Get item from inventory
        $sql = "SELECT pii.* 
                FROM personagens_inventario_itens as pii
                WHERE pii.idItem = ? AND pii.idPersonagem = ? 
                LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idItem, $idPersonagem]);
        $itemInventario = $stmt->fetch();
        
        if(!$itemInventario){
            return false; // Item not in inventory
        }
        
        // Find empty adesivo slot
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE idPersonagem = ? 
                AND adesivo = 1 
                AND vazio = 1 
                ORDER BY slot ASC
                LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slotVazio = $stmt->fetch();
        
        if(!$slotVazio){
            // Also show modal if slots are full (redundant check but good UX)
             if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo '
                <div id="limit-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 999999; display: flex; justify-content: center; align-items: center;">
                    <div style="background: #1a1a1a; border: 2px solid #f44336; padding: 30px; border-radius: 10px; text-align: center; color: white; box-shadow: 0 0 30px rgba(244,67,54,0.3);">
                        <h2 style="color: #f44336; margin: 0 0 10px;">SEM ESPAÃ‡O</h2>
                        <p style="font-size: 16px; color: #ddd;">NÃ£o hÃ¡ slots de adesivo vazios!</p>
                        <button onclick="document.getElementById(\'limit-modal\').remove();" style="background: #f44336; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer;">OK</button>
                    </div>
                </div>';
                exit;
            }
            return false;
        }
        
        // Equip adesivo to slot
        $sql = "UPDATE personagens_itens_equipados 
                SET idItem = ?, vazio = 0 
                WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$item->id, $slotVazio->id]);
        
        // Remove from inventory using prepared statement
        $sql = "DELETE FROM personagens_inventario_itens WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$itemInventario->id]);
        
        // AUTO-ORGANIZE INVENTORY
        $this->organizarInventario($idPersonagem);
        
        return true;
    }


    public function getSlotsEquipados($idPersonagem) {
        $core = new Core();

        // Inicializa os slots de equipamentos (slots 4-8) se nÃ£o existirem
        $sql = "SELECT * FROM personagens_itens_equipados WHERE idPersonagem = ? AND emblema = 0 AND adesivo = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);

        if ($stmt->rowCount() < 5) {
            // Sempre 5 slots, slots 4 a 8
            for ($i = 4; $i <= 8; $i++) {
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'emblema' => 0,
                    'adesivo' => 0,
                    'vazio' => 1
                );
                $core->insert('personagens_itens_equipados', $campos);
            }
            // Re-query apÃ³s criar
            $stmt = DB::prepare($sql);
            $stmt->execute([$idPersonagem]);
        }

        // Busca os slots equipamentos
        $sql = "SELECT pie.*, i.nome, i.imagem, i.tipo, i.raridade 
                FROM personagens_itens_equipados as pie 
                LEFT JOIN itens as i ON i.id = pie.idItem 
                WHERE pie.idPersonagem = ? AND pie.emblema = 0 AND pie.adesivo = 0
                ORDER BY pie.slot ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slots = $stmt->fetchAll();

        // Renderiza slots
        foreach ($slots as $slot) {
            if ($slot->idItem && $slot->idItem > 0 && !empty($slot->imagem)) {
                $raridade_class = 'raridade-' . $slot->raridade;
                echo '<li class="slots equipped slot-equipado slot-item has-item ' . $raridade_class . '" data-slot="' . $slot->slot . '" data-item="' . $slot->idItem . '" data-id="' . $slot->id . '" data-idItem="' . $slot->idItem . '">';
                echo '<img src="' . BASE . 'assets/itens/' . $slot->imagem . '" alt="' . $slot->nome . '" title="' . $slot->nome . '">';
                echo '</li>';

            } else {
                echo '<li class="slots equipped slot-equipado slot-vazio" data-slot="' . $slot->slot . '">';
                echo '<img src="' . BASE . 'assets/slot-equipado.png" alt="Slot Equipamento Vazio">';
                echo '</li>';
            }
        }
    }

    /**
     * Unequip adesivo and return to inventory
     */
    public function desequiparAdesivo($idSlotAdesivo, $idPersonagem) {
        $core = new Core();
        
        // Get adesivo info from equipped slot
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE id = ? AND idPersonagem = ? AND adesivo = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idSlotAdesivo, $idPersonagem]);
        $equipado = $stmt->fetch();
        
        if(!$equipado || $equipado->vazio == 1){
            return false; // Slot is empty or not found
        }
        
        $idItem = $equipado->idItem;
        
        // Find empty inventory slot
        $sql = "SELECT pi.* 
                FROM personagens_inventario as pi 
                LEFT JOIN personagens_inventario_itens as pii ON pii.idSlot = pi.id
                WHERE pi.idPersonagem = ? 
                AND pii.id IS NULL
                LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slotVazio = $stmt->fetch();
        
        if(!$slotVazio){
            return false; // Inventory full
        }
        
        // Clear adesivo slot
        $sql = "UPDATE personagens_itens_equipados 
                SET idItem = 0, vazio = 1 
                WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$equipado->id]);
        
        // Add back to inventory
        $sql = "INSERT INTO personagens_inventario_itens (idItem, idSlot, idPersonagem) 
                VALUES (?, ?, ?)";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idItem, $slotVazio->id, $idPersonagem]);
        
        // Auto-organize inventory
        $this->organizarInventario($idPersonagem);
        
        return true;
    }





    // Helper method to render a slot
    private function renderSlot($slot, $isEmblem) {
        $slotClass = $isEmblem ? 'slot-emblema' : 'slot-equipado';
        $emptyImage = $isEmblem ? 'slot-emblema.png' : 'slot-equipado.png';
        
        if(!empty($slot['idItem']) && $slot['idItem'] > 0 && !empty($slot['imagem'])) {
            // Slot with equipped item
            $raridadeclass = isset($slot['raridade']) ? 'raridade-'.$slot['raridade'] : '';
            echo '<div class="slots equipped '.$slotClass.' '.$raridadeclass.' has-item" data-slot="'.$slot['slot'].'" data-item="'.$slot['idItem'].'">';
            echo '<img src="'.BASE.'assets/itens/'.$slot['imagem'].'" alt="'.$slot['nome'].'" title="'.$slot['nome'].'" style="width: 100%; height: 100%; object-fit: contain;" />';
            echo '</div>';
        } else {
            // Empty slot - show slot background image
            echo '<div class="slots equipped '.$slotClass.' slot-vazio" data-slot="'.$slot['slot'].'">';
            echo '<img src="'.BASE.'assets/'.$emptyImage.'" alt="Empty Slot" style="width: 100%; height: 100%; object-fit: contain;" />';
            echo '</div>';
        }
    }



    public function displayAdesivosPerfil($idPersonagem, $slots_array) {
        // Get the adesivos data using your existing method
        $adesivos = $this->getSlotsAdesivosPerfil($idPersonagem);
        
        // Display only the slots in the array
        foreach($slots_array as $slot_number) {
            $found = false;
            
            // Find the slot in the results
            foreach($adesivos as $adesivo) {
                if($adesivo['slot'] == $slot_number) {
                    $found = true;
                    
                    // Check if slot has an item
                    if(!empty($adesivo['idAdesivo']) && $adesivo['idAdesivo'] > 0 && $adesivo['vazio'] == 0) {
                        // Slot has adesivo - get item details
                        $sql = "SELECT * FROM itens WHERE id = ?";
                        $stmt = DB::prepare($sql);
                        $stmt->execute([$adesivo['idAdesivo']]);
                        $item = $stmt->fetch();
                        
                        if($item) {
                            $raridadeClass = 'raridade-'.$item['raridade'];
                            echo '<li class="slots adesivo slot-amarelo has-item '.$raridadeClass.'" data-slot="'.$slot_number.'">';
                            echo '<img src="'.BASE.'assets/itens/'.$item['imagem'].'" alt="'.$item['nome'].'" title="'.$item['nome'].'" />';
                            echo '</li>';
                        } else {
                            // Item not found, show empty
                            echo '<li class="slots adesivo slot-amarelo slot-vazio" data-slot="'.$slot_number.'">';
                            echo '<div class="slot-content"></div>';
                            echo '</li>';
                        }
                    } else {
                        // Empty slot
                        echo '<li class="slots adesivo slot-amarelo slot-vazio" data-slot="'.$slot_number.'">';
                        echo '<div class="slot-content"></div>';
                        echo '</li>';
                    }
                    break;
                }
            }
            
            // If slot doesn't exist in database, show empty
            if(!$found) {
                echo '<li class="slots adesivo slot-amarelo slot-vazio" data-slot="'.$slot_number.'">';
                echo '<div class="slot-content"></div>';
                echo '</li>';
            }
        }
    }


    public function displayAdesivosSlots($idPersonagem, $slots_array) {
        $core = new Core();
        
        // Initialize adesivo slots if they don't exist
        $sql = "SELECT * FROM personagens_itens_equipados WHERE idPersonagem = ? AND adesivo = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        
        if($stmt->rowCount() == 0) {
            // Create 10 adesivo slots
            for($i = 1; $i <= 10; $i++) {
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'emblema' => 0,
                    'adesivo' => 1,
                    'vazio' => 1
                );
                $core->insert('personagens_itens_equipados', $campos);
            }
        }
        
        // Get adesivo items for requested slots only
        $placeholders = implode(',', array_fill(0, count($slots_array), '?'));
        $sql = "SELECT pie.*, i.nome, i.imagem, i.tipo, i.raridade 
                FROM personagens_itens_equipados as pie 
                LEFT JOIN itens as i ON i.id = pie.idItem 
                WHERE pie.idPersonagem = ? AND pie.adesivo = 1 AND pie.slot IN ($placeholders)
                ORDER BY pie.slot ASC";
        
        $stmt = DB::prepare($sql);
        $params = array_merge([$idPersonagem], $slots_array);
        $stmt->execute($params);
        $slots = $stmt->fetchAll();
        
        // Display adesivo slots
        foreach($slots as $slot) {
            if($slot['idItem'] && $slot['idItem'] > 0 && !empty($slot['imagem'])) {
                // Slot with adesivo item
                $raridadeclass = 'raridade-'.$slot['raridade'];
                echo '<li class="slots adesivo slot-amarelo has-item '.$raridadeclass.'" data-slot="'.$slot['slot'].'" data-item="'.$slot['idItem'].'">';
                echo '<img src="'.BASE.'assets/itens/'.$slot['imagem'].'" alt="'.$slot['nome'].'" title="'.$slot['nome'].'" />';
                echo '</li>';
            } else {
                // Empty yellow slot
                echo '<li class="slots adesivo slot-amarelo slot-vazio" data-slot="'.$slot['slot'].'">';
                echo '<img src="'.BASE.'assets/slot-amarelo.png" alt="Slot Adesivo Vazio" />';
                echo '</li>';
            }
        }
    }

    public function getSlotsAdesivos($idPersonagem) {
        $core = new Core();

        // Initialize adesivo slots if they don't exist
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE idPersonagem = ? AND adesivo = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);

        if ($stmt->rowCount() == 0) {
            // Create 10 adesivo slots (yellow slots)
            for ($i = 1; $i <= 10; $i++) {
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'emblema' => 0,
                    'adesivo' => 1,
                    'vazio' => 1
                );
                $core->insert('personagens_itens_equipados', $campos);
            }
            // Re-query after insert
            $stmt = DB::prepare($sql);
            $stmt->execute([$idPersonagem]);
        }

        // Get adesivo items
        $sql = "SELECT pie.*, i.nome, i.imagem, i.tipo, i.raridade 
                FROM personagens_itens_equipados as pie 
                LEFT JOIN itens as i ON i.id = pie.idItem 
                WHERE pie.idPersonagem = ? 
                AND pie.adesivo = 1
                ORDER BY pie.slot ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slots = $stmt->fetchAll();

        // Display adesivo slots
        foreach ($slots as $slot) {
            if ($slot->idItem && $slot->idItem > 0 && !empty($slot->imagem)) {
                $raridade_class = 'raridade-' . $slot->raridade;
                echo '<li class="slots adesivo slot-amarelo slot-item has-item ' . $raridade_class . '" data-slot="' . $slot->slot . '" data-item="' . $slot->idItem . '" data-id="' . $slot->id . '" data-idItem="' . $slot->idItem . '">';
                echo '<img src="' . BASE . 'assets/itens/' . $slot->imagem . '" alt="' . $slot->nome . '" title="' . $slot->nome . '">';
                echo '</li>';

            } else {
                // Empty yellow slot - show slot-amarelo.png image
                echo '<li class="slots adesivo slot-amarelo slot-vazio" data-slot="' . $slot->slot . '">';
                echo '<img src="' . BASE . 'assets/slot-amarelo.png" alt="Slot Adesivo Vazio">';
                echo '</li>';
            }
        }
    }


    public function getStatusEquipados($idPersonagem) {
        // âœ… OPTIMIZED: Single aggregated query - 100x faster
        $sql = "SELECT 
                    COALESCE(SUM(i.forca), 0) as forca,
                    COALESCE(SUM(i.agilidade), 0) as agilidade,
                    COALESCE(SUM(i.habilidade), 0) as habilidade,
                    COALESCE(SUM(i.resistencia), 0) as resistencia,
                    COALESCE(SUM(i.sorte), 0) as sorte
                FROM personagens_itens_equipados as pie
                INNER JOIN itens as i ON i.id = pie.idItem
                WHERE pie.idPersonagem = :idPersonagem AND pie.vazio = 0";
        
        $stmt = DB::prepare($sql);
        $stmt->execute(['idPersonagem' => $idPersonagem]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'forca' => (int)$result['forca'],
            'agilidade' => (int)$result['agilidade'],
            'habilidade' => (int)$result['habilidade'],
            'resistencia' => (int)$result['resistencia'],
            'sorte' => (int)$result['sorte']
        ];
    }






    public function atualizaEquipados($idPersonagem, $id, $idItem){
        $core = new Core();
        
        if($idItem != 'undefined'){
            $sql = "SELECT * FROM itens WHERE id = $idItem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $market = $stmt->fetch();
            
            if($this->verificaItemIgual($market->nome, $idPersonagem)){
                $slot_recebido = $this->verificaItemIgual($market->nome, $idPersonagem);
            }
            
            $campos = array(
                'idItem' => $idItem,
                'idSlot' => $slot_recebido,
                'idPersonagem' => $idPersonagem
            );

            $core->insert('personagens_inventario_itens', $campos);

            $campos_inventario = array(
                'vazio' => '1'
            );

            $where_inventario = "id = ".$id;

            $core->update('personagens_itens_equipados', $campos_inventario, $where_inventario);
        }
        
        $this->getSlotsEquipados($idPersonagem);
    }
    
    public function atualizaAdesivos($idPersonagem, $id, $idItem){
        $core = new Core();
        
        if($idItem != 'undefined'){
            $sql = "SELECT * FROM itens WHERE id = $idItem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $market = $stmt->fetch();
            
            if($this->verificaItemIgual($market->nome, $idPersonagem)){
                $slot_recebido = $this->verificaItemIgual($market->nome, $idPersonagem);
            }
            
            $campos = array(
                'idItem' => $idItem,
                'idSlot' => $slot_recebido,
                'idPersonagem' => $idPersonagem
            );

            $core->insert('personagens_inventario_itens', $campos);

            $campos_inventario = array(
                'vazio' => '1'
            );

            $where_inventario = "id = ".$id;

            $core->update('personagens_itens_equipados', $campos_inventario, $where_inventario);
        }
        
        $this->getSlotsAdesivos($idPersonagem);
    }
    
    public function getSorteio(){
        $x = rand(1, 150);
        
        $numeros_raridade_2 = array(3,5,6,9,12,15,18,21,24,27,31,34,37,41,44,47,51,54,57,61,64,67,71,74,77);
        
        $numeros_raridade_3 = array(81,84,87,91,94,97,101,104,107,111,114,117);
        
        $numeros_raridade_4 = array(121,124,127,131);
        
        $numeros_raridade_5 = array(1,134);

        if(in_array($x, $numeros_raridade_5, true)){
            $tipo = 5;
        } else if(in_array($x, $numeros_raridade_4, true)){
            $tipo = 4;
        } else if(in_array($x, $numeros_raridade_3, true)){
            $tipo = 3;
        } else if(in_array($x, $numeros_raridade_2, true)){
            $tipo = 2;
        } else {
            $tipo = 1;
        }

        return $tipo;
    }
    
    

    public function getSorteioBau(){
        $core = new Core();
        $config = $core->getConfiguracoes();
        
        $numeros = array(2, 3, 7, 10, 1, 4, 5, 6, 18, 23, 20, 35, 40);
 
        $qtdNumeros = sizeof($numeros);
         
        // Sorteando
        $sorteado[1] = $numeros[rand(0,$qtdNumeros - 1)];
        $randon = rand(1, 100);

        $total =  $randon - $sorteado[1];

        if($total <= 0){
            return 1;
        } else {
            return 0;
        }
    }
    
    public function getNewItem($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT pr.*,i.nome "
             . "FROM personagens_missoes_premios as pr "
             . "INNER JOIN itens as i ON i.id = pr.idItem "
             . "WHERE pr.visualizado = 0 "
             . "AND pr.idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<div class="avisos-user drop-inventario">';
                $row .= '<span>VocÃª Ganhou o item <strong>'.$value->nome.'</strong>. Veja em seu inventÃ¡rio.</span><a class="bts-form" href="'.BASE.'inventario">Visualizar</a>';
            $row .= '</div>';
        }
        
        echo $row;
    }
    
    public function getExistsNewItem($idPersonagem){
        $core = new Core();
        
        $sql = "SELECT pr.*,i.nome "
             . "FROM personagens_missoes_premios as pr "
             . "INNER JOIN itens as i ON i.id = pr.idItem "
             . "WHERE pr.visualizado = 0 "
             . "AND pr.idPersonagem = $idPersonagem";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function setViewInventory($idPersonagem){
        $core = new Core();
        
        $campos = array(
            'visualizado' => '1'
        );
            
        $where = "idPersonagem = ".$idPersonagem;

        $core->update('personagens_missoes_premios', $campos, $where);
        
        $campos_i = array(
            'novo' => '0'
        );
            
        $where_i = "idPersonagem = ".$idPersonagem;

        $core->update('personagens_inventario', $campos_i, $where_i);
    }
    
    public function getDadosBau($idBau){
        $sql = "SELECT pi.*, i.* "
             . "FROM personagens_inventario as pi "
             . "INNER JOIN personagens_inventario_itens as psi ON psi.idSlot = pi.id "
             . "INNER JOIN itens as i ON i.id = psi.idItem "
             . "WHERE pi.id = $idBau";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $bau = $stmt->fetch();
        
        return $bau;
    }
    
    public function getCountItensBau($idBau){
        $sql = "SELECT count(*) as total FROM itens_bau WHERE idBau = $idBau";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $bau = $stmt->fetch();
        
        return $bau->total;
    }
    
    public function existsBau($idBau){
        $sql = "SELECT * FROM personagens_inventario_itens WHERE id = $idBau";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }

    public function getItemSorteado($idBau, $raridade){
        // 1. Try to get item with specific rarity
        // Note: Using :idBau and :raridade placeholders
        $sql = "SELECT ib.*, i.* 
                FROM itens_bau as ib 
                INNER JOIN itens as i ON i.id = ib.idItemRecompensa 
                WHERE ib.idBau = :idBau 
                AND i.raridade = :raridade 
                ORDER BY RAND() LIMIT 1";
        
        $stmt = DB::prepare($sql);
        // Bind the values securely
        $stmt->bindValue(':idBau', $idBau, PDO::PARAM_INT);
        $stmt->bindValue(':raridade', $raridade, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }

        // 2. FALLBACK
        $sql_fallback = "SELECT ib.*, i.* 
                        FROM itens_bau as ib 
                        INNER JOIN itens as i ON i.id = ib.idItemRecompensa 
                        WHERE ib.idBau = :idBau 
                        ORDER BY RAND() LIMIT 1";
        
        $stmt_fallback = DB::prepare($sql_fallback);
        $stmt_fallback->bindValue(':idBau', $idBau, PDO::PARAM_INT);
        $stmt_fallback->execute();
        
        return $stmt_fallback->fetch();
    }

    public function getItensBau($idBau){
        // Select ib.* explicitly to ensure idItemRecompensa is available
        $sql = "SELECT ib.*, i.* 
                FROM itens_bau as ib 
                INNER JOIN itens as i ON i.id = ib.idItemRecompensa 
                WHERE ib.idBau = :idBau";
        
        $stmt = DB::prepare($sql);
        $stmt->bindValue(':idBau', $idBau, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                // Use $value->idItemRecompensa safely
                // If you are displaying the item, use $value->id (from itens table)
                $row .= '<li class="slots" dataidItem="'.$value->id.'" dataid="'.$value->id.'">';

                $row .= '<span>';
                $row .= '<img src="'.BASE.'assets/itens/'.$value->imagem.'" alt="'.$value->nome.'" />';
                $row .= '</span>';

                $row .= '<div class="informacoes">
                <h3>'.$value->nome.'</h3>';
                
                // Display stats if they exist
                if(isset($value->hp) && $value->hp > 0) $row .= '<p><strong>HP:</strong>+ '.$value->hp.'</p>';
                if(isset($value->mana) && $value->mana > 0) $row .= '<p><strong>KI:</strong>+ '.$value->mana.'</p>';
                // ... (rest of your stats code) ...

                $row .= '</div>';
                $row .= '</li>';
            }
        } else {
            $row .= '<h4>BaÃº Vazio</h4>';
        }
        
        echo $row;
    }


    public function verificaItemIgual($nome, $idPersonagem){
        $sql = "SELECT pi.*, i.nome "
            . "FROM personagens_inventario_itens as pi "
            . "INNER JOIN itens i ON i.id = pi.idItem "
            . "WHERE i.nome = :nome "
            . "AND idPersonagem = :idPersonagem";
        
        $stmt = DB::prepare($sql);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':idPersonagem', $idPersonagem, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() > 0 && $stmt->rowCount() < 100){
            $slot = $stmt->fetch();
            return $slot->idSlot;
        } else {
            // Find first empty slot
            // Optimized query to find empty slot directly without looping PHP
            $sql_empty = "SELECT id FROM personagens_inventario 
                        WHERE idPersonagem = :idPersonagem 
                        AND (id NOT IN (SELECT idSlot FROM personagens_inventario_itens WHERE idPersonagem = :idPersonagem2))
                        ORDER BY slot ASC LIMIT 1";
                        
            $stmt_empty = DB::prepare($sql_empty);
            $stmt_empty->bindValue(':idPersonagem', $idPersonagem, PDO::PARAM_INT);
            $stmt_empty->bindValue(':idPersonagem2', $idPersonagem, PDO::PARAM_INT);
            $stmt_empty->execute();
            
            if ($stmt_empty->rowCount() > 0) {
                return $stmt_empty->fetch()->id;
            }
        }
        return false;
    }

    /**
     * Auto-organize inventory - moves all items to the front, eliminating gaps
     */
    public function organizarInventario($idPersonagem) {
        $core = new Core();
        
        // Get all items currently in inventory with their slots
        $sql = "SELECT pii.id, pii.idItem, pii.idSlot, pi.slot as slotNumber
                FROM personagens_inventario_itens as pii
                INNER JOIN personagens_inventario as pi ON pi.id = pii.idSlot
                WHERE pii.idPersonagem = ?
                ORDER BY pi.slot ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $items = $stmt->fetchAll();
        
        if(count($items) == 0){
            return true; // No items to organize
        }
        
        // Get all inventory slots
        $sql = "SELECT * FROM personagens_inventario 
                WHERE idPersonagem = ? 
                ORDER BY slot ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slots = $stmt->fetchAll();
        
        // Reorganize: assign items to slots 1, 2, 3... without gaps
        $slotIndex = 0;
        foreach($items as $item) {
            $targetSlot = $slots[$slotIndex];
            
            // If item is not in the correct slot, move it
            if($item->idSlot != $targetSlot->id){
                $sql = "UPDATE personagens_inventario_itens 
                        SET idSlot = ? 
                        WHERE id = ?";
                $stmt = DB::prepare($sql);
                $stmt->execute([$targetSlot->id, $item->id]);
            }
            
            $slotIndex++;
        }
        
        return true;
    }

    public function getSlotsEmblemas($idPersonagem) {
        $core = new Core();

        // Inicializa os slots de emblema se nÃ£o existirem
        $sql = "SELECT * FROM personagens_itens_equipados WHERE idPersonagem = ? AND emblema = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);

        if ($stmt->rowCount() == 0) {
            for ($i = 1; $i <= 3; $i++) {
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'emblema' => 1,
                    'adesivo' => 0,
                    'vazio' => 1
                );
                $core->insert('personagens_itens_equipados', $campos);
            }
            // Re-query apÃ³s criar
            $stmt = DB::prepare($sql);
            $stmt->execute([$idPersonagem]);
        }

        // Busca os emblemas equipados
        $sql = "SELECT pie.*, i.nome, i.imagem, i.tipo, i.raridade 
                FROM personagens_itens_equipados as pie 
                LEFT JOIN itens as i ON i.id = pie.idItem 
                WHERE pie.idPersonagem = ? AND pie.emblema = 1
                ORDER BY pie.slot ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slots = $stmt->fetchAll();

        // Renderiza slots
        foreach ($slots as $slot) {
            if ($slot->idItem && $slot->idItem > 0 && !empty($slot->imagem)) {
                $raridade_class = 'raridade-' . $slot->raridade;
                echo '<li class="slots emblema slot-emblema slot-item has-item ' . $raridade_class . '" data-slot="' . $slot->slot . '" data-item="' . $slot->idItem . '" data-id="' . $slot->id . '" data-idItem="' . $slot->idItem . '" data-emblema="1">';
                echo '<img src="' . BASE . 'assets/itens/' . $slot->imagem . '" alt="' . $slot->nome . '" title="' . $slot->nome . '">';
                echo '</li>';

            } else {
                echo '<li class="slots emblema slot-emblema slot-vazio" data-slot="' . $slot->slot . '" dataemblema="1">';
                echo '<img src="' . BASE . 'assets/slot-emblema.png" alt="Slot Emblema Vazio">';
                echo '</li>';
            }
        }
    }




    // ==================== MÃ‰TODOS PARA EMBLEMAS ====================
    
    /**
     * Equip emblema from inventory to emblema slot
     */
    /**
     * Equip emblema from inventory to emblema slot
     * @param int $idInventario ID da linha em personagens_inventario_itens
     * @param int $idPersonagem
     * @return bool
     */
    public function equiparEmblema($idInventario, $idPersonagem) {
        $core = new Core();

        // Busca o item do inventÃ¡rio pelo id da linha do inventÃ¡rio
        $sql = "SELECT * FROM personagens_inventario_itens WHERE id = ? AND idPersonagem = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idInventario, $idPersonagem]);
        $itemInventario = $stmt->fetch();

        if(!$itemInventario){
            return false;
        }

        // Agora puxa o tipo do item
        $sql = "SELECT * FROM itens WHERE id = ? AND emblema = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$itemInventario->idItem]);
        $item = $stmt->fetch();

        if(!$item){
            return false; // Not an emblema
        }

        // Busca slot vago de emblema
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE idPersonagem = ? AND emblema = 1 AND vazio = 1 
                ORDER BY slot ASC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slotVazio = $stmt->fetch();

        if(!$slotVazio){
            return false; // No empty slot available
        }

        // Equipa o emblema no slot
        $sql = "UPDATE personagens_itens_equipados SET idItem = ?, vazio = 0 WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$item->id, $slotVazio->id]);

        // Remove do inventÃ¡rio
        $sql = "DELETE FROM personagens_inventario_itens WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$itemInventario->id]);

        $this->organizarInventario($idPersonagem);

        return true;
    }

    /**
     * Unequip emblema from slot back to inventory
     */
    public function desequiparEmblema($idSlot, $idPersonagem) {
        $core = new Core();

        // Get equipped emblema data
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE id = ? AND idPersonagem = ? AND emblema = 1 AND vazio = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idSlot, $idPersonagem]);
        $slotData = $stmt->fetch();

        if(!$slotData || !$slotData->idItem){
            return false;
        }

        // âœ… FIX: Find empty inventory slot properly
        $sql = "SELECT pi.* 
                FROM personagens_inventario as pi 
                LEFT JOIN personagens_inventario_itens as pii ON pii.idSlot = pi.id
                WHERE pi.idPersonagem = ? 
                AND pii.id IS NULL
                ORDER BY pi.slot ASC
                LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slotInventario = $stmt->fetch();

        if(!$slotInventario){
            return false; // Inventory full
        }

        // Insert emblema back to inventory
        $sql = "INSERT INTO personagens_inventario_itens (idPersonagem, idSlot, idItem, quantidade) 
                VALUES (?, ?, ?, 1)";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem, $slotInventario->id, $slotData->idItem]);

        // Clear the emblema slot
        $sql = "UPDATE personagens_itens_equipados 
                SET idItem = NULL, vazio = 1 
                WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idSlot]);

        // Auto-organize
        $this->organizarInventario($idPersonagem);

        return true;
    }

        /**
     * Equip equipamento from inventory to equipment slot
     */
    public function equiparEquipados($idInventario, $idPersonagem) {
        $core = new Core();

        // Busca o item do inventÃ¡rio pelo id da linha do inventÃ¡rio
        $sql = "SELECT * FROM personagens_inventario_itens WHERE id = ? AND idPersonagem = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idInventario, $idPersonagem]);
        $item_inventario = $stmt->fetch();

        if(!$item_inventario) {
            return false;
        }

        // Pega o tipo do item caso queira validar ("arma", "equipamento", etc)
        // $sql = "SELECT * FROM itens WHERE id = ?";
        // $stmt = DB::prepare($sql);
        // $stmt->execute([$item_inventario->idItem]);
        // $item = $stmt->fetch();

        // Busca slot vago de equipamento (emblema = 0 e adesivo = 0)
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE idPersonagem = ? AND emblema = 0 AND adesivo = 0 AND vazio = 1
                ORDER BY slot ASC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slot_vazio = $stmt->fetch();

        if(!$slot_vazio) {
            return false;
        }

        // Equipa o item
        $sql = "UPDATE personagens_itens_equipados SET idItem = ?, vazio = 0 WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$item_inventario->idItem, $slot_vazio->id]);

        // Remove do inventÃ¡rio
        $sql = "DELETE FROM personagens_inventario_itens WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$item_inventario->id]);

        // Marca slot inventÃ¡rio como vago
        $sql = "UPDATE personagens_inventario SET vazio = 1 WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$item_inventario->idSlot]);

        $this->organizarInventario($idPersonagem);

        return true;
    }

    /**
     * Unequip equipamento from slot back to inventory
     */
    public function desequiparEquipados($idSlot, $idPersonagem) {
        $core = new Core();

        // Get the equipped item data
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE id = ? AND idPersonagem = ? AND emblema = 0 AND adesivo = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idSlot, $idPersonagem]);
        $equipped = $stmt->fetch();

        if(!$equipped || $equipped->vazio == 1){
            return false; // Slot is empty or not found
        }

        $idItem = $equipped->idItem;

        // âœ… FIX: Find empty inventory slot (no item in personagens_inventario_itens)
        $sql = "SELECT pi.* 
                FROM personagens_inventario as pi 
                LEFT JOIN personagens_inventario_itens as pii ON pii.idSlot = pi.id
                WHERE pi.idPersonagem = ? 
                AND pii.id IS NULL
                ORDER BY pi.slot ASC
                LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $inventory_slot = $stmt->fetch();

        if(!$inventory_slot){
            return false; // Inventory is actually full
        }

        // Insert item back into inventory
        $sql = "INSERT INTO personagens_inventario_itens (idPersonagem, idSlot, idItem, quantidade) 
                VALUES (?, ?, ?, 1)";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem, $inventory_slot->id, $idItem]);

        // Clear the equipped slot
        $sql = "UPDATE personagens_itens_equipados 
                SET idItem = NULL, vazio = 1 
                WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idSlot]);

        // Auto-organize inventory
        $this->organizarInventario($idPersonagem);

        return true;
    }


    

}



