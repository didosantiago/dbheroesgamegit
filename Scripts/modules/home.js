DBH.home = (function() {
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
}());