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
