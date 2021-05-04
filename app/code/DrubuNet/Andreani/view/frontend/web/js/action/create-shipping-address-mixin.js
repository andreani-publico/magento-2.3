/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {
            if (messageContainer.custom_attributes !== undefined) {
                $.each(messageContainer.custom_attributes , function(key, value) {
                    if ($.isPlainObject(value)){
                        if(key !== undefined && !isNaN(key)) {
                            key = value['attribute_code'];
                        }
                        value = value['value'];
                    }

                    messageContainer['custom_attributes'][key] = value;
                });
            }

            return originalAction(messageContainer);
        });
    };
});
