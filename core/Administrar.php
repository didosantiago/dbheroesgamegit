<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Administrar
 *
 * @author Felipe Faciroli
 */
class Administrar {
    
    public function getListGuerreiros(){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens ORDER BY nome ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            if($value->liberado == 0){
                $ativo = 'inativo';
            } else {
                $ativo = '';
            }
            
            $row .= '<li class="adm-personagem '.$ativo.'">
                        <a href="'.BASE.'personagens/edit/'.$value->id.'">
                            <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome.'" />
                            <div class="info">
                                <h3>'.$value->nome.'</h3>
                                <span class="raca"><strong>Raça:</strong>'.$value->raca.'</span>
                                <span class="hp"><strong>HP:</strong>'.$value->hp.'</span>
                                <span class="mana"><strong>KI:</strong>'.$value->mana.'</span>
                                <span class="energia"><strong>Energia:</strong>'.$value->energia.'</span>
                            </div>
                        </a>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getListPalavrasOfensivas(){
        $core = new Core();
        
        $sql = "SELECT * FROM palavras_bloqueadas ORDER BY palavra ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $row .= '<li>
                        <a href="'.BASE.'palavras/edit/'.$value->id.'">
                            <h3>'.$value->palavra.'</h3>
                            <i class="fas fa-edit"></i>
                        </a>
                     </li>';
        }
        
        echo $row;
    }
    
        /**
     * Update photo raridade based on selected flags
     */
    public function updatePhotoRaridade($photo, $selectedFlags) {
        if(empty($photo)) {
            return false;
        }
        
        // Map flags to raridade numbers
        $raridade = 4; // Default to orange (4) as you set
        
        if(in_array('Mítico', $selectedFlags)) {
            $raridade = 5; // Pink
        } elseif(in_array('Lendário', $selectedFlags)) {
            $raridade = 4; // Orange 
        } elseif(in_array('Épico', $selectedFlags)) {
            $raridade = 3; // Purple (lilas)
        } elseif(in_array('Raro', $selectedFlags)) {
            $raridade = 2; // Blue (like Gohan)
        } elseif(in_array('Comum', $selectedFlags)) {
            $raridade = 1; // Green
        }
        
        try {
            // Update or insert into personagens_fotos table
            $sql = "INSERT INTO personagens_fotos (foto, raridade, free) VALUES (?, ?, 0) 
                    ON DUPLICATE KEY UPDATE raridade = VALUES(raridade)";
            $stmt = DB::prepare($sql);
            $success = $stmt->execute([$photo, $raridade]);
            
            return $success;
        } catch (Exception $e) {
            error_log("Error updating photo raridade: " . $e->getMessage());
            return false;
        }
    }

    
    public function getJogadoresPorPersonagens(){
        $sql = "SELECT * FROM personagens WHERE liberado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $sql = "SELECT count(*) as total FROM usuarios_personagens WHERE idPersonagem = $value->id";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $quantidade = $stmt->fetch();
            
            $row .= '<li>
                        <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome.'" />
                        <span>'.$quantidade->total.'</span>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getListVisitantesOnline($pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $sql = "SELECT count(*) as total "
             . "FROM usuarios_personagens as up "
             . "INNER JOIN usuarios_monitoramento as um ON um.idPersonagem = up.id "
             . "INNER JOIN usuarios as u ON u.id = up.idUsuario ";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $qtd = $stmt->fetch();
        $counter = $qtd->total;     
        
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT up.*, u.nome as nome_usuario, u.email as email_usuario "
             . "FROM usuarios_personagens as up "
             . "INNER JOIN usuarios_monitoramento as um ON um.idPersonagem = up.id "
             . "INNER JOIN usuarios as u ON u.id = up.idUsuario "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $row .= '<tr>
                            <td>'.$value->nome.'</td>
                            <td>'.$value->nome_usuario.'</td>
                            <td>'.$value->email_usuario.'</td>
                            <td>'.$core->dataBR($value->data_cadastro).'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>'.$value->nivel.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="6" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
            $row .= '<tr>
                        <td colspan="6" class="not">Nenhum Guerreiro Online.</td>
                     </tr>';
        }
        
        echo $row;
    }
    
    public function getListTransacoes($pc, $qtd_resultados){
        $core = new Core();
        
        $mes = date('m');
        $ano = date('Y');
        
        // Fix: First get count
        $sql = "SELECT count(*) as total "
             . "FROM transacoes as t "
             . "INNER JOIN usuarios as u ON u.id = t.idUsuario "
             . "WHERE MONTH(t.data) = '$mes' "
             . "AND YEAR(t.data) = '$ano' "
             . "AND t.status = 3 ";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $qtd = $stmt->fetch();
        $counter = $qtd->total;     
        
        //Paginando os Resultados
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT t.*, u.nome, u.email "
             . "FROM transacoes as t "
             . "INNER JOIN usuarios as u ON u.id = t.idUsuario "
             . "WHERE MONTH(t.data) = '$mes' "
             . "AND YEAR(t.data) = '$ano' "
             . "AND t.status = 3 "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $row .= '<tr>
                            <td>'.$value->nome.'</td>
                            <td>'.$value->email.'</td>
                            <td>'.$core->dataBR($value->data).'</td>
                            <td>'.$core->formataMoeda($value->valor).'</td>
                            <td>'.$value->coins.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="5" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
            $row .= '<tr>
                        <td colspan="5" class="not">Nenhuma Transação Recebida.</td>
                     </tr>';
        }
        
        echo $row;
    }
    
    // NEW LOJA METHODS - CLEAN VERSION WITHOUT DUPLICATES
    
    /**
     * Get shop statistics - SINGLE VERSION ONLY
     */
    public function getLojaStats(){
        $stats = new stdClass();
        
        try {
            // Total active items
            $sql = "SELECT COUNT(*) as total FROM adm_loja_itens WHERE loja = 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            $stats->total_items = $result->total;
            
            // Items by type
            $sql = "SELECT modulo, COUNT(*) as count FROM adm_loja_itens WHERE loja = 1 GROUP BY modulo";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $types = $stmt->fetchAll();
            
            $stats->fotos = 0;
            $stats->modulos = 0;
            $stats->itens = 0;
            
            foreach($types as $type) {
                switch($type->modulo) {
                    case 1: $stats->fotos = $type->count; break;
                    case 2: $stats->modulos = $type->count; break;
                    case 3: $stats->itens = $type->count; break;
                }
            }
            
            // Current active day - simplified query
            $sql = "SELECT dia FROM adm_loja_produtos WHERE status = 1 LIMIT 1";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            if($stmt->rowCount() > 0) {
                $active_day = $stmt->fetch();
                $stats->active_day = $active_day->dia;
            } else {
                $stats->active_day = null;
            }
            
        } catch (Exception $e) {
            error_log("Error getting loja stats: " . $e->getMessage());
            $stats->total_items = 0;
            $stats->fotos = 0;
            $stats->modulos = 0;
            $stats->itens = 0;
            $stats->active_day = null;
        }
        
        return $stats;
    }
    
    /**
     * Add new item to shop
     */
    public function addLojaItem($data) {
        try {
            // Debug what we're inserting
            error_log("DEBUG - Adding item: " . json_encode($data));
            
            // Use the exact column structure from your database
            $sql = "INSERT INTO adm_loja_itens 
                    (nome, descricao, valor, modulo, foto, loja, novo, promocao, flag, idBoneco) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = DB::prepare($sql);
            
            $params = [
                $data['nome'],                    // nome
                $data['descricao'] ?: null,      // descricao 
                intval($data['valor']),          // valor
                intval($data['modulo']),         // modulo
                $data['foto'] ?: null,           // foto
                1,                               // loja (always 1 for active)
                $data['novo'] ? 1 : 0,          // novo
                $data['promocao'] ? 1 : 0,      // promocao  
                $data['flag'] ?: null,           // flag
                $data['idBoneco'] ?: null       // idBoneco
            ];
            
            error_log("DEBUG - SQL params: " . json_encode($params));
            
            $result = $stmt->execute($params);
            
            if (!$result) {
                error_log("DEBUG - SQL Error: " . json_encode($stmt->errorInfo()));
                return false;
            }
            
            // FIX: Use proper method to get last insert ID for your DB class
            try {
                // Alternative method - query for it
                $lastIdStmt = DB::prepare("SELECT LAST_INSERT_ID() as id");
                $lastIdStmt->execute();
                $lastIdResult = $lastIdStmt->fetch();
                $lastId = $lastIdResult->id;
                
                error_log("DEBUG - Item created with ID: " . $lastId);
                return $lastId;
                
            } catch (Exception $e) {
                error_log("DEBUG - Could not get last insert ID: " . $e->getMessage());
                // Return true since the insert was successful even if we can't get the ID
                return true;
            }
            
        } catch (Exception $e) {
            error_log("DEBUG - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing shop item
     */
    public function updateLojaItem($id, $data) {
        try {
            $sql = "UPDATE adm_loja_itens SET 
                    nome = ?, descricao = ?, valor = ?, modulo = ?, foto = ?, 
                    novo = ?, promocao = ?, flag = ?
                    WHERE id = ?";
            
            $stmt = DB::prepare($sql);
            $result = $stmt->execute([
                $data['nome'],
                $data['descricao'] ?: '',
                $data['valor'],
                $data['modulo'],
                $data['foto'] ?: '',
                $data['novo'] ? 1 : 0,
                $data['promocao'] ? 1 : 0,
                $data['flag'] ?: '',
                $id
            ]);
            
            return $result;
        } catch (Exception $e) {
            error_log("Error updating loja item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove item from shop (soft delete)
     */
    public function removeLojaItem($id) {
        try {
            $sql = "UPDATE adm_loja_itens SET loja = 0 WHERE id = ?";
            $stmt = DB::prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Error removing loja item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get available personagens for dropdown
     */
    public function getPersonagensDisponiveis() {
        try {
            $sql = "SELECT id, nome, foto FROM personagens WHERE liberado = 1 ORDER BY nome ASC";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting personagens: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get available itens for dropdown  
     */
    public function getItensDisponiveis() {
        try {
            $sql = "SELECT id, nome FROM itens ORDER BY nome ASC LIMIT 50";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting itens: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update daily products configuration
     */
    public function updateProdutosDiarios($dia, $positions) {
        try {
            // Check if day configuration exists
            $sql = "SELECT id FROM adm_loja_produtos WHERE dia = ?";
            $stmt = DB::prepare($sql);
            $stmt->execute([$dia]);
            $exists = $stmt->fetch();
            
            if($exists) {
                // Update existing
                $sql = "UPDATE adm_loja_produtos SET 
                        posicao_1 = ?, posicao_2 = ?, posicao_3 = ?, posicao_4 = ?,
                        posicao_5 = ?, posicao_6 = ?, posicao_7 = ?, posicao_8 = ?
                        WHERE dia = ?";
                
                $stmt = DB::prepare($sql);
                return $stmt->execute([
                    $positions[1] ?: null, $positions[2] ?: null, $positions[3] ?: null, $positions[4] ?: null,
                    $positions[5] ?: null, $positions[6] ?: null, $positions[7] ?: null, $positions[8] ?: null,
                    $dia
                ]);
            } else {
                // Insert new
                $sql = "INSERT INTO adm_loja_produtos 
                        (dia, posicao_1, posicao_2, posicao_3, posicao_4, posicao_5, posicao_6, posicao_7, posicao_8, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
                
                $stmt = DB::prepare($sql);
                return $stmt->execute([
                    $dia,
                    $positions[1] ?: null, $positions[2] ?: null, $positions[3] ?: null, $positions[4] ?: null,
                    $positions[5] ?: null, $positions[6] ?: null, $positions[7] ?: null, $positions[8] ?: null
                ]);
            }
        } catch (Exception $e) {
            error_log("Error updating produtos diarios: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get daily products list for admin
     */
    public function getListProdutosDiarios(){
        $dias = ['', 'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        
        $row = '';
        
        for($d = 1; $d <= 7; $d++) {
            try {
                $sql = "SELECT * FROM adm_loja_produtos WHERE dia = ?";
                $stmt = DB::prepare($sql);
                $stmt->execute([$d]);
                $day_config = $stmt->fetch();
                
                $configured_items = 0;
                $items_list = [];
                
                if($day_config) {
                    for($pos = 1; $pos <= 8; $pos++) {
                        if($day_config->{"posicao_$pos"}) {
                            $configured_items++;
                            
                            // Get item name
                            $sql_item = "SELECT nome FROM adm_loja_itens WHERE id = ?";
                            $stmt_item = DB::prepare($sql_item);
                            $stmt_item->execute([$day_config->{"posicao_$pos"}]);
                            $item_data = $stmt_item->fetch();
                            
                            if($item_data) {
                                $items_list[] = $item_data->nome;
                            }
                        }
                    }
                }
                
                $status_text = $day_config && $day_config->status ? 'Ativo' : 'Inativo';
                $items_preview = count($items_list) > 0 ? implode(', ', array_slice($items_list, 0, 3)) : 'Nenhum item';
                if(count($items_list) > 3) $items_preview .= '...';
                
                $row .= '<tr>
                            <td><strong>'.$dias[$d].'</strong></td>
                            <td>'.$status_text.'</td>
                            <td>'.$configured_items.'/8 posições</td>
                            <td><small>'.$items_preview.'</small></td>
                         </tr>';
            } catch (Exception $e) {
                error_log("Error in getListProdutosDiarios for day $d: " . $e->getMessage());
                $row .= '<tr>
                            <td><strong>'.$dias[$d].'</strong></td>
                            <td>Erro</td>
                            <td>-</td>
                            <td>Erro ao carregar</td>
                         </tr>';
            }
        }
        
        echo $row;
    }
}
