(function() {
    var carousel = $(".owl-carousel");
    if (carousel.length > 0){
        carousel.owlCarousel({
            margin:1,
            loop:true,
            autoWidth:true,
            items:1,
            autoplay:true,
            dots:true,
            center:true
        })
    }
}());
