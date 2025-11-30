DBH.npc = (function() {
    var attackInitiated = false; // ✅ ADD THIS FLAG
    
    var init = function() {
        if($('body').hasClass('npc')){
            verificaNPC();
            verificaAtaque();
            combateLog();
        }
    },
    // ... rest of the code
    verificaNPC = function() {
        var id = $('#personagemLogged').val();
        var data_string = 'id=' + id;
        var baseSite = $('#baseSite').val();
        
        $('html, body').animate({scrollTop: $('.batalha').offset().top}, 'slow');

        $.ajax({
            type: "POST",
            url: baseSite+"ajax/ajaxNPC.php",
            data: data_string,
            success: function (res) {
                // Convert response to integer, default to 0 if empty
                var tempo = parseInt(res) || 0;
                startCountdownNPC(tempo);
            },
            error: function() {
                // If AJAX fails, start with 0
                startCountdownNPC(0);
            }
        });
    },
    

    startCountdownNPC = function(tempo){
        // Se o tempo não for zerado
        if(tempo > 0){
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
            if($('.npc-vitoria').length > 0 || $('.npc-derrota').length > 0){
                $(".contador-batalha .cronometro").html('00:00');
            } else {
                $(".contador-batalha .cronometro").html(horaImprimivel);
            }
            
            $(".contador-batalha").show();

            // Define que a função será executada novamente em 1000ms = 1 segundo
            setTimeout(function(){ 
                startCountdownNPC(tempo - 1);
            }, 1000);

        } else {
            // ✅ TIMER REACHED 0
            $(".contador-batalha .cronometro").html('00:00');
            
            var finalizado = $('#finalizado').val();
            
            // ✅ ONLY attack once if battle is active and we haven't attacked yet
            if(finalizado == 0 && !attackInitiated){
                attackInitiated = true; // ✅ Prevent multiple calls
                atacar();
            } else if(finalizado == 1) {
                // Battle is finished, show message
                $(".contador-batalha .cronometro").html('FIM');
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
                var tempo = parseInt(res) || 0;
                if(tempo > 0) {
                    startCountdownBatalha(tempo);
                }
            }
        });
    },
    startCountdownBatalha = function(tempo){
        // Se o tempo não for zerado
        if(tempo > 0){
            
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
            
            $(".npc-running .contador").html(horaImprimivel);
            $(".npc-running").show();

            // Define que a função será executada novamente em 1000ms = 1 segundo
            setTimeout(function(){ 
                startCountdownBatalha(tempo - 1);
            }, 1000);

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
            dataType: 'json',
            success: function (response) {
                // ✅ Check if battle ended
                if(response.battle_ended === true){
                    // Battle ended, reload immediately
                    alert('Batalha finalizada!');
                    location.reload(true);
                } else {
                    // ✅ Battle continues - DON'T reload, just start new timer
                    console.log('Attack successful, waiting for next round...');
                    // The timer will handle the next attack automatically
                }
            },
            error: function(xhr, status, error) {
                console.error('Attack error:', error);
                // On error, reload to refresh state
                setTimeout(function() {
                    location.reload(true);
                }, 1500);
            }
        });
    },


    combateLog = function(){
        const container = document.querySelector('.log');
        if(container) {
            const ps = new PerfectScrollbar(container);
        }
    }
    
    ,

    concluirBatalha = function() {
        var btn = document.getElementById('btnConcluir');

        if(!btn) {
            console.error('Concluir button not found!');
            return false;
        }

        if(btn) {
            // Disable button immediately to prevent double-click
            btn.disabled = true;
            btn.value = 'Processando...';
            if(btn.textContent) btn.textContent = 'Processando...';

            var baseSite = $('#baseSite').val();
            var currentUrl = window.location.href;

            // Add concluir parameter to URL
            var url = currentUrl + (currentUrl.indexOf('?') > -1 ? '&' : '?') + 'concluir=1';

            // Send AJAX request
            $.ajax({
                type: "GET",
                url: url,
                success: function(res) {
                    // Redirect to tournament page after processing
                    window.location.href = baseSite + 'torneio';
                },
                error: function() {
                    alert('Erro ao concluir batalha. Por favor, tente novamente.');
                    btn.disabled = false;
                    btn.value = 'Concluir';
                    if(btn.textContent) btn.textContent = 'Concluir';
                }
            });
        }
    }

	return {
        init: init,
        combateLog: combateLog
    }
}());