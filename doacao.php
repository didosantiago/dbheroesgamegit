<?php 
require_once 'core/CharacterRequired.php';
$pagamentos = new Pagamentos();
$core = new Core();

$user_info = $core->getDados('usuarios', "WHERE id = ".$user->id);
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Exo+2:wght@400;700;900&display=swap');

* {
    margin: 0;
    padding: 0;
    font-family: 'Exo 2', sans-serif;
}

body {
    background: #0d1117;
    color: #fff;
    overflow-x: hidden;
}

/* Fundo Digital */
.digital-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 30%, rgba(0, 150, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(0, 255, 150, 0.1) 0%, transparent 50%),
        linear-gradient(180deg, #0d1117 0%, #161b22 100%);
    z-index: -2;
}

/* Grade HUD */
.hud-grid {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(rgba(0, 150, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 150, 255, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
    z-index: -1;
}

/* Partículas de Dados */
@keyframes dataFloat {
    0% {
        transform: translateY(100vh) translateX(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100px) translateX(50px);
        opacity: 0;
    }
}

.data-particle {
    position: fixed;
    color: #0096ff;
    font-size: 12px;
    font-family: 'Courier New', monospace;
    animation: dataFloat 10s linear infinite;
    pointer-events: none;
    opacity: 0.6;
}

/* Orbes Brilhantes */
@keyframes orbFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-30px);
    }
}

.glowing-orb {
    position: fixed;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 150, 255, 0.3), transparent);
    animation: orbFloat 4s ease-in-out infinite;
    pointer-events: none;
    filter: blur(20px);
}

/* Container */
.shop-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 20px;
    position: relative;
    z-index: 1;
}

/* Cabeçalho HUD do Shop */
@keyframes digitalGlow {
    0%, 100% {
        text-shadow: 
            0 0 10px #0096ff,
            0 0 20px #0096ff,
            0 0 30px #0096ff,
            0 0 40px #00ff96;
    }
    50% {
        text-shadow: 
            0 0 20px #0096ff,
            0 0 40px #0096ff,
            0 0 60px #0096ff,
            0 0 80px #00ff96;
    }
}

.shop-hud-header {
    text-align: center;
    margin: 80px 0 60px 0;
    position: relative;
}

.shop-hud-header::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #0096ff, transparent);
    transform: translateY(-50%);
    z-index: -1;
}

.shop-hud-header h1 {
    font-size: 72px;
    font-weight: 900;
    margin: -120px 0px 0px 0px;
    position: relative;
    display: inline-block;
    padding: 0 50px;

}

.shop-hud-header .system-text {
    font-family: 'Press Start 2P', cursive;
    font-size: 14px;
    color: #00ff96;
    margin-top: 25px;
    letter-spacing: 3px;
    text-shadow: 0 0 10px #00ff96;
}

/* Saldo de Coins */
.coin-balance-hud {
    background: rgba(13, 17, 23, 0.95);
    border: 2px solid #ffd700;
    border-radius: 0;
    padding: 50px;
    margin: 50px 0;
    position: relative;
    box-shadow: 
        0 0 40px rgba(255, 215, 0, 0.3),
        inset 0 0 40px rgba(255, 215, 0, 0.05);
    clip-path: polygon(20px 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%, 0 20px);
}

.coin-balance-hud::before {
    content: '[ SALDO DE CRÉDITOS ]';
    position: absolute;
    top: 15px;
    left: 30px;
    background: #0d1117;
    padding: 5px 20px;
    font-family: 'Press Start 2P', cursive;
    font-size: 12px;
    color: #ffd700;
    letter-spacing: 2px;
}

.coin-balance-hud::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 1px solid #ffd700;
    margin: 5px;
    pointer-events: none;
}

.balance-display {
    text-align: center;
}

.balance-amount {
    font-family: 'Press Start 2P', cursive;
    font-size: 72px;
    color: #ffd700;
    text-shadow: 0 0 30px rgba(255, 215, 0, 0.8);
    margin: 20px 0;
}

.balance-label {
    font-size: 18px;
    color: #0096ff;
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* VIP Call to Action */
.vip-alert-box {
    background: linear-gradient(135deg, rgba(0, 150, 255, 0.2), rgba(0, 255, 150, 0.2));
    border: 3px solid #00ff96;
    padding: 40px;
    margin: 50px 0;
    text-align: center;
    position: relative;
    clip-path: polygon(0 0, calc(100% - 30px) 0, 100% 30px, 100% 100%, 30px 100%, 0 calc(100% - 30px));
    animation: pulse-glow 2s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 20px rgba(0, 255, 150, 0.3);
    }
    50% {
        box-shadow: 0 0 40px rgba(0, 255, 150, 0.6);
    }
}

.vip-alert-box h2 {
    font-family: 'Press Start 2P', cursive;
    font-size: 20px;
    color: #00ff96;
    margin-bottom: 20px;
    text-shadow: 0 0 15px rgba(0, 255, 150, 0.8);
}

.vip-alert-box p {
    font-size: 18px;
    color: #fff;
    margin-bottom: 25px;
}

.btn-vip-activate {
    display: inline-block;
    padding: 20px 50px;
    font-family: 'Press Start 2P', cursive;
    font-size: 16px;
    background: linear-gradient(135deg, #0096ff, #00ff96);
    color: #000;
    border: none;
    text-decoration: none;
    cursor: pointer;
    clip-path: polygon(15px 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%, 0 15px);
    transition: all 0.3s ease;
}

.btn-vip-activate:hover {
    transform: scale(1.1);
    box-shadow: 0 10px 40px rgba(0, 255, 150, 0.6);
}

/* Info Box */
.info-terminal {
    background: rgba(13, 17, 23, 0.95);
    border: 2px solid #0096ff;
    padding: 40px;
    margin: 50px 0;
    position: relative;
    box-shadow: 
        0 0 40px rgba(0, 150, 255, 0.3),
        inset 0 0 40px rgba(0, 150, 255, 0.05);
}

.info-terminal::before {
    content: '[ INFORMAÇÕES DO SISTEMA ]';
    position: absolute;
    top: -15px;
    left: 30px;
    background: #0d1117;
    padding: 5px 20px;
    font-family: 'Press Start 2P', cursive;
    font-size: 12px;
    color: #0096ff;
    letter-spacing: 2px;
}

.info-terminal h2 {
    font-family: 'Press Start 2P', cursive;
    font-size: 20px;
    color: #0096ff;
    margin-bottom: 20px;
    text-shadow: 0 0 15px rgba(0, 150, 255, 0.8);
}

.info-terminal p {
    font-size: 16px;
    line-height: 1.8;
    color: #fff;
    margin-bottom: 20px;
}

.info-terminal ul {
    list-style: none;
    padding: 0;
}

.info-terminal ul li {
    padding: 12px 0 12px 40px;
    color: #fff;
    border-bottom: 1px solid rgba(0, 150, 255, 0.2);
    position: relative;
    font-size: 16px;
}

.info-terminal ul li::before {
    content: '▶';
    position: absolute;
    left: 0;
    color: #00ff96;
    font-size: 20px;
}

.conversion-display {
    text-align: center;
    margin: 30px 0;
    padding: 30px;
    background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 165, 0, 0.1));
    border: 2px solid #ffd700;
    clip-path: polygon(20px 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%, 0 20px);
}

.conversion-display .rate {
    font-family: 'Press Start 2P', cursive;
    font-size: 32px;
    color: #ffd700;
    text-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
}

/* Título da Seção */
.section-title {
    font-family: 'Press Start 2P', cursive;
    text-align: center;
    color: #9aff3f;
    font-size: 24px;
    margin: 80px 0 50px 0;
    text-shadow: 0 0 30px rgba(0, 150, 255, 0.8);
    letter-spacing: 4px;
}

/* Cards de Planos */
.shop-plans {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
    margin: 60px 0;
}

.plan-box {
    background: linear-gradient(135deg, rgba(13, 17, 23, 0.98), rgba(22, 27, 34, 0.98));
    border: 3px solid #0096ff;
    padding: 40px;
    position: relative;
    overflow: hidden;
    transition: all 0.5s ease;
    clip-path: polygon(0 0, calc(100% - 25px) 0, 100% 25px, 100% 100%, 25px 100%, 0 calc(100% - 25px));
}

.plan-box::before {
    content: '';
    position: absolute;
    top: -100%;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, transparent, rgba(0, 150, 255, 0.3));
    transition: top 0.5s;
}

.plan-box:hover::before {
    top: 0;
}

.plan-box:hover {
    transform: translateY(-15px) scale(1.03);
    border-color: #00ff96;
    box-shadow: 
        0 25px 50px rgba(0, 150, 255, 0.5),
        0 0 80px rgba(0, 255, 150, 0.3);
}

.plan-box.premium-plan {
    border-color: #ffd700;
    box-shadow: 0 0 50px rgba(255, 215, 0, 0.4);
}

.plan-box.premium-plan::after {
    content: '★ PREMIUM ★';
    position: absolute;
    top: 25px;
    right: -35px;
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #000;
    padding: 8px 50px;
    transform: rotate(45deg);
    font-family: 'Press Start 2P', cursive;
    font-size: 9px;
    letter-spacing: 2px;
    box-shadow: 0 5px 20px rgba(255, 215, 0, 0.5);
}

.plan-header {
    text-align: center;
    margin-bottom: 25px;
    position: relative;
    z-index: 1;
}

.plan-name {
    font-family: 'Press Start 2P', cursive;
    font-size: 16px;
    color: #0096ff;
    margin-bottom: 20px;
    text-shadow: 0 0 15px rgba(0, 150, 255, 0.8);
}

.plan-price {
    font-family: 'Press Start 2P', cursive;
    font-size: 48px;
    color: #00ff96;
    text-shadow: 0 0 30px rgba(0, 255, 150, 0.8);
    margin: 15px 0;
}

.plan-coins {
    font-family: 'Press Start 2P', cursive;
    font-size: 28px;
    color: #ffd700;
    text-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
    margin: 15px 0;
}

.plan-description {
    font-size: 16px;
    color: #fff;
    margin: 20px 0;
    text-align: center;
}

.btn-purchase {
    width: 100%;
    padding: 20px;
    font-family: 'Press Start 2P', cursive;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 2px;
    border: 3px solid #0096ff;
    background: linear-gradient(135deg, rgba(0, 150, 255, 0.2), rgba(0, 255, 150, 0.2));
    color: #0096ff;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
    margin-top: 25px;
    clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);
}

.btn-purchase::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 150, 255, 0.4), transparent);
    transition: left 0.6s;
}

.btn-purchase::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #0096ff, #00ff96);
    opacity: 0;
    transition: opacity 0.4s;
    z-index: -1;
}

.btn-purchase:hover {
    color: #000;
    border-color: #00ff96;
    box-shadow: 
        0 10px 40px rgba(0, 150, 255, 0.6),
        0 0 60px rgba(0, 255, 150, 0.4);
    transform: scale(1.05);
}

.btn-purchase:hover::before {
    left: 100%;
}

.btn-purchase:hover::after {
    opacity: 1;
}

/* Métodos de Pagamento */
.payment-terminal {
    background: rgba(13, 17, 23, 0.95);
    border: 2px solid #00ff96;
    padding: 40px;
    margin: 50px 0;
    position: relative;
    box-shadow: 
        0 0 40px rgba(0, 255, 150, 0.3),
        inset 0 0 40px rgba(0, 255, 150, 0.05);
}

.payment-terminal::before {
    content: '[ MÉTODOS DE PAGAMENTO ]';
    position: absolute;
    top: -15px;
    left: 30px;
    background: #0d1117;
    padding: 5px 20px;
    font-family: 'Press Start 2P', cursive;
    font-size: 12px;
    color: #00ff96;
    letter-spacing: 2px;
}

.payment-terminal h2 {
    font-family: 'Press Start 2P', cursive;
    font-size: 20px;
    color: #00ff96;
    margin-bottom: 25px;
    text-shadow: 0 0 15px rgba(0, 255, 150, 0.8);
}

.payment-terminal ul {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.payment-terminal ul li {
    padding: 15px 20px 15px 50px;
    color: #fff;
    background: linear-gradient(135deg, rgba(0, 255, 150, 0.1), rgba(0, 150, 255, 0.1));
    border: 1px solid rgba(0, 255, 150, 0.3);
    position: relative;
    font-size: 16px;
    clip-path: polygon(8px 0, 100% 0, 100% calc(100% - 8px), calc(100% - 8px) 100%, 0 100%, 0 8px);
}

.payment-terminal ul li::before {
    content: '✓';
    position: absolute;
    left: 15px;
    color: #00ff96;
    font-size: 24px;
    font-weight: bold;
}

/* Responsivo */
@media (max-width: 768px) {
    .shop-hud-header h1 {
        font-size: 36px;
        padding: 0 20px;
    }
    
    .shop-plans {
        grid-template-columns: 1fr;
    }
    
    .balance-amount {
        font-size: 48px;
    }
}
</style>

<div class="digital-bg"></div>
<div class="hud-grid"></div>

<div class="shop-container">
    <!-- Header -->
    <div class="shop-hud-header">
        <h1>💰 LOJA DE CRÉDITOS</h1>
        <p class="system-text">&lt; SISTEMA DE PAGAMENTO ATIVO &gt;</p>
    </div>

    <!-- Saldo Atual -->
    <div class="coin-balance-hud">
        <div class="balance-display">
            <div class="balance-label">💎 SEU SALDO ATUAL</div>
            <div class="balance-amount"><?php echo number_format($user_info->coins, 0, ',', '.'); ?></div>
            <div class="balance-label">CRÉDITOS DISPONÍVEIS</div>
        </div>
    </div>

    <!-- VIP Alert -->
    <?php if($user_info->coins >= 10){ ?>
    <div class="vip-alert-box">
        <h2>⚡ ALERTA DO SISTEMA ⚡</h2>
        <p>Você possui créditos suficientes para ativar benefícios VIP!</p>
        <p>Use seus créditos para desbloquear vantagens exclusivas agora!</p>
        <a href="<?php echo BASE; ?>ativar-vip" class="btn-vip-activate">
            &lt; ATIVAR VIP &gt;
        </a>
    </div>
    <?php } ?>

    <!-- Info Terminal -->
    <div class="info-terminal">
        <h2>📋 COMO FUNCIONA</h2>
        <p>
            O DB Heroes RPG é um jogo <strong>gratuito e sem fins lucrativos</strong>. 
            Sua contribuição ajuda na manutenção e melhoria do servidor. 
            Ao fazer uma doação, você recebe <strong>créditos</strong> que podem ser usados para:
        </p>
        <ul>
            <li><strong>Ativar VIP</strong> - Use créditos para ativar benefícios exclusivos</li>
            <li><strong>Comprar itens premium</strong> na loja do jogo</li>
            <li><strong>Acelerar progresso</strong> com recursos especiais</li>
            <li><strong>Participar de eventos exclusivos</strong></li>
        </ul>
        
        <div class="conversion-display">
            <p style="color: #00ff96; margin-bottom: 15px; font-size: 16px;">TAXA DE CONVERSÃO:</p>
            <div class="rate">R$ 1,00 = 1 CRÉDITO 💰</div>
        </div>
    </div>

    <!-- Planos -->
    <h2 class="section-title">&lt; ESCOLHA SEU PACOTE &gt;</h2>

    <div class="shop-plans">
        <!-- Plano 10 -->
        <div class="plan-box">
            <div class="plan-header">
                <div class="plan-name">PACOTE INICIANTE</div>
                <div class="plan-price">R$ 10</div>
                <div class="plan-coins">10 CRÉDITOS 💰</div>
                <p class="plan-description">Perfeito para ativar VIP Mensal!</p>
            </div>
            
            <form method="POST" action="<?php echo BASE; ?>processaPagamento">
                <input type="hidden" name="valor" value="10">
                <input type="hidden" name="coins" value="10">
                <button type="submit" class="btn-purchase">
                    &lt; COMPRAR AGORA &gt;
                </button>
            </form>
        </div>

        <!-- Plano 25 -->
        <div class="plan-box">
            <div class="plan-header">
                <div class="plan-name">PACOTE BÁSICO</div>
                <div class="plan-price">R$ 25</div>
                <div class="plan-coins">25 CRÉDITOS 💰</div>
                <p class="plan-description">Ideal para VIP Trimestral!</p>
            </div>
            
            <form method="POST" action="<?php echo BASE; ?>processaPagamento">
                <input type="hidden" name="valor" value="25">
                <input type="hidden" name="coins" value="25">
                <button type="submit" class="btn-purchase">
                    &lt; COMPRAR AGORA &gt;
                </button>
            </form>
        </div>

        <!-- Plano 50 -->
        <div class="plan-box">
            <div class="plan-header">
                <div class="plan-name">PACOTE AVANÇADO</div>
                <div class="plan-price">R$ 50</div>
                <div class="plan-coins">50 CRÉDITOS 💰</div>
                <p class="plan-description">Ótimo para VIP Semestral + extras!</p>
            </div>
            
            <form method="POST" action="<?php echo BASE; ?>processaPagamento">
                <input type="hidden" name="valor" value="50">
                <input type="hidden" name="coins" value="50">
                <button type="submit" class="btn-purchase">
                    &lt; COMPRAR AGORA &gt;
                </button>
            </form>
        </div>

        <!-- Plano 100 (Premium) -->
        <div class="plan-box premium-plan">
            <div class="plan-header">
                <div class="plan-name">PACOTE PREMIUM</div>
                <div class="plan-price" style="color: #ffd700; text-shadow: 0 0 30px rgba(255, 215, 0, 0.8);">R$ 100</div>
                <div class="plan-coins">100 CRÉDITOS 💰</div>
                <p class="plan-description">VIP Anual + muito mais!</p>
            </div>
            
            <form method="POST" action="<?php echo BASE; ?>processaPagamento">
                <input type="hidden" name="valor" value="100">
                <input type="hidden" name="coins" value="100">
                <button type="submit" class="btn-purchase" style="border-color: #ffd700; color: #ffd700;">
                    &lt; COMPRAR PREMIUM &gt;
                </button>
            </form>
        </div>
    </div>

    <!-- Métodos de Pagamento -->
    <div class="payment-terminal">
        <h2>💳 PAGSEGURO - PAGAMENTO SEGURO</h2>
        <ul>
            <li>Cartão de Crédito (todas as bandeiras)</li>
            <li>Cartão de Débito</li>
            <li>Transferência Bancária</li>
            <li>Boleto Bancário</li>
            <li>PIX (instantâneo)</li>
        </ul>
    </div>

    <!-- CTA Final -->
    <div class="vip-alert-box" style="border-color: #0096ff;">
        <h2 style="color: #0096ff; text-shadow: 0 0 15px rgba(0, 150, 255, 0.8);">
            ⭐ APÓS COMPRAR, ATIVE SEU VIP! ⭐
        </h2>
        <p>
            Depois de receber seus créditos, vá para a página de ativação VIP e escolha o plano ideal!
        </p>
        <a href="<?php echo BASE; ?>ativar-vip" class="btn-vip-activate" style="background: linear-gradient(135deg, #0096ff, #00ff96);">
            &lt; VER PLANOS VIP &gt;
        </a>
    </div>
</div>

<script>
// Criar partículas de dados
const dataSymbols = ['0', '1', '01', '10', '001', '101', '▲', '►', '◆'];
for (let i = 0; i < 30; i++) {
    const particle = document.createElement('div');
    particle.className = 'data-particle';
    particle.textContent = dataSymbols[Math.floor(Math.random() * dataSymbols.length)];
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 10 + 's';
    particle.style.animationDuration = (Math.random() * 5 + 8) + 's';
    document.body.appendChild(particle);
}

// Criar orbes brilhantes
for (let i = 0; i < 5; i++) {
    const orb = document.createElement('div');
    orb.className = 'glowing-orb';
    orb.style.left = (Math.random() * 80 + 10) + '%';
    orb.style.top = (Math.random() * 80 + 10) + '%';
    orb.style.animationDelay = Math.random() * 4 + 's';
    document.body.appendChild(orb);
}
</script>
