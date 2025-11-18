<?php 

    if (isset($_POST['conceder'])) {
        // Mark the battle as finished/lost for the player
    if (isset($_SESSION['npc_id'])) {
        $core = new Core();
        $npc_battle = $core->getDados('npc', 'WHERE id = '.$_SESSION['npc_id']);
        // If the match is finished, clear the session!
        if ($npc_battle && $npc_battle->concluido == 1) {
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
        } elseif ($npc_battle && $npc_battle->concluido == 0) {
            // Only redirect if active
            header('Location: ' . BASE . 'npc/' . $_SESSION['npc_id']);
            exit;
        }
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


        $personagem->getGuerreiro($idPersonagem);

        if($npc->npcRun($idPersonagem, $parametro_1)){
        $sql = "SELECT * FROM npc WHERE id = ".$_SESSION['npc_id']." AND concluido = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $dados_npc = $stmt->fetch();

            // Check if NPC needs to counter-attack
            if($dados_npc->atacou == 1 && $dados_npc->atacado == 0 && $dados_npc->pausado == 0){
                // Check if battle time hasn't expired
                if($dados_npc->time_final > time()){
                    // NPC counter-attacks
                    $npc->atack(4, $parametro_1, $idPersonagem, 0, 0);

                    // Redirect to refresh
                    header('Location: '.BASE.'npc/'.$parametro_1);
                    exit();
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

            if(isset($oponente) && $oponente->hp == 0){
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

                $personagem->checkLevelUp($idPersonagem);
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
            unset($_SESSION['npc_atacado']);
            unset($_SESSION['npc_vitoria']);
            unset($_SESSION['npc_derrota']);
            unset($_SESSION['npc_desafiador']);
            unset($_SESSION['npc_finalizado']);
            unset($_SESSION['npc_life']);
            unset($_SESSION['npc_life_oponente']);
            unset($_SESSION['npc_final']);

            header('Location: '.BASE.'torneio');
            exit;
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

        // ✅ Check if battle ended immediately after attack
        $sql = "SELECT * FROM npc WHERE id = ".intval($_SESSION['npc_id']);
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $battle_check = $stmt->fetch();

        if($battle_check){
            // Get current HP values
            $lifes = $npc->getLifeRestante($battle_check->id);
            $player_hp = $personagem->hp - $lifes->dano_atacante;
            $npc_hp = $oponente->hp - $lifes->dano_atacado;
            
            // If either is dead, end battle immediately
            if($player_hp <= 0 || $npc_hp <= 0){
                // Mark battle as finished
                $campos_fim = array(
                    'concluido' => 1,
                
                );
                $core->update('npc', $campos_fim, 'id = '.$battle_check->id);
                $_SESSION['npc_finalizado'] = 1;
                
                // Determine winner
                if($npc_hp <= 0){
                    $_SESSION['npc_vitoria'] = 1;  // Player wins
                } else {
                    $_SESSION['npc_derrota'] = 1;  // Player loses
                }
                
                // Redirect to show result
                header('Location: '.BASE.'npc/'.$parametro_1);
                exit();
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
