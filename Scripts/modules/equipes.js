DBH.equipes = (function() {
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
}());