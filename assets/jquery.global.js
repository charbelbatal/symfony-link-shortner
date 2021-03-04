"use strict";

(function($) {
    $.fn.extend({
        addFormValidation: function () {
            $(this).validate({
                ignore: ".ignore",
                highlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group").addClass(errorClass).removeClass(validClass);
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group").removeClass(errorClass).addClass(validClass);
                },
                errorPlacement: function(error, element){
                    $(element).parent('.form-group').find('label.error').remove();
                    error.insertAfter(element);
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
        }
    })
})(jQuery);