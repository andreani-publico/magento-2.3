define([], function() {
    'use strict';

    return {
        rules: {
            'country_id': {
                'required': true
            },
            'postcode': {
                'required': true
            }
        },

        /**
         * @return {Object}
         */
        getRules: function () {
            return this.rules;
        }
    };
});
