<?php 

    if (isset($_POST['conceder'])) {
        if (isset($_SESSION['npc_id'])) {
            $core = new Core();
            
            // 1. Mark battle as concluded and player as loser
            $campos_fim = array(
                'concluido' => 1,
                'vencedor' => 0 // 0 means NPC won / Player lost
            );
            $core->update('npc', $campos_fim, 'id = '.$_SESSION['npc_id']);
            
            // 2. Clear all battle sessions
            unset($_SESSION['npc']);
            unset($_SESSION['npc_id']);
            unset($_SESSION['npc_atacado']);
            unset($_SESSION['npc_vitoria']);
            unset($_SESSION['npc_derrota']);
            unset($_SESSION['npc_desafiador']);
            unset($_SESSION['npc_finalizado']);
            unset($_SESSION['npc_life']);
            unset($_SESSION['npc_life_oponente']);
            unset($_SESSION['npc_ki_oponente']);
            unset($_SESSION['npc_final']);
            
            // 3. Redirect to Torneio (EXITING the battle)
            header('Location: ' . BASE . 'torneio');
            exit;
        }
    }


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

        // Check if there's an old finished battle in session
        // Initialize $npc object
        $npc = new Npc();

        // If there's a URL parameter, load opponent data FIRST
        if($parametro_1 != null){
            $oponente = $npc->getOponenteNPC($parametro_1);
            
            // ✅ CHECK DATABASE FIRST: See if this battle just ended
            $sql_check_finished = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 1 ORDER BY id DESC LIMIT 1";
            $stmt_finished = DB::prepare($sql_check_finished);
            $stmt_finished->execute();
            
            if($stmt_finished->rowCount() > 0){
                // Battle is finished! Set victory/defeat session
                $finished_battle = $stmt_finished->fetch();
                
                if($finished_battle->vencedor == 1){
                    $_SESSION['npc_vitoria'] = true;
                } else {
                    $_SESSION['npc_derrota'] = true;
                }
                $_SESSION['npc_finalizado'] = 1;
                $_SESSION['npc_id'] = $finished_battle->id;
                $_SESSION['npc'] = true;
            }
            // ✅ Only load existing ACTIVE battles if no victory/defeat session exists
            elseif(!isset($_SESSION['npc_vitoria']) && !isset($_SESSION['npc_derrota'])){
                $sql_check_existing = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 0 ORDER BY id DESC LIMIT 1";
                $stmt_check = DB::prepare($sql_check_existing);
                $stmt_check->execute();

                if($stmt_check->rowCount() > 0){
                    $existing_battle = $stmt_check->fetch();
                    $_SESSION['npc'] = true;
                    $_SESSION['npc_id'] = $existing_battle->id;
                    $_SESSION['npc_atacado'] = $existing_battle->atacado;
                    $_SESSION['npc_desafiador'] = ($existing_battle->atacado == 1) ? 1 : 0;
                }
            }


        
        // ... rest of the code continues

            
            // Check if there's a session battle ID and if it matches the URL
            if(isset($_SESSION['npc_id']) && $_SESSION['npc_id'] != $parametro_1){
                // Different battle in session, check if old one is finished
                $core = new Core();
                $battle_check = $core->getDados('npc', 'WHERE id = '.$_SESSION['npc_id']);
                
                if($battle_check && $battle_check->concluido == 1){
                    // Old battle finished, clear session
                    unset($_SESSION['npc']);
                    unset($_SESSION['npc_id']);
                    unset($_SESSION['npc_atacado']);
                    unset($_SESSION['npc_vitoria']);
                    unset($_SESSION['npc_derrota']);
                    unset($_SESSION['npc_desafiador']);
                }
            }
        } elseif(isset($_SESSION['npc_id'])){
            // No URL parameter but session exists, redirect to the battle
            header('Location: '.BASE.'npc/'.$_SESSION['npc_id']);
            exit;
        }

            if (isset($_POST['concluir'])) {
        // Re-get the opponent ID from the URL to be safe
        $opponent_id_from_url = Url::getURL(1);

        // ✅ FIX: Check if the opponent ID from the URL is valid
        if (empty($opponent_id_from_url)) {


            // If no ID, we can't process. Just clear session and leave.
            unset($_SESSION['npc'], $_SESSION['npc_id'], $_SESSION['npc_vitoria'], $_SESSION['npc_derrota']);
            header('Location: ' . BASE . 'torneio');
            exit;
        }

        // Now we know the ID is not empty. Let's get the opponent object for EXP.
        // Assumes $npc object is already created.
        $oponente_obj = $npc->getOponenteNPC($opponent_id_from_url);

        if (isset($_SESSION['npc_vitoria'])) {
            // --- On Victory ---
            if ($oponente_obj) {
                $exp_recebido = $oponente_obj->exp;
                // ... (rest of EXP calculations)
                $campos_usuario = [
                    'tam' => intval($personagem->tam) + 1,
                    'exp' => intval($personagem->exp) + intval($exp_recebido) // ... add other exp bonuses
                ];
                $core->update('usuarios_personagens', $campos_usuario, 'id = "' . $idPersonagem . '"');
                $personagem->checkLevelUp($idPersonagem);
            }

            // Mark battle as won
            $core->update('npc', ['vencedor' => 1, 'concluido' => 1], 'idPersonagem = ' . $idPersonagem . ' AND idDesafiado = ' . $opponent_id_from_url . ' AND concluido = 0');
            
        } else {
            // --- On Loss or other cases ---
            // Mark battle as lost
            $core->update('npc', ['vencedor' => 0, 'concluido' => 1], 'idPersonagem = ' . $idPersonagem . ' AND idDesafiado = ' . $opponent_id_from_url . ' AND concluido = 0');
        }

        // --- Clean up and Redirect ---

        // ✅ FIX: Save remaining HP to database
        if($_SESSION['npc_finalizado'] == 1){
            $campos_hp = array(
                'hp' => $_SESSION['npc_life'],  // Save current HP
                'time_hp' => time()
            );
            $core->update('usuarios_personagens', $campos_hp, 'id = "'.$idPersonagem.'"');
        }

        // --- Clean up and Redirect ---
        unset(
            $_SESSION['npc'],
            // ... rest of unsets
        );

        // Unset all battle session variables
        unset(
            $_SESSION['npc'], $_SESSION['npc_id'], $_SESSION['npc_atacado'], $_SESSION['npc_vitoria'],
            $_SESSION['npc_derrota'], $_SESSION['npc_desafiador'], $_SESSION['npc_finalizado'],
            $_SESSION['npc_life'], $_SESSION['npc_life_oponente'], $_SESSION['npc_ki_oponente'], $_SESSION['npc_final']
        );

        // Redirect to tournament page
        header('Location: ' . BASE . 'torneio');
        exit;
    }


        $personagem->getGuerreiro($idPersonagem);

                // ✅ ADD THIS: Check if battle just ended (BEFORE any other logic)
        if(isset($_SESSION['npc_id'])){
            $check_battle_end = $core->getDados('npc', 'WHERE id = '.$_SESSION['npc_id']);
            if($check_battle_end && $check_battle_end->concluido == 1){
                // Battle just ended! Set victory/defeat session based on winner
                if($check_battle_end->vencedor == 1){
                    $_SESSION['npc_vitoria'] = true;
                } else {
                    $_SESSION['npc_derrota'] = true;
                }
                $_SESSION['npc_finalizado'] = 1;
            }
        }


        if($npc->npcRun($idPersonagem, $parametro_1)){
            $sql = "SELECT * FROM npc WHERE id = ".$_SESSION['npc_id']." AND concluido = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $dados_npc = $stmt->fetch();

                // ✅ BUG #5 FIX: Check if timer expired
                if($dados_npc->time_final < time() && $dados_npc->pausado == 0){
                    // Timer expired!
                    
                    // If it was player's turn (atacado=1), player took too long
                    if($dados_npc->atacado == 1){
                        // Penalty: Player loses the round, NPC attacks for free
                        
                        // Calculate NPC HP to ensure it's alive before attacking
                        $lifes = $npc->getLifeRestante($dados_npc->id);
                        
                        if($user->vip == 1){
                            $porcentagemVip = round((40/100) * intval($oponente->hp));
                            $hp_npc_max = round($oponente->hp - $porcentagemVip);
                        } else {
                            $porcentagemFree = round((20/100) * intval($oponente->hp));
                            $hp_npc_max = round($oponente->hp - $porcentagemFree);
                        }
                        $npc_hp = $hp_npc_max - $lifes->dano_atacado;
                        
                        // Only attack if NPC is alive
                        if($npc_hp > 0){
                            $npc->atack(4, $parametro_1, $idPersonagem, 0, 0);
                            
                            // Reset timer for next round
                            $campos_timer = array(
                                'time_final' => time() + 30,
                                'atacado' => 1,
                                'atacou' => 1
                            );
                            $where_timer = 'id = ?';
                            $whereParams_timer = array($dados_npc->id);
                            $core->update('npc', $campos_timer, $where_timer, $whereParams_timer);
                            
                            
                            exit();
                        }
                    }
                    
                    // If it was NPC's turn (atacou=1), NPC took too long (shouldn't happen, but reset)
                    if($dados_npc->atacou == 1){
                        $campos_timer_reset = array('time_final' => time() + 30);
                        $where_timer_reset = 'id = ?';
                        $whereParams_timer_reset = array($dados_npc->id);
                        $core->update('npc', $campos_timer_reset, $where_timer_reset, $whereParams_timer_reset);
                    }
                }

                // ✅ EXISTING CODE: Check if NPC needs to counter-attack
                if($dados_npc->atacou == 1 && $dados_npc->atacado == 0 && $dados_npc->pausado == 0){
                    // Calculate NPC current HP
                    $lifes = $npc->getLifeRestante($dados_npc->id);
                    
                    // Apply VIP/Free HP reduction
                    if($user->vip == 1){
                        $porcentagemVip = round((40/100) * intval($oponente->hp));
                        $hp_npc_max = round($oponente->hp - $porcentagemVip);
                    } else {
                        $porcentagemFree = round((20/100) * intval($oponente->hp));
                        $hp_npc_max = round($oponente->hp - $porcentagemFree);
                    }
                    $npc_hp = $hp_npc_max - $lifes->dano_atacado;

                    // Only attack if NPC is alive AND timer hasn't expired
                    if($npc_hp > 0 && $dados_npc->time_final > time()){
                        $npc->atack(4, $parametro_1, $idPersonagem, 0, 0);
                        
              
                    }
                }
            }
        }


        if($npc->npcRun($idPersonagem, $parametro_1)){
            $sql = "SELECT * FROM npc WHERE id = ".$_SESSION['npc_id'];
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $npc_info = $stmt->fetch();

            if($npc_info->pausado == 1){
                // ✅ BUG #6 FIX: Check pause timeout (5 minutes = 300 seconds)
                if(isset($npc_info->time_pausado) && $npc_info->time_pausado > 0){
                    $pause_duration = time() - $npc_info->time_pausado;
                    
                    if($pause_duration > 300){
                        // Paused too long (5+ minutes)! Auto-forfeit the battle
                        $campos_forfeit = array(
                            'pausado' => 0,
                            'concluido' => 1,
                            'vencedor' => 0 // Player loses
                        );
                        $where_forfeit = 'id = ?';
                        $whereParams_forfeit = array($npc_info->id);
                        $core->update('npc', $campos_forfeit, $where_forfeit, $whereParams_forfeit);
                        
                        // Set defeat session
                        $_SESSION['npc_derrota'] = true;
                        
                        // Clear battle sessions
                        unset($_SESSION['npc']);
                        unset($_SESSION['npc_id']);
                        unset($_SESSION['npc_atacado']);
                        unset($_SESSION['npc_desafiador']);
                        unset($_SESSION['npc_finalizado']);
                        unset($_SESSION['npc_life']);
                        unset($_SESSION['npc_life_oponente']);
                        unset($_SESSION['npc_ki_oponente']);
                        unset($_SESSION['npc_final']);
                        
                        $core->msg('error', 'Batalha cancelada por inatividade prolongada (5 minutos).');
                        header('Location: '.BASE.'torneio');
                        exit();
                    }
                }
                
                // ✅ EXISTING RESUME LOGIC (unchanged):
                if($npc_info->atacado == 1){
                    $campos = array(
                        'pausado' => 0,
                        'time_inicial' => time(),
                        'time_final' => time() + 30
                    );

                    $where = 'id = ?';
                    $whereParams = array($_SESSION['npc_id']);

                    $core->update('npc', $campos, $where, $whereParams);
                }

                if($npc_info->atacou == 1){
                    $campos = array(
                        'pausado' => 0
                    );

                    $where = 'id = ?';
                    $whereParams = array($_SESSION['npc_id']);

                    $core->update('npc', $campos, $where, $whereParams);

                    $npc->atack(4, $parametro_1, $idPersonagem, 0);
                }
            }

            if(isset($oponente) && $oponente->hp == 0){
                $_SESSION['npc_derrota'] = true;
            }

            if($personagem->hp == 0){
                $_SESSION['npc_vitoria'] = true;
            }

            $habilitado = 0;
        } else {
            // ✅ DON'T clear victory/defeat sessions - they're needed for the popup!
            // Only clear if no victory/defeat exists
            if(!isset($_SESSION['npc_vitoria']) && !isset($_SESSION['npc_derrota'])){
                unset($_SESSION['npc']);
                unset($_SESSION['npc_id']);
                unset($_SESSION['npc_atacado']);
                unset($_SESSION['npc_desafiador']);
                unset($_SESSION['npc_finalizado']);
                unset($_SESSION['npc_life']);
                unset($_SESSION['npc_life_oponente']);
                unset($_SESSION['npc_ki_oponente']);
                unset($_SESSION['npc_final']);
            }
        }


        // ✅ SIMPLE FIX: Only create battle if no victory/defeat exists
        // ✅ CRITICAL: NEVER create new battle if victory/defeat session exists!
        if(!isset($_SESSION['npc']) && !isset($_SESSION['npc_vitoria']) && !isset($_SESSION['npc_derrota'])){
            if($habilitado == 1){
                // Check if battle already exists in database
                $sql_check = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 0";
                $stmt_check = DB::prepare($sql_check);
                $stmt_check->execute();
                
                if($stmt_check->rowCount() == 0){
                    
                    // --- SAFETY CHECK START ---
                    // Before creating a new battle, check if we JUST finished one!
                    $sql_safety = "SELECT * FROM npc WHERE idPersonagem = $idPersonagem AND idDesafiado = $parametro_1 AND concluido = 1 ORDER BY id DESC LIMIT 1";
                    $stmt_safety = DB::prepare($sql_safety);
                    $stmt_safety->execute();
                    $last_battle = $stmt_safety->fetch();

                    // If we found a finished battle, and it's the one we are in (via session ID or just logic), restore it!
                    if($stmt_safety->rowCount() > 0 && isset($_SESSION['npc_id']) && $last_battle->id == $_SESSION['npc_id']) {
                        // FORCE RESTORE THE VICTORY STATE
                        if($last_battle->vencedor == 1){
                            $_SESSION['npc_vitoria'] = true;
                        } else {
                            $_SESSION['npc_derrota'] = true;
                        }
                        // Do NOT create a new battle. Let the page load and show the popup.
                    } 
                    else {
                        // --- ORIGINAL CODE: Create New Battle ---
                        $npc->saveBatalhaNPC($idPersonagem, $parametro_1);
                        
                        $comeca = 1;
                        if($comeca == 1){
                            $_SESSION['npc_desafiador'] = 1;
                        } else {
                            $_SESSION['npc_desafiador'] = 0;
                        }
                        
                        if($npc->getGuerreiroNPCAtacado($_SESSION['npc_id'])){
                            $_SESSION['npc_desafiador'] = 1;
                        }
                    }
                    // --- SAFETY CHECK END ---

                } else {
                    // Battle exists - load it
                    // ... (rest of your code)

                    $existing = $stmt_check->fetch();
                    $_SESSION['npc'] = true;
                    $_SESSION['npc_id'] = $existing->id;
                    $_SESSION['npc_desafiador'] = ($existing->atacado == 1) ? 1 : 0;
                }
            }
        }




        if(isset($_POST['atacar'])){
            if(addslashes($_POST['estado']) == 1){
                if(!isset($_SESSION['npc_vitoria']) && !isset($_SESSION['npc_derrota'])){
                    if(isset($_SESSION['npc_final'])){
                        $npc_final = $_SESSION['npc_final'];
                    } else {
                        $npc_final = 0;
                    }
                    
                    $npc->atack(addslashes($_POST['idAtack']), $idPersonagem, $parametro_1, 1, $npc_final);
                    $_SESSION['npc_desafiador'] = 0;
                    
                    // Check if battle just ended
                    $sql_check_ended = "SELECT * FROM npc WHERE id = ".intval($_SESSION['npc_id']);
                    $stmt_check_ended = DB::prepare($sql_check_ended);
                    $stmt_check_ended->execute();
                    $battle_after_attack = $stmt_check_ended->fetch();
                    
                    if($battle_after_attack && $battle_after_attack->concluido == 1){
                        if($battle_after_attack->vencedor == 1){
                            $_SESSION['npc_vitoria'] = true;
                        } else {
                            $_SESSION['npc_derrota'] = true;
                        }
                        $_SESSION['npc_finalizado'] = 1;
                        
                        // ✅ CRITICAL: Force session write BEFORE redirect!
                        session_write_close();
                    }
                    
                    // Now redirect
                    header('Location: '.BASE.'npc/'.$parametro_1);
                    exit;
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

<!-- Conceder Button - Styled -->
<div style="text-align: center; margin-top: 15px;">
    <form method="post" style="margin: 0;">
        <button type="submit" name="conceder" class="btn-conceder" onclick="return confirm('Tem certeza que deseja desistir da batalha?');">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 5px;">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
            Desistir da Batalha
        </button>
    </form>
</div>
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-conceder:hover {
    background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
    box-shadow: 0 6px 12px rgba(231, 76, 60, 0.4);
    transform: translateY(-2px);
}

.btn-conceder:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(231, 76, 60, 0.3);
}

.btn-conceder svg {
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
}



</style>

<script>
// Pause battle when player leaves page
window.addEventListener('beforeunload', function(e) {
    // Use Navigator.sendBeacon for reliable page unload request
    var formData = new FormData();
    formData.append('action', 'pause');
    formData.append('npc_id', '<?php echo $_SESSION["npc_id"]; ?>');
    
    // sendBeacon is more reliable than AJAX for beforeunload
    navigator.sendBeacon('<?php echo BASE; ?>ajax/ajaxNPC.php', formData);
});
</script>
