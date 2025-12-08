DBH.npc = (function() {
    var attackInitiated = false;
    var isAttacking = false; // Prevent duplicate attacks
    
    var init = function() {
        if($('body').hasClass('npc')){
            verificaNPC();
            verificaAtaque();
            combateLog();
            
            // ✅ Setup attack button click handlers
            $('.ataques li').on('click', function(e) {
                e.preventDefault();
                var finalizado = $('#finalizado').val();
                var round = $('#round').val();
                
                if(finalizado == 1) {
                    alert('Batalha finalizada!');
                    return false;
                }
                
                if(round == 0) {
                    alert('Aguarde sua vez de atacar!');
                    return false;
                }
                
                if(isAttacking) {
                    console.log('Ataque já em andamento...');
                    return false;
                }
                
                var idAtaque = $(this).data('ataque');
                atacarPlayer(idAtaque);
            });
        }
    },
    
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
                var tempo = parseInt(res) || 0;
                startCountdownNPC(tempo);
            },
            error: function() {
                startCountdownNPC(0);
            }
        });
    },

    startCountdownNPC = function(tempo){
        if(tempo > 0){
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
                startCountdownNPC(tempo - 1);
            }, 1000);

        } else {
            $(".contador-batalha .cronometro").html('00:00');
            
            var finalizado = $('#finalizado').val();
            
            // ✅ Battle ended - no auto-attack, just reload page
            if(finalizado == 1) {
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
        if(tempo > 0){
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
                startCountdownBatalha(tempo - 1);
            }, 1000);

        } else {
            $(".npc-running").remove();
        }
    },
    
    // ✅ NEW: Player attack function
    atacarPlayer = function(idAtaque) {
        if(isAttacking) {
            return false;
        }
        
        isAttacking = true;
        var idOponente = $('#idOponente').val();
        var baseSite = $('#baseSite').val();
        
        // Submit the form normally to PHP (no AJAX)
        var form = $('<form>', {
            'method': 'POST',
            'action': window.location.href
        });
        
        $('<input>').attr({
            type: 'hidden',
            name: 'atacar',
            value: '1'
        }).appendTo(form);
        
        $('<input>').attr({
            type: 'hidden',
            name: 'idAtack',
            value: idAtaque
        }).appendTo(form);
        
        $('<input>').attr({
            type: 'hidden',
            name: 'estado',
            value: '1'
        }).appendTo(form);
        
        form.appendTo('body').submit();
    },

    combateLog = function(){
        const container = document.querySelector('.log');
        if(container) {
            const ps = new PerfectScrollbar(container);
        }
    },

    concluirBatalha = function() {
        var btn = document.getElementById('btnConcluir');

        if(!btn) {
            console.error('Concluir button not found!');
            return false;
        }

        if(btn) {
            btn.disabled = true;
            btn.value = 'Processando...';
            if(btn.textContent) btn.textContent = 'Processando...';

            var baseSite = $('#baseSite').val();
            var currentUrl = window.location.href;

            var url = currentUrl + (currentUrl.indexOf('?') > -1 ? '&' : '?') + 'concluir=1';

            $.ajax({
                type: "GET",
                url: url,
                success: function(res) {
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
