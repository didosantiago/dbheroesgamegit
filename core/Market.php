<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Market
 *
 * @author Felipe Faciroli
 */
class Market {
    public function getList($pc, $qtd_resultados, $filtro_busca){
        $core = new Core();
        $pager = new Paginator();
        
        $sql_filtro = '';
        
        if($filtro_busca != ''){
            $sql_filtro = "AND i.nome LIKE '%".$filtro_busca."%' ";
        }
        
        $lista_itens = array();
        
        $sql = "SELECT * FROM personagens_mercado WHERE vendido = 0";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $all_itens = $stmt->fetchAll();
        
        foreach ($all_itens as $chave_itens => $t_itens) {
            if(!in_array($t_itens->idItem, $lista_itens)) {
                array_push($lista_itens, $t_itens->idItem);
            }
        }
        
        $sql = "SELECT count(*) as total "
              ."FROM itens as i "
              ."WHERE i.id in (".implode(",", array_map('intval', $lista_itens)).") "
              .$sql_filtro;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $row = '';

        $sql = "SELECT i.* "
              ."FROM itens as i "
              ."WHERE id in (".implode(",", array_map('intval', $lista_itens)).") "
              .$sql_filtro
              ."LIMIT " . $inicio . ',' . $qtd_resultados;

        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();

        foreach ($item as $chave => $value) {
            $row .= '<div class="market-itens-body">
                        <a href="'.BASE.'market/view/'.$value->id.'">
                            <div class="td-market nome">
                                <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                <div class="market_listing_item_name_block">
                                    <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome.'</span>
                                    <br>
                                    <span class="market_listing_game_name">DB Heroes</span>
                                </div>
                            </div>
                            <div class="td-market quantidade">
                                <span>'.$this->getCountItens($value->id).'</span>
                            </div>
                            <div class="td-market preco">
                                <div class="market_listing_right_cell market_listing_their_price">
                                    <span class="market_table_value normal_price">
                                        A partir de:<br>
                                        <span class="normal_price" data-price="181" data-currency="7">'.$this->getItemMenorValor($value->id).'</span>
                                    </span>
                                    <span class="market_arrow_down" style="display: none"></span>
                                    <span class="market_arrow_up" style="display: none"></span>
                                </div>
                            </div>
                        </a>
                    </div>';
        }

        // Mostra Navegador da Paginação
        $row .= '<div>'
               .$pager->paginar($pc, $tp)
              . '</div>'; 
            
        echo $row;
    }
    
    public function getListS($pc, $qtd_resultados, $filtro_busca){
        $core = new Core();
        $pager = new Paginator();
        
        $sql_filtro = '';
        
        if($filtro_busca != ''){
            $sql_filtro = "AND i.nome LIKE '%".$filtro_busca."%' ";
        }
        
        $lista_itens = array();
        
        $sql = "SELECT * FROM personagens_mercado_solicitacoes";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $all_itens = $stmt->fetchAll();
        
        if(!empty($all_itens)){
            foreach ($all_itens as $chave_itens => $t_itens) {
                if(!in_array($t_itens->idItem, $lista_itens)) {
                    array_push($lista_itens, $t_itens->idItem);
                }
            }

            $sql = "SELECT count(*) as total "
                  ."FROM itens as i "
                  ."WHERE i.id in (".implode(",", array_map('intval', $lista_itens)).") "
                  .$sql_filtro;

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $ti = $stmt->fetch();

            $counter = $ti->total;
            $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
            $tp = $counter / $qtd_resultados;

            $row = '';

            $sql = "SELECT i.* "
                  ."FROM itens as i "
                  ."WHERE id in (".implode(",", array_map('intval', $lista_itens)).") "
                  .$sql_filtro
                  ."LIMIT " . $inicio . ',' . $qtd_resultados;

            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetchAll();

            foreach ($item as $chave => $value) {
                $row .= '<div class="market-itens-body">
                            <a href="'.BASE.'market/view_solicitacoes/'.$value->id.'">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome.'</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market quantidade">
                                    <span>'.$this->getCountItensS($value->id).'</span>
                                </div>
                                <div class="td-market preco">
                                    <div class="market_listing_right_cell market_listing_their_price">
                                        <span class="market_table_value normal_price">
                                            A partir de:<br>
                                            <span class="normal_price" data-price="181" data-currency="7">'.$this->getItemMenorValorS($value->id).'</span>
                                        </span>
                                        <span class="market_arrow_down" style="display: none"></span>
                                        <span class="market_arrow_up" style="display: none"></span>
                                    </div>
                                </div>
                            </a>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>'; 

            echo $row;
        }
    }
    
    public function getListInventario($idPersonagem, $pc, $qtd_resultados){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total "
              ."FROM personagens_inventario_itens as pi "
              ."INNER JOIN itens as i ON i.id = pi.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pi.idPersonagem "
              ."WHERE pi.idPersonagem = $idPersonagem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT i.*, pi.id as idVenda "
              ."FROM personagens_inventario_itens as pi "
              ."INNER JOIN itens as i ON i.id = pi.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pi.idPersonagem "
              ."WHERE pi.idPersonagem = $idPersonagem LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome.'</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market preco">
                                    <input type="hidden" name="idVenda" value="'.$value->idVenda.'" />
                                    <input type="hidden" name="id" value="'.$value->id.'" />
                                    <input type="number" name="valor" value="" placeholder="0" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="submit" name="vender" class="bt-vender" value="Vender" />
                                </div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item no inventário</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListCambio($idUsuario, $idPersonagem, $pc, $qtd_resultados){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total FROM personagens_cambio WHERE idUsuario = $idUsuario AND idPersonagem = $idPersonagem AND vendido = 0 AND tipo = 1";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT * FROM personagens_cambio "
               . "WHERE idUsuario = $idUsuario "
               . "AND idPersonagem = $idPersonagem "
               . "AND vendido = 0 "
               . "AND tipo = 1 "
               . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/icones/coin.png" srcset="'.BASE.'assets/icones/coin.png" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">Vender Coin por Gold</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market coins">
                                    <input type="text" name="coins" value="'.$value->coins.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="golds" value="'.$value->golds.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="hidden" name="idVenda" value="'.$value->id.'" />
                                    <input type="submit" name="retirar_coins" class="bt-retirar" value="Retirar" />
                                </div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum coin à venda</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListSolicitacoesGold($idUsuario, $idPersonagem, $pc, $qtd_resultados){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total FROM personagens_mercado_solicitacoes WHERE idUsuario = $idUsuario AND idPersonagem = $idPersonagem AND tipo = 1";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT ps.*, i.foto, i.nome "
             . "FROM personagens_mercado_solicitacoes as ps "
             . "INNER JOIN itens as i ON i.id = ps.idItem "
             . "WHERE ps.idUsuario = $idUsuario "
             . "AND ps.idPersonagem = $idPersonagem "
             . "AND ps.tipo = 1 "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome.'</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="golds" value="'.$value->golds.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="hidden" name="idVenda" value="'.$value->id.'" />
                                    <input type="submit" name="retirar_solicitacao_gold" class="bt-retirar" value="Retirar" />
                                </div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item solicitado</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListSolicitacoesCoin($idUsuario, $idPersonagem, $pc, $qtd_resultados){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total FROM personagens_mercado_solicitacoes WHERE idUsuario = $idUsuario AND idPersonagem = $idPersonagem AND tipo = 2";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT ps.*, i.foto, i.nome "
             . "FROM personagens_mercado_solicitacoes as ps "
             . "INNER JOIN itens as i ON i.id = ps.idItem "
             . "WHERE ps.idUsuario = $idUsuario "
             . "AND ps.idPersonagem = $idPersonagem "
             . "AND ps.tipo = 2 "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome.'</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="coins" value="'.$value->coins.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="hidden" name="idVenda" value="'.$value->id.'" />
                                    <input type="submit" name="retirar_solicitacao_coin" class="bt-retirar" value="Retirar" />
                                </div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item solicitado</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListCambioGold($idUsuario, $idPersonagem, $pc, $qtd_resultados){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total FROM personagens_cambio WHERE idUsuario = $idUsuario AND idPersonagem = $idPersonagem AND vendido = 0 AND tipo = 2";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT * FROM personagens_cambio "
               . "WHERE idUsuario = $idUsuario "
               . "AND idPersonagem = $idPersonagem "
               . "AND vendido = 0 "
               . "AND tipo = 2 "
               . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/icones/gold.png" srcset="'.BASE.'assets/icones/gold.png" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">Vender Coin por Gold</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="golds" value="'.$value->golds.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market coins">
                                    <input type="text" name="coins" value="'.$value->coins.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="hidden" name="idVenda" value="'.$value->id.'" />
                                    <input type="submit" name="retirar_golds" class="bt-retirar" value="Retirar" />
                                </div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum gold à venda</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListCambioAll($pc, $qtd_resultados, $idUsuario){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total FROM personagens_cambio WHERE vendido = 0 AND tipo = 1";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT pc.*, up.nome "
             . "FROM personagens_cambio as pc "
             . "INNER JOIN usuarios_personagens as up ON up.id = pc.idPersonagem "
             . "WHERE pc.vendido = 0 "
             . "AND pc.tipo = 1 "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/icones/coin.png" srcset="'.BASE.'assets/icones/coin.png" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">Coin por Gold</span>
                                        <br>
                                        <span class="market_listing_game_name"><strong style="color: #a5c808;">'.$value->nome.'</strong> quer trocar Coins por Golds</span>
                                    </div>
                                </div>
                                <div class="td-market coins">
                                    <input type="text" name="coins" value="'.$value->coins.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="golds" value="'.$value->golds.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market acoes">';
                                    if($idUsuario != $value->idUsuario){
                                        $row .= '<input type="hidden" name="idVenda" value="'.$value->id.'" />
                                                 <input type="submit" name="comprar_coins" class="bt-vender" value="Trocar" />';
                                    }
                                $row .= '</div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum coin à venda</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListCambioAllGold($pc, $qtd_resultados, $idUsuario){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total FROM personagens_cambio WHERE vendido = 0 AND tipo = 2";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT pc.*, up.nome "
             . "FROM personagens_cambio as pc "
             . "INNER JOIN usuarios_personagens as up ON up.id = pc.idPersonagem "
             . "WHERE pc.vendido = 0 "
             . "AND pc.tipo = 2 "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/icones/gold.png" srcset="'.BASE.'assets/icones/gold.png" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">Golds por Coins</span>
                                        <br>
                                        <span class="market_listing_game_name"><strong style="color: #a5c808;">'.$value->nome.'</strong> quer trocar Golds por Coins</span>
                                    </div>
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="golds" value="'.$value->golds.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market coins">
                                    <input type="text" name="coins" value="'.$value->coins.'" readonly placeholder="0" />
                                </div>
                                <div class="td-market acoes">';
                                    if($idUsuario != $value->idUsuario){
                                        $row .= '<input type="hidden" name="idVenda" value="'.$value->id.'" />
                                                 <input type="submit" name="comprar_golds" class="bt-vender" value="Trocar" />';
                                    }
                                $row .= '</div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum coin à venda</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getItensAVenda($idPersonagem, $pc, $qtd_resultados){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total FROM personagens_mercado WHERE idPersonagem = $idPersonagem";    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT i.*, pi.id as idVenda, pi.valor "
              ."FROM personagens_mercado as pi "
              ."INNER JOIN itens as i ON i.id = pi.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pi.idPersonagem "
              ."WHERE pi.idPersonagem = $idPersonagem LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome.'</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market preco">
                                    <span style="display: block; padding: 30px 0 0 0;">'.$value->valor.'</span>
                                </div>
                                <div class="td-market acoes">
                                    <input type="hidden" name="id" value="'.$value->id.'" />
                                    <input type="hidden" name="idVenda" value="'.$value->idVenda.'" />
                                    <input type="submit" name="retirar" class="bt-retirar" value="Retirar" />
                                </div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item no inventário</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListInventarioBanco($idPersonagem, $pc, $qtd_resultados){
        $core = new Core();
        $pager = new Paginator();
        
        $sql = "SELECT count(*) as total "
              ."FROM personagens_inventario_itens as pi "
              ."INNER JOIN itens as i ON i.id = pi.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pi.idPersonagem "
              ."WHERE pi.idPersonagem = $idPersonagem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT i.*, pi.id as idVenda "
              ."FROM personagens_inventario_itens as pi "
              ."INNER JOIN itens as i ON i.id = pi.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pi.idPersonagem "
              ."WHERE pi.idPersonagem = $idPersonagem LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome.'</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market preco">
                                    <input type="hidden" name="idVenda" value="'.$value->idVenda.'" />
                                    <input type="hidden" name="id" value="'.$value->id.'" />
                                    <input type="text" name="valor" readonly value="'.$value->preco_min.'" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="submit" name="vender" class="bt-vender" value="Vender" />
                                </div>
                            </form>
                        </div>';
            }

            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item no inventário</div>
                     </div>';
        }
            
        echo $row;
    }
    
    public function getListItens($id, $idUsuario, $pc, $qtd_resultados, $graduacao, $idPersonagem){
        $core = new Core();
        $pager = new Paginator();
        $personagem = new Personagens();
        
        $sql = "SELECT count(*) as total "
              ."FROM personagens_mercado as pm "
              ."INNER JOIN itens as i ON i.id = pm.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pm.idPersonagem "
              ."INNER JOIN usuarios as u ON u.id = up.idUsuario "
              ."WHERE pm.vendido = 0 "
              ."AND pm.idItem = $id ";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT pm.*, i.foto, i.nome as nome_item, u.username, u.foto as foto_usuario, up.idUsuario, up.nome as nome_personagem "
              ."FROM personagens_mercado as pm "
              ."INNER JOIN itens as i ON i.id = pm.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pm.idPersonagem "
              ."INNER JOIN usuarios as u ON u.id = up.idUsuario "
              ."WHERE pm.vendido = 0 "
              ."AND pm.idItem = $id "
              . "ORDER BY pm.valor ASC "
              . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {

                $row .= '<div class="market-itens-body">
                            <div class="td-market nome">
                                <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                <div class="market_listing_item_name_block">
                                    <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome_item.'</span>
                                    <br>
                                    <span class="market_listing_game_name">DB Heroes</span>
                                </div>
                            </div>
                            <div class="td-market vendedor">
                                <img src="'.BASE.$value->foto_usuario.'" alt="'.$value->username.'" title="'.$value->username.'" />
                                <span>'.$value->nome_personagem.'</span>
                            </div>
                            <div class="td-market preco">
                                <div class="market_listing_right_cell market_listing_their_price">
                                    <span class="market_table_value normal_price">
                                        <span class="normal_price" data-price="181" data-currency="7">'.$value->valor.'</span>
                                    </span>
                                </div>
                            </div>
                            <div class="td-market acoes">';
                                if(($graduacao < $value->graduacao_inicial)){
                                    $row .= '<span style="display: block; width: 90%; font-size: 11px; color: #7FC900; margin: 20px auto 0 auto; word-break: break-word;">Liberado na Graduação</span>'
                                            . '<span style="display: block; width: 90%; font-size: 11px; color: #7FC900; margin: 0 auto; word-break: break-word;">'.$personagem->getGraduacaoTextoByID($graduacao).'</span>';
                                } else if($idPersonagem != $value->idPersonagem){
                                    $row .= '<a href="'.BASE.'market/comprar/'.$value->id.'" class="item_market_action_button btn_green_white_innerfade btn_small">
                                        <span>Comprar agora</span>
                                    </a>';
                                } else {
                                    $row .= '<span style="display: block; margin-top: 25px;">Meu Item</span>';
                                }
                            $row .= '</div>
                        </div>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item à venda</div>
                     </div>';
        }
        
        echo $row;
    }
    
    public function getListItensS($id, $idUsuario, $pc, $qtd_resultados, $graduacao, $idPersonagem){
        $core = new Core();
        $pager = new Paginator();
        $personagem = new Personagens();
        
        $sql = "SELECT count(*) as total "
              ."FROM personagens_mercado_solicitacoes as pm "
              ."INNER JOIN itens as i ON i.id = pm.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pm.idPersonagem "
              ."INNER JOIN usuarios as u ON u.id = up.idUsuario "
              ."WHERE pm.idItem = $id ";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT pm.*, i.foto, i.nome as nome_item, u.username, u.foto as foto_usuario, up.idUsuario, up.nome as nome_personagem "
              ."FROM personagens_mercado_solicitacoes as pm "
              ."INNER JOIN itens as i ON i.id = pm.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pm.idPersonagem "
              ."INNER JOIN usuarios as u ON u.id = up.idUsuario "
              ."WHERE pm.idItem = $id "
              . "ORDER BY pm.golds ASC, pm.coins ASC "
              . "LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {

                $row .= '<div class="market-itens-body">
                            <div class="td-market nome">
                                <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                <div class="market_listing_item_name_block">
                                    <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome_item.'</span>
                                    <br>
                                    <span class="market_listing_game_name">DB Heroes</span>
                                </div>
                            </div>
                            <div class="td-market vendedor">
                                <img src="'.BASE.$value->foto_usuario.'" alt="'.$value->username.'" title="'.$value->username.'" />
                                <span>'.$value->nome_personagem.'</span>
                            </div>
                            <div class="td-market preco">
                                <div class="market_listing_right_cell market_listing_their_price">
                                    <span class="market_table_value normal_price">';
                                        if($value->golds != ''){
                                            $row .= '<span class="normal_price" data-price="181" data-currency="7">'.$value->golds.' Golds</span>';
                                        }
                                        
                                        if($value->coins != ''){
                                            $row .= '<span class="normal_price" data-price="181" data-currency="7">'.$value->coins.' Coins</span>';
                                        }
                                    $row .= '</span>
                                </div>
                            </div>
                            <div class="td-market acoes">';
                                if(($graduacao < $value->graduacao_inicial)){
                                    $row .= '<span style="display: block; width: 90%; font-size: 11px; color: #7FC900; margin: 20px auto 0 auto; word-break: break-word;">Liberado na Graduação</span>'
                                            . '<span style="display: block; width: 90%; font-size: 11px; color: #7FC900; margin: 0 auto; word-break: break-word;">'.$personagem->getGraduacaoTextoByID($graduacao).'</span>';
                                } else if($idPersonagem != $value->idPersonagem){
                                    $row .= '<a href="'.BASE.'market/vender-item/'.$value->id.'" class="item_market_action_button btn_green_white_innerfade btn_small">
                                                <span>Vender Agora</span>
                                            </a>';
                                } else {
                                    $row .= '<span style="display: block; margin-top: 25px;">Meu Item</span>';
                                }
                            $row .= '</div>
                        </div>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item à venda</div>
                     </div>';
        }
        
        echo $row;
    }
    
    public function getListItensAnunciados($idUsuario, $pc, $qtd_resultados, $idPersonagem){
        $pager = new Paginator();
        $personagem = new Personagens();
        
        $sql = "SELECT count(*) as total FROM personagens_mercado WHERE idPersonagem = $idPersonagem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $ti = $stmt->fetch();
        
        $counter = $ti->total;
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT pm.*, i.foto, i.nome as nome_item, u.username, u.foto as foto_usuario, up.idUsuario "
              ."FROM personagens_mercado as pm "
              ."INNER JOIN itens as i ON i.id = pm.idItem "
              ."INNER JOIN usuarios_personagens as up ON up.id = pm.idPersonagem "
              ."INNER JOIN usuarios as u ON u.id = up.idUsuario "
              ."WHERE pm.vendido = 0 "
              ."AND pm.idPersonagem = $idPersonagem LIMIT " . $inicio . ',' . $qtd_resultados;
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {

                $row .= '<div class="market-itens-body">
                            <div class="td-market nome">
                                <img id="result_0_image" src="'.BASE.'assets/'.$value->foto.'" srcset="'.BASE.'assets/'.$value->foto.'" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                <div class="market_listing_item_name_block">
                                    <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">'.$value->nome_item.'</span>
                                    <br>
                                    <span class="market_listing_game_name">DB Heroes</span>
                                </div>
                            </div>
                            <div class="td-market vendedor">
                                <img src="'.BASE.$value->foto_usuario.'" alt="'.$value->username.'" />
                                <span>'.$value->username.'</span>
                            </div>
                            <div class="td-market preco">
                                <div class="market_listing_right_cell market_listing_their_price">
                                    <span class="market_table_value normal_price">
                                        <span class="normal_price" data-price="181" data-currency="7">'.$value->valor.'</span>
                                    </span>
                                </div>
                            </div>
                            <div class="td-market acoes">';
                                $row .= '<a href="'.BASE.'market/retirar/'.$value->id.'" class="item_market_action_button btn_red_white_innerfade btn_small">
                                            <span>Retirar Item</span>
                                         </a>';
                            $row .= '</div>
                        </div>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<div>'
                   .$pager->paginar($pc, $tp)
                  . '</div>';
        } else {
            $row .= '<div class="market-itens-body">
                        <div class="not-item">Nenhum item à venda</div>
                     </div>';
        }
        
        echo $row;
    }
    
    public function getItemMenorValor($idItem){        
        $sql = "SELECT MIN(valor) as valor FROM personagens_mercado WHERE idItem = $idItem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->valor;
    }
    
    public function getItemMenorValorS($idItem){        
        $sql = "SELECT MIN(golds) as valor, MIN(coins) as valor_coins FROM personagens_mercado_solicitacoes WHERE idItem = $idItem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($item->valor != ''){
            return $item->valor.'<span> Golds</span>';
        }
        
        if($item->valor_coins != ''){
            return $item->valor_coins.'<span> Coins</span>';
        }
    }
    
    public function getCountItens($idItem){        
        $sql = "SELECT count(*) as total FROM personagens_mercado WHERE idItem = $idItem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getCountItensS($idItem){        
        $sql = "SELECT count(*) as total FROM personagens_mercado_solicitacoes WHERE idItem = $idItem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item->total;
    }
    
    public function getExistItem($id){        
        $sql = "SELECT * FROM personagens_mercado WHERE id = $id";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getExistItemS($id){        
        $sql = "SELECT * FROM personagens_mercado_solicitacoes WHERE id = $id";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getExistItemInInventario($idItem, $idPersonagem){        
        $sql = "SELECT * FROM personagens_inventario_itens WHERE idItem = $idItem AND idPersonagem = $idPersonagem";
                    
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getListAllItens(){        
        $sql = "SELECT * FROM itens WHERE status = 1";   
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $all_itens = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($all_itens as $chave_itens => $t_itens) {
            $row .= '<option value="'.$t_itens->id.'">'.$t_itens->nome.'</option>';
        }
        
        return $row;
    }
}
