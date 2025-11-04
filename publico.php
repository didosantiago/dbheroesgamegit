<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if(Url::getURL(1) != null){
        if(Url::getURL(1) != 'ajax'){
            $idPersonagem = Url::getURL(1);
            $idUserP = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        }
    } else {
        $idPersonagem = $_SESSION['PERSONAGEMID'];
    }
    
    $personagem->getGuerreiro($idPersonagem);
    $dadosUser = $core->getDados('usuarios', 'WHERE id = '.$personagem->idUsuario);
    
    //STATUS EXTRA DAS EQUIPES
    $status_extra = intval($equipes->getStatusExtra($personagem->id));
    $status_extra_graduacao = intval($core->getStatusGraduacao($personagem->graduacao_id));
    $status_equipados = $inventario->getStatusEquipados($personagem->id);
    
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
    
    if(isset($_POST['adicionar'])){
        if(!$personagem->getExisteAmizade($_SESSION['PERSONAGEMID'], Url::getURL(1))){
            if(!$personagem->getExisteSolicitacaoAmizade($_SESSION['PERSONAGEMID'], Url::getURL(1))){
                $campos = array(
                    'idPersonagem' => $_SESSION['PERSONAGEMID'],
                    'idAmigo' => Url::getURL(1)
                );

                if($core->insert('personagens_amigos', $campos)){
                    $core->msg('sucesso', 'Adicionado aos Amigos.');
                    header('Location: '.BASE.'amigos');
                } else {
                    $core->msg('error', 'Erro ao adicionar aos Amigos.');
                }
            } else {
                $core->msg('error', 'Já existe uma solicitação de amizade pendente.');
            }
        } else {
            $core->msg('error', 'Este amigo já esta em sua lista.');
        }
    }
    
    if(isset($_POST['desfazer'])){
        if($core->isExists('personagens_amigos', 'WHERE idPersonagem = '.$_SESSION['PERSONAGEMID'].' AND idAmigo = '.$idPersonagem)){
            $core->delete('personagens_amigos', "idPersonagem = ".$_SESSION['PERSONAGEMID']." AND idAmigo = ".$idPersonagem);
            $core->msg('sucesso', 'Removido da Lista de Amigos.');
        }
        
        if($core->isExists('personagens_amigos', 'WHERE idAmigo = '.$idPersonagem.' AND idAmigo = '.$_SESSION['PERSONAGEMID'])){
            $core->delete('personagens_amigos', "idAmigo = ".$idPersonagem." AND idAmigo = ".$_SESSION['PERSONAGEMID']);
            $core->msg('sucesso', 'Removido da Lista de Amigos.');
        }
    }
?>

<input type="hidden" id="idAdversario" value="<?php echo $idPersonagem; ?>" />

<?php require_once 'includes/chat.php'; ?>

<div class="infos-guerreiro">
    <div class="inf-right">
        <h2>Informações</h2>
        <ul class="dados">
            <li class="alter level-guerreiro">
                <div class="titulo-coluna"><?php echo $personagem->nome; ?></div>
                <div class="resultado-coluna">
                    <em>LEVEL</em> 
                    <div class="level-label"><?php echo $personagem->nivel; ?></div>
                </div>
            </li>
            <li>
                <div class="titulo-coluna">ID</div>
                <div class="resultado-coluna"><?php echo $personagem->id; ?></div>
            </li>
            <li class="alter">
                <div class="titulo-coluna">Vitórias no Torneio</div>
                <div class="resultado-coluna"><?php echo $personagem->tam; ?></div>
            </li>
            <li>
                <div class="titulo-coluna">Vitórias PVP</div>
                <div class="resultado-coluna"><?php echo $personagem->getTotalPvpIndividual($personagem->id); ?></div>
            </li>
            <li class="alter">
                <?php 
                    if($personagem->nivel > 1){
                        $nivel_hp = 100 + ((intval($personagem->nivel) - 1) * 50);
                    } else {
                        $nivel_hp = 100;
                    }
                ?>
                <div class="titulo-coluna">HP</div>
                <div class="resultado-coluna campo-vip">
                    <?php 
                        if($user->vip == 1){
                            echo $personagem->hp.'/'.$nivel_hp;
                        } else {
                            echo '(Somente VIP)';
                        }
                    ?>
                </div>
            </li>
            <li>
                <div class="titulo-coluna">Energia</div>
                <div class="resultado-coluna campo-vip">
                    <?php 
                        if($user->vip == 1){
                            echo $personagem->energia - $personagem->energia_usada.'/'.$personagem->energia;
                        } else {
                            echo '(Somente VIP)';
                        }
                    ?>
                </div>
            </li>
            <li class="alter">
                <div class="titulo-coluna">KI</div>
                <div class="resultado-coluna campo-vip">
                    <?php 
                        if($user->vip == 1){
                            echo $personagem->mana - $personagem->ki_usado.'/'.$personagem->mana;
                        } else {
                            echo '(Somente VIP)';
                        }
                    ?>
                </div>
            </li>
            <li>
                <div class="titulo-coluna">Gold Faturado</div>
                <div class="resultado-coluna"><?php echo $personagem->gold_total; ?></div>
            </li>
            
            <li class="alter">
                <div class="titulo-coluna">Gold em Mãos</div>
                <div class="resultado-coluna campo-vip">
                    <?php 
                        if($user->vip == 1){
                            echo $personagem->gold;
                        } else {
                            echo '(Somente VIP)';
                        }
                    ?>
                </div>
            </li>
              
            <li>
                <div class="titulo-coluna">Pontos (Ganho ao Upar level)</div>
                <div class="resultado-coluna"><?php echo $personagem->pontos; ?></div>
            </li>
            <li class="alter">
                <div class="titulo-coluna">Força</div>
                <div class="resultado-coluna"><?php echo $personagem->forca + $forca_equipados + $status_extra +  $status_extra_graduacao; ?></div>
            </li>
            <li>
                <div class="titulo-coluna">Agilidade</div>
                <div class="resultado-coluna"><?php echo $personagem->agilidade + $agilidade_equipados + $status_extra +  $status_extra_graduacao; ?></div>
            </li>
            <li class="alter">
                <div class="titulo-coluna">Habilidade</div>
                <div class="resultado-coluna"><?php echo $personagem->habilidade + $habilidade_equipados + $status_extra +  $status_extra_graduacao; ?></div>
            </li>
            <li>
                <div class="titulo-coluna">Resistência</div>
                <div class="resultado-coluna"><?php echo $personagem->resistencia + $resistencia_equipados + $status_extra +  $status_extra_graduacao; ?></div>
            </li>
            <li class="alter">
                <div class="titulo-coluna">Sorte</div>
                <div class="resultado-coluna"><?php echo $personagem->sorte + $sorte_equipados + $status_extra +  $status_extra_graduacao; ?></div>
            </li>
        </ul>
    </div>
    <div class="foto">
        <?php if($equipes->existsInEquipe($personagem->id)){ ?>
            <?php $dadosEquipe = $equipes->printEquipe($personagem->id); ?>
        
            <div class="minha-equipe">
                <a href="<?php echo BASE; ?>equipes/<?php echo $dadosEquipe->id; ?>">
                    <img src="<?php echo BASE.'assets/equipes/'.$dadosEquipe->foto; ?>" alt="<?php echo $dadosEquipe->nome; ?>" width="300" />
                    <h2 class="name-equipe">Equipe - <?php echo $dadosEquipe->nome.' ['.$dadosEquipe->sigla.']'; ?></h2>
                </a>
            </div>
        <?php } ?>
        
        <?php if($dadosUser->vip == 1){ ?>
            <img class="emblema-vip" src="<?php echo BASE; ?>assets/icones/bt-vip.png" />
        <?php } ?>
            
        <div class="painel-guerreiro">
            <ul class="botoes-publico">
                <?php if(!$personagem->verificaPersonagem($idPersonagem, $user->id)){ ?>
                    <?php if(!$personagem->getExisteAmizade($_SESSION['PERSONAGEMID'], Url::getURL(1))){ ?>
                        <?php if(!$personagem->getExisteSolicitacaoAmizade($_SESSION['PERSONAGEMID'], Url::getURL(1))){ ?>
                            <li>
                                <form id="AdicionarAmigo" method="post">
                                    <input type="hidden" name="adicionar" value="" />
                                    <button type="submit" class="adicionar"  title="Adicionar Amigo">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </form>
                            </li>
                        <?php } else { ?>
                            <li>
                                <form id="AdicionarAmigo" method="post">
                                    <button type="submit" disabled class="pendente" title="Pedido de Amizade Enviado">
                                        <i class="fas fa-user-clock"></i>
                                    </button>
                                </form>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li>
                            <form id="DesfazerAmizade" method="post">
                                <input type="hidden" name="desfazer" value="" />
                                <button type="submit" class="desfazer" title="Desfazer Amizade">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </form>
                        </li>
                    <?php } ?>
                <?php } ?>
                <?php if(!isset($_SESSION['pvp'])){ ?>
                    <li>
                        <a href="<?php echo BASE.'combate/'.$personagem->id; ?>" class="atacar">
                            <img src="<?php echo BASE; ?>assets/icones/bt-pvp.png" />
                        </a>
                    </li>
                <?php } ?>
            </ul>
            
            <ul class="slots-adesivos-left slots-adsivos">
                <?php 
                    $lista_itens_left = array(9,10,11,12,13);
                    $inventario->getSlotsAdesivosPerfil($personagem->id, $lista_itens_left); 
                ?>
            </ul>
            
            <div class="foto-principal">
                <?php $ft = str_replace('cards/', '', $personagem->foto); ?>
                <img src="<?php echo BASE.'assets/cards/'.$ft; ?>" class="ft-guerreiro" alt="<?php echo $personagem->nome; ?>" />
                <div class="graduacao-patente">
                    <div class="graduacao_img">
                        <h3 class="personagem-nome"><?php echo $personagem->nome; ?></h3>
                        <div class="nivel-atual">
                            <h4>LEVEL</h4>
                            <span><?php echo $personagem->nivel; ?></span>
                        </div>
                        <?php $personagem->getGraduacao($personagem->nivel); ?>
                        <?php $personagem->getGraduacaoTexto($personagem->nivel); ?>
                        <div class="status-extra-graduacao">
                            <span>Acrescenta </span>
                            <div class="label-status">
                                + <?php echo $status_extra_graduacao; ?>
                            </div>
                            <span> de Status</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <ul class="slots-adesivos-right slots-adsivos">
                <?php 
                    $lista_itens_right = array(14,15,16,17,18);
                    $inventario->getSlotsAdesivosPerfil($personagem->id, $lista_itens_right); 
                ?>
            </ul>
        </div>
        <ul class="equipamento">
            <h4>Equipamento</h4>
            <?php $inventario->getSlotsEquipados($idPersonagem); ?>
        </ul>
    </div>
</div>

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
        <p>Aumenta sua resistência a ataques</p>
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

<ul class="indicadores">
    <h2>Estatísticas de Missões</h2>
    <?php echo $missoes->getCountMissoes($personagem->id); ?>
</ul>