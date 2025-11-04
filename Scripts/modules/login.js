DBH.login = (function() {
    var init = function() {
        
    },
    instalarInsta = function(a){
        $(".box-instagram").length && $.ajax({
            url: "https://api.instagram.com/v1/users/" + a.id + "/media/recent",
            dataType: "jsonp",
            type: "GET",
            data: {
                access_token: a.token,
                count: 4
            }
        }).then(function(a) {
            getPhotoInstagram(a), $(".box-instagram").fadeIn()
        });
    },
    getPhotoInstagram = function(a){
        for (var n = $(".box-instagram ul"), o = 0; o < a.data.length; o++) n.append('<li><a href="' + a.data[o].link + '" target="_blank"><figure class="photo" style="background-image: url(' + a.data[o].images.standard_resolution.url + ')"><div class="stats"><span class="likes"><i class="fa fa-heart"></i>' + a.data[o].likes.count + '</span><span class="comments"><i class="fa fa-comment"></i>' + a.data[o].comments.count + "</span></div></figure></a></li>")
    }
    
    return {
        init: init,
        instalarInsta: instalarInsta
    }
}());