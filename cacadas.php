<?php
        // Calculate daily hunt time used (VIP members use half time)
        if($user->vip == 1){
            // VIP members: Count HALF of the tempo value
            $sql_usado = "SELECT SUM(tempo / 2) as total_usado FROM cacadas 
                          WHERE idPersonagem = ".$_SESSION['PERSONAGEMID']." 
                          AND data = CURDATE()
                          AND concluida = 1
                          AND cancelada = 0";
        } else {
            // Free members: Count full tempo value
            $sql_usado = "SELECT SUM(tempo) as total_usado FROM cacadas 
                          WHERE idPersonagem = ".$_SESSION['PERSONAGEMID']." 
                          AND data = CURDATE()
                          AND concluida = 1
                          AND cancelada = 0";
        }
        $stmt_usado = DB::prepare($sql_usado);
        $stmt_usado->execute();
        $tempo_usado_data = $stmt_usado->fetch();
        $tempo_usado = $tempo_usado_data->total_usado ? intval($tempo_usado_data->total_usado) : 0;
        
        // Define daily limits
        if($user->vip == 1){
            $limite_diario = 120; // 2 hours for VIP
            $status_vip = "(Membro VIP)";
        } else {
            $limite_diario = 60; // 1 hour for non-VIP
            $status_vip = "";
        }
        
        $tempo_restante = $limite_diario - $tempo_usado;
        
        // Process hunt submission
        if(isset($_POST['cacar'])){
            $tempo_solicitado = intval($_POST['tempo']);
            
            // Check if THIS character already has an active hunt
            $sql_check = "SELECT * FROM cacadas 
                        WHERE idPersonagem = ".$_SESSION['PERSONAGEMID']." 
                        AND concluida = 0 
                        AND cancelada = 0 
                        AND tempo_final > ".time();
            $stmt_check = DB::prepare($sql_check);
            $stmt_check->execute();
            
            if($stmt_check->rowCount() > 0){
                $core->msg('error', 'Este personagem já está em uma caçada ativa!');
            } else if($tempo_solicitado > $tempo_restante){
                $core->msg('error', 'Você não tem tempo suficiente! Tempo usado hoje: '.$tempo_usado.' minutos. Tempo restante: '.$tempo_restante.' minutos '.$status_vip);
            } else if($personagem->cacadaEsgotada($_SESSION['PERSONAGEMID'], addslashes($_POST['tempo']), $user->vip)){
                $core->msg('error', 'Tempo de Caçada diário Esgotado.');
            } else {
                if(!isset($_SESSION['cacada']) && !isset($_SESSION['missao'])){           
                    if(isset($_SESSION['PERSONAGEMID'])){
                        $personagem->getGuerreiro($_SESSION['PERSONAGEMID']);
                        $personagem->calculaCacada($user->id, $_POST, $personagem->idPlaneta, $_SESSION['PERSONAGEMID'], $user->vip, $personagem->nivel, $personagem->exp);
                    }
                } 
            }
        }
        
        if(isset($_SESSION['cacada']) || isset($_SESSION['missao'])){ 
            header('Location: '.BASE.'portal');
            exit;
        }
    ?>

<h2 class="title">Iniciar uma Caçada</h2>

<!-- Daily Hunt Time Status Alert -->
<div class="hunt-time-status" style="background: <?php echo ($tempo_restante > 0) ? '#4CAF50' : '#f44336'; ?>; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 16px; font-weight: bold;">
    <?php if($tempo_restante > 0){ ?>
        ✅ Tempo de Caçada Hoje: <span style="color: #ffeb3b;"><?php echo $tempo_usado; ?> minutos usados</span> | 
        <span style="color: #8bc34a;"><?php echo $tempo_restante; ?> minutos restantes</span>
        <?php if($user->vip == 1){ ?>
            <span style="background: #ff9800; padding: 5px 10px; border-radius: 5px; margin-left: 10px;">⭐ VIP: 2 Horas Diárias</span>
        <?php } else { ?>
            <span style="background: #ff5722; padding: 5px 10px; border-radius: 5px; margin-left: 10px;">Jogador Free: 1 Hora Diária</span>
        <?php } ?>
    <?php } else { ?>
        ❌ Tempo de Caçada Esgotado Hoje! Você já usou <?php echo $tempo_usado; ?> minutos.
        <?php if($user->vip == 1){ ?>
            (Limite VIP: <?php echo $limite_diario; ?> minutos por dia)
        <?php } else { ?>
            (Limite: <?php echo $limite_diario; ?> minutos por dia - <a href="<?php echo BASE; ?>vantagens" style="color: #ffeb3b; text-decoration: underline;">Seja VIP para 2 horas!</a>)
        <?php } ?>
    <?php } ?>
</div>

<?php if($tempo_restante <= 0){ ?>
    <!-- COUNTDOWN TIMER - Shows when hunts will be available again -->
    <div class="hunt-countdown-timer" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div style="font-size: 18px; margin-bottom: 10px; opacity: 0.9;">
            ⏰ Novas caçadas estarão disponíveis em:
        </div>
        <div id="countdown-display" style="font-size: 48px; font-weight: bold; letter-spacing: 3px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); font-family: 'Courier New', monospace;">
            00:00:00
        </div>
        <div style="font-size: 14px; margin-top: 10px; opacity: 0.8;">
            🌅 Meia-noite - Novo dia, novas aventuras!
        </div>
    </div>
    
    <script>
        // Countdown timer to midnight (when hunts reset)
        function updateCountdown() {
            var now = new Date();
            
            // Calculate midnight tonight (00:00:00)
            var midnight = new Date();
            midnight.setHours(24, 0, 0, 0); // Next midnight
            
            // Calculate time difference in milliseconds
            var diff = midnight - now;
            
            if(diff <= 0){
                // Midnight reached! Reload page to reset hunts
                location.reload();
                return;
            }
            
            // Convert to hours, minutes, seconds
            var hours = Math.floor(diff / (1000 * 60 * 60));
            var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            // Format with leading zeros
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            // Display countdown
            $('#countdown-display').html(hours + ':' + minutes + ':' + seconds);
        }
        
        // Update every second
        $(document).ready(function(){
            updateCountdown(); // Initial call
            setInterval(updateCountdown, 1000); // Update every 1 second
        });
    </script>
<?php } ?>

<p class="informativo h-mobile">
    Começe agora mesmo sua caçada. você recebe golds, e ainda pode ganhar caixas com itens para equipar seu guerreiro.
    <br><br>
    Aumente também sua Experiência
    <br><br>
    Procure Fazer suas caçadas sempre para que tenha uma boa recompensa diaria.
    <br><br>
    <strong>10 minutos (Membros VIP 5 minutos)</strong> = Você ganha 1 de Gold e 1 de Exp.
    <br>
    <strong>20 minutos (Membros VIP 5 minutos)</strong> = Você ganha 1 de Gold e 1 de Exp.
    <br>
    <strong>30 minutos (Membros VIP 5 minutos)</strong> = Você ganha 1 de Gold e 1 de Exp.
    <br>
    <strong>40 minutos (Membros VIP 5 minutos)</strong> = Você ganha 1 de Gold e 1 de Exp.
    <br>
    <strong>50 minutos (Membros VIP 5 minutos)</strong> = Você ganha 1 de Gold e 1 de Exp.
    <br>
    <strong>60 minutos (Membros VIP 5 minutos)</strong> = Você ganha 1 de Gold e 1 de Exp.
    <br>
    <br><br>
    - Tempo de Caçada Diário <strong>1 Hora</strong>
    <br>
    - Tempo de Caçada Diário (Membros VIP) <strong>2 Horas</strong>
</p>

<div class="forms-cacadas">
    <?php if($tempo_restante <= 0){ ?>
        <!-- Daily limit reached - Show disabled buttons -->
        <script>
            $(document).ready(function(){
                swal({
                    title: 'Limite Diário Atingido!',
                    text: 'Desculpe, você não pode realizar mais caçadas por hoje! Você já usou <?php echo $tempo_usado; ?> minutos. <?php echo $user->vip == 1 ? "Volte amanhã para mais 2 horas de caçadas!" : "Seja VIP para ter 2 horas diárias!"; ?>',
                    type: 'warning',
                    confirmButtonColor: '#f44336',
                    confirmButtonText: 'Entendi'
                });
            });
        </script>
        
        <?php
        // Show all buttons but disabled
        $hunt_times = [
            ['tempo' => 10, 'gold' => 120, 'display' => ($user->vip == 1 ? '5 Minutos' : '10 Minutos')],
            ['tempo' => 20, 'gold' => 240, 'display' => ($user->vip == 1 ? '10 Minutos' : '20 Minutos')],
            ['tempo' => 30, 'gold' => 360, 'display' => ($user->vip == 1 ? '15 Minutos' : '30 Minutos')],
            ['tempo' => 40, 'gold' => 480, 'display' => ($user->vip == 1 ? '20 Minutos' : '40 Minutos')],
            ['tempo' => 50, 'gold' => 600, 'display' => ($user->vip == 1 ? '25 Minutos' : '50 Minutos')],
            ['tempo' => 60, 'gold' => 720, 'display' => ($user->vip == 1 ? '30 Minutos' : '60 Minutos')]
        ];
        
        $counter = 0;
        foreach($hunt_times as $hunt){
            $counter++;
            $class = ($counter % 2 == 0) ? 'alter' : '';
        ?>
            <form class="forms <?php echo $class; ?>" style="opacity: 0.5;">
                <div class="campos" style="width: 150px;">
                    <span class="tempo-cacada" style="text-decoration: line-through;"><?php echo $hunt['display']; ?></span>
                </div>
                <div class="campos" style="width: 300px;">
                    <button type="button" class="bts-form" disabled style="background: #999; cursor: not-allowed;">Limite Esgotado</button>
                </div>
            </form>
        <?php } ?>
        
    <?php } else { ?>
        <!-- Daily limit NOT reached - Show active buttons -->
        
        <form id="formCacada" class="forms" action="" method="post">
            <div class="campos" style="width: 150px;">
                <input type="hidden" name="tempo" value="10" />
                <input type="hidden" name="gold" value="120" />
                <?php
                    if($user->vip == 1){
                        $tempo_10 = 5;
                    } else {
                        $tempo_10 = 10;
                    }
                ?>
                <span class="tempo-cacada"><?php echo $tempo_10; ?> Minutos</span>
            </div>
            <div class="campos" style="width: 300px;">
                <input type="submit" id="cacar" class="bts-form" name="cacar" value="Iniciar Caçada" />
            </div>
        </form>

        <form id="formCacada" class="forms alter" action="" method="post">
            <div class="campos" style="width: 150px;">
                <input type="hidden" name="tempo" value="20" />
                <input type="hidden" name="gold" value="240" />
                <?php 
                    if($user->vip == 1){
                        $tempo_20 = 10;
                    } else {
                        $tempo_20 = 20;
                    }
                ?>
                <span class="tempo-cacada"><?php echo $tempo_20; ?> Minutos</span>
            </div>
            <div class="campos" style="width: 300px;">
                <input type="submit" id="cacar" class="bts-form" name="cacar" value="Iniciar Caçada" />
            </div>
        </form>

        <form id="formCacada" class="forms" action="" method="post">
            <div class="campos" style="width: 150px;">
                <input type="hidden" name="tempo" value="30" />
                <input type="hidden" name="gold" value="360" />
                <?php 
                    if($user->vip == 1){
                        $tempo_30 = 15;
                    } else {
                        $tempo_30 = 30;
                    }
                ?>
                <span class="tempo-cacada"><?php echo $tempo_30; ?> Minutos</span>
            </div>
            <div class="campos" style="width: 300px;">
                <input type="submit" id="cacar" class="bts-form" name="cacar" value="Iniciar Caçada" />
            </div>
        </form>

        <form id="formCacada" class="forms alter" action="" method="post">
            <div class="campos" style="width: 150px;">
                <input type="hidden" name="tempo" value="40" />
                <input type="hidden" name="gold" value="480" />
                <?php 
                    if($user->vip == 1){
                        $tempo_40 = 20;
                    } else {
                        $tempo_40 = 40;
                    }
                ?>
                <span class="tempo-cacada"><?php echo $tempo_40; ?> Minutos</span>
            </div>
            <div class="campos" style="width: 300px;">
                <input type="submit" id="cacar" class="bts-form" name="cacar" value="Iniciar Caçada" />
            </div>
        </form>

        <form id="formCacada" class="forms" action="" method="post">
            <div class="campos" style="width: 150px;">
                <input type="hidden" name="tempo" value="50" />
                <input type="hidden" name="gold" value="600" />
                <?php 
                    if($user->vip == 1){
                        $tempo_50 = 25;
                    } else {
                        $tempo_50 = 50;
                    }
                ?>
                <span class="tempo-cacada"><?php echo $tempo_50; ?> Minutos</span>
            </div>
            <div class="campos" style="width: 300px;">
                <input type="submit" id="cacar" class="bts-form" name="cacar" value="Iniciar Caçada" />
            </div>
        </form>
        
        <form id="formCacada" class="forms alter" action="" method="post">
            <div class="campos" style="width: 150px;">
                <input type="hidden" name="tempo" value="60" />
                <input type="hidden" name="gold" value="720" />
                <?php 
                    if($user->vip == 1){
                        $tempo_60 = 30;
                    } else {
                        $tempo_60 = 60;
                    }
                ?>
                <span class="tempo-cacada"><?php echo $tempo_60; ?> Minutos</span>
            </div>
            <div class="campos" style="width: 300px;">
                <input type="submit" id="cacar" class="bts-form" name="cacar" value="Iniciar Caçada" />
            </div>
        </form>
        
    <?php } ?>
</div>