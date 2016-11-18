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


