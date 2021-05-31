/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {
            let shippingAddress = quote.shippingAddress();

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            if (shippingAddress.customAttributes !== undefined) {
                $.each(shippingAddress.customAttributes , function(key, value) {
                    if ($.isPlainObject(value)){
                        if(key !== undefined && !isNaN(key)) {
                            key = value['attribute_code'];
                        }
                        value = value['value'];
                    }

                    shippingAddress['extension_attributes'][key] = value;
                });
            }

            return originalAction(messageContainer);
        });
    };
});
