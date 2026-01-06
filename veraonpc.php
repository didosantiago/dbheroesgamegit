<?php 
require_once 'init.php';
require_once 'core/CharacterRequired.php';

// ✅ Initialize VeraoNpc object
$veraonpc = new VeraoNpc();

// CONCEDER (Give Up) Handler
if (isset($_POST['conceder'])) {
    if (isset($_SESSION['verao_npc_id'])) {
        $idPersonagem = $_SESSION['PERSONAGEMID'];
        
        // Calculate remaining HP from battle
        $personagem = $core->getDados('usuarios_personagens', 'WHERE id = "'.$idPersonagem.'"');
        $lifes = $veraonpc->getLifeRestante($_SESSION['verao_npc_id']);
        
        // Calculate HP after damage taken in battle
        $hp_restante = max(1, intval($personagem->hp) - intval($lifes->dano_atacante));
        
        // Update player HP with battle damage
        $campos_hp = array(
            'hp' => $hp_restante,
            'time_hp' => time()
        );
        $core->update('usuarios_personagens', $campos_hp, 'id = "'.$idPersonagem.'"');
        
        // Mark battle as concluded and player as loser
        $campos_fim = array(
            'concluido' => 1,
            'vencedor' => 0, // 0 means NPC won / Player lost
            'recompensa_recebida' => 1
        );
        $core->update('verao_batalhas', $campos_fim, 'id = '.$_SESSION['verao_npc_id']);
        
        // Clear all battle sessions
        unset($_SESSION['verao_npc']);
        unset($_SESSION['verao_npc_id']);
        unset($_SESSION['verao_npc_atacado']);
        unset($_SESSION['verao_npc_vitoria']);
        unset($_SESSION['verao_npc_derrota']);
        unset($_SESSION['verao_npc_desafiador']);
        unset($_SESSION['verao_npc_finalizado']);
        unset($_SESSION['verao_npc_life']);
        unset($_SESSION['verao_npc_life_oponente']);
        unset($_SESSION['verao_npc_ki_oponente']);
        unset($_SESSION['verao_npc_final']);
        
        // Force session write
        session_write_close();
        
        // Redirect to Veraoexplorando
        header('Location: ' . BASE . 'veraoexplorando');
        exit;
    }
}

if(!isset($_SESSION['PERSONAGEMID'])){
    header('Location: '.BASE.'portal');
    exit;
}

// Initialize session variables
if(isset($_SESSION['verao_npc_finalizado'])){
    $npc_finalizado = $_SESSION['verao_npc_finalizado'];
} else {
    $npc_finalizado = 0;
}

if(isset($_SESSION['verao_npc_desafiador'])){
    $npc_desafiador = $_SESSION['verao_npc_desafiador'];
} else {
    $npc_desafiador = 0;
}

if(isset($_SESSION['verao_npc_life'])){
    $npc_life = $_SESSION['verao_npc_life'];
} else {
    $npc_life = 0;
}

if(isset($_SESSION['verao_npc_life_oponente'])){
    $npc_life_oponente = $_SESSION['verao_npc_life_oponente'];
} else {
    $npc_life_oponente = 0;
}

if(isset($_SESSION['verao_npc_ki_oponente'])){
    $npc_ki_oponente = $_SESSION['verao_npc_ki_oponente'];
} else {
    $npc_ki_oponente = 0;
}

if(!isset($_SESSION['pvp'])){
    $habilitado = 1;
    $idPersonagem = $_SESSION['PERSONAGEMID'];
    $parametro_1 = Url::getURL(1);

    // ✅ CRITICAL: Check database FIRST on EVERY page load
    if($parametro_1 != null){
        $oponente = $veraonpc->getOponenteNPC($parametro_1);
        
        // Check if battle is finished in database
        $sql_check_db = "SELECT * FROM verao_batalhas WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 1 AND recompensa_recebida = 0 ORDER BY id DESC LIMIT 1";
        $stmt_check_db = DB::prepare($sql_check_db);
        $stmt_check_db->execute();

        if($stmt_check_db->rowCount() > 0){
            $battle_status = $stmt_check_db->fetch();
            
            // Set victory/defeat popup
            if($battle_status->vencedor == 1){
                $_SESSION['verao_npc_vitoria'] = true;
            } else {
                $_SESSION['verao_npc_derrota'] = true;
            }
            $_SESSION['verao_npc_finalizado'] = 1;
            $_SESSION['verao_npc_id'] = $battle_status->id;
            $_SESSION['verao_npc'] = true;
        }
        // ✅ Only load existing ACTIVE battles if no victory/defeat session exists
        elseif(!isset($_SESSION['verao_npc_vitoria']) && !isset($_SESSION['verao_npc_derrota'])){
            $sql_check_existing = "SELECT * FROM verao_batalhas WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 0 ORDER BY id DESC LIMIT 1";
            $stmt_check = DB::prepare($sql_check_existing);
            $stmt_check->execute();

            if($stmt_check->rowCount() > 0){
                $existing_battle = $stmt_check->fetch();
                $_SESSION['verao_npc'] = true;
                $_SESSION['verao_npc_id'] = $existing_battle->id;
                $_SESSION['verao_npc_atacado'] = $existing_battle->atacado;
                $_SESSION['verao_npc_desafiador'] = ($existing_battle->atacado == 1) ? 1 : 0;
            }
        }
    } elseif(isset($_SESSION['verao_npc_id'])){
        // No URL parameter but session exists, redirect to the battle
        header('Location: '.BASE.'veraonpc/'.$_SESSION['verao_npc_id']);
        exit;
    }

    // CONCLUIR (Finish Battle) Handler
    if (isset($_POST['concluir'])) {
        $opponent_id_from_url = Url::getURL(1);
        
        if (empty($opponent_id_from_url)) {
            unset($_SESSION['verao_npc'], $_SESSION['verao_npc_id'], $_SESSION['verao_npc_vitoria'], $_SESSION['verao_npc_derrota'], $_SESSION['verao_npc_finalizado']);
            header('Location: ' . BASE . 'veraoexplorando');
            exit;
        }
        
        // Get battle data from database
        $sql_get_battle = "SELECT * FROM verao_batalhas WHERE idPersonagem = $idPersonagem AND idDesafiado = $opponent_id_from_url ORDER BY id DESC LIMIT 1";
        $stmt_get_battle = DB::prepare($sql_get_battle);
        $stmt_get_battle->execute();
        
        if($stmt_get_battle->rowCount() > 0){
            $battle_data = $stmt_get_battle->fetch();
            $battle_id = $battle_data->id;
            $battle_won = ($battle_data->vencedor == 1);
        } else {
            unset($_SESSION['verao_npc'], $_SESSION['verao_npc_id'], $_SESSION['verao_npc_vitoria'], $_SESSION['verao_npc_derrota']);
            header('Location: ' . BASE . 'veraoexplorando');
            exit;
        }
        
        $oponente_obj = $veraonpc->getOponenteNPC($opponent_id_from_url);
        $personagem_atual = $core->getDados('usuarios_personagens', 'WHERE id = "'.$idPersonagem.'"');
        
        // ✅ Award EXP if player won
        if ($battle_won && $oponente_obj) {
            $exp_recebido = $oponente_obj->exp;
            
            // VIP bonus
            if($user->vip == 1){
                $exp_extra = intval($exp_recebido) * (20 / 100);
            } else {
                $exp_extra = 0;
            }
            
            $exp_total = intval($exp_recebido) + intval($exp_extra);
            
            // Award EXP
            $campos_usuario = array(
                'exp' => intval($personagem_atual->exp) + $exp_total
            );
            $core->update('usuarios_personagens', $campos_usuario, 'id = "' . $idPersonagem . '"');
            $personagem->checkLevelUp($idPersonagem);
            
            // ✅ Update verao_progresso - unlock next enemy
            $ordem = $oponente_obj->ordem;
            $next_ordem = $ordem + 1;
            
            $sql_update_progress = "UPDATE verao_progresso SET ultimo_desbloqueado = :next WHERE id_personagem = :id AND ultimo_desbloqueado < :next";
            $stmt_update_progress = DB::prepare($sql_update_progress);
            $stmt_update_progress->execute(['next' => $next_ordem, 'id' => $idPersonagem]);
            
            $personagem_atual = $core->getDados('usuarios_personagens', 'WHERE id = "'.$idPersonagem.'"');
        }
        
        // ✅ Save remaining HP
        if($battle_won){
            $lifes_final = $veraonpc->getLifeRestante($battle_id);
            $hp_restante = max(1, intval($personagem_atual->hp) - intval($lifes_final->dano_atacante));
            
            $campos_hp = array(
                'hp' => $hp_restante,
                'time_hp' => time()
            );
            $core->update('usuarios_personagens', $campos_hp, 'id = "'.$idPersonagem.'"');
        } else {
            $campos_hp = array(
                'hp' => 0,
                'time_hp' => time()
            );
            $core->update('usuarios_personagens', $campos_hp, 'id = "'.$idPersonagem.'"');
        }
        
        // ✅ Mark reward as claimed
        $campos_recompensa = array('recompensa_recebida' => 1);
        $core->update('verao_batalhas', $campos_recompensa, 'id = '.$battle_id);
        
        // ✅ Clear ALL battle sessions
        unset(
            $_SESSION['verao_npc'],
            $_SESSION['verao_npc_id'],
            $_SESSION['verao_npc_atacado'],
            $_SESSION['verao_npc_vitoria'],
            $_SESSION['verao_npc_derrota'],
            $_SESSION['verao_npc_desafiador'],
            $_SESSION['verao_npc_finalizado'],
            $_SESSION['verao_npc_life'],
            $_SESSION['verao_npc_life_oponente'],
            $_SESSION['verao_npc_ki_oponente'],
            $_SESSION['verao_npc_final']
        );
        
        session_write_close();
        
        header('Location: ' . BASE . 'veraoexplorando');
        exit;
    }

    // Load character data
    $personagem->getGuerreiro($idPersonagem);

    // Check if battle just ended
    if(isset($_SESSION['verao_npc_id'])){
        $check_battle_end = $core->getDados('verao_batalhas', 'WHERE id = '.$_SESSION['verao_npc_id']);
        if($check_battle_end && $check_battle_end->concluido == 1){
            if($check_battle_end->vencedor == 1){
                $_SESSION['verao_npc_vitoria'] = true;
            } else {
                $_SESSION['verao_npc_derrota'] = true;
            }
            $_SESSION['verao_npc_finalizado'] = 1;
        }
    }

    // Active battle processing
    if($veraonpc->npcRun($idPersonagem, $parametro_1)){
        $sql = "SELECT * FROM verao_batalhas WHERE id = ".$_SESSION['verao_npc_id']." AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $dados_npc = $stmt->fetch();

            // Timer check
            if($dados_npc->time_final < time() && $dados_npc->pausado == 0){
                if($dados_npc->atacado == 1){
                    $lifes = $veraonpc->getLifeRestante($dados_npc->id);

                    if($user->vip == 1){
                        $porcentagemVip = round((40/100) * intval($oponente->hp));
                        $hp_npc_max = round($oponente->hp - $porcentagemVip);
                    } else {
                        $porcentagemFree = round((20/100) * intval($oponente->hp));
                        $hp_npc_max = round($oponente->hp - $porcentagemFree);
                    }
                    $npc_hp = $hp_npc_max - $lifes->dano_atacado;

                    if($npc_hp > 0 && $dados_npc->atacou == 0){
                        $veraonpc->atack(4, $parametro_1, $idPersonagem, 0, 0);

                        $lifes_after = $veraonpc->getLifeRestante($dados_npc->id);
                        $player_hp_after = $personagem->hp - $lifes_after->dano_atacante;

                        if($player_hp_after <= 0){
                            $campos_fim = array(
                                'concluido' => 1,
                                'vencedor' => 0,
                                'pausado' => 0,
                                'atacado' => 0,
                                'atacou' => 1
                            );
                            $core->update('verao_batalhas', $campos_fim, 'id = "'.$dados_npc->id.'"');

                            $campos_hp = array(
                                'hp' => 0,
                                'time_hp' => time()
                            );
                            $core->update('usuarios_personagens', $campos_hp, 'id = "'.$idPersonagem.'"');

                            $_SESSION['verao_npc_derrota'] = true;
                            $_SESSION['verao_npc_finalizado'] = 1;

                            header('Location: '.BASE.'veraonpc/'.$parametro_1);
                            exit();
                        } else {
                            $campos_timer = array(
                                'time_final' => time() + 30,
                                'atacado' => 1,
                                'atacou' => 0
                            );
                            $where_timer = 'id = "'.$dados_npc->id.'"';
                            $core->update('verao_batalhas', $campos_timer, $where_timer);

                            header('Location: '.BASE.'veraonpc/'.$parametro_1);
                            exit();
                        }
                    }
                }
            }
            
            // NPC counter-attack
            if($dados_npc->atacou == 1 && $dados_npc->atacado == 0 && $dados_npc->pausado == 0){
                $lifes = $veraonpc->getLifeRestante($dados_npc->id);
                
                if($user->vip == 1){
                    $porcentagemVip = round((40/100) * intval($oponente->hp));
                    $hp_npc_max = round($oponente->hp - $porcentagemVip);
                } else {
                    $porcentagemFree = round((20/100) * intval($oponente->hp));
                    $hp_npc_max = round($oponente->hp - $porcentagemFree);
                }
                $npc_hp = $hp_npc_max - $lifes->dano_atacado;
                
                if($npc_hp > 0 && $dados_npc->time_final > time()){
                    $veraonpc->atack(4, $parametro_1, $idPersonagem, 0, 0);
                }
            }
        }
    }

    // Pause/Resume handling
    if($veraonpc->npcRun($idPersonagem, $parametro_1)){
        $sql = "SELECT * FROM verao_batalhas WHERE id = ".$_SESSION['verao_npc_id'];
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $npc_info = $stmt->fetch();

        if($npc_info && $npc_info->pausado == 1){
            if(isset($npc_info->time_pausado) && $npc_info->time_pausado > 0){
                $pause_duration = time() - $npc_info->time_pausado;
                
                if($pause_duration > 300){
                    $campos_forfeit = array(
                        'pausado' => 0,
                        'concluido' => 1,
                        'vencedor' => 0
                    );
                    $where_forfeit = 'id = "'.$npc_info->id.'"';
                    $core->update('verao_batalhas', $campos_forfeit, $where_forfeit);
                    
                    $_SESSION['verao_npc_derrota'] = true;
                    $_SESSION['verao_npc_finalizado'] = 1;
                    
                    unset($_SESSION['verao_npc'], $_SESSION['verao_npc_id'], $_SESSION['verao_npc_atacado']);
                    
                    $core->msg('error', 'Batalha cancelada por inatividade prolongada (5 minutos).');
                    header('Location: '.BASE.'veraoexplorando');
                    exit();
                }
            }
            
            if($npc_info->atacado == 1){
                $campos = array(
                    'pausado' => 0,
                    'time_inicial' => time(),
                    'time_final' => time() + 30
                );
                $where = 'id = "'.$_SESSION['verao_npc_id'].'"';
                $core->update('verao_batalhas', $campos, $where);
            }

            if($npc_info->atacou == 1){
                $campos = array('pausado' => 0);
                $where = 'id = "'.$_SESSION['verao_npc_id'].'"';
                $core->update('verao_batalhas', $campos, $where);

                $veraonpc->atack(4, $parametro_1, $idPersonagem, 0);
            }
        }

        $habilitado = 0;
    } else {
        if(!isset($_SESSION['verao_npc_vitoria']) && !isset($_SESSION['verao_npc_derrota'])){
            unset($_SESSION['verao_npc'], $_SESSION['verao_npc_id'], $_SESSION['verao_npc_atacado']);
        }
    }

    // New battle creation
    if(!isset($_SESSION['verao_npc']) && !isset($_SESSION['verao_npc_vitoria']) && !isset($_SESSION['verao_npc_derrota'])){
        if($habilitado == 1){
            $sql_check = "SELECT * FROM verao_batalhas WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 0";
            $stmt_check = DB::prepare($sql_check);
            $stmt_check->execute();
            
            if($stmt_check->rowCount() == 0){
                $sql_safety = "SELECT * FROM verao_batalhas WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 1 AND recompensa_recebida = 0 ORDER BY id DESC LIMIT 1";
                $stmt_safety = DB::prepare($sql_safety);
                $stmt_safety->execute();

                $can_create_battle = true;

                if($stmt_safety->rowCount() > 0){
                    $last_battle = $stmt_safety->fetch();
                    $time_since_battle = time() - $last_battle->time_inicial;
                    
                    if($time_since_battle < 30){
                        if($last_battle->vencedor == 1){
                            $_SESSION['verao_npc_vitoria'] = true;
                        } else {
                            $_SESSION['verao_npc_derrota'] = true;
                        }

                        $_SESSION['verao_npc_id'] = $last_battle->id;
                        $_SESSION['verao_npc'] = true;
                        $_SESSION['verao_npc_finalizado'] = 1;
                        
                        $habilitado = 0;
                        $can_create_battle = false;
                    }
                }
                
                if($can_create_battle){
                    $veraonpc->saveBatalhaNPC($idPersonagem, $parametro_1);
                    
                    $comeca = 1;
                    if($comeca == 1){
                        $_SESSION['verao_npc_desafiador'] = 1;
                    } else {
                        $_SESSION['verao_npc_desafiador'] = 0;
                    }
                    
                    if($veraonpc->getGuerreiroNPCAtacado($_SESSION['verao_npc_id'])){
                        $_SESSION['verao_npc_desafiador'] = 1;
                    }
                }
            } else {
                $existing = $stmt_check->fetch();
                $_SESSION['verao_npc'] = true;
                $_SESSION['verao_npc_id'] = $existing->id;
                $_SESSION['verao_npc_desafiador'] = ($existing->atacado == 1) ? 1 : 0;
            }
        }
    }

    // Attack handler
    if(isset($_POST['atacar'])){
        if(addslashes($_POST['estado']) == 1){
            if(!isset($_SESSION['verao_npc_vitoria']) && !isset($_SESSION['verao_npc_derrota'])){
                if(isset($_SESSION['verao_npc_final'])){
                    $npc_final = $_SESSION['verao_npc_final'];
                } else {
                    $npc_final = 0;
                }
                
                $veraonpc->atack(addslashes($_POST['idAtack']), $idPersonagem, $parametro_1, 1, $npc_final);
                $_SESSION['verao_npc_desafiador'] = 0;
                
                $sql_check_ended = "SELECT * FROM verao_batalhas WHERE id = ".intval($_SESSION['verao_npc_id']);
                $stmt_check_ended = DB::prepare($sql_check_ended);
                $stmt_check_ended->execute();
                $battle_after_attack = $stmt_check_ended->fetch();
                
                if($battle_after_attack && $battle_after_attack->concluido == 1){
                    if($battle_after_attack->vencedor == 1){
                        $_SESSION['verao_npc_vitoria'] = true;
                    } else {
                        $_SESSION['verao_npc_derrota'] = true;
                    }
                    $_SESSION['verao_npc_finalizado'] = 1;
                    
                    echo "<script>setTimeout(function(){ window.location.href = '".BASE."veraonpc/".$parametro_1."'; }, 100);</script>";
                    exit;
                } else {
                    header('Location: '.BASE.'veraonpc/'.$parametro_1);
                    exit;
                }
            }
        }
    }

    // Calculate HP for display
    if($veraonpc->npcRun($idPersonagem, $parametro_1) || isset($_SESSION['verao_npc_vitoria']) || isset($_SESSION['verao_npc_derrota'])){
        if(isset($_SESSION['verao_npc_vitoria']) || isset($_SESSION['verao_npc_derrota'])){
            $npc_dados = $core->getDados('verao_batalhas', 'WHERE id = '.$_SESSION['verao_npc_id'].' ORDER BY id DESC LIMIT 1');
        } else {
            $npc_dados = $core->getDados('verao_batalhas', 'WHERE idPersonagem = '.$idPersonagem.' AND idDesafiado = '.$parametro_1.' AND concluido = 0 ORDER BY id DESC LIMIT 1');
        }
        
        if($npc_dados){
            $lifes = $veraonpc->getLifeRestante($npc_dados->id);
            $kis = $veraonpc->getKiRestante($npc_dados->id);

            $_SESSION['verao_npc_life'] = $personagem->hp - $lifes->dano_atacante;

            if($user->vip == 1){
                $porcentagemVip = (40 / 100) * intval($oponente->hp);
                $_SESSION['verao_npc_life_oponente'] = $oponente->hp - $porcentagemVip - $lifes->dano_atacado;
            } else {
                $porcentagemFree = (20 / 100) * intval($oponente->hp);
                $_SESSION['verao_npc_life_oponente'] = $oponente->hp - $porcentagemFree - $lifes->dano_atacado;
            }
            
            $_SESSION['verao_npc_ki_oponente'] = $kis->ki_npc;
            
            if($_SESSION['verao_npc_life'] < 0) $_SESSION['verao_npc_life'] = 0;
            if($_SESSION['verao_npc_life_oponente'] < 0) $_SESSION['verao_npc_life_oponente'] = 0;
            if($_SESSION['verao_npc_ki_oponente'] < 0) $_SESSION['verao_npc_ki_oponente'] = 0;
            
            if(!isset($_SESSION['verao_npc_vitoria']) && !isset($_SESSION['verao_npc_derrota'])){
                if($_SESSION['verao_npc_life'] > 0 && $_SESSION['verao_npc_life_oponente'] <= 0){
                    $ganhou = 1;
                    $_SESSION['verao_npc_finalizado'] = 1;
                } else if($_SESSION['verao_npc_life'] <= 0 && $_SESSION['verao_npc_life_oponente'] > 0){
                    $ganhou = 0;
                    $_SESSION['verao_npc_finalizado'] = 1;
                } else if($_SESSION['verao_npc_life'] <= 0 && $_SESSION['verao_npc_life_oponente'] <= 0){
                    if($_SESSION['verao_npc_life'] > $_SESSION['verao_npc_life_oponente']){
                        $ganhou = 1;
                    } else {
                        $ganhou = 0;
                    }
                    $_SESSION['verao_npc_finalizado'] = 1;
                } else {
                    $ganhou = 0;
                    $_SESSION['verao_npc_finalizado'] = 0;
                }

                if($ganhou == 1 && $_SESSION['verao_npc_finalizado'] == 1){
                    $_SESSION['verao_npc_vitoria'] = true;
                    $vitoria = 1;
                } else if($ganhou == 0 && $_SESSION['verao_npc_finalizado'] == 1){
                    $_SESSION['verao_npc_derrota'] = true;
                    $vitoria = 0;
                }
            }
        }
    }
} else {
    $core->msg('error', 'Você está em uma batalha PVP no momento.');
    header('Location: '.BASE.'portal');
}
?>

<!-- Victory Popup -->
<?php if(isset($_SESSION['verao_npc_vitoria'])){ ?>
    <script type="text/javascript">
        $('html, body').animate({scrollTop: $('.conteudo').offset().top}, 'slow');
    </script>
    
    <?php 
    $exp_recebido = $oponente->exp;

    if($user->vip == 1){
        $exp_extra = intval($exp_recebido) * (20 / 100);
        $txt_exp_extra = '<p>+ <strong>'.intval($exp_extra).'</strong> por ser jogador VIP.</p>';
    } else {
        $exp_extra = 0;
        $txt_exp_extra = '';
    }
    ?>
    
    <div class="npc-vitoria">
        <div class="dados">
            <i class="fas fa-trophy"></i>
            <div class="info-vitoria">
                <p>Você venceu!</p>
                <p><strong><?php echo $oponente->nome; ?></strong> foi derrotado!</p>
                <p>Você ganhou <strong><?php echo intval($exp_recebido); ?></strong> de experiência.</p>
                <?php echo $txt_exp_extra; ?>
                <p>Total: <strong><?php echo intval($exp_recebido) + intval($exp_extra); ?></strong> EXP!</p>
            </div>
        </div>
        <form id="concluirBatalha" method="post">
            <input type="submit" class="bts-form" name="concluir" value="Concluir">
        </form>
    </div>
<?php } ?>

<!-- Defeat Popup -->
<?php if(isset($_SESSION['verao_npc_derrota'])){ ?>
    <script type="text/javascript">
        $('html, body').animate({scrollTop: $('.conteudo').offset().top}, 'slow');
    </script>
    <div class="npc-derrota">
        <div class="dados">
            <i class="fas fa-thumbs-down"></i>
            <div class="info-derrota">
                <p>Você Perdeu!</p>
                <p><strong><?php echo $personagem->nome; ?></strong> foi derrotado por <?php echo $oponente->nome; ?>.</p>
            </div>
        </div>
        <form id="concluirBatalha" method="post">
            <input type="submit" class="bts-form" name="concluir" value="Concluir" />
        </form>
    </div>
<?php } ?>

<!-- Battle Arena -->
<div class="batalha">
    <input type="hidden" name="finalizado" id="finalizado" value="<?php echo $npc_finalizado; ?>" />
    <input type="hidden" name="round" id="round" value="<?php echo $npc_desafiador; ?>" />
    <?php
        if(isset($_SESSION['verao_npc_life'])){
            $npc_life = $_SESSION['verao_npc_life'];
        } else {
            $npc_life = 0;
        }

        if(isset($_SESSION['verao_npc_life_oponente'])){
            $npc_life_oponente = $_SESSION['verao_npc_life_oponente'];
        } else {
            $npc_life_oponente = 0;
        }
        
        if(isset($_SESSION['verao_npc_ki_oponente'])){
            $npc_ki_oponente = $_SESSION['verao_npc_ki_oponente'];
        } else {
            $npc_ki_oponente = 0;
        }
        
        $veraonpc->printConfronto($parametro_1, $idPersonagem, $npc_life, $npc_life_oponente, $personagem->mana, $user->vip, $npc_ki_oponente); 
    ?>
</div>

<!-- Give Up Button -->
<?php if(!isset($_SESSION['verao_npc_vitoria']) && !isset($_SESSION['verao_npc_derrota'])): ?>
<div style="text-align: center; margin-top: 15px;">
    <form method="post" style="margin: 0;">
        <button type="submit" name="conceder" class="btn-conceder" onclick="return confirm('Tem certeza que deseja desistir da batalha?')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 5px;">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
            Desistir da Batalha
        </button>
    </form>
</div>
<?php endif; ?>

<style>
.btn-conceder {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(231, 76, 60, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-conceder:hover {
    background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
    box-shadow: 0 6px 12px rgba(231, 76, 60, 0.4);
    transform: translateY(-2px);
}
</style>

<script>
// Disable F5 during battle
document.addEventListener('keydown', function(e) {
    <?php if(isset($_SESSION['verao_npc_id'])): ?>
    if(e.key === 'F5' || (e.ctrlKey && e.key === 'r')){
        e.preventDefault();
        alert('F5 e Ctrl+R desabilitados durante a batalha!');
        return false;
    }
    <?php endif; ?>
});

// Pause battle when player leaves page
window.addEventListener('beforeunload', function(e) {
    var formData = new FormData();
    formData.append('action', 'pause');
    formData.append('verao_npc_id', '<?php echo $_SESSION["verao_npc_id"]; ?>');
    
    navigator.sendBeacon('<?php echo BASE; ?>ajax/ajaxVeraoNpc.php', formData);
});
</script>
