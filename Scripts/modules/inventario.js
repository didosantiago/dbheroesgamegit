DBH.inventario = (function() {

    var unequipInProgress = false;

    var init = function() {
        loadInventory();
        loadEmblemas();
        loadEquipamentos();
        loadAdesivos();
        selectItem();
    },

    loadInventory = function() {
        var guerreiro = $('#personagemLogged').val();
        $.ajax({
            type: "POST",
            url: "ajax/ajaxInventario.php",
            data: { idPersonagem: guerreiro },
            success: function (res) {
                $(".content-inventory .itens ul").html(res);
            },
            error: function(xhr, status, error) {
                console.error("Inventory load error:", error);
            }
        });
    },

    loadEmblemas = function() {
        var guerreiro = $('#personagemLogged').val();
        $.ajax({
            type: "POST",
            url: "ajax/ajaxInventarioEmblemas.php",
            data: { idPersonagem: guerreiro, loadOnly: 1 },
            success: function (res) {
                $(".emblemas ul").html(res);
            }
        });
    },

    loadEquipamentos = function() {
        var guerreiro = $('#personagemLogged').val();
        $.ajax({
            type: "POST",
            url: "ajax/ajaxInventarioEquipamentos.php",
            data: { idPersonagem: guerreiro, loadOnly: 1 },
            success: function (res) {
                $(".equipamentos ul").html(res);
            }
        });
    },

    loadAdesivos = function() {
        var guerreiro = $('#personagemLogged').val();
        $.ajax({
            type: "POST",
            url: "ajax/ajaxInventarioAdesivos.php",
            data: { idPersonagem: guerreiro, loadOnly: 1 },
            success: function (res) {
                $(".adesivos ul").html(res);
            }
        });
    },

    selectItem = function() {
        $(document).on('click', '.content-inventory .itens ul li', function() {
            var id = $(this).attr('dataid');
            var dataAdesivo = $(this).attr('dataadesivo');
            var dataEmblema = $(this).attr('dataemblema');
            var dataConsumivel = $(this).attr('dataconsumivel');
            console.log("Item clicked:", {id, dataAdesivo, dataEmblema, dataConsumivel});
            
            if (typeof id !== 'undefined' && id !== false) {
                if(dataAdesivo == 1){
                    console.log("Equipando adesivo...");
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventarioAdesivos.php",
                        data: { id: id },
                        success: function (res) {
                            $(".adesivos ul").html(res);
                            loadInventory();
                        }
                    });
                } else if(dataEmblema == 1){
                    console.log("Equipando emblema...");
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventarioEmblemas.php",
                        data: { id: id },
                        success: function (res) {
                            $(".emblemas ul").html(res);
                            loadInventory();
                        }
                    });
                } else if(dataConsumivel == 1){
                    console.log("Usando consumível...");
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventarioEquipado.php",
                        data: { id: id },
                        success: function (res) {
                            loadInventory();
                        }
                    });
                } else {
                    console.log("Equipando equipamento...");
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventarioEquipamentos.php",
                        data: { id: id },
                        success: function (res) {
                            $(".equipamentos ul").html(res);
                            loadInventory();
                        }
                    });
                }
            }
        });

        // Desequipar Emblemas
        $(document).on('click', '.emblemas ul li.emblema', function() {
            if (unequipInProgress) return;
            unequipInProgress = true;
            var idSlot = $(this).attr('dataid');
            console.log("Desequipando emblema slot:", idSlot);
            
            if (typeof idSlot !== 'undefined' && idSlot !== false) {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajaxEmblemas.php",
                    data: { idSlot: idSlot },
                    success: function (res) {
                        $(".emblemas ul").html(res);
                        loadInventory();
                        unequipInProgress = false;
                    },
                    error: function() {
                        unequipInProgress = false;
                    }
                });
            } else {
                unequipInProgress = false;
            }
        });

        // Desequipar Equipamentos
        $(document).on('click', '.equipamentos ul li.equipped', function() {
            if (unequipInProgress) return;
            unequipInProgress = true;
            var idSlot = $(this).attr('dataid');
            console.log("Desequipando equipamento slot:", idSlot);
            
            if (typeof idSlot !== 'undefined' && idSlot !== false) {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajaxEquipamentos.php",
                    data: { idSlot: idSlot },
                    success: function (res) {
                        $(".equipamentos ul").html(res);
                        loadInventory();
                        unequipInProgress = false;
                    },
                    error: function() {
                        unequipInProgress = false;
                    }
                });
            } else {
                unequipInProgress = false;
            }
        });

        // Desequipar Adesivos
        $(document).on('click', '.adesivos ul li.adesivo', function() {
            if (unequipInProgress) return;
            unequipInProgress = true;
            var idSlot = $(this).attr('dataid');
            console.log("Desequipando adesivo slot:", idSlot);
            
            if (typeof idSlot !== 'undefined' && idSlot !== false) {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajaxAdesivos.php",
                    data: { idSlot: idSlot },
                    success: function (res) {
                        $(".adesivos ul").html(res);
                        loadInventory();
                        unequipInProgress = false;
                    },
                    error: function() {
                        unequipInProgress = false;
                    }
                });
            } else {
                unequipInProgress = false;
            }
        });

        // =========================================
        // LOJA PURCHASE HANDLER
        // =========================================
        $(document).on('click', '.btn-comprar-loja', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var idProduto = $button.data('idproduto');
            var guerreiro = $('#personagemLogged').val();
            
            if (!idProduto) {
                console.error("ID do produto não encontrado!");
                return;
            }
            
            console.log("Comprando produto:", idProduto, "Guerreiro:", guerreiro);
            
            // Disable button during purchase
            $button.prop('disabled', true).text('Processando...');
            
            $.ajax({
                type: "POST",
                url: "ajax/ajaxAdquirirItem.php",
                data: { 
                    id: idProduto,
                    idPersonagem: guerreiro
                },
                success: function(response) {
                    console.log("Compra bem-sucedida!");
                    // Reload page to update coins/inventory
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error("Erro na compra:", error);
                    console.error("Response:", xhr.responseText);
                    alert('Erro ao processar compra. Tente novamente.');
                    $button.prop('disabled', false).text('COMPRAR');
                }
            });
        });


    };

    return {
        init: init,
        loadInventory: loadInventory,
        loadEmblemas: loadEmblemas,
        loadEquipamentos: loadEquipamentos,
        loadAdesivos: loadAdesivos
    };

})();

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
                "<strong>"+data.nome+"</strong>"
                +(data.forca ? "<div>Força: +"+data.forca+"</div>" : "")
                +(data.agilidade ? "<div>Agilidade: +"+data.agilidade+"</div>" : "")
                +(data.habilidade ? "<div>Habilidade: +"+data.habilidade+"</div>" : "")
                +(data.resistencia ? "<div>Resistência: +"+data.resistencia+"</div>" : "")
                +(data.sorte ? "<div>Sorte: +"+data.sorte+"</div>" : "");
            document.body.appendChild(tip);
            function moveTooltip(ev){
                tip.style.position='fixed';
                tip.style.left = (ev.clientX+20)+'px';
                tip.style.top = (ev.clientY+20)+'px';
                tip.style.zIndex=10000;
            }
            moveTooltip(e);
            el.onmousemove = moveTooltip;
            el.onmouseleave = function() {
                tip.remove();
                el.onmousemove = null;
                el.onmouseleave = null;
            };
        };
    });
}
