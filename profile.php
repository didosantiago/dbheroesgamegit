<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }

    $idPersonagem = $_SESSION['PERSONAGEMID'];
    $personagem->getGuerreiro($idPersonagem);
    
    $personagem_dbz = $personagem->getInfoPersonagem($personagem->persona);
    
    //STATUS EXTRA DAS EQUIPES
    if(isset($_SESSION['PERSONAGEMID'])){
        $status_extra = intval($equipes->getStatusExtra($personagem->id));
        $status_extra_graduacao = intval($core->getStatusGraduacao($personagem->graduacao_id));
        $status_equipados = $inventario->getStatusEquipados($personagem->id);
    } else {
        $status_extra = 0;
        $status_extra_graduacao = 0;
        $status_equipados = 0;
    }
    
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
    
    if(isset($_POST['trocar_nome'])){
        $dadosProduto = $core->getDados('usuarios_personagens_modulos', "WHERE idUsuario = ".$user->id." AND modulo = 1");
        
        $nomeGuerreiro = str_replace(" ","",addslashes($_POST['nome']));
            
        if(!$personagem->nomeGuerreiroExists($nomeGuerreiro)){
            $campos = array(
                'nome' => addslashes($_POST['nome'])
            );

            $where = 'id="'.$personagem->id.'"';

            if($core->update('usuarios_personagens', $campos, $where)){
                if($dadosProduto->vitalicio == 0){
                    $core->delete('usuarios_personagens_modulos', "id = ".$dadosProduto->id);
                }
                $core->msg('sucesso', 'Nome Alterado.');
                header('Location: '.BASE.'profile/');
            } else {
                $core->msg('error', 'Erro na Alteração.');
            }
        } else {
            header('Location: '.BASE.'publico');
            $core->msg('error', 'Já existe um Guerreiro com este Nome.');
        }
    }
?>

<div class="inf-right">
    <h2>Informações</h2>
    <ul class="dados">
        <li>
            <span>Nome</span>
            <strong><?php echo $personagem->nome; ?></strong>
        </li>
        <li class="alter">
            <span>No jogo desde</span>
            <strong><?php echo $core->dataBR($personagem->data_cadastro); ?></strong>
        </li>
        <li>
            <span>Personagem</span>
            <strong><?php echo $personagem_dbz->nome; ?></strong>
        </li>
        <li class="alter">
            <span>Nível</span>
            <strong><?php echo $personagem->nivel; ?></strong>
        </li>
        <li>
            <span>Planeta</span>
            <strong><?php echo $personagem->planeta; ?></strong>
        </li>
    </ul>
</div>

<div class="inf-left">
    <p class="informativo">
        Convide seus amigos e ganhe <strong>Coins</strong><br>
        A cada usuário que você convidar através do link abaixo, <br>
        e que realmente esteja jogando o DB Heroes, você receberá <strong>1 Coin</strong><br><br>
        Você pode usar este link em sites, facebook e muito mais, aproveite.
    </p>
    <input type="text" id="userInvite" readonly onclick="$(this).select();" value="<?php echo BASE.'cadastro/invite/'.$user->id; ?>" />
    <a href="<?php echo BASE; ?>invites" class="bts-form bt-indicados">Ver Meus Indicados</a>
</div>

<?php if($core->isExists('usuarios_personagens_modulos', "WHERE idUsuario = ".$user->id)){  ?>
    <div class="modulos-usuario">
        <h2 class="title">Itens Adquiridos na Loja</h2>
        
        <?php if($core->isExists('usuarios_personagens_modulos', "WHERE idUsuario = ".$user->id." AND modulo = 1")){  ?>
            <div class="bloco-item troca-nome">
                <h3>Troca de Nome do Guerreiro Atual</h3>
                <?php
                    $dadosProduto = $core->getDados('usuarios_personagens_modulos', "WHERE idUsuario = ".$user->id." AND modulo = 1");
                    if($dadosProduto->vitalicio == 0){
                ?>
                    <p>Você comprou a utilização deste item na Loja, e ele está disponível para 1 Troca.</p>
                <?php } ?>

                <form id="formTrocarNome" action="" method="post">
                    <input type="text" name="nome" value="<?php echo $personagem->nome; ?>" required />
                    <input type="submit" name="trocar_nome" value="Confirmar" />
                </form>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<a href="<?php echo BASE; ?>treinar" class="bts-form bt-treinar"><i class="fas fa-khanda"></i> Treinar Guerreiro</a>

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

<ul class="estatisticas">
    <li>
        <strong><?php echo $personagem->gold; ?></strong>
        <span>Gold</span>
    </li>
    <li>
        <strong><?php echo $personagem->gold_guardados; ?></strong>
        <span>Gold Guardados</span>
    </li>
    <li>
        <strong><?php echo $personagem->gold_total; ?></strong>
        <span>Gold Total</span>
    </li>
    <li>
        <strong><?php echo $personagem->vitorias_pvp; ?></strong>
        <span>Vitórias PVP</span>
    </li>
    <li>
        <strong><?php echo $personagem->tam; ?></strong>
        <span>Vitórias no TAM</span>
    </li>
    <li>
        <strong><?php echo $personagem->derrotas; ?></strong>
        <span>Derrotas</span>
    </li>
</ul>