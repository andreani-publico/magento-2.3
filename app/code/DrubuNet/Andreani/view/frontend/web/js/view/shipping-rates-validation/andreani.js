define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../../model/shipping-rates-validator/andreani',
    '../../model/shipping-rates-validation-rules/andreani'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    andreaniShippingRatesValidator,
    andreaniShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('andreaniestandar', andreaniShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('andreaniestandar', andreaniShippingRatesValidationRules);

    defaultShippingRatesValidator.registerValidator('andreanisucursal', andreaniShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('andreanisucursal', andreaniShippingRatesValidationRules);

    defaultShippingRatesValidator.registerValidator('andreaniurgente', andreaniShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('andreaniurgente', andreaniShippingRatesValidationRules);

    return Component;
});
