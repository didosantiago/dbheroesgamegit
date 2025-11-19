<?php

/* ... (rest of your file remains unchanged above this line) ... */

class Inventario {
    // ... existing methods ...

    /**
     * Initializes 8 equipped slots (3 emblems, 5 normal) for a new character
     */
    public function inicializaSlotsEquipados($idPersonagem) {
        $core = new Core();
        $sql = "SELECT * FROM personagens_itens_equipados WHERE idPersonagem = $idPersonagem AND slot IN (1,2,3,4,5,6,7,8)";
        $stmt = DB::prepare($sql);
        $stmt->execute();

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
                if($item_invetario->emblema == 1){
                    $where = "id = ".$slots_vazio->id." AND emblema = 1";
                } else if($item_invetario->adesivo == 1){
                    $where = "id = ".$slots_vazio->id." AND adesivo = 1";
                } else {
                    $where = "id = ".$slots_vazio->id;
                }

                $core->update('personagens_itens_equipados', $campos, $where);

                $core->delete('personagens_inventario_itens', "id = ".$item_invetario->idArmazenado);
            }

            $this->getSlotsEquipados($idPersonagem);
        } else if($item_invetario->tipo == 1){
            $personagem->getGuerreiro($idPersonagem);
            
            $level = $personagem->nivel;
            $hp = $personagem->hp;
            $ki = $personagem->mana;
            $ki_usado = $personagem->ki_usado;
            $energia = $personagem->energia;
            $energia_usada = $personagem->energia_usada;
            
            $hp_item = $item_invetario->item_hp;
            $ki_item = $item_invetario->item_ki;
            $energia_item = $item_invetario->item_energia;
            
            if($ki_usado < $item_invetario->item_ki){
                $diferenca_ki = 0;
            } else {
                $diferenca_ki = $ki_usado;
            }
            
            $calc_hp = $hp_item / 100;
            $calc_ki = $ki_item / 100;
            $calc_energia = $energia_item / 100;
    
            $hp_level = 50;
            $valor_hp = ($level * $hp_level) + 50;

            $diferenca_hp = ($valor_hp - $hp);
            $diferenca_energia = ($energia_usada - $energia);
            
            $total_hp = floor($diferenca_hp * $calc_hp);
            $total_ki = floor($diferenca_ki * $calc_ki);
            $total_energia = floor($diferenca_energia * $calc_energia);
            
            if($total_hp < 0){
                $total_hp = $valor_hp;
            } else {
                $total_hp = $hp + $total_hp;
            }
            
            if($total_ki > $diferenca_ki){
                $total_ki = 0;
            } else {
                $total_ki = $ki_usado - $total_ki; 
            }
            
            if($energia_item > 0){
                $energia_recuperar = $total_energia;
            } else {
                $energia_recuperar = $energia_usada;
            }
            
            if($item_invetario->item_energia > 0){
                $energia_faltante = $energia - intval($energia_usada);
                if($energia_faltante > 0){
                    if($energia_faltante >= $item_invetario->item_energia){
                        $energia_recuperar = intval($energia_usada) - ($item_invetario->item_energia);
                    } else {
                        $energia_recuperar = 0;
                    }
                }
            }
            
            $up_guerreiro = array(
                'hp' => $total_hp,
                'ki_usado' => $total_ki,
                'energia_usada' => $energia_recuperar
            );

            $where_guerreiro = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
            
            $core->delete('personagens_inventario_itens', "id = ".$item_invetario->idArmazenado);
            
            $this->getSlotsEquipados($idPersonagem);
        } else if($item_invetario->tipo == 3){
            $personagem->getGuerreiro($idPersonagem);
            
            $level = $personagem->nivel;
            $hp = $personagem->hp;
            
            $hp_item = $item_invetario->item_hp;
            
            $calc_hp = $hp_item / 100;
    
            $hp_level = 50;
            $valor_hp = ($level * $hp_level) + 50;

            $diferenca_hp = ($valor_hp - $hp);
            
            $total_hp = floor($diferenca_hp * $calc_hp);
            
            if($total_hp < 0){
                $total_hp = $valor_hp;
            } else {
                $total_hp = $hp + $total_hp;
            }
            
            $up_guerreiro = array(
                'hp' => $total_hp
            );

            $where_guerreiro = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
            
            $core->delete('personagens_inventario_itens', "id = ".$item_invetario->idArmazenado);
            
            $this->getSlotsEquipados($idPersonagem);
        } else if($item_invetario->tipo == 4){
            $personagem->getGuerreiro($idPersonagem);
            
            $level = $personagem->nivel;
            $ki = $personagem->mana;
            $ki_usado = $personagem->ki_usado;
            
            $ki_item = $item_invetario->item_ki;
            
            if($ki_usado < $item_invetario->item_ki){
                $diferenca_ki = 0;
            } else {
                $diferenca_ki = $ki_usado;
            }
            
            $calc_ki = $ki_item / 100;
            
            $total_ki = floor($diferenca_ki * $calc_ki);
            
            if($total_ki > $diferenca_ki){
                $total_ki = 0;
            } else {
                $total_ki = $ki_usado - $total_ki;
            }
            
            $up_guerreiro = array(
                'ki_usado' => $total_ki
            );

            $where_guerreiro = 'id = "'.$idPersonagem.'"';

            $core->update('usuarios_personagens', $up_guerreiro, $where_guerreiro);
            
            $core->delete('personagens_inventario_itens', "id = ".$item_invetario->idArmazenado);
            
            $this->getSlotsEquipados($idPersonagem);
        }
    }
                
    public function getSlots($idPersonagem) {
        $core = new Core();
        
        // Get inventory slots
        $sql = "SELECT pi.*, pii.id as itemStorageId, pii.idItem, i.nome, i.imagem, i.tipo, i.raridade, i.adesivo, i.emblema
                FROM personagens_inventario as pi 
                LEFT JOIN personagens_inventario_itens as pii ON pii.idSlot = pi.id 
                LEFT JOIN itens as i ON i.id = pii.idItem 
                WHERE pi.idPersonagem = ? 
                ORDER BY pi.slot ASC";
        
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slots = $stmt->fetchAll();
        
        if(count($slots) == 0) {
            // Initialize inventory if doesn't exist
            for($i = 1; $i <= 31; $i++) {
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'vazio' => 1
                );
                $core->insert('personagens_inventario', $campos);
            }
            // Reload after initialization
            $stmt->execute([$idPersonagem]);
            $slots = $stmt->fetchAll();
        }
        
        // Display slots
        foreach($slots as $slot) {
            if($slot->idItem && $slot->idItem > 0) {
                // Determine if item is a bau (chest)
                $isBau = (stripos($slot->nome, 'baú') !== false || stripos($slot->nome, 'bau') !== false);
                $isConsumable = in_array($slot->tipo, [1, 3, 4]);
                
                // Set raridade class
                $raridade_class = 'raridade-' . $slot->raridade;
                
                // Set background image
                if($isBau){
                    $bg_image = 'slot-bau.png';
                    $slot_class = 'slot-bau';
                } else {
                    $bg_image = 'slot-item.png';
                    $slot_class = 'slot-item';
                }

                // CRITICAL: Add your unique dataidinventario attribute here for all items!
                echo '<li class="slots ' . $slot_class . ' ' . $raridade_class . '" ';
                echo 'data-slot="' . $slot->slot . '" ';
                echo 'data-item="' . $slot->idItem . '" ';
                echo 'dataid="' . $slot->id . '" ';
                echo 'dataidItem="' . $slot->idItem . '" ';
                echo 'dataadesivo="' . $slot->adesivo . '" ';
                echo 'dataidinventario="' . $slot->itemStorageId . '" '; // <-- THIS IS IMPORTANT!
                echo 'style="background-image: url(' . BASE . 'assets/' . $bg_image . '); background-size: cover;">';

                if($isBau){
                    echo '<span class="bau">';
                } else {
                    echo '<span>';
                }
                echo '<img src="' . BASE . 'assets/itens/' . $slot->imagem . '" alt="' . $slot->nome . '" title="' . $slot->nome . '">';
                echo '</span>';
                
                // Add item info tooltip
                echo '<div class="informacoes" style="display: none;">';
                echo '<h3>' . $slot->nome . '</h3>';
                if($isConsumable){
                    echo '<p><em>Consumível</em></p>';
                }
                if($isBau){
                    echo '<p><em>Clique para abrir</em></p>';
                }
                echo '</div>';
                
                echo '</li>';
            } else {
                // Empty slot
                echo '<li class="slots slot-vazio" data-slot="' . $slot->slot . '" ';
                echo 'style="background-image: url(' . BASE . 'assets/slot-vazio.png); background-size: cover;">';
                echo '</li>';
            }
        }
    }

    /**
     * Main method to equip/use items
     */
    // ADD THIS METHOD - Checks item slots properly
    public function equiparItens($idItem, $idPersonagem, $idInventario) {
        $core = new Core();
        $personagem = new Personagens();

        // Find the exact instance user clicked!
        $sql = "SELECT pii.*, pi.slot as slotNum 
                FROM personagens_inventario_itens as pii
                INNER JOIN personagens_inventario as pi ON pi.id = pii.idSlot
                WHERE pii.id = ? AND pii.idPersonagem = ? LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idInventario, $idPersonagem]);
        $itemInventario = $stmt->fetch();

        if(!$itemInventario) return false;
            
        // Type checking
        $consumable_types = ['consumivel', 'capsula', 'comida', 'restauracao'];
        
        // Consumables
        if(in_array(strtolower($item->tipo), $consumable_types)){
            return $this->usarConsumivel($item, $itemInventario, $idPersonagem);
        }
        
        // Adesivos
        if(isset($item->adesivo) && $item->adesivo == 1){
            return $this->equiparAdesivo($idItem, $idPersonagem);
        }
        
        // Equipment and Emblems
        return $this->equiparEquipamento($item, $itemInventario, $idPersonagem);
    }

    // CRITICAL: This properly detects emblems
    private function equiparEquipamento($item, $itemInventario, $idPersonagem) {
        $core = new Core();
        
        $isEmblem = 0;
        if(strtolower($item->tipo) == 'emblema'){
            $isEmblem = 1;
        } else if(isset($item->emblema) && $item->emblema == 1){
            $isEmblem = 1;
        }
        
        $sql = "SELECT * FROM personagens_itens_equipados 
                WHERE idPersonagem = ? AND adesivo = 0 AND emblema = ? AND vazio = 1 
                ORDER BY slot ASC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem, $isEmblem]);
        $slotVazio = $stmt->fetch();
        
        if(!$slotVazio){
            return false;
        }
        
        $sql = "UPDATE personagens_itens_equipados SET idItem = ?, vazio = 0 WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$item->id, $slotVazio->id]);
        
        $sql = "DELETE FROM personagens_inventario_itens WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$itemInventario->id]);
        
        // AUTO-ORGANIZE INVENTORY AFTER EQUIP
        $this->organizarInventario($idPersonagem);
        
        return true;
    }



        
    private function usarConsumivel($item, $itemInventario, $idPersonagem) {
        $core = new Core();
        $personagem = new Personagens();
        $personagem->getGuerreiro($idPersonagem);
        
        $level = $personagem->nivel;
        $hp = $personagem->hp;
        $ki_usado = $personagem->ki_usado;
        $energia_usada = $personagem->energia_usada;
        
        $hp_level = 50;
        $valor_hp_max = ($level * $hp_level) + 50;
        
        $new_hp = $hp;
        $new_ki_usado = $ki_usado;
        $new_energia_usada = $energia_usada;
        
        if(isset($item->efeito_hp) && $item->efeito_hp > 0){
            $calc_hp = $item->efeito_hp / 100;
            $diferenca_hp = ($valor_hp_max - $hp);
            $total_hp = floor($diferenca_hp * $calc_hp);
            $new_hp = min($hp + $total_hp, $valor_hp_max);
        }
        
        if(isset($item->efeito_ki) && $item->efeito_ki > 0 && $ki_usado > 0){
            $calc_ki = $item->efeito_ki / 100;
            $total_ki = floor($ki_usado * $calc_ki);
            $new_ki_usado = max($ki_usado - $total_ki, 0);
        }
        
        if(isset($item->efeito_energia) && $item->efeito_energia > 0){
            $new_energia_usada = max($energia_usada - $item->efeito_energia, 0);
        }
        
        $campos = array(
            'hp' => $new_hp,
            'ki_usado' => $new_ki_usado,
            'energia_usada' => $new_energia_usada
        );
        $where = 'id = "' . $idPersonagem . '"';
        $core->update('usuarios_personagens', $campos, $where);
        
        $core->delete('personagens_inventario_itens', "id = " . $itemInventario->id);
        
        return true;
    }


    /**
     * Equip adesivo from inventory to adesivo slot
        */
    /**
     * Equip adesivo from inventory to adesivo slot
     */
    public function equiparAdesivo($idItem, $idPersonagem) {
        $core = new Core();
        
        // Verify item is an adesivo
        $sql = "SELECT * FROM itens WHERE id = ? AND adesivo = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idItem]);
        $item = $stmt->fetch();
        
        if(!$item){
            return false; // Not an adesivo
        }
        
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
            return false; // No empty adesivo slot
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
        
        $sql = "SELECT * FROM personagens_itens_equipados WHERE idPersonagem = ? AND adesivo = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        
        if($stmt->rowCount() == 0) {
            for($i = 1; $i <= 8; $i++) {
                $emblema = ($i <= 3) ? 1 : 0;
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'emblema' => $emblema,
                    'adesivo' => 0,
                    'idItem' => 0,
                    'vazio' => 1
                );
                $core->insert('personagens_itens_equipados', $campos);
            }
        } else if($stmt->rowCount() < 8) {
            $existing_slots = array();
            $slots = $stmt->fetchAll();
            foreach($slots as $slot) {
                $existing_slots[] = $slot->slot;
            }
            
            for($i = 1; $i <= 8; $i++) {
                if(!in_array($i, $existing_slots)) {
                    $emblema = ($i <= 3) ? 1 : 0;
                    $campos = array(
                        'idPersonagem' => $idPersonagem,
                        'slot' => $i,
                        'emblema' => $emblema,
                        'adesivo' => 0,
                        'idItem' => 0,
                        'vazio' => 1
                    );
                    $core->insert('personagens_itens_equipados', $campos);
                }
            }
        }
        
        $sql = "SELECT pie.*, i.nome, i.imagem, i.tipo, i.raridade 
                FROM personagens_itens_equipados as pie 
                LEFT JOIN itens as i ON i.id = pie.idItem 
                WHERE pie.idPersonagem = ? AND pie.adesivo = 0 
                ORDER BY pie.slot ASC";
        
        $stmt = DB::prepare($sql);
        $stmt->execute([$idPersonagem]);
        $slots = $stmt->fetchAll();
        
        $emblems = array_slice($slots, 0, 3);
        $equipped = array_slice($slots, 3, 5);
        
        echo '<div class="emblems-row">';
        foreach($emblems as $slot) {
            $this->renderEquippedSlot($slot, true);
        }
        echo '</div>';
        
        echo '<div class="equipped-row">';
        foreach($equipped as $slot) {
            $this->renderEquippedSlot($slot, false);
        }
        echo '</div>';
    }

    private function renderEquippedSlot($slot, $isEmblem) {
        $slotClass = $isEmblem ? 'slot-emblema' : 'slot-equipado';
        $emptyBgImage = $isEmblem ? 'slot-emblema.png' : 'slot-equipado.png';
        
        if(!empty($slot->idItem) && $slot->idItem > 0 && !empty($slot->imagem)) {
            $raridadeClass = isset($slot->raridade) ? 'raridade-'.$slot->raridade : '';
            
            echo '<li class="slots equipped '.$slotClass.' '.$raridadeClass.' has-item" ';
            echo 'data-slot="'.$slot->slot.'" ';
            echo 'data-item="'.$slot->idItem.'" ';
            echo 'dataid="'.$slot->id.'" ';
            echo 'dataidItem="'.$slot->idItem.'" ';
            echo 'style="background-image: url('.BASE.'assets/slot-item.png); background-size: cover;">';
            echo '<img src="'.BASE.'assets/itens/'.$slot->imagem.'" alt="'.$slot->nome.'" title="'.$slot->nome.'" />';
            echo '</li>';
        } else {
            echo '<li class="slots equipped '.$slotClass.' slot-vazio" ';
            echo 'data-slot="'.$slot->slot.'" ';
            echo 'style="background-image: url('.BASE.'assets/'.$emptyBgImage.'); background-size: cover;">';
            echo '</li>';
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



    /**
     * Unequip item and return to inventory
     */
    public function desequiparItem($idSlotEquipado, $idPersonagem) {
        $core = new Core();
        
        $sql = "SELECT * FROM personagens_itens_equipados WHERE id = ? AND idPersonagem = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idSlotEquipado, $idPersonagem]);
        $equipado = $stmt->fetch();
        
        if(!$equipado || $equipado->vazio == 1){
            return false;
        }
        
        $idItem = $equipado->idItem;
        
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
            return false;
        }
        
        // Clear equipped slot
        $sql = "UPDATE personagens_itens_equipados 
                SET idItem = 0, vazio = 1 
                WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$equipado->id]);
        
        // Add to inventory
        $campos = array(
            'idItem' => $idItem,
            'idSlot' => $slotVazio->id,
            'idPersonagem' => $idPersonagem
        );
        $core->insert('personagens_inventario_itens', $campos);
        
        // AUTO-ORGANIZE INVENTORY AFTER UNEQUIP
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
                // Slot with adesivo item - show the item image and all IDs  
                $raridade_class = 'raridade-' . $slot->raridade;
                echo '<li class="slots adesivo slot-amarelo has-item ' . $raridade_class . '" data-slot="' . $slot->slot . '" dataid="' . $slot->id . '" dataidItem="' . $slot->idItem . '">';
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
    // Get all equipped items (emblemas and regular equipped slots, excluding adesivos)
    $sql = "SELECT i.forca, i.agilidade, i.habilidade, i.resistencia, i.sorte 
            FROM personagens_itens_equipados as pie 
            INNER JOIN itens as i ON i.id = pie.idItem 
            WHERE pie.idPersonagem = $idPersonagem 
            AND pie.adesivo = 0 
            AND pie.vazio = 0";
    
    $stmt = DB::prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll();
    
    // Calculate total stats from all equipped items
    $total_forca = 0;
    $total_agilidade = 0;
    $total_habilidade = 0;
    $total_resistencia = 0;
    $total_sorte = 0;
    
    foreach($items as $item) {
        $total_forca += intval($item->forca);
        $total_agilidade += intval($item->agilidade);
        $total_habilidade += intval($item->habilidade);
        $total_resistencia += intval($item->resistencia);
        $total_sorte += intval($item->sorte);
    }
    
    // Return array with total stats
    return array(
        'forca' => $total_forca,
        'agilidade' => $total_agilidade,
        'habilidade' => $total_habilidade,
        'resistencia' => $total_resistencia,
        'sorte' => $total_sorte
    );
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
                $row .= '<span>Você Ganhou o item <strong>'.$value->nome.'</strong>. Veja em seu inventário.</span><a class="bts-form" href="'.BASE.'inventario">Visualizar</a>';
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
        $sql = "SELECT ib.*, i.* "
             . "FROM itens_bau as ib "
             . "INNER JOIN itens as i ON i.id = ib.idItem "
             . "WHERE ib.idBau = $idBau "
             . "AND i.raro = $raridade "
             . "ORDER BY RAND()";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function getItensBau($idBau){
        $sql = "SELECT ib.*, i.* "
             . "FROM itens_bau as ib "
             . "INNER JOIN itens as i ON i.id = ib.idItem "
             . "WHERE ib.idBau = $idBau";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $row .= '<li class="slots" dataidItem="'.$value->idItem.'" dataid="'.$value->id.'">';

                $row .= '<span>';

                $row .= '<img src="'.BASE.'assets/itens/'.$value->imagem.'" alt="'.$value->nome.'" />';

                $row .= '</span>';

                $row .= '<div class="informacoes">

                <h3>'.$value->nome.'</h3>';

                if($value->hp > 0){
                    $row .= '<p><strong>HP:</strong>+ '.$value->hp.'</p>';
                }

                if($value->mana > 0){
                    $row .= '<p><strong>KI:</strong>+ '.$value->mana.'</p>';
                }

                if($value->energia > 0){
                    $row .= '<p><strong>Energia:</strong>+ '.$value->energia.'</p>';
                }

                if($value->forca > 0){
                    $row .= '<p><strong>Força:</strong>+ '.$value->forca.'</p>';
                }

                if($value->agilidade > 0){
                    $row .= '<p><strong>Agilidade:</strong>+ '.$value->agilidade.'</p>';
                }

                if($value->habilidade > 0){
                    $row .= '<p><strong>Habilidade:</strong>+ '.$value->habilidade.'</p>';
                }

                if($value->resistencia > 0){
                    $row .= '<p><strong>Resistência:</strong>+ '.$value->resistencia.'</p>';
                }

                if($value->sorte > 0){
                    $row .= '<p><strong>Sorte:</strong>+ '.$value->sorte.'</p>';
                }

                $row .= '</div>';
                $row .= '</li>';
            }
        } else {
            $row .= '<h4>Baú Vazio</h4>';
        }
        
        echo $row;
    }
    
    public function verificaItemIgual($nome, $idPersonagem){
        $sql = "SELECT pi.*, i.nome "
            . "FROM personagens_inventario_itens as pi "
            . "INNER JOIN itens i ON i.id = pi.idItem "
            . "WHERE i.nome = '$nome' "
            . "AND idPersonagem = $idPersonagem";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $total = $stmt->rowCount();
        
        if($stmt->rowCount() > 0 && $stmt->rowCount() < 100){
            $slot = $stmt->fetch();
            return $slot->idSlot;
        } else {
            $sql = "SELECT * FROM personagens_inventario WHERE idPersonagem = $idPersonagem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $slot = $stmt->fetchAll();
            
            if($stmt->rowCount() > 0){
                foreach ($slot as $key => $value) {
                    $sql = "SELECT * FROM personagens_inventario_itens WHERE idSlot = $value->id";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();

                    if($stmt->rowCount() <= 0){
                        return $value->id;
                    }
                }
                
            }
        }
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


}


