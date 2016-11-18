$(document).ready(function () {
    $(document).on('click', '.listRestaurantsTrigger', function () {
        var list = $('.List__restaurant');
        var wrapper = list.find('.wrapper');

        if (list.is(':visible')) {
            list.slideUp();
            hideSpinner(wrapper);
        } else {
            list.slideDown();
            displaySpinner(wrapper);

            var url = Routing.generate('restaurants_list');

            $.ajax({
                url: url,
                dataType: "html",
                method: "POST",
                success: function (data) {
                    wrapper.html(data);
                },
                error: function () {

                },
                complete: function () {
                    hideSpinner(wrapper);
                }
            });
        }
    });


    if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
        $(document).on('mouseover', '.List__restaurant .all ul li:not(.alpha)', function (e) {
            var li = $(this);
            var ul = li.parents('ul');

            if ($('.List__restaurant .picture').width() == 0) {
                $('.List__restaurant .content').toggleClass('col-sm-12 col-sm-7');
                $('.List__restaurant .picture').toggleClass('col-sm-0 col-sm-5');
            }

            var img = li.data('img');

            $('.List__restaurant .picture').html("" +
                "<a target='_blank' href='" + li.data('link') + "'>" +
                "<div class='img' style='background-image: url(" + img + ")'>" +
                "<div class='Sticker__overlay'><div class='Logo'></div></div></div>" +
                "</a>" );

            if(li.data('img').length == 0) {
                $('.Sticker__overlay').css('opacity', '1')
            }
        });
    }
});