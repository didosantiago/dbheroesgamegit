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

function attachInventoryTooltips() {
    document.querySelectorAll('.slot-item').forEach(function(el){
        el.onmouseenter = function(e){
            document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
            var itemId = this.getAttribute('data-item');
            var data = window.INVENTORY_ITEMS[itemId];
            if (!data) return;
            var tip = document.createElement("div");
            tip.className = "item-tooltip";
            tip.innerHTML =
                "<strong>" + data.nome + "</strong><br>" +
                (data.forca ? "Força: <span style='color:#5b5'>+" + data.forca + "</span><br>" : "") +
                (data.agilidade ? "Agilidade: <span style='color:#5b5'>+" + data.agilidade + "</span><br>" : "") +
                (data.habilidade ? "Habilidade: <span style='color:#5b5'>+" + data.habilidade + "</span><br>" : "") +
                (data.resistencia ? "Resistência: <span style='color:#5b5'>+" + data.resistencia + "</span><br>" : "") +
                (data.sorte ? "Sorte: <span style='color:#5b5'>+" + data.sorte + "</span><br>" : "");
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
// Call on page load
attachInventoryTooltips();
// Remove tooltip on inventory area mouseleave
document.querySelector('.content-inventory').addEventListener('mouseleave', () => {
    document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
});

$(document).ready(function() {
    $(document).off('click', '.content-inventory .itens ul li.slots');
    $(document).on('click', '.content-inventory .itens ul li.slots', function(e) {
        if($(this).hasClass('slot-vazio') ||
           $(this).find('span').hasClass('bau') ||
           $(this).attr('dataadesivo') == "1") return;
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
        var guerreiro = $('#personagemLogged').val();
        var idInventario = $(this).attr('dataidinventario');
        var dataEmblema = $(this).attr('dataemblema');
        if(!idInventario || idInventario === 'undefined') return;
        var ajaxUrl = (dataEmblema == "1") ?
            'ajax/ajaxInventarioEmblemas.php' :
            'ajax/ajaxInventarioEquipados.php';
        var targetDiv = (dataEmblema == "1") ?
            '.emblemas ul' :
            '.equipamentos ul';
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: { id: idInventario },
            success: function(response) {
                $(targetDiv).html(response);
                // FIX INSERTION: attach tooltip after updates
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

    $(document).off('click', '.emblemas ul li.has-item');
    $(document).on('click', '.emblemas ul li.has-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
        var guerreiro = $('#personagemLogged').val();
        var idSlot = $(this).attr('dataid');
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

    $(document).off('click', '.equipamentos ul li.has-item');
    $(document).on('click', '.equipamentos ul li.has-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.item-tooltip').forEach(x => x.remove());
        var guerreiro = $('#personagemLogged').val();
        var idSlot = $(this).attr('dataid');
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

    // You can also add a similar block for adesivos if needed
});
</script>