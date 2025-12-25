<?php
switch($acao){
    default:
?>

<?php 
    // Centralized session ID retrieval
    $idPersonagem = null;
    if(isset($_SESSION['PERSONAGEM']['ID']) && !empty($_SESSION['PERSONAGEM']['ID'])) {
        $idPersonagem = $_SESSION['PERSONAGEM']['ID'];
    } else if(isset($_SESSION['PERSONAGEM_ID']) && !empty($_SESSION['PERSONAGEM_ID'])) {
        $idPersonagem = $_SESSION['PERSONAGEM_ID'];
    } else if(isset($_SESSION['PERSONAGEMID']) && !empty($_SESSION['PERSONAGEMID'])) {
        $idPersonagem = $_SESSION['PERSONAGEMID'];
    }

    // ✅ FIX: Use Url::getURL() as STATIC method (not $Url->getURL())
    if(Url::getURL(1) != null && Url::getURL(1) != 'ajax'){
        $idGet = Url::getURL(1);
        $dados_equipe = $equipes->dadosEquipeAtual($idPersonagem, $idGet);
    } else {
        $dados_equipe = $equipes->dadosEquipeAtual($idPersonagem, '');
        if($dados_equipe != 0){
            $idGet = $dados_equipe->id;
        } else {
            $idGet = '';
        }
    }

    // ✅ ADDED: Check and update team level before displaying
    if($dados_equipe != 0 && !empty($idGet)){
        $equipes->verificaLevel($idGet); // Check only this team
        // Reload team data after level update
        $dados_equipe = $equipes->dadosEquipeAtual($idPersonagem, $idGet);
    }

    // ✅ Check if we have valid team data
    if(!$dados_equipe || $dados_equipe == 0 || empty($idGet)) {
        // No team found - show create team message
        ?>
        <div style="padding: 40px; text-align: center;">
            <?php if($idPersonagem): ?>
                <?php if(isset($personagem->nivel) && $personagem->nivel >= 10): ?>
                    <h2 style="color: #fff; margin-bottom: 20px;">Você não está em uma equipe</h2>
                    <a class="bt-criar-equipe" href="<?php echo BASE; ?>equipes/add">
                        <i class="fas fa-plus-circle"></i>
                        <span>Criar Equipe</span>
                    </a>
                <?php else: ?>
                    <h2 class="title" style="color: #FFF;">É necessário ter level 10 no mínimo para criar equipes.</h2>
                <?php endif; ?>
            <?php else: ?>
                <h2 style="color: #fff;">Faça login para visualizar equipes.</h2>
            <?php endif; ?>
        </div>
        <?php
        break; // Stop processing here
    }

    // ✅ NOW check permissions (only if we have valid team data)
    $isLeader = false;
    $isMember = false;
    $isVisitor = true;

    if($idPersonagem) {
        $isLeader = ($idPersonagem == $dados_equipe->idCriador) || $equipes->isLider($idPersonagem, $idGet);
        $isMember = $equipes->isMembro($idPersonagem, $idGet);
        $isVisitor = (!$isLeader && !$isMember);
    }
?>

<!-- ✅ CHAT SECTION - Only for members -->
<?php if(!$isVisitor && $idPersonagem && !empty($idGet)): ?>
    <?php require_once 'includes/chat-equipe.php'; ?>
<?php endif; ?>

<div class="perfil-equipe">
    <div class="foto-equipe">
        <img src="<?php echo BASE.'assets/equipes/'.$dados_equipe->foto; ?>" alt="<?php echo $dados_equipe->nome; ?>">
        <?php if($isLeader): ?>
            <a class="editar-dados" href="<?php echo BASE; ?>equipes/editar/<?php echo $dados_equipe->id; ?>">
                <i class="fas fa-edit"></i>
                <span>Editar Dados da Equipe</span>
            </a>
        <?php endif; ?>
    </div>
    
    <div class="info-equipe">
        <h4><?php echo $dados_equipe->nome ?></h4>
        <span class="sigla"><?php echo $dados_equipe->sigla ?></span>
        
        <!-- Líder da Equipe -->
        <div class="lider">
            <h5>Líder da Equipe</h5>
            <?php $dadosLider = $equipes->getDadosCriador($dados_equipe->idCriador); ?>
            <p>
                <a href="<?php echo BASE ?>publico/<?php echo $dados_equipe->idCriador ?>">
                    <img src="<?php echo BASE ?>assets/cards/<?php echo $dadosLider->foto ?>">
                    <span><?php echo $dadosLider->nome ?></span>
                </a>
            </p>
        </div>
        
        <!-- Gold Doados -->
        <div class="indice-gold">
            <h5>Gold Doados</h5>
            <p>
                <img src="<?php echo BASE ?>assets/icones/gold.png">
                <span><?php echo $equipes->getTotalGold($dados_equipe->id) ?></span>
            </p>
        </div>
        
        <!-- LEVEL -->
        <div class="level">
            <h5>LEVEL</h5>
            <p><span><?php echo $dados_equipe->level ?></span></p>
        </div>
        
        <!-- Total Membros -->
        <div class="totalMembros">
            <h5>Membros</h5>
            <p>
                <i class="far fa-user"></i>
                <span><?php echo $equipes->getTotalMembros($dados_equipe->id) ?> / 30</span>
            </p>
        </div>
        
        <!-- PVP -->
        <div class="pvp">
            <h5>PVP</h5>
            <p>
                <i class="fas fa-medal"></i>
                <span><?php echo $equipes->getTotalVitoriasPVP($dados_equipe->id) ?></span>
            </p>
        </div>
        
        <!-- Torneio -->
        <div class="tam">
            <h5>Torneio</h5>
            <p>
                <i class="fas fa-medal"></i>
                <span><?php echo $equipes->getTotalVitoriasTAM($dados_equipe->id) ?></span>
            </p>
        </div>

         <?php if($isMember){ ?>
            <?php
                $idP = $_SESSION['PERSONAGEM']['ID'];
                $dataHora = date('Y-m-d H:i:s');
                
                if(!$core->isExists('equipes_chat', "WHERE idEquipe = ".$idGet)){
                    $campos = array(
                        'idEquipe' => $idGet
                    );
                    $core->insert('equipes_chat', $campos);
                }
            ?>
            
            <?php if($idPersonagem == $dados_equipe->idCriador): ?>
                <a class="sair-equipe" href="<?php echo BASE; ?>equipes/delete/<?php echo $dados_equipe->id; ?>" title="Excluir">
                    <i class="far fa-trash-alt"></i>
                    <span>Excluir Equipe</span>
                </a>
            <?php else: ?>
                <a class="sair-equipe" href="<?php echo BASE; ?>equipes/sair/<?php echo $dados_equipe->id; ?>" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair da Equipe</span>
                </a>
            <?php endif; ?>

        <?php } ?>
    </div>

    
    <?php if(!empty($dados_equipe->descricao)){ ?>
        <div class="mensagem">
            <h4>Mensagem do Lider</h4>
            <p><?php echo $dados_equipe->descricao; ?></p>
        </div>
    <?php } ?>
    
    <?php
    // Calculate team bonuses
    $team_level = isset($dados_equipe->level) ? $dados_equipe->level : 1;
    $bonuses = $equipes->getTeamBonuses($team_level);
    ?>

<ul class="status">
    <h4>Status Adicionais para Cada Membro</h4>
    <?php 
    // Calculate team bonuses
    $teamLevel = isset($dados_equipe->level) ? $dados_equipe->level : 1;
    $bonuses = $equipes->getTeamBonuses($teamLevel);
    
    // For the bars: assume max bonus is around level*5 for percentage calc
    $maxBonus = $teamLevel * 10; // Adjust this based on your formula
    ?>
    
    <li>
        <p>Aumenta o dano nos ataques do seu guerreiro</p>
        <div class="meter animate red">
            <em>FORÇA <?php echo $bonuses['forca'] ?></em>
            <span style="width:<?php echo min(100, ($bonuses['forca']/$maxBonus)*100) ?>%"><span></span></span>
        </div>
    </li>
    
    <li>
        <p>Aumenta taxa de desvio contra o ataque de um inimigo</p>
        <div class="meter animate blue">
            <em>AGILIDADE <?php echo $bonuses['agilidade'] ?></em>
            <span style="width:<?php echo min(100, ($bonuses['agilidade']/$maxBonus)*100) ?>%"><span></span></span>
        </div>
    </li>
    
    <li>
        <p>Aumenta a chance em acerto de ataques críticos</p>
        <div class="meter animate orange">
            <em>HABILIDADE <?php echo $bonuses['habilidade'] ?></em>
            <span style="width:<?php echo min(100, ($bonuses['habilidade']/$maxBonus)*100) ?>%"><span></span></span>
        </div>
    </li>
    
    <li>
        <p>Aumenta sua resistência a ataques</p>
        <div class="meter animate purple">
            <em>RESISTÊNCIA <?php echo $bonuses['resistencia'] ?></em>
            <span style="width:<?php echo min(100, ($bonuses['resistencia']/$maxBonus)*100) ?>%"><span></span></span>
        </div>
    </li>
    
    <li>
        <p>Este atributo te dara sorte extra em cair baús nas Missões</p>
        <div class="meter animate yellow">
            <em>SORTE <?php echo $bonuses['sorte'] ?></em>
            <span style="width:<?php echo min(100, ($bonuses['sorte']/$maxBonus)*100) ?>%"><span></span></span>
        </div>
    </li>
</ul>

<!-- ==================== MEMBER-ONLY SECTIONS ==================== -->
<?php if($isLeader || $isMember): ?>

    <!-- 1. PRÓXIMO LEVEL Section -->
    <div class="barra-level">
        <h4>Próximo Level</h4>
        <?php 
        if($dados_equipe->level >= 150){
            $prox_level = 150;
        } else {
            $prox_level = intval($dados_equipe->level + 1);
        }
        $porcentagem_level = $equipes->getPorcentagemLevel($idPersonagem, $dados_equipe->level, $equipes->getTotalGold($dados_equipe->id));
        ?>
        <div class="content-gold">
            <div class="exp-faltante">
                Falta <strong><?php echo $equipes->getGoldRestante($dados_equipe->level, $equipes->getTotalGold($dados_equipe->id)) ?></strong> golds para avançar de Level
            </div>
            <div class="label-exp">
                Próximo Level
                <span class="txt"><?php echo $prox_level ?></span>
            </div>
            <div class="meter animate roxo">
                <em><?php echo $equipes->getTotalGold($dados_equipe->id) ?> / <?php echo $equipes->getProximoLevel($dados_equipe->level) ?></em>
                <span style="width:<?php echo $porcentagem_level ?>%"><span></span></span>
            </div>
        </div>
    </div>

    <!-- 2. DOAÇÕES DOS MEMBROS Section -->
    <?php
    if(isset($_REQUEST['doar'])){
        if($personagem->getSaldo($_REQUEST['valor'], $idPersonagem)){
            $campos = array(
                'idPersonagem' => $idPersonagem,
                'idEquipe' => $dados_equipe->id,
                'valor' => addslashes($_POST['valor']),
                'data' => date('Y-m-d')
            );
            if($core->insert('equipes_doacoes', $campos)){
                $equipes->verificaLevel();
                $campos = array(
                    'gold' => intval($personagem->gold) - intval(addslashes($_POST['valor']))
                );
                $where = "id = ".$idPersonagem;
                $core->update('usuarios_personagens', $campos, $where);
                $core->msg('sucesso', 'Doação realizada com sucesso.');
                header("Location: ".BASE."equipes/".$dados_equipe->id);
                exit;
            } else {
                $core->msg('error', 'Erro ao efetuar Doação.');
            }
        } else {
            $core->msg('error', 'Gold insuficiente para Doação.');
        }
    }
    ?>
    <div class="doacoes">
        <h4>Doações dos Membros</h4>
        <form id="formDoacoes" class="forms" action="" method="post">
            <input type="text" name="valor" value="" required>
            <input type="submit" id="doar" class="bts-form" name="doar" value="Fazer Doação">
        </form>

        <!-- 3. TOTAL DOADO POR MEMBRO -->
        <h4 style="margin-top: 20px; text-align: center; color: #fff207;">Total Doado por Membro</h4>
        <ul class="total-doacoes">
            <?php $equipes->getIndicadorDoacao($dados_equipe->id); ?>
        </ul>

        <!-- 4. RANKING SEMANAL DE DOAÇÕES -->
        <h4 style="margin-top: 20px;">Ranking Semanal de Doações</h4>
        <table class="tableList">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Data</th>
                    <th>Total Doado</th>
                </tr>
            </thead>
            <tbody>
                <?php $equipes->getDoacoesSemanal($dados_equipe->id); ?>
            </tbody>
        </table>

        <!-- 5. HISTÓRICO DE DOAÇÕES -->
        <h4 style="margin-top: 40px;">Histórico de Doações</h4>
        <table class="tableList">
            <thead>
                <tr>
                    <th>Doador</th>
                    <th>Data</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php $equipes->getDoacoes($dados_equipe->id, $pc, 10); ?>
            </tbody>
        </table>
    </div>

    <!-- 6. ADICIONAR MEMBROS / PENDENTES (Leader Only) -->
    <?php if($isLeader): ?>
        <?php
        if(isset($_POST['adicionarmembro'])){
            if($core->isExists('usuarios_personagens', "WHERE nome = '".addslashes($_POST['nickname'])."'")){
                $dados_membro = $core->getDados('usuarios_personagens', "WHERE nome = '".addslashes($_POST['nickname'])."'");
                if(!$equipes->existsEquipe($dados_membro->id)){
                    $campos = array(
                        'idEquipe' => $dados_equipe->id,
                        'idPersonagem' => $dados_membro->id,
                        'status' => 0
                    );
                    if($core->insert('equipes_membros', $campos)){
                        $core->msg('sucesso', 'Convite Enviado.');
                        header("Location: ".BASE."equipes/".$dados_equipe->id);
                        exit;
                    } else {
                        $core->msg('error', 'Ocorreu um Erro ao enviar o convite.');
                    }
                } else {
                    $core->msg('error', 'Este guerreiro já está em uma equipe.');
                }
            } else {
                $core->msg('error', 'Guerreiro não encontrado com este Nickname.');
            }
        }
        ?>
        <div class="adicionar-membros">
            <h4>Convites Pendentes</h4>
            <?php if($equipes->getTotalMembrosConvidados($dados_equipe->id) < 30): ?>
                <form id="formMembros" class="forms" action="" method="post">
                    <input type="text" name="nickname" value="" placeholder="Insira o nome do Guerreiro" required>
                    <input type="submit" id="adicionar" class="bts-form" name="adicionarmembro" value="Enviar Convite">
                </form>
            <?php endif; ?>

            <table class="tableList">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Guerreiro</th>
                        <th width="250">Graduação</th>
                        <th>Level</th>
                        <th>Gold Faturado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $equipes->getPendentes($dados_equipe->id, $pc, 10, $idPersonagem); ?>
                </tbody>
            </table>

            <h4 style="margin-top: 20px;">Membros Aceitos</h4>
            <table class="tableList">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Guerreiro</th>
                        <th width="250">Graduação</th>
                        <th>Level</th>
                        <th>Gold Faturado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $equipes->getAceitos($dados_equipe->id, $pc, 5, $idPersonagem); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php endif; // END MEMBER-ONLY SECTIONS ?>

<!-- VISITOR MESSAGE -->
<?php if($isVisitor && $idPersonagem): ?>
    <div class="visitor-message" style="padding: 20px; background: rgba(255,107,107,0.1); border-left: 4px solid #ff6b6b; margin: 20px 0;">
        <h4 style="color: #ff6b6b; margin-bottom: 10px;">
            <i class="fas fa-eye"></i> Você está visualizando como visitante
        </h4>
        <p style="color: #fff;">Você não é membro desta equipe. Para ver o chat, doações e recursos de membros, você precisa ser convidado e aceitar o convite.</p>
    </div>
<?php endif; ?>

<!-- 7. RANKING DOS MEMBROS - VISIBLE TO ALL -->
<div class="ranking-equipe">
    <h4>Ranking dos Membros</h4>
    <table class="tableList">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Foto</th>
                <th>Guerreiro</th>
                <th width="300">Graduação</th>
                <th>Level</th>
                <th>Gold Faturado</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $equipes->getRanking($dados_equipe->id); ?>
        </tbody>
    </table>
</div>

</div>

<?php break; ?>

<?php case 'add': ?>

<?php
if(isset($_POST['criar'])){
    $idPersonagemCriador = $_SESSION['PERSONAGEMID'] ?? $_SESSION['PERSONAGEM']['ID'] ?? $_SESSION['PERSONAGEM_ID'];
    
    // Check 1: User already in a team
    if($equipes->existsEquipe($idPersonagemCriador)){
        $core->msg('error', 'Você já está em uma equipe.');
        header('Location: '.BASE.'equipes/add');
        exit;
    }
    
    // Check 2: Sigla already exists
    if($equipes->existsSigla(strtoupper(addslashes($_POST['sigla'])))){
        $core->msg('error', 'Já existe uma equipe com esta Sigla.');
        header('Location: '.BASE.'equipes/add');
        exit;
    }
    
    // Check 3: Offensive words filter
    if(!$core->filtrarPalavrasOfensivas(addslashes($_POST['nome']))){
        $core->msg('error', 'Não é permitido palavras ofensivas ou bloqueadas no nome.');
        header('Location: '.BASE.'equipes/add');
        exit;
    }
    
    // Check 4: Team name already exists
    if($equipes->existsNome(strtoupper(addslashes($_POST['nome'])))){
        $core->msg('error', 'Já existe uma equipe com este Nome.');
        header('Location: '.BASE.'equipes/add');
        exit;
    }
    
    // Check 5: Level requirement
    if($personagem->nivel < 10){
        $core->msg('error', 'Você precisa atingir o level 10 para criar equipes.');
        header('Location: '.BASE.'equipes/add');
        exit;
    }
    
    // Split bandeira value to get ID and image
    $bandeiraData = explode('|', $_POST['idBandeira']);
    $bandeiraId = isset($bandeiraData[0]) ? intval($bandeiraData[0]) : 0;
    $bandeiraImagem = isset($bandeiraData[1]) ? $bandeiraData[1] : 'default.png';
    
    // Prepare team data
    $campos = array(
        'nome' => strtoupper(addslashes($_POST['nome'])),
        'sigla' => strtoupper(addslashes($_POST['sigla'])),
        'foto' => $bandeiraImagem,
        'bandeira_id' => $bandeiraId,
        'idCriador' => $idPersonagemCriador,
        'idLider' => $idPersonagemCriador,
        'level' => 1,
        'pvp' => 0,
        'gold' => 0
    );
    
    // Try to create the team
    if($core->insert('equipes', $campos)){
        $dadosequipe = $core->getDados('equipes', "WHERE idCriador = ".$idPersonagemCriador." ORDER BY id DESC LIMIT 1");
        
        if($dadosequipe){
            // Add creator as team leader
            $campos = array(
                'idEquipe' => $dadosequipe->id,
                'idPersonagem' => $idPersonagemCriador,
                'lider' => 1,
                'status' => 1
            );
            
            if($core->insert('equipes_membros', $campos)){
                $core->msg('sucesso', 'Equipe "'.$_POST['nome'].'" criada com sucesso!');
                header('Location: '.BASE.'equipes/'.$dadosequipe->id);
                exit;
            } else {
                // Rollback: Delete the team if member insert fails
                $core->delete('equipes', 'id = '.$dadosequipe->id);
                $core->msg('error', 'Erro ao adicionar você como membro da equipe.');
                header('Location: '.BASE.'equipes/add');
                exit;
            }
        } else {
            $core->msg('error', 'Erro ao recuperar dados da equipe criada.');
            header('Location: '.BASE.'equipes/add');
            exit;
        }
    } else {
        $core->msg('error', 'Ocorreu um erro ao criar a equipe no banco de dados.');
        header('Location: '.BASE.'equipes/add');
        exit;
    }
}
?>

<div class="criar-equipe-container">
    <div class="criar-equipe-header">
        <h2><i class="fas fa-shield-alt"></i> Criar Equipe</h2>
        <p>Crie sua própria equipe e convide guerreiros para se juntarem a você!</p>
    </div>

    <form id="formEquipes" class="form-criar-equipe" action="" method="post">
        <!-- Informações Básicas -->
        <div class="equipe-section">
            <h3><i class="fas fa-info-circle"></i> Informações Básicas</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">
                        <i class="fas fa-flag"></i> Nome da Equipe
                    </label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        class="input-field" 
                        style="text-transform:uppercase"
                        placeholder="Ex: GUERREIROS DO UNIVERSO" 
                        maxlength="30" 
                        required>
                    <small>Máximo 30 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="sigla">
                        <i class="fas fa-tag"></i> Sigla (3 caracteres)
                    </label>
                    <input 
                        type="text" 
                        id="sigla" 
                        name="sigla" 
                        class="input-field sigla-input" 
                        style="text-transform:uppercase"
                        placeholder="Ex: GDU" 
                        maxlength="3" 
                        required>
                    <small>Use 3 letras únicas</small>
                </div>
            </div>
        </div>

        <!-- Escolha de Bandeira -->
        <div class="equipe-section">
            <h3><i class="fas fa-flag-checkered"></i> Escolha uma Bandeira</h3>
            <div class="bandeiras-grid">
                <?php $equipes->getBandeiras(); ?>
            </div>
        </div>

        <!-- Botão Criar -->
        <div class="form-actions">
            <button type="submit" id="criar" name="criar" class="btn-criar-equipe">
                <i class="fas fa-check-circle"></i> Criar Equipe
            </button>
            <a href="<?php echo BASE; ?>equipes" class="btn-cancelar">
                <i class="fas fa-times-circle"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php break; ?>

<?php case 'convites': ?>

<div class="solicitacoes-equipes">
    <h4>Solicitações para entrar em Equipes</h4>
    <table class="tableList">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nome da Equipe</th>
                <th>Level</th>
                <th>Sigla</th>
                <th>Total de Membros</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $idP = $_SESSION['PERSONAGEM']['ID'] ?? $_SESSION['PERSONAGEM_ID'] ?? $_SESSION['PERSONAGEMID'];
            $equipes->getEquipesPendentes($idP, $pc, 10); 
            ?>
        </tbody>
    </table>
</div>

<?php break; ?>

<?php case 'editar': ?>

<?php
    $idPersonagemLogado = $_SESSION['PERSONAGEM']['ID'] ?? $_SESSION['PERSONAGEM_ID'] ?? $_SESSION['PERSONAGEMID'];
    
    if(!$equipes->isLider($idPersonagemLogado, Url::getURL(2))){
        header('Location: '.BASE.'equipes');
        exit;
    } else {
        $id = Url::getURL(2);
        $idGet = Url::getURL(2);
        $dadosEquipe = $core->getDados('equipes', "WHERE id = ".$id);
        
        if(isset($_POST['salvar'])){
            $campos = array(
                'descricao' => addslashes($_POST['descricao'])
            );
            $where = "id = ".$id;
            
            if($core->update('equipes', $campos, $where)){
                $core->msg('sucesso', 'Mensagem Alterada.');
                header('Location: '.BASE.'equipes/editar/'.$id);
                exit;
            } else {
                $core->msg('error', 'Erro ao alterar Mensagem.');
                header('Location: '.BASE.'equipes/editar/'.$id);
                exit;
            }
        }
        
        if(isset($_POST['transferir'])){
            if($equipes->isMembro(addslashes($_POST['idMembro']), $dadosEquipe->id)){
                $campos = array(
                    'idCriador' => addslashes($_POST['idMembro'])
                );
                $where = "id = ".$dadosEquipe->id;
                
                if($core->update('equipes', $campos, $where)){
                    $core->delete('equipes_membros', "idPersonagem = ".$idPersonagemLogado." AND idEquipe = ".$dadosEquipe->id);
                    
                    $campos_novo_personagem = array(
                        'lider' => 1
                    );
                    $where_novo_personagem = "idPersonagem = ".addslashes($_POST['idMembro'])." AND idEquipe = ".$dadosEquipe->id;
                    $core->update('equipes_membros', $campos_novo_personagem, $where_novo_personagem);
                    
                    $core->msg('sucesso', 'Equipe Transferida.');
                    header('Location: '.BASE.'equipes');
                    exit;
                } else {
                    $core->msg('error', 'Erro ao Transferir Equipe.');
                    header('Location: '.BASE.'equipes/editar/'.$id);
                    exit;
                }
            } else {
                $core->msg('error', 'Você só pode transferir para membros da equipe.');
                header('Location: '.BASE.'equipes/editar/'.$id);
                exit;
            }
        }
        
        if(isset($_POST['alterarfoto'])){
            if($core->validaTamanhoImagem('foto', 400)){
                $foto = $_FILES['foto'];
                $upload = new Upload($foto, 1000, 1000, 'assets/equipes/');
                $newPhoto = $upload->salvar();
                $retorno = $newPhoto;
                
                $campos = array(
                    'foto' => $retorno
                );
                $where = "id=".Url::getURL(2);
                
                if($core->update('equipes', $campos, $where)){
                    $core->msg('sucesso', 'Foto Alterada.');
                    header('Location: '.BASE.'equipes/editar/'.Url::getURL(2));
                    exit;
                } else {
                    $core->msg('error', 'Erro na Alteração.');
                }
            } else {
                $core->msg('error', 'A foto deve ter no mínimo 400px x 400px.');
            }
        }
?>

<?php require_once 'includes/chat-equipe.php'; ?>

<a class="bt-voltar" href="<?php echo BASE; ?>equipes">
    <i class="far fa-eye"></i>
    <span>Visualizar Equipe</span>
</a>

<?php $dadosChat = $core->getDados('equipes_chat', "WHERE idEquipe = ".$dadosEquipe->id); ?>

<?php if($dadosChat->status == 0): ?>
    <a class="bt-voltar bt-ativar-chat" href="<?php echo BASE; ?>equipes/ativarchat/<?php echo $dadosEquipe->id; ?>">
        <i class="fas fa-comment"></i>
        <span>Ativar Chat</span>
    </a>
<?php else: ?>
    <a class="bt-voltar bt-desativar-chat" href="<?php echo BASE; ?>equipes/desativarchat/<?php echo $dadosEquipe->id; ?>">
        <i class="fas fa-comment"></i>
        <span>Desativar Chat</span>
    </a>
<?php endif; ?>

<div class="editar-foto">
    <h4>Alterar Foto</h4>
    <form id="formFoto" action="" method="post" enctype="multipart/form-data">
        <label>Tamanho Mínimo da Imagem: 400px x 400px</label>
        <input type="file" name="foto" value="">
        <input type="submit" id="alterar-foto" class="bts-form" name="alterarfoto" value="Upload">
    </form>
</div>

<div class="editar-mensagem">
    <h4>Editar Mensagem da Equipe</h4>
    <form id="formMensagem" class="forms" action="" method="post">
        <div class="painel-campos">
            <textarea id="descricao" class="text-editor" rows="20" name="descricao"><?php echo $dadosEquipe->descricao; ?></textarea>
        </div>
        <input type="submit" id="doar" class="bts-form" name="salvar" value="Salvar">
    </form>
</div>

<?php if($idPersonagemLogado == $dadosEquipe->idCriador): ?>
    <div class="transferir-equipe">
        <h4>Transferir Equipe</h4>
        <form id="formTransferir" class="forms" action="" method="post">
            <input type="text" name="idMembro" placeholder="ID do Membro" value="">
            <input type="submit" id="transferirEquipe" class="bts-form" name="transferir" value="Transferir">
        </form>
    </div>
<?php endif; ?>

<div class="moderar-membros">
    <h4>Inserir Vice Líderes</h4>
    <table class="tableList">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Guerreiro</th>
                <th>Level</th>
                <th>Vice Líder</th>
                <th>Marcar Vice Líder</th>
            </tr>
        </thead>
        <tbody>
            <?php $equipes->getModeradores($id, $pc, 10); ?>
        </tbody>
    </table>
</div>

<?php } ?>

<?php break; ?>

<?php case 'add_lider': ?>

<?php
    $id = Url::getURL(2);
    $dadosEquipe = $core->getDados('equipes_membros', "WHERE id = ".$id);
    
    $campos = array(
        'vicelider' => 1
    );
    $where = "id = ".$id;
    
    if($core->update('equipes_membros', $campos, $where)){
        $core->msg('sucesso', 'Novo Vice Líder.');
        header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
        exit;
    } else {
        $core->msg('error', 'Erro ao adicionar Vice Líder.');
        header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
        exit;
    }
?>

<?php break; ?>

<?php case 'remove_lider': ?>

<?php
    $id = Url::getURL(2);
    $dadosEquipe = $core->getDados('equipes_membros', "WHERE id = ".$id);
    
    $campos = array(
        'vicelider' => 0
    );
    $where = "id = ".$id;
    
    if($core->update('equipes_membros', $campos, $where)){
        $core->msg('sucesso', 'Alterado Vice Líder.');
        header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
        exit;
    } else {
        $core->msg('error', 'Erro ao alterar Vice Líder.');
        header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
        exit;
    }
?>

<?php break; ?>

<?php case 'remove_convite': ?>

<?php
    $id = Url::getURL(2);
    
    if($core->delete('equipes_membros', "id = ".$id)){
        $core->msg('sucesso', 'Convite removido com Sucesso.');
        header('Location: '.BASE.'equipes');
        exit;
    } else {
        $core->msg('error', 'Erro ao excluir convite.');
        header('Location: '.BASE.'equipes');
        exit;
    }
?>

<?php break; ?>

<?php case 'remove_membro': ?>

<?php
    $id = Url::getURL(2);
    
    if($core->delete('equipes_membros', "id = ".$id)){
        $core->msg('sucesso', 'Membro removido com Sucesso.');
        header('Location: '.BASE.'equipes');
        exit;
    } else {
        $core->msg('error', 'Erro ao remover membro.');
        header('Location: '.BASE.'equipes');
        exit;
    }
?>

<?php break; ?>

<?php case 'aceitar': ?>

<?php
    $id = Url::getURL(2);
    $idPersonagemAceitar = $_SESSION['PERSONAGEM']['ID'] ?? $_SESSION['PERSONAGEM_ID'] ?? $_SESSION['PERSONAGEMID'];
    $idEquipe = $core->getDados('equipes_membros', "WHERE id = ".$id);
    
    if(!$equipes->existsInEquipe($idPersonagemAceitar)){
        $campos = array(
            'status' => 1
        );
        $where = "id = ".$id;
        
        if($core->update('equipes_membros', $campos, $where)){
            $core->msg('sucesso', 'Você entrou para a Equipe.');
            header('Location: '.BASE.'equipes/'.$idEquipe->idEquipe);
            exit;
        } else {
            $core->msg('error', 'Erro ao aceitar Equipe.');
            header('Location: '.BASE.'equipes/convites');
            exit;
        }
    } else {
        $core->msg('error', 'Você já está em uma equipe.');
        header('Location: '.BASE.'equipes/convites');
        exit;
    }
?>

<?php break; ?>

<?php case 'recusar': ?>

<?php
    $id = Url::getURL(2);
    
    if($core->delete('equipes_membros', "id = ".$id)){
        $core->msg('sucesso', 'Convite Recusado.');
        header('Location: '.BASE.'equipes/convites');
        exit;
    } else {
        $core->msg('error', 'Erro ao recusar convite.');
        header('Location: '.BASE.'equipes/convites');
        exit;
    }
?>

<?php break; ?>

<?php case 'delete': ?>

<?php
    $id = Url::getURL(2);
    
    if($core->delete('equipes', "id = ".$id)){
        $core->delete('equipes_membros', "idEquipe = ".$id);
        $core->msg('sucesso', 'Equipe excluida com Sucesso.');
        header('Location: '.BASE.'equipes');
        exit;
    } else {
        $core->msg('error', 'Erro ao excluir Equipe.');
        header('Location: '.BASE.'equipes');
        exit;
    }
?>

<?php break; ?>

<?php case 'sair': ?>

<?php
    $id = Url::getURL(2);
    $idPersonagemSair = $_SESSION['PERSONAGEM']['ID'] ?? $_SESSION['PERSONAGEM_ID'] ?? $_SESSION['PERSONAGEMID'];
    
    if($core->delete('equipes_membros', "idEquipe = ".$id." AND idPersonagem = ".$idPersonagemSair)){
        $core->msg('sucesso', 'Você saiu da Equipe.');
        header('Location: '.BASE.'equipes');
        exit;
    } else {
        $core->msg('error', 'Erro ao sair da Equipe.');
        header('Location: '.BASE.'equipes');
        exit;
    }
?>

<?php break; ?>

<?php case 'ativarchat': ?>

<?php
    $id = Url::getURL(2);
    
    $campos = array(
        'status' => 1
    );
    $where = "idEquipe = ".$id;
    $core->update('equipes_chat', $campos, $where);
    
    header('Location: '.BASE.'equipes/editar/'.$id);
    exit;
?>

<?php break; ?>

<?php case 'desativarchat': ?>

<?php
    $id = Url::getURL(2);
    
    $campos = array(
        'status' => 0
    );
    $where = "idEquipe = ".$id;
    $core->update('equipes_chat', $campos, $where);
    
    header('Location: '.BASE.'equipes/editar/'.$id);
    exit;
?>

<?php break; ?>

<?php case 'ranking': ?>

<div class="ranking-equipes">
    <h4>Ranking de Equipes</h4>
    
    <table class="tableList">
        <thead>
            <tr>
                <th width="90">Rank</th>
                <th width="80">Emblema</th>
                <th>Nome</th>
                <th>Level</th>
                <th>Vitórias PVP</th>
                <th>Gold</th>
            </tr>
        </thead>
        <tbody>
            <?php $equipes->getRankingEquipes($pc, 30); ?>
        </tbody>
    </table>
</div>

<?php break; ?>

<?php } ?>
