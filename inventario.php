<?php   
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
    
    if($personagem->existsGuerreiro($user->id)){
        if(!isset($_SESSION['PERSONAGEMID'])){
            header('Location: '.BASE.'meus-personagens');
        }
    } else {
        header('Location: '.BASE.'criar-personagem');
    }
    
    if(Url::getURL(1) == 'open'){
        $_SESSION['DESTRANCAR'] = 1;
        header('Location: '.BASE.'inventario');
    } else {
        $_SESSION['DESTRANCAR'] = 0;
    }
?>

<input type="hidden" id="personagemLogged" value="<?php echo $_SESSION['PERSONAGEMID']; ?>">

<div class="box-inventario">
    <div class="border-horizontal-top"></div>
    <div class="border-vertical-left"></div>
    <div class="border-top-left"></div>
    <div class="border-top-right"></div>
    <div class="border-bottom-left"></div>
    <div class="border-bottom-right"></div>
    <div class="border-vertical-right"></div>
    <div class="border-horizontal-bottom"></div>
    
    <div class="content-inventory">
        <div class="itens">
            <h2>Meu Inventário</h2>
            <ul style="list-style: none; padding: 10px;">
                <?php 
                // DIRECT LOAD - NO AJAX
                $inventario->getSlots($_SESSION['PERSONAGEMID']); 
                ?>
            </ul>
        </div>

        <div class="separador"></div>

        <div class="equipados">
            <h2>Itens Equipados</h2>
            <ul style="list-style: none; padding: 10px;">
                <?php $inventario->getSlotsEquipados($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>
        
        <div class="adesivos">
            <h2>Adesivos Equipados</h2>
            <ul style="list-style: none; padding: 10px;">
                <?php $inventario->getSlotsAdesivos($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>
    </div>
    <?php $inventario->setViewInventory($_SESSION['PERSONAGEMID']); ?>
</div>

<!-- Temporary CSS to make slots visible -->
<style>
.content-inventory .itens ul li.slots,
.content-inventory .equipados ul li.slots,
.content-inventory .adesivos ul li.slots {
    width: 70px !important;
    height: 70px !important;
    background: #3a2317 !important;
    border: 2px solid #6b4423 !important;
    display: inline-block !important;
    margin: 5px !important;
    position: relative !important;
    vertical-align: top !important;
}

.content-inventory ul li.slot-vazio {
    background: #2a1a0f !important;
    border: 2px dashed #4a3323 !important;
}

.content-inventory ul li.slots img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
}
</style>

<!-- REMOVE AJAX INIT FOR NOW -->
<!--
<script>
$(document).ready(function() {
    if (typeof DBH !== 'undefined' && typeof DBH.inventario !== 'undefined') {
        DBH.inventario.init();
    }
});
</script>
-->
