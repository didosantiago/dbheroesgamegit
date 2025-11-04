DBH.pvp = (function() {
    var init = function() {
        verificaPVP();
        combateLog();
    },
    verificaPVP = function() {
        var id = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        
        if($('body').hasClass('combate')){
            $('html, body').animate({scrollTop: $('.batalha').offset().top}, 'slow');
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
            horaImprimivel = min + ':' + seg;
            //JQuery pra setar o valor
            
            if($('.pvp-vitoria').length > 0 || $('.pvp-derrota').length > 0){
                $(".contador-batalha .cronometro").html('00:00');
            } else {
                $(".contador-batalha .cronometro").html(horaImprimivel);
            }
            
            $(".contador-batalha").show();

            // Define que a função será executada novamente em 1000ms = 1 segundo
            setTimeout(function(){ 
                startCountdownPVP(tempo);
            }, 1000);
            

            // diminui o tempo
            tempo --;
        } else {
            $(".contador-batalha .cronometro").html('00:00');
            var finalizado = $('#finalizado').val();
            var round = $('#round').val();
            
            console.log(finalizado);
            console.log(round);

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
        const ps = new PerfectScrollbar(container);

        // or just with selector string
        const ps = new PerfectScrollbar('.log');
    }
    
    return {
        init: init
    }
}());