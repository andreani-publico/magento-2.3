/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

define([], function() {
    'use strict';

    return {
        /**
         * @return {Object}
         */
        getRules: function () {
            return {
                'country_id': {
                    'required': true
                },
                'postcode': {
                    'required': true
                }
            };
        }
    };
});