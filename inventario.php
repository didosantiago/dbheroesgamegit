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

        <!-- EMBLEMAS (3 slots superiores) -->
        <div class="emblemas">
            <h2>Emblemas Equipados</h2>
            <ul style="list-style: none; padding: 10px;">
                <?php $inventario->getSlotsEmblemas($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>

        <!-- EQUIPAMENTOS (5 slots) -->
        <div class="equipamentos">
            <h2>Equipamentos</h2>
            <ul style="list-style: none; padding: 10px;">
                <?php $inventario->getSlotsEquipados($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>

        <!-- ADESIVOS -->
        <div class="adesivos">
            <h2>Adesivos Equipados</h2>
            <ul style="list-style: none; padding: 10px;">
                <?php $inventario->getSlotsAdesivos($_SESSION['PERSONAGEMID']); ?>
            </ul>
</div>


<!-- Temporary CSS to make slots visible -->
<style>

    /* Emblema slots (purple border) */
.emblemas ul li.slot-emblema {
    background-color: #6a1b9a;
    border-color: #9c27b0;
}

.emblemas ul li.slot-emblema.slot-vazio {
    background-image: url('./assets/slot-emblema.png') !important;
    background-color: #4a148c;
}

.emblemas ul li.emblema:hover {
    border-color: #ce93d8;
    transform: scale(1.05);
    transition: all 0.2s;
}

/* Equipment slots (blue border) */
.equipamentos ul li.slot-equipado {
    background-color: #1565c0;
    border-color: #2196f3;
}

.equipamentos ul li.slot-equipado.slot-vazio {
    background-image: url('./assets/slot-equipado.png') !important;
    background-color: #0d47a1;
}

.equipamentos ul li.equipped:hover {
    border-color: #64b5f6;
    transform: scale(1.05);
    transition: all 0.2s;
}

/* Inventory Slot Styles */
/* Inventory Slot Styles */
.content-inventory .itens ul li.slots,
.content-inventory .equipados ul li.slots,
.content-inventory .adesivos ul li.slots {
    width: 70px;
    height: 70px;
    display: inline-block;
    margin: 5px;
    position: relative;
    vertical-align: top;
    cursor: pointer;
    border: 2px solid #6b4423;
    border-radius: 5px;
    background-size: cover !important;
    background-repeat: no-repeat !important;
}

/* Slot hover effects */
.content-inventory .itens ul li.slots.slot-bau:hover {
    background-image: url('./assets/slot-bau-hover.png') !important;
    border-color: #00ff40ff;
}

.content-inventory .itens ul li.slots:hover {
    border-color: #00ff40ff;
    transform: scale(1.05);
    transition: all 0.2s;
}

/* Empty slots */
.content-inventory ul li.slot-vazio {
    background: #2a1a0f;
    border: 2px dashed #4a3323;
    cursor: default;
}

.content-inventory ul li.slot-vazio:hover {
    border-color: #4a3323;
    transform: none;
}

/* Item images */
.content-inventory ul li.slots img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
}

/* Item info tooltip 
.content-inventory .informacoes {
    position: absolute;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.95);
    padding: 10px;
    border-radius: 5px;
    border: 2px solid #ffd700;
    min-width: 150px;
    z-index: 1000;
    display: none;
}*/

.content-inventory .informacoes h3 {
    margin: 0 0 5px 0;
    color: #ffd700;
    font-size: 14px;
}

.content-inventory .informacoes p {
    margin: 3px 0;
    color: #fff;
    font-size: 12px;
}

/* Raridade colors */
.raridade-1 { border-color: #888; } /* Common - Gray */
.raridade-2 { border-color: #4caf50; } /* Uncommon - Green */
.raridade-3 { border-color: #2196f3; } /* Rare - Blue */
.raridade-4 { border-color: #9c27b0; } /* Epic - Purple */
.raridade-5 { border-color: #ff9800; } /* Legendary - Orange */

/* Adesivo slots (yellow) */
.adesivos ul li.slot-amarelo {
    background-color: #ffeb3b;
    border-color: #fbc02d;
}

.adesivos ul li.slot-amarelo.slot-vazio {
    background-image: url('./assets/slot-amarelo.png') !important;
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
