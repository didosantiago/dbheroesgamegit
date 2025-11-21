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
            
            console.log("Item clicked:", {id, dataAdesivo, dataEmblema, dataConsumivel}); // DEBUG
            
            if (typeof id !== 'undefined' && id !== false) {
                if(dataAdesivo == 1){
                    // Equipar Adesivo
                    console.log("Equipando adesivo..."); // DEBUG
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
                    // Equipar Emblema
                    console.log("Equipando emblema..."); // DEBUG
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
                    // Consumível - usar item
                    console.log("Usando consumível..."); // DEBUG
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventarioEquipado.php",
                        data: { id: id },
                        success: function (res) {
                            loadInventory();
                        }
                    });
                } else {
                    // Equipar Equipamento Normal
                    console.log("Equipando equipamento..."); // DEBUG
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
            console.log("Desequipando emblema slot:", idSlot); // DEBUG
            
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
            console.log("Desequipando equipamento slot:", idSlot); // DEBUG
            
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
            console.log("Desequipando adesivo slot:", idSlot); // DEBUG
            
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
    };
    
    return {
        init: init,
        loadInventory: loadInventory,
        loadEmblemas: loadEmblemas,
        loadEquipamentos: loadEquipamentos,
        loadAdesivos: loadAdesivos
    };
})();
