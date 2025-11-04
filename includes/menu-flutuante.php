<ul class="menu-flutuante desktop">
    <li>
        <a href="<?php echo BASE; ?>doacao">
            <i class="fas fa-donate"></i>
            <span>Adquirir Coins</span>
        </a>
    </li>
    <?php if(!isset($_SESSION['missao']) && !isset($_SESSION['cacada'])){ ?>
    <li class="missoes">
        <a href="<?php echo BASE; ?>missoes">
            <i class="far fa-clock"></i>
            <span>Iniciar Missão</span>
        </a>
    </li>
    <li class="cacadas">
        <a href="<?php echo BASE; ?>cacadas">
            <i class="fas fa-search-location"></i>
            <span>Iniciar Caçada</span>
        </a>
    </li>
    <?php } ?>
</ul>