var DBH = DBH || {};

DBH.common = {
	$body: $('body'),
	bxProduct: {},
	bxProducThumb: {},
	owlProductMobile: {},
	createCrossDomainRequest: function(url, handler) {
		var request,
			isIE8 = window.XDomainRequest ? true : false

		if (isIE8) {
			request = new window.XDomainRequest();
		} else {
			request = new XMLHttpRequest();
		}
		return request;
	},
	removeAccents: function(newStringComAcento) {
		var string = newStringComAcento;
		var mapaAcentosHex = {
			a: /[\xE0-\xE6]/g,
			e: /[\xE8-\xEB]/g,
			i: /[\xEC-\xEF]/g,
			o: /[\xF2-\xF6]/g,
			u: /[\xF9-\xFC]/g,
			c: /\xE7/g,
			n: /\xF1/g
		};

		for (var letra in mapaAcentosHex) {
			var expressaoRegular = mapaAcentosHex[letra];
			string = string.replace(expressaoRegular, letra);
		}

		return string.replace(/\(?\d\)?/g, '').trim().replace(/\s|\./g, '-').toLowerCase(); //retira o "(numero)"
	},
	remove_class: function(element, _regex) {
		var regex = new RegExp(_regex);
		element.removeClass(function(index, classNames) {
			var current_classes = classNames.split(' '),
				font_remove = [];

			$.each(current_classes, function(index, item_class) {
				if (regex.test(item_class)) {
					font_remove.push(item_class);
				}
			});

			return font_remove.join(' ');
		});
	},
	formatCurrency: function(int) {
		var tmp = int + '';
		tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
		if (tmp.length > 6)
			tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

		return 'R$ ' + tmp;
	},
	sortUsingNestedText: function(parent, childSelector, keySelector) {
        var items = parent.children(childSelector).sort(function(a, b) {
            var vA = $(keySelector, a).text();
            var vB = $(keySelector, b).text();
            return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
        });
        parent.append(items);
    },
    justNumbers: function(string) {
	    var numsStr = string.replace(/[^0-9]/g,'');
	    return parseInt(numsStr);
    }
};DBH.assistir = (function() {
    var init = function() {
        slider();
    },
    slider = function(){
        $('.slider-videos').slick({
            dots: true,
            infinite: true,
            speed: 500,
            fade: true,
            autoplay: true,
            cssEase: 'linear',
            prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>'
        });
        
        $('.slider-videos').show();
    }
    
    return {
        init: init
    }
}());;DBH.chat = (function() {
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
            new PerfectScrollbar('.chat-conversation');
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
            new PerfectScrollbar('.chat-conversation');
            
            var div = $('.chat-conversation')[0];
            div.scrollTop = div.scrollHeight;
        }
    }
    
    return {
        init: init,
        equipes: equipes,
        amigos: amigos
    }
}());;DBH.equipes = (function() {
    var init = function() {
        itens();
    },
    itens = function(){
        $('.lista-bandeiras label').on('click', function(){
            $('.lista-bandeiras label').removeClass('active');
            $(this).toggleClass('active');
        });
    }
    
    return {
        init: init
    }
}());;DBH.forum = (function() {
    var init = function() {
        selects();
        texteditor();
    },
    selects = function(){
        $('#idCategoria').on('change', function(){
            searchSubCategorias();
        });
    },
    searchSubCategorias = function() {
        var id = $('#idCategoria').val();
        var data_string = 'id=' + id;

        $.ajax({
            type: "POST",
            url: "../ajax/ajaxSubCategorias.php",
            data: data_string,
            success: function (res) {
                console.log(res);
                $("#idSubcategoria").html(res);
            }
        });
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
    }
    
    return {
        init: init
    }
}());;DBH.header = (function() {
    var reload = 0;  // <- ensure reload is declared
    var init = function() {
        mascaras();
        menu();
        menuMobile();
        loader();
        verificaCacada();
        verificaMissao();
        texteditor();
        
        if($('body').hasClass('pvp') || $('body').hasClass('ranking')){
            verificaAtaque();
            verificaMissao();
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
    verificaMissao = function() {
    var id = $('#personagemLogged').val();
    var data_string = 'id=' + id;
    var baseSite = $('#baseSite').val();
    
    if($('.missao-running').length > 0){
        $.ajax({
            type: "POST",
            url: baseSite + "ajax/ajaxMissao.php",  // ✅ Changed from ajaxVerificaMissao.php
            data: data_string,
            success: function (res) {
                startCountdownMissao(res);
            }
        });
    }
},




startCountdownMissao = function(tempo){
    // First call - display immediately
    if(tempo >= 0){
        var horas = Math.floor(tempo / 3600);
        var minutos = Math.floor((tempo % 3600) / 60);
        var segundos = tempo % 60;

        // Add leading zeros
        horas = (horas < 10) ? "0" + horas : horas;
        minutos = (minutos < 10) ? "0" + minutos : minutos;
        segundos = (segundos < 10) ? "0" + segundos : segundos;

        var horaImprimivel = horas + ':' + minutos + ':' + segundos;
        $(".missao-running .contador").html(horaImprimivel);
        
        // Continue countdown with setTimeout
        window.missionTimer = setTimeout(function(){ 
            startCountdownMissao(tempo - 1);
        }, 1000);
        
    } else {
        // Mission complete!
        $(".missao-running .contador").html("CONCLUÍDA!");
        clearTimeout(window.missionTimer);
    }
};




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
            //JQuery pra setar the value
            
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

            // Formata the values
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

            horaImprimivel = horas + ':' + min + ':' + seg;
            $(".punicao-adversario .contador").html(horaImprimivel);
            $(".punicao-adversario").show();

            setTimeout(function(){ 
                startCountdownPunicao(tempo);
            }, 1000);

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
}());;DBH.home = (function() {
    var init = function() {
        slider();
    },
    slider = function(){
        $('.slider-destaques').slick({
            dots: true,
            infinite: true,
            speed: 500,
            fade: true,
            autoplay: true,
            autoplaySpeed: 3000,
            cssEase: 'linear',
            prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>'
        });
        
        $('.slider-destaques').show();
        
        $('.lista-personagens').slick({
            slidesToScroll: 1,
            slidesToShow: 5,
            autoplay: true,
            autoplaySpeed: 2000,
            prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>'
        });
        
        $('.lista-personagens').show();
    }
    
    return {
        init: init
    }
}());;DBH.invasao = (function() {
    var init = function() {
        log();
    },
    log = function(){
        new PerfectScrollbar('.meu-log ul');
        
        $('.log-ataques ul').slick({
            dots: false,
            vertical: true,
            verticalSwiping: true,
            slidesToScroll: 1,
            infinite: false,
            autoplay: true,
            autoplaySpeed: 2500,
            arrows: false
        });
    }
    
    return {
        init: init
    }
}());;DBH.inventario = (function() {
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
}());;DBH.login = (function() {
    var init = function() {
        
    },
    instalarInsta = function(a){
        $(".box-instagram").length && $.ajax({
            url: "https://api.instagram.com/v1/users/" + a.id + "/media/recent",
            dataType: "jsonp",
            type: "GET",
            data: {
                access_token: a.token,
                count: 4
            }
        }).then(function(a) {
            getPhotoInstagram(a), $(".box-instagram").fadeIn()
        });
    },
    getPhotoInstagram = function(a){
        for (var n = $(".box-instagram ul"), o = 0; o < a.data.length; o++) n.append('<li><a href="' + a.data[o].link + '" target="_blank"><figure class="photo" style="background-image: url(' + a.data[o].images.standard_resolution.url + ')"><div class="stats"><span class="likes"><i class="fa fa-heart"></i>' + a.data[o].likes.count + '</span><span class="comments"><i class="fa fa-comment"></i>' + a.data[o].comments.count + "</span></div></figure></a></li>")
    }
    
    return {
        init: init,
        instalarInsta: instalarInsta
    }
}());;DBH.mercado = (function() {
    var init = function() {
        tabs();
    },
    tabs = function() {        
        if(sessionStorage.getItem('tabs')){
            var sessao_tab = sessionStorage.getItem('tabs');
        } else {
            var sessao_tab = 'solicitacoes';
        }

        $('.tabs-mercado li a').on('click', function(){
            $('.tabs-mercado li').removeClass('active');
            $('.tab-mercado-content .tab-mercado-item').removeClass('active');

            var content = $(this).attr('data-url');

            sessionStorage.setItem('tabs', content);

            var url = sessionStorage.getItem('tabs');

            $(this).closest('li').addClass('active');

            $('#'+url).addClass('active');
        });

        $('.tabs-mercado li').each(function(){
            var data = $(this).find('a').attr('data-url');
            if(data == sessao_tab){
                $('.tabs-mercado li').removeClass('active');
                $('.tab-mercado-content .tab-mercado-item').removeClass('active');
                $(this).addClass('active');
                $('#'+sessao_tab).addClass('active');
            }
        });
    }
    
    return {
        init: init
    }
}());
;DBH.modal = (function() {
    var init = function() {
        
    },
    iniciaModal = function(){
        if((!$('body').hasClass('npc')) && (!$('body').hasClass('combate'))){
            
        }
        
        $('.modal-game .anuncio').on('click', function(){
            $('.modal-game').hide();
            $('.backdrop-game').remove();
        });
    }
    
    return {
        init: init,
        iniciaModal: iniciaModal
    }
}());;DBH.noticias = (function() {
    var init = function() {
        texteditor();
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
    }
    
    return {
        init: init
    }
}());;DBH.npc = (function() {
    var init = function() {
        if($('body').hasClass('npc')){
            verificaNPC();
            verificaAtaque();
            combateLog();
        }
    },
    verificaNPC = function() {
        var id = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        var baseSite = $('#baseSite').val();
        
        // guard - only scroll if element exists
        var alvo = $('.batalha');
        if (alvo.length) {
            $('html, body').animate({scrollTop: alvo.offset().top}, 'slow');
        }

        $.ajax({
            type: "POST",
            url: baseSite+"ajax/ajaxNPC.php",
            data: data_string,
            success: function (res) {
                startCountdownNPC(res);
            }
        });
    },
    startCountdownNPC = function(tempo){
        // Se o tempo não for zerado
        if((tempo - 1) >= 0){
            
            var min = parseInt(tempo/60);
            var horas = parseInt(min/60);
            min = min % 60;
            var seg = tempo%60;

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

            horaImprimivel = min + ':' + seg;

            if($('.npc-vitoria').length > 0 || $('.npc-derrota').length > 0){
                $(".contador-batalha .cronometro").html('00:00');
            } else {
                $(".contador-batalha .cronometro").html(horaImprimivel);
            }
            
            $(".contador-batalha").show();

            setTimeout(function(){ 
                startCountdownNPC(tempo);
            }, 1000);

            tempo --;
        } else {
            $(".contador-batalha .cronometro").html('00:00');
            var finalizado = $('#finalizado').val();
            var round = $('#round').val();

            if(round == 0){
                if(finalizado == 0){
                    atacar();
                    $('#round').val('3');
                } else {
                    $(".contador-batalha .cronometro").html('00:00');
                }
            }
        }
    },
    verificaAtaque = function() {
        var id = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        var baseSite = $('#baseSite').val();
        
        $.ajax({
            type: "POST",
            url: baseSite+"ajax/ajaxBatalhaNPC.php",
            data: data_string,
            success: function (res) {
                startCountdownBatalha(res);
            }
        });
    },
    startCountdownBatalha = function(tempo){
        if((tempo - 1) >= 0){
            
            var min = parseInt(tempo/60);
            var horas = parseInt(min/60);
            min = min % 60;
            var seg = tempo%60;

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

            horaImprimivel = horas + ':' + min + ':' + seg;
            $(".npc-running .contador").html(horaImprimivel);
            $(".npc-running").show();

            setTimeout(function(){ 
                startCountdownBatalha(tempo);
            }, 1000);

            tempo --;
        } else {
            $(".npc-running").remove();
        }
    },
    atacar = function() {
        var round = $('#round').val();
        var finalizado = $('#finalizado').val();
        var idGuerreiro = $('#idOponente').val();
        var idPersonagem = $('#personagemLogged').val();
        var idAtaque = 4;
        var data_string = 'id=' + idAtaque + '&idGuerreiro=' + idGuerreiro + '&idPersonagem=' + idPersonagem + '&round=' + round + '&finalizado=' + finalizado;
        var baseSite = $('#baseSite').val();

        $.ajax({
            type: "POST",
            url: baseSite+"ajax/ajaxAtacarNPC.php",
            data: data_string,
            success: function (res) {
                location.reload(true);
            }
        });
    },
    combateLog = function(){
    const container = document.querySelector('.log');
    new PerfectScrollbar(container);
}
    
    return {
        init: init,
        combateLog: combateLog
    }
}());;DBH.pagamentos = (function() {
    var init = function() {
        
    },
    pagamentoDoacao = function(i) {
        $.ajax({
            type: "POST",
            url: "ajax/ajaxDoacao.php",
            data: new FormData(i),
            processData: false,
            cache: false,
            contentType: false,
            success: function (res) {
                $(i).find('#code').val(res);
                
                PagSeguroLightbox({
                    code: res
                }, {
                    success : function(transactionCode) {
                        $.ajax({
                            type: "POST",
                            url: "ajax/ajaxTransacoes.php",
                            data: {
                                transaction_id: res,
                                retorno: 'success'
                            },
                            success: function (res) {
                                var url_atual = window.location.href;
                                window.location.href = url_atual.replace('doacao', 'transacoes');
                            }
                        });
                    },
                    abort : function(transactionCode) {            
                        $.ajax({
                            type: "POST",
                            url: "ajax/ajaxTransacoes.php",
                            data: {
                                transaction_id: res,
                                retorno: 'abort'
                            },
                            success: function (res) {
                                
                            }
                        });
                    }
                });
            }
        });
    }
    
    return {
        init: init,
        pagamentoDoacao: pagamentoDoacao
    }
}());;DBH.personagem = (function() {
    var init = function() {
        itens();
    },
    itens = function(){
        $('.item-personagem').on('click', function(){
            $('.item-personagem').removeClass('active');
            $(this).toggleClass('active');
            var foto = $(this).attr('dataFoto');
            $('#fotoPersonagem').val(foto);
            var alvo = $('.btn-step-1');
            if (alvo.length) {
                $('html, body').animate({scrollTop: alvo.offset().top}, 'slow');
            }
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
                var alvo = $('.btn-step-2');
                if (alvo.length) $('html, body').animate({scrollTop: alvo.offset().top}, 'slow');
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
            var alvo = $('.btn-confirmar');
            if (alvo.length) $('html, body').animate({scrollTop: alvo.offset().top}, 'slow');
        });
    },
    meuPersonagem = function(){
        // Only scroll if the element exists (fixes "Cannot read properties of undefined (reading 'top')")
        var lista = $('.lista-meus-personagens');
        if (lista.length) {
            $('html, body').animate({scrollTop: lista.offset().top}, 'slow');
        }

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
                async: true,
                // --- Replace the existing image fallback block inside the ajax success handler with this code ---
                success: function (res) {
                    // defensive handling: if server returned empty or error-like response, show friendly message
                    if (!res || (typeof res === 'string' && res.trim().length === 0)) {
                        console.warn('ajaxPersonagens returned empty response for id:', id);
                        $(".personagem-atual").html('<div class="msg-error">Erro ao carregar personagem. Tente novamente.</div>');
                        return;
                    }

                    // if response contains server error / forbidden notice, show message
                    var low = (res+"").toLowerCase();
                    if (low.indexOf('forbidden') !== -1 || low.indexOf('notice') !== -1 || low.indexOf('warning') !== -1 || low.indexOf('error') !== -1 && low.indexOf('<html') !== -1) {
                        console.warn('Server-side error in ajaxPersonagens response:', res);
                        $(".personagem-atual").html('<div class="msg-error">Erro do servidor ao carregar personagem. Verifique logs.</div>');
                        return;
                    }

                    // insert HTML
                    $(".personagem-atual").html(res);

                    // determine base path saved in #baseSite (fallback to empty)
                    var baseSitePath = $('#baseSite').val() || '';
                    // ensure trailing slash (so concatenation is safe)
                    if (baseSitePath && baseSitePath.slice(-1) !== '/') baseSitePath += '/';
                    var defaultCard = baseSitePath + 'assets/cards/default.png';

                    // Make image src fallbacks: if any image src looks like '/assets/cards/' (no filename) or failed to load, replace with default placeholder
                    $(".personagem-atual img").each(function() {
                        try {
                            var $img = $(this);
                            var src = $img.attr('src') || '';
                            // If src ends with '/assets/cards/' or missing filename, set placeholder using baseSite
                            if (src.match(/\/assets\/cards\/?$/i) || src.trim() === '') {
                                $img.attr('src', defaultCard);
                            }
                            // Add onerror fallback to handle 403/404 — set to baseSite-aware placeholder
                            $img.on('error', function(){
                                // avoid infinite loop if default is missing: only replace if current src isn't already the default
                                try {
                                    if ($img.attr('src') !== defaultCard) {
                                        $img.attr('src', defaultCard);
                                    }
                                } catch(e) { /* noop */ }
                            });
                        } catch(e) {
                            console.warn('Error handling personagem image fallback', e);
                        }
                    });

                    // Scroll to inserted content, prefer .personagem-atual > .info if present
                    var info = $('.personagem-atual > .info');
                    if (info.length) {
                        $('html, body').animate({scrollTop: info.offset().top}, 'slow');
                    } else {
                        var conteudo = $('.conteudo');
                        if (conteudo.length) $('html, body').animate({scrollTop: conteudo.offset().top}, 'slow');
                    }
                },
                // ---
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('ajaxPersonagens error', textStatus, errorThrown, jqXHR.responseText);
                    $(".personagem-atual").html('<div class="msg-error">Erro de rede ao carregar personagem. ('+jqXHR.status+')</div>');
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
        $('.bt-jogar').on('click', function(){
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
}());;DBH.produto = (function() {
    var init = function() {
        variations();
        verificaVariation();
    },
    variations = function(){
        $('.variations label').on('click', function(){
            $('.variations label').removeClass('active');
            $(this).toggleClass('active');
            
            var link = $('#linkProd').val();
            var id = $(this).find('input').val();
            
            $('.bt-comprar').attr('href', link+'/'+id);
        });
    },
    verificaVariation = function() {
        var link = $('#linkProd').val();
        
        if($('.variations').length > 0){
            if($('.variations label').hasClass('active')){
                var id = $('.variations label.active').find('input').val();
                
                $('.bt-comprar').attr('href', link+'/'+id);
            } else {
                $('.bt-comprar').attr('href', 'javascript:void(0);');
            }
        }
    }
    
    return {
        init: init
    }
}());;DBH.pvp = (function() {
    var init = function() {
        verificaPVP();
        combateLog();
    },
    verificaPVP = function() {
        var id = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        
        if($('body').hasClass('combate')){
            var alvo = $('.batalha');
            if (alvo.length) $('html, body').animate({scrollTop: alvo.offset().top}, 'slow');
            var url = "../ajax/ajaxPVP.php";
        } else {
            var url = "ajax/ajaxPVP.php"; 
        }

        $.ajax({
            type: "POST",
            url: url,
            data: data_string,
            success: function (res) {
                startCountdownPVP(res);
            }
        });
    },
    startCountdownPVP = function(tempo){
        if((tempo - 1) >= 0){
            
            var min = parseInt(tempo/60);
            var horas = parseInt(min/60);
            min = min % 60;
            var seg = tempo%60;

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

            horaImprimivel = min + ':' + seg;
            
            if($('.pvp-vitoria').length > 0 || $('.pvp-derrota').length > 0){
                $(".contador-batalha .cronometro").html('00:00');
            } else {
                $(".contador-batalha .cronometro").html(horaImprimivel);
            }
            
            $(".contador-batalha").show();

            setTimeout(function(){ 
                startCountdownPVP(tempo);
            }, 1000);

            tempo --;
        } else {
            $(".contador-batalha .cronometro").html('00:00');
            var finalizado = $('#finalizado').val();
            var round = $('#round').val();

            if(round == 0){
                if(finalizado == 0){
                    atacar();
                    $('#round').val('3');
                } else {
                    $(".contador-batalha .cronometro").html('00:00');
                }
            }
        }
    },
    atacar = function() {
        var round = $('#round').val();
        var finalizado = $('#finalizado').val();
        var idGuerreiro = $('#idOponente').val();
        var idPersonagem = $('#personagemLogged').val();
        var idAtaque = 4;
        var data_string = 'id=' + idAtaque + '&idGuerreiro=' + idGuerreiro + '&idPersonagem=' + idPersonagem + '&round=' + round + '&finalizado=' + finalizado;

        var link = location.href.split('combate/')[0]+'ajax/ajaxAtacar.php';

        $.ajax({
            type: "POST",
            url: link,
            data: data_string,
            success: function (res) {
                location.reload(true);
            }
        });
    },
    combateLog = function(){
    const container = document.querySelector('.log');
    new PerfectScrollbar(container);
}
    
    return {
        init: init
    }
}());;$(document).ready(function(){
    var $body = $('body');

    DBH.header.init();
    
    init = function(){
    // Your existing code...
    
    // ADD THIS - START TIMER IF MISSION IS ACTIVE
    var $missaoBox = $(".missao-running");
    if($missaoBox.length > 0){
        // Mission is active, start the verification loop
        verificaMissao(); // Call once immediately
        setInterval(verificaMissao, 1000); // Then every second
    }
};

    if(!$body.hasClass('login')){
        DBH.modal.iniciaModal();
    }

    if($body.hasClass('minhas-fotos')){
    	DBH.personagem.foto();
    }
    
    if($body.hasClass('criar-personagem')){
    	DBH.personagem.init();
    }
    
    if($body.hasClass('troca-guerreiro')){
    	DBH.personagem.trocaGuerreiro();
    }
    
    if($body.hasClass('meus-personagens')){
    	DBH.personagem.meuPersonagem();
    }
    
    if($body.hasClass('inventario')){
    	DBH.inventario.init();
    }
    
    if($body.hasClass('equipes')){
    	DBH.equipes.init();
    }
    
    if($body.hasClass('produto')){
    	DBH.produto.init();
    }
    
    if($body.hasClass('combate')){
    	DBH.pvp.init();
    }
    
    if($body.hasClass('npc')){
    	DBH.npc.init();
    }
    
    if($body.hasClass('forum')){
    	DBH.forum.init();
    }
    
    if($body.hasClass('login')){
    	DBH.login.init();
    }
    
    if($body.hasClass('noticias')){
    	DBH.noticias.init();
    }
    
    if($body.hasClass('equipes')){
    	DBH.chat.equipes();
    }
    
    if($body.hasClass('publico')){
    	DBH.chat.amigos();
    }
    
    if($body.hasClass('home')){
    	DBH.home.init();
    }
    
    if($body.hasClass('assistir')){
    	DBH.assistir.init();
    }
    
    if($body.hasClass('market')){
    	DBH.mercado.init();
    }
    
    if($body.hasClass('invasao')){
    	DBH.invasao.init();
    }
    
    DBH.header.audioTema();
    DBH.header.playTema();
});

$(document).ajaxStop(function() {
    var $body = $('body');
    
    if($body.hasClass('meus-personagens')){
    	DBH.personagem.jogar();
    }
});