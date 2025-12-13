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
                <?php $inventario->getSlots($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>
        <div class="separador"></div>
        <div class="emblemas">
            <h2>Emblemas Equipados</h2>
            <ul style="list-style: none; padding: 10px;">
                <?php $inventario->getSlotsEmblemas($_SESSION['PERSONAGEMID']); ?>
            </ul>
        </div>
        <div class="equipamentos">
            <h2>Equipamentos</h2>
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
</div>

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
.raridade-1 { border-color: #888; }
.raridade-2 { border-color: #4caf50; }
.raridade-3 { border-color: #2196f3; }
.raridade-4 { border-color: #9c27b0; }
.raridade-5 { border-color: #ff9800; }

/* Adesivo slots (yellow) */
.adesivos ul li.slot-amarelo {
    background-color: #ffeb3b;
    border-color: #fbc02d;
}
.adesivos ul li.slot-amarelo.slot-vazio {
    background-image: url('./assets/slot-amarelo.png') !important;
}

/* Emblema slots - ALWAYS show purple slot background */
.emblemas ul li.slot-emblema {
    background-image: url('./assets/slot-emblema.png') !important;
    background-size: cover !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
}

/* Equipment slots - ALWAYS show blue slot background */
.equipamentos ul li.slot-equipado {
    background-image: url('./assets/slot-equipado.png') !important;
    background-size: cover !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
}

/* Adesivo slots - ALWAYS show yellow slot background */
.adesivos ul li.slot-amarelo {
    background-image: url('./assets/slot-amarelo.png') !important;
    background-size: cover !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
}

/* Item images should be layered ON TOP of backgrounds */
.emblemas ul li img,
.equipamentos ul li img,
.adesivos ul li img {
    position: relative;
    z-index: 2;
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
}

/* Remove any background-color overrides */
.emblemas ul li.has-item,
.equipamentos ul li.has-item,
.adesivos ul li.has-item {
    background-color: transparent !important;
}

/* Center Emblemas and Equipamentos sections */
.emblemas { text-align: center; }
.emblemas h2 { text-align: center; }
.emblemas ul {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    padding: 10px;
    list-style: none;
}
.equipamentos { text-align: center; }
.equipamentos h2 { text-align: center; }
.equipamentos ul {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    padding: 10px;
    list-style: none;
}
.adesivos { text-align: center; }
.adesivos h2 { text-align: center; }
.adesivos ul {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    padding: 10px;
    list-style: none;
}

/* Style ALL section headers with gold color */
.content-inventory h2,
.itens h2,
.emblemas h2,
.equipamentos h2,
.adesivos h2 {
    color: #FFD700 !important;
    font-size: 20px;
    font-weight: bold;
    text-transform: uppercase;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    margin: 15px 0 10px 0;
    letter-spacing: 2px;
}



</style>

<script>
window.INVENTORY_ITEMS = <?php
$allItems = [];
$stmt = DB::prepare("SELECT * FROM itens");
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $item){
    $allItems[$item['id']] = $item;
}
echo json_encode($allItems, JSON_UNESCAPED_UNICODE);
?>;

const CONSUMABLE_TYPES = ['consumivel', 'capsula', 'comida', 'restauracao', 'pocao'];

function isConsumable(itemId) {
    const item = window.INVENTORY_ITEMS[itemId];
    if(!item) return false;
    return CONSUMABLE_TYPES.includes((item.tipo || '').toLowerCase());
}

function attachInventoryTooltips() {
    document.querySelectorAll('.slot-item').forEach(function(el){
        el.onmouseenter = function(e){
            document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
            var itemId = this.getAttribute('data-item');
            var data = window.INVENTORY_ITEMS[itemId];
            if (!data) return;
            
            var tip = document.createElement("div");
            tip.className = "item-tooltip";
            
            var content = "<strong>" + data.nome + "</strong><br>";
            
            if(isConsumable(itemId)){
                content += "<em style='color:#ffa500;'>Consumível - Clique para usar</em><br>";
                if(data.efeito_hp) content += "Restaura " + data.efeito_hp + "% de HP<br>";
                if(data.efeito_ki) content += "Restaura " + data.efeito_ki + "% de KI<br>";
                if(data.efeito_energia) content += "Restaura " + data.efeito_energia + "% de Energia<br>";
                if(data.efeito_experiencia) content += "Bônus de " + data.efeito_experiencia + "% EXP por 30min<br>";
            } else {
                if(data.forca) content += "Força: <span style='color:#5b5'>+" + data.forca + "</span><br>";
                if(data.agilidade) content += "Agilidade: <span style='color:#5b5'>+" + data.agilidade + "</span><br>";
                if(data.habilidade) content += "Habilidade: <span style='color:#5b5'>+" + data.habilidade + "</span><br>";
                if(data.resistencia) content += "Resistência: <span style='color:#5b5'>+" + data.resistencia + "</span><br>";
                if(data.sorte) content += "Sorte: <span style='color:#5b5'>+" + data.sorte + "</span><br>";
            }
            
            tip.innerHTML = content;
            document.body.appendChild(tip);
            
            function moveTooltip(ev) {
                tip.style.position = 'fixed';
                tip.style.left = (ev.clientX + 20) + 'px';
                tip.style.top = (ev.clientY + 20) + 'px';
                tip.style.zIndex = 10000;
            }
            moveTooltip(e);
            el.onmousemove = moveTooltip;
            el.onmouseleave = function () {
                tip.remove();
                el.onmousemove = null;
                el.onmouseleave = null;
            };
        };
    });
}

attachInventoryTooltips();

document.querySelector('.content-inventory').addEventListener('mouseleave', () => {
    document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
});

const inventoryUL = document.querySelector('.content-inventory .itens ul');
if (inventoryUL) {
    const observer = new MutationObserver(function() {
        setTimeout(attachInventoryTooltips, 100);
    });
    observer.observe(inventoryUL, { childList: true });
}

$(document).ready(function() {
    $(document).off('click', '.content-inventory .itens ul li.slots');
    $(document).on('click', '.content-inventory .itens ul li.slots', function(e) {
        // Remove NOVO badge
        $(this).find('.novo-badge').fadeOut(200);
        
        if($(this).hasClass('slot-vazio')) {
            e.preventDefault();
            return;
        }
        
        var isBau = $(this).attr('data-isbau');
        if(isBau == '1') {
            e.preventDefault();
            // Open chest via AJAX and reorganize inventory
            window.location.href = $(this).find('a').attr('href');
            return;
        }
        
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
        
        var guerreiro = $('#personagemLogged').val();
        var idInventario = $(this).attr('data-idinventario');
        var idItem = $(this).attr('data-idItem');
        var dataEmblema = $(this).attr('data-emblema');
        var dataAdesivo = $(this).attr('data-adesivo');
        
        if(!idInventario || idInventario === 'undefined') return;
        
        if(dataAdesivo == "1"){
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxInventarioAdesivos.php',
                data: { id: idInventario },
                success: function(response) {
                    $('.adesivos ul').html(response);
                    attachInventoryTooltips();
                    setTimeout(function() {
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/ajaxInventario.php',
                            data: { idPersonagem: guerreiro },
                            success: function(res) {
                                $('.content-inventory .itens ul').html(res);
                                attachInventoryTooltips();
                            }
                        });
                    }, 300);
                }
            });
            return;
        }
        
        if(dataEmblema == "1"){
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxInventarioEmblemas.php',
                data: { id: idInventario },
                success: function(response) {
                    $('.emblemas ul').html(response);
                    attachInventoryTooltips();
                    setTimeout(function() {
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/ajaxInventario.php',
                            data: { idPersonagem: guerreiro },
                            success: function(res) {
                                $('.content-inventory .itens ul').html(res);
                                attachInventoryTooltips();
                            }
                        });
                    }, 300);
                }
            });
            return;
        }

        if(isConsumable(idItem)){
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxUsarConsumivel.php',
                data: { id: idInventario },
                dataType: 'json',
                success: function(response) {
                    if(response.success){
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Erro: ' + response.message);
                    }
                }
            });
            return;
        }


        
        $.ajax({
            type: 'POST',
            url: 'ajax/ajaxInventarioEquipados.php',
            data: { id: idInventario },
            success: function(response) {
                $('.equipamentos ul').html(response);
                attachInventoryTooltips();
                setTimeout(function() {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/ajaxInventario.php',
                        data: { idPersonagem: guerreiro },
                        success: function(res) {
                            $('.content-inventory .itens ul').html(res);
                            attachInventoryTooltips();
                        }
                    });
                }, 300);
            }
        });
    });

    $(document).off('click', '.emblemas ul li.slots');
    $(document).on('click', '.emblemas ul li.slots:not(.slot-vazio)', function(e) {
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
        
        var guerreiro = $('#personagemLogged').val();
        var idSlot = $(this).attr('data-id');
        
        if(!idSlot) return;
        
        $.ajax({
            type: 'POST',
            url: 'ajax/ajaxEmblemas.php',
            data: { idSlot: idSlot },
            success: function(response) {
                $('.emblemas ul').html(response);
                attachInventoryTooltips();
                setTimeout(function() {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/ajaxInventario.php',
                        data: { idPersonagem: guerreiro },
                        success: function(res) {
                            $('.content-inventory .itens ul').html(res);
                            attachInventoryTooltips();
                        }
                    });
                }, 300);
            }
        });
    });

    $(document).off('click', '.equipamentos ul li.slots');
    $(document).on('click', '.equipamentos ul li.slots:not(.slot-vazio)', function(e) {
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
        
        var guerreiro = $('#personagemLogged').val();
        var idSlot = $(this).attr('data-id');
        
        if(!idSlot) return;
        
        $.ajax({
            type: 'POST',
            url: 'ajax/ajaxEquipados.php',
            data: { idSlot: idSlot },
            success: function(response) {
                $('.equipamentos ul').html(response);
                attachInventoryTooltips();
                setTimeout(function() {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/ajaxInventario.php',
                        data: { idPersonagem: guerreiro },
                        success: function(res) {
                            $('.content-inventory .itens ul').html(res);
                            attachInventoryTooltips();
                        }
                    });
                }, 300);
            }
        });
    });

    $(document).off('click', '.adesivos ul li.slots');
    $(document).on('click', '.adesivos ul li.slots:not(.slot-vazio)', function(e) {
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
        
        var guerreiro = $('#personagemLogged').val();
        var idSlot = $(this).attr('data-id');
        
        if(!idSlot) return;
        
        $.ajax({
            type: 'POST',
            url: 'ajax/ajaxAdesivos.php',
            data: { idSlot: idSlot },
            success: function(response) {
                $('.adesivos ul').html(response);
                attachInventoryTooltips();
                setTimeout(function() {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/ajaxInventario.php',
                        data: { idPersonagem: guerreiro },
                        success: function(res) {
                            $('.content-inventory .itens ul').html(res);
                            attachInventoryTooltips();
                        }
                    });
                }, 300);
            }
        });
    });

    const pixelsToScroll = 390; 
    window.scrollTo({
        top: pixelsToScroll,
        behavior: "smooth"
    });
});
</script>
