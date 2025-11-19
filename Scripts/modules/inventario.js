DBH.inventario = (function() {
    var unequipInProgress = false;
    var unequipAdesivoInProgress = false;

    var init = function() {
        loadInventory();
        loadEquipados();
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
                console.log("Response:", xhr.responseText);
            }
        });
    },

    loadEquipados = function() {
        var guerreiro = $('#personagemLogged').val();
        $.ajax({
            type: "POST",
            url: "ajax/ajaxInventarioEquipado.php",
            data: { idPersonagem: guerreiro, loadOnly: 1 },
            success: function (res) {
                $(".equipados ul").html(res);
            },
            error: function(xhr, status, error) {
                console.error("Equipados load error:", error);
            }
        });
    },

    loadAdesivos = function() {
        var guerreiro = $('#personagemLogged').val();
        $.ajax({
            type: "POST",
            url: "ajax/ajaxAdesivos.php",
            data: { idPersonagem: guerreiro, loadOnly: 1 },
            success: function (res) {
                $(".adesivos ul").html(res);
            },
            error: function(xhr, status, error) {
                console.error("Adesivos load error:", error);
            }
        });
    },
    
    selectItem = function(){
        // Hover tooltips
        $(document).on('mouseover', '.content-inventory .itens ul li', function(){
            $(this).find('.informacoes').show();
        });
        
        $(document).on('mouseout', '.content-inventory .itens ul li', function(){
            $(this).find('.informacoes').hide();
        });
        

        // ============================================
        // EQUIP FROM INVENTORY
        // ============================================
        $(document).on('click', '.content-inventory .itens ul li', function(event){
            if(!$(this).hasClass('slot-vazio')){
                // Don't intercept bau (chest) clicks
                if($(this).find('span').hasClass('bau')){
                    return;
                }
                
                event.preventDefault();
                event.stopPropagation();
                
                var guerreiro = $('#personagemLogged').val();
                var id = $(this).attr('dataidItem');
                var dataid = $(this).attr('dataid');
                var dataAdesivo = $(this).attr('dataadesivo');
                var idInventario = $(this).attr('dataidinventario');  // <-- ADD THIS LINE!
                
                // INCLUDE idInventario in the POST data
               var idInventario = $(this).attr('dataidinventario');  // <-- make sure this line exists
                var data_string = 'id=' + id + '&idp=' + dataid + '&idInventario=' + idInventario + '&idPersonagem=' + guerreiro;

                
                console.log('Equipping item - ID:', id, 'Adesivo:', dataAdesivo, 'idInventario:', idInventario);
                
                if(dataAdesivo == 1){
                    // Equip to adesivos
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxAdesivos.php",
                        data: data_string,
                        success: function (res) {
                            $(".adesivos ul").html(res);
                        },
                        error: function(xhr, status, error) {
                            console.error("Adesivo equip error:", error);
                            console.log("Response:", xhr.responseText);
                        }
                    });
                    setTimeout(function(){ loadInventory(); }, 400);
                } else {
                    // Equip to equipment/emblem slots
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventarioEquipado.php",
                        data: data_string,
                        success: function (res) {
                            $(".equipados ul").html(res);
                        },
                        error: function(xhr, status, error) {
                            console.error("Item equip error:", error);
                            console.log("Response:", xhr.responseText);
                        }
                    });
                    setTimeout(function(){ loadInventory(); }, 400);
                }
            }
        });

        
        // ============================================
        // UNEQUIP FROM EQUIPPED SLOTS
        // ============================================
        $(document).on('click', '.equipados ul li.equipped', function(event){
            event.preventDefault();
            event.stopPropagation();
            
            if($(this).hasClass('slot-vazio') || unequipInProgress){
                return;
            }
            
            unequipInProgress = true;
            var $slot = $(this);
            $slot.css('opacity', '0.5');
            
            var guerreiro = $('#personagemLogged').val();
            var id = $slot.attr('dataid');
            var idItem = $slot.attr('dataidItem');
            
            console.log('Unequipping - Slot ID:', id, 'Item ID:', idItem);
            
            var data_string = 'id=' + id + '&idItem=' + idItem + '&idPersonagem=' + guerreiro;
            
            $.ajax({
                type: "POST",
                url: "ajax/ajaxEquipado.php",
                data: data_string,
                success: function (res) {
                    $(".equipados ul").html(res);
                },
                error: function(xhr, status, error) {
                    console.error("Unequip error:", error);
                    console.log("Response:", xhr.responseText);
                    $slot.css('opacity', '1');
                },
                complete: function() {
                    setTimeout(function() {
                        unequipInProgress = false;
                        loadInventory();
                    }, 500);
                }
            });
        });
        
        // ============================================
        // UNEQUIP ADESIVOS
        // ============================================
        $(document).on('click', '.adesivos ul li.adesivo', function(event){
            event.preventDefault();
            event.stopPropagation();
            
            if($(this).hasClass('slot-vazio') || unequipAdesivoInProgress){
                return;
            }
            
            unequipAdesivoInProgress = true;
            var $slot = $(this);
            $slot.css('opacity', '0.5');
            
            var guerreiro = $('#personagemLogged').val();
            var id = $slot.attr('dataid');
            var idItem = $slot.attr('dataidItem');
            
            console.log('Unequipping adesivo - Slot ID:', id, 'Item ID:', idItem);
            
            var data_string = 'id=' + id + '&idItem=' + idItem + '&idPersonagem=' + guerreiro;
            
            $.ajax({
                type: "POST",
                url: "ajax/ajaxAdesivos.php",
                data: data_string,
                success: function (res) {
                    $(".adesivos ul").html(res);
                },
                error: function(xhr, status, error) {
                    console.error("Adesivo unequip error:", error);
                    console.log("Response:", xhr.responseText);
                    $slot.css('opacity', '1');
                },
                complete: function() {
                    setTimeout(function() {
                        unequipAdesivoInProgress = false;
                        loadInventory();
                    }, 500);
                }
            });
        });
    };
    
    return {
        init: init
    };
})();
