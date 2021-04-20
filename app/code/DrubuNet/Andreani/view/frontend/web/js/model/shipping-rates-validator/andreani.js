define([
    'jquery',
    'mageUtils',
    'mage/translate',
    '../shipping-rates-validation-rules/andreani'
], function ($, utils, $t, validationRules) {
    'use strict';

    return {
        validationErrors: [],

        /**
         * @param {Object} address
         * @returns {boolean}
         */
        validate: function (address) {
            var self = this;

            this.validationErrors = [];

            $.each(validationRules.getRules(), function (field, rule) {
                var message;

                if (rule.required && utils.isEmpty(address[field])) {
                    message = $t('Field ') + field + $t(' is required.');
                    self.validationErrors.push(message);
                }
            });

            return !this.validationErrors.length;
        }
    };
});
