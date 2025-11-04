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
            <ul>
                <?php $inventario->getSlots($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>

        <div class="separador"></div>

        <div class="equipados">
            <h2>Itens Equipados</h2>
            <ul>
                <?php $inventario->getSlotsEquipados($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>
        
        <div class="adesivos">
            <h2>Adesivos Equipados</h2>
            <ul>
                <?php $inventario->getSlotsAdesivos($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>
    </div>
    <?php $inventario->setViewInventory($_SESSION['PERSONAGEMID']); ?>
</div>