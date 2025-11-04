DBH.mercado = (function() {
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
