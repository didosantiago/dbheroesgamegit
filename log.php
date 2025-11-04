<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
?>

<h2 class="title">LOG Batalha</h2>

<div class="lista-logs">
    <?php
        if(Url::getURL(1) != 'ajax'){
            $batalha->getHistoricoBatalha(Url::getURL(1), $pc, 15);
        }
    ?>
</div>