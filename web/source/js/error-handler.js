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
