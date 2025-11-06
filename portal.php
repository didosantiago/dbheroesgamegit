<?php 

    // Redirect logged-out users to home/login
    if(!isset($_SESSION['user_logado']) || $_SESSION['user_logado'] !== true){
        header('Location: '.BASE.'home');
        exit;
    }

    // ============ MISSION/HUNTING TIMER ============
    $tempoMissao = 0;
    $tempoTotal = 0; // Total time for display
    $idMissao = 0;
    $missaoAtiva = null;
    $tipoBusca = 'missao'; // 'missao' or 'cacada'
    
    // Check for active mission
    if(isset($_SESSION['missao'])) {
        $dadosMissao = $core->getDados('personagens_missoes', 'WHERE idPersonagem = '.$_SESSION['PERSONAGEMID'].' AND concluida = 0 AND cancelada = 0');
        if($dadosMissao) {
            $idMissao = $dadosMissao->id;
            $missaoAtiva = $dadosMissao;
            $tipoBusca = 'missao';
            
            if($dadosMissao->tempo_final && $dadosMissao->tempo_final > time()) {
                $tempoMissao = $dadosMissao->tempo_final - time();
                $tempoTotal = $tempoMissao;
            }
        }
    }
    
    // If no mission, check for active hunting
    if(!$missaoAtiva && isset($_SESSION['cacada'])) {
        $dadosCacada = $core->getDados('personagens_cacadas', 'WHERE idPersonagem = '.$_SESSION['PERSONAGEMID'].' AND ativa = 1');
        if($dadosCacada) {
            $missaoAtiva = $dadosCacada;
            $tipoBusca = 'cacada';
            
            if($dadosCacada->tempo_final && $dadosCacada->tempo_final > time()) {
                $tempoMissao = $dadosCacada->tempo_final - time();
                $tempoTotal = $tempoMissao;
            }
        }
    }

    // Get Double EXP data
    $tempoRestanteDouble = 0;
    $dadosDouble = null;
    
    try {
        if(method_exists($core, 'monitoraDoubleExp')){
            $core->monitoraDoubleExp();
        }
        
        $dadosDouble = $core->getDados('adm_double_exp');
        
        if($dadosDouble && method_exists($core, 'convertDataDoubleEXP')) {
            $time_atual = time();
            
            if(method_exists($core, 'convertDataDoubleEXPInicio')){
                $time_inicio = $core->convertDataDoubleEXPInicio();
                $tempoRestanteInicio = $time_inicio - $time_atual;
            } else {
                $tempoRestanteInicio = -1;
            }
            
            if($tempoRestanteInicio <= 0){
                $tempoRestanteDouble = $core->convertDataDoubleEXP() - $time_atual;
            } else {
                $tempoRestanteDouble = 1;
            }
            
            if($tempoRestanteDouble < 0){
                $tempoRestanteDouble = 0;
            }
        }
    } catch (Exception $e) {
        $tempoRestanteDouble = 0;
        $dadosDouble = (object) array('status' => 0, 'periodo' => 0);
    }
    
    if(!$dadosDouble){
        $dadosDouble = (object) array('status' => 0, 'periodo' => 0);
    }

    // Handle poll voting
    if(isset($_POST['votar_enquete'])){
        $dados = $core->getDados('adm_enquetes_opcoes', "WHERE id = ".addslashes($_POST['votar_enquete']));
        
        $campos = array(
            'votos' => intval($dados->votos) + 1
        );

        $where = 'id = '.addslashes($_POST['votar_enquete']);

        if($core->update('adm_enquetes_opcoes', $campos, $where)){
            $campos = array(
                'idEnquete' => $dados->idEnquete,
                'idUsuario' => $user->id,
                'data' => date('Y-m-d H:i:s'),
                'voto' => addslashes($_POST['votar_enquete'])
            );
            
            if($core->insert('adm_enquetes_usuarios', $campos)){
                $core->msg('sucesso', 'Votação Realizada.');
                header('Location: '.BASE.'portal');
                exit;
            }
        } else {
            $core->msg('error', 'Erro ao Votar.');
        }
    }
?>

<div class="video-bar">
    <a href="https://www.youtube.com/channel/UC88PqK6ByP47PrcyW5oCYrQ" target="_blank">
        <img src="<?php echo BASE; ?>assets/banner-youtube.jpg" />
        <script src="https://apis.google.com/js/platform.js"></script>
        <div class="g-ytsubscribe" data-channelid="UC88PqK6ByP47PrcyW5oCYrQ" data-layout="default" data-count="default"></div>
    </a>
</div>

<div class="widgets widget-news">
    <div class="mural">
        <div class="news">
            <div class="info img">
                <h3>Título da notícia</h3>
                <span>Publicado em 05/01/2022</span>
                <div class="descricao">Bem-vindo ao DB Heroes Game!</div>
            </div>
        </div>
    </div>
</div>

<div class="evento">
    <a href="<?php echo BASE; ?>invasao">
        <img src="<?php echo BASE.'assets/boss/boss_freeza.jpg'; ?>" />
    </a>
</div>

<ul class="ultimos-acontecimentos">
    <!-- Mission/Hunting Timer (if active) -->
    <?php if($missaoAtiva && $tempoMissao > 0): ?>
    <li class="double-exp">
        <h4><?php echo strtoupper($tipoBusca); ?> ATIVA</h4>
        <div class="contador-double-exp cont timer-missao" id="timer-missao" data-tempo="<?php echo $tempoTotal; ?>">00:00:00</div>
        <span>Tempo Restante</span>
    </li>
    <?php endif; ?>
    
    <!-- Double EXP Timer -->
    <li class="double-exp">
        <img src="<?php echo BASE; ?>assets/bg-double-exp.jpg" />
        <h4>DOUBLE EXP</h4>
        <div class="contador-double-exp cont" id="contador-double-exp">00:00:00</div>
        <span>Tempo Restante</span>
    </li>
    
    <li class="nf">
        <img src="<?php echo BASE.'assets/bg-ultimos-avisos.jpg'; ?>" />
        <h4>Avisos</h4>
        <p>Fique atento às novidades!</p>
    </li>
</ul>

<?php if(isset($config) && isset($config->video_destaque)){ ?>
<div class="video h-mobile">
    <iframe width="503" height="320" src="https://www.youtube.com/embed/<?php echo $config->video_destaque; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>

<div class="video h-mobile">
    <iframe width="503" height="320" src="https://www.youtube.com/embed/<?php echo isset($config->video_destaque_prev) ? $config->video_destaque_prev : ''; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>
<?php } ?>

<ul class="lista-acesso-rapido">
    <li class="h-mobile" style="margin-right: 0; width: 252px;">
        <a href="<?php echo BASE; ?>vantagens">
            <i class="fas fa-crown"></i>
            <div class="bundle">Novo</div>
            <span>Seja VIP</span>
            <p>Seja vip sem tempo de expiração</p>
        </a>
    </li>
    <li class="h-mobile">
        <a href="<?php echo BASE; ?>loja">
            <i class="fas fa-store"></i>
            <div class="bundle">Novo</div>
            <span>Loja de Items</span>
            <p>Itens Diários com duração de 24 Horas</p>
        </a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>banco">
            <i class="fas fa-piggy-bank"></i>
            <span>Banco Central</span>
            <p>Depósitos, Saques de Gold e Venda de Itens</p>
        </a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>bonus-diario">
            <i class="fas fa-box"></i>
            <span>Bônus Diário</span>
            <p>Colete itens e Gold todo dia</p>
        </a>
    </li>
    <li class="h-mobile" style="margin-right: 0; width: 252px;">
        <a href="<?php echo BASE; ?>market">
            <i class="fas fa-cart-plus"></i>
            <span>Mercado</span>
            <p>Compre e Venda seus itens no Mercado</p>
        </a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>troca-guerreiro">
            <i class="fas fa-male"></i>
            <span>Troca de Guerreiro FREE</span>
            <p>Troque agora seu guerreiro por outro personagem</p>
        </a>
    </li>
    <li>
        <a href="<?php echo BASE; ?>treinar">
            <i class="fas fa-upload"></i>
            <span>Treinar Guerreiro</span>
            <p>Treine os Atributos de seu Guerreiro</p>
        </a>
    </li>
    <li class="h-mobile">
        <a href="<?php echo BASE; ?>equipes">
            <i class="fas fa-users"></i>
            <span>Equipes</span>
            <p>Crie ou participe de Equipes e aumente seus Atributos</p>
        </a>
    </li>
</ul>

<?php if(isset($config) && isset($config->video_destaque)){ ?>
<div class="video-mobile v-mobile">
    <a href="https://www.youtube.com/watch?v=<?php echo $config->video_destaque; ?>" target="_blank">
        <i class="fab fa-youtube"></i>
        <span>Vídeo Tutorial</span>
    </a>
</div>
<?php } ?>

<?php if(isset($administrar) && method_exists($administrar, 'existsEnquete') && $administrar->existsEnquete()){ ?>
    <div class="enquete">
        <h3>Enquete</h3>
        <?php $dadosEnquete = $administrar->getEnquete(); ?>
        
        <h4><?php echo $dadosEnquete->pergunta ?></h4>
        
        <?php if(!$core->isExists('adm_enquetes_usuarios', "WHERE idUsuario = $user->id AND idEnquete = ".$dadosEnquete->id)){ ?>
            <form id="formEnquete" action="" method="post">
                <?php echo $administrar->getOptionsEnquete($dadosEnquete->id); ?>
            </form>
        <?php } else { ?>
            <ul>
                <?php echo $administrar->getPorcentagensEnquete($dadosEnquete->id); ?>
            </ul>
        <?php } ?>
    </div>
<?php } ?>

<div class="redes-sociais">
    <div class="banner-1 h-mobile">
        <a href="https://www.instagram.com/dbheroesgame/" target="_blank">
            <img src="<?php echo BASE; ?>assets/bn-insta.jpg" />
        </a>
    </div>

    <div class="banner-2 h-mobile">
        <a href="https://www.facebook.com/dbheroesgame" target="_blank">
            <img src="<?php echo BASE; ?>assets/bn-face.jpg" />
        </a>
    </div>
</div>

<!-- ============ JAVASCRIPT TIMERS ============ -->
<script type="text/javascript">
    // ============ MISSION/HUNTING TIMER (WORKS!) ============
    <?php if($missaoAtiva && $tempoMissao > 0): ?>
    (function() {
        let tempoRestante = <?php echo $tempoMissao; ?>;
        const timerElement = document.getElementById('timer-missao');
        
        if (!timerElement) return;
        
        function updateTimer() {
            if (tempoRestante > 0) {
                let horas = Math.floor(tempoRestante / 3600);
                let minutos = Math.floor((tempoRestante % 3600) / 60);
                let segundos = tempoRestante % 60;
                
                let display = 
                    String(horas).padStart(2, '0') + ':' +
                    String(minutos).padStart(2, '0') + ':' +
                    String(segundos).padStart(2, '0');
                
                timerElement.textContent = display;
                tempoRestante--;
            } else {
                timerElement.textContent = '00:00:00';
                clearInterval(timerInterval);
                setTimeout(() => location.reload(), 1000);
            }
        }
        
        updateTimer(); // Update immediately
        const timerInterval = setInterval(updateTimer, 1000); // Update every second
    })();
    <?php endif; ?>
    
    // ============ DOUBLE EXP TIMER ============
    (function() {
        var tempoRestante = <?php echo $tempoRestanteDouble; ?>;
        const doubleExpElement = document.getElementById('contador-double-exp');
        
        if (!doubleExpElement) return;
        
        function startCountdownDouble(tempo) {
            if (tempo > 0) {
                var min = parseInt(tempo / 60);
                var horas = parseInt(min / 60);
                min = min % 60;
                var seg = tempo % 60;
                
                if (min < 10) min = "0" + min;
                if (seg <= 9) seg = "0" + seg;
                if (horas <= 9) horas = "0" + horas;
                
                var horaImprimivel = horas + ':' + min + ':' + seg;
                doubleExpElement.innerHTML = horaImprimivel;
                tempo--;
                
                setTimeout(function() {
                    startCountdownDouble(tempo);
                }, 1000);
            } else {
                doubleExpElement.innerHTML = "00:00:00";
            }
        }
        
        if (tempoRestante > 0) {
            startCountdownDouble(tempoRestante);
        }
    })();
    
    // ============ POLL VOTING ============
    $('#formEnquete input[name="votar_enquete"]').on('change', function() {
        $('#formEnquete').submit();
    });
</script>
