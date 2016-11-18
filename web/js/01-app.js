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

var ajaxErrorHandler = (function () {
    var renderFieldError = function (id, message) {
        var formElement = $('#' + id);
        var formElementContainer = formElement.parent();
        var blockTemplate = '<span class="help-block" id="' + id + '-error"></span>';

        var errorBlock = formElementContainer.find('.help-block');
        if (errorBlock.length == 0) {
            formElementContainer.append(blockTemplate);
            errorBlock = formElementContainer.find('.help-block');
        }

        formElementContainer.addClass('has-error');
        errorBlock.html(message);
        errorBlock.show();
    };

    var renderFieldErrors = function(form, fieldErrors) {
        for (var key in fieldErrors) {
            if (fieldErrors.hasOwnProperty(key)) {
                renderFieldError(key, fieldErrors[key]);
            }
        }
    };

    var renderGlobalErrors = function(form, globalErrors) {
        var errorString = '';
        var globalErrorContainer = form.find('.global-errors');

        for (var key in globalErrors) {
            if (globalErrors.hasOwnProperty(key)) {
                errorString += globalErrors[key] + '<br>';
            }
        }

        globalErrorContainer.html(errorString);
        globalErrorContainer.show();
    };

    var clearGlobalErrors = function (form) {
        var globalErrorContainer = form.find('.global-errors');
        globalErrorContainer.html('');
        globalErrorContainer.hide();
    };

    return {
        display: function (form, errors) {
            if (errors.fields) {
                renderFieldErrors(form, errors.fields);
            }

            if (errors.global) {
                renderGlobalErrors(form, errors.global)
            }
        },

        clear: function (form) {
            clearGlobalErrors(form);
        }
    }
})();

$(document).on('mouseup', 'input,a,button,.btn', function (e) {
	var node = $(this);
	setTimeout(function () { node.css('pointer-events', 'none'); }, 10);
	var er;try { clearTimeout(node.timerTMO); } catch(er) {};
	node.timerTMO = setTimeout(function () { node.css('pointer-events', ''); }, 500);
});

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

// Aditional custom validators

$.validator.addMethod(
    "customDate",
    function(value, element) {
        var m = value.match(/^(\d\d?)\/(\d\d?)\/(\d\d\d\d)$/);

        if (m) {
            var year = parseInt((new Date()).getFullYear());
            var yearOfBirth = parseInt(m[3]);

            return (yearOfBirth !== undefined && yearOfBirth >= year-100 && yearOfBirth <= year-7);
        }

        return false;
    },
    "Please enter a date in the format dd/mm/yyyy."
);

// Function with default templates

var validatorTemplates = function (config, template)
{
    template = template || 'defaults';

    var templates = {
        defaults: {
            errorClass: 'help-block',
            errorElement: 'span',
            ignore: "",
            highlight: function (element) {
                $(element).parent().addClass('has-error');
            },
            unhighlight: function (element) {
                var globalErrorContainer = $(element).parent('form').find('.global-errors');

                $(element).parent().removeClass('has-error');
                $(element).siblings('.help-block').hide();
                globalErrorContainer.hide();
            },
            errorPlacement: function (error, element) {
                $(element).parent().append(error);
            }
        }
    };


    return jQuery.extend({}, templates[template], config);
};



$(document).ready(function () {
    var hash = window.location.hash.substr(1);

    if (hash.match(/login/)) {
        displayModal('modal_login');
    }

    if (hash.match(/password_reset/)) {
        displayModal('modal_resetting_request');
    }

    if (hash.match(/applicant_success/)) {
        displayModal('modal_simple', {'template': 'applicant-success'});
    }
})

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
/** @jshint multistr=true */
jQuery.customModal = jQuery.fn.customModal = function(url) {
    var modalNode = $('\
    <div style="transition: 1s;-webkit-transition:1s;display: none;">\
        <div class="modal fade" id="customModal" style="transition: 1s;-webkit-transition:1s;display: block;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">\
            <div class="modal-dialog modal-lg" role="document">\
                <div class="modal-header Modal__header">\
                    <div class="Modal__close" data-dismiss="modal">\
                        <span class="close">Fermer <i class="fa fa-times"></i></span>\
                    </div>\
                </div>\
                <div class="modal-content">\
                    <div class="modal-body"></div>\
                </div>\
            </div>\
        </div>\
        <div class="modal-backdrop fade in"></div>\
    </div>').get(0);
    $('body').eq(0).append(modalNode);

    var priv = {
        processing: false,
        destroyOnClose: true,
    };
    var vars = {};
    var pub = {
        vars: function(o) {
            if (o) {
                vars = $.extend(vars, o);
                return pub;
            }
            return vars;
        },
        $: function() {
            return $(pub.node());
        },
        node: function() {
            return modalNode;
        },
        show: function(callback) {
            if (priv.processing) {
                //do timeout
                setTimeout(pub.show, 50);
            } else {
                pub.$().show();
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                    pub.$().find('.modal-dialog').eq(0).css({
                        top: '32px'
                    });
                } else {
                    pub.$().find('.modal-dialog').eq(0).css({
                        top: Math.max(32, Math.floor((pub.$().find('.modal-backdrop').height() - pub.$().find('.modal-dialog').height()) / 3 + 32)) + 'px'
                    });
                }
                pub.$().find('#customModal').addClass('in');
                pub.$().find('#customModal').focus();
                $('body').addClass('modal-open');

                if (typeof callback !== "undefined") {
                    callback(pub);
                }
            }
            return this;
        },
        recenter: function() {
            if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                pub.$().find('.modal-dialog').eq(0).css({
                    top: '32px'
                });
            } else {
                pub.$().find('.modal-dialog').eq(0).css({
                    top: Math.max(32, Math.floor((pub.$().find('.modal-backdrop').height() - pub.$().find('.modal-dialog').height()) / 3 + 32)) + 'px'
                });
            }
        },
        destroyOnClose: function(status) {
            if (typeof(status) !== "undefined") {
                priv.destroyOnClose = !!status;
            }
            return priv.destroyOnClose;
        },
        hide: function() {
            pub.$().hide();
            if (priv.destroyOnClose) {
                pub.$().remove();
            }
            $('body').removeClass('modal-open');
            return pub;
        },
        load: function(url, callback) {
            var spinnerBlock = $('body');
            priv.processing = true;
            displaySpinner(spinnerBlock);
            $.post(url, vars)
                .success(function(block) {
                    pub.loadTemplate($(block));
                })
                .error(function(err) {
                    console.error(err);
                }).always(function() {
                hideSpinner(spinnerBlock);
                if (typeof callback !== "undefined") {
                    callback(pub);
                }
            });

            return pub;
        },
        content: function(s) {
            if (s) {
                pub.$().find('.modal-body').html('').append(s);
                return pub;
            }
            return pub.$().find('.modal-body');
        },
        loadTemplate: function(modalBlock) {
            if (typeof modalBlock === "string") {
                modalBlock = $(modalBlock);
            }
            pub.content(modalBlock.find('content').eq(0).html());
            var script = modalBlock.find('script[type="text/modal-template"]').each(function() {
                var js = $(this).text();
                var f = new Function('modal', js);
                try {
                    f(pub);
                } catch (e) {
                    console.error(e);
                }
            });
            priv.processing = false;
            return pub;
        }
    };

    pub.$().on('click', '.close, .modal-close', function() {
        pub.hide();
    });

    if (url !== undefined) {
        pub.load(url);
    }

    pub.$().find('#customModal').data('modal', pub);

    return pub;
};

function displayModal(route, params) {
    $('.List__restaurant').slideUp();
    return $.customModal().load(Routing.generate.apply(Routing, arguments)).show();
}

function displayMessageModal(message, buttonText, callback) {
    message = $('<p style="text-align: center;">' + message + '</p>' + '<div class="text-center"><a class="modal-close Btn green-dark2 wide round-corners">' + (buttonText || 'OK') + '</a></div>');

    if (callback) {
        message.find('a.modal-close').on('click', callback);
    }

    var modal = $.customModal();
    modal.content(message);
    modal.show();

    return modal;
}

function getOrientation(file, callback) {
    var reader = new FileReader();
    reader.onload = function(e) {

        var view = new DataView(e.target.result);
        if (view.getUint16(0, false) != 0xFFD8) return callback(-2);
        var length = view.byteLength, offset = 2;
        while (offset < length) {
            var marker = view.getUint16(offset, false);
            offset += 2;
            if (marker == 0xFFE1) {
                if (view.getUint32(offset += 2, false) != 0x45786966) return callback(-1);
                var little = view.getUint16(offset += 6, false) == 0x4949;
                offset += view.getUint32(offset + 4, little);
                var tags = view.getUint16(offset, little);
                offset += 2;
                for (var i = 0; i < tags; i++)
                    if (view.getUint16(offset + (i * 12), little) == 0x0112)
                        return callback(view.getUint16(offset + (i * 12) + 8, little));
            }
            else if ((marker & 0xFF00) != 0xFF00) break;
            else offset += view.getUint16(offset, false);
        }
        return callback(-1);
    };
    reader.readAsArrayBuffer(file.slice(0, 64 * 1024));
}



$(document).ready(function () {
    var _config    = {
        prefix: "photoPreview"
    };
    var _methods = {};
    _methods.elementsToRender = function () {
        $("."+_config.prefix+":not(.active)").get().forEach(function (node) {
            _methods.render(node);
        });
    };

    _methods.render = function (node) {
        // mark
        var $node = $(node);

        $node.addClass("active");
        var input = $node.find("."+_config.prefix+"_control").find("input[type=\"file\"]").get();
        var wall  = $node.find("."+_config.prefix+"_wall");

        $(wall).on('click', function () {
            $(input).trigger("click");
        });
        $node.find("> input[type=\"button\"]").on('click', function () {
            $(input).trigger("focus");
            $(input).trigger("click");
        });

        $(input).on('change', function () {
            var file = this.files[0];
            if (this.files.length && file) {

                loadImage.parseMetaData(
                    file,
                    function (data) {
                        var degrees = 0;
                        if(data.hasOwnProperty('exif')){
                            var orientation = data.exif[0x0112];
                            switch(orientation) {
                                case 3:
                                    degrees = 180;
                                    break;
                                case 6:
                                    degrees = 90;
                                    break;
                                case 8:
                                    degrees = -90;
                                    break;
                                default:
                                    degrees = 0;
                            }
                        }

                        loadImage(
                            file,
                            function (canvas) {

                                var mycanvas = document.createElement("canvas");

                                if (degrees === 90 || degrees === -90) {
                                    mycanvas.width = canvas.height;
                                    mycanvas.height = canvas.width;
                                } else {
                                    mycanvas.width = canvas.width;
                                    mycanvas.height = canvas.height;
                                }


                                var ctx3 = mycanvas.getContext("2d");

                                if (degrees == 90) {
                                    ctx3.translate(canvas.height, 0);
                                }

                                if (degrees == -90) {
                                    ctx3.translate(0, canvas.width);
                                }

                                if (degrees == 180) {
                                    ctx3.translate(canvas.width, canvas.height);
                                }

                                ctx3.rotate((Math.PI/180)*degrees);
                                ctx3.drawImage(canvas,0,0);

                                $node.addClass("selected");
                                $(wall).css("backgroundImage", "url(\""+mycanvas.toDataURL()+"\")");
                            },
                            {
                                maxWidth: 600,
                                //maxHeight: 300,
                                orientation: true,
                                canvas: true
                            } // Options
                        );
                    }
                );




            } else {
                $node.removeClass("selected");
                $(wall).css("backgroundImage", "");
            }
        });
    };

    setInterval(function () {
        _methods.elementsToRender();
    }, 500);
});

$.extend({
    redirectPost: function(location, args)
    {
        var form = '';
        $.each( args, function( key, value ) {
            value = value.split('"').join('\"')
            form += '<input type="hidden" name="'+key+'" value="'+value+'">';
        });
        $('<form action="' + location + '" method="POST" style="display: none">' + form + '</form>').appendTo($(document.body)).submit();
    }
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

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

(function($, window) {
    var Starrr;

    Starrr = (function() {
        Starrr.prototype.defaults = {
            rating: void 0,
            numStars: 5,
            change: function(e, value) {}
        };

        function Starrr($el, options) {
            var i, _, _ref,
                _this = this;

            this.options = $.extend({}, this.defaults, options);
            this.$el = $el;
            _ref = this.defaults;
            for (i in _ref) {
                _ = _ref[i];
                if (this.$el.data(i) != null) {
                    this.options[i] = this.$el.data(i);
                }
            }
            this.createStars();
            this.syncRating();
            //this.$el.on('mouseover.starrr', 'i', function(e) {
            //    return _this.syncRating(_this.$el.find('i').index(e.currentTarget) + 1);
            //});
            //this.$el.on('mouseout.starrr', function() {
            //    return _this.syncRating();
            //});
            //this.$el.on('click.starrr', 'i', function(e) {
            //    return _this.setRating(_this.$el.find('i').index(e.currentTarget) + 1);
            //});
            this.$el.on('starrr:change', this.options.change);
        }

        Starrr.prototype.createStars = function() {
            var _i, _ref, _results;

            _results = [];
            for (_i = 1, _ref = this.options.numStars; 1 <= _ref ? _i <= _ref : _i >= _ref; 1 <= _ref ? _i++ : _i--) {
                _results.push(this.$el.append("<i class='fa fa-star-o'></i>"));
            }
            return _results;
        };

        //Starrr.prototype.setRating = function(rating) {
        //    if (this.options.rating === rating) {
        //        rating = void 0;
        //    }
        //    this.options.rating = rating;
        //    this.syncRating();
        //    return this.$el.trigger('starrr:change', rating);
        //};

        Starrr.prototype.syncRating = function(rating) {
            var i, _i, _j, _ref;

            rating || (rating = this.options.rating);

            if (rating) {
                rating = Math.round(rating*2)/2;

                for (i = 0;  i <= rating - 1 ; i++ ) {
                    this.$el.find('i').eq(i).removeClass('fa-star-o').addClass('fa-star')
                }

                if (rating != Math.ceil(rating)) {
                    this.$el.find('i').eq(i).addClass('fa-star-half-o')
                }
            }
            //if (rating && rating < 5) {
            //    for (i = _j = rating; rating <= 4 ? _j <= 4 : _j >= 4; i = rating <= 4 ? ++_j : --_j) {
            //        this.$el.find('i').eq(i).removeClass('fa-star').addClass('fa-star-o');
            //    }
            //}
            //if (!rating) {
            //    return this.$el.find('i').removeClass('fa-star').addClass('fa-star-o');
            //}
        };

        return Starrr;

    })();
    return $.fn.extend({
        starrr: function() {
            var args, option;

            option = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
            return this.each(function() {
                var data;

                data = $(this).data('star-rating');
                if (!data) {
                    $(this).data('star-rating', (data = new Starrr($(this), option)));
                }
                if (typeof option === 'string') {
                    return data[option].apply(data, args);
                }
            });
        }
    });
})(window.jQuery, window);

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
