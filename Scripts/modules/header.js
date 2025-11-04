DBH.header = (function() {
    var reload = 0;  // ADD THIS LINE - Initialize reload variable
    
    var init = function() {
        mascaras();
        menu();
        menuMobile();
        loader();
        verificaCacada();
        texteditor();
        
        if($('body').hasClass('pvp') || $('body').hasClass('ranking')){
            verificaAtaque();
        }
        
        if($('body').hasClass('publico')){
            verificaPunicao();
        }
        
        controleValores();
        menuFlutuante();
        menuAdmin();
        removeDoubleClick();
        removeDoubleSubmit();
        
        $('form').preventDoubleSubmit(); 
    },
    texteditor = function(i) {
        if($('.text-editor').length){
            window.onload = function()  {
                CKEDITOR.replace('descricao', {
                    enterMode: CKEDITOR.ENTER_BR,
                    uiColor: '#CCCCCC',
                    language: 'pt-br',
                    filebrowserBrowseUrl: '../assets/ckeditor/kcfinder/browse.php?opener=ckeditor&type=files',
                    filebrowserImageBrowseUrl: '../assets/ckeditor/kcfinder/browse.php?opener=ckeditor&type=images',
                    filebrowserFlashBrowseUrl: '../assets/ckeditor/kcfinder/browse.php?opener=ckeditor&type=flash',
                    filebrowserUploadUrl: '../assets/ckeditor/kcfinder/upload.php?opener=ckeditor&type=files',
                    filebrowserImageUploadUrl: '../assets/ckeditor/kcfinder/upload.php?opener=ckeditor&type=images',
                    filebrowserFlashUploadUrl: '../assets/ckeditor/kcfinder/upload.php?opener=ckeditor&type=flash',
                    toolbar:
                    [
                      { name: 'basicstyles', items : [ 'Bold','Italic','Underline' ] },
                      { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
                      { name: 'paragraph', items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
                      { name: 'styles', items : [ 'Font','FontSize' ] },
                      { name: 'colors', items : [ 'TextColor','BGColor' ] },
                      { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteFromWord','-','Undo','Redo' ] },                             
                      { name: 'tools', items : [ 'Maximize','-','About' ] },
                      { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
                      { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] }
                    ],
                    height: "400px"
                });
            };
        }
    },
    menuMobile = function(){
        $('#dl-menu').dlmenu({
            animationClasses : { classin : 'dl-animate-in-4', classout : 'dl-animate-out-4' }
        });
    },
    removeDoubleClick = function(){        
        $(document).on('click', '.bt-comprar', function(event){
            event.preventDefault();
            $(this).prop('disabled', true);
        });
        
        $(document).bind("contextmenu",function(e){
            return false;
        });
    },
    clicks = function(e) {
        //Desabilita o botao
        e.disabled = true;

        //Habilita novamente após dois segundos (2000) ms
        setTimeout(function(){
            toggleDisabled(e);
        },2000);
    },
    toggleDisabled = function(elem){
        elem.disabled = !elem.disabled;
    },
    removeDoubleSubmit = function(){
        $.fn.preventDoubleSubmit = function() {
          $(this).submit(function() {
            if (this.beenSubmitted)
              return false;
            else
              this.beenSubmitted = true;
          });
        };  
    },
    audioTema = function(){
//        $(document).on('click', '#playTema', function(){
//            console.log('cliquei');
//            document.getElementById("intro").volume-=0.9;
//
//            var x = document.getElementById("intro"); 
//
//            x.play(); 
//        }); 
    },
    playTema = function(){
        $('#playTema').trigger('click');
    },
    mascaras = function(){
        $('.valor-monetario').maskMoney({
            showSymbol:true, 
            symbol:"", 
            decimal: ",", 
            thousands: ""
        });
    },
    menu = function(){
        $('.menu-superior li').each(function(){
            $(this).on('mouseover', function(){
               $('.menu-superior li').removeClass('active');
               $(this).addClass('active');
            });
            $(this).on('mouseout', function(){
               $(this).removeClass('active');
            });
        });
    },
    loader = function(){
        if($('.loader').length > 0){
            setTimeout(function(){ 
                $('.loader').remove();
            }, 3000);
        }
        
        $(".meter > span").each(function() {
            $(this)
              .data("origWidth", $(this).width())
              .width(0)
              .animate({
                width: $(this).data("origWidth") // or + "%" if fluid
              }, 1200);
        });
    },
    verificaCacada = function() {
        var id = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        var baseSite = $('#baseSite').val();
            
        if($('.cacada-running').length > 0){
            $.ajax({
                type: "POST",
                url: baseSite+"ajax/ajaxCacada.php",
                data: data_string,
                success: function (res) {
                    startCountdown(res);
                }
            });
        }
    },
    startCountdown = function(tempo){
        // Se o tempo não for zerado
        if((tempo - 1) >= 0){

            // Pega a parte inteira dos minutos
            var min = parseInt(tempo/60);
            // Calcula os segundos restantes
            var seg = tempo%60;

            // Formata o número menor que dez, ex: 08, 07, ...
            if(min < 10){
                min = "0"+min;
                min = min.substr(0, 2);
            }
            if(seg <= 9){
                seg = "0"+seg;
            }

            // Cria a variável para formatar no estilo hora/cronômetro
            horaImprimivel = '00:' + min + ':' + seg;
            //JQuery pra setar o valor
            
            $(".cacada-running .contador").html(horaImprimivel);

            // Define que a função será executada novamente em 1000ms = 1 segundo
            setTimeout(function(){ 
                startCountdown(tempo);
            }, 1000);
            

            // diminui o tempo
            tempo --;
            
            reload = 0;
        } else {
            if(reload == 0){
                reload = 1;
                location.reload(true);
            }
        }
    },
    verificaAtaque = function() {
        var id = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        var baseSite = $('#baseSite').val();
        
        var url = baseSite+"ajax/ajaxBatalha.php"; 

        $.ajax({
            type: "POST",
            url: url,
            data: data_string,
            success: function (res) {
                startCountdownBatalha(res);
            }
        });
    },
    startCountdownBatalha = function(tempo){
        // Se o tempo não for zerado
        if((tempo - 1) >= 0){
            
            var min = parseInt(tempo/60);
            var horas = parseInt(min/60);
            min = min % 60;
            var seg = tempo%60;

            // Formata o número menor que dez, ex: 08, 07, ...
            if(min < 10){
                min = "0"+min;
                min = min.substr(0, 2);
            }

            if(seg <=9){
                seg = "0"+seg;
            }

            if(horas <=9){
                horas = "0"+horas;
            }

            // Cria a variável para formatar no estilo hora/cronômetro
            horaImprimivel = horas + ':' + min + ':' + seg;
            //JQuery pra setar o valor
            
            $(".pvp-running .contador").html(horaImprimivel);
            $(".pvp-running").show();

            // Define que a função será executada novamente em 1000ms = 1 segundo
            setTimeout(function(){ 
                startCountdownBatalha(tempo);
            }, 1000);
            

            // diminui o tempo
            tempo --;
        } else {
            $(".pvp-running").remove();
        }
    },
    verificaPunicao = function() {
        var id = $('#idAdversario').val();
        var logado = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        var baseSite = $('#baseSite').val();
        
        if(logado != id){
            if($(".punicao-adversario").length > 0){
                $.ajax({
                    type: "POST",
                    url: baseSite+"ajax/ajaxPunicao.php",
                    data: data_string,
                    success: function (res) {
                        startCountdownPunicao(res);
                    }
                });
            }
        }
    },
    startCountdownPunicao = function(tempo){
        // Se o tempo não for zerado
        if((tempo - 1) >= 0){
            
            var min = parseInt(tempo/60);
            var horas = parseInt(min/60);
            min = min % 60;
            var seg = tempo%60;

            // Formata o número menor que dez, ex: 08, 07, ...
            if(min < 10){
                min = "0"+min;
                min = min.substr(0, 2);
            }

            if(seg <=9){
                seg = "0"+seg;
            }

            if(horas <=9){
                horas = "0"+horas;
            }

            // Cria a variável para formatar no estilo hora/cronômetro
            horaImprimivel = horas + ':' + min + ':' + seg;
            //JQuery pra setar o valor
            
            $(".punicao-adversario .contador").html(horaImprimivel);
            $(".punicao-adversario").show();

            // Define que a função será executada novamente em 1000ms = 1 segundo
            setTimeout(function(){ 
                startCountdownPunicao(tempo);
            }, 1000);
            

            // diminui o tempo
            tempo --;
        } else {
            $(".punicao-adversario").remove();
        }
    },
    controleValores = function() {
        $(".valores #mais").click(function(){
            var $contador = $(this).closest('.valores').find('.input-contador');
            $contador.val(parseInt($contador.val())+1); return false;
        });
        $(".valores #menos").click(function(){
            var $contador = $(this).closest('.valores').find('.input-contador');
            if($contador.val()!=1){$contador.val(parseInt($contador.val())-1);} return false;
        });
        
        $(document).on('click', '.valores-treino .mais', function(){
            var preco = $(this).closest('.valores-treino').find('.input-contador').val();
            var unidades = $(this).closest('.valores-treino').find('.unidades').val();
            var soma = $(this).closest('.valores-treino').find('.soma').val();
            
            $(this).closest('.valores-treino').find('.input-contador').val(parseInt(soma) + 99);
            
            var nova_soma = $(this).closest('.valores-treino').find('.soma').val();
            
            if(parseInt(soma) == 99){
                $(this).closest('.valores-treino').find('.soma').val(parseInt(nova_soma) + parseInt(preco) + 99);
            } else {
                $(this).closest('.valores-treino').find('.soma').val(parseInt(nova_soma) + parseInt(preco));
            }
            
            $(this).closest('form').find('.qtd-unidades em strong').html(parseInt(unidades)+1);
            $(this).closest('.valores-treino').find('.unidades').val(parseInt(unidades)+1); 
            
            return false;
        });
        
        $(".valores-treino #menos").click(function(){
            document.location.reload(true);
        });
    },
    menuFlutuante = function(){
        $('.menu-flutuante li').on('mouseover', function(){
            $('.menu-flutuante li span').removeClass('active');
            $(this).find('span').addClass('active'); 
        });
        
        $('.menu-flutuante li').on('mouseout', function(){
            $('.menu-flutuante li span').removeClass('active');
            $(this).find('span').removeClass('active'); 
        });
    },
    menuAdmin = function(){
        $('.menu-admin .open-menu').on('click', function(){
            $('.menu-admin').toggleClass('active');
        });
        
        $('.menu-admin .pai').on('click', function(){
            $('.menu-admin .sub').removeClass('active');
            $(this).find('.sub').addClass('active');
        });
    }
    
    return {
        init: init,
        audioTema: audioTema,
        playTema: playTema,
        clicks: clicks
    }
}());