DBH.invasao = (function() {
    var init = function() {
        log();
    },
    log = function(){
        const ps = new PerfectScrollbar('.meu-log ul');
        
        $('.log-ataques ul').slick({
            dots: false,
            vertical: true,
            verticalSwiping: true,
            slidesToScroll: 1,
            infinite: false,
            autoplay: true,
            autoplaySpeed: 2500,
            arrows: false
        });
    }
    
    return {
        init: init
    }
}());