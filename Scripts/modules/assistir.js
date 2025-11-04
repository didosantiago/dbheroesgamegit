DBH.assistir = (function() {
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
}());