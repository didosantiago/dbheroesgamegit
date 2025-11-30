<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Precisa estar logado
    if (!isset($_SESSION['PERSONAGEMID'])) {
        header('Location: ' . BASE . 'portal');
        exit;
    }

    // ID vindo da URL: /publico/ID
    $url_param = Url::getURL(1);

    if ($url_param !== null && is_numeric($url_param)) {
        $idPersonagem = (int)$url_param;

        // Confirma se o personagem existe
        $idUserP = $core->getDados('usuarios_personagens', 'WHERE id = '.$idPersonagem);
        if (!$idUserP) {
            $idPersonagem = (int)$_SESSION['PERSONAGEMID'];
        }
    } else {
        // Se não tiver ID válido na URL, mostra o próprio personagem logado
        $idPersonagem = (int)$_SESSION['PERSONAGEMID'];
    }

    // Carrega dados do personagem
    $personagem->getGuerreiro($idPersonagem);

    if (empty($personagem->idUsuario)) {
        echo "Erro: Personagem não encontrado.";
        exit;
    }

    $dadosUser = $core->getDados('usuarios', 'WHERE id = ' . $personagem->idUsuario);

    // STATUS EXTRA DAS EQUIPES
    $status_extra           = (int)$equipes->getStatusExtra($personagem->id);
    $status_extra_graduacao = (int)$core->getStatusGraduacao($personagem->graduacao_id);
    $status_equipados       = $inventario->getStatusEquipados($personagem->id);

    $forca_equipados        = (int)$status_equipados['forca'];
    $agilidade_equipados    = (int)$status_equipados['agilidade'];
    $habilidade_equipados   = (int)$status_equipados['habilidade'];
    $resistencia_equipados  = (int)$status_equipados['resistencia'];
    $sorte_equipados        = (int)$status_equipados['sorte'];

    $forca       = $personagem->forca       + $status_extra + $status_extra_graduacao + $forca_equipados;
    $agilidade   = $personagem->agilidade   + $status_extra + $status_extra_graduacao + $agilidade_equipados;
    $habilidade  = $personagem->habilidade  + $status_extra + $status_extra_graduacao + $habilidade_equipados;
    $resistencia = $personagem->resistencia + $status_extra + $status_extra_graduacao + $resistencia_equipados;
    $sorte       = $personagem->sorte       + $status_extra + $status_extra_graduacao + $sorte_equipados;

    $porcentagem_forca       = $treino->getPorcentagemForca($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_agilidade   = $treino->getPorcentagemAgilidade($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_habilidade  = $treino->getPorcentagemHabilidade($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_resistencia = $treino->getPorcentagemResistencia($forca, $agilidade, $habilidade, $resistencia, $sorte);
    $porcentagem_sorte       = $treino->getPorcentagemSorte($forca, $agilidade, $habilidade, $resistencia, $sorte);

    // Amizades
    if (isset($_POST['adicionar'])) {
        if (!$personagem->getExisteAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)) {
            if (!$personagem->getExisteSolicitacaoAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)) {
                $campos = array(
                    'idPersonagem' => $_SESSION['PERSONAGEMID'],
                    'idAmigo'      => $idPersonagem
                );
                if ($core->insert('personagens_amigos', $campos)) {
                    $core->msg('sucesso', 'Adicionado aos Amigos.');
                    header('Location: ' . BASE . 'amigos');
                } else {
                    $core->msg('error', 'Erro ao adicionar aos Amigos.');
                }
            } else {
                $core->msg('error', 'Já existe uma solicitação de amizade pendente.');
            }
        } else {
            $core->msg('error', 'Este amigo já está em sua lista.');
        }
    }

    if (isset($_POST['desfazer'])) {
        if ($core->isExists('personagens_amigos', 'WHERE idPersonagem = '.$_SESSION['PERSONAGEMID'].' AND idAmigo = '.$idPersonagem)) {
            $core->delete('personagens_amigos', 'idPersonagem = '.$_SESSION['PERSONAGEMID'].' AND idAmigo = '.$idPersonagem);
            $core->msg('sucesso', 'Removido da Lista de Amigos.');
        }

        if ($core->isExists('personagens_amigos', 'WHERE idAmigo = '.$idPersonagem.' AND idAmigo = '.$_SESSION['PERSONAGEMID'])) {
            $core->delete('personagens_amigos', 'idAmigo = '.$idPersonagem.' AND idAmigo = '.$_SESSION['PERSONAGEMID']);
            $core->msg('sucesso', 'Removido da Lista de Amigos.');
        }
    }
?>
<input type="hidden" id="idAdversario" value="<?php echo $idPersonagem; ?>" />
<?php require_once 'includes/chat.php'; ?>

<!-- TOP GRID: INFORMAÇÕES + FOTO/PERFIL -->
<div class="infos-guerreiro" style="display: flex; flex-direction: row; justify-content: flex-start; gap:34px; margin-bottom:40px;" >
    
    <div class="inf-right" style="min-width:370px;">
        
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
                    if ($personagem->nivel > 1) {
                        $nivel_hp = 100 + ((intval($personagem->nivel) - 1) * 50);
                    } else {
                        $nivel_hp = 100;
                    }
                ?>
                <div class="titulo-coluna">HP</div>
                <div class="resultado-coluna campo-vip">
                <?php 
                    if ($user->vip == 1) {
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
                    if ($user->vip == 1) {
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
                    if ($user->vip == 1) {
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
                    if ($user->vip == 1) {
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
                <div class="resultado-coluna"><?php echo $personagem->forca + $forca_equipados + $status_extra + $status_extra_graduacao; ?></div>
            </li>
            <li>
                <div class="titulo-coluna">Agilidade</div>
                <div class="resultado-coluna"><?php echo $personagem->agilidade + $agilidade_equipados + $status_extra + $status_extra_graduacao; ?></div>
            </li>
            <li class="alter">
                <div class="titulo-coluna">Habilidade</div>
                <div class="resultado-coluna"><?php echo $personagem->habilidade + $habilidade_equipados + $status_extra + $status_extra_graduacao; ?></div>
            </li>
            <li>
                <div class="titulo-coluna">Resistência</div>
                <div class="resultado-coluna"><?php echo $personagem->resistencia + $resistencia_equipados + $status_extra + $status_extra_graduacao; ?></div>
            </li>
            <li class="alter">
                <div class="titulo-coluna">Sorte</div>
                <div class="resultado-coluna"><?php echo $personagem->sorte + $sorte_equipados + $status_extra + $status_extra_graduacao; ?></div>
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
    <?php if(!$personagem->getExisteAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)){ ?>
        <?php if(!$personagem->getExisteSolicitacaoAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)){ ?>
            <li>
                <form id="AdicionarAmigo" method="post">
                    <input type="hidden" name="adicionar" value="" />
                    <button type="submit" class="adicionar" title="Adicionar Amigo">
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

            <div class="profile-character-wrapper" 
                style="border: 4px solid #7bff00ff;
                border-radius: 18px;
                box-shadow: 0 0 16px #24ee1d66;
                background: rgba(0, 0, 0, 0.87);
                padding: 18px 12px;
                max-width: 300px;
                margin: 0 auto; display: flex; flex-direction: row; align-items: center; justify-content: center;">
                <ul class="slots-adesivos-left slots-adsivos" style="display: flex; flex-direction: column; gap: 5px;">
                
                </ul>
                <div class="foto-principal" style="margin: 0 17px;">
                    <?php $ft = str_replace('cards/', '', $personagem->foto); ?>
                    <div class="profile-picture-frame" style="position:relative; width:300px; height:300px; margin:0 auto;">
                        <img src="<?php echo BASE.'assets/cards/'.$ft; ?>" 
                            class="ft-guerreiro" 
                            alt="<?php echo $personagem->nome; ?>" 
                            style="width:100%; height:100%; object-fit:cover; z-index:1; position:relative;" />
                        <img src="<?php echo BASE.'assets/borders/border-blue.png'; ?>" 
                            class="profile-border" 
                            alt="profile border" 
                            style="position:absolute; left:-50px; top:-76px; width:400px; height:430px; z-index:2; pointer-events:none;" />
                    </div>
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

                <ul class="slots-adesivos-right slots-adsivos" style="display: flex; flex-direction: column; gap: 5px;">
    
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Horizontal STATUS & EQUIPADOS panel -->
<div class="status-equipados-wrapper" style="display:flex; align-items:flex-start; justify-content:center; gap:34px; margin-top:40px;">
    <!-- STATUS COLUMN -->
    <div class="status-attributes">
           <h2>Hatributos do guerreiro</h2>
        <ul class="status">
            <li>
                <p>Aumenta o dano nos ataques do seu guerreiro</p>
                <div class="meter animate <?php $treino->setCorBarra($porcentagem_forca); ?>">
                    <em>FORÇA <?php echo $personagem->forca; ?></em>
                    <span style="width: <?php echo $porcentagem_forca; ?>%"><span></span></span>
                </div>
                <em style="color: #5b5;">+ [<?php echo $forca_equipados + $status_extra + $status_extra_graduacao; ?>]</em>
            </li>
            <li>
                <p>Aumenta taxa de desvio contra o ataque de um inimigo</p>
                <div class="meter animate <?php $treino->setCorBarra($porcentagem_agilidade); ?>">
                    <em>AGILIDADE <?php echo $personagem->agilidade; ?></em>
                    <span style="width: <?php echo $porcentagem_agilidade; ?>%"><span></span></span>
                </div>
                <em style="color: #5b5;">+ [<?php echo $agilidade_equipados + $status_extra + $status_extra_graduacao; ?>]</em>
            </li>
            <li>
                <p>Aumenta a chance em acerto de ataques críticos</p>
                <div class="meter animate <?php $treino->setCorBarra($porcentagem_habilidade); ?>">
                    <em>HABILIDADE <?php echo $personagem->habilidade; ?></em>
                    <span style="width: <?php echo $porcentagem_habilidade; ?>%"><span></span></span>
                </div>
                <em style="color: #5b5;">+ [<?php echo $habilidade_equipados + $status_extra + $status_extra_graduacao; ?>]</em>
            </li>
            <li>
                <p>Aumenta sua resistência a ataques</p>
                <div class="meter animate <?php $treino->setCorBarra($porcentagem_resistencia); ?>">
                    <em>RESISTÊNCIA <?php echo $personagem->resistencia; ?></em>
                    <span style="width: <?php echo $porcentagem_resistencia; ?>%"><span></span></span>
                </div>
                <em style="color: #5b5;">+ [<?php echo $resistencia_equipados + $status_extra + $status_extra_graduacao; ?>]</em>
            </li>
            <li>
                <p>Este atributo te dara sorte extra em cair baús nas Missões</p>
                <div class="meter animate <?php $treino->setCorBarra($porcentagem_sorte); ?>">
                    <em>SORTE <?php echo $personagem->sorte; ?></em>
                    <span style="width: <?php echo $porcentagem_sorte; ?>%"><span></span></span>
                </div>
                <em style="color: #5b5;">+ [<?php echo $sorte_equipados + $status_extra + $status_extra_graduacao; ?>]</em>
            </li>

        </ul>
    </div>
    <!-- EQUIPADOS COLUMN -->
    <div class="equipados-panel">
        <div class="equipamentos" style="margin-top:0;">
            <h3 style="color: #FFD700; text-shadow: 2px 2px 4px #000; text-align: center; text-transform: uppercase;">EMBLEMAS EQUIPADOS</h3><br>
            <ul style="justify-content: center; display: flex; padding: 0; list-style: none; gap: 5px;">
                <?php
                    $sql = "SELECT pie.*, i.imagem, i.raridade 
                            FROM personagens_itens_equipados pie
                            LEFT JOIN itens i ON i.id = pie.idItem
                            WHERE pie.idPersonagem = ? AND pie.emblema = 1 
                            ORDER BY pie.slot ASC";
                    $stmt = DB::prepare($sql);
                    $stmt->execute([$idPersonagem]);
                    if($stmt->rowCount() > 0){
                        foreach($stmt->fetchAll() as $emb){
                            $bg = 'url('.BASE.'assets/slot-emblema.png)';
                            if($emb->vazio == 0 && $emb->idItem > 0){
                                echo '<li style="width: 71px; height: 71px; background-image: '.$bg.'; background-size: cover; position: relative;" class="raridade-'.$emb->raridade.'">';
                                echo '<img src="'.BASE.'assets/itens/'.$emb->imagem.'" width="100%" height="100%" style="object-fit: contain;">';
                                echo '</li>';
                            } else {
                                echo '<li style="width: 71px; height: 71px; background-image: '.$bg.'; background-size: cover;"></li>';
                            }
                        }
                    } else {
                        for($i=0; $i<3; $i++){
                            echo '<li style="width: 71px; height: 71px; background-image: url('.BASE.'assets/slot-emblema.png); background-size: cover;"></li>';
                        }
                    }
                ?>
            </ul>
        </div>
        <div class="equipamentos" style="margin-top: 12px;">
            <h3 style="color: #FFD700; text-shadow: 2px 2px 4px #000; text-align: center; text-transform: uppercase;">EQUIPAMENTO</h3><br>
            <ul style="justify-content: center; display: flex; padding: 0; list-style: none; gap: 5px;">
                <?php $inventario->getSlotsEquipados($idPersonagem); ?>
            </ul>
        </div>
        <div class="equipamentos" style="margin-top: 12px;">
            <h3 style="color: #FFD700; text-shadow: 2px 2px 4px #000; text-align: center; text-transform: uppercase;">ADESIVOS EQUIPADOS</h3><br>
            <ul style="justify-content: center; display: flex; flex-wrap: wrap; max-width: 400px; margin: 0 auto; padding: 0; list-style: none; gap: 5px;">
                <?php
                    $sql = "SELECT pie.*, i.imagem, i.raridade 
                            FROM personagens_itens_equipados pie
                            LEFT JOIN itens i ON i.id = pie.idItem
                            WHERE pie.idPersonagem = ? AND pie.adesivo = 1 
                            ORDER BY pie.slot ASC";
                    $stmt = DB::prepare($sql);
                    $stmt->execute([$idPersonagem]);
                    if($stmt->rowCount() > 0){
                        foreach($stmt->fetchAll() as $ad){
                            $bg = 'url('.BASE.'assets/slot-amarelo.png)';
                            if($ad->vazio == 0 && $ad->idItem > 0){
                                echo '<li style="width: 71px; height: 71px; background-image: '.$bg.'; background-size: cover; position: relative;" class="raridade-'.$ad->raridade.'">';
                                $img = !empty($ad->imagem) ? $ad->imagem : 'sem-imagem.png';
                                echo '<img src="'.BASE.'assets/itens/'.$img.'" width="100%" height="100%" style="object-fit: contain;">';
                                echo '</li>';
                            } else {
                                echo '<li style="width: 71px; height: 71px; background-image: '.$bg.'; background-size: cover;"></li>';
                            }
                        }
                    } else {
                        for($i=0; $i<10; $i++){
                            echo '<li style="width: 71px; height: 71px; background-image: url('.BASE.'assets/slot-amarelo.png); background-size: cover;"></li>';
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
</div>

<ul class="indicadores">
    <h2>Estatísticas de Missões</h2>
    <?php echo $missoes->getCountMissoes($personagem->id); ?>
</ul>

<script>
// === 2. AUTO SCROLL SCRIPT ===
// Scroll down 520px on page load
// =============================
const pixelsToScroll = 350; 
window.scrollTo({
    top: pixelsToScroll,
    behavior: "smooth"
});

</script>