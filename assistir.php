<?php require_once 'front/header-front.php'; ?>

<div class="secao-videos">
    <div class="stm-container">
        <h2>Vídeos em Destaque</h2>
        <ul class="slider-videos">
            <?php echo $administrar->getListVideosDestaque(); ?>
        </ul>
    </div>
</div>

<div class="all-videos">
    <div class="stm-container">
        <h2>Todos o Vídeos</h2>
        <ul class="todos-videos">
            <?php echo $administrar->getListVideos(); ?>
        </ul>
    </div>
</div>

<?php require_once 'front/footer-front.php'; ?>
