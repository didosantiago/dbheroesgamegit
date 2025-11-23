<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="stm-main">
    <div class="stm-top-bar">
        <h1 class="logo">
            <a href="<?php echo BASE; ?>">
                <?php require_once 'front/svg.php'; ?>
            </a>
        </h1>
        <ul class="stm-menu desktop">
            <li>
                <a href="<?php echo BASE; ?>home">
                    <p>Início</p>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE; ?>cadastro">
                    <p>Crie sua Conta</p>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE; ?>rank">
                    <p>TOP 10 Ranking</p>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE; ?>assistir">
                    <p>Tutorial do Jogo</p>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE; ?>sobre">
                    <p>Sobre</p>
                </a>
            </li>
            <li>
                <a href="https://blog.dbheroes.com.br" target="_blank">
                    <p>Blog</p>
                </a>
            </li>
        </ul>
        
        <ul class="right-menu">

            <li class="iniciar-sessao mobile">
                <a href="<?php echo BASE; ?>cadastro">
                    <i class="far fa-play-circle"></i>
                    <p>Criar Conta</p>
                </a>
            </li>
        </ul>
    </div>
</div>