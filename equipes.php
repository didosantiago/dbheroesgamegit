<?php switch($acao) {
    default: ?>
        <?php
            if((Url::getURL(1) != null) && (Url::getURL(1) != 'ajax')){
                $idGet = Url::getURL(1);
                if($idGet != 'ajax'){
                    $dados_equipe = $equipes->dadosEquipeAtual($_SESSION['PERSONAGEMID'], $idGet);
                }
            } else {
                $dados_equipe = $equipes->dadosEquipeAtual($_SESSION['PERSONAGEMID'], '');
                
                if($dados_equipe != 0){
                    $idGet = $dados_equipe->id;
                } else {
                    $idGet = '';
                }
            }
        ?>
        
        <?php if($equipes->existsEquipe($_SESSION['PERSONAGEMID'])){ ?>
            <?php require_once 'includes/chat-equipe.php'; ?>

            <div class="perfil-equipe">
                <div class="foto-equipe">
                    <img src="<?php echo BASE.'assets/equipes/'.$dados_equipe->foto; ?>" alt="<?php echo $dados_equipe->nome; ?>" />
                    
                    <?php if($equipes->isLider($_SESSION['PERSONAGEMID'], $idGet)){ ?>
                        <a class="editar-dados" href="<?php echo BASE.'equipes/editar/'.$dados_equipe->id;?>">
                            <i class="fas fa-edit"></i>
                            <span>Editar Dados da Equipe</span>
                        </a>
                    <?php } ?>
                </div>
                
                <div class="info-equipe">
                    <h4><?php echo $dados_equipe->nome; ?></h4>
                    
                    <span class="sigla">
                        <?php echo $dados_equipe->sigla; ?>
                    </span>
                    
                    <div class="lider">
                        <h5>Líder da Equipe</h5>
                        <?php 
                            $dadosLider = $equipes->getDadosCriador($dados_equipe->idCriador);
                        ?>
                        <p>
                            <a href="<?php echo BASE; ?>publico/<?php echo $dados_equipe->idCriador; ?>">
                                <img src="<?php echo BASE; ?>assets/cards/<?php echo $dadosLider->foto; ?>" />
                                <span><?php echo $dadosLider->nome; ?></span>
                            </a>
                        </p>
                    </div>
                    
                    <div class="indice-gold">
                        <h5>Gold Doados</h5>
                        <p>
                            <img src="<?php echo BASE; ?>assets/icones/gold.png" />
                            <span><?php echo $equipes->getTotalGold($dados_equipe->id); ?></span>
                        </p>
                    </div>
                    
                    <div class="level">
                        <h5>LEVEL</h5>
                        <p>
                            <span><?php echo $dados_equipe->level; ?></span>
                        </p>
                    </div>
                    
                    <div class="totalMembros">
                        <h5>Membros</h5>
                        <p>
                            <i class="far fa-user"></i>
                            <span><?php echo $equipes->getTotalMembros($dados_equipe->id); ?> / 30</span>
                        </p>
                    </div>
                    
                    <div class="pvp">
                        <h5>PVP</h5>
                        <p>
                            <i class="fas fa-medal"></i>
                            <span><?php echo $equipes->getTotalVitoriasPVP($dados_equipe->id); ?></span>
                        </p>
                    </div>
                    
                    <div class="tam">
                        <h5>Torneio</h5>
                        <p>
                            <i class="fas fa-medal"></i>
                            <span><?php echo $equipes->getTotalVitoriasTAM($dados_equipe->id); ?></span>
                        </p>
                    </div>
                    
                    <?php if($equipes->isMembro($_SESSION['PERSONAGEMID'], $idGet)){ ?>
                    
                        <?php 
                            $idP = $_SESSION['PERSONAGEMID'];
                            $dataHora = date('Y-m-d H:i:s');
                            
                            if(!$core->isExists('equipes_chat', "WHERE idEquipe =".$idGet)){                                
                                $campos = array(
                                    'idEquipe' => $idGet
                                );

                                $core->insert('equipes_chat', $campos);
                            }
                        ?>
                    
                        <?php if($_SESSION['PERSONAGEMID'] == $dados_equipe->idCriador){ ?>
                            <a class="sair-equipe" href="<?php echo BASE; ?>equipes/delete/<?php echo $dados_equipe->id; ?>" title="Excluir">
                                <i class="far fa-trash-alt"></i>
                                <span>Excluir Equipe</span>
                            </a>
                        <?php } else { ?>
                            <a class="sair-equipe" href="<?php echo BASE; ?>equipes/sair/<?php echo $dados_equipe->id; ?>" title="Sair">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Sair da Equipe</span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
                
                <?php if(!empty($dados_equipe->descricao)){ ?>
                    <div class="mensagem">
                        <h4>Mensagem do Líder</h4>
                        <p>
                            <?php echo $dados_equipe->descricao; ?>
                        </p>
                    </div>
                <?php } ?>
                
                <ul class="status">
                    <h4>Status Adicionais para Cada Membro</h4>
                    <?php 
                        $atributo_valor = $dados_equipe->level * 3; 
                    
                        $porcentagem_forca = $treino->getPorcentagemForca($atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor);
                        $porcentagem_agilidade = $treino->getPorcentagemAgilidade($atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor);
                        $porcentagem_habilidade = $treino->getPorcentagemHabilidade($atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor);
                        $porcentagem_resistencia = $treino->getPorcentagemResistencia($atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor);
                        $porcentagem_sorte = $treino->getPorcentagemSorte($atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor, $atributo_valor);
                    ?>
                    <li>
                        <p>Aumenta o dano nos ataques do seu guerreiro</p>
                        <div class="meter animate <?php $treino->setCorBarra($porcentagem_forca); ?>">
                            <em>Força <?php echo $atributo_valor; ?></em>
                            <span style="width: <?php echo $porcentagem_forca; ?>%"><span></span></span>
                        </div>
                    </li>
                    <li>
                        <p>Aumenta taxa de desvio contra o ataque de um inimigo</p>
                        <div class="meter animate <?php $treino->setCorBarra($porcentagem_agilidade); ?>">
                            <em>Agilidade <?php echo $atributo_valor; ?></em>
                            <span style="width: <?php echo $porcentagem_agilidade; ?>%"><span></span></span>
                        </div>
                    </li>
                    <li>
                        <p>Aumenta a chance em acerto de ataques críticos</p>
                        <div class="meter animate <?php $treino->setCorBarra($porcentagem_habilidade); ?>">
                            <em>Habilidade <?php echo $atributo_valor; ?></em>
                            <span style="width: <?php echo $porcentagem_habilidade; ?>%"><span></span></span>
                        </div>
                    </li>
                    <li>
                        <p>Aumenta sua resistência a ataques</p>
                        <div class="meter animate <?php $treino->setCorBarra($porcentagem_resistencia); ?>">
                            <em>Resistência <?php echo $atributo_valor; ?></em>
                            <span style="width: <?php echo $porcentagem_resistencia; ?>%"><span></span></span>
                        </div>
                    </li>
                    <li>
                        <p>Este atributo te dara sorte extra em cair baús nas Missões</p>
                        <div class="meter animate <?php $treino->setCorBarra($porcentagem_sorte); ?>">
                            <em>Sorte <?php echo $atributo_valor; ?></em>
                            <span style="width: <?php echo $porcentagem_sorte; ?>%"><span></span></span>
                        </div>
                    </li>
                </ul>
                
                <?php if($equipes->isMembro($_SESSION['PERSONAGEMID'], $idGet)){ ?>
                    <div class="barra-level">
                        <h4>Próximo Level</h4>

                        <?php 
                            if($dados_equipe->level == 150){
                                $prox_level = '';
                            } else {
                                $prox_level = intval($dados_equipe->level) + 1;
                            }

                            $porcentagem_level = $equipes->getPorcentagemLevel($_SESSION['PERSONAGEMID'], $dados_equipe->level, $equipes->getTotalGold($dados_equipe->id));
                        ?>

                        <div class="content-gold">
                            <div class="exp-faltante">
                                Falta <strong><?php echo $equipes->getGoldRestante($dados_equipe->level, $equipes->getTotalGold($dados_equipe->id)); ?></strong> golds para avançar de Level
                            </div>

                            <div class="label-exp">
                                Próximo Level 
                                <span class="txt">
                                    <?php echo $prox_level; ?>
                                </span>
                            </div>

                            <div class="meter animate roxo">
                                <em><?php echo $equipes->getTotalGold($dados_equipe->id); ?> / <?php echo $equipes->getProximoLevel($dados_equipe->level); ?></em>
                                <span style="width: <?php echo $porcentagem_level; ?>%"><span></span></span>
                            </div>
                        </div>
                    </div>
                
                    <?php 
                        if(isset($_REQUEST['doar'])){
                            if($personagem->getSaldo($_REQUEST['valor'], $_SESSION['PERSONAGEMID'])){
                                $campos = array(
                                    'idMembro' => $_SESSION['PERSONAGEMID'],
                                    'idEquipe' => $dados_equipe->id,
                                    'valor' => addslashes($_POST['valor']),
                                    'data' => date('Y-m-d')
                                );

                                if($core->insert('equipes_doacoes', $campos)){
                                    $equipes->verificaLevel($dados_equipe->id);

                                    $campos = array(
                                        'gold' => intval($personagem->gold) - intval(addslashes($_POST['valor']))
                                    );

                                    $where = 'id = "'.$_SESSION['PERSONAGEMID'].'"';

                                    $core->update('usuarios_personagens', $campos, $where);

                                    $core->msg('sucesso', 'Doação realizada com sucesso.');
                                    header('Location: '.BASE.'equipes/'.$dados_equipe->id);
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
                            <input type="text" name="valor" value="" required />
                            <input type="submit" id="doar" class="bts-form" name="doar" value="Fazer Doação" />
                        </form>
                        
                        <h4 style="margin-top: 20px; text-align: center; color: #fff207;">Total Doado por Membro</h4>
                        
                        <ul class="total-doacoes">
                            <?php $equipes->getIndicadorDoacao($dados_equipe->id); ?>
                        </ul>
                        
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
                
                    <?php if($equipes->isLider($_SESSION['PERSONAGEMID'], $idGet)){ ?>
                        <?php
                            if(isset($_POST['adicionar_membro'])){
                                if($core->isExists('usuarios_personagens', "WHERE nome = '".addslashes($_POST['nickname'])."' ")){
                                    $dados_membro = $core->getDados('usuarios_personagens', "WHERE nome = '".addslashes($_POST['nickname'])."' ");

                                    if(!$equipes->existsEquipe($dados_membro->id)){
                                        if(!$equipes->existsMembro($dados_membro->id, $idGet)){
                                            $campos = array(
                                                'idEquipe' => $dados_equipe->id,
                                                'idMembro' => $dados_membro->id,
                                                'status' => 0
                                            );

                                            if($core->insert('equipes_membros', $campos)){
                                                $core->msg('sucesso', 'Convite Enviado');
                                                header('Location: '.BASE.'equipes/'.$dados_equipe->id);
                                            } else {
                                                $core->msg('error', 'Ocorreu um Erro ao enviar o convite.');
                                            }
                                        } else {
                                            $core->msg('error', 'Membro já está na Equipe.');
                                        }
                                    } else {
                                        $core->msg('error', 'Este guerreiro já está em uma equipe.');
                                    }
                                } else {
                                    $core->msg('error', 'Guerreiro não encontrado com este Nickname');
                                }
                            }
                        ?>
                        <div class="adicionar-membros">
                            <h4>Convites Pendentes</h4>
                            
                            <?php if($equipes->getTotalMembrosConvidados($dados_equipe->id) < 30){ ?>
                                <form id="formMembros" class="forms" action="" method="post">
                                    <input type="text" name="nickname" value="" placeholder="Insira o nome do Guerreiro" required />
                                    <input type="submit" id="adicionar" class="bts-form" name="adicionar_membro" value="Enviar Convite" />
                                </form>
                            <?php } ?>
                            
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
                                    <?php $equipes->getPendentes($dados_equipe->id, $pc, 10, $_SESSION['PERSONAGEMID']); ?>
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
                                    <?php $equipes->getAceitos($dados_equipe->id, $pc, 5, $_SESSION['PERSONAGEMID']); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                <?php } ?>
                
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
        <?php } else { ?>
            <?php if($personagem->nivel >= 10){ ?>
                <a class="bt-criar-equipe" href="<?php echo BASE; ?>equipes/add" class="add">
                    <i class="fas fa-plus-circle"></i>
                    <span>Criar Equipe</span>
                </a>
            <?php } else { ?>
                <h2 class="title" style="color: #FFF;">É necessário ter level 10 no mínimo para criar equipes.</h2>
            <?php } ?>
        <?php } ?>
    <?php break; ?>

    <?php case 'add': ?>
        <?php 
            if(isset($_POST['criar'])){
                if(!$equipes->existsEquipe(strtoupper($_SESSION['PERSONAGEMID']))){
                    if(!$equipes->existsSigla(strtoupper(addslashes($_POST['sigla'])))){
                        if($core->filtrarPalavrasOfensivas(addslashes($_POST['nome']))){
                            if(!$equipes->existsNome(strtoupper(addslashes($_POST['nome'])))){
                                $campos = array(
                                    'nome' => strtoupper(addslashes($_POST['nome'])),
                                    'sigla' => strtoupper(addslashes($_POST['sigla'])),
                                    'foto' => addslashes($_POST['idBandeira']),
                                    'idCriador' => $_SESSION['PERSONAGEMID']
                                );

                                if($personagem->nivel >= 10){
                                    if($core->insert('equipes', $campos)){
                                        $dados_equipe = $core->getDados('equipes', "WHERE idCriador = ".$_SESSION['PERSONAGEMID']." ORDER BY id DESC LIMIT 1");

                                        $campos = array(
                                            'idEquipe' => $dados_equipe->id,
                                            'idMembro' => $_SESSION['PERSONAGEMID'],
                                            'lider' => 1,
                                            'status' => 1
                                        );
                                        $core->insert('equipes_membros', $campos);

                                        $core->msg('sucesso', 'Equipe Criada.');
                                        header('Location: '.BASE.'equipes/');
                                    } else {
                                        $core->msg('error', 'Ocorreu um Erro ao Criar Equipe.');
                                    }
                                } else {
                                    $core->msg('error', 'Você precisa atingir o level 10 para criar equipes.');
                                }
                            } else {
                                $core->msg('error', 'Já existe uma equipe com este Nome.');
                            }
                        } else {
                            header('Location: '.BASE.'equipes/add');
                            $core->msg('error', 'Não é permitido palavras ofensivas ou bloqueadas.');
                        }
                    } else {
                        $core->msg('error', 'Já existe uma equipe com esta Sigla.');
                        header('Location: '.BASE.'equipes/add');
                    }
                } else {
                    $core->msg('error', 'Você já está em uma equipe.');
                }
            }
        ?>
        
        <div class="add-equipe">
            <h4>Criar Equipe</h4>

            <form id="formEquipes" class="forms" action="" method="post">
                <div class="campos" style="width: 300px;">
                    <label>Nome: </label>
                    <input type="text" style="text-transform:uppercase" name="nome" value="" required />
                </div>
                <div class="campos" style="width: 200px;">
                    <label>Sigla (Apenas 3 Caracteres): </label>
                    <input type="text" name="sigla" style="text-transform:uppercase" value="" maxlength="3" required />
                </div>
                <div class="lista-bandeiras">
                    <h4>Escolha uma Bandeira</h4>
                    <?php $equipes->getBandeiras(); ?>
                </div>
                <input type="submit" id="criar" class="bts-form" name="criar" value="Criar" />
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
                    <?php $equipes->getEquipesPendentes($_SESSION['PERSONAGEMID'], $pc, 10); ?>
                </tbody>
            </table>
        </div>
    <?php break; ?>
        
    <?php case 'editar': ?>
        <?php 
            if(!$equipes->isLider($_SESSION['PERSONAGEMID'], Url::getURL(2))){ 
                header('Location: '.BASE.'equipes');
            } else {
                $id = Url::getURL(2);
                $idGet = Url::getURL(2);
                $dadosEquipe = $core->getDados('equipes', "WHERE id = ".$id);
            }
            
            if(isset($_POST['salvar'])){
                $campos = array(
                    'descricao' => addslashes($_POST['descricao'])
                );

                $where = 'id = "'.$id.'"';

                if($core->update('equipes', $campos, $where)){
                    $core->msg('sucesso', 'Mensagem Alterada.');
                    header('Location: '.BASE.'equipes/editar/'.$id);
                } else {
                    $core->msg('error', 'Erro ao alterar Mensagem.');
                    header('Location: '.BASE.'equipes/editar/'.$id);
                }
            }
            
            if(isset($_POST['transferir'])){
                if($equipes->isMembro(addslashes($_POST['idMembro']), $dadosEquipe->id)){
                    $campos = array(
                        'idCriador' => addslashes($_POST['idMembro'])
                    );

                    $where = 'id = "'.$dadosEquipe->id.'"';

                    if($core->update('equipes', $campos, $where)){
                        
                        $core->delete('equipes_membros', "idMembro = ".$_SESSION['PERSONAGEMID']." AND idEquipe = ".$dadosEquipe->id);
                        
                        $campos_novo_personagem = array(
                            'lider' => 1
                        );

                        $where_novo_personagem = 'idMembro = '.addslashes($_POST['idMembro']).' AND idEquipe = '.$dadosEquipe->id;

                        $core->update('equipes_membros', $campos_novo_personagem, $where_novo_personagem);
                        
                        $core->msg('sucesso', 'Equipe Transferida.');
                        header('Location: '.BASE.'equipes');
                    } else {
                        $core->msg('error', 'Erro ao Transferir Equipe.');
                        header('Location: '.BASE.'equipes/editar/'.$id);
                    }
                } else {
                    $core->msg('error', 'Você só pode transferir para membros da equipe.');
                    header('Location: '.BASE.'equipes/editar/'.$id);
                }
            }
            
            if(isset($_POST['alterar_foto'])){
                if($core->validaTamanhoImagem('foto', 400)){
                    $foto = $_FILES['foto'];

                    $upload = new Upload($foto, 1000, 1000, "assets/equipes/");
                    $newPhoto = $upload->salvar();
                    $retorno = $newPhoto;

                    $campos = array(
                        'foto' => $retorno
                    );

                    $where = 'id="'.Url::getURL(2).'"';

                    if($core->update('equipes', $campos, $where)){
                        $core->msg('sucesso', 'Foto Alterada.');
                        header('Location: '.BASE.'equipes/editar/'.Url::getURL(2));
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
                
        <?php 
            $dadosChat = $core->getDados('equipes_chat', "WHERE idEquipe = ".$dadosEquipe->id);
        ?>
                
        <?php if($dadosChat->status == 0){ ?>
            <a class="bt-voltar bt-ativar-chat" href="<?php echo BASE; ?>equipes/ativar_chat/<?php echo $dadosEquipe->id; ?>">
                <i class="fas fa-comment"></i>
                <span>Ativar Chat</span>
            </a>
        <?php } else { ?>
            <a class="bt-voltar bt-desativar-chat" href="<?php echo BASE; ?>equipes/desativar_chat/<?php echo $dadosEquipe->id; ?>">
                <i class="fas fa-comment"></i>
                <span>Desativar Chat</span>
            </a>
        <?php } ?>
                
        <div class="editar-foto">
            <h4>Alterar Foto</h4>
            <form id="formFoto" action="" method="post" enctype="multipart/form-data">
                <label>Tamanho Mínimo da Imagem (400px x 400px): </label>
                <input type="file" name="foto" value="" />
                <input type="submit" id="alterar-foto" class="bts-form" name="alterar_foto" value="Upload" />
            </form>
        </div>
        
        <div class="editar-mensagem">
            <h4>Editar Mensagem da Equipe</h4>

            <form id="formMensagem" class="forms" action="" method="post">
                <div class="painel-campos">
                    <textarea id="descricao" class="text-editor" rows="20" name="descricao">
                        <?php echo $dadosEquipe->descricao; ?>
                    </textarea>
                </div>
                <input type="submit" id="doar" class="bts-form" name="salvar" value="Salvar" />
            </form>
        </div>
                
        <?php if($_SESSION['PERSONAGEMID'] == $dados_equipe->idCriador){ ?>
            <div class="transferir-equipe">
                <h4>Transferir Equipe</h4>

                <form id="formTransferir" class="forms" action="" method="post">
                    <input type="text" name="idMembro" placeholder="ID do Membro" value="" />
                    <input type="submit" id="transferirEquipe" class="bts-form" name="transferir" value="Transferir" />
                </form>
            </div>
        <?php } ?>
        
        <div class="moderar-membros">
            <h4>Inserir Vice Líderes</h4>

            <table class="tableList">
                <thead>
                    <tr>
                        <tr>
                            <th>Foto</th>
                            <th>Guerreiro</th>
                            <th>Level</th>
                            <th>Vice Líder</th>
                            <th>Marcar Vice Líder</th>
                        </tr>
                    </tr>
                </thead>
                <tbody>
                    <?php $equipes->getModeradores($id, $pc, 10); ?>
                </tbody>
            </table>
        </div>
    <?php break; ?>
        
    <?php case 'add_lider': ?>
        <?php 
            $id = Url::getURL(2);
            $dadosEquipe = $core->getDados('equipes_membros', "WHERE id = ".$id);
            
            $campos = array(
                'vice_lider' => 1
            );

            $where = 'id = "'.$id.'"';

            if($core->update('equipes_membros', $campos, $where)){
                $core->msg('sucesso', 'Novo Vice Líder.');
                header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
            } else {
                $core->msg('error', 'Erro ao adicionar Vice Líder.');
                header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
            }
        ?>
    <?php break; ?>
        
    <?php case 'remove_lider': ?>
        <?php 
            $id = Url::getURL(2);
            $dadosEquipe = $core->getDados('equipes_membros', "WHERE id = ".$id);
            
            $campos = array(
                'vice_lider' => 0
            );

            $where = 'id = "'.$id.'"';

            if($core->update('equipes_membros', $campos, $where)){
                $core->msg('sucesso', 'Alteradp Vice Líder.');
                header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
            } else {
                $core->msg('error', 'Erro ao alterar Vice Líder.');
                header('Location: '.BASE.'equipes/editar/'.$dadosEquipe->idEquipe);
            }
        ?>
    <?php break; ?>
                
    <?php case 'remove_convite': ?>
        <?php 
            $id = Url::getURL(2);
            if($core->delete('equipes_membros', "id = ".$id)){
                $core->msg('sucesso', 'Convite removido com Sucesso.');
                header('Location: '.BASE.'equipes/');
            } else {
                $core->msg('error', 'Erro ao excluir convite.');
                header('Location: '.BASE.'equipes/');
            }
        ?>
    <?php break; ?>
                
    <?php case 'remove_membro': ?>
        <?php 
            $id = Url::getURL(2);
            if($core->delete('equipes_membros', "id = ".$id)){
                $core->msg('sucesso', 'Membro removido com Sucesso.');
                header('Location: '.BASE.'equipes/');
            } else {
                $core->msg('error', 'Erro ao remover membro.');
                header('Location: '.BASE.'equipes/');
            }
        ?>
    <?php break; ?>
        
    <?php case 'aceitar': ?>
        <?php 
            $id = Url::getURL(2);
            $idEquipe = $core->getDados('equipes_membros', "WHERE idMembro = ".$id);
            
            if(!$equipes->existsInEquipe($_SESSION['PERSONAGEMID'])){
                $campos = array(
                    'status' => 1
                );

                $where = 'id = "'.$id.'"';

                if($core->update('equipes_membros', $campos, $where)){
                    $core->msg('sucesso', 'Você entrou para a Equipe.');
                    header('Location: '.BASE.'equipes/'.$idEquipe->idEquipe);
                } else {
                    $core->msg('error', 'Erro ao aceitar Equipe.');
                    header('Location: '.BASE.'equipes/convites');
                }
            }
        ?>
    <?php break; ?>
        
    <?php case 'recusar': ?>
        <?php 
            $id = Url::getURL(2);
            
            if($core->delete('equipes_membros', "id = ".$id)){
                $core->msg('sucesso', 'Convite Recusado.');
                header('Location: '.BASE.'equipes/convites');
            } else {
                $core->msg('error', 'Erro ao recusar convite.');
                header('Location: '.BASE.'equipes/convites');
            }
        ?>
    <?php break; ?>
        
    <?php case 'delete': ?>
        <?php 
            $id = Url::getURL(2);
            if($core->delete('equipes', "id = ".$id)){
                
                $core->delete('equipes_membros', "idEquipe = ".$id);
                
                $core->msg('sucesso', 'Equipe excluida com Sucesso.');
                header('Location: '.BASE.'equipes/');
            } else {
                $core->msg('error', 'Erro ao excluir Equipe.');
                header('Location: '.BASE.'equipes/');
            }
        ?>
    <?php break; ?>
        
    <?php case 'sair': ?>
        <?php 
            $id = Url::getURL(2);
            if($core->delete('equipes_membros', "idEquipe = ".$id." AND idMembro = ".$personagem->id)){
                $core->msg('sucesso', 'Você saiu da Equipe.');
                header('Location: '.BASE.'equipes/');
            } else {
                $core->msg('error', 'Erro ao sair da Equipe.');
                header('Location: '.BASE.'equipes/');
            }
        ?>
    <?php break; ?>
                
    <?php case 'ativar_chat': ?>
        <?php 
            $id = Url::getURL(2);
            
            $campos = array(
                'status' => 1
            );

            $where = 'idEquipe = "'.$id.'"';

            $core->update('equipes_chat', $campos, $where);
            header('Location: '.BASE.'equipes/editar/'.$id);
        ?>
    <?php break; ?>
                
    <?php case 'desativar_chat': ?>
        <?php 
            $id = Url::getURL(2);
            
            $campos = array(
                'status' => 0
            );

            $where = 'idEquipe = "'.$id.'"';

            $core->update('equipes_chat', $campos, $where);
            header('Location: '.BASE.'equipes/editar/'.$id);
        ?>
    <?php break; ?>
                
    <?php case 'ranking': ?>
        <div class="ranking-equipes">
            <h4>Ranking de Equipes</h2>

            <div class="pub-02" style="text-align: center;">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- Ranking -->
                <ins class="adsbygoogle"
                     style="display:inline-block;width:970px;height:90px"
                     data-ad-client="ca-pub-7787997452337920"
                     data-ad-slot="5723949251"></ins>
                <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>

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
                    <?php
                        $equipes->getRankingEquipes($pc,30); 
                    ?>
                </tbody>
            </table>
        </div>
    <?php break; ?>
<?php } ?>

