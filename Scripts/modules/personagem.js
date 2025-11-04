DBH.personagem = (function() {
    var reload = 0;  // ADD THIS LINE - Initialize reload variable
    var init = function() {
        itens();
    },
    itens = function(){
        $('.item-personagem').on('click', function(){
            $('.item-personagem').removeClass('active');
            $(this).toggleClass('active');
            var foto = $(this).attr('dataFoto');
            $('#fotoPersonagem').val(foto);
            $('html, body').animate({scrollTop: $('.btn-step-1').offset().top}, 'slow');
        });
        
        $('.btn-step-1').on('click', function(){
            $('.valida').remove();
            var radioValue = $("input[name='idPersonagem']:checked").val();
            
            if(radioValue){
                $('#load-wizard').show();

                setTimeout(function(){ 
                    $('#load-wizard').hide();
                    $('#etapa-1').hide();
                    $('#wizard-personagem ul li').removeClass('active');
                    $('.lk-etapa-2').addClass('active');
                    $('#etapa-2').show();
                }, 2000);
            } else {
                $('.conteudo #wizard-personagem ul').after('<div class="valida">Escolha o personagem para avançar a próxima etapa.</div>');
            }
        });
        
        $('.item-planetas').on('click', function(){
            $('.valida').remove();
            if($('#nomeGuerreiro').val() != ''){
                $('.item-planetas').removeClass('active');
                $(this).toggleClass('active');
                $('html, body').animate({scrollTop: $('.btn-step-2').offset().top}, 'slow');
            } else {
                $('.conteudo #wizard-personagem ul').after('<div class="valida">Digite um nome para o seu guerreiro antes de avançar para a próxima etapa.</div>');
                $('#nomeGuerreiro').focus();
            }
        });
        
        $('.btn-step-2').on('click', function(){
            $('.valida').remove();
            var radioValue = $("input[name='idPlaneta']:checked").val();
            
            if($('#nomeGuerreiro').val() != ''){
                if(radioValue){
                    $('#load-wizard').show();

                    setTimeout(function(){ 
                        $('#load-wizard').hide();
                        $('#etapa-2').hide();
                        $('#wizard-personagem ul li').removeClass('active');
                        $('.lk-etapa-3').addClass('active');
                        $('#etapa-3').show();
                    }, 2000);
                } else {
                    $('.conteudo #wizard-personagem ul').after('<div class="valida">Escolha o planeta para avançar a próxima etapa.</div>');
                }
            } else {
                $('.conteudo #wizard-personagem ul').after('<div class="valida">Digite um nome para o seu guerreiro antes de avançar para a próxima etapa.</div>');
                $('#nomeGuerreiro').focus();
            }
        });
        
        $('.lk-etapa-1').on('click', function(){
            $('.valida').remove();
            $('#etapa-2').hide();
            $('#etapa-3').hide();
            $('#load-wizard').show();
            $('#wizard-personagem ul li').removeClass('active');
            
            setTimeout(function(){
                $('.lk-etapa-1').addClass('active');
                $('#load-wizard').hide();
                $('#etapa-1').show();
            }, 1000);
        });
        
        $('.lk-etapa-2').on('click', function(){
            $('.valida').remove();
            var radioValue = $("input[name='idPersonagem']:checked").val();
            
            if(radioValue){
                $('#etapa-1').hide();
                $('#etapa-3').hide();
                $('#load-wizard').show();
                $('#wizard-personagem ul li').removeClass('active');

                setTimeout(function(){ 
                    $('.lk-etapa-2').addClass('active');
                    $('#load-wizard').hide();
                    $('#etapa-2').show();
                }, 1000);
            } else {
                $('.conteudo #wizard-personagem ul').after('<div class="valida">Escolha o personagem para avançar a próxima etapa.</div>');
            }
        });
        
        $('.lk-etapa-3').on('click', function(){
            $('.valida').remove();
            var radioValue = $("input[name='idPlaneta']:checked").val();
            
            if($('#nomeGuerreiro').val() != ''){
                if(radioValue){
                    $('#etapa-1').hide();
                    $('#etapa-2').hide();
                    $('#load-wizard').show();
                    $('#wizard-personagem ul li').removeClass('active');

                    setTimeout(function(){ 
                        $('.lk-etapa-3').addClass('active');
                        $('#load-wizard').hide();
                        $('#etapa-3').show();
                    }, 1000);
                } else {
                    $('.conteudo #wizard-personagem ul').after('<div class="valida">Escolha o planeta para avançar a próxima etapa.</div>');
                }
            } else {
                $('.conteudo #wizard-personagem ul').after('<div class="valida">Digite um nome para o seu guerreiro antes de avançar para a próxima etapa.</div>');
                $('#nomeGuerreiro').focus();
            }
        });
        
        $('#nomeGuerreiro').on('keyup', function(){
            var texto = $(this).val();
            var new_texto = texto.replace(/^\s+|\s+$/g, "");
            $(this).val(new_texto);
        });
        
        $('#nomeGuerreiro').on('focus', function(){
            $(this).val('');
        });
        
        $('#nomeGuerreiro').on('keypress', function(){
            
            var texto = $(this).val();
            var new_texto = texto.replace(/^\s+|\s+$/g, "");
            $(this).val(new_texto);
        });
    },
    trocaGuerreiro = function(){
        $('.item-personagem').on('click', function(){
            $('.item-personagem').removeClass('active');
            $(this).toggleClass('active');
            $('html, body').animate({scrollTop: $('.btn-confirmar').offset().top}, 'slow');
        });
    },
    meuPersonagem = function(){
        $('html, body').animate({scrollTop: $('.lista-meus-personagens').offset().top}, 'slow');
        
        $('label.meu-personagem').on('click', function(){
            $('label.meu-personagem').removeClass('active-gray');
            $(this).toggleClass('active-gray');
            var id = $(this).attr('dataid');
            var data_string = 'id=' + id;
            var baseSite = $('#baseSite').val();
            
            $.ajax({
                type: "POST",
                url: baseSite+"ajax/ajaxPersonagens.php",
                data: data_string,
                cache: true,
                async: true, // NO LONGER ALLOWED TO BE FALSE BY BROWSER
                success: function (res) {
                    console.log(res);
                    $(".personagem-atual").html(res);

                    if(detectar_mobile() == true){
                        $('html, body').animate({scrollTop: $('.personagem-atual > .info').offset().top}, 'slow');
                    } else {
                        $('html, body').animate({scrollTop: $('.conteudo').offset().top}, 'slow');
                    }
                }
            });
        });
    },
    detectar_mobile = function() { 
        if( navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)
        ){
            return true;
        } else {
            return false;
        }
    },
    jogar = function(){
    $(document).on('click', '.bt-jogar', function(){
        if($('.loader').length <= 0){
            $('body').prepend('<div class="loader">'+
                            '<img src="assets/loader2.gif" alt="Carregando Game..." />'+
                            '<p>Carregando o Jogo, Aguarde...</p>'+
                        '</div>'); 
                
            var id = $(this).attr('dataid');
            var data_string = 'id=' + id;

            $.ajax({
                type: "POST",
                url: "ajax/ajaxJogar.php",
                data: data_string,
                success: function (res) {
                    
                }
            });
                
            setTimeout(function(){ 
                var base = window.location.href.split('/meus-personagens');
                var url = base[0] + '/portal';
                window.location.href = url;
            }, 3000);
            
            setTimeout(function(){ 
                $('.loader').remove();
            }, 3000);
        }
    });
},
    foto = function(){
        $('.fotos-personagem li').on('click', function(){
            if(!$(this).hasClass('bloqueado')){
                $('#load-game').show();

                var personagem = $('#personagemLogged').val()
                var foto = $(this).attr('dataImage');
                var data_string = 'foto=' + foto + '&id=' + personagem;

                $.ajax({
                    type: "POST",
                    url: "ajax/ajaxFotos.php",
                    data: data_string,
                    success: function (res) {
                        location.reload(true);
                    }
                });
            }
        });
    }
    
    return {
        init: init,
        meuPersonagem: meuPersonagem,
        jogar: jogar,
        foto: foto,
        trocaGuerreiro: trocaGuerreiro
    }
}());