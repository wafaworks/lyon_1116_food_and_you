(function ($) {

    $.infiniteScroll = function (element, options) {

        var state = {
            locked: false,
            page: 1,
            finished: false
        };

        var defaults = {
                url: false,
                data: {},
                form: false,
                bindReloadToForm: true,
                loadPageOnInit: false,
                stopElement: '.stop-list',
                watchMode: 'window',
                bottomOffset: false,
                preProcessData: function (data) {
                    return data;
                }
            },
            plugin = this,
            $element = $(element);

        plugin.settings = {};

        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);

            // load initial data if needed
            if (plugin.settings.loadPageOnInit) {
                state.page = 0;
                loadNextPage();
            }

            // enable watchers
            if (plugin.settings.watchMode === 'window') {
                watchWindow();
            }
            if (plugin.settings.watchMode === 'timer') {
                watchTimer();
            }

            // bind form submit to reload content
            if (plugin.settings.bindReloadToForm && plugin.settings.form) {
                bindReloadToForm();
            }
        };

        var bindReloadToForm = function () {
            plugin.settings.form.off('submit');
            plugin.settings.form.on('submit', function (e) {
                e.preventDefault();
                e.stopPropagation();

                resetInfiniteScroll();
                loadNextPage();
            });
        };

        var resetInfiniteScroll = function () {
            $element.html('');
            state.page = 0;
        };

        var isFinished = function () {
            return !!$element.find(plugin.settings.stopElement).length
        };

        var getPostData = function () {
            var postData = {
                page: state.page
            };
            postData = $.extend(postData, plugin.settings.data);

            if (plugin.settings.form) {
                postData = $.extend(postData, plugin.settings.form.serializeObject());
            }

            return plugin.settings.preProcessData(postData);
        };

        var watchWindow = function () {
            $(window).on('scroll resize orientationchange focus', function () {
                watchChecker();
            });
        };

        var watchTimer = function () {
            setInterval(
                function () {
                    watchChecker();
                },
                500
            );
        };

        var watchChecker = function () {
            var offset = calculateOffset();

            if ($(window).scrollTop() + $(window).height() >= $(document).height() - offset) {
                loadNextPage();
            }
        };

        var calculateOffset = function () {
            var bottomOffset = 0;

            // if Integer
            if (plugin.settings.bottomOffset === parseInt(plugin.settings.bottomOffset, 10)) {
                bottomOffset = plugin.settings.bottomOffset;
            }

            // if array of Jquery objects
            if (plugin.settings.bottomOffset instanceof Array) {
                for (var i=0; i<plugin.settings.bottomOffset.length; i++) {
                    bottomOffset += plugin.settings.bottomOffset[i].height();
                }
            }

            return bottomOffset;
        };

        var loadNextPage = function () {
            if (isFinished() || state.locked) {
                return;
            }
            state.page += 1;
            state.locked = true;
            var postData = getPostData();

            $.ajax({
                url: plugin.settings.url,
                method: "POST",
                data: postData
            }).done(function (result) {
                $element.append(result.data);
                state.finished = result.finished;
            }).always(function () {
                state.locked = false;
                watchChecker();
            });
        };

        plugin.init();
    };

    $.fn.infiniteScroll = function (options) {
        return this.each(function () {
            if (undefined == $(this).data('infiniteScroll')) {
                var plugin = new $.infiniteScroll(this, options);
                $(this).data('infiniteScroll', plugin);
            }
        });
    }

})(jQuery);
