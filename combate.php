<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if(isset($_SESSION['pvp_finalizado'])){
        $pvp_finalizado = $_SESSION['pvp_finalizado'];
    } else {
        $pvp_finalizado = 0;
    }
    
    if(isset($_SESSION['pvp_desafiador'])){
        $pvp_desafiador = $_SESSION['pvp_desafiador'];
    } else {
        $pvp_desafiador = 0;
    }
    
    if(isset($_SESSION['pvp_life'])){
        $pvp_life = $_SESSION['pvp_life'];
    } else {
        $pvp_life = 0;
    }
    
    if(isset($_SESSION['pvp_life_oponente'])){
        $pvp_life_oponente = $_SESSION['pvp_life_oponente'];
    } else {
        $pvp_life_oponente = 0;
    }
    
    if(isset($_SESSION['pvp_ki_oponente'])){
        $pvp_ki_oponente = $_SESSION['pvp_ki_oponente'];
    } else {
        $pvp_ki_oponente = 0;
    }
    
    if(!isset($_SESSION['npc'])){
        $habilitado = 1;

        $idPersonagem = $_SESSION['PERSONAGEMID'];

        $parametro_1 = Url::getURL(1);

        if(($parametro_1 != null) && ($parametro_1 != 'ajax')){
            $oponente = $personagem->getOponente($parametro_1);
        }

        $personagem->getGuerreiro($idPersonagem);

        if($batalha->pvpRun($idPersonagem, $parametro_1)){
            $sql = "SELECT * FROM pvp WHERE id = ".$_SESSION['pvp_id'];
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $pvp_info = $stmt->fetch();

            if($pvp_info->pausado == 1){
                if($pvp_info->atacado == 1){
                    $campos = array(
                        'pausado' => 0,
                        'time_inicial' => time(),
                        'time_final' => time() + 30
                    );

                    $where = 'id="'.$_SESSION['pvp_id'].'"';

                    $core->update('pvp', $campos, $where);
                }

                if($pvp_info->atacou == 1){
                    $campos = array(
                        'pausado' => 0
                    );

                    $where = 'id="'.$_SESSION['pvp_id'].'"';

                    $core->update('pvp', $campos, $where);

                    $batalha->atack(4, $parametro_1, $idPersonagem, 0);
                }
            }

            if($oponente->hp == 0){       
                $_SESSION['pvp_derrota'] = true;
            }

            if($personagem->hp == 0){
                $_SESSION['pvp_vitoria'] = true;
            }

            $habilitado = 0;
        } else {
            unset($_SESSION['pvp']);
            unset($_SESSION['pvp_id']);
            unset($_SESSION['pvp_vitoria']);
            unset($_SESSION['pvp_derrota']);
            unset($_SESSION['atacado']);
            unset($_SESSION['pvp_desafiador']);
            unset($_SESSION['pvp_finalizado']);
            unset($_SESSION['pvp_life']);
            unset($_SESSION['pvp_life_oponente']);
            unset($_SESSION['pvp_ki_oponente']);
            unset($_SESSION['pvp_final']);
        }
        
        if(!isset($_SESSION['pvp'])){
            if($personagem->hp <= 0){
                $habilitado = 0;
                $core->msg('error', 'Seu HP é insuficiente para a luta.');
                header('Location: '.BASE.'ranking');
            }
        }

        if($personagem->nivel < 10){
            $habilitado = 0;
            $core->msg('error', 'Você não está habilitado para o PVP, é necessário ter level 10 no mínimo.');
            header('Location: '.BASE.'ranking');
        }
        
        if($oponente->nivel < 10){
            $habilitado = 0;
            $core->msg('error', 'Adversário não habilitado para o PVP, é necessário ter level 10 no mínimo.');
            header('Location: '.BASE.'ranking');
        }
        
        if($core->proccessInNotPVP()){
            $habilitado = 0;
            header('Location: '.BASE.'profile');
        }
        
        if($personagem->gold < 20){
            $habilitado = 0;
            $core->msg('error', 'Gold insuficiente para a Batalha, realize caçadas ou missões para conseguir o gold necessário!');
            header('Location: '.BASE.'ranking');
        }
        
        if($equipes->verificaMembrosEquipe($idPersonagem, $oponente->id)){
            $habilitado = 0;
            $core->msg('error', 'Você não pode atacar membros da sua Equipe.');
            header('Location: '.BASE.'ranking');
        }

        if($oponente->idUsuario == $user->id){
            $habilitado = 0;
            $core->msg('error', 'Você não pode atacar seus Guerreiros.');
            header('Location: '.BASE.'ranking');
        }

        if($oponente->gold < 20){
            $habilitado = 0;
            $core->msg('error', 'Seu adversário não tem Gold suficiente para a Batalha.');
            header('Location: '.BASE.'ranking');
        }

        if($parametro_1 == $idPersonagem){
            $habilitado = 0;
            $core->msg('error', 'Você não pode se atacar.');
            header('Location: '.BASE.'pvp');
        }
        
        if($oponente->hp <= 0){
            $habilitado = 0;
            $core->msg('error', 'O HP de seu adversário é insuficiente para a luta.');
            header('Location: '.BASE.'ranking');
        }

        if($batalha->playerAtacadoDAY($idPersonagem, $parametro_1)){
            $habilitado = 0;
            $core->msg('error', 'Você já atacou este guerreiro hoje, aguarde até amanhã para um novo ataque.');
            header('Location: '.BASE.'pvp');
        } else {
            if($batalha->getAtacouRecente($idPersonagem, $parametro_1)){
                if(!isset($_SESSION['pvp'])){
                    $habilitado = 0;
                    header('Location: '.BASE.'pvp');
                    $core->msg('error', 'Você atacou um adversário recentemente e não poderá atacar durante 10 minutos.');
                }
            } else {
                if(!isset($_SESSION['pvp'])){
                    if($habilitado == 1){
                        $batalha->saveBatalha($idPersonagem, $parametro_1);

                        $comeca = rand(1, 2);

                        if($comeca == 1){
                            $_SESSION['pvp_desafiador'] = 1;

                            $time_atacar = time() + 30; 
                        } else {
                            $_SESSION['pvp_desafiador'] = 0;
                        }

                        if($batalha->getGuerreiroAtacado($_SESSION['pvp_id'])){
                            $_SESSION['pvp_desafiador'] = 1;
                        }

                        if($_SESSION['pvp_desafiador'] == 0){
                            $batalha->atack(4, $parametro_1, $idPersonagem, 0);

                            $_SESSION['pvp_desafiador'] = 1;
                        }
                    }
                }
            }
        }

        if(isset($_POST['concluir'])){
            if(isset($_SESSION['pvp_vitoria'])){
                $vitoria = 1;
            } else {
                $vitoria = 0;
            }
            if($vitoria == 1){
                $gold_recebido = intval((intval($oponente->gold) * 10) / 100);
                
                if($user->vip == 1){
                    $extra_vip_gold = intval($gold_recebido) * (20 / 100);
                } else {
                    $extra_vip_gold = 0;
                }

                $campos_usuario = array(
                    'vitorias_pvp' => intval($personagem->vitorias_pvp) + 1,
                    'gold' => intval($personagem->gold) + $gold_recebido + $extra_vip_gold,
                    'gold_total' => intval($personagem->gold_total) + $gold_recebido + $extra_vip_gold
                );

                $where_usuario = 'id = "'.$idPersonagem.'"';

                $core->update('usuarios_personagens', $campos_usuario, $where_usuario);

                $campos_adversario = array(
                    'derrotas_pvp' => intval($personagem->derrotas_pvp) + 1,
                    'gold' => intval($oponente->gold) - $gold_recebido
                );

                $where_adversario = 'id = "'.$oponente->id.'"';

                $core->update('usuarios_personagens', $campos_adversario, $where_adversario);

                $oponente = $parametro_1;

                $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND idDesafiado = $oponente AND concluido = 0";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $dados_pvp = $stmt->fetch();

                $campos_pvp = array(
                    'vencedor' => 1,
                    'concluido' => 1
                );

                $where_pvp = 'id = "'.$dados_pvp->id.'"';

                $core->update('pvp', $campos_pvp, $where_pvp);
            } else {
                $gold_recebido = intval((intval($personagem->gold) * 10) / 100);

                $campos_adv = array(
                    'vitorias_pvp' => intval($oponente->vitorias_pvp) + 1,
                    'gold' => intval($oponente->gold) + $gold_recebido,
                    'gold_total' => intval($oponente->gold_total) + $gold_recebido
                );

                $where_adv = 'id = "'.$oponente->id.'"';

                $core->update('usuarios_personagens', $campos_adv, $where_adv);

                $campos_usuario = array(
                    'derrotas_pvp' => intval($personagem->derrotas_pvp) + 1,
                    'gold' => intval($personagem->gold) - $gold_recebido
                );

                $where_usuario = 'id = "'.$idPersonagem.'"';

                $core->update('usuarios_personagens', $campos_usuario, $where_usuario);

                $campos_adversario = array(
                    'vitorias_pvp' => intval($oponente->vitorias_pvp) + 1,
                    'gold' => intval($oponente->gold) + $gold_recebido,
                    'gold_total' => intval($oponente->gold_total) + $gold_recebido
                );

                $where_adversario = 'id = "'.$oponente->id.'"';

                $core->update('usuarios_personagens', $campos_adversario, $where_adversario);

                $oponente = $parametro_1;

                $sql = "SELECT * FROM pvp WHERE idPersonagem = $idPersonagem AND idDesafiado = $oponente AND concluido = 0";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $dados_pvp = $stmt->fetch();

                $campos_pvp = array(
                    'vencedor' => 0,
                    'concluido' => 1
                );

                $where_pvp = 'id = "'.$dados_pvp->id.'"';

                $core->update('pvp', $campos_pvp, $where_pvp);
            }

            if($_SESSION['pvp_finalizado'] == 1){
                $campos = array(
                    'hp' => $_SESSION['pvp_life'],
                    'time_hp' => time()
                );

                $where = 'id = "'.$idPersonagem.'"';

                $core->update('usuarios_personagens', $campos, $where);
            }

            unset($_SESSION['atacado']);
            unset($_SESSION['pvp']);
            unset($_SESSION['pvp_id']);
            unset($_SESSION['pvp_vitoria']);
            unset($_SESSION['pvp_derrota']);
            unset($_SESSION['pvp_desafiador']);
            unset($_SESSION['pvp_finalizado']);
            unset($_SESSION['pvp_life']);
            unset($_SESSION['pvp_life_oponente']);
            unset($_SESSION['pvp_final']);

            header('Location: '.BASE.'historico');
        }

        if(isset($_POST['atacar'])){
            if(addslashes($_POST['estado']) == 1){
                if(isset($_SESSION['pvp_vitoria']) || isset($_SESSION['pvp_vitoria'])){
                   $_SESSION['pvp_final'] = 1;
                }

                if(!isset($_SESSION['pvp_vitoria']) || isset($_SESSION['pvp_derrota'])){
                    if(isset($_SESSION['pvp_final'])){
                        $pvp_final = $_SESSION['pvp_final'];
                    } else {
                        $pvp_final = 0;
                    }
                    $batalha->atack(addslashes($_POST['idAtack']), $idPersonagem, $parametro_1, 1, $pvp_final);
                    $_SESSION['pvp_desafiador'] = 0;
                }
            }
        }

        if($batalha->pvpRun($idPersonagem, $parametro_1)){
            $pvp = $core->getDados('pvp', 'WHERE idPersonagem = '.$idPersonagem.' AND idDesafiado = '.$parametro_1.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
            $lifes = $batalha->getLifeRestante($pvp->id);
            $kis = $batalha->getKiRestante($pvp->id);

            $_SESSION['pvp_life'] = $personagem->hp - $lifes->dano_atacante;
            
            if($user->vip == 1){
                $porcentagemVip = (40 / 100) * intval($oponente->hp);
                $_SESSION['pvp_life_oponente'] = $oponente->hp - $porcentagemVip - $lifes->dano_atacado;
            } else {
                $porcentagemFree = (20 / 100) * intval($oponente->hp);
                $_SESSION['pvp_life_oponente'] = $oponente->hp - $porcentagemFree - $lifes->dano_atacado;
            }
            
            $_SESSION['pvp_ki_oponente'] = $kis->ki_pvp;

            if($_SESSION['pvp_life'] > 0 && $_SESSION['pvp_life_oponente'] <= 0 && $_SESSION['pvp_life'] > $_SESSION['pvp_life_oponente']){
                $ganhou = 1;
                $_SESSION['pvp_finalizado'] = 1;
            } else if($_SESSION['pvp_life'] <= 0 && $_SESSION['pvp_life_oponente'] > 0 && $_SESSION['pvp_life_oponente'] > $_SESSION['pvp_life']){
                $ganhou = 0;
                $_SESSION['pvp_finalizado'] = 1;
            } else if($_SESSION['pvp_life'] < 0 && $_SESSION['pvp_life_oponente'] < 0 && $_SESSION['pvp_life_oponente'] > $_SESSION['pvp_life']){
                $ganhou = 0;
                $_SESSION['pvp_finalizado'] = 1;
            } else if($_SESSION['pvp_life'] < 0 && $_SESSION['pvp_life_oponente'] < 0 && $_SESSION['pvp_life_oponente'] < $_SESSION['pvp_life']){
                $ganhou = 1;
                $_SESSION['pvp_finalizado'] = 1;
            } else {
                $ganhou = 0;
                $_SESSION['pvp_finalizado'] = 0;
            }

            if($_SESSION['pvp_life'] < 0){
                $_SESSION['pvp_life'] = 0;
            }

            if($_SESSION['pvp_life_oponente'] < 0){
                $_SESSION['pvp_life_oponente'] = 0;
            }
            
            if($_SESSION['pvp_ki_oponente'] < 0){
                $_SESSION['pvp_ki_oponente'] = 0;
            }

            if($ganhou == 1 && $_SESSION['pvp_finalizado'] == 1){
                $_SESSION['pvp_vitoria'] = true;
                $gold_recebido = intval((intval($oponente->gold) * 10) / 100);
                $vitoria = 1;
            } else if($ganhou == 0 && $_SESSION['pvp_finalizado'] == 1){
                $_SESSION['pvp_derrota'] = true;
                $gold_recebido = intval((intval($personagem->gold) * 10) / 100);
                $vitoria = 0;
            }
        }
    } else {
        $core->msg('error', 'Você está em uma batalha do Torneio de Artes Marciais no momento.');
        header('Location: '.BASE.'portal');
    }
?>

<?php if(isset($_SESSION['pvp_vitoria'])){ ?>
    <script type="text/javascript">
        $('html, body').animate({scrollTop: $('.conteudo').offset().top}, 'slow');
    </script>
    <div class="pvp-vitoria">
        <div class="dados">
            <i class="fas fa-trophy"></i>
            <div class="info-vitoria">
                <p>Você venceu!</p>
                <p><strong><?php echo $oponente->nome; ?></strong> desmaiou após o seu último ataque.</p>
                <p>Golds: Você recebeu <?php echo intval($gold_recebido + $extra_vip_gold); ?> golds de seu rival.</p>
            </div>            
        </div>
        <form id="concluirBatalha" method="post">
            <input type="submit" class="bts-form" name="concluir" value="Concluir" />
        </form>
    </div>
<?php } ?>
    
<?php if(isset($_SESSION['pvp_derrota'])){ ?>
    <script type="text/javascript">
        $('html, body').animate({scrollTop: $('.conteudo').offset().top}, 'slow');
    </script>
    <div class="pvp-derrota">
        <div class="dados">
            <i class="fas fa-thumbs-down"></i>
            <div class="info-derrota">
                <p>Você Perdeu!</p>
                <p><strong><?php echo $personagem->nome; ?></strong> desmaiou após o último ataque de <?php echo $oponente->nome; ?>.</p>
                <p>Golds: Você não recebeu golds, seu rival recebeu <?php echo $gold_recebido; ?> golds de seu guerreiro.</p>
            </div>
        </div>
        <form id="concluirBatalha" method="post">
            <input type="submit" class="bts-form" name="concluir" value="Concluir" />
        </form>
    </div>
<?php } ?>
    
<div class="batalha">
    <input type="hidden" name="finalizado" id="finalizado" value="<?php echo $_SESSION['pvp_finalizado']; ?>" />
    <input type="hidden" name="round" id="round" value="<?php echo $_SESSION['pvp_desafiador']; ?>" />
    <?php
        if(isset($_SESSION['pvp_life'])){
            $pvp_life = $_SESSION['pvp_life'];
        } else {
            $pvp_life = 0;
        }

        if(isset($_SESSION['pvp_life_oponente'])){
            $pvp_life_oponente = $_SESSION['pvp_life_oponente'];
        } else {
            $pvp_life_oponente = 0;
        }
        
        if(isset($_SESSION['pvp_ki_oponente'])){
            $pvp_ki_oponente = $_SESSION['pvp_ki_oponente'];
        } else {
            $pvp_ki_oponente = 0;
        }
        
        $batalha->printConfronto($parametro_1, $idPersonagem, $pvp_life, $pvp_life_oponente, $personagem->mana, $user->vip, $pvp_ki_oponente); 
    ?>
</div>

