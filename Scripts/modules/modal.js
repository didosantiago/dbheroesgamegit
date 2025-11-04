DBH.modal = (function() {
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
}());