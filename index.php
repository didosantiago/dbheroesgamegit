<?php 
    require_once './init.php';
    
    $modulo = Url::getURL(0);

    if($modulo == null){
        $modulo = "home";
    }

    if(Url::getURL(1) != ''){
        $acao = Url::getURL(1);
    } else {
        $acao = 'default';
    }
    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    } else {
        $pagina = '';
    }
    
    if (!$pagina) {
        $pc = "1";
    } else {
        $pc = $pagina;
    }
    
    // Define public pages that don't need the game layout
    $publicPages = array('home', 'login', 'cadastro', 'sobre', 'autenticar', 'rank', 'ranking', 'assistir');
    $isPublicPage = in_array($modulo, $publicPages);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
        
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        
    <link rel="shortcut icon" href="<?php echo BASE; ?>assets/favicon.ico" type="image/x-icon">
    <link type="text/css" rel="stylesheet" href="<?php echo BASE; ?>assets/dbheroes.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo BASE; ?>assets/dbheroes-vendor.css" />
    <script type="text/javascript" src="<?php echo BASE; ?>assets/jquery.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js" integrity="sha384-kW+oWsYx3YpxvjtZjFXqazFpA7UP/MbiY4jvs+RWZo2+N94PFZ36T6TFkc9O3qoB" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo BASE; ?>assets/db-heroes-vendor.min.js?v=15042019.2"></script>
    <script type="text/javascript" src="<?php echo BASE; ?>assets/modernizr.custom.js"></script>
    <script type="text/javascript" src="<?php echo BASE; ?>assets/jquery.dlmenu.js"></script>
    <script src="<?php echo BASE; ?>assets/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo BASE; ?>assets/db-heroes.min.js"></script>

</head>
<body class="<?php echo $modulo; ?>">
        <div class="top-bar">
            <button id="playTema" style="display: none;"></button>
            <audio id="intro">
                <source src="<?php echo BASE; ?>assets/soco.mp3" type="audio/mpeg">
            </audio>
        </div>
    
        <?php if(!$isPublicPage){ ?>
            <?php require_once 'includes/menu-mobile.php'; ?>
            <?php require_once 'includes/header.php'; ?>
        <?php } ?>
                  
        <?php if(!$isPublicPage){ ?>
            <div class="nao-validado-top">
                <p>Seu e-mail ainda não foi confirmado, confirme clicando no link enviado para seu email. Caso não tenha recebido <a href="<?php echo BASE; ?>perfil">Clique Aqui.</a></p>
            </div>  
        <?php } ?>

    <div class="container">        

            <?php if(!$isPublicPage){ ?>
                <?php
                    // Handle rewards confirmation - FIRST PRIORITY!
                    if(isset($_POST['confirmar_ganho']) && isset($_SESSION['PERSONAGEMID'])){
                        // Mark rewards as viewed in database
                        $campos = array('visualizado' => 1);
                        $where = 'idPersonagem = '.$_SESSION['PERSONAGEMID'].' AND visualizado = 0';
                        $core->update('personagens_new_valores', $campos, $where);
                        
                        // Redirect to prevent form resubmission
                        header('Location: '.BASE.$modulo);
                        exit;
                    }
                    
                    // Handle level up confirmation
                    if(isset($_POST['confirmarMSG'])){
                        unset($_SESSION['novo_nivel']);
                        unset($_SESSION['nivel_atual']);
                        header('Location: '.BASE.'portal');
                        exit;
                    }
                ?>

                <?php if(isset($_SESSION['novo_nivel']) && $_SESSION['novo_nivel'] == true){ ?>
                    <div class="float-msg" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: linear-gradient(135deg, #1a237e 0%, #0d47a1 50%, #01579b 100%); padding: 40px 60px; border-radius: 20px; z-index: 99999; box-shadow: 0 25px 50px rgba(0,0,0,0.8), 0 0 0 3px rgba(255,193,7,0.3); border: 3px solid #ffc107; text-align: center; min-width: 500px;">
                        
                        <div style="margin-bottom: 30px;">
                            <div style="font-size: 60px; margin-bottom: 15px; animation: bounce 1s infinite;">🎊</div>
                            <h2 style="color: #ffc107; font-size: 28px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 3px; text-shadow: 3px 3px 6px rgba(0,0,0,0.5), 0 0 20px rgba(255,193,7,0.4);">LEVEL UP!</h2>
                            <p style="color: #fff; font-size: 22px; margin: 0;">Você alcançou o</p>
                        </div>
                        
                        <div style="background: rgba(0,0,0,0.3); padding: 25px; border-radius: 15px; margin-bottom: 30px; border: 2px solid rgba(255,193,7,0.2);">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                                <span style="color: #fff; font-size: 24px;">Nível</span>
                                <span style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #1a237e; font-size: 48px; font-weight: bold; padding: 15px 30px; border-radius: 15px; box-shadow: 0 8px 20px rgba(255,193,7,0.4), inset 0 2px 0 rgba(255,255,255,0.3);"><?php echo isset($_SESSION['nivel_atual']) ? $_SESSION['nivel_atual'] : ''; ?></span>
                            </div>
                        </div>
                        
                        <form method="post" style="text-align: center;">
                            <button type="submit" name="confirmarMSG" class="bts-form" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #1a237e; padding: 18px 60px; border: none; border-radius: 50px; cursor: pointer; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; transition: all 0.3s ease; box-shadow: 0 8px 20px rgba(255,193,7,0.4), inset 0 1px 0 rgba(255,255,255,0.3); display: inline-flex; align-items: center; gap: 10px;" onmouseover="this.style.transform='scale(1.05) translateY(-2px)'; this.style.boxShadow='0 12px 30px rgba(255,193,7,0.6)';" onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.boxShadow='0 8px 20px rgba(255,193,7,0.4)';">
                                <span style="font-size: 24px;">✓</span>
                                CONFIRMAR
                            </button>
                        </form>
                    </div>
                    
                    <div class="backdrop-game" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 99998;"></div>
                    
                    <style>
                        @keyframes bounce {
                            0%, 100% { transform: translateY(0); }
                            50% { transform: translateY(-10px); }
                        }
                    </style>
                <?php } ?>

                <?php 
                    // Show hunt notification if active - CHECK DATABASE!
                    if(isset($_SESSION['PERSONAGEMID'])){
                        $sql_cacada = "SELECT * FROM cacadas 
                                    WHERE idPersonagem = ".$_SESSION['PERSONAGEMID']." 
                                    AND concluida = 0 
                                    AND cancelada = 0";
                        $stmt_cacada = DB::prepare($sql_cacada);
                        $stmt_cacada->execute();
                        
                        if($stmt_cacada->rowCount() > 0 && method_exists($personagem, 'getCacadaRunning')){
                            $cacada_ativa = $stmt_cacada->fetch();
                            
                            // Set session variables for this character's hunt
                            $_SESSION['cacada'] = true;
                            $_SESSION['cacada_id'] = $cacada_ativa->id;
                            
                            // This will auto-process rewards if hunt finished
                            echo $personagem->getCacadaRunning($_SESSION['PERSONAGEMID'], $cacada_ativa->id);
                        } else {
                            // No active hunt - clear sessions
                            unset($_SESSION['cacada']);
                            unset($_SESSION['cacada_id']);
                        }
                    }
                ?>

                <?php 
                    // Show PVP paused notification if active
                    if(isset($_SESSION['pvp']) && isset($_SESSION['pvp_id'])){
                ?>
                        <div class="pvp-paused">
                            <span>Você está em uma batalha PVP, volte para o combate para finalizar.</span>
                            <a href="<?php echo BASE; ?>combate/<?php echo $_SESSION['pvp_id']; ?>" class="bts-form" id="voltarBatalha">Ir para Batalha</a>
                        </div>
                <?php } ?>

                <?php 
                    // Show NPC battle notification if active
                    if(isset($_SESSION['npc']) && isset($_SESSION['npc_id'])){
                ?>
                        <div class="npc-paused">
                            <span>Você está em uma batalha do Torneio de Artes Marciais NPC, volte para o combate para finalizar.</span>
                            <a href="<?php echo BASE; ?>npc/<?php echo $_SESSION['npc_id']; ?>" class="bts-form" id="voltarBatalha">Ir para Batalha</a>
                        </div>
                <?php } ?>

                <?php 
                // Show mission notification if active - CHECK DATABASE!
                if(isset($_SESSION['PERSONAGEMID'])){
                    $sql_missao = "SELECT * FROM personagens_missoes 
                                WHERE idPersonagem = ".$_SESSION['PERSONAGEMID']." 
                                AND concluida = 0 
                                AND cancelada = 0 
                                AND tempo_final > ".time();
                    $stmt_missao = DB::prepare($sql_missao);
                    $stmt_missao->execute();
                    
                    if($stmt_missao->rowCount() > 0 && method_exists($personagem, 'getMissaoRunning')){
                        $missao_ativa = $stmt_missao->fetch();
                        
                        // Set session variables for this character's mission
                        $_SESSION['missao'] = true;
                        $_SESSION['missao_id'] = $missao_ativa->id;
                        
                        echo $personagem->getMissaoRunning($_SESSION['PERSONAGEMID'], $missao_ativa->id);
                    } else {
                        // Clear mission session if no active mission
                        unset($_SESSION['missao']);
                        unset($_SESSION['missao_id']);
                    }
                }
            ?>

                <?php 
                    // Show PVP penalty notification if active
                    if(isset($_SESSION['pvp_penalty'])){
                ?>
                        <div class="pvp-running">
                            <span>Você atacou recentemente, por isso deve aguardar o período de penalidade para novos ataques.</span>
                            <div class="contador"></div>
                        </div>
                <?php } ?>

                <?php 
                    // Show opponent penalty notification if active
                    if(isset($_SESSION['punicao_adversario'])){
                ?>
                        <div class="punicao-adversario">
                            <span>Este guerreiro foi atacado recentemente, aguarde a penalidade dele terminar para atacá-lo.</span>
                            <div class="contador"></div>
                        </div>
                <?php } ?>
            <?php } ?>
        
        <?php if(!$isPublicPage){ ?>
            <?php require_once 'includes/menu-lateral.php'; ?>
            <?php require_once 'includes/menu-flutuante.php'; ?>
        <?php } ?>

        <div class="conteudo">
            <?php
                if( file_exists( $modulo . ".php" )){
                    require $modulo . ".php";
                } else {
                    require "erro.php";
                }
            ?>
        </div>
    </div>

    <?php if(!$isPublicPage){ ?>
        <?php
            // Show rewards popup if there are new gains
            if(isset($_SESSION['PERSONAGEMID']) && method_exists($personagem, 'getNewValores')){
                if($personagem->getNewValores($_SESSION['PERSONAGEMID'])){
                    
                    // Get the rewards info
                    $sql = "SELECT * FROM personagens_new_valores WHERE idPersonagem = '".$_SESSION['PERSONAGEMID']."' AND visualizado = 0";
                    $stmt = DB::prepare($sql);
                    $stmt->execute();
                    $rewards = $stmt->fetchAll();
        ?>
                    <div class="backdrop-game" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 99998;"></div>
                    <div class="ganhos-game" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: linear-gradient(135deg, #1a237e 0%, #0d47a1 50%, #01579b 100%); padding: 40px 50px; border-radius: 20px; z-index: 99999; min-width: 500px; max-width: 600px; box-shadow: 0 25px 50px rgba(0,0,0,0.8), 0 0 0 3px rgba(255,193,7,0.3); color: white; text-align: center; border: 3px solid #ffc107;">
                        
                        <div style="margin-bottom: 30px;">
                            <h3 style="margin: 0; color: #ffc107; font-size: 32px; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; text-shadow: 3px 3px 6px rgba(0,0,0,0.5), 0 0 20px rgba(255,193,7,0.4);">
                                🎉 PARABÉNS! 🎉
                            </h3>
                        </div>
                        
                        <div class="infos" style="background: rgba(0,0,0,0.3); padding: 30px; border-radius: 15px; margin-bottom: 30px; border: 2px solid rgba(255,193,7,0.2);">
                            <?php foreach($rewards as $reward){ ?>
                                <?php if($reward->gold > 0){ ?>
                                    <div style="margin: 20px 0;">
                                        <div style="background: linear-gradient(90deg, rgba(255,193,7,0.2) 0%, rgba(255,193,7,0.05) 100%); padding: 18px 25px; border-radius: 12px; border-left: 5px solid #ffc107; display: inline-block; min-width: 80%;">
                                            <span style="font-size: 20px; color: #fff; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                                <span style="font-size: 28px;">💰</span>
                                                <span>Você ganhou</span>
                                                <strong style="color: #ffc107; font-size: 28px; text-shadow: 0 0 10px rgba(255,193,7,0.5);">+<?php echo number_format($reward->gold, 0, ',', '.'); ?></strong>
                                                <span>de Gold!</span>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if($reward->exp > 0){ ?>
                                    <div style="margin: 20px 0;">
                                        <div style="background: linear-gradient(90deg, rgba(76,175,80,0.2) 0%, rgba(76,175,80,0.05) 100%); padding: 18px 25px; border-radius: 12px; border-left: 5px solid #4caf50; display: inline-block; min-width: 80%;">
                                            <span style="font-size: 20px; color: #fff; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                                <span style="font-size: 28px;">⭐</span>
                                                <span>Você ganhou</span>
                                                <strong style="color: #4caf50; font-size: 28px; text-shadow: 0 0 10px rgba(76,175,80,0.5);">+<?php echo number_format($reward->exp, 0, ',', '.'); ?></strong>
                                                <span>de Experiência!</span>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        
                        <form id="confirmarGanho" method="post" action="" style="text-align: center;">
                            <input type="hidden" name="confirmar_ganho" value="1" />
                            <button type="submit" class="bts-form" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #1a237e; padding: 18px 60px; border: none; border-radius: 50px; cursor: pointer; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; transition: all 0.3s ease; box-shadow: 0 8px 20px rgba(255,193,7,0.4), inset 0 1px 0 rgba(255,255,255,0.3); display: inline-flex; align-items: center; gap: 10px; margin: 0 auto;" onmouseover="this.style.transform='scale(1.05) translateY(-2px)'; this.style.boxShadow='0 12px 30px rgba(255,193,7,0.6), inset 0 1px 0 rgba(255,255,255,0.3)';" onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.boxShadow='0 8px 20px rgba(255,193,7,0.4), inset 0 1px 0 rgba(255,255,255,0.3)';">
                                <span style="font-size: 24px;">✓</span>
                                CONFIRMAR
                            </button>
                        </form>
                    </div>
        <?php
                }
            }
        ?>
    <?php } ?>
            
    <input type="hidden" id="baseSite" value="<?php echo BASE; ?>" />
    <input type="hidden" id="personagemLogged" value="<?php echo isset($_SESSION['PERSONAGEMID']) ? $_SESSION['PERSONAGEMID'] : ''; ?>" />
    
    <?php if(!$isPublicPage){ ?>
        <div class="copy">
            <div class="container">
                <p>©2018 DB Heroes Game - <a href="<?php echo BASE; ?>doc/aviso-legal.pdf" target="_blank">Aviso Legal</a> - <a href="<?php echo BASE; ?>doc/politica-de-privacidade.pdf" target="_blank">Política de Privacidade</a> - <a href="<?php echo BASE; ?>doc/termos-de-uso.pdf" target="_blank">Termos de Uso</a> - <a href="<?php echo BASE; ?>doc/regras.pdf" target="_blank">Regras & Punições</a></p>
                <p>Personagens e desenhos © CopyRight 1984 by Akira Toriyama. Todos os direitos reservados</p>
            </div>
        </div>
    <?php } else { ?>
        <?php if(file_exists('front/footer-front.php')){ require_once 'front/footer-front.php'; } ?>
    <?php } ?>
            
    <div id="load-game">
        <img src="<?php echo BASE; ?>assets/load.gif" alt="Carregando..." />
    </div>
    
    <script type="text/javascript">
    var id = $('#personagemLogged').val();
    var data_string = 'id=' + id;
    var huntTimerRunning = false;

    // Hunt countdown timer
    if($('.cacada-running').length > 0 && $('.cacada-running .contador').length > 0){
        console.log("🎮 Hunt notification detected for character: " + id);
        
        function checkHuntStatus(){
            if(huntTimerRunning){
                console.warn("⚠️ Timer already running, skipping...");
                return;
            }
            
            huntTimerRunning = true;
            
            $.ajax({
                type: "POST",
                url: "<?php echo BASE; ?>ajax/ajaxCacada.php",
                data: data_string,
                success: function (res) {
                    console.log("📥 Hunt status: " + res);
                    
                    var seconds = parseInt(res);
                    
                    if(seconds > 0){
                        var horas = Math.floor(seconds / 3600);
                        var minutos = Math.floor((seconds % 3600) / 60);
                        var segs = seconds % 60;
                        
                        if(horas < 10) horas = "0" + horas;
                        if(minutos < 10) minutos = "0" + minutos;
                        if(segs < 10) segs = "0" + segs;
                        
                        var horaImprimivel = horas + ':' + minutos + ':' + segs;
                        $(".cacada-running .contador").html(horaImprimivel);
                        
                        huntTimerRunning = false;
                        setTimeout(checkHuntStatus, 1000);
                        
                    } else if(seconds == 0){
                        console.log("🎉 Hunt completed! Reloading page...");
                        $(".cacada-running .contador").html('00:00:00');
                        
                        setTimeout(function(){
                            location.reload(true);
                        }, 1000);
                    } else {
                        console.error("❌ Invalid response from AJAX: " + res);
                        huntTimerRunning = false;
                        location.reload(true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("🚨 AJAX Error: " + error);
                    huntTimerRunning = false;
                    setTimeout(checkHuntStatus, 2000);
                }
            });
        }
        
        checkHuntStatus();
    }

    // Mission countdown timer
    if($('.missao-running').length > 0 && $('.missao-running .contador').length > 0){
        $.ajax({
            type: "POST",
            url: "<?php echo BASE; ?>ajax/ajaxMissao.php",
            data: data_string,
            success: function (res) {
                if(res && parseInt(res) > 0){
                    startCountdownMissao(parseInt(res));
                }
            }
        });
    }
        
    function startCountdownMissao(tempo){
        if(tempo > 0){
            var min = parseInt(tempo/60);
            var horas = parseInt(min/60);
            min = min % 60;
            var seg = tempo%60;

            if(min < 10){
                min = "0"+min;
                min = min.substr(0, 2);
            }
            if(seg <=9){
                seg = "0"+seg;
            }
            if(horas <=9){
                horas = "0"+horas;
            }

            var horaImprimivel = horas + ':' + min + ':' + seg;
            $(".missao-running .contador").html(horaImprimivel);

            setTimeout(function(){ 
                startCountdownMissao(tempo - 1);
            }, 1000);
        } else {
            $(".missao-running .contador").html('00:00:00');
            setTimeout(function(){
                location.reload(true);
            }, 1000);
        }
    }
        
    $(document).on('click', '#cancelarCacada', function(e){
        e.preventDefault();
        
        var idCacada = $('#idCacada').val();
        console.log("🔴 Cancel button clicked! Hunt ID: " + idCacada);
        
        if(!idCacada || idCacada == '' || idCacada == '0'){
            console.error("❌ No hunt ID found!");
            swal('Erro!', 'ID da caçada não encontrado.', 'error');
            return false;
        }
        
        swal({
            title: 'Confirmar Cancelamento?',
            text: "Cancelando você não irá receber os prêmios!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não'
        }).then((result) => {
            if (result.value) {
                console.log("✅ User confirmed cancellation");
                
                huntTimerRunning = true;
                $('.cacada-running .contador').html('CANCELANDO...');
                
                var cancel_data = 'id=' + idCacada;

                $.ajax({
                    type: "POST",
                    url: "<?php echo BASE; ?>ajax/ajaxCancelarCacada.php",
                    data: cancel_data,
                    success: function (res) {
                        console.log("📥 Response: [" + res + "]");
                        
                        if(res.trim() == "success"){
                            swal(
                                'Cancelado!',
                                'Caçada cancelada com sucesso.',
                                'success'
                            );
                            setTimeout(function(){
                                window.location.href = "<?php echo BASE; ?>cacadas";
                            }, 1500);
                        } else {
                            swal(
                                'Erro!',
                                'Erro ao cancelar: ' + res,
                                'error'
                            );
                            huntTimerRunning = false;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("❌ AJAX Error: " + error);
                        swal(
                            'Erro!',
                            'Erro ao conectar: ' + error,
                            'error'
                        );
                        huntTimerRunning = false;
                    }
                });
            }
        });
        
        return false;
    });
    
    $('#cancelarMissao').on('click', function(){
        swal({
            title: 'Confirmar Cancelamento?',
            text: "Cancelando você não irá receber os prêmios!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, cancelar'
        }).then((result) => {
            if (result.value) {
                var id = $('#idMissao').val();
                var data_string = 'id=' + id;

                $.ajax({
                    type: "POST",
                    url: "<?php echo BASE; ?>ajax/ajaxCancelarMissao.php",
                    data: data_string,
                    success: function (res) {
                        swal(
                            'Cancelado!',
                            'Missão cancelada com sucesso.',
                            'success'
                        );
                        location.reload(true);
                    }
                });
            }
        });
    });

    // NOTE: We DON'T need JavaScript for confirmarGanho
    // The form POST from Personagens.php->getListaNewValores() handles it
</script>

<script>
// Initialize JOGAR button immediately on page load
$(document).ready(function(){
    // Attach jogar button handler
    $(document).on('click', '.bt-jogar', function(){
        if($('.loader').length <= 0){
            $('body').prepend('<div class="loader">'+
                            '<img src="<?php echo BASE; ?>assets/loader2.gif" alt="Carregando Game..." />'+
                            '<p>Carregando o Jogo, Aguarde...</p>'+
                        '</div>'); 
                
            var id = $(this).attr('dataid');
            var data_string = 'id=' + id;

            $.ajax({
                type: "POST",
                url: "<?php echo BASE; ?>ajax/ajaxJogar.php",
                data: data_string,
                success: function (res) {
                    console.log('Character switched successfully');
                }
            });
                
            setTimeout(function(){ 
                var base = window.location.href.split('/meus-personagens');
                var url = base[0] + '/portal';
                window.location.href = url;
            }, 2000);
            
            setTimeout(function(){ 
                $('.loader').remove();
            }, 2000);
        }
    });
    
    console.log('✅ JOGAR button handler initialized');
});


</script>

</body>
</html>