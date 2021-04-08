/**
 * @type {{map: {*: {}}}}
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'DrubuNet_Andreani/js/view/shipping': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'DrubuNet_Andreani/js/view/billing-address': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'DrubuNet_Andreani/js/view/shipping-address/address-renderer/default': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'DrubuNet_Andreani/js/view/shipping-information/address-renderer/default':true
            },
        }
    }
};
