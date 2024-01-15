define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function($){
    'use strict';
    return function(config) {
        $.validator.addMethod(
            "max-upload-size",
            function(value, elm) {
                var maxSize = parseInt(config.maxSize)
                if (elm.files[0] != undefined) {
                    if (elm.files[0].size > maxSize) {
                        return false;
                    }
                }
                return true;
            },
            $.mage.__("Files bigger than %1 MB not allowed").replace('%1', Math.ceil(config.maxSize / 1048576))
        );
    }
});
