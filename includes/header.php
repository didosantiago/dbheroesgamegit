<?php
    // Load selected character data
    if(isset($_SESSION["PERSONAGEMID"])){
        $personagem->getGuerreiro($_SESSION["PERSONAGEMID"]);
        
        // Calculate HP Max based on level
        if($personagem->nivel > 1){
            $nivel_hp = 150 + ((intval($personagem->nivel) - 1) * 50);
        } else {
            $nivel_hp = 150;
        }
        
        // Calculate current values
        $ki_atual = intval($personagem->mana) - intval($personagem->ki_usado);
        $energia_atual = intval($personagem->energia) - intval($personagem->energia_usada);
        
        // Calculate percentages
        $porcentagem_hp = ($personagem->hp / $nivel_hp) * 100;
        $porcentagem_ki = ($ki_atual / $personagem->mana) * 100;
        $porcentagem_energia = ($energia_atual / $personagem->energia) * 100;
    }
    
    // VIP Status Check - CORRIGIDO
    $vip_ativo = false;
    if(isset($user->vip) && $user->vip == 1){
        if(!empty($user->vip_data_expiracao)){
            $vip_ativo = (strtotime($user->vip_data_expiracao) > time());
        } else {
            // Se não tem data de expiração mas vip=1, considera ativo
            $vip_ativo = true;
        }
    }
?>

<!-- CSS PARA O VIP BADGE COMPACTO -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');

.vip-badge-compact {
    position: absolute;
    top: 210px; /* AJUSTADO - mais baixo */
    right: 360px;
    z-index: 10000;
    padding: 12px 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
}

/* VIP ATIVO - Dourado */
.vip-badge-compact.vip-active {
    background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(255, 165, 0, 0.15));
    border: 2px solid #ffd700;
    box-shadow: 
        0 0 20px rgba(255, 215, 0, 0.5),
        inset 0 0 20px rgba(255, 215, 0, 0.1);
    animation: vipGlow 2s ease-in-out infinite;
}

/* NÃO VIP - Cinza escuro */
.vip-badge-compact.vip-inactive {
    background: linear-gradient(135deg, rgba(60, 60, 60, 0.3), rgba(40, 40, 40, 0.3));
    border: 2px solid #555;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.vip-badge-compact:hover {
    transform: translateY(-3px);
}

.vip-badge-compact.vip-active:hover {
    box-shadow: 
        0 5px 25px rgba(255, 215, 0, 0.6),
        inset 0 0 25px rgba(255, 215, 0, 0.2);
}

.vip-badge-compact.vip-inactive:hover {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
    border-color: #777;
}

@keyframes vipGlow {
    0%, 100% {
        box-shadow: 
            0 0 20px rgba(255, 215, 0, 0.5),
            inset 0 0 20px rgba(255, 215, 0, 0.1);
    }
    50% {
        box-shadow: 
            0 0 35px rgba(255, 215, 0, 0.8),
            inset 0 0 35px rgba(255, 215, 0, 0.2);
    }
}

.vip-badge-icon {
    font-size: 24px;
    filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.8));
}

.vip-badge-compact.vip-inactive .vip-badge-icon {
    filter: grayscale(100%) opacity(0.6);
}

.vip-badge-info {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.vip-badge-title {
    font-family: 'Press Start 2P', cursive;
    font-size: 11px;
    color: #ffd700;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
    letter-spacing: 1px;
    text-transform: uppercase;
}

.vip-badge-compact.vip-inactive .vip-badge-title {
    color: #888;
    text-shadow: none;
}

.vip-badge-subtitle {
    font-size: 9px;
    color: #00ff96;
    font-weight: 600;
    text-shadow: 0 0 8px rgba(0, 255, 150, 0.6);
}

.vip-badge-compact.vip-inactive .vip-badge-subtitle {
    color: #00ff96;
    text-shadow: 0 0 8px rgba(0, 255, 150, 0.8);
    cursor: pointer;
}

/* Responsivo */
@media (max-width: 768px) {
    .vip-badge-compact {
        top: 80px; /* AJUSTADO - proporcional em mobile */
        right: 10px;
        padding: 8px 15px;
        gap: 8px;
    }
    
    .vip-badge-icon {
        font-size: 20px;
    }
    
    .vip-badge-title {
        font-size: 9px;
    }
    
    .vip-badge-subtitle {
        font-size: 8px;
    }
}

@media (max-width: 480px) {
    .vip-badge-compact {
        top: 70px; /* AJUSTADO - proporcional em mobile pequeno */
        right: 5px;
        padding: 6px 12px;
        gap: 6px;
    }
    
    .vip-badge-icon {
        font-size: 18px;
    }
    
    .vip-badge-title {
        font-size: 8px;
    }
    
    .vip-badge-subtitle {
        font-size: 7px;
    }
}
</style>

<header>
    <!-- VIP BADGE COMPACTO NO TOPO DIREITO -->
    <a href="<?php echo BASE; ?>ativar-vip" class="vip-badge-compact <?php echo $vip_ativo ? 'vip-active' : 'vip-inactive'; ?>">
        <div class="vip-badge-icon">
            <?php echo $vip_ativo ? '👑' : '🔒'; ?>
        </div>
        <div class="vip-badge-info">
            <div class="vip-badge-title">
                <?php echo $vip_ativo ? 'JOGADOR VIP' : 'NÃO VIP'; ?>
            </div>
            <div class="vip-badge-subtitle">
                <?php 
                if($vip_ativo){
                    if(!empty($user->vip_data_expiracao)){
                        echo 'Expira: ' . date('d/m/Y', strtotime($user->vip_data_expiracao));
                    } else {
                        echo 'VIP Ativo';
                    }
                } else {
                    echo 'Ativar VIP';
                }
                ?>
            </div>
        </div>
    </a>

    <div class="topo">
        <img class="desktop" src="<?php echo BASE; ?>assets/header.jpg" alt="" />
        <img class="mobile" src="<?php echo BASE; ?>assets/header-mobile.jpg" alt="" />
        <h1 class="logo">
            <a href="<?php echo BASE; ?>">
                <?php require_once 'front/svg.php'; ?>
            </a>
        </h1>
    </div>
    
    <ul class="menu-superior desktop">
        <div class="container">
            <li>
                <a href="<?php echo BASE; ?>portal">Inicio</a>
            </li>
            
            <li>
                <a href="#"><i class="fas fa-question"></i> Suporte</a>
                <ul class="submenu">
                    <li>
                        <a href="https://discord.gg/zbSWtcs" target="_blank"><i class="fab fa-discord"></i> Chat da Comunidade</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE; ?>forum"><i class="fas fa-user-tie"></i> Fórum</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE; ?>faq"><i class="fas fa-question"></i> Faq</a>
                    </li>
                </ul>
            </li>
            
                 
                    <li>
                        <a href="<?php echo BASE; ?>meus-personagens"><i class="fas fa-users"></i> Meus Guerreiros</a>
                    </li>
                    
            <li>
                <a href="#"><i class="far fa-user"></i> Usuário</a>
                <ul class="submenu">
                        <li>
                            <a href="<?php echo BASE; ?>criar-personagem"><i class="fa fa-plus"></i> Novo Guerreiro</a>
                        </li>
                    <li>
                        <a href="<?php echo BASE; ?>meus-personagens"><i class="fas fa-user-shield"></i> Meus Guerreiros</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE; ?>amigos"><i class="fas fa-users"></i> Lista de Amigos</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE; ?>perfil"><i class="fas fa-edit"></i> Editar Perfil</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE; ?>trocar-senha"><i class="fas fa-key"></i> Trocar Senha</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE; ?>transacoes"><i class="far fa-credit-card"></i> Transações</a>
                    </li>
                </ul>
            </li>
                    
                <li>
                    <a href="#"><i class="far fa-play-circle"></i> Jogar</a>
                    <ul class="submenu">
                        <li>
                            <a href="<?php echo BASE; ?>profile"><i class="fas fa-user"></i> Profile</a>
                        </li>
                        <li>
                            <a href="<?php echo BASE; ?>publico"><i class="fas fa-user-shield"></i> Perfil Público</a>
                        </li>
                        <li>
                            <a href="<?php echo BASE; ?>golpes"><i class="fas fa-chart-line"></i> Treinar Golpes</a>
                        </li>
                        <?php if(!isset($_SESSION['missao']) && !isset($_SESSION['cacada'])){ ?>
                            <li>
                                <a href="<?php echo BASE; ?>missoes"><i class="far fa-clock"></i> Iniciar uma Missão</a>
                            </li>
                            <li>
                                <a href="<?php echo BASE; ?>cacadas"><i class="fas fa-search-location"></i> Iniciar uma Caçada</a>
                            </li>
                            <li>
                                <a href="<?php echo BASE; ?>torneio"><i class="fas fa-award"></i> TAM (Ganhe EXP)</a>
                            </li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo BASE; ?>historico"><i class="fas fa-history"></i> Histórico PVP</a>
                        </li>
                        <li>
                            <a href="<?php echo BASE; ?>inventario"><i class="fas fa-archive"></i> Inventário</a>
                        </li>
                        <li>
                            <a href="<?php echo BASE; ?>equipes"><i class="fas fa-users"></i> Equipes</a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="<?php echo BASE; ?>hospital"><i class="fas fa-calendar-plus"></i> Hospital</a>
                </li>
                
                    <li>
                        <a href="<?php echo BASE; ?>torneio"><i class="fas fa-award"></i> TAM (Ganhe EXP)</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE; ?>pvp"><i class="fas fa-globe-americas"></i> PVP Global</a>
                    </li>
                    
                <li>
                    <a href="#"><i class="fas fa-trophy"></i> Ranking</a>
                    <ul class="submenu">
                        <li>
                            <a href="<?php echo BASE; ?>ranking"><i class="far fa-chart-bar"></i> Jogadores</a>
                        </li>
                        <li>
                            <a href="<?php echo BASE; ?>equipes/ranking"><i class="far fa-chart-bar"></i> Equipes</a>
                        </li>
                    </ul>
                </li>
                
            <li class="sair">
                <a href="<?php echo BASE; ?>logout"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </li>
            
            <div class="radar">
                <span><?php echo isset($user->coins) ? $user->coins : '0'; ?></span>
                <strong>Coins</strong>
            </div>
        </div>
    </ul>
    
    <!-- Mobile Version -->
    <div class="user-logado-mobile v-mobile">
        <div class="bloco">
            Usuário Logado: <strong><?php echo $user->username; ?></strong>
        </div>

        <div class="bloco">
            <span><strong style="color: #fff600; font-size: 14px;"><i class="fas fa-coins"></i> <?php echo isset($personagem->gold) ? $personagem->gold : '0'; ?> golds</strong></span>
        </div>

        <div class="bloco">
            <span>Guerreiro: <strong style="color: #41BCD1; font-size: 12px;"><?php echo isset($personagem->nome) ? $personagem->nome : 'N/A'; ?></strong></span>
        </div>

        <div class="bloco">
            <span>Raça: <strong style="color: #41BCD1; font-size: 12px;"><?php echo isset($personagem->raca) ? $personagem->raca : 'N/A'; ?></strong></span>
        </div>

        <div class="bloco">
            <span>Planeta: <strong style="color: #41BCD1; font-size: 12px;"><?php echo isset($personagem->planeta) ? $personagem->planeta : 'N/A'; ?></strong></span>
        </div>

        <div class="bloco">
            <?php if(isset($user->vip) && $user->vip == 1){ ?>
                <span>Jogador VIP</span>
            <?php } else { ?>
                <span>Jogador Free</span>
                <a href="<?php echo BASE; ?>vantagens" class="tornar-vip">(Clique Aqui para ser VIP)</a>
            <?php } ?>
        </div>
        
        <div class="coins-mobile v-mobile">
            <img src="<?php echo BASE; ?>assets/icones/coin.png" /> <?php echo isset($user->coins) ? $user->coins : '0'; ?> Coins
        </div>
    </div>
    
    <!-- User Status Bar -->
    <div class="user-status">
        <div class="container">
            <ul>
                <li class="amigos-box">
                    <a href="<?php echo BASE; ?>amigos">
                        <i class="fas fa-user-friends"></i>
                        <span class="cont">1</span>
                    </a>
                </li>
                
                <li class="equipes-notify">
                    <a href="<?php echo BASE; ?>equipes/convites">
                        <i class="fas fa-shield-alt"></i>
                        <span>Convite Equipes</span>
                        <span class="cont">1</span>
                    </a>
                </li>
                
                <li class="loja-itens">
                    <a href="<?php echo BASE; ?>loja">
                        <i class="fas fa-star"></i>
                        <span>Loja de Itens</span>
                    </a>
                </li>
                <div class="atributos-g">
                    <li class="hp">
                        <strong>HP </strong>
                        <div class="meter animate red">
                            <em><?php echo isset($personagem->hp) ? $personagem->hp : '0'; ?> / <strong><?php echo isset($nivel_hp) ? $nivel_hp : '150'; ?></strong></em>
                            <span style="width: <?php echo isset($porcentagem_hp) ? $porcentagem_hp : '0'; ?>%"><span></span></span>
                        </div>
                    </li>
                    <li class="ki">
                        <strong>KI </strong>
                        <div class="meter animate blue">
                            <em><?php echo isset($ki_atual) ? $ki_atual : '0'; ?> / <strong><?php echo isset($personagem->mana) ? $personagem->mana : '100'; ?></strong></em>
                            <span style="width: <?php echo isset($porcentagem_ki) ? $porcentagem_ki : '0'; ?>%"><span></span></span>
                        </div>
                    </li>
                    <li class="energia">
                        <strong>Energia </strong>
                        <div class="meter animate">
                            <em><?php echo isset($energia_atual) ? $energia_atual : '0'; ?> / <strong><?php echo isset($personagem->energia) ? $personagem->energia : '100'; ?></strong></em>
                            <span style="width: <?php echo isset($porcentagem_energia) ? $porcentagem_energia : '0'; ?>%"><span></span></span>
                        </div>
                    </li>
                </div>
            </ul>
        </div>
    </div>

    <!-- Desktop Version -->
    <div class="user-logado h-mobile">
        <div class="bloco">
            Usuário Logado: <strong><?php echo $user->username; ?></strong>
        </div>
        
        <div class="bloco">
            <span><strong style="color: #fff600; font-size: 14px;"><i class="fas fa-coins"></i> <?php echo isset($personagem->gold) ? $personagem->gold : '0'; ?> golds</strong></span>
        </div>
        
        <div class="bloco">
            <span>Guerreiro: <strong style="color: #41BCD1; font-size: 12px;"><?php echo isset($personagem->nome) ? $personagem->nome : 'N/A'; ?></strong></span>
        </div>
        
        <div class="bloco">
            <span>Raça: <strong style="color: #41BCD1; font-size: 12px;"><?php echo isset($personagem->raca) ? $personagem->raca : 'N/A'; ?></strong></span>
        </div>
        
        <div class="bloco">
            <span>Planeta: <strong style="color: #41BCD1; font-size: 12px;"><?php echo isset($personagem->planeta) ? $personagem->planeta : 'N/A'; ?></strong></span>
        </div>
        
        <div class="bloco">
            <?php if(isset($user->vip) && $user->vip == 1){ ?>
                <span>Jogador VIP</span>
            <?php } else { ?>
                <span>Jogador Free</span>
                <a href="<?php echo BASE; ?>vantagens" class="tornar-vip">(Clique Aqui para ser VIP)</a>
            <?php } ?>
        </div>
    </div>
</header>

<div class="modal-game">
    <div class="modal-header">
        <button class="close-modal"></button>
    </div>
    <div class="modal-body">
        <div class="anuncio">
            <a href="<?php echo BASE; ?>doacao">
                <img src="<?php echo BASE; ?>assets/banner-vip.jpg" alt="" />
            </a>
        </div>
    </div>
</div>

<script type="text/javascript">
    if(!$.cookie('modal_vip_ad')){
        $('body').prepend('<div class="backdrop-game"></div>');
        var date = new Date();
        var minutes = 30;
        date.setTime(date.getTime() + (minutes * 60 * 1000));
        $.cookie('modal_vip_ad', 'value', { expires: date });
        $('.modal-game').show();
    }
    
    $('.close-modal').on('click', function(){
        $('.modal-game').hide();
        $('.backdrop-game').remove();
    });
</script>
