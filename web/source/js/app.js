$(document).ready(function () {

    var ratings = $(".starrr");
    if (ratings.length > 0){
        ratings.starrr();
    }

    // mobile navbar toogle
    $('.navbar-toggle').on('click', function () {
        if (($(this).attr("aria-expanded") == "false") || ($(this).attr("aria-expanded") == undefined)) {
            $('body').append('<div class="modal-backdrop fade in"></div>');
            $('body').addClass('fixed');
        } else {
            $(".modal-backdrop").remove();
            $('body').removeClass('fixed');
        }
    });

    $(document).ready(function() {
        $('.social').click(function(e) {
            if($(this).find('.fa-instagram').length == 0) {
                e.preventDefault();
                window.open($(this).attr('href'), 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
                return false;
            }
        });
    });

    $('.Event__map').click(function () {
        $('.Event__map iframe').css("pointer-events", "auto");
    });

    $( ".Event__map" ).mouseleave(function() {
        $('.Event__map iframe').css("pointer-events", "none");
    });
});


