(function ($) {
    $.fn.spinnerLoader = function (options) {

        var settings = $.extend({
            action: "append",
            container: $('body'),
            color: 'green',
            size: 'fa-2x'
        }, options);

        var spinner = '<span class="spinner"><i class="fa fa-spinner fa-spin ' + settings.color + " " + settings.size + '"></i></span><div class="bg-spinner"></div>';

        if (settings.action === "append") {
            settings.container.css('position', 'relative');
            if (settings.container.find('.spinner').length === 0) {
                settings.container.append(spinner);
            }
            var hViewport = $(window).height();
            var wViewport = $(window).width();
            var hUsed = settings.container.outerHeight();
            var wUsed = settings.container.outerWidth();
            var displayMode = 'absolute';

            console.log('container', hUsed, wUsed);
            console.log('viewport', hViewport, wViewport);

            if (hViewport <= hUsed || wViewport <= wUsed) {
                hUsed = hViewport;
                wUsed = wViewport;
                displayMode = 'fixed';
            }

            console.log('used', hUsed, wUsed);

            var hChild = settings.container.find('.spinner').outerHeight();
            var wChild = settings.container.find('.spinner').outerWidth();
            settings.container.find('.spinner').css({
                'left': (wUsed / 2) - (wChild / 2),
                'top': (hUsed / 2) - (hChild / 2),
                'position': displayMode
            });
            settings.container.find('.bg-spinner').css({
                'position': displayMode
            });
        }

        if (settings.action === "remove") {
            settings.container.find('.spinner').remove();
            settings.container.find('.bg-spinner').remove();
        }
        return this;
    };
})(jQuery);

/**
 * Display a spinner in a container
 * @param container
 */
function displaySpinner(container) {
    $(document).spinnerLoader({
        action: 'append',
        container: container
    });
}

/**
 * Hide a spinner from the container
 * @param container
 */
function hideSpinner(container) {
    $(document).spinnerLoader({
        action: 'remove',
        container: container
    });
}
