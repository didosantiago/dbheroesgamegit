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
    
    public function equiparAdesivos($idPersonagem, $idItem, $idp){
        $core = new Core();
        $personagem = new Personagens();
        
        $sql = "SELECT * FROM personagens_itens_equipados WHERE adesivo = 1 AND idPersonagem = $idPersonagem AND idItem = $idItem AND vazio = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() <= 0){
            $sql = "SELECT pi.*, i.emblema, i.adesivo, psi.id as idArmazenado, i.tipo, i.hp as item_hp, i.mana as item_ki, i.energia as item_energia "
                 . "FROM personagens_inventario as pi "
                 . "INNER JOIN personagens_inventario_itens as psi ON psi.idSlot = pi.id "
                 . "INNER JOIN itens as i ON i.id = psi.idItem "
                 . "WHERE pi.idPersonagem = $idPersonagem "
                 . "AND psi.idItem = $idItem";

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item_invetario = $stmt->fetch();

            if($item_invetario->tipo == 0){
                if($item_invetario->adesivo == 1){
                    $sql = "SELECT * FROM personagens_itens_equipados WHERE vazio = 1 AND adesivo = 1 AND idPersonagem = $idPersonagem ORDER BY slot ASC LIMIT 1";
                }

                $stmt = DB::prepare($sql);
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $slots_vazio = $stmt->fetch();

                    $campos = array(
                        'idItem' => $idItem,
                        'vazio' => '0'
                    );

                    if($item_invetario->adesivo == 1){
                        $where = "id = ".$slots_vazio->id." AND adesivo = 1";
                    } else {
                        $where = "id = ".$slots_vazio->id;
                    }

                    $core->update('personagens_itens_equipados', $campos, $where);

                    $core->delete('personagens_inventario_itens', "id = ".$item_invetario->idArmazenado);
                }

                $this->getSlotsAdesivos($idPersonagem);
            }
        } else {
            $this->getSlotsAdesivos($idPersonagem);
        }
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

    // ... rest of your Inventario class
}

/* ... (rest of your file remains unchanged after this line) ... */
