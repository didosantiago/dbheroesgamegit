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
    
    $idPersonagem = null;

    if ($url_param !== null && $url_param !== '' && is_numeric($url_param)) {
        $idPersonagem = (int)$url_param;
        
        // Confirma se o personagem existe
        $stmt = DB::prepare("SELECT id, idUsuario FROM usuarios_personagens WHERE id = ?");
        $stmt->execute([$idPersonagem]);
        $idUserP = $stmt->fetch();
        
        if (!$idUserP) {
            $core->msg('error', 'Personagem não encontrado.');
            header('Location: ' . BASE . 'ranking');
            exit;
        }
    } else {
        // Se não tiver ID válido na URL, redireciona para ranking
        $core->msg('error', 'ID de personagem inválido.');
        header('Location: ' . BASE . 'ranking');
        exit;
    }

    // Carrega dados do personagem
    $personagem->getGuerreiro($idPersonagem);

    if (empty($personagem->idUsuario) || empty($personagem->id)) {
        $core->msg('error', 'Erro ao carregar dados do personagem.');
        header('Location: ' . BASE . 'ranking');
        exit;
    }

    $dadosUser = $core->getDados('usuarios', 'WHERE id = ' . $personagem->idUsuario);
    
    if (!$dadosUser) {
        $core->msg('error', 'Usuário não encontrado.');
        header('Location: ' . BASE . 'ranking');
        exit;
    }

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
    // Amizades
    if(isset($_POST['adicionar'])){
        if(!$personagem->getExisteAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)){
            if(!$personagem->getExisteSolicitacaoAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)){
                $campos = array(
                    'idPersonagem' => $_SESSION['PERSONAGEMID'],
                    'idAmigo' => $idPersonagem
                );
                if($core->insert('personagens_amigos', $campos)){
                    $core->msg('sucesso', 'Adicionado aos Amigos.');
                    header('Location: '.BASE.'publico/'.$idPersonagem); // STAY ON PROFILE
                    exit;
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

    if(isset($_POST['desfazer'])){
        // Remove friendship in BOTH directions
        if($core->isExists('personagens_amigos', "WHERE idPersonagem = ".$_SESSION['PERSONAGEMID']." AND idAmigo = ".$idPersonagem)){
            $core->delete('personagens_amigos', "idPersonagem = ".$_SESSION['PERSONAGEMID']." AND idAmigo = ".$idPersonagem);
            $core->msg('sucesso', 'Removido da Lista de Amigos.');
        }
        
        if($core->isExists('personagens_amigos', "WHERE idPersonagem = ".$idPersonagem." AND idAmigo = ".$_SESSION['PERSONAGEMID'])){
            $core->delete('personagens_amigos', "idPersonagem = ".$idPersonagem." AND idAmigo = ".$_SESSION['PERSONAGEMID']);
            $core->msg('sucesso', 'Removido da Lista de Amigos.');
        }
        
        // STAY ON PROFILE after removing
        header('Location: '.BASE.'publico/'.$idPersonagem);
        exit;
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

            <!-- ACTION BUTTONS CONTAINER -->
            <!-- ACTION BUTTONS CONTAINER -->
            <div class="action-buttons-container">
                <?php 
                // Check if viewing another player's profile (not your own character)
                if($idPersonagem != $_SESSION['PERSONAGEMID']){ 
                    
                    // Get current player data to check conditions
                    $myChar = new Personagens();
                    $myChar->getGuerreiro($_SESSION['PERSONAGEMID']);
                ?>
                    
                    <div class="friend-actions">
                        <?php if(!$personagem->getExisteAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)){ ?>
                            <?php if(!$personagem->getExisteSolicitacaoAmizade($_SESSION['PERSONAGEMID'], $idPersonagem)){ ?>
                                <!-- Add Friend Button -->
                                <form id="AdicionarAmigo" method="post">
                                    <input type="hidden" name="adicionar" value="1" />
                                    <button type="submit" class="action-btn btn-add-friend">
                                        <i class="fas fa-user-plus"></i>
                                        <span>ADICIONAR AMIGOS</span>
                                    </button>
                                </form>
                            <?php } else { ?>
                                <!-- Pending Request -->
                                <button type="button" disabled class="action-btn btn-pending">
                                    <i class="fas fa-clock"></i>
                                    <span>PENDENTE</span>
                                </button>
                            <?php } ?>
                        <?php } else { ?>
                            <!-- Already Friends - GREEN OUTLINE STYLE (same as add friend button) -->
                            <div class="friends-compact">
                                <button type="button" disabled class="action-btn btn-already-friends">
                                    <i class="fas fa-user-check"></i>
                                    <span>AMIGOS</span>
                                </button>
                                
                                <!-- Tiny Remove Button -->
                                <form id="DesfazerAmizade" method="post" style="display:inline;">
                                    <input type="hidden" name="desfazer" value="1" />
                                    <button type="submit" class="btn-remove-tiny" title="Remover amigo" onclick="return confirm('Remover este amigo?');">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        <?php } ?>


                    </div>

                    <!-- PVP ATTACK BUTTON -->
                    <?php 
                    // Check all PVP conditions
                    $canAttack = true;
                    $errorMessages = array();
                    
                    if(isset($_SESSION['pvp'])){
                        $canAttack = false;
                        $errorMessages[] = "Você já está em batalha PVP";
                    }
                    
                    if($myChar->nivel < 10){
                        $canAttack = false;
                        $errorMessages[] = "Você precisa ter nível 10";
                    }
                    
                    if($personagem->nivel < 10){
                        $canAttack = false;
                        $errorMessages[] = "Adversário precisa ter nível 10";
                    }
                    
                    if($myChar->hp <= 0){
                        $canAttack = false;
                        $errorMessages[] = "Seu HP está zerado";
                    }
                    
                    if($personagem->hp <= 0){
                        $canAttack = false;
                        $errorMessages[] = "HP do adversário zerado";
                    }
                    
                    if($myChar->gold < 20){
                        $canAttack = false;
                        $errorMessages[] = "Você precisa de 20 gold";
                    }
                    
                    if($personagem->gold < 20){
                        $canAttack = false;
                        $errorMessages[] = "Adversário sem gold";
                    }
                    
                    if($equipes->verificaMembrosEquipe($_SESSION['PERSONAGEMID'], $idPersonagem)){
                        $canAttack = false;
                        $errorMessages[] = "Não ataque sua equipe";
                    }
                    
                    if($batalha->playerAtacadoDAY($_SESSION['PERSONAGEMID'], $idPersonagem)){
                        $canAttack = false;
                        $errorMessages[] = "Aguarde até amanhã";
                    }
                    // Check if attacking character from same account
            if($personagem->idUsuario == $user->id){
                $canAttack = false;
                $errorMessages[] = "Você não pode atacar um personagem da sua conta";
            }

                    
                    if($batalha->getAtacouRecente($_SESSION['PERSONAGEMID'], $idPersonagem)){
                        $canAttack = false;
                        $errorMessages[] = "Aguarde 10 minutos";
                    }
                    ?>
                    
                    <div class="pvp-attack-section">
                        <?php if($canAttack){ ?>
                            <a href="<?php echo BASE.'combate/'.$idPersonagem; ?>" class="btn-atacar-pvp">
                                <i class="fas fa-skull"></i>
                                
                                <span>DESAFIAR para PvP</span>
                            </a>


                        <?php } else { ?>
                            <button type="button" disabled class="btn-atacar-pvp disabled" title="<?php echo implode(' | ', $errorMessages); ?>">
                                <i class="fas fa-ban"></i>
                                <span>BLOQUEADO</span>
                            </button>
                            <div class="pvp-requirements">
                                <?php foreach($errorMessages as $msg){ ?>
                                    <p><i class="fas fa-exclamation-circle"></i> <?php echo $msg; ?></p>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>

                <?php 
                } else {
                    // Viewing your own profile
                    echo '<div style="text-align:center; color:#888; padding:20px; font-style:italic;">Este é o seu personagem</div>';
                }
                ?>
            </div>





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
        <h2>Atributos do guerreiro</h2>
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
// Auto scroll on page load
const pixelsToScroll = 350; 
window.scrollTo({
    top: pixelsToScroll,
    behavior: "smooth"
});
</script>

<style>
/* ACTION BUTTONS - MODERN DESIGN */
.action-buttons-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin: 25px auto 15px;
    align-items: center;
    width: 100%;
    max-width: 340px;
}

.friend-actions {

    flex-direction: column;
    gap: 10px;
    width: 100%;
}

/* Friend Button Styles */
.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 11px 18px;
    border: 2px solid;
    border-radius: 6px;
    background: linear-gradient(135deg, rgba(0,0,0,0.4), rgba(0,0,0,0.2));
    backdrop-filter: blur(5px);
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-add-friend {
    border-color: #4CAF50;
    color: #4CAF50;
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.2);
}

.btn-add-friend:hover {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(76, 175, 80, 0.5);
    border-color: #5FD663;
}

.btn-pending {
    border-color: #FFA500;
    color: #FFA500;
    opacity: 0.7;
    cursor: not-allowed;
    box-shadow: 0 0 10px rgba(255, 165, 0, 0.15);
}

.btn-remove-friend-small {
    border-color: #ff4444;
    color: #ff4444;
    font-size: 13px;
    padding: 9px 16px;
}

.btn-remove-friend-small:hover {
    background: linear-gradient(135deg, #ff4444, #cc0000);
    color: white;
    transform: translateY(-1px);
    border-color: #ff6666;
}

.friends-status-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 11px 18px;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(76, 175, 80, 0.05));
    border: 2px solid #4CAF50;
    border-radius: 6px;
    color: #5FD663;
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.2);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
}

.btn-icon, .friends-icon {
    font-size: 18px;
}

.btn-content, .friends-content {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 6px;
}

.btn-label, .friends-label {
    font-size: 12px;
    opacity: 0.9;
}

.btn-text, .friends-text {
    font-size: 14px;
    font-weight: bold;
}

/* PVP ATTACK BUTTON - SIMPLIFIED AND POLISHED */
.pvp-attack-section {
    width: 100%;
    margin-top: 8px;
}

.btn-atacar-pvp {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin: 0px 0px 20px 0px ;
    background: linear-gradient(135deg, #ff3333 0%, #dd0000 100%);
    color: white;
    padding: 14px 24px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 4px 20px rgba(255, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    cursor: pointer;
    width: 86%;
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    position: relative;
    overflow: hidden;
}

.btn-atacar-pvp::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    transform: rotate(45deg);
    animation: shine 3s ease-in-out infinite;
}

@keyframes shine {
    0%, 100% { transform: translateX(-100%) rotate(45deg); }
    50% { transform: translateX(100%) rotate(45deg); }
}

.btn-atacar-pvp:hover {
    background: linear-gradient(135deg, #ff0000 0%, #bb0000 100%);
    box-shadow: 0 6px 25px rgba(255, 0, 0, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.btn-atacar-pvp:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(255, 0, 0, 0.5);
}

.btn-atacar-pvp.disabled {
    background: linear-gradient(135deg, #ff3b19ff 0%, #ff650cff 100%);
    cursor: not-allowed;
    opacity: 0.6;
    box-shadow: none;
    width: 338px;
}

.btn-atacar-pvp.disabled:hover {
    transform: none;
    box-shadow: none;
}

.btn-atacar-pvp.disabled::after {
    display: none;
}

.btn-atacar-pvp i {
    font-size: 18px;
    animation: pulse-icon 2s ease-in-out infinite;
}

@keyframes pulse-icon {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

.btn-atacar-pvp.disabled i {
    animation: none;
}

.pvp-text {
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.4);
}

/* PVP Requirements Messages */
.pvp-requirements {
    margin-top: 12px;
    margin: 10px 0px 30px 0px;
    padding: 1px;
    background: linear-gradient(135deg, rgba(255, 68, 68, 0.12), rgba(255, 68, 68, 0.05));
    border: 1px solid rgba(255, 68, 68, 0.3);
    border-radius: 6px;
    box-shadow: 0 0 10px rgba(255, 68, 68, 0.15);
}

.pvp-requirements p {
    color: #ff8888;
    font-size: 10px;
    margin: 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;

}

.pvp-requirements i {
    font-size: 11px;
    color: #ff6666;
}

/* Compact Friends Display */
.friends-compact {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}

/* Already Friends - Same style as add friend button */
.btn-already-friends {
    flex: 1;
    border-color: #4CAF50;
    color: #4CAF50;
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.2);
    cursor: default;
}

.btn-already-friends:hover {
    background: linear-gradient(135deg, rgba(0,0,0,0.4), rgba(0,0,0,0.2));
    transform: none;
}

/* Tiny Remove Button */
.btn-remove-tiny {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #ff4444;
    background: linear-gradient(135deg, rgba(255, 68, 68, 0.2), rgba(255, 68, 68, 0.05));
    color: #ff6666;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    flex-shrink: 0;
}

.btn-remove-tiny:hover {
    background: linear-gradient(135deg, #ff4444, #cc0000);
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(255, 68, 68, 0.4);
}

</style>
