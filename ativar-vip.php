<?php 
require_once 'core/CharacterRequired.php';

$core = new Core();
$pagamentos = new Pagamentos();
$user_info = $core->getDados('usuarios', "WHERE id = ".$user->id);

// ========== VERIFICAR SE EXISTE MENSAGEM DE SUCESSO NA SESSÃO ==========
$success_message = null;
if(isset($_SESSION['vip_success_message'])){
    $success_message = $_SESSION['vip_success_message'];
    unset($_SESSION['vip_success_message']); // Limpar mensagem após exibir
}

// Lógica de ativação VIP
if(isset($_POST['ativar_vip'])){
    $plano = (int)$_POST['plano'];
    $planos = array(
        1 => array('nome' => 'VIP Mensal', 'coins' => 10, 'dias' => 30, 'economia' => 0),
        2 => array('nome' => 'VIP Trimestral', 'coins' => 25, 'dias' => 90, 'economia' => 5),
        3 => array('nome' => 'VIP Semestral', 'coins' => 45, 'dias' => 180, 'economia' => 15),
        4 => array('nome' => 'VIP Anual', 'coins' => 80, 'dias' => 365, 'economia' => 40)
    );
    
    if(isset($planos[$plano])){
        $plano_selecionado = $planos[$plano];
        $coins_necessarios = $plano_selecionado['coins'];
        
        // ========== VERIFICAR SE PRECISA DE CONFIRMAÇÃO (SEMPRE) ==========
        if(!isset($_POST['confirmar_compra'])){
            
            // CASO 1: JÁ É VIP - Mostra confirmação de EXTENSÃO
            if($user_info->vip == 1 && !empty($user_info->vip_data_expiracao)){
                $dias_restantes = ceil((strtotime($user_info->vip_data_expiracao) - time()) / (60 * 60 * 24));
                $confirm_message = '⚠️ VIP JÁ ATIVO! ⚠️<br><br>'.
                                 '<div style="text-align: left; display: inline-block;">'.
                                 '✅ <strong>Status Atual:</strong> VIP Ativo<br>'.
                                 '📅 <strong>Expira em:</strong> '.date('d/m/Y', strtotime($user_info->vip_data_expiracao)).'<br>'.
                                 '⏰ <strong>Dias Restantes:</strong> '.$dias_restantes.' dias<br><br>'.
                                 '🔄 <strong>Nova Validade:</strong> '.date('d/m/Y', strtotime("+".$plano_selecionado['dias']." days", strtotime($user_info->vip_data_expiracao))).'<br>'.
                                 '💰 <strong>Custo:</strong> '.$coins_necessarios.' coins'.
                                 '</div><br><br>'.
                                 '<strong style="color: #ffd700; font-size: 18px;">Deseja estender seu VIP?</strong>';
            } 
            // CASO 2: NÃO É VIP - Mostra confirmação de ATIVAÇÃO
            else {
                $nova_expiracao_prevista = date('d/m/Y', strtotime("+".$plano_selecionado['dias']." days"));
                $confirm_message = '🎮 CONFIRMAR ATIVAÇÃO VIP 🎮<br><br>'.
                                 '<div style="text-align: left; display: inline-block;">'.
                                 '📦 <strong>Plano:</strong> '.$plano_selecionado['nome'].'<br>'.
                                 '⏱️ <strong>Duração:</strong> '.$plano_selecionado['dias'].' dias<br>'.
                                 '📅 <strong>Válido até:</strong> '.$nova_expiracao_prevista.'<br>'.
                                 '💰 <strong>Custo:</strong> '.$coins_necessarios.' coins<br>'.
                                 '💳 <strong>Saldo Atual:</strong> '.$user_info->coins.' coins'.
                                 '</div><br><br>'.
                                 '<strong style="color: #00ff96; font-size: 18px;">Deseja adquirir o VIP?</strong>';
            }
            
            $show_confirm = true;
            $confirm_plano = $plano;
            
        } 
        // ========== PROCESSAR COMPRA (APÓS CONFIRMAÇÃO) ==========
        else if($user_info->coins >= $coins_necessarios){
            
            // Calcular data de expiração
            if($user_info->vip == 1 && !empty($user_info->vip_data_expiracao)){
                $data_atual_vip = strtotime($user_info->vip_data_expiracao);
                if($data_atual_vip < time()){
                    $nova_expiracao = date('Y-m-d', strtotime("+".$plano_selecionado['dias']." days"));
                } else {
                    $nova_expiracao = date('Y-m-d', strtotime("+".$plano_selecionado['dias']." days", $data_atual_vip));
                }
            } else {
                $nova_expiracao = date('Y-m-d', strtotime("+".$plano_selecionado['dias']." days"));
            }
            
            // Atualizar status VIP
            $campos = array(
                'vip' => 1,
                'coins' => $user_info->coins - $coins_necessarios,
                'vip_data_expiracao' => $nova_expiracao
            );
            
            $where = 'id = '.$user->id;
            $core->update('usuarios', $campos, $where);
            
            // Registrar transação
            try {
                $sql = "INSERT INTO transacoes 
                        (idUsuario, idPersonagem, data, status, valor, coins, visualizado, pre_order, transaction_id) 
                        VALUES 
                        (".$user->id.", ".$_SESSION['PERSONAGEMID'].", NOW(), 3, 0.00, ".$coins_necessarios.", 1, 0, 'COIN-VIP-ACTIVATION')";
                $stmt = DB::prepare($sql);
                $stmt->execute();
            } catch(Exception $e) {
                error_log("Erro ao registrar transação: " . $e->getMessage());
            }
            
            // Registrar log
            try {
                $personagem->setLog(
                    $user->id, 
                    $_SESSION['PERSONAGEMID'], 
                    0, 
                    'Ativação VIP com '.$coins_necessarios.' coins - '.$plano_selecionado['nome'],
                    ''
                );
            } catch(Exception $e) {
                error_log("Erro ao registrar log: " . $e->getMessage());
            }
            
            // ========== SALVAR MENSAGEM NA SESSÃO E REDIRECIONAR ==========
            $_SESSION['vip_success_message'] = '🎉 VIP ATIVADO COM SUCESSO! 🎉<br><br>'.
                               '<div style="text-align: left; display: inline-block;">'.
                               '✅ <strong>Plano:</strong> '.$plano_selecionado['nome'].'<br>'.
                               '✅ <strong>Créditos Usados:</strong> '.$coins_necessarios.' coins<br>'.
                               '✅ <strong>Expira em:</strong> '.date('d/m/Y', strtotime($nova_expiracao)).'<br>'.
                               '✅ <strong>Saldo Restante:</strong> '.($user_info->coins - $coins_necessarios).' coins'.
                               '</div>';
            
            // REDIRECIONAR (PRG Pattern - evita POST resubmission)
            header('Location: '.BASE.'ativar-vip');
            exit;
            
        } else {
            // Mensagem de ERRO - Créditos Insuficientes
            $faltam = $coins_necessarios - $user_info->coins;
            $error_message = '❌ CRÉDITOS INSUFICIENTES! ❌<br><br>'.
                           '<div style="text-align: left; display: inline-block;">'.
                           '💰 <strong>Você precisa de:</strong> '.$coins_necessarios.' coins<br>'.
                           '💳 <strong>Você tem:</strong> '.$user_info->coins.' coins<br>'.
                           '⚠️ <strong>Faltam:</strong> '.$faltam.' coins'.
                           '</div><br><br>'.
                           '<a href="'.BASE.'doacao" style="color: #00ff96; text-decoration: underline; font-weight: bold; font-size: 16px;">'.
                           '🛒 COMPRAR CRÉDITOS AGORA</a>';
        }
    } else {
        $error_message = '❌ PLANO INVÁLIDO!<br><br>Por favor, selecione um plano válido.';
    }
}

// Recarregar user_info após possível compra
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

/* Partículas de Dados Flutuantes */
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
.sao-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 20px;
    position: relative;
    z-index: 1;
}

/* Cabeçalho HUD do Jogo */
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

.game-hud-header {
    text-align: center;
    margin: 80px 0 60px 0;
    position: relative;
}

.game-hud-header::before {
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

.game-hud-header h1 {
    font-size: 72px;
    font-weight: 900;
    margin: -120px 0px 0px 0px;
    position: relative;
    display: inline-block;
    padding: 0 50px;
}

.game-hud-header .system-text {
    font-family: 'Press Start 2P', cursive;
    font-size: 14px;
    color: #00ff96;
    margin-top: 25px;
    letter-spacing: 3px;
    text-shadow: 0 0 10px #00ff96;
}

/* HUD de Status do Jogador */
.player-stats-hud {
    background: rgba(13, 17, 23, 0.95);
    border: 2px solid #0096ff;
    border-radius: 0;
    padding: 50px;
    margin: 50px 0;
    position: relative;
    box-shadow: 
        0 0 40px rgba(0, 150, 255, 0.3),
        inset 0 0 40px rgba(0, 150, 255, 0.05);
}

.player-stats-hud::before {
    content: '[ STATUS DO JOGADOR ]';
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

.player-stats-hud::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 1px solid #00ff96;
    margin: 5px;
    pointer-events: none;
}

.stats-display-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
}

.stat-box-digital {
    background: linear-gradient(135deg, rgba(0, 150, 255, 0.1), rgba(0, 255, 150, 0.1));
    border: 2px solid #0096ff;
    padding: 30px;
    text-align: center;
    position: relative;
    clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);
    transition: all 0.3s ease;
}

.stat-box-digital::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #0096ff, #00ff96);
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s;
    clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);
}

.stat-box-digital:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 150, 255, 0.5);
}

.stat-box-digital:hover::before {
    opacity: 1;
}

.stat-value-digital {
    font-family: 'Press Start 2P', cursive;
    font-size: 36px;
    color: #00ff96;
    text-shadow: 0 0 20px #00ff96;
    margin-bottom: 15px;
}

.stat-label-digital {
    font-size: 14px;
    color: #0096ff;
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* Cards de Nível de Guilda */
.guild-tiers {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 40px;
    margin: 80px 0;
}

.tier-card {
    background: linear-gradient(135deg, rgba(13, 17, 23, 0.98), rgba(22, 27, 34, 0.98));
    border: 3px solid #0096ff;
    padding: 45px;
    position: relative;
    overflow: hidden;
    transition: all 0.5s ease;
    clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 20px 100%, 0 calc(100% - 20px));
}

.tier-card::before {
    content: '';
    position: absolute;
    top: -100%;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, transparent, rgba(0, 150, 255, 0.3));
    transition: top 0.5s;
}

.tier-card:hover::before {
    top: 0;
}

.tier-card:hover {
    transform: translateY(-20px) scale(1.02);
    border-color: #00ff96;
    box-shadow: 
        0 30px 60px rgba(0, 150, 255, 0.5),
        0 0 80px rgba(0, 255, 150, 0.3);
}

.tier-card.legendary-tier {
    border-color: #ffd700;
    box-shadow: 0 0 50px rgba(255, 215, 0, 0.4);
}

.tier-card.legendary-tier::after {
    content: '★ LENDÁRIO ★';
    position: absolute;
    top: 25px;
    right: -40px;
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #000;
    padding: 10px 60px;
    transform: rotate(45deg);
    font-family: 'Press Start 2P', cursive;
    font-size: 10px;
    letter-spacing: 2px;
    box-shadow: 0 5px 20px rgba(255, 215, 0, 0.5);
}

/* Emblema de Guilda */
.guild-badge {
    text-align: center;
    margin-bottom: 30px;
    position: relative;
    z-index: 1;
}

.badge-icon {
    width: 90px;
    height: 90px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #0096ff, #00ff96);
    clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    box-shadow: 0 0 40px rgba(0, 150, 255, 0.8);
    animation: pulse 2s ease-in-out infinite;
    position: relative;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1) rotate(0deg);
    }
    50% {
        transform: scale(1.1) rotate(180deg);
    }
}

.badge-icon::after {
    content: '';
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    background: linear-gradient(135deg, #0096ff, #00ff96);
    clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
    z-index: -1;
    opacity: 0.5;
    animation: pulse 2s ease-in-out infinite reverse;
}

.guild-name {
    font-family: 'Press Start 2P', cursive;
    font-size: 20px;
    color: #0096ff;
    margin-bottom: 10px;
    text-shadow: 0 0 15px rgba(0, 150, 255, 0.8);
}

.guild-rank {
    font-size: 16px;
    color: #00ff96;
    letter-spacing: 2px;
}

/* Exibição de Preço */
.tier-price {
    text-align: center;
    margin: 30px 0;
    position: relative;
    z-index: 1;
}

.price-digital {
    font-family: 'Press Start 2P', cursive;
    font-size: 48px;
    color: #00ff96;
    text-shadow: 0 0 30px rgba(0, 255, 150, 0.8);
}

.price-type {
    font-size: 18px;
    color: #0096ff;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-top: 10px;
}

.tier-duration {
    text-align: center;
    color: #00ff96;
    font-size: 18px;
    margin-bottom: 25px;
    letter-spacing: 2px;
    position: relative;
    z-index: 1;
}

/* Painel de Bônus */
.bonus-panel {
    background: linear-gradient(135deg, rgba(0, 255, 150, 0.1), rgba(255, 215, 0, 0.1));
    border: 2px solid #00ff96;
    padding: 20px;
    margin: 25px 0;
    text-align: center;
    position: relative;
    z-index: 1;
}

.bonus-panel .amount {
    font-family: 'Press Start 2P', cursive;
    font-size: 20px;
    color: #ffd700;
    text-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
}

.bonus-panel .description {
    font-size: 14px;
    color: #00ff96;
    margin-top: 10px;
    letter-spacing: 2px;
}

/* Lista de Habilidades */
.skills-list {
    list-style: none;
    padding: 0;
    margin: 30px 0;
    position: relative;
    z-index: 1;
}

.skills-list li {
    padding: 15px 0 15px 40px;
    color: #fff;
    border-bottom: 2px solid rgba(0, 150, 255, 0.2);
    position: relative;
    font-size: 16px;
}

.skills-list li::before {
    content: '▶';
    position: absolute;
    left: 0;
    color: #0096ff;
    font-size: 20px;
}

/* Botão de Aceitar */
.btn-accept {
    width: 100%;
    padding: 25px;
    font-family: 'Press Start 2P', cursive;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 3px;
    border: 3px solid #0096ff;
    background: linear-gradient(135deg, rgba(0, 150, 255, 0.2), rgba(0, 255, 150, 0.2));
    color: #0096ff;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
    margin-top: 30px;
    clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);
}

.btn-accept::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 150, 255, 0.4), transparent);
    transition: left 0.6s;
}

.btn-accept::after {
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

.btn-accept:hover {
    color: #000;
    border-color: #00ff96;
    box-shadow: 
        0 10px 40px rgba(0, 150, 255, 0.6),
        0 0 60px rgba(0, 255, 150, 0.4);
    transform: scale(1.02);
}

.btn-accept:hover::before {
    left: 100%;
}

.btn-accept:hover::after {
    opacity: 1;
}

.btn-accept:disabled {
    border-color: #555;
    color: #555;
    background: rgba(0, 0, 0, 0.5);
    cursor: not-allowed;
}

.btn-accept:disabled:hover {
    transform: none;
    box-shadow: none;
}

/* ========== POP-UP ALERT STYLES ========== */
.alert-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

.alert-box {
    background: linear-gradient(135deg, rgba(13, 17, 23, 0.98), rgba(22, 27, 34, 0.98));
    border: 3px solid;
    padding: 50px;
    max-width: 650px;
    width: 90%;
    position: relative;
    clip-path: polygon(0 0, calc(100% - 30px) 0, 100% 30px, 100% 100%, 30px 100%, 0 calc(100% - 30px));
    animation: slideDown 0.5s ease;
}

@keyframes slideDown {
    from {
        transform: translateY(-100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.alert-box.success {
    border-color: #00ff96;
    box-shadow: 
        0 0 60px rgba(0, 255, 150, 0.6),
        inset 0 0 60px rgba(0, 255, 150, 0.1);
}

.alert-box.error {
    border-color: #ff6b6b;
    box-shadow: 
        0 0 60px rgba(255, 107, 107, 0.6),
        inset 0 0 60px rgba(255, 107, 107, 0.1);
}

.alert-box.warning {
    border-color: #ffd700;
    box-shadow: 
        0 0 60px rgba(255, 215, 0, 0.6),
        inset 0 0 60px rgba(255, 215, 0, 0.1);
}

.alert-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 1px solid;
    margin: 5px;
    pointer-events: none;
}

.alert-box.success::before {
    border-color: #00ff96;
}

.alert-box.error::before {
    border-color: #ff6b6b;
}

.alert-box.warning::before {
    border-color: #ffd700;
}

.alert-content {
    text-align: center;
    color: #fff;
    position: relative;
    z-index: 1;
}

.alert-icon {
    font-size: 80px;
    margin-bottom: 25px;
    animation: bounce 1s ease infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

.alert-title {
    font-family: 'Press Start 2P', cursive;
    font-size: 20px;
    margin-bottom: 25px;
    text-shadow: 0 0 20px currentColor;
    letter-spacing: 2px;
}

.alert-box.success .alert-title {
    color: #00ff96;
}

.alert-box.error .alert-title {
    color: #ff6b6b;
}

.alert-box.warning .alert-title {
    color: #ffd700;
}

.alert-message {
    font-size: 16px;
    line-height: 2;
    margin-bottom: 35px;
}

.alert-message strong {
    color: #0096ff;
}

.alert-box.success .alert-message strong {
    color: #00ff96;
}

.alert-box.error .alert-message strong {
    color: #ff6b6b;
}

.alert-box.warning .alert-message strong {
    color: #ffd700;
}

.alert-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.btn-close-alert, .btn-confirm-yes, .btn-confirm-no {
    padding: 20px 50px;
    font-family: 'Press Start 2P', cursive;
    font-size: 14px;
    border: none;
    cursor: pointer;
    clip-path: polygon(15px 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%, 0 15px);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.btn-close-alert {
    background: linear-gradient(135deg, #0096ff, #00ff96);
    color: #000;
}

.btn-confirm-yes {
    background: linear-gradient(135deg, #00ff96, #0096ff);
    color: #000;
}

.btn-confirm-no {
    background: linear-gradient(135deg, #ff6b6b, #ff8787);
    color: #fff;
}

.btn-close-alert:hover, .btn-confirm-yes:hover, .btn-confirm-no:hover {
    transform: scale(1.1);
    box-shadow: 0 10px 40px rgba(0, 255, 150, 0.6);
}

/* Responsivo */
@media (max-width: 768px) {
    .game-hud-header h1 {
        font-size: 36px;
        padding: 0 20px;
    }
    
    .guild-tiers {
        grid-template-columns: 1fr;
    }
    
    .alert-box {
        padding: 30px;
        max-width: 95%;
    }
    
    .alert-title {
        font-size: 16px;
    }
    
    .alert-message {
        font-size: 14px;
    }
    
    .alert-buttons {
        flex-direction: column;
    }
    
    .btn-confirm-yes, .btn-confirm-no {
        width: 100%;
    }
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
</style>

<!-- POP-UP DE CONFIRMAÇÃO -->
<?php if(isset($show_confirm) && $show_confirm){ ?>
<div class="alert-overlay" id="confirmAlert">
    <div class="alert-box warning">
        <div class="alert-content">
            <div class="alert-icon">⚠️</div>
            <div class="alert-title">&lt; CONFIRMAR <?php echo ($user_info->vip == 1 && !empty($user_info->vip_data_expiracao)) ? 'EXTENSÃO' : 'ATIVAÇÃO'; ?> &gt;</div>
            <div class="alert-message"><?php echo $confirm_message; ?></div>
            <div class="alert-buttons">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="plano" value="<?php echo $confirm_plano; ?>">
                    <input type="hidden" name="ativar_vip" value="1">
                    <input type="hidden" name="confirmar_compra" value="1">
                    <button type="submit" class="btn-confirm-yes">
                        &lt; SIM &gt;
                    </button>
                </form>
                <button class="btn-confirm-no" onclick="closeAlert()">
                    &lt; NÃO &gt;
                </button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- POP-UP DE SUCESSO -->
<?php if(isset($success_message) && $success_message){ ?>
<div class="alert-overlay" id="successAlert">
    <div class="alert-box success">
        <div class="alert-content">
            <div class="alert-icon">🎉</div>
            <div class="alert-title">&lt; SISTEMA CONFIRMADO &gt;</div>
            <div class="alert-message"><?php echo $success_message; ?></div>
            <button class="btn-close-alert" onclick="closeAlert()">
                &lt; OK &gt;
            </button>
        </div>
    </div>
</div>
<?php } ?>

<!-- POP-UP DE ERRO -->
<?php if(isset($error_message)){ ?>
<div class="alert-overlay" id="errorAlert">
    <div class="alert-box error">
        <div class="alert-content">
            <div class="alert-icon">⚠️</div>
            <div class="alert-title">&lt; ERRO DO SISTEMA &gt;</div>
            <div class="alert-message"><?php echo $error_message; ?></div>
            <button class="btn-close-alert" onclick="closeAlert()">
                &lt; OK &gt;
            </button>
        </div>
    </div>
</div>
<?php } ?>

<div class="digital-bg"></div>
<div class="hud-grid"></div>

<div class="sao-container">
    <div class="game-hud-header">
        <h1>⚔️ DBHEROES VIP ⚔️</h1>
        <p class="system-text">&lt; SISTEMA ONLINE &gt;</p>
    </div>

    <div class="player-stats-hud">
        <div class="stats-display-grid">
            <div class="stat-box-digital">
                <div class="stat-value-digital"><?php echo number_format($user_info->coins, 0, ',', '.'); ?></div>
                <div class="stat-label-digital">💰 Créditos</div>
            </div>
            
            <?php if($user_info->vip == 1 && !empty($user_info->vip_data_expiracao)){ ?>
                <div class="stat-box-digital">
                    <div class="stat-value-digital"><?php echo date('d/m', strtotime($user_info->vip_data_expiracao)); ?></div>
                    <div class="stat-label-digital">⏰ Expira</div>
                </div>
                
                <?php 
                    $dias_restantes = ceil((strtotime($user_info->vip_data_expiracao) - time()) / (60 * 60 * 24));
                    if($dias_restantes > 0){
                ?>
                <div class="stat-box-digital">
                    <div class="stat-value-digital"><?php echo $dias_restantes; ?></div>
                    <div class="stat-label-digital">📅 Dias</div>
                </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <!-- VIP Alert -->
    <?php if($user_info->coins >= 0){ ?>
    <div class="vip-alert-box">
        <h2>⚡ ALERTA DO SISTEMA ⚡</h2>
        <p>Você não possui créditos suficientes para ativar benefícios VIP!</p>
        <p>Realize uma doação e use seus créditos para desbloquear vantagens exclusivas agora!</p>
        <a href="<?php echo BASE; ?>doacao" class="btn-vip-activate">
            &lt; DOAÇÃO &gt;
        </a>
    </div>
    <?php } ?>
    <h2 style="font-family: 'Press Start 2P', cursive; text-align: center; color: #9aff3f; font-size: 24px; margin: 100px 0 60px 0; text-shadow: 0 0 30px rgba(0, 150, 255, 0.8); letter-spacing: 0px;">
        &lt; SELECIONE O NÍVEL DO DBHEROES VIP &gt;
    </h2>

    <div class="guild-tiers">
        <!-- Nível Ferro -->
        <div class="tier-card">
            <div class="guild-badge">
                <div class="badge-icon">🛡️</div>
                <div class="guild-name">FERRO</div>
                <div class="guild-rank">DBHEROES Iniciante</div>
            </div>
            
            <div class="tier-price">
                <div class="price-digital">10</div>
                <div class="price-type">Créditos</div>
            </div>
            
            <div class="tier-duration">⏱️ 30 Dias de Acesso</div>
            
            <ul class="skills-list">
                <li>⏱️ Caçadas 50% mais rápidas</li>
                <li>⏰ 2h de Missões Diárias</li>
                <li>⭐ Badge Exclusiva VIP</li>
                <li>🏆 +20% de Experiência</li>
                <li>💰 +20% de Gold</li>
                <li>🏥 Hospital 50% OFF</li>
                <li>⚡ Recuperação +30% mais rápida</li>
                <li>🎯 Missões Premium 12h = 24h</li>
                <li>🎁 Sorteios Exclusivos VIP</li>
                <li>📊 Estatísticas Detalhadas</li>
                <li>⏳ Penalidades -50%</li>
                <li>🚀 Evolução Acelerada</li>
            </ul>
            
            <form method="POST">
                <input type="hidden" name="plano" value="1">
                <button type="submit" name="ativar_vip" class="btn-accept" 
                        <?php echo $user_info->coins < 10 ? 'disabled' : ''; ?>>
                    <?php echo $user_info->coins < 10 ? '&lt; BLOQUEADO &gt;' : '&lt; ACEITAR &gt;'; ?>
                </button>
            </form>
        </div>

        <!-- Nível Prata -->
        <div class="tier-card">
            <div class="guild-badge">
                <div class="badge-icon">⚔️</div>
                <div class="guild-name">PRATA</div>
                <div class="guild-rank">DBHEROES Avançada</div>
            </div>
            
            <div class="tier-price">
                <div class="price-digital">25</div>
                <div class="price-type">Créditos</div>
            </div>
            
            <div class="tier-duration">⏱️ 90 Dias de Acesso</div>
            
            <div class="bonus-panel">
                <div class="amount">💎 ECONOMIZE 5 CRÉDITOS 💎</div>
                <div class="description">Valor Especial de 3 Meses</div>
            </div>
            
            <ul class="skills-list">
                <li>⏱️ Caçadas 50% mais rápidas</li>
                <li>⏰ 2h de Missões Diárias</li>
                <li>⭐ Badge Exclusiva VIP</li>
                <li>🏆 +20% de Experiência</li>
                <li>💰 +20% de Gold</li>
                <li>🏥 Hospital 50% OFF</li>
                <li>⚡ Recuperação +30% mais rápida</li>
                <li>🎯 Missões Premium 12h = 24h</li>
                <li>🎁 Sorteios Exclusivos VIP</li>
                <li>📊 Estatísticas Detalhadas</li>
                <li>⏳ Penalidades -50%</li>
                <li>🚀 Evolução Acelerada</li>
                <li><strong>💵 + 5 Créditos de Bônus</strong></li>
            </ul>
            
            <form method="POST">
                <input type="hidden" name="plano" value="2">
                <button type="submit" name="ativar_vip" class="btn-accept"
                        <?php echo $user_info->coins < 25 ? 'disabled' : ''; ?>>
                    <?php echo $user_info->coins < 25 ? '&lt; BLOQUEADO &gt;' : '&lt; ACEITAR &gt;'; ?>
                </button>
            </form>
        </div>

        <!-- Nível Ouro (LENDÁRIO) -->
        <div class="tier-card legendary-tier">
            <div class="guild-badge">
                <div class="badge-icon">👑</div>
                <div class="guild-name">OURO</div>
                <div class="guild-rank">Guilda de Elite</div>
            </div>
            
            <div class="tier-price">
                <div class="price-digital">45</div>
                <div class="price-type">Créditos</div>
            </div>
            
            <div class="tier-duration">⏱️ 180 Dias de Acesso</div>
            
            <div class="bonus-panel">
                <div class="amount">🌟 ECONOMIZE 15 CRÉDITOS 🌟</div>
                <div class="description">Melhor Custo-Benefício!</div>
            </div>
            
            <ul class="skills-list">
                <li>⏱️ Caçadas 50% mais rápidas</li>
                <li>⏰ 2h de Missões Diárias</li>
                <li>⭐ Badge Exclusiva VIP</li>
                <li>🏆 +20% de Experiência</li>
                <li>💰 +20% de Gold</li>
                <li>🏥 Hospital 50% OFF</li>
                <li>⚡ Recuperação +30% mais rápida</li>
                <li>🎯 Missões Premium 12h = 24h</li>
                <li>🎁 Sorteios Exclusivos VIP</li>
                <li>📊 Estatísticas Detalhadas</li>
                <li>⏳ Penalidades -50%</li>
                <li>🚀 Evolução Acelerada</li>
                <li><strong>💵 + 15 Créditos de Bônus</strong></li>
                <li><strong>🎨 Itens Raros Exclusivos</strong></li>
            </ul>
            
            <form method="POST">
                <input type="hidden" name="plano" value="3">
                <button type="submit" name="ativar_vip" class="btn-accept"
                        <?php echo $user_info->coins < 45 ? 'disabled' : ''; ?>>
                    <?php echo $user_info->coins < 45 ? '&lt; BLOQUEADO &gt;' : '&lt; ENTRAR AGORA &gt;'; ?>
                </button>
            </form>
        </div>

        <!-- Nível Platina -->
        <div class="tier-card">
            <div class="guild-badge">
                <div class="badge-icon">💎</div>
                <div class="guild-name">PLATINA</div>
                <div class="guild-rank">DBHEROES Mestre</div>
            </div>
            
            <div class="tier-price">
                <div class="price-digital">80</div>
                <div class="price-type">Créditos</div>
            </div>
            
            <div class="tier-duration">⏱️ 365 Dias de Acesso</div>
            
            <div class="bonus-panel">
                <div class="amount">✨ ECONOMIZE 40 CRÉDITOS ✨</div>
                <div class="description">Máxima Economia Anual!</div>
            </div>
            
            <ul class="skills-list">
                <li>⏱️ Caçadas 50% mais rápidas</li>
                <li>⏰ 2h de Missões Diárias</li>
                <li>⭐ Badge Exclusiva VIP</li>
                <li>🏆 +20% de Experiência</li>
                <li>💰 +20% de Gold</li>
                <li>🏥 Hospital 50% OFF</li>
                <li>⚡ Recuperação +30% mais rápida</li>
                <li>🎯 Missões Premium 12h = 24h</li>
                <li>🎁 Sorteios Exclusivos VIP</li>
                <li>📊 Estatísticas Detalhadas</li>
                <li>⏳ Penalidades -50%</li>
                <li>🚀 Evolução Acelerada</li>
                <li><strong>💵 + 40 Créditos de Bônus</strong></li>
                <li><strong>🎨 Itens Raros Exclusivos</strong></li>
                <li><strong>⚡ Itens Épicos Lendários</strong></li>
                <li><strong>👑 Status de Mestre VIP</strong></li>
            </ul>
            
            <form method="POST">
                <input type="hidden" name="plano" value="4">
                <button type="submit" name="ativar_vip" class="btn-accept"
                        <?php echo $user_info->coins < 80 ? 'disabled' : ''; ?>>
                    <?php echo $user_info->coins < 80 ? '&lt; BLOQUEADO &gt;' : '&lt; ACEITAR &gt;'; ?>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
// Função para fechar pop-up alert
function closeAlert() {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    const confirmAlert = document.getElementById('confirmAlert');
    
    if(successAlert) {
        successAlert.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => successAlert.remove(), 300);
    }
    
    if(errorAlert) {
        errorAlert.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => errorAlert.remove(), 300);
    }
    
    if(confirmAlert) {
        confirmAlert.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => confirmAlert.remove(), 300);
    }
}

// Fechar ao clicar fora do box
document.addEventListener('click', function(e) {
    if(e.target.classList.contains('alert-overlay')) {
        closeAlert();
    }
});

// Fechar com tecla ESC
document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        closeAlert();
    }
});

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
