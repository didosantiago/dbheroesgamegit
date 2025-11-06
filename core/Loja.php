<?php

/**
 * Description of Loja
 *
 * @author Felipe Faciroli
 */
class Loja {
    public function getLoja(){
        $sql = "SELECT * FROM adm_loja";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        return $item;
    }

    public function getPrecoFoto($foto, $valor, $modulo){
        if($modulo == 1){
            $sql = "SELECT * FROM personagens_fotos WHERE foto = ? AND free = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute([$foto]);
            $item = $stmt->fetch();

            if($item && isset($item->raridade)){
                if($item->raridade == 1){
                    $preco = 4;
                } else if($item->raridade == 2){
                    $preco = 6;
                } else if($item->raridade == 3){
                    $preco = 8;
                } else if($item->raridade == 4){
                    $preco = 10;
                } else if($item->raridade == 10){
                    $preco = 15;
                } else {
                    $preco = $valor ?: 5; // Default price
                }
            } else {
                $preco = $valor ?: 5; // Default price if no item found
            }
        } else {
            $preco = $valor ?: 5; // Default price for non-photo items
        }

        $row = '<div class="valor">
                    <img src="'.BASE.'assets/icones/coin.png" />';
        if(!empty($valor) && $valor < $preco){
            $row .= '<span class="preco_de">De: <em>'.$preco.'</em></span>';
            $row .= '<span class="preco_por">Por: <em>'.$valor.'</em></span>';
        } else {
            $row .= '<span class="preco_por">'.$preco.'</span>';
        }
        $row .= '</div>';

        return $row;
    }

    public function getClassRarirade($foto){
        $sql = "SELECT * FROM personagens_fotos WHERE foto = ? AND free = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute([$foto]);
        $item = $stmt->fetch();
        
        if($item && isset($item->raridade)){
            $tipo = $item->raridade;

            if($tipo == 1){
                $classe = 'green';     // Comum
            } else if($tipo == 2){
                $classe = 'blue';      // Raro  
            } else if($tipo == 3){
                $classe = 'lilas';     // Épico (purple)
            } else if($tipo == 4){
                $classe = 'orange';    // Lendário (Broly!)
            } else if($tipo == 5){
                $classe = 'pink';      // Mítico
            } else {
                $classe = 'green';     // Default
            }
        } else {
            $classe = 'green'; // Default if no item found
        }

        return $classe;
    }


    public function getValor($foto, $valor, $modulo){
        if($modulo == 1){
            $sql = "SELECT * FROM personagens_fotos WHERE foto = ? AND free = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute([$foto]);
            $item = $stmt->fetch();

            if($item && isset($item->raridade)){
                if($item->raridade == 1){
                    $preco = 4;
                } else if($item->raridade == 2){
                    $preco = 6;
                } else if($item->raridade == 3){
                    $preco = 8;
                } else if($item->raridade == 4){
                    $preco = 10;
                } else if($item->raridade == 10){
                    $preco = 15;
                } else {
                    $preco = $valor ?: 5;
                }
                
                if(!empty($valor) && $valor < $preco){
                    return $valor;
                } else {
                    return $preco;
                }
            } else {
                return $valor ?: 5; // Default if no item found
            }
        } else {
            return $valor ?: 5; // Default for non-photo items
        }
    }

        // FIXED VERSION - This was causing the main error
    // BACKWARD COMPATIBLE VERSION - This will fix the loja.php error
    public function getNomeFoto($idBoneco, $modulo, $nome) {
        try {
            // Check if idBoneco is null or empty
            if (empty($idBoneco) || $idBoneco === null) {
                // For backward compatibility, return string when used as string
                return $nome ?: 'Item';
            }
            
            $sql = "SELECT nome, foto FROM personagens WHERE id = ? AND liberado = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute([$idBoneco]);
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch();
                // Return the character name for backward compatibility
                return $result->nome ?: ($nome ?: 'Item');
            } else {
                // Fallback if character not found
                return $nome ?: 'Item';
            }
            
        } catch (Exception $e) {
            error_log("Error in getNomeFoto: " . $e->getMessage());
            // Return safe fallback
            return $nome ?: 'Item';
        }
    }


    public function marcarFotoLoja($idLoja){
        if (empty($idLoja)) {
            return;
        }
        $core = new Core();

        $campos = array(
            'loja' => 0
        );

        $where = 'id is not null';

        $core->update('adm_loja_itens', $campos, $where);

        $sql = "SELECT * FROM adm_loja_produtos WHERE id = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idLoja]);
        $item = $stmt->fetch();

        if (!$item) {
            return;
        }

        // Update only if posicao_N is not empty
        $this->atualizaFotoLoja($item->posicao_1);
        $this->atualizaFotoLoja($item->posicao_2);
        $this->atualizaFotoLoja($item->posicao_3);
        $this->atualizaFotoLoja($item->posicao_4);
        $this->atualizaFotoLoja($item->posicao_5);
        $this->atualizaFotoLoja($item->posicao_6);
        $this->atualizaFotoLoja($item->posicao_7);
        $this->atualizaFotoLoja($item->posicao_8);
    }

    public function atualizaFotoLoja($id){
        if (empty($id)) {
            return;
        }
        $core = new Core();

        $campos = array(
            'loja' => 1
        );

        $where = 'id = '.$id;

        $core->update('adm_loja_itens', $campos, $where);
    }

    public function marcarFotoLoja2($id){
        $core = new Core();

        $sql = "SELECT * FROM adm_loja_produtos WHERE status = 1 LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        $status = 0;
        $loja = 0;

        if($item){
            if($item->posicao_1 == $id){ $status = 1; $loja = 1; }
            if($item->posicao_2 == $id){ $status = 1; $loja = 1; }
            if($item->posicao_3 == $id){ $status = 1; $loja = 1; }
            if($item->posicao_4 == $id){ $status = 1; $loja = 1; }
            if($item->posicao_5 == $id){ $status = 1; $loja = 1; }
            if($item->posicao_6 == $id){ $status = 1; $loja = 1; }
            if($item->posicao_7 == $id){ $status = 1; $loja = 1; }
            if($item->posicao_8 == $id){ $status = 1; $loja = 1; }
        }

        $campos = array(
            'loja' => $status
        );

        $where = 'id = '.$id;

        $core->update('adm_loja_itens', $campos, $where);
    }

    public function getDadosItensBau($idBau){
        $sql = "SELECT * FROM itens_bau WHERE idBau = ?";
        $stmt = DB::prepare($sql);
        $stmt->execute([$idBau]);
        $itens = $stmt->fetchAll();

        $row = '';

        foreach ($itens as $key => $value) {
            $sql = "SELECT * FROM itens WHERE id = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([$value->idItem]);
            $item = $stmt->fetch();

            if(!$item) continue; // Skip if item not found

            $row .= '<div class="status-item">';
            $row .= '<div class="foto-item">
                        <img src="'.BASE.'assets/'.$item->foto.'" />
                     </div>';

            $row .= '<h5>'.$item->nome.'</h5>';

            if($item->tipo == 1 || $item->tipo == 3 || $item->tipo == 4){
                $row .= '<h4>Item Consumível</h4>';
                $percent = '% de recuperação';
            } else {
                $percent = '';
            }

            if($item->hp > 0){
                $row .= '<p><strong>HP:</strong>+ '.$item->hp.$percent.'</p>';
            }

            if($item->mana > 0){
                $row .= '<p><strong>KI:</strong>+ '.$item->mana.$percent.'</p>';
            }

            if($item->energia > 0){
                $row .= '<p><strong>Energia:</strong>+ '.$item->energia.'</p>';
            }

            if($item->forca > 0){
                $row .= '<p><strong>Força:</strong>+ '.$item->forca.'</p>';
            }

            if($item->agilidade > 0){
                $row .= '<p><strong>Agilidade:</strong>+ '.$item->agilidade.'</p>';
            }

            if($item->habilidade > 0){
                $row .= '<p><strong>Habilidade:</strong>+ '.$item->habilidade.'</p>';
            }

            if($item->resistencia > 0){
                $row .= '<p><strong>Resistência:</strong>+ '.$item->resistencia.'</p>';
            }

            if($item->sorte > 0){
                $row .= '<p><strong>Sorte:</strong>+ '.$item->sorte.'</p>';
            }
            $row .= '</div>';
        }

        return $row;
    }

    public function getTipoProduto($modulo){
        if($modulo == 1){
            $texto = 'Foto de Perfil';
        } else if($modulo == 2){
            $texto = 'Troca de Nome';
        } else if($modulo == 3){
            $texto = 'Item do Jogo';
        } else {
            $texto = 'Produto'; // Default
        }

        return $texto;
    }
    
    // NEW METHODS FOR BETTER INTEGRATION
    
    /**
     * Get active daily products configuration
     */
    public function getDailyProducts() {
        try {
            $sql = "SELECT * FROM adm_loja_produtos WHERE status = 1 LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch();
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting daily products: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get shop items for specific positions
     */
    public function getShopItems($positions = []) {
        try {
            if(empty($positions)) {
                $sql = "SELECT * FROM adm_loja_itens WHERE loja = 1 ORDER BY id DESC";
                $stmt = DB::prepare($sql);
                $stmt->execute();
            } else {
                $placeholders = str_repeat('?,', count($positions) - 1) . '?';
                $sql = "SELECT * FROM adm_loja_itens WHERE id IN ($placeholders) AND loja = 1";
                $stmt = DB::prepare($sql);
                $stmt->execute($positions);
            }
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting shop items: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if item is available in shop
     */
    public function isItemAvailable($itemId) {
        try {
            $sql = "SELECT id FROM adm_loja_itens WHERE id = ? AND loja = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute([$itemId]);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error checking item availability: " . $e->getMessage());
            return false;
        }
    }
}
