/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'DrubuNet_Andreani/js/view/shipping': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'DrubuNet_Andreani/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'DrubuNet_Andreani/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'DrubuNet_Andreani/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'DrubuNet_Andreani/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'DrubuNet_Andreani/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'DrubuNet_Andreani/js/view/shipping-information-mixin': true,
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'DrubuNet_Andreani/js/view/shipping-address/address-renderer/default': true,
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'DrubuNet_Andreani/js/view/shipping-information/address-renderer/default': true,
            },
            'Magento_Checkout/js/view/billing-address': {
                'DrubuNet_Andreani/js/view/billing-address': true,
            },
        }
    },
};
