DBH.produto = (function() {
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
}());