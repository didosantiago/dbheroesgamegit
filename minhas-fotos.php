<h2 class="title">Minhas Fotos</h2>

<div class="legenda">
    <p class="free">
        <i class="fas fa-lock"></i>
        Item disponível na Loja Diária de Itens
    </p>
</div>

<ul class="fotos-personagem">
    <?php
        $dados = $core->getDados('usuarios_personagens', 'WHERE id = '.$_SESSION['PERSONAGEMID']);
        $personagem->getAllFotosPersonagem($_SESSION['PERSONAGEMID'], $dados->foto, $user->vip, $personagem->graduacao_id, $personagem->boneco, $user->id); 
    ?>
</ul>

<?php $personagem->setViewFotos($_SESSION['PERSONAGEMID']); ?>

<script type="text/javascript">
    $("#confirmarFoto").click(function(e) {
        e.preventDefault();
        var id = $('.nova-foto img').attr('id');
        var obj = $('.fotos-personagem #'+id+'-1');
        
        $('.backdrop-game').remove();
        $('.nova-foto').remove();;
        
        $('html, body').animate({
            scrollTop: $(obj).offset().top
        }, 1200);
    });
</script>

