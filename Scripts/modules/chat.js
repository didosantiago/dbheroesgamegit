DBH.chat = (function() {
    var init = function() {
        
    },
    equipes = function(){
        $('.chat-equipe .chat-header').on('click', function(){
            $(this).closest('.chat-equipe').toggleClass('open');
        });
        
        $('#btnEnviarMensagem').on('click', function(){
            var idEquipe = $('#idEquipe').val();
            var idMembro = $('#idMembro').val();
            var baseSite = $('#baseSite').val();
            var tipo = $('#tipo').val();
            var mensagem = $('#mensagemChatEquipe').val();
            var data_string = 'idEquipe=' + idEquipe + '&idMembro=' + idMembro + '&mensagem=' + mensagem + '&tipo=' + tipo;

            $.ajax({
                type: 'POST',
                url: baseSite+"ajax/ajaxChatEquipe.php",
                data: data_string,
                success: function (res) {
                    $('#mensagemChatEquipe').val('');
                }
            });
        });
        
        setInterval(function(){ 
            var idEquipe = $('#idEquipe').val();
            var tipo = 'monitora';
            var baseSite = $('#baseSite').val();
            
            var data_string = 'idEquipe=' + idEquipe + '&tipo=' + tipo;
            
            $.ajax({
                type: 'POST',
                url: baseSite+"ajax/ajaxChatEquipe.php",
                data: data_string,
                success: function (res) {
                    $('.chat-conversation').html(res);
                }
            });
        }, 1000);
        
        if($('.chat-conversation').length > 0){
            const ps = new PerfectScrollbar('.chat-conversation');
        }
    },
    amigos = function(){
        $('.chat-messenger .chat-header').on('click', function(){
            $(this).closest('.chat-messenger').toggleClass('open');
            
            var idPersonagem = $('#idPersonagem').val();
            var idAmigo = $('#idAmigo').val();
            var baseSite = $('#baseSite').val();
            var tipo = 'ler';
            var data_string = 'idPersonagem=' + idPersonagem + '&idAmigo=' + idAmigo + '&tipo=' + tipo;

            $.ajax({
                type: 'POST',
                url: baseSite+"ajax/ajaxChat.php",
                data: data_string,
                success: function (res) {
                    
                }
            });
            
            $('.mensagens-pendentes').remove();
        });
        
        $('#btnEnviarMensagem').on('click', function(){
            var idPersonagem = $('#idPersonagem').val();
            var idAmigo = $('#idAmigo').val();
            var baseSite = $('#baseSite').val();
            var tipo = $('#tipo').val();
            var mensagem = $('#mensagemChat').val();
            var data_string = 'idPersonagem=' + idPersonagem + '&idAmigo=' + idAmigo + '&mensagem=' + mensagem + '&tipo=' + tipo;

            $.ajax({
                type: 'POST',
                url: baseSite+"ajax/ajaxChat.php",
                data: data_string,
                success: function (res) {
                    $('#mensagemChat').val('');
                }
            });
        });
        
        setInterval(function(){ 
            var idPersonagem = $('#idPersonagem').val();
            var idAmigo = $('#idAmigo').val();
            var baseSite = $('#baseSite').val();
            var tipo = 'monitora';
            
            var data_string = 'idPersonagem=' + idPersonagem + '&idAmigo=' + idAmigo + '&tipo=' + tipo;
            
            $.ajax({
                type: 'POST',
                url: baseSite+"ajax/ajaxChat.php",
                data: data_string,
                success: function (res) {
                    $('.chat-conversation').html(res);
                }
            });
        }, 1500);
        
        if($('.chat-conversation').length > 0){
            const ps = new PerfectScrollbar('.chat-conversation');
            
            var div = $('.chat-conversation')[0];
            div.scrollTop = div.scrollHeight;
        }
    }
    
    return {
        init: init,
        equipes: equipes,
        amigos: amigos
    }
}());