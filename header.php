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
?>

<style>
    /* Smarter, flexible menu layout for top bar */
    .menu-superior.desktop .container {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        align-items: center;
    }
    .menu-superior.desktop li {
        white-space: nowrap;
        flex-shrink: 1;
        margin: 0 6px;
    }
    .menu-superior.desktop li.sair {
        margin-left: auto;
        flex-shrink: 0;
    }
    .menu-superior.desktop li a {
        font-size: 14px;
        padding: 8px 12px;
    }
    .menu-superior.desktop i {
        font-size: 14px;
    }
</style>

<header>
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
                    <a href="<?php echo BASE; ?>criar-personagem"><i class="fa fa-plus"></i> Novo Guerreiro</a>
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
                        <a href="<?php echo BASE; ?>meus-personagens"><i class="fas fa-users"></i> Meus Guerreiros</a>
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
                        <a href="<?php echo BASE; ?>historico"><i class="fas fa-archive"></i> Histórico PVP</a>
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
                <a href="#">Ranking</a>
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
                <a href="<?php echo BASE; ?>logout">Sair</a>
            </li>
            <div class="radar">
                <span><?php echo isset($user->coins) ? $user->coins : '0'; ?></span>
                <strong>Coins</strong>
            </div>
        </div>
    </ul>
    
    <!-- (rest of your file remains the same) -->
