<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if(isset($_SESSION['username'])){
        $user->getUserInfo($_SESSION['username']);
    }
    
    $listaAnuncios_ativo = $core->getDados('adm_loja_produtos', "WHERE status = 1");
    
    if(empty($listaAnuncios_ativo)){
        $dataHoje = mktime(20, 00, 00, date('m'), date('d'), date('Y'));
        
        $campos = array(
            'time_inicial' => $dataHoje,
            'time_final' => $dataHoje + 86400,
            'status' => 1
        );

        $where = 'dia = 1';

        $core->update('adm_loja_produtos', $campos, $where);
    }

    $listaAnuncios = $core->getDados('adm_loja_produtos', "WHERE status = 1");

    $time_atual = time();
    $tempoRestante = $listaAnuncios->time_final - $time_atual;

    $anuncio_1 = (!empty($listaAnuncios->posicao_1)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_1) : null;
    $anuncio_2 = (!empty($listaAnuncios->posicao_2)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_2) : null;
    $anuncio_3 = (!empty($listaAnuncios->posicao_3)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_3) : null;
    $anuncio_4 = (!empty($listaAnuncios->posicao_4)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_4) : null;
    $anuncio_5 = (!empty($listaAnuncios->posicao_5)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_5) : null;
    $anuncio_6 = (!empty($listaAnuncios->posicao_6)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_6) : null;
    $anuncio_7 = (!empty($listaAnuncios->posicao_7)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_7) : null;
    $anuncio_8 = (!empty($listaAnuncios->posicao_8)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_8) : null;
    
    if (!empty($listaAnuncios->id)) {
    $loja->marcarFotoLoja($listaAnuncios->id);
}    
    if($tempoRestante <= 0){
        if($listaAnuncios->dia == 7){
            $proximo = 1;
        } else {
            $proximo = $listaAnuncios->dia + 1;
        }

        $campos = array(
            'status' => 0
        );

        $where = 'dia is not null';
        $core->update('adm_loja_produtos', $campos, $where);
        
        $dataHoje = mktime(20, 00, 00, date('m'), date('d'), date('Y'));

        $campos = array(
            'time_inicial' => $dataHoje,
            'time_final' => $dataHoje + 86400,
            'status' => 1
        );

        $where = 'dia = '.$proximo;
        $core->update('adm_loja_produtos', $campos, $where);

        $listaAnuncios = $core->getDados('adm_loja_produtos', "WHERE status = 1");

        $time_atual = time();
        $tempoRestante = $listaAnuncios->time_final - $time_atual;

        $anuncio_1 = (!empty($listaAnuncios->posicao_1)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_1) : null;
        $anuncio_2 = (!empty($listaAnuncios->posicao_2)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_2) : null;
        $anuncio_3 = (!empty($listaAnuncios->posicao_3)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_3) : null;
        $anuncio_4 = (!empty($listaAnuncios->posicao_4)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_4) : null;
        $anuncio_5 = (!empty($listaAnuncios->posicao_5)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_5) : null;
        $anuncio_6 = (!empty($listaAnuncios->posicao_6)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_6) : null;
        $anuncio_7 = (!empty($listaAnuncios->posicao_7)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_7) : null;
        $anuncio_8 = (!empty($listaAnuncios->posicao_8)) ? $core->getDados('adm_loja_itens', "WHERE id =".$listaAnuncios->posicao_8) : null;
        
        $loja->marcarFotoLoja($listaAnuncios->id);
    }
?>

<ul class="menu-loja">
    <li>
        <a href="<?php echo BASE; ?>portal">Voltar ao Jogo</a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>loja">Loja de Itens</a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>loja/creditos">Comprar Coins</a>
    </li>
    <li class="coins">
        <span><img src="<?php echo BASE; ?>assets/icones/coin.png" /><?php echo $user->coins; ?> <strong>coins</strong></span>
    </li>
</ul>

<?php switch($acao) {
    default: ?>
        <div class="contentLoja">
            <div class="destaques">
                <?php 
                    if($anuncio_1->modulo == 1){
                        if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_1->foto."' AND idUsuario = ".$user->id)){ 
                            $adquirido = '<div class="adquirido">
                                            <span>Item Adquirido</span>
                                          </div>';
                        } else {
                            $adquirido = '';
                        }
                    } else {
                        if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_1->id."' AND idUsuario = ".$user->id)){ 
                            $adquirido = '<div class="adquirido">
                                            <span>Item Adquirido</span>
                                          </div>';
                        } else {
                            $adquirido = '';
                        }
                    }
                ?>
                <h4>Itens em Destaque <span>00:00:00</span></h4>
                <div id="destaque_one" class="item-destaque <?php echo $loja->getClassRarirade($anuncio_1->foto); ?>">
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_1->id ?>">
                        <?php echo $adquirido; ?>
                        <?php if($anuncio_1->promocao == 1){ ?>
                            <h5 class="flag">Promoção</h5>
                        <?php } else { ?>
                            <?php if($anuncio_1->novo == 1){ ?>
                                <h5 class="flag">Novo</h5>
                            <?php } else { ?>
                                <?php if(!empty($anuncio_1->flag)){ ?>
                                    <h5 class="flag"><?php echo $anuncio_1->flag ?></h5>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <div class="foto">
                            <?php if($anuncio_1->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_1->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_1->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                        <h5>
                            <?php 
                            if (!empty($anuncio_1->idBoneco)) {
                                echo $loja->getNomeFoto($anuncio_1->idBoneco, $anuncio_1->modulo, $anuncio_1->nome);
                            } else {
                                echo htmlspecialchars($anuncio_1->nome);
                            }
                            ?>
                        </h5>
                        <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_2->modulo); ?></span>
                        <?php echo $loja->getPrecoFoto($anuncio_1->foto, $anuncio_1->valor, $anuncio_1->modulo); ?>
                    </div>
                    </a>
                </div>
                <div id="destaque_two" class="item-destaque <?php echo $loja->getClassRarirade($anuncio_2->foto); ?>">
                    <?php 
                        if($anuncio_2->modulo == 1){
                            if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_2->foto."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        } else {
                            if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_2->id."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        }
                    ?>
                    <?php if($anuncio_2->promocao == 1){ ?>
                        <h5 class="flag">Promoção</h5>
                    <?php } else { ?>
                        <?php if($anuncio_2->novo == 1){ ?>
                            <h5 class="flag">Novo</h5>
                        <?php } else { ?>
                            <?php if(!empty($anuncio_2->flag)){ ?>
                                <h5 class="flag"><?php echo $anuncio_2->flag ?></h5>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_2->id ?>">
                        <?php echo $adquirido; ?>
                        <div class="foto">
                            <?php if($anuncio_2->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_2->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_2->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                        <h5>
                            <?php 
                            if (!empty($anuncio_2->idBoneco)) {
                                echo $loja->getNomeFoto($anuncio_2->idBoneco, $anuncio_2->modulo, $anuncio_2->nome);
                            } else {
                                echo htmlspecialchars($anuncio_2->nome);
                            }
                            ?>
                        </h5>
                        <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_2->modulo); ?></span>
                        <?php echo $loja->getPrecoFoto($anuncio_2->foto, $anuncio_2->valor, $anuncio_2->modulo); ?>
                    </div>
                    </a>
                </div>
            </div>
            <div class="itens-lista">
                <h4>Itens Diários <span>00:00:00</span></h4>
                <div class="itens item-1 <?php echo $loja->getClassRarirade($anuncio_3->foto); ?>">
                    <?php 
                        if($anuncio_3->modulo == 1){
                            if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_3->foto."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        } else {
                            if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_3->id."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        }
                    ?>
                    <?php if($anuncio_3->promocao == 1){ ?>
                        <h5 class="flag">Promoção</h5>
                    <?php } else { ?>
                        <?php if($anuncio_3->novo == 1){ ?>
                            <h5 class="flag">Novo</h5>
                        <?php } else { ?>
                            <?php if(!empty($anuncio_3->flag)){ ?>
                                <h5 class="flag"><?php echo $anuncio_3->flag ?></h5>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_3->id ?>">
                        <?php echo $adquirido; ?>
                        <div class="foto">
                            <?php if($anuncio_3->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_3->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_3->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                            <h5><?php echo $loja->getNomeFoto($anuncio_3->idBoneco, $anuncio_3->modulo, $anuncio_3->nome) ?></h5>
                            <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_3->modulo); ?></span>
                            <?php echo $loja->getPrecoFoto($anuncio_3->foto, $anuncio_3->valor, $anuncio_3->modulo); ?>
                        </div>
                    </a>
                </div>
                <div class="itens item-2 <?php echo $loja->getClassRarirade($anuncio_4->foto); ?>">
                    <?php 
                        if($anuncio_4->modulo == 1){
                            if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_4->foto."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        } else {
                            if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_4->id."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        }
                    ?>
                    <?php if($anuncio_4->promocao == 1){ ?>
                        <h5 class="flag">Promoção</h5>
                    <?php } else { ?>
                        <?php if($anuncio_4->novo == 1){ ?>
                            <h5 class="flag">Novo</h5>
                        <?php } else { ?>
                            <?php if(!empty($anuncio_4->flag)){ ?>
                                <h5 class="flag"><?php echo $anuncio_4->flag ?></h5>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_4->id ?>">
                        <?php echo $adquirido; ?>
                        <div class="foto">
                            <?php if($anuncio_4->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_4->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_4->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                            <h5><?php echo $loja->getNomeFoto($anuncio_4->idBoneco, $anuncio_4->modulo, $anuncio_4->nome) ?></h5>
                            <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_4->modulo); ?></span>
                            <?php echo $loja->getPrecoFoto($anuncio_4->foto, $anuncio_4->valor, $anuncio_4->modulo); ?>
                        </div>
                    </a>
                </div>
                <div class="itens item-3 <?php echo $loja->getClassRarirade($anuncio_5->foto); ?>" style="margin-right: 0;">
                    <?php 
                        if($anuncio_5->modulo == 1){
                            if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_5->foto."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        } else {
                            if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_5->id."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        }
                    ?>
                    <?php if($anuncio_5->promocao == 1){ ?>
                        <h5 class="flag">Promoção</h5>
                    <?php } else { ?>
                        <?php if($anuncio_5->novo == 1){ ?>
                            <h5 class="flag">Novo</h5>
                        <?php } else { ?>
                            <?php if(!empty($anuncio_5->flag)){ ?>
                                <h5 class="flag"><?php echo $anuncio_5->flag ?></h5>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_5->id ?>">
                        <?php echo $adquirido; ?>
                        <div class="foto">
                            <?php if($anuncio_5->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_5->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_5->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                            <h5><?php echo $loja->getNomeFoto($anuncio_5->idBoneco, $anuncio_5->modulo, $anuncio_5->nome) ?></h5>
                            <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_5->modulo); ?></span>
                            <?php echo $loja->getPrecoFoto($anuncio_5->foto, $anuncio_5->valor, $anuncio_5->modulo); ?>
                        </div>
                    </a>
                </div>
                <div class="itens item-4 <?php echo $loja->getClassRarirade($anuncio_6->foto); ?>" style="margin: 0 5px 0 0;">
                    <?php 
                        if($anuncio_6->modulo == 1){
                            if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_6->foto."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        } else {
                            if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_6->id."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        }
                    ?>
                    <?php if($anuncio_6->promocao == 1){ ?>
                        <h5 class="flag">Promoção</h5>
                    <?php } else { ?>
                        <?php if($anuncio_6->novo == 1){ ?>
                            <h5 class="flag">Novo</h5>
                        <?php } else { ?>
                            <?php if(!empty($anuncio_6->flag)){ ?>
                                <h5 class="flag"><?php echo $anuncio_6->flag ?></h5>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_6->id ?>">
                        <?php echo $adquirido; ?>
                        <div class="foto">
                            <?php if($anuncio_6->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_6->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_6->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                            <h5><?php echo $loja->getNomeFoto($anuncio_6->idBoneco, $anuncio_6->modulo, $anuncio_6->nome) ?></h5>
                            <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_6->modulo); ?></span>
                            <?php echo $loja->getPrecoFoto($anuncio_6->foto, $anuncio_6->valor, $anuncio_6->modulo); ?>
                        </div>
                    </a>
                </div>
                <div class="itens item-5 <?php echo $loja->getClassRarirade($anuncio_7->foto); ?>" style="margin: 0 5px 0 0;">
                    <?php 
                        if($anuncio_7->modulo == 1){
                            if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_7->foto."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        } else {
                            if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_7->id."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        }
                    ?>
                    <?php if($anuncio_7->promocao == 1){ ?>
                        <h5 class="flag">Promoção</h5>
                    <?php } else { ?>
                        <?php if($anuncio_7->novo == 1){ ?>
                            <h5 class="flag">Novo</h5>
                        <?php } else { ?>
                            <?php if(!empty($anuncio_7->flag)){ ?>
                                <h5 class="flag"><?php echo $anuncio_7->flag ?></h5>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_7->id ?>">
                        <?php echo $adquirido; ?>
                        <div class="foto">
                            <?php if($anuncio_7->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_7->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_7->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                            <h5><?php echo $loja->getNomeFoto($anuncio_7->idBoneco, $anuncio_7->modulo, $anuncio_7->nome) ?></h5>
                            <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_7->modulo); ?></span>
                            <?php echo $loja->getPrecoFoto($anuncio_7->foto, $anuncio_7->valor, $anuncio_7->modulo); ?>
                        </div>
                    </a>
                </div>
                <div class="itens item-6 <?php echo $loja->getClassRarirade($anuncio_8->foto); ?>" style="margin: 0;">
                    <?php 
                        if($anuncio_8->modulo == 1){
                            if($core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$anuncio_8->foto."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        } else {
                            if($core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$anuncio_8->id."' AND idUsuario = ".$user->id)){ 
                                $adquirido = '<div class="adquirido">
                                                <span>Item Adquirido</span>
                                              </div>';
                            } else {
                                $adquirido = '';
                            }
                        }
                    ?>
                    <?php if($anuncio_8->promocao == 1){ ?>
                        <h5 class="flag">Promoção</h5>
                    <?php } else { ?>
                        <?php if($anuncio_8->novo == 1){ ?>
                            <h5 class="flag">Novo</h5>
                        <?php } else { ?>
                            <?php if(!empty($anuncio_8->flag)){ ?>
                                <h5 class="flag"><?php echo $anuncio_8->flag ?></h5>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <a href="<?php echo BASE; ?>loja/produto/<?php echo $anuncio_8->id ?>">
                        <?php echo $adquirido; ?>
                        <div class="foto">
                            <?php if($anuncio_8->modulo == 1){ ?>
                                <img src="<?php echo BASE.'assets/cards/'.$anuncio_8->foto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo BASE.'uploads/'.$anuncio_8->foto; ?>" />
                            <?php } ?>
                        </div>
                        <div class="infos">
                            <h5><?php echo $loja->getNomeFoto($anuncio_8->idBoneco, $anuncio_8->modulo, $anuncio_8->nome) ?></h5>
                            <span class="tipo"><?php echo $loja->getTipoProduto($anuncio_8->modulo); ?></span>
                            <?php echo $loja->getPrecoFoto($anuncio_8->foto, $anuncio_8->valor, $anuncio_8->modulo); ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="mensagem">
            <Strong>Observação:</Strong> Cada dia temos itens diferentes para você escolher o que mais lhe agrada.
        </div>

        <script type="text/javascript">
            startCountdown(<?php echo $tempoRestante; ?>);

            function startCountdown(tempo){
                if((tempo - 1) >= 0){

                    var min = parseInt(tempo/60);
                    var horas = parseInt(min/60);
                    min = min % 60;
                    var seg = tempo%60;

                    if(min < 10){
                        min = "0"+min;
                        min = min.substr(0, 2);
                    }

                    if(seg <=9){
                        seg = "0"+seg;
                    }

                    if(horas <=9){
                        horas = "0" + horas;
                    }

                    horaImprimivel = horas + ':' + min + ':' + seg;

                    $("h4 span").html(horaImprimivel);

                    setTimeout(function(){ 
                        startCountdown(tempo);
                    }, 1000);

                    tempo --;
                } else {
                    //atualizar novos itens
                    if (tempoRestante <= 0) { location.reload(); }
                }
            }
        </script>
    <?php break; ?>
        
    <?php case 'creditos': ?>
        <div class="contentLoja">
            <div class="detalhes-vip">
                <p>O DB HEREOS RPG é um jogo gratuito e sem fins lucrativos, e por isso a contribuição de seus jogadores é fundamental para que, cada dia mais, o jogo se desenvolva e melhore suas funcionalidades. Qualquer tipo de arrecadação ou doações feitas ao DB Heroes serão revertidas em manutenção e melhorias ao site, bem como divulgação deste e do anime. E contribuindo com sua doação ao jogo, além de nos ajudar a cada dia melhorar o DB Heroes, você, jogador, passa a ser um Jogador VIP, com acesso à vantagens exclusivas.</p>
                <p>- Seus créditos não expiram por falta de uso.</p>
                <p>- Todos os personagens de sua conta podem usufluir dos créditos.</p>
                <p>- Estar colaborando com a manuntenção e evolução do jogo.</p>
                <br>
                <p style="color: #C21F39;"><strong>Atenção:</strong> Na compra de 10 coins para cima, você se torna VIP vitalício no Jogo e poderá ser usado para todos seus Guerreiros.</p>
            </div>

            <ul class="botoes-doacao">
                <li>
                    <div class="img"></div>
                    <h3>Nome</h3>
                    <span>Créditos</span>
                    <span class="coins">Valor</span>
                </li>
                <li class="even">
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 5 Coins</span>
                    <span class="coins">R$ 5,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar5" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar5" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" id="valor5" checked value="5" />
                        <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li>
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 10 Coins</span>
                    <span class="coins">R$ 10,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar10" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar10" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" id="valor10" checked value="10" />
                        <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li class="even">
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 15 Coins</span>
                    <span class="coins">R$ 15,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar15" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar15" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" id="valor15" checked value="15" />
                        <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li>
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 20 Coins</span>
                    <span class="coins">R$ 20,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar20" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar20" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" id="valor20" checked value="20" />
                        <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li class="even">
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 25 Coins</span>
                    <span class="coins">R$ 25,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar25" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar25" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" id="valor25" checked value="25" />
                        <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li>
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 30 Coins</span>
                    <span class="coins">R$ 30,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar30" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar30" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" checked value="30" />
                        <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li class="even">
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 35 Coins</span>
                    <span class="coins">R$ 35,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar35" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar35" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" checked value="35" />
                        <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li>
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 40 Coins</span>
                    <span class="coins">R$ 40,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar40" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar40" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" checked value="40" />
                        <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li class="even">
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 45 Coins</span>
                    <span class="coins">R$ 45,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar45" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar45" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" checked value="45" />
                        <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li>
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 50 Coins</span>
                    <span class="coins">R$ 50,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar50" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar50" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" checked value="50" />
                        <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li class="even">
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 100 Coins</span>
                    <span class="coins">R$ 100,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar100" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar100" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" checked value="100" />
                        <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>
                <li>
                    <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
                    <h3>Coins (MOEDA DO JOGO)</h3>
                    <span class="creditos">Ganha 500 Coins</span>
                    <span class="coins">R$ 500,00</span>

                    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                        <form id="formDoar500" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } else { ?>
                        <form id="formDoar500" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="pagamentoCreditos(this); return false;">
                    <?php } ?>

                        <input type="hidden" name="code" id="code" value="" />
                        <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
                        <input type="hidden" name="valor" checked value="500" />
                        <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

                        <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
                    </form>
                </li>

                <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
                    <script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
                <?php } else { ?>
                    <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
                <?php } ?>
            </ul>
        </div>
        
        <script type="text/javascript">
            function pagamentoCreditos(i){
                $.ajax({
                    type: "POST",
                    url: "<?php echo BASE; ?>ajax/ajaxDoacao.php",
                    data: new FormData(i),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function (res) {
                        $(i).find('#code').val(res);

                        PagSeguroLightbox({
                            code: res
                        }, {
                            success : function(transactionCode) {
                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo BASE; ?>ajax/ajaxTransacoes.php",
                                    data: {
                                        transaction_id: res,
                                        retorno: 'success'
                                    },
                                    success: function (res) {
                                        var url_atual = window.location.href;
                                        window.location.href = url_atual.replace('doacao', 'transacoes');
                                    }
                                });
                            },
                            abort : function(transactionCode) {            
                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo BASE; ?>ajax/ajaxTransacoes.php",
                                    data: {
                                        transaction_id: res,
                                        retorno: 'abort'
                                    },
                                    success: function (res) {

                                    }
                                });
                            }
                        });
                    }
                });
            }
        </script>
    <?php break; ?>
        
    <?php case 'produto': ?>
        <?php 
            $id = Url::getURL(2);
            $dadosAnuncio = $core->getDados('adm_loja_itens', "WHERE id =".$id);
            
            if($dadosAnuncio->loja == 0){
                $core->msg('error', 'Item não encontrado.');
                header('Location: '.BASE.'loja');
            }
        ?>
        <div class="box-produto">
            <?php if($dadosAnuncio->modulo == 1){ ?>
                <img class="foto" src="<?php echo BASE.'assets/cards/'.$dadosAnuncio->foto; ?>" />
            <?php } else { ?>
                <img class="foto" src="<?php echo BASE.'uploads/'.$dadosAnuncio->foto; ?>" />
            <?php } ?>
            <div class="header-box">
                <span class="tipo"><?php echo $loja->getTipoProduto($dadosAnuncio->modulo); ?></span>
                <h3><?php echo $loja->getNomeFoto($dadosAnuncio->idBoneco, $dadosAnuncio->modulo, $dadosAnuncio->nome); ?></h3>
            </div>
            <div class="body-box">
                <p><?php echo $dadosAnuncio->descricao; ?></p>
                
                <?php if($dadosAnuncio->modulo == 3){ ?>
                    <?php 
                        $idItem = $dadosAnuncio->idItem;
                        $dadosItem = $core->getDados('itens', "WHERE id = ".$idItem);
                    ?>
                    
                    <?php if($dadosItem->bau == 0){ ?>
                        <div class="informacoes-item">
                            <h4>Informações do Item</h4>
                            <?php 
                                if($dadosItem->tipo == 1 || $dadosItem->tipo == 3 || $dadosItem->tipo == 4){
                                    echo '<h4>Item Consumível</h4>';
                                    $percent = '% de recuperação';
                                } else {
                                    $percent = '';
                                }

                                if($dadosItem->hp > 0){
                                    echo '<p><strong>HP:</strong>+ '.$dadosItem->hp.$percent.'</p>';
                                }

                                if($dadosItem->mana > 0){
                                    echo '<p><strong>KI:</strong>+ '.$dadosItem->mana.$percent.'</p>';
                                }

                                if($dadosItem->energia > 0){
                                    echo '<p><strong>Energia:</strong>+ '.$dadosItem->energia.'</p>';
                                }

                                if($dadosItem->forca > 0){
                                    echo '<p><strong>Força:</strong>+ '.$dadosItem->forca.'</p>';
                                }

                                if($dadosItem->agilidade > 0){
                                    echo '<p><strong>Agilidade:</strong>+ '.$dadosItem->agilidade.'</p>';
                                }

                                if($dadosItem->habilidade > 0){
                                    echo '<p><strong>Habilidade:</strong>+ '.$dadosItem->habilidade.'</p>';
                                }

                                if($dadosItem->resistencia > 0){
                                    echo '<p><strong>Resistência:</strong>+ '.$dadosItem->resistencia.'</p>';
                                }

                                if($dadosItem->sorte > 0){
                                    echo '<p><strong>Sorte:</strong>+ '.$dadosItem->sorte.'</p>';
                                }
                            ?>
                        </div>
                    <?php } else { ?>
                        <div class="informacoes-item">
                            <h4>Itens do Baú</h4>

                            <ul>
                                <?php $market->getfotoItens($dadosItem->id); ?>
                            </ul>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
             
            <div class="buy-box">
                <?php echo $loja->getPrecoFoto($dadosAnuncio->foto, $dadosAnuncio->valor, $dadosAnuncio->modulo); ?>
                <?php if($dadosAnuncio->modulo == 1){ ?>
                    <?php if($dadosAnuncio->idBoneco == $personagem->boneco){ ?>
                        <?php if(!$core->isExists('usuarios_personagens_fotos', "WHERE foto = '".$dadosAnuncio->foto."' AND idUsuario = ".$user->id)){ ?>
                            <?php if($user->coins >= $dadosAnuncio->valor){ ?>
                                <input type="hidden" name="idProduto" id="idProduto" value="<?php echo $dadosAnuncio->id; ?>" />
                                <input type="hidden" name="idItem" id="idItem" value="<?php echo $dadosAnuncio->idItem; ?>" />
                                <input type="hidden" name="valor" id="valor" value="<?php echo $loja->getValor($dadosAnuncio->foto, $dadosAnuncio->valor, $dadosAnuncio->modulo); ?>" />
                                <input type="hidden" name="foto" id="foto" value="<?php echo $dadosAnuncio->foto; ?>" />
                                <input type="hidden" name="modulo" id="modulo" value="<?php echo $dadosAnuncio->modulo; ?>" />
                                <input type="hidden" name="idUsuario" id="idUsuario" value="<?php echo $user->id; ?>" />
                                <input type="hidden" name="idPersonagem" id="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID']; ?>" />
                                <input type="buttom" class="botaoComprar bt-adquirir-item" value="Adquirir Item" />
                            <?php } else { ?>
                                <a href="<?php echo BASE; ?>loja/creditos" class="botaoComprar coins">
                                    <img src="<?php echo BASE; ?>assets/icones/coin.png" /> Adquirir Coins
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <a href="javascript:void(0);" class="botaoComprar coins">
                                <i class="far fa-check-circle"></i> Item Adquirido
                            </a>
                        <?php } ?>
                    <?php } else { ?>
                        <p style="display: block; padding: 10px 0; color: #FFF; text-align: center;">* Este item pertence a outro Guerreiro, efetue a Troca de Guerreiro</p>
                        <a href="<?php echo BASE; ?>troca-guerreiro/<?php echo $dadosAnuncio->id; ?>" class="botaoComprar coins">
                            <i class="fas fa-sync-alt"></i> Trocar Guerreiro (Grátis)
                        </a>
                    <?php } ?>
                <?php } else if($dadosAnuncio->modulo == 2){ ?>
                    <?php if(!$core->isExists('usuarios_personagens_modulos', "WHERE idProduto = '".$dadosAnuncio->id."' AND idUsuario = ".$user->id)){ ?>
                        <?php if($user->coins >= $dadosAnuncio->valor){ ?>
                            <input type="hidden" name="idProduto" id="idProduto" value="<?php echo $dadosAnuncio->id; ?>" />
                            <input type="hidden" name="idItem" id="idItem" value="<?php echo $dadosAnuncio->idItem; ?>" />
                            <input type="hidden" name="valor" id="valor" value="<?php echo $loja->getValor($dadosAnuncio->foto, $dadosAnuncio->valor, $dadosAnuncio->modulo); ?>" />
                            <input type="hidden" name="foto" id="foto" value="<?php echo $dadosAnuncio->foto; ?>" />
                            <input type="hidden" name="modulo" id="modulo" value="<?php echo $dadosAnuncio->modulo; ?>" />
                            <input type="hidden" name="idUsuario" id="idUsuario" value="<?php echo $user->id; ?>" />
                            <input type="hidden" name="idPersonagem" id="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID']; ?>" />
                            <input type="buttom" class="botaoComprar bt-adquirir-item" value="Adquirir Item" />
                        <?php } else { ?>
                            <a href="<?php echo BASE; ?>loja/creditos" class="botaoComprar coins">
                                <img src="<?php echo BASE; ?>assets/icones/coin.png" /> Adquirir Coins
                            </a>
                        <?php } ?>
                    <?php } else { ?>
                        <a href="javascript:void(0);" class="botaoComprar coins">
                            <i class="far fa-check-circle"></i> Item Adquirido
                        </a>
                    <?php } ?>
                <?php } else if($dadosAnuncio->modulo == 3){ ?>
                    <?php if($user->coins >= $dadosAnuncio->valor){ ?>
                        <input type="hidden" name="idProduto" id="idProduto" value="<?php echo $dadosAnuncio->id; ?>" />
                        <input type="hidden" name="idItem" id="idItem" value="<?php echo $dadosAnuncio->idItem; ?>" />
                        <input type="hidden" name="valor" id="valor" value="<?php echo $loja->getValor($dadosAnuncio->foto, $dadosAnuncio->valor, $dadosAnuncio->modulo); ?>" />
                        <input type="hidden" name="foto" id="foto" value="<?php echo $dadosAnuncio->foto; ?>" />
                        <input type="hidden" name="modulo" id="modulo" value="<?php echo $dadosAnuncio->modulo; ?>" />
                        <input type="hidden" name="idUsuario" id="idUsuario" value="<?php echo $user->id; ?>" />
                        <input type="hidden" name="idPersonagem" id="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID']; ?>" />
                        <input type="buttom" class="botaoComprar bt-adquirir-item" value="Adquirir Item" />
                    <?php } else { ?>
                        <a href="<?php echo BASE; ?>loja/creditos" class="botaoComprar coins">
                            <img src="<?php echo BASE; ?>assets/icones/coin.png" /> Adquirir Coins
                        </a>
                    <?php } ?>       
                <?php } ?>
            </div>
                
            <?php if($dadosAnuncio->modulo == 3){ ?>
                <?php if($dadosItem->bau == 1){ ?>
                    <h3 class="infoItens">Informações sobre os itens do Baú</h3>
                    <?php echo $loja->getDadosItensBau($dadosItem->id); ?>
                <?php } ?>
            <?php } ?>
        </div>
        
        <div class="holograma">
            <?php if($dadosAnuncio->modulo == 1){ ?>
                <img class="foto" src="<?php echo BASE.'assets/cards/'.$dadosAnuncio->foto; ?>" />
            <?php } else { ?>
                <img class="foto" src="<?php echo BASE.'uploads/'.$dadosAnuncio->foto; ?>" />
            <?php } ?>
        </div>
        
        <script type="text/javascript">
            $('.bt-adquirir-item').on('click', function(){
                swal({
                    title: 'Deseja adquirir esse item?',
                    text: '',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim'
                }).then((result) => {
                    if (result.value) {
                        var id = $('#idProduto').val();
                        var foto = $('#foto').val();
                        var modulo = $('#modulo').val();
                        var valor = $('#valor').val();
                        var idPersonagem = $('#idPersonagem').val();
                        var idItem = $('#idItem').val();
                        var idUsuario = $('#idUsuario').val();
                        var data_string = 'id=' + id + '&valor=' + valor + '&foto=' + foto + '&idPersonagem=' + idPersonagem + '&idUsuario=' + idUsuario + '&modulo=' + modulo + '&idItem=' + idItem;

                        $.ajax({
                            type: 'POST',
                            url: "<?php echo BASE; ?>ajax/ajaxAdquirirItem.php",
                            data: data_string,
                            success: function (res) {
                                if(modulo == 1){
                                    window.location.href = "<?php echo BASE; ?>minhas-fotos";
                                } else if(modulo == 2){
                                    window.location.href = "<?php echo BASE; ?>profile";
                                } else if(modulo == 3){
                                    window.location.href = "<?php echo BASE; ?>inventario";
                                }
                            }
                        });
                    }
                });
            });
        </script>
    <?php break; ?>
<?php } ?>