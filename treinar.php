<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($core->proccessInExecution()){
        header('Location: '.BASE.'profile');
    }
    
    $idPersonagem = $_SESSION['PERSONAGEMID'];
    $personagem->getGuerreiro($idPersonagem);
    
    $valores = $treino->getValoresTreino($_SESSION['PERSONAGEMID']);
    
    //STATUS EXTRA DAS EQUIPES
    $status_extra = intval($equipes->getStatusExtra($_SESSION['PERSONAGEMID']));
    $status_extra_graduacao = intval($core->getStatusGraduacao($personagem->graduacao_id));
    $status_equipados = $inventario->getStatusEquipados($_SESSION['PERSONAGEMID']);
    
    $forca_equipados = intval($status_equipados['forca']);
    $agilidade_equipados = intval($status_equipados['agilidade']);
    $habilidade_equipados = intval($status_equipados['habilidade']);
    $resistencia_equipados = intval($status_equipados['resistencia']);
    $sorte_equipados = intval($status_equipados['sorte']);
    
    $forca = $personagem->forca + $status_extra + $status_extra_graduacao + $forca_equipados;
    $agilidade = $personagem->agilidade + $status_extra + $status_extra_graduacao + $agilidade_equipados;
    $habilidade = $personagem->habilidade + $status_extra + $status_extra_graduacao + $habilidade_equipados;
    $resistencia = $personagem->resistencia + $status_extra + $status_extra_graduacao + $resistencia_equipados;
    $sorte = $personagem->sorte + $status_extra + $status_extra_graduacao + $sorte_equipados;
    
    $porcentagem_forca = $treino->getPorcentagemForca($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_agilidade = $treino->getPorcentagemAgilidade($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_habilidade = $treino->getPorcentagemHabilidade($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_resistencia = $treino->getPorcentagemResistencia($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_sorte = $treino->getPorcentagemSorte($forca, $agilidade, $habilidade, $resistencia, $sorte);
    
    if(isset($_POST['usar_forca'])){
        if($personagem->pontos >= $_POST['pontos_forca']){
            if($treino->usarPontos($_SESSION['PERSONAGEMID'], 'forca', addslashes($_POST['pontos_forca']))){
                $core->msg('sucesso', 'Força Aumentada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a força.');
            }
        } else {
            $core->msg('error', 'Você não possui pontos extras para treinar.');
        }
    }
    
    if(isset($_POST['usar_agilidade'])){
        if($personagem->pontos >= 1){
            if($treino->usarPontos($_SESSION['PERSONAGEMID'], 'agilidade', addslashes($_POST['pontos_agilidade']))){
                $core->msg('sucesso', 'Agilidade Aumentada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a Agilidade.');
            }
        } else {
            $core->msg('error', 'Você não possui pontos extras para treinar.');
        }
    }
    
    if(isset($_POST['usar_habilidade'])){
        if($personagem->pontos >= 1){
            if($treino->usarPontos($_SESSION['PERSONAGEMID'], 'habilidade', addslashes($_POST['pontos_habilidade']))){
                $core->msg('sucesso', 'Habilidade Aumentada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a Habilidade.');
            }
        } else {
            $core->msg('error', 'Você não possui pontos extras para treinar.');
        }
    }
    
    if(isset($_POST['usar_resistencia'])){
        if($personagem->pontos >= 1){
            if($treino->usarPontos($_SESSION['PERSONAGEMID'], 'resistencia', addslashes($_POST['pontos_resistencia']))){
                $core->msg('sucesso', 'Resistência Aumentada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a Resistência.');
            }
        } else {
            $core->msg('error', 'Você não possui pontos extras para treinar.');
        }
    }
    
    if(isset($_POST['usar_sorte'])){
        if($personagem->pontos >= 1){
            if($treino->usarPontos($_SESSION['PERSONAGEMID'], 'sorte', addslashes($_POST['pontos_sorte']))){
                $core->msg('sucesso', 'Sorte Aumentada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a Sorte.');
            }
        } else {
            $core->msg('error', 'Você não possui pontos extras para treinar.');
        }
    }
    
    if(isset($_POST['treinar_forca'])){
        if(intval($personagem->gold) >= intval(addslashes($_POST['soma']))){
            if($treino->treinarGuerreiro($_SESSION['PERSONAGEMID'], 'forca', addslashes($_POST['golds']), addslashes($_POST['unidades']), addslashes($_POST['golds']))){
                $core->msg('sucesso', 'Força Treinada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a força.');
            }
        } else {
            $core->msg('error', 'Você não possui gold suficiente para treinar.');
        }
    }
    
    if(isset($_POST['treinar_agilidade'])){
        if(intval($personagem->gold) >= intval($_POST['soma'])){
            if($treino->treinarGuerreiro($_SESSION['PERSONAGEMID'], 'agilidade', addslashes($_POST['soma']), addslashes($_POST['unidades']), addslashes($_POST['golds']))){
                $core->msg('sucesso', 'Agilidade Treinada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a agilidade.');
            }
        } else {
            $core->msg('error', 'Você não possui gold suficiente para treinar.');
        }
    }
    
    if(isset($_POST['treinar_habilidade'])){
        if(intval($personagem->gold) >= intval(addslashes($_POST['soma']))){
            if($treino->treinarGuerreiro($_SESSION['PERSONAGEMID'], 'habilidade', addslashes($_POST['soma']), addslashes($_POST['unidades']), addslashes($_POST['golds']))){
                $core->msg('sucesso', 'Habilidade Treinada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a habilidade.');
            }
        } else {
            $core->msg('error', 'Você não possui gold suficiente para treinar.');
        }
    }
    
    if(isset($_POST['treinar_resistencia'])){
        if(intval($personagem->gold) >= intval($_POST['soma'])){
            if($treino->treinarGuerreiro($_SESSION['PERSONAGEMID'], 'resistencia', addslashes($_POST['soma']), addslashes($_POST['unidades']), addslashes($_POST['golds']))){
                $core->msg('sucesso', 'Resistência Treinada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a resistência.');
            }
        } else {
            $core->msg('error', 'Você não possui gold suficiente para treinar.');
        }
    }
    
    if(isset($_POST['treinar_sorte'])){
        if(intval($personagem->gold) >= intval(addslashes($_POST['soma']))){
            if($treino->treinarGuerreiro($_SESSION['PERSONAGEMID'], 'sorte', addslashes($_POST['soma']), addslashes($_POST['unidades']), addslashes($_POST['golds']))){
                $core->msg('sucesso', 'Sorte Treinada.');
                header('Location: '.BASE.'treinar/');
            } else {
                $core->msg('error', 'Não foi possível treinar a sorte.');
            }
        } else {
            $core->msg('error', 'Você não possui gold suficiente para treinar.');
        }
    }
?>

<div class="pontos-extras">
    Você possui <Strong><?php echo $personagem->pontos; ?></Strong> pontos extras para distribuir em um dos atributos abaixo.
</div>

<ul class="pontuar">
    <li>
        <form id="usarForca" method="post">
            <label>Força</label>
            <div class="valores">
                <a id="menos" href="">-</a>
                <input type="text" name="pontos_forca" class="input-contador" value="1" readonly="readonly" />
                <a id="mais" href="">+</a>
            </div>
            <input type="submit" name="usar_forca" value="Usar" />
        </form>
    </li> 
    <li>
        <form id="usarForca" method="post">
            <label>Agilidade</label>
            <div class="valores">
                <a id="menos" href="">-</a>
                <input type="text" name="pontos_agilidade" class="input-contador" value="1" readonly="readonly" />
                <a id="mais" href="">+</a>
            </div>
            <input type="submit" name="usar_agilidade" value="Usar" />
        </form>
    </li>
    <li>
        <form id="usarForca" method="post">
            <label>Habilidade</label>
            <div class="valores">
                <a id="menos" href="">-</a>
                <input type="text" name="pontos_habilidade" class="input-contador" value="1" readonly="readonly" />
                <a id="mais" href="">+</a>
            </div>
            <input type="submit" name="usar_habilidade" value="Usar" />
        </form>
    </li> 
    <li>
        <form id="usarForca" method="post">
            <label>Resistência</label>
            <div class="valores">
                <a id="menos" href="">-</a>
                <input type="text" name="pontos_resistencia" class="input-contador" value="1" readonly="readonly" />
                <a id="mais" href="">+</a>
            </div>
            <input type="submit" name="usar_resistencia" value="Usar" />
        </form>
    </li> 
    <li>
        <form id="usarForca" method="post">
            <label>Sorte</label>
            <div class="valores">
                <a id="menos" href="">-</a>
                <input type="text" name="pontos_sorte" class="input-contador" value="1" readonly="readonly" />
                <a id="mais" href="">+</a>
            </div>
            <input type="submit" name="usar_sorte" value="Usar" />
        </form>
    </li> 
</ul>

<ul class="status">
    <li>
        <p>Aumenta o dano nos ataques do seu guerreiro</p>
        <div class="meter animate <?php $treino->setCorBarra($porcentagem_forca); ?>">
            <em>Força <?php echo $personagem->forca + $forca_equipados + $status_extra + $status_extra_graduacao; ?></em>
            <span style="width: <?php echo $porcentagem_forca; ?>%"><span></span></span>
        </div>
        <em>+ [<?php echo $status_extra + $status_extra_graduacao; ?>]</em>
    </li>
    <li>
        <p>Aumenta taxa de desvio contra o ataque de um inimigo</p>
        <div class="meter animate <?php $treino->setCorBarra($porcentagem_agilidade); ?>">
            <em>Agilidade <?php echo $personagem->agilidade + $agilidade_equipados + $status_extra + $status_extra_graduacao; ?></em>
            <span style="width: <?php echo $porcentagem_agilidade; ?>%"><span></span></span>
        </div>
        <em>+ [<?php echo $status_extra + $status_extra_graduacao; ?>]</em>
    </li>
    <li>
        <p>Aumenta a chance em acerto de ataques críticos</p>
        <div class="meter animate <?php $treino->setCorBarra($porcentagem_habilidade); ?>">
            <em>Habilidade <?php echo $personagem->habilidade + $habilidade_equipados + $status_extra + $status_extra_graduacao; ?></em>
            <span style="width: <?php echo $porcentagem_habilidade; ?>%"><span></span></span>
        </div>
        <em>+ [<?php echo $status_extra + $status_extra_graduacao; ?>]</em>
    </li>
    <li>
        <p>Aumenta sua resistência a ataques.</p>
        <div class="meter animate <?php $treino->setCorBarra($porcentagem_resistencia); ?>">
            <em>Resistência <?php echo $personagem->resistencia + $resistencia_equipados + $status_extra + $status_extra_graduacao; ?></em>
            <span style="width: <?php echo $porcentagem_resistencia; ?>%"><span></span></span>
        </div>
        <em>+ [<?php echo $status_extra + $status_extra_graduacao; ?>]</em>
    </li>
    <li>
        <p>Este atributo te dara sorte extra em cair baús nas Missões</p>
        <div class="meter animate <?php $treino->setCorBarra($porcentagem_sorte); ?>">
            <em>Sorte <?php echo $personagem->sorte + $sorte_equipados + $status_extra + $status_extra_graduacao; ?></em>
            <span style="width: <?php echo $porcentagem_sorte; ?>%"><span></span></span>
        </div>
        <em>+ [<?php echo $status_extra + $status_extra_graduacao; ?>]</em>
    </li>
</ul>

<div class="info-gold">
    Você possui <strong><?php echo $personagem->gold; ?></strong> golds
</div>

<ul class="treinar-itens">
    <p class="informativo">
        Os valores para treinar cada atributo será debitado de seus Golds.
    </p>
    <input type="hidden" name="qtdGold" id="qtdGold" value="<?php echo $personagem->gold; ?>" readonly="readonly" />
    <li class="forca">
        <span class="titulo">Força</span>
        <span class="descricao">Aumenta o dano nos ataques do seu guerreiro</span>
        <form id="treinarForca" method="post">
            <div class="valores-treino">
                <em>Golds</em>
                <a id="menos"><i class="fas fa-undo"></i></a>
                <input type="hidden" name="unidades" class="unidades" value="1" readonly="readonly" />
                <input type="hidden" name="golds" class="input-contador" value="<?php echo intval($valores->forca); ?>" readonly="readonly" />
                <input type="text" name="soma" class="soma" value="<?php echo intval($valores->forca) + 99; ?>" readonly="readonly" />
                <!--<a class="mais">+</a>-->
            </div>
            <input type="submit" name="treinar_forca" value="Treinar" />
            <div class="qtd-unidades">
                <em>+ <strong>1</strong> Força</em>
            </div>
        </form>
    </li>
    <li class="agilidade">
        <span class="titulo">Agilidade</span>
        <span class="descricao">Aumenta taxa de desvio contra o ataque de um inimigo</span>
        <form id="treinarAgilidade" method="post">
            <div class="valores-treino">
                <em>Golds</em>
                <a id="menos"><i class="fas fa-undo"></i></a>
                <input type="hidden" name="golds" class="input-contador" value="<?php echo intval($valores->agilidade); ?>" readonly="readonly" />
                <input type="hidden" name="unidades" class="unidades" value="1" readonly="readonly" />
                <input type="text" name="soma" class="soma" value="<?php echo intval($valores->agilidade) + 99; ?>" readonly="readonly" />
                <!--<a class="mais">+</a>-->
            </div>
            <input type="submit" name="treinar_agilidade" value="Treinar" />
            <div class="qtd-unidades">
                <em>+ <strong>1</strong> Agilidade</em>
            </div>
        </form>
    </li>
    <li class="habilidade">
        <span class="titulo">Habilidade</span>
        <span class="descricao">Aumenta a chance em acerto de ataques críticos</span>
        <form id="treinarHabilidade" method="post">
            <div class="valores-treino">
                <em>Golds</em>
                <a id="menos"><i class="fas fa-undo"></i></a>
                <input type="hidden" name="golds" class="input-contador" value="<?php echo intval($valores->habilidade); ?>" readonly="readonly" />
                <input type="hidden" name="unidades" class="unidades" value="1" readonly="readonly" />
                <input type="text" name="soma" class="soma" value="<?php echo intval($valores->habilidade) + 99; ?>" readonly="readonly" />
                <!--<a class="mais">+</a>-->
            </div>
            <input type="submit" name="treinar_habilidade" value="Treinar" />
            <div class="qtd-unidades">
                <em>+ <strong>1</strong> Habilidade</em>
            </div>
        </form>
    </li>
    <li class="resistencia">
        <span class="titulo">Resistência</span>
        <span class="descricao">Aumenta sua resistência a ataques.</span>
        <form id="treinarResistencia" method="post">
            <div class="valores-treino">
                <em>Golds</em>
                <a id="menos"><i class="fas fa-undo"></i></a>
                <input type="hidden" name="golds" class="input-contador" value="<?php echo intval($valores->resistencia); ?>" readonly="readonly" />
                <input type="hidden" name="unidades" class="unidades" value="1" readonly="readonly" />
                <input type="text" name="soma" class="soma" value="<?php echo intval($valores->resistencia) + 99; ?>" readonly="readonly" />
                <!--<a class="mais">+</a>-->
            </div>
            <input type="submit" name="treinar_resistencia" value="Treinar" />
            <div class="qtd-unidades">
                <em>+ <strong>1</strong> Resistência</em>
            </div>
        </form>
    </li>
    <li class="sorte">
        <span class="titulo">Sorte</span>
        <span class="descricao">Este atributo te dara sorte extra em cair baús nas Missões</span>
        <form id="treinarSorte" method="post">
            <div class="valores-treino">
                <em>Golds</em>
                <a id="menos"><i class="fas fa-undo"></i></a>
                <input type="hidden" name="golds" class="input-contador" value="<?php echo intval($valores->sorte); ?>" readonly="readonly" />
                <input type="hidden" name="unidades" class="unidades" value="1" readonly="readonly" />
                <input type="text" name="soma" class="soma" value="<?php echo intval($valores->sorte) + 99; ?>" readonly="readonly" />
                <!--<a class="mais">+</a>-->
            </div>
            <input type="submit" name="treinar_sorte" value="Treinar" />
            <div class="qtd-unidades">
                <em>+ <strong>1</strong> Sorte</em>
            </div>
        </form>
    </li>
</ul>