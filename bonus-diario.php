<?php
$core = new Core();
$inventario = new Inventario();

$diasemana = $core->getDiaSemana();

if(isset($_POST['coletar'])){
    $dados = $core->getDados('adm_recompensas', 'WHERE id = '.intval($_POST['id']));
    
    // Check if player already collected today's bonus
    $hoje = date('Y-m-d');
    $sql_check = "SELECT * FROM personagens_recompensas 
                  WHERE idPersonagem = ".$_SESSION['PERSONAGEMID']." 
                  AND data = '$hoje' 
                  AND idRecompensa = ".intval($_POST['id']);
    $stmt = DB::prepare($sql_check);
    $stmt->execute();
    
    if($stmt->rowCount() == 0){
        // Player hasn't collected this bonus today
        $campos = array(
            'idPersonagem' => $_SESSION['PERSONAGEMID'],
            'idRecompensa' => intval($_POST['id']),
            'diasemana' => $diasemana,
            'data' => $hoje
        );
        $core->insert('personagens_recompensas', $campos);
        
        if($dados->premio == 'gold'){
            $campos = array(
                'gold' => intval($personagem->gold) + intval($dados->valor),
                'gold_total' => intval($personagem->gold_total) + intval($dados->valor)
            );
            $where = 'id="'.$_SESSION['PERSONAGEMID'].'"';
            
            if($core->update('usuarios_personagens', $campos, $where)){
                $_SESSION['bonus_collected'] = true;
                $_SESSION['bonus_reward'] = $dados->valor.' Golds';
                header('Location: '.BASE.'bonus-diario');
                exit;
            }
        } else if($dados->premio == 'item'){
            $dadosItem = $core->getDados('itens', 'WHERE id = '.$dados->valor);
            
            if($inventario->verificaItemIgual($dadosItem->nome, $_SESSION['PERSONAGEMID'])){
                $slot_recebido = $inventario->verificaItemIgual($dadosItem->nome, $_SESSION['PERSONAGEMID']);
                
                $campos = array('novo' => 1);
                $where = 'id = "'.$slot_recebido.'"';
                $core->update('personagens_inventario', $campos, $where);
                
                $campos_add = array(
                    'idItem' => $dadosItem->id,
                    'idSlot' => $slot_recebido,
                    'idPersonagem' => $_SESSION['PERSONAGEMID']
                );
                $core->insert('personagens_inventario_itens', $campos_add);
                
                $_SESSION['bonus_collected'] = true;
                $_SESSION['bonus_reward'] = $dadosItem->nome;
                header('Location: '.BASE.'bonus-diario');
                exit;
            }
        }
    }
}

// Check if bonus was just collected
$successMessage = false;
$rewardText = '';
if(isset($_SESSION['bonus_collected']) && $_SESSION['bonus_collected'] === true){
    $successMessage = true;
    $rewardText = $_SESSION['bonus_reward'];
    unset($_SESSION['bonus_collected']);
    unset($_SESSION['bonus_reward']);
}

// Calculate time until next reward (midnight)
$now = new DateTime();
$tomorrow = new DateTime('tomorrow');
$interval = $now->diff($tomorrow);
$hoursLeft = $interval->h;
$minutesLeft = $interval->i;
$secondsLeft = $interval->s;
?>

<!-- SUCCESS POPUP -->
<?php if($successMessage): ?>
<div id="notification-overlay" class="notification-overlay">
    <div class="notification-box success">
        <div class="notification-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="notification-content">
            <h3><i class="fas fa-gift"></i> RECOMPENSA COLETADA!</h3>
            <p>Você coletou sua recompensa diária com sucesso!</p>
            <p class="reward-amount"><?php echo $rewardText; ?></p>
        </div>
        <button class="notification-close" type="button" aria-label="Fechar">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
function closeNotification() {
    const overlay = document.getElementById('notification-overlay');
    if(overlay) {
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s ease';
        setTimeout(function() {
            overlay.remove();
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('notification-overlay');
    if(overlay) {
        setTimeout(closeNotification, 3000);
        overlay.addEventListener('click', function(e) {
            if(e.target === this) closeNotification();
        });
        const closeBtn = overlay.querySelector('.notification-close');
        if(closeBtn) closeBtn.addEventListener('click', closeNotification);
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') closeNotification();
        });
    }
});
</script>
<?php endif; ?>

<div class="bonus-diario-banner"></div>
<h2 class="title">Adquira seu Bônus do Dia</h2>


<!-- COUNTDOWN TIMER -->
<div class="bonus-timer">
    <i class="fas fa-clock"></i>
    <span>Próximo bônus em: </span>
    <span id="countdown" data-hours="<?php echo $hoursLeft; ?>" data-minutes="<?php echo $minutesLeft; ?>" data-seconds="<?php echo $secondsLeft; ?>">
        <?php echo sprintf('%02d:%02d:%02d', $hoursLeft, $minutesLeft, $secondsLeft); ?>
    </span>
</div>

<ul class="dias-lista">
    <?php
        $treino = new Treino();
        echo $treino->getListBonus($diasemana, $_SESSION['PERSONAGEMID']); 
    ?> 
</ul>

<!-- COUNTDOWN TIMER SCRIPT -->
<script>
function updateCountdown() {
    const countdownElement = document.getElementById('countdown');
    if(!countdownElement) return;
    
    let hours = parseInt(countdownElement.dataset.hours);
    let minutes = parseInt(countdownElement.dataset.minutes);
    let seconds = parseInt(countdownElement.dataset.seconds);
    
    // Decrease seconds
    seconds--;
    
    if(seconds < 0) {
        seconds = 59;
        minutes--;
    }
    
    if(minutes < 0) {
        minutes = 59;
        hours--;
    }
    
    if(hours < 0) {
        // Refresh page when countdown hits 0
        location.reload();
        return;
    }
    
    // Update dataset
    countdownElement.dataset.hours = hours;
    countdownElement.dataset.minutes = minutes;
    countdownElement.dataset.seconds = seconds;
    
    // Update display
    countdownElement.textContent = 
        String(hours).padStart(2, '0') + ':' + 
        String(minutes).padStart(2, '0') + ':' + 
        String(seconds).padStart(2, '0');
}

// Update every second
setInterval(updateCountdown, 1000);

// 2. AUTO SCROLL SCRIPT
document.addEventListener("DOMContentLoaded", function() {
    // == CHANGE THIS VALUE TO SCROLL MORE OR LESS ==
    const pixelsToScroll = 350; 
    // ==============================================

    // Scroll the window down
    window.scrollTo({
        top: pixelsToScroll,
        behavior: "smooth"
    });
});
</script>

<style>
.bonus-timer {
    text-align: center;
    margin: 20px auto;
    padding: 15px 30px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border: 2px solid #0f3460;
    border-radius: 10px;
    max-width: 400px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.bonus-timer i {
    color: #4CAF50;
    margin-right: 10px;
    font-size: 1.2em;
}

.bonus-timer span {
    color: #ffffff;
    font-size: 1.1em;
    font-weight: 500;
}

.bonus-timer #countdown {
    color: #4CAF50;
    font-weight: 700;
    font-size: 1.3em;
    font-family: 'Courier New', monospace;
    margin-left: 5px;
}

/* --- MODERN DAILY BONUS STYLE (Targets existing HTML) --- */

/* 1. Grid Layout for the List */
ul.dias-lista {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    padding: 20px;
    list-style: none;
    margin: 0 auto;
    max-width: 1100px;
}

/* 2. Card Style (The 'li' elements) */
ul.dias-lista li {
    background: linear-gradient(145deg, #1a1c24 0%, #121318 100%);
    border: 1px solid #2a2d35;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 280px; /* Uniform height */
}

/* Hover Effect: Lift Up */
ul.dias-lista li:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 15px 30px rgba(0,0,0,0.5), 0 0 15px rgba(0, 200, 255, 0.1);
    border-color: #4a90e2;
    z-index: 10;
}

/* 3. Day Title (h3 or strong inside li) */
ul.dias-lista li h3, 
ul.dias-lista li strong {
    color: #4a90e2;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 2px;
    margin-bottom: 15px;
    display: block;
    text-shadow: 0 0 10px rgba(74, 144, 226, 0.3);
}

/* 4. Images */
ul.dias-lista li img {
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin: 10px auto;
    filter: drop-shadow(0 5px 10px rgba(0,0,0,0.5));
    transition: transform 0.5s ease;
}

ul.dias-lista li:hover img {
    transform: scale(1.15) rotate(5deg);
    filter: drop-shadow(0 8px 15px rgba(74, 144, 226, 0.4));
}

/* 5. Reward Text (p tag) */
ul.dias-lista li p {
    color: #aaa;
    font-size: 13px;
    margin: 10px 0;
    min-height: 40px; /* align buttons */
    display: flex;
    align-items: center;
    justify-content: center;
}

/* 6. Buttons (Forms and Inputs) */
ul.dias-lista li form {
    width: 100%;
    margin-top: auto;
}

ul.dias-lista li button, 
ul.dias-lista li input[type="submit"],
ul.dias-lista li .btn {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    background: #333;
    color: #fff;
}

/* === THEME 2: DIVINE GOLD === */
body#bonus-diario { background: radial-gradient(circle at center, #2b2000, #000); }

ul.dias-lista {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 20px; list-style: none; max-width: 1100px; margin: 0 auto;
}

ul.dias-lista li {
    background: linear-gradient(180deg, #1a1a1a 0%, #000 100%);
    border: 1px solid #665500;
    border-top: 3px solid #ffd700; /* Golden Top Bar */
    border-radius: 4px;
    padding: 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: 0.4s;
}

/* Shine Effect on Hover */
ul.dias-lista li::before {
    content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.2), transparent);
    transition: 0.5s;
}
ul.dias-lista li:hover::before { left: 100%; }

ul.dias-lista li:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
    border-color: #ffd700;
}

/* Titles */
ul.dias-lista li h3 {
    background: -webkit-linear-gradient(#ffd700, #bf953f);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
}

/* Available Button */
ul.dias-lista li button:not(.indisponivel):not(.coletado) {
    background: linear-gradient(to bottom, #ffd700, #b8860b);
    color: #000; font-weight: bold; border: 1px solid #fff;
    box-shadow: 0 0 15px #b8860b;
}


</style>
