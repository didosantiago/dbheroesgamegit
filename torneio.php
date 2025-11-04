<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($core->proccessInExecution()){
        header('Location: '.BASE.'profile');
    }
?>

<script type="text/javascript">
    var nav = $('.guerreiros-list li:nth-child(4)');
    if (nav.length) {
      var contentNav = nav.offset().top;
      $('html, body').animate({scrollTop: contentNav}, 'slow');
    }
</script>

<div class="arena">
    <h2 class="title">Torneio de Artes Marciais (NPC)</h2>
    
    <ul class="guerreiros-list">
        <?php 
            $torneio->getList($personagem->nivel); 
        ?> 
    </ul>
</div>
