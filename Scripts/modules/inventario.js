DBH.inventario = (function() {
    var init = function() {
        selectItem();   
    },
    selectItem = function(){
        $(document).on('mouseover', '.content-inventory .itens ul li', function(){
            $(this).find('.informacoes').show();
        });
        
        $(document).on('mouseout', '.content-inventory .itens ul li', function(){
            $(this).find('.informacoes').hide();
        });
        
        $(document).on('click', '.content-inventory .itens ul li', function(event){
            if(!$(this).hasClass('slot-vazio')){
                if(!$(this).find('span').hasClass('bau')){
                    event.preventDefault();
                    $(this).prop('disabled', true);
                    var guerreiro = $('#personagemLogged').val();
                    var id = $(this).attr('dataidItem');
                    var dataid = $(this).attr('dataid');
                    var dataAdesivo = $(this).attr('dataadesivo');
                    var data_string = 'id=' + id + '&idp=' + dataid + '&idPersonagem=' + guerreiro;
                    var data_idp = 'idP=' + dataid + '&idPersonagem=' + guerreiro;;

                    if(dataAdesivo == 1){
                        $.ajax({
                            type: "POST",
                            url: "ajax/ajaxInventarioAdesivos.php",
                            data: data_string,
                            success: function (res) {
                                $(this).addClass('clicked');
                                $(".adesivos ul").html(res);
                            }
                        });
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "ajax/ajaxInventarioEquipado.php",
                            data: data_string,
                            success: function (res) {
                                $(this).addClass('clicked');
                                $(".equipados ul").html(res);
                            }
                        });
                    }

                    setTimeout(function(){
                        $.ajax({
                            type: "POST",
                            url: "ajax/ajaxInventario.php",
                            data: data_idp,
                            success: function (res) {
                                $(".content-inventory .itens ul").html(res);
                            }
                        });
                    }, 400);
                }
            }
        });
        
        $(document).on('click', '.equipados ul li', function(event){
            event.preventDefault();
            
            $(this).prop('disabled', true);
            
            if(!$(this).hasClass('slot-vazio')){
                var guerreiro = $('#personagemLogged').val();
                var id = $(this).attr('dataid');
                var idItem = $(this).attr('dataidItem');
                var data_string = 'id=' + id + '&idItem=' + idItem + '&idPersonagem=' + guerreiro;;

                $.ajax({
                    type: "POST",
                    url: "ajax/ajaxEquipado.php",
                    data: data_string,
                    success: function (res) {
                        $(".equipados ul").html(res);
                    }
                });

                setTimeout(function(){
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventario.php",
                        data: data_string,
                        success: function (res) {
                            $(".content-inventory .itens ul").html(res);
                        }
                    });
                }, 400);
            }
        });
        
        $(document).on('click', '.adesivos ul li', function(event){
            event.preventDefault();
            
            $(this).prop('disabled', true);
            
            if(!$(this).hasClass('slot-vazio')){
                var guerreiro = $('#personagemLogged').val();
                var id = $(this).attr('dataid');
                var idItem = $(this).attr('dataidItem');
                var data_string = 'id=' + id + '&idItem=' + idItem + '&idPersonagem=' + guerreiro;;

                $.ajax({
                    type: "POST",
                    url: "ajax/ajaxAdesivos.php",
                    data: data_string,
                    success: function (res) {
                        $(".adesivos ul").html(res);
                    }
                });

                setTimeout(function(){
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajaxInventario.php",
                        data: data_string,
                        success: function (res) {
                            $(".content-inventory .itens ul").html(res);
                        }
                    });
                }, 400);
            }
        });
    }
    
    return {
        init: init
    }
}());