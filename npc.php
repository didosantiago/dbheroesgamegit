<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if(isset($_SESSION['npc_finalizado'])){
        $npc_finalizado = $_SESSION['npc_finalizado'];
    } else {
        $npc_finalizado = 0;
    }
    
    if(isset($_SESSION['npc_desafiador'])){
        $npc_desafiador = $_SESSION['npc_desafiador'];
    } else {
        $npc_desafiador = 0;
    }
    
    if(isset($_SESSION['npc_life'])){
        $npc_life = $_SESSION['npc_life'];
    } else {
        $npc_life = 0;
    }
    
    if(isset($_SESSION['npc_life_oponente'])){
        $npc_life_oponente = $_SESSION['npc_life_oponente'];
    } else {
        $npc_life_oponente = 0;
    }
    
    if(isset($_SESSION['npc_ki_oponente'])){
        $npc_ki_oponente = $_SESSION['npc_ki_oponente'];
    } else {
        $npc_ki_oponente = 0;
    }
    
    if(!isset($_SESSION['pvp'])){
        $habilitado = 1;

        $idPersonagem = $_SESSION['PERSONAGEMID'];

        $parametro_1 = Url::getURL(1);

        if($parametro_1 != null){
            $oponente = $npc->getOponenteNPC($parametro_1);
        }

        $personagem->getGuerreiro($idPersonagem);

        if($npc->npcRun($idPersonagem, $parametro_1)){
            $sql = "SELECT * FROM npc WHERE id = ".$_SESSION['npc_id'];
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $npc_info = $stmt->fetch();

            if($npc_info->pausado == 1){
                if($npc_info->atacado == 1){
                    $campos = array(
                        'pausado' => 0,
                        'time_inicial' => time(),
                        'time_final' => time() + 30
                    );

                    $where = 'id="'.$_SESSION['npc_id'].'"';

                    $core->update('npc', $campos, $where);
                }

                if($npc_info->atacou == 1){
                    $campos = array(
                        'pausado' => 0
                    );

                    $where = 'id="'.$_SESSION['npc_id'].'"';

                    $core->update('npc', $campos, $where);

                    $npc->atack(4, $parametro_1, $idPersonagem, 0);
                }
            }

            if($oponente->hp == 0){       
                $_SESSION['npc_derrota'] = true;
            }

            if($personagem->hp == 0){
                $_SESSION['npc_vitoria'] = true;
            }

            $habilitado = 0;
        } else {
            unset($_SESSION['npc']);
            unset($_SESSION['npc_id']);
            unset($_SESSION['npc_vitoria']);
            unset($_SESSION['npc_derrota']);
            unset($_SESSION['npc_atacado']);
            unset($_SESSION['npc_desafiador']);
            unset($_SESSION['npc_finalizado']);
            unset($_SESSION['npc_life']);
            unset($_SESSION['npc_life_oponente']);
            unset($_SESSION['npc_ki_oponente']);
            unset($_SESSION['npc_final']);
        }

        // Validation checks - only run if battle doesn't exist yet
        if(!isset($_SESSION['npc'])){
            if($personagem->hp <= 0){
                $habilitado = 0;
                $core->msg('error', 'Seu HP é insuficiente para a luta.');
                header('Location: '.BASE.'hospital');
                exit();
            }
            
            if(isset($oponente) && $personagem->nivel < $oponente->nivel){
                $habilitado = 0;
                $core->msg('error', 'Você não pode atacar um adversário com level maior');
                header('Location: '.BASE.'torneio');
                exit();
            }
        
            if($personagem->gold < 20){
                $habilitado = 0;
                $core->msg('error', 'Gold insuficiente para a Batalha, realize caçadas ou missões para conseguir o gold necessário!');
                header('Location: '.BASE.'ranking');
                exit();
            }
            
            $energia_restante = intval($personagem->energia) - intval($personagem->energia_usada);
            
            if($energia_restante < 10){
                $habilitado = 0;
                $core->msg('error', 'Energia insuficiente para a Batalha.');
                header('Location: '.BASE.'torneio');
                exit();
            }
        }

        if(!isset($_SESSION['npc'])){
            if($habilitado == 1){
                $npc->saveBatalhaNPC($idPersonagem, $parametro_1);

                $comeca = 1;

                if($comeca == 1){
                    $_SESSION['npc_desafiador'] = 1;

                    $time_atacar = time() + 30; 
                } else {
                    $_SESSION['npc_desafiador'] = 0;
                }

                if($npc->getGuerreiroNPCAtacado($_SESSION['npc_id'])){
                    $_SESSION['npc_desafiador'] = 1;
                }

                if($_SESSION['npc_desafiador'] == 0){
                    $npc->atack(4, $parametro_1, $idPersonagem, 0);

                    $_SESSION['npc_desafiador'] = 1;
                }
            }
        }

        if(isset($_POST['concluir'])){
            if(isset($_SESSION['npc_vitoria'])){
                $vitoria = 1;
            } else {
                $vitoria = 0;
            }
            if($vitoria == 1){
                $exp_recebido = $oponente->exp;
                
                if($user->vip == 1){
                    $exp_extra = intval($exp_recebido) * (20 / 100);
                } else {
                    $exp_extra = 0;
                }
                
                if($core->verifyDoubleEXP()){
                    $double_exp_dados = $core->getDoubleEXP();
                    $double_exp = intval($exp_recebido) * (intval($double_exp_dados->porcentagem) / 100);
                } else {
                    $double_exp = 0;
                }

                $campos_usuario = array(
                    'tam' => intval($personagem->tam) + 1,
                    'exp' => intval($personagem->exp) + intval($oponente->exp) + intval($exp_extra) + intval($double_exp)
                );

                $where_usuario = 'id = "'.$idPersonagem.'"';

                $core->update('usuarios_personagens', $campos_usuario, $where_usuario);

                $oponente = $parametro_1;

                $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND idDesafiado = $oponente AND concluido = 0";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $dados_npc = $stmt->fetch();

                $campos_npc = array(
                    'vencedor' => 1,
                    'concluido' => 1
                );

                $where_npc = 'id = "'.$dados_npc->id.'"';

                $core->update('npc', $campos_npc, $where_npc);
                
                $personagem->getGuerreiro($idPersonagem);
                
                $treino->viewNewLevel($personagem->id, $personagem->nivel, $personagem->exp);
            } else {
                $oponente = $parametro_1;

                $sql = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND idDesafiado = $oponente AND concluido = 0";
                $stmt = DB::prepare($sql);
                $stmt->execute();
                $dados_npc = $stmt->fetch();

                $campos_npc = array(
                    'vencedor' => 0,
                    'concluido' => 1
                );

                $where_npc = 'id = "'.$dados_npc->id.'"';

                $core->update('npc', $campos_npc, $where_npc);
            }

            if($_SESSION['npc_finalizado'] == 1){
                $campos = array(
                    'hp' => $npc_life,
                    'time_hp' => time()
                );

                $where = 'id = "'.$idPersonagem.'"';

                $core->update('usuarios_personagens', $campos, $where);
            }

            unset($_SESSION['npc_atacado']);
            unset($_SESSION['npc']);
            unset($_SESSION['npc_id']);
            unset($_SESSION['npc_vitoria']);
            unset($_SESSION['npc_derrota']);
            unset($_SESSION['npc_desafiador']);
            unset($_SESSION['npc_finalizado']);
            unset($_SESSION['npc_life']);
            unset($_SESSION['npc_life_oponente']);
            unset($_SESSION['npc_final']);

            header('Location: '.BASE.'torneio');
        }

        if(isset($_POST['atacar'])){
            if(addslashes($_POST['estado']) == 1){
                if(isset($_SESSION['npc_vitoria']) || isset($_SESSION['npc_derrota'])){
                   $_SESSION['npc_final'] = 1;
                }

                if(!isset($_SESSION['npc_vitoria']) || isset($_SESSION['npc_derrota'])){
                    if(isset($_SESSION['npc_final'])){
                        $npc_final = $_SESSION['npc_final'];
                    } else {
                        $npc_final = 0;
                    }
                    $npc->atack(addslashes($_POST['idAtack']), $idPersonagem, $parametro_1, 1, $npc_final);
                    $_SESSION['npc_desafiador'] = 0;
                }
            }
        }

        if($npc->npcRun($idPersonagem, $parametro_1)){
            $npc_dados = $core->getDados('npc', 'WHERE idPersonagem = '.$idPersonagem.' AND idDesafiado = '.$parametro_1.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
            $lifes = $npc->getLifeRestante($npc_dados->id);
            $kis = $npc->getKiRestante($npc_dados->id);

            $_SESSION['npc_life'] = $personagem->hp - $lifes->dano_atacante;

            if($user->vip == 1){
                $porcentagemVip = (40 / 100) * intval($oponente->hp);
                $_SESSION['npc_life_oponente'] = $oponente->hp - $porcentagemVip - $lifes->dano_atacado;
            } else {
                $porcentagemFree = (20 / 100) * intval($oponente->hp);
                $_SESSION['npc_life_oponente'] = $oponente->hp - $porcentagemFree - $lifes->dano_atacado;
            }
            
            $_SESSION['npc_ki_oponente'] = $kis->ki_npc;
            
            if($_SESSION['npc_life'] > 0 && $_SESSION['npc_life_oponente'] <= 0 && $_SESSION['npc_life'] > $_SESSION['npc_life_oponente']){
                $ganhou = 1;
                $_SESSION['npc_finalizado'] = 1;
            } else if($_SESSION['npc_life'] <= 0 && $_SESSION['npc_life_oponente'] > 0 && $_SESSION['npc_life_oponente'] > $_SESSION['npc_life']){
                $ganhou = 0;
                $_SESSION['npc_finalizado'] = 1;
            } else if($_SESSION['npc_life'] < 0 && $_SESSION['npc_life_oponente'] < 0 && $_SESSION['npc_life_oponente'] > $_SESSION['npc_life']){
                $ganhou = 0;
                $_SESSION['npc_finalizado'] = 1;
            } else if($_SESSION['npc_life'] < 0 && $_SESSION['npc_life_oponente'] < 0 && $_SESSION['npc_life_oponente'] < $_SESSION['npc_life']){
                $ganhou = 1;
                $_SESSION['npc_finalizado'] = 1;
            } else {
                $ganhou = 0;
                $_SESSION['npc_finalizado'] = 0;
            }

            if($_SESSION['npc_life'] < 0){
                $_SESSION['npc_life'] = 0;
            }

            if($_SESSION['npc_life_oponente'] < 0){
                $_SESSION['npc_life_oponente'] = 0;
            }
            
            if($_SESSION['npc_ki_oponente'] < 0){
                $_SESSION['npc_ki_oponente'] = 0;
            }

            if($ganhou == 1 && $_SESSION['npc_finalizado'] == 1){
                $_SESSION['npc_vitoria'] = true;
                $vitoria = 1;
            } else if($ganhou == 0 && $_SESSION['npc_finalizado'] == 1){
                $_SESSION['npc_derrota'] = true;
                $vitoria = 0;
            }
        }
    } else {
        $core->msg('error', 'Você está em uma batalha PVP no momento.');
        header('Location: '.BASE.'portal');
    }
?>

<?php if(isset($_SESSION['npc_vitoria'])){ ?>
    <script type="text/javascript">
        $('html, body').animate({scrollTop: $('.conteudo').offset().top}, 'slow');
    </script>
    <div class="npc-vitoria">
        <div class="dados">
            <?php
                $exp_recebido = $oponente->exp;
                
                if($user->vip == 1){
                    $exp_extra = intval($exp_recebido) * (20 / 100);
                    $txt_exp_extra = '<p>+ '.intval($exp_extra).' por ser jogador VIP.</p>';
                } else {
                    $exp_extra = 0;
                    $gold_extra = 0;
                    $txt_exp_extra = '';
                }
                
                if($core->verifyDoubleEXP()){
                    $double_exp_dados = $core->getDoubleEXP();
                    $double_exp = intval($exp_recebido) * (intval($double_exp_dados->porcentagem) / 100);
                    $txt_double_exp = '<p>+ <strong>'.intval($double_exp).'</strong> de experiência extra.</p>';
                } else {
                    $double_exp = 0;
                    $txt_double_exp = '';
                }
            ?>
            <i class="fas fa-trophy"></i>
            <div class="info-vitoria">
                <p>Você venceu!</p>
                <p><strong><?php echo $oponente->nome; ?></strong> desmaiou após o seu último ataque.</p>
                <p>Você ganhou <?php echo intval($exp_recebido) ?> de  experiência.</p>
                <?php echo $txt_exp_extra; ?>
                <?php echo $txt_double_exp; ?>
                <p>Você aumentou em <?php echo intval($exp_recebido) + intval($exp_extra) + intval($double_exp); ?> sua experiência.</p>
            </div>
        </div>
        <form id="concluirBatalha" method="post">
            <input type="submit" class="bts-form" name="concluir" value="Concluir" />
        </form>
    </div>
<?php } ?>
    
<?php if(isset($_SESSION['npc_derrota'])){ ?>
    <script type="text/javascript">
        $('html, body').animate({scrollTop: $('.conteudo').offset().top}, 'slow');
    </script>
    <div class="npc-derrota">
        <div class="dados">
            <i class="fas fa-thumbs-down"></i>
            <div class="info-derrota">
                <p>Você Perdeu!</p>
                <p><strong><?php echo $personagem->nome; ?></strong> desmaiou após o último ataque de <?php echo $oponente->nome; ?>.</p>
            </div>
        </div>
        <form id="concluirBatalha" method="post">
            <input type="submit" class="bts-form" name="concluir" value="Concluir" />
        </form>
    </div>
<?php } ?>
    
<div class="batalha">
    <input type="hidden" name="finalizado" id="finalizado" value="<?php echo $npc_finalizado; ?>" />
    <input type="hidden" name="round" id="round" value="<?php echo $npc_desafiador; ?>" />
    <?php
        if(isset($_SESSION['npc_life'])){
            $npc_life = $_SESSION['npc_life'];
        } else {
            $npc_life = 0;
        }

        if(isset($_SESSION['npc_life_oponente'])){
            $npc_life_oponente = $_SESSION['npc_life_oponente'];
        } else {
            $npc_life_oponente = 0;
        }
        
        if(isset($_SESSION['npc_ki_oponente'])){
            $npc_ki_oponente = $_SESSION['npc_ki_oponente'];
        } else {
            $npc_ki_oponente = 0;
        }
        
        $npc->printConfronto($parametro_1, $idPersonagem, $npc_life, $npc_life_oponente, $personagem->mana, $user->vip, $npc_ki_oponente); 
    ?>
</div>