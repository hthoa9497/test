define([
    'jquery',
    'jquery/ui',
    'mage/translate'
], function($){
    'use strict';

    $.widget('magezon.advancedContactAjaxSubmit', {
        options: {
            link: ''
        },

        _create: function() {
            let form = this.element;
            let self = this;
            let link = this.options.link;

            if (link) {
                $(document).on('submit', form, function(e) {
                    e.preventDefault();
                    if (typeof grecaptcha != "undefined") {
                        let response = grecaptcha.getResponse();
                        if (response.length == 0) {
                            alert($.mage.__('Google reCAPTCHA fail'));
                            return false;
                        }
                    }
                    self.submitAjax();
                });
            }
        },

        submitAjax: function() {
            let link = this.options.link;
            let form = this.element;
            $('.mz_loadfr').css('display', 'block');
            $.ajax({
                type: "POST",
                url: link,
                data: $(form).serialize(),
                success: function() {
                    $('.mz_loadfr').css('display', 'none');
                    $("html, body").animate({scrollTop:0}, 500, 'swing');
                    form.trigger("reset");
                }
            });
        }
    });

    return $.magezon.advancedContactAjaxSubmit;
});