// Override the DBH.npc module
DBH.npc = (function() {
    var attackInitiated = false;
    var isAttacking = false;
    
    var init = function() {
        if($('body').hasClass('npc')){
            console.log('✅ NPC module initialized (OVERRIDE VERSION)');
            verificaNPC();
            verificaAtaque();
            combateLog();
            
            // Setup attack button click handlers
            $('.ataques li').off('click').on('click', function(e) {
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
                console.log('⏱️ NPC Timer:', tempo, 'seconds');
                startCountdownNPC(tempo);
            },
            error: function() {
                console.log('❌ Error loading timer');
                startCountdownNPC(0);
            }
        });
    },
    
    startCountdownNPC = function(tempo){
        if(tempo > 0){
            var min = parseInt(tempo/60);
            var seg = tempo%60;
            
            if(min < 10){
                min = "0"+min;
            }
            if(seg <=9){
                seg = "0"+seg;
            }
            
            var horaImprimivel = min + ':' + seg;
            
            // Check if battle ended
            if($('.npc-vitoria').length > 0 || $('.npc-derrota').length > 0){
                $(".contador-batalha .cronometro").html('FIM');
                console.log('🏁 Battle ended - stopping timer');
                return;
            }
            
            $(".contador-batalha .cronometro").html(horaImprimivel);
            $(".contador-batalha").show();
            
            setTimeout(function(){
                startCountdownNPC(tempo - 1);
            }, 1000);
        } else {
            // ✅ CRITICAL FIX: Timer expired - RELOAD PAGE
            $(".contador-batalha .cronometro").html('00:00');
            
            // Check if battle ended
            if($('.npc-vitoria').length > 0 || $('.npc-derrota').length > 0){
                $(".contador-batalha .cronometro").html('FIM');
                console.log('🏁 Battle ended - not reloading');
            } else {
                // ✅ FORCE RELOAD TO TRIGGER PHP AUTO-ATTACK
                console.log('⏰ Timer expired at 00:00 - FORCING RELOAD');
                setTimeout(function(){
                    console.log('🔄 Reloading page now...');
                    location.reload(true);
                }, 500);
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
    
    atacarPlayer = function(idAtaque) {
        if(isAttacking) {
            return false;
        }
        
        isAttacking = true;
        
        var idOponente = $('#idOponente').val();
        var baseSite = $('#baseSite').val();
        
        // Submit the form
        var form = $('<form method="POST"></form>');
        form.append('<input type="hidden" name="atacar" value="1">');
        form.append('<input type="hidden" name="idAtack" value="'+idAtaque+'">');
        form.append('<input type="hidden" name="estado" value="1">');
        $('body').append(form);
        form.submit();
    },
    
    combateLog = function(){
        var baseSite = $('#baseSite').val();
        var idNPC = $('#idNPC').val();
        
        $.ajax({
            type: "POST",
            url: baseSite+"ajax/ajaxNPCHistorico.php",
            data: {id: idNPC},
            success: function (res) {
                $('.combate-log').html(res);
            }
        });
    };
    
    return {
        init: init
    };
})();

// ✅ CRITICAL: Auto-initialize immediately after definition
// This ensures the override is active even after page reload
if($('body').hasClass('npc')){
    console.log('🚀 NPC override loaded - reinitializing...');
    DBH.npc.init();
}
