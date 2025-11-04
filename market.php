<?php switch($acao) {
    default: ?>
        <?php
            $idPersonagem = $_SESSION["PERSONAGEMID"];
            
            $filtro_busca = '';
            
            if(isset($_POST['search'])){
                $filtro_busca = addslashes($_POST['search']);
            }
            
            if(isset($_POST['vender'])){ 
                if($core->isExists('personagens_inventario_itens', "WHERE id = ".addslashes($_POST['idVenda']))){
                    if(addslashes($_POST['valor']) > 0){
                        $dadosItem = $core->getDados('itens', 'WHERE id = '.addslashes($_POST['id']));

                        $campos_add = array(
                            'idItem' => addslashes($_POST['id']),
                            'valor' => addslashes($_POST['valor']),
                            'idPersonagem' => $idPersonagem
                        );

                        $core->insert('personagens_mercado', $campos_add);

                        if($core->delete('personagens_inventario_itens', "id = ".addslashes($_POST['idVenda']))){
                            $core->msg('sucesso', 'Item Anunciado.');
                        } else {
                            $core->msg('error', 'Erro ao anunciar item.');
                        }
                    } else {
                        $core->msg('error', 'Insira um valor para a venda');
                    }
                } else {
                    $core->msg('error', 'Erro ao buscar item.');
                }
            }
            
            if(isset($_POST['vender_coins'])){ 
                if(addslashes($_POST['coins']) <= $user->coins){
                    if(addslashes($_POST['coins']) > 0){
                        if(addslashes($_POST['golds']) > 0){
                            
                            $campos = array(
                                'coins' => intval($user->coins) - intval(addslashes($_POST['coins']))
                            );

                            $where = 'id = "'.$user->id.'"';

                            if($core->update('usuarios', $campos, $where)){
                                $campos_add = array(
                                    'idUsuario' => $user->id,
                                    'idPersonagem' => $idPersonagem,
                                    'coins' => addslashes($_POST['coins']),
                                    'golds' => addslashes($_POST['golds']),
                                    'tipo' => 1
                                );

                                if($core->insert('personagens_cambio', $campos_add)){
                                    $core->msg('sucesso', 'Coins inseridos no mercado para venda.');
                                    header('Location: '.BASE.'market');
                                } else {
                                    $core->msg('error', 'Erro ao vender coins');
                                }
                            } else {
                                $core->msg('error', 'Erro ao vender coins');
                            }
                        } else {
                            $core->msg('error', 'O valor de Golds não pode estar zerado');
                        }
                    } else {
                        $core->msg('error', 'O valor de Coins não pode estar zerado');
                    }
                } else {
                    $core->msg('error', 'Quantidade de Coins Indisponível.');
                }
            }
            
            if(isset($_POST['vender_golds'])){ 
                if(addslashes($_POST['golds']) <= $personagem->gold){
                    if(addslashes($_POST['golds']) > 0){
                        if(addslashes($_POST['coins']) > 0){
                            
                            $campos = array(
                                'gold' => intval($personagem->gold) - intval(addslashes($_POST['golds']))
                            );

                            $where = 'id = "'.$idPersonagem.'"';

                            if($core->update('usuarios_personagens', $campos, $where)){
                                $campos_add = array(
                                    'idUsuario' => $user->id,
                                    'idPersonagem' => $idPersonagem,
                                    'coins' => addslashes($_POST['coins']),
                                    'golds' => addslashes($_POST['golds']),
                                    'tipo' => 2
                                );

                                if($core->insert('personagens_cambio', $campos_add)){
                                    $core->msg('sucesso', 'Golds inseridos no mercado para venda.');
                                    header('Location: '.BASE.'market');
                                } else {
                                    $core->msg('error', 'Erro ao vender golds');
                                }
                            } else {
                                $core->msg('error', 'Erro ao vender golds');
                            }
                        } else {
                            $core->msg('error', 'O valor de Golds não pode estar zerado');
                        }
                    } else {
                        $core->msg('error', 'O valor de Coins não pode estar zerado');
                    }
                } else {
                    $core->msg('error', 'Quantidade de Golds Indisponível.');
                }
            }
            
            if(isset($_POST['retirar_coins'])){ 
                $campos = array(
                    'coins' => intval($user->coins) + intval(addslashes($_POST['coins']))
                );

                $where = 'id = "'.$user->id.'"';

                if($core->update('usuarios', $campos, $where)){
                    if($core->delete('personagens_cambio', "id = ".addslashes($_POST['idVenda']))){
                        $core->msg('sucesso', 'Valor retirado do Mercado.');
                        header('Location: '.BASE.'market');
                    } else {
                        $core->msg('error', 'Erro ao retirar coins.');
                    }
                } else {
                    $core->msg('error', 'Erro ao retirar coins.');
                }
            }
            
            if(isset($_POST['retirar_golds'])){ 
                $campos = array(
                    'gold' => intval($personagem->gold) + intval(addslashes($_POST['golds']))
                );

                $where = 'id = "'.$personagem->id.'"';

                if($core->update('usuarios_personagens', $campos, $where)){
                    if($core->delete('personagens_cambio', "id = ".addslashes($_POST['idVenda']))){
                        $core->msg('sucesso', 'Valor retirado do Mercado.');
                        header('Location: '.BASE.'market');
                    } else {
                        $core->msg('error', 'Erro ao retirar golds.');
                    }
                } else {
                    $core->msg('error', 'Erro ao retirar golds.');
                }
            }
            
            if(isset($_POST['retirar_solicitacao_gold'])){ 
                $campos = array(
                    'gold' => intval($personagem->gold) + intval(addslashes($_POST['golds']))
                );

                $where = 'id = "'.$idPersonagem.'"';

                if($core->update('usuarios_personagens', $campos, $where)){
                    if($core->delete('personagens_mercado_solicitacoes', "id = ".addslashes($_POST['idVenda']))){
                        $core->msg('sucesso', 'Solicitação retirada do Mercado.');
                        header('Location: '.BASE.'market');
                    } else {
                        $core->msg('error', 'Erro ao retirar solicitação.');
                    }
                } else {
                    $core->msg('error', 'Erro ao retirar solicitação.');
                }
            }
            
            if(isset($_POST['retirar_solicitacao_coin'])){ 
                $campos = array(
                    'coins' => intval($user->coins) + intval(addslashes($_POST['coins']))
                );

                $where = 'id = "'.$user->id.'"';

                if($core->update('usuarios', $campos, $where)){
                    if($core->delete('personagens_mercado_solicitacoes', "id = ".addslashes($_POST['idVenda']))){
                        $core->msg('sucesso', 'Solicitação retirada do Mercado.');
                        header('Location: '.BASE.'market');
                    } else {
                        $core->msg('error', 'Erro ao retirar solicitação.');
                    }
                } else {
                    $core->msg('error', 'Erro ao retirar solicitação.');
                }
            }
            
            if(isset($_POST['comprar_coins'])){ 
                if(addslashes($_POST['golds']) <= $personagem->gold){
                    $dados = $core->getDados('personagens_cambio', "WHERE id = ".addslashes($_POST['idVenda']));
                    
                    $campos = array(
                        'coins' => intval($user->coins) + intval(addslashes($_POST['coins']))
                    );

                    $where = 'id = "'.$user->id.'"';

                    if($core->update('usuarios', $campos, $where)){

                        $campos = array(
                            'gold' => intval($personagem->gold) - intval(addslashes($_POST['golds']))
                        );

                        $where = 'id = "'.$idPersonagem.'"';

                        if($core->update('usuarios_personagens', $campos, $where)){
                            
                            $dadosVendedor = $core->getDados('usuarios_personagens', "WHERE id = ".$dados->idPersonagem);
                            
                            $campos = array(
                                'gold' => intval($dadosVendedor->gold) + intval(addslashes($_POST['golds']))
                            );

                            $where = 'id = "'.$dados->idPersonagem.'"';
                            
                            if($core->update('usuarios_personagens', $campos, $where)){
                                if($core->delete('personagens_cambio', "id = ".addslashes($_POST['idVenda']))){
                                    $core->msg('sucesso', 'Compra de Coins realizada');
                                    header('Location: '.BASE.'market');
                                } else {
                                    $core->msg('error', 'Erro ao comprar coins.');
                                }
                            } else {
                                $core->msg('error', 'Erro ao comprar coins.');
                            }
                        } else {
                            $core->msg('error', 'Erro ao comprar coins.');
                        }
                    } else {
                        $core->msg('error', 'Erro ao comprar coins.');
                    }
                } else {
                    $core->msg('error', 'Golds insuficientes para compra.');
                }
            }
            
            if(isset($_POST['comprar_golds'])){ 
                if(addslashes($_POST['coins']) <= $user->coins){
                    $dados = $core->getDados('personagens_cambio', "WHERE id = ".addslashes($_POST['idVenda']));
                    
                    $campos = array(
                        'gold' => intval($personagem->gold) + intval(addslashes($_POST['golds']))
                    );

                    $where = 'id = "'.$idPersonagem.'"';

                    if($core->update('usuarios_personagens', $campos, $where)){

                        $campos = array(
                            'coins' => intval($user->coins) - intval(addslashes($_POST['coins']))
                        );

                        $where = 'id = "'.$user->id.'"';

                        if($core->update('usuarios', $campos, $where)){
                            
                            $dadosVendedor = $core->getDados('usuarios', "WHERE id = ".$dados->idUsuario);
                            
                            $campos = array(
                                'coins' => intval($dadosVendedor->coins) + intval(addslashes($_POST['coins']))
                            );

                            $where = 'id = "'.$dadosVendedor->id.'"';
                            
                            if($core->update('usuarios', $campos, $where)){
                                if($core->delete('personagens_cambio', "id = ".addslashes($_POST['idVenda']))){
                                    $core->msg('sucesso', 'Compra de Golds realizada');
                                    header('Location: '.BASE.'market');
                                } else {
                                    $core->msg('error', 'Erro ao comprar golds.');
                                }
                            } else {
                                $core->msg('error', 'Erro ao comprar golds.');
                            }
                        } else {
                            $core->msg('error', 'Erro ao comprar golds.');
                        }
                    } else {
                        $core->msg('error', 'Erro ao comprar golds.');
                    }
                } else {
                    $core->msg('error', 'Golds insuficientes para compra.');
                }
            }
            
            if(isset($_POST['retirar'])){ 
                if($core->isExists('personagens_mercado', "WHERE id = ".addslashes($_POST['idVenda']))){
                    $dadosItem = $core->getDados('itens', 'WHERE id = '.addslashes($_POST['id']));
        
                    if($inventario->verificaItemIgual($dadosItem->nome, $idPersonagem)){
                        $slot_recebido = $inventario->verificaItemIgual($dadosItem->nome, $idPersonagem);
                    }

                    $campos_add = array(
                        'idItem' => $dadosItem->id,
                        'idSlot' => $slot_recebido,
                        'idPersonagem' => $idPersonagem
                    );
                    
                    $core->insert('personagens_inventario_itens', $campos_add);

                    if($core->delete('personagens_mercado', "id = ".addslashes($_POST['idVenda']))){
                        $core->msg('sucesso', 'Item Retirado do Mercado.');
                    } else {
                        $core->msg('error', 'Erro ao retirar item.');
                    }
                } else {
                    $core->msg('error', 'Erro ao buscar item.');
                }
            }
            
            if(isset($_POST['solicitar_venda_gold'])){ 
                if(addslashes($_POST['golds']) > 0){
                    if($personagem->gold >= addslashes($_POST['golds'])){
                        $campos = array(
                            'gold' => intval($personagem->gold) - intval(addslashes($_POST['golds']))
                        );

                        $where = 'id = "'.$personagem->id.'"';

                        if($core->update('usuarios_personagens', $campos, $where)){
                            $campos_add = array(
                                'idUsuario' => $user->id,
                                'idPersonagem' => $idPersonagem,
                                'idItem' => addslashes($_POST['idItem']),
                                'golds' => addslashes($_POST['golds']),
                                'tipo' => 1
                            );

                            if($core->insert('personagens_mercado_solicitacoes', $campos_add)){
                                $core->msg('sucesso', 'Solicitação de Item inserida no Mercado.');
                                header('Location: '.BASE.'market');
                            } else {
                                $core->msg('error', 'Erro ao solicitar Item');
                            }
                        } else {
                            $core->msg('error', 'Erro ao solicitar Item');
                        }
                    } else {
                        $core->msg('error', 'Gold Insuficiente');
                    }
                } else {
                    $core->msg('error', 'O valor de Golds não pode estar zerado');
                }
            }
            
            if(isset($_POST['solicitar_venda_coin'])){ 
                if(addslashes($_POST['coins']) > 0){
                    if($user->coins >= addslashes($_POST['coins'])){
                        $campos = array(
                            'coins' => intval($user->coins) - intval(addslashes($_POST['coins']))
                        );

                        $where = 'id = "'.$user->id.'"';

                        if($core->update('usuarios', $campos, $where)){
                            $campos_add = array(
                                'idUsuario' => $user->id,
                                'idPersonagem' => $idPersonagem,
                                'idItem' => addslashes($_POST['idItem']),
                                'coins' => addslashes($_POST['coins']),
                                'tipo' => 2
                            );

                            if($core->insert('personagens_mercado_solicitacoes', $campos_add)){
                                $core->msg('sucesso', 'Solicitação de Item inserida no Mercado.');
                                header('Location: '.BASE.'market');
                            } else {
                                $core->msg('error', 'Erro ao solicitar Item');
                            }
                        } else {
                            $core->msg('error', 'Erro ao solicitar Item');
                        }
                    } else {
                        $core->msg('error', 'Coin Insuficiente');
                    }
                } else {
                    $core->msg('error', 'O valor de Golds não pode estar zerado');
                }
            }
        ?>
        <div class="market_header_bg">
            <div id="BG_top">
                <h2 class="market_header">
                    <a href="<?php echo BASE; ?>market/">
                        <div class="market_header_logo">
                            <div class="market_header_text">
                                <span class="market_title_text">Mercado da Comunidade</span><br>
                                <span class="market_subtitle_text">Compre e venda itens a membros da comunidade com o saldo da Carteira.</span>
                            </div>
                        </div>
                    </a>
                </h2>

                <div class="user_info">
                    <span class="iconHolder_offline" style="display: inline-block">
                        <span class="avatarIcon">
                            <img src="<?php echo BASE.$user->foto; ?>" srcset="<?php echo BASE.$user->foto; ?>">
                        </span>
                    </span>
                    <span class="user_info_text">
                        <a id="marketWalletBalance" href="<?php echo BASE; ?>profile">Saldo na Carteira: <span id="marketWalletBalanceAmount"><?php echo $personagem->gold; ?> golds</span></a>
                        <br>
                        <a href="<?php echo BASE; ?>inventario">Ver inventário</a>
                        <a style="color: #AACC08; padding-left: 10px;" href="<?php echo BASE; ?>market/anunciados">Meus Itens Anunciados</a>
                    </span>
                </div>

            </div>
        </div>

        <ul class="tabs-mercado">
            <li class="active">
                <a data-url="solicitacoes">
                    <i class="fas fa-folder-plus"></i> Solicitações de Itens
                </a>
            </li>
            <li>
                <a data-url="mercado">
                    <i class="fas fa-shopping-cart"></i> Mercado de Itens
                </a>
            </li>
            <li>
                <a data-url="valores">
                    <i class="fas fa-coins"></i> Mercado de Moedas
                </a>
            </li>
            <li class="vender">
                <a data-url="cambio">
                    <i class="fas fa-coins"></i> Câmbio de Moedas
                </a>
            </li>
            <li class="vender">
                <a data-url="vender">
                    <i class="fas fa-cart-arrow-down"></i> Vender
                </a>
            </li>
            <li class="comprar">
                <a data-url="comprar">
                    <i class="far fa-credit-card"></i> Comprar
                </a>
            </li>
        </ul>

        <div class="tab-mercado-content">
            <div class="tab-mercado-item" id="mercado">
                <h3 class="subtitle">Itens disponíveis para Compra</h3>
        
                <div class="market-itens">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market quantidade">
                            <span>Quantidade</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Preço</span>
                        </div>
                    </div>

                    <?php $mercado->getList($pc, 10, $filtro_busca); ?>
                </div>

                <div class="market_search_sidebar_contents">
                    <span class="market_search_sidebar_section_tip_small">
                        Buscar itens					
                    </span>
                    <div class="market_search_box_container">
                        <form id="market_search" action="" method="post">
                            <span class="market_search_sidebar_search_box market_search_input_container">
                                <span>
                                    <input class="" type="text" id="findItemsSearchBox" value="" placeholder="Buscar" name="search" autocomplete="off" tabindex="1">
                                    <input class="market_search_submit_button" id="findItemsSearchSubmit" type="submit" value="Enviar" tabindex="3">
                                </span>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="tab-mercado-item active" id="solicitacoes">
                <h3 class="subtitle">Itens Solicitados</h3>
        
                <div class="market-itens">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market quantidade">
                            <span>Quantidade</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Preço</span>
                        </div>
                    </div>

                    <?php $mercado->getListS($pc, 10, $filtro_busca); ?>
                </div>

                <div class="market_search_sidebar_contents">
                    <span class="market_search_sidebar_section_tip_small">
                        Buscar itens					
                    </span>
                    <div class="market_search_box_container">
                        <form id="market_search" action="" method="post">
                            <span class="market_search_sidebar_search_box market_search_input_container">
                                <span>
                                    <input class="" type="text" id="findItemsSearchBox" value="" placeholder="Buscar" name="search" autocomplete="off" tabindex="1">
                                    <input class="market_search_submit_button" id="findItemsSearchSubmit" type="submit" value="Enviar" tabindex="3">
                                </span>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="tab-mercado-item" id="valores">
                <h3 class="subtitle">Coins disponíveis para troca</h3>
                
                <div class="market-itens-cambio">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market coins">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <?php $mercado->getListCambioAll($pc, 10, $user->id); ?> 
                </div>
                
                <h3 class="subtitle">Golds disponíveis para troca</h3>
                
                <div class="market-itens-cambio">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market coins">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <?php $mercado->getListCambioAllGold($pc, 10, $user->id); ?> 
                </div>
            </div>
            
            <div class="tab-mercado-item" id="cambio">
                <h3 class="subtitle">Câmbio de Moedas (Trocar)</h3>

                <div class="market-itens-cambio">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market coins">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>

                    <?php if($user->coins > 0){ ?>
                        <div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="<?php echo BASE; ?>assets/icones/coin.png" srcset="<?php echo BASE; ?>assets/icones/coin.png" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">Trocar Coin por Gold</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market coins">
                                    <input type="text" name="coins" value="" placeholder="0" />
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="golds" value="" placeholder="0" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="submit" name="vender_coins" class="bt-vender" value="Trocar" />
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
                        <div class="market-itens-body">
                            <div class="not-item">Você não tem coins disponível para venda</div>
                        </div>
                    <?php } ?>
                </div>
                
                <div class="market-itens-cambio">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market coins">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>

                    <?php if($personagem->gold > 0){ ?>
                        <div class="market-itens-body">
                            <form action="" method="post">
                                <div class="td-market nome">
                                    <img id="result_0_image" src="<?php echo BASE; ?>assets/icones/gold.png" srcset="<?php echo BASE; ?>assets/icones/gold.png" style="border-color: #D2D2D2;" class="market_listing_item_img" alt="">
                                    <div class="market_listing_item_name_block">
                                        <span id="result_0_name" class="market_listing_item_name" style="color: #D2D2D2;">Trocar Gold por Coin</span>
                                        <br>
                                        <span class="market_listing_game_name">DB Heroes</span>
                                    </div>
                                </div>
                                <div class="td-market coins">
                                    <input type="text" name="golds" value="" placeholder="0" />
                                </div>
                                <div class="td-market preco">
                                    <input type="text" name="coins" value="" placeholder="0" />
                                </div>
                                <div class="td-market acoes">
                                    <input type="submit" name="vender_golds" class="bt-vender" value="Trocar" />
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
                        <div class="market-itens-body">
                            <div class="not-item">Você não tem golds disponível para venda</div>
                        </div>
                    <?php } ?>
                </div>
                
                <h3 style="margin-top: 50px;" class="subtitle">Meus Coins à Venda</h3>

                <div class="market-itens-cambio">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market coins">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <?php $mercado->getListCambio($user->id, $idPersonagem, $pc, 5); ?> 
                </div>
                
                <h3 style="margin-top: 50px;" class="subtitle">Meus Golds à Venda</h3>
                
                <div class="market-itens-cambio">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market coins">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <?php $mercado->getListCambioGold($user->id, $idPersonagem, $pc, 5); ?> 
                </div>
            </div>
            
            <div class="tab-mercado-item" id="vender">
                <h3 style="margin-top: 50px;" class="subtitle">Itens do Meu Inventário</h3>

                <div class="market-itens-inventario">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Preço</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>

                    <?php $mercado->getListInventario($idPersonagem, $pc, 5); ?>              
                </div>

                <h3 class="subtitle">Meus Itens à Venda</h3>

                <div class="market-itens-inventario">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Preço</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>

                    <?php $mercado->getItensAVenda($idPersonagem, $pc, 5); ?>              
                </div>
            </div>
            
            <div class="tab-mercado-item" id="comprar">
                <h3 class="subtitle">Solicitar Item por Gold</h3>

                <div class="market-itens-compra">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Item</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <div class="market-itens-body">
                        <form action="" method="post">
                            <div class="td-market nome">
                                <select name="idItem">
                                    <option value="" disabled placeholder="Selecione o Item..." />
                                    <?php echo $mercado->getListAllItens(); ?>
                                </select>
                            </div>
                            <div class="td-market preco">
                                <input type="text" name="golds" value="" placeholder="0" />
                            </div>
                            <div class="td-market acoes">
                                <input type="submit" name="solicitar_venda_gold" class="bt-vender" value="Solicitar" />
                            </div>
                        </form>
                    </div>
                </div>
                
                <h3 style="margin-top: 50px;" class="subtitle">Solicitar Item por Coin</h3>

                <div class="market-itens-compra">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Item</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <div class="market-itens-body">
                        <form action="" method="post">
                            <div class="td-market nome">
                                <select name="idItem">
                                    <option value="" disabled placeholder="Selecione o Item..." />
                                    <?php echo $mercado->getListAllItens(); ?>
                                </select>
                            </div>
                            <div class="td-market preco">
                                <input type="text" name="coins" value="" placeholder="0" />
                            </div>
                            <div class="td-market acoes">
                                <input type="submit" name="solicitar_venda_coin" class="bt-vender" value="Solicitar" />
                            </div>
                        </form>
                    </div>
                </div>
                
                <h3 style="margin-top: 50px;" class="subtitle">Itens Solicitados por Gold</h3>

                <div class="market-itens-solicitacoes">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Golds</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <?php $mercado->getListSolicitacoesGold($user->id, $idPersonagem, $pc, 5); ?> 
                </div>
                
                <h3 style="margin-top: 30px;" class="subtitle">Itens Solicitados por Coin</h3>

                <div class="market-itens-solicitacoes">
                    <div class="market-itens-header">
                        <div class="tag-market nome">
                            <span>Nome</span>
                        </div>
                        <div class="tag-market preco">
                            <span>Quantidade de Coins</span>
                        </div>
                        <div class="tag-market acoes">

                        </div>
                    </div>
                    
                    <?php $mercado->getListSolicitacoesCoin($user->id, $idPersonagem, $pc, 5); ?> 
                </div>
            </div>
        </div>
    <?php break; ?>

    <?php case 'view': ?>
        <?php
            $idPersonagem = $_SESSION["PERSONAGEMID"];
            $id = Url::getURL(2);
            if($id != 'ajax'){
                $dados = $core->getDados('itens', 'WHERE id = '.$id);
            }
        ?>
        
        <ul class="tabs-mercado">
            <li class="active">
                <a href="<?php echo BASE; ?>market">
                    <i class="fas fa-arrow-left"></i> Voltar para o Mercado
                </a>
            </li>
        </ul>

        <div class="market-item-info">
            <h2 class="nome-item"><?php echo $dados->nome; ?></h2>
            
            <div class="market_listing_largeimage">
                <img src="<?php echo BASE.'assets/'.$dados->foto; ?>" />
            </div>
            <div id="largeiteminfo">
                <div class="inventory_iteminfo hover_box" id="largeiteminfo_clienthover" style="border-color: rgb(210, 210, 210);">
                    <div class="item_desc_content app730 context2" id="largeiteminfo_content">
                        <div class="item_desc_description">
                            <div class="item_desc_game_info" id="largeiteminfo_game_info">
                                <?php if($dados->bau == 0){ ?>
                                    <div class="informacoes-item">
                                        <h4>Informações do Item</h4>
                                        <?php 
                                            if($dados->tipo == 1 || $dados->tipo == 3 || $dados->tipo == 4){
                                                echo '<h4>Item Consumível</h4>';
                                                $percent = '% de recuperação';
                                            } else {
                                                $percent = '';
                                            }

                                            if($dados->hp > 0){
                                                echo '<p><strong>HP:</strong>+ '.$dados->hp.$percent.'</p>';
                                            }

                                            if($dados->mana > 0){
                                                echo '<p><strong>KI:</strong>+ '.$dados->mana.$percent.'</p>';
                                            }

                                            if($dados->energia > 0){
                                                echo '<p><strong>Energia:</strong>+ '.$dados->energia.'</p>';
                                            }

                                            if($dados->forca > 0){
                                                echo '<p><strong>Força:</strong>+ '.$dados->forca.'</p>';
                                            }

                                            if($dados->agilidade > 0){
                                                echo '<p><strong>Agilidade:</strong>+ '.$dados->agilidade.'</p>';
                                            }

                                            if($dados->habilidade > 0){
                                                echo '<p><strong>Habilidade:</strong>+ '.$dados->habilidade.'</p>';
                                            }

                                            if($dados->resistencia > 0){
                                                echo '<p><strong>Resistência:</strong>+ '.$dados->resistencia.'</p>';
                                            }

                                            if($dados->sorte > 0){
                                                echo '<p><strong>Sorte:</strong>+ '.$dados->sorte.'</p>';
                                            }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="item_desc_descriptors" id="largeiteminfo_item_descriptors">
                                <div class="descriptor"><?php echo $dados->descricao; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if($dados->bau == 1){ ?>
            <div class="itens-do-bau">
                <h3 class="infoItens">Informações sobre os itens do Baú</h3>
                <?php echo $loja->getDadosItensBau($dados->id); ?>
            </div>
        <?php } ?>
        
        <div id="largeiteminfo_warning">
            <p>Após a compra, este item:</p>
            <p>não será trocável por uma semana</p>
            <p>pode ser revendido no Mercado da Comunidade imediatamente</p>
        </div>

        <div class="market-itens-vendedor">
            <h3 class="subtitle">Itens Anunciados</h3>
            <div class="market-itens-header">
                <div class="tag-market nome">
                    <span>Nome</span>
                </div>
                <div class="tag-market vendedor">
                    <span>Vendedor</span>
                </div>
                <div class="tag-market preco">
                    <span>Preço</span>
                </div>
                <div class="tag-market acoes">
                    
                </div>
            </div>

            <?php $mercado->getListItens($id, $user->id, $pc, 20, $personagem->graduacao_id, $idPersonagem); ?>
        </div>
    <?php break; ?>

    <?php case 'view_solicitacoes': ?>
        <?php
            $idPersonagem = $_SESSION["PERSONAGEMID"];
            $id = Url::getURL(2);
            if($id != 'ajax'){
                $dados = $core->getDados('itens', 'WHERE id = '.$id);
            }
        ?>

        <ul class="tabs-mercado">
            <li class="active">
                <a href="<?php echo BASE; ?>market">
                    <i class="fas fa-arrow-left"></i> Voltar para o Mercado
                </a>
            </li>
        </ul>

        <div class="market-item-info">
            <h2 class="nome-item"><?php echo $dados->nome; ?></h2>
            
            <div class="market_listing_largeimage">
                <img src="<?php echo BASE.'assets/'.$dados->foto; ?>" />
            </div>
            <div id="largeiteminfo">
                <div class="inventory_iteminfo hover_box" id="largeiteminfo_clienthover" style="border-color: rgb(210, 210, 210);">
                    <div class="item_desc_content app730 context2" id="largeiteminfo_content">
                        <div class="item_desc_description">
                            <div class="item_desc_game_info" id="largeiteminfo_game_info">
                                
                                <?php if($dados->bau == 0){ ?>
                                    <div class="informacoes-item">
                                        <h4>Informações do Item</h4>
                                        <?php 
                                            if($dados->tipo == 1 || $dados->tipo == 3 || $dados->tipo == 4){
                                                echo '<h4>Item Consumível</h4>';
                                                $percent = '% de recuperação';
                                            } else {
                                                $percent = '';
                                            }

                                            if($dados->hp > 0){
                                                echo '<p><strong>HP:</strong>+ '.$dados->hp.$percent.'</p>';
                                            }

                                            if($dados->mana > 0){
                                                echo '<p><strong>KI:</strong>+ '.$dados->mana.$percent.'</p>';
                                            }

                                            if($dados->energia > 0){
                                                echo '<p><strong>Energia:</strong>+ '.$dados->energia.'</p>';
                                            }

                                            if($dados->forca > 0){
                                                echo '<p><strong>Força:</strong>+ '.$dados->forca.'</p>';
                                            }

                                            if($dados->agilidade > 0){
                                                echo '<p><strong>Agilidade:</strong>+ '.$dados->agilidade.'</p>';
                                            }

                                            if($dados->habilidade > 0){
                                                echo '<p><strong>Habilidade:</strong>+ '.$dados->habilidade.'</p>';
                                            }

                                            if($dados->resistencia > 0){
                                                echo '<p><strong>Resistência:</strong>+ '.$dados->resistencia.'</p>';
                                            }

                                            if($dados->sorte > 0){
                                                echo '<p><strong>Sorte:</strong>+ '.$dados->sorte.'</p>';
                                            }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="item_desc_descriptors" id="largeiteminfo_item_descriptors">
                                <div class="descriptor"><?php echo $dados->descricao; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if($dados->bau == 1){ ?>
            <div class="itens-do-bau">
                <h3 class="infoItens">Informações sobre os itens do Baú</h3>
                <?php echo $loja->getDadosItensBau($dados->id); ?>
            </div>
        <?php } ?>
        
        <div id="largeiteminfo_warning">
            <p>Após a compra, este item:</p>
            <p>não será trocável por uma semana</p>
            <p>pode ser revendido no Mercado da Comunidade imediatamente</p>
        </div>

        <div class="market-itens-vendedor">
            <h3 class="subtitle">Itens Solicitados</h3>
            <div class="market-itens-header">
                <div class="tag-market nome">
                    <span>Nome</span>
                </div>
                <div class="tag-market vendedor">
                    <span>Comprador</span>
                </div>
                <div class="tag-market preco">
                    <span>Preço</span>
                </div>
                <div class="tag-market acoes">
                    
                </div>
            </div>

            <?php
                if($id != 'ajax'){
                    $mercado->getListItensS($id, $user->id, $pc, 20, $personagem->graduacao_id, $idPersonagem); 
                }
            ?>
        </div>
    <?php break; ?>
        
    <?php case 'anunciados': ?>
        <?php 
            $idPersonagem = $_SESSION["PERSONAGEMID"];
        ?>
        
        <div class="market_header_bg">
            <div id="BG_top">
                <h2 class="market_header">
                    <a href="<?php echo BASE; ?>market/">
                        <div class="market_header_logo">
                            <div class="market_header_text">
                                <span class="market_title_text">Mercado da Comunidade</span><br>
                                <span class="market_subtitle_text">Compre e venda itens a membros da comunidade com o saldo da Carteira.</span>
                            </div>
                        </div>
                    </a>
                </h2>

                <div class="user_info">
                    <span class="iconHolder_offline" style="display: inline-block">
                        <span class="avatarIcon">
                            <img src="<?php echo BASE.$user->foto; ?>" srcset="<?php echo BASE.$user->foto; ?>">
                        </span>
                    </span>
                    <span class="user_info_text">
                        <a id="marketWalletBalance" href="<?php echo BASE; ?>profile">Saldo na Carteira: <span id="marketWalletBalanceAmount"><?php echo $personagem->gold; ?> golds</span></a>
                        <br>
                        <a href="<?php echo BASE; ?>inventario">Ver inventário</a>
                        <a style="color: #AACC08; padding-left: 10px;" href="<?php echo BASE; ?>market/anunciados">Meus Itens Anunciados</a>
                    </span>
                </div>

            </div>
        </div>
        
        <div class="market-itens-vendedor">
            <h3 class="subtitle">Meus Itens Anunciados</h3>
            <div class="market-itens-header">
                <div class="tag-market nome">
                    <span>Nome</span>
                </div>
                <div class="tag-market vendedor">
                    <span>Vendedor</span>
                </div>
                <div class="tag-market preco">
                    <span>Preço</span>
                </div>
                <div class="tag-market acoes">
                    
                </div>
            </div>

            <?php $mercado->getListItensAnunciados($user->id, $pc, 20, $idPersonagem); ?>
        </div>
    <?php break; ?>
        
    <?php case 'retirar': ?>
        <?php 
            $idPersonagem = $_SESSION["PERSONAGEMID"];
            $id = Url::getURL(2);
            
            $dadosAnunciado = $core->getDados('personagens_mercado', 'WHERE id = '.$id);
            $dadosItem = $core->getDados('itens', 'WHERE id = '.$dadosAnunciado->idItem);

            if($inventario->verificaItemIgual($dadosItem->nome, $idPersonagem)){
                $slot_recebido = $inventario->verificaItemIgual($dadosItem->nome, $idPersonagem);

                $campos_add = array(
                    'idItem' => $dadosItem->id,
                    'idSlot' => $slot_recebido,
                    'idPersonagem' => $idPersonagem
                );

                $core->insert('personagens_inventario_itens', $campos_add);
            }

            if($core->delete('personagens_mercado', "id = ".$id)){
                $core->msg('sucesso', 'Item retirado do Market.');
                header('Location: '.BASE.'market/anunciados');
            }
        ?>
    <?php break; ?>

    <?php case 'comprar': ?>
        <?php 
            $id = Url::getURL(2);
            $idComprador = $_SESSION["PERSONAGEMID"];
            
            if($id != 'ajax'){
                if(!$mercado->getExistItem($id)){
                    header('Location: '.BASE.'portal');
                }
            
                $dados = $core->getDados('personagens_mercado', 'WHERE id = '.$id);
                $dadosItem = $core->getDados('itens', 'WHERE id = '.$dados->idItem);
                $dadosPersonagem = $core->getDados('usuarios_personagens', 'WHERE id = '.$dados->idPersonagem);
                $dadosComprador = $core->getDados('usuarios_personagens', 'WHERE id = '.$idComprador);

                $vendido = 0;

                if($personagem->gold >= $dados->valor){
                    $campos = array(
                        'gold' => intval($dadosPersonagem->gold) + intval($dados->valor)
                    );

                    $where = 'id = "'.$dados->idPersonagem.'"';

                    if($core->update('usuarios_personagens', $campos, $where)){
                        $campos = array(
                            'gold' => intval($dadosComprador->gold) - intval($dados->valor)
                        );

                        $where = 'id = "'.$idComprador.'"';

                        $core->update('usuarios_personagens', $campos, $where);

                        $campos_vendedor = array(
                            'gold' => intval($dadosPersonagem->gold) + intval($dados->valor),
                            'gold_total' => intval($dadosPersonagem->gold_total) + intval($dados->valor)
                        );

                        $where_vendedor = 'id = "'.$dados->idPersonagem.'"';

                        if($core->update('usuarios_personagens', $campos_vendedor, $where_vendedor)){
                            if($inventario->verificaItemIgual($dadosItem->nome, $idComprador)){
                                $slot_recebido = $inventario->verificaItemIgual($dadosItem->nome, $idComprador);
                            }

                            $campos_i = array(
                                'novo' => 1
                            );

                            $where_i = 'id = "'.$slot_recebido.'"';

                            $core->update('personagens_inventario', $campos_i, $where_i);

                            $campos_add = array(
                                'idItem' => $dados->idItem,
                                'idSlot' => $slot_recebido,
                                'idPersonagem' => $idComprador
                            );

                            $core->insert('personagens_inventario_itens', $campos_add);

                            if($core->delete('personagens_mercado', "id = ".$id)){
                                $core->msg('sucesso', 'Venda realizada.');
                                $vendido = 1;
                            } else {
                                $core->msg('error', 'Erro ao efetuar a Compra.');
                            }
                        }
                    }
                } else {
                    $core->msg('error', 'Gold insuficiente para compra!');
                    header('Location: '.BASE.'market');
                }
            }
        ?>

        <?php if($vendido == 1){ ?>
            <div class="sucesso-venda">
                <img src="<?php echo BASE.'assets/'.$dadosItem->foto; ?>" />
                <h3><?php echo $dadosItem->nome; ?></h3>
                <p>Parabéns compra realizada com sucesso, veja o item em seu inventário.</p>
                <a href="<?php echo BASE; ?>inventario" class="bt-inventario">Meu Inventário</a>
            </div>
        <?php } ?>
    <?php break; ?>

    <?php case 'vender-item': ?>
        <?php 
            $id = Url::getURL(2);
            $idVendedor = $_SESSION["PERSONAGEMID"];
            
            if($id != 'ajax'){
                if(!$mercado->getExistItemS($id)){
                    $core->msg('error', 'Item não existe.');
                    header('Location: '.BASE.'market');
                }
                
                $dados = $core->getDados('personagens_mercado_solicitacoes', 'WHERE id = '.$id);
                $dadosItem = $core->getDados('itens', 'WHERE id = '.$dados->idItem);
                $dadosPersonagem = $core->getDados('usuarios_personagens', 'WHERE id = '.$dados->idPersonagem);
                $dadosVendedor = $core->getDados('usuarios_personagens', 'WHERE id = '.$idVendedor);
                
                if(!$mercado->getExistItemInInventario($dados->idItem, $personagem->id)){
                    $core->msg('error', 'Você não possui este item no inventário.');
                    header('Location: '.BASE.'inventario');
                } else {
                    $dadosInventario = $core->getDados('personagens_inventario_itens', 'WHERE idItem = '.$dados->idItem.' AND idPersonagem = '.$personagem->id);
                    
                    if($dados->tipo == 1){
                        $campos = array(
                            'gold' => intval($personagem->gold) + intval($dados->golds)
                        );

                        $where = 'id = "'.$dadosVendedor->id.'"';

                        if($core->update('usuarios_personagens', $campos, $where)){
                            $core->delete('personagens_inventario_itens', "id = ".$dadosInventario->id);
                        }
                    }

                    if($dados->tipo == 2){
                        $campos = array(
                            'coins' => intval($user->coins) + intval($dados->coins)
                        );

                        $where = 'id = "'.$user->id.'"';

                        if($core->update('usuarios', $campos, $where)){
                            $core->delete('personagens_inventario_itens', "id = ".$dadosInventario->id);
                        }
                    }
                    
                    if($inventario->verificaItemIgual($dadosItem->nome, $dadosPersonagem->id)){
                        $slot_recebido = $inventario->verificaItemIgual($dadosItem->nome, $dadosPersonagem->id);
                    }

                    $campos = array(
                        'novo' => 1
                    );

                    $where = 'id = "'.$slot_recebido.'"';

                    $core->update('personagens_inventario', $campos, $where);

                    $campos_add = array(
                        'idItem' => $dadosItem->id,
                        'idSlot' => $slot_recebido,
                        'idPersonagem' => $dadosPersonagem->id
                    );

                    $core->insert('personagens_inventario_itens', $campos_add);

                    if($core->delete('personagens_mercado_solicitacoes', "id = ".$dados->id)){
                        $core->msg('sucesso', 'Item Vendido.');
                        header('Location: '.BASE.'inventario');
                    } else {
                        $core->msg('error', 'Erro ao vender item.');
                        header('Location: '.BASE.'market');
                    }
                }
            }
        ?>

        <?php if($vendido == 1){ ?>
            <div class="sucesso-venda">
                <img src="<?php echo BASE.'assets/'.$dadosItem->foto; ?>" />
                <h3><?php echo $dadosItem->nome; ?></h3>
                <p>Parabéns compra realizada com sucesso, veja o item em seu inventário.</p>
                <a href="<?php echo BASE; ?>inventario" class="bt-inventario">Meu Inventário</a>
            </div>
        <?php } ?>
    <?php break; ?>
<?php } ?>