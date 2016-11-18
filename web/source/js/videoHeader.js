(function ($) {

    $.videoHeader = function (element, options) {

        var defaults = {
                placeholderSelector: '.Site__header--video',
                placeholderImage: '/images/home/videos/defaultImage.jpg'
            },
            plugin = this,
            $element = $(element);

        plugin.settings = {};

        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);

            if (checkVideoSupport()) {
                bindVideos();
            } else {
                loadPlaceholderImage();
            }
        };

        var bindVideos = function () {
            var $videos = $element.find('video'),
                videoCount = $videos.length;

            if (videoCount === 0) {
                loadPlaceholderImage();
                return;
            }

            $videos.each(function () {
                $(this).on('ended', function () {
                    var currentPlayer = $(this);
                    var nextPlayer = currentPlayer.next();

                    if (nextPlayer.length === 0) {
                        nextPlayer = currentPlayer.siblings().first();
                    }

                    currentPlayer.hide();
                    nextPlayer.show();
                    nextPlayer.get(0).play();
                })
            });
        };

        var loadPlaceholderImage = function () {
            $(settings.placeholderSelector).css('background-image', 'url(' + settings.placeholderImage + ')');
        };

        var checkVideoSupport = function () {
            var checkVideoSupportedByType = function (vidType, codType) {
                    var vid = document.createElement('video'),
                        isSupported = vid.canPlayType(vidType + ';codecs="' + codType + '"');
                    if (isSupported === "") {
                        return false;
                    }
                    return isSupported;
                },
                videoIsSupported = true,
                mp4Supported = checkVideoSupportedByType('video/mp4', 'avc1.42E01E, mp4a.40.2'),
                oggSupported = checkVideoSupportedByType('video/ogg', 'theora, vorbis');

            if ((mp4Supported === false) && (oggSupported === false)) {
                videoIsSupported = false;
            }

            return videoIsSupported;
        };

        plugin.init();
    };

    $.fn.videoHeader = function (options) {
        return this.each(function () {
            if (undefined == $(this).data('videoHeader')) {
                var plugin = new $.videoHeader(this, options);
                $(this).data('videoHeader', plugin);
            }
        });
    }

})(jQuery);
