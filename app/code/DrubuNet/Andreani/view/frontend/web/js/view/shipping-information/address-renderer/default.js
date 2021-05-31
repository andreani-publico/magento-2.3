/*******************************************************************************
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

define([
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function ($, registry, quote) {
    'use strict';

    return function (AddressRenderer) {
        return AddressRenderer.extend({
            defaults: {
                template: 'DrubuNet_Andreani/shipping-information/address-renderer/default'
            },
            getAndreaniStreetAttributes: function (customAttributes) {
                var andreaniAttributes = ['altura','piso','departamento'];
                var attributeLabels = '';
                for(let pos in andreaniAttributes) {
                    let attributeCode = andreaniAttributes[pos];
                    if (attributeCode in customAttributes && customAttributes[attributeCode].value !== '') {
                        if(attributeCode === 'altura') {
                            attributeLabels += ' ' + customAttributes[attributeCode].value + ', ';
                        }
                        else{
                            attributeLabels += attributeCode.charAt(0).toUpperCase() + attributeCode.slice(1) + ': ' + customAttributes[attributeCode].value + ', ';
                        }
                    }
                    else if(Array.isArray(customAttributes)){
                        for(let arrayPos in customAttributes){
                            let attribute = customAttributes[arrayPos];
                            if(attributeCode === attribute.attribute_code && attribute.value !== ''){
                                if(attributeCode === 'altura') {
                                    attributeLabels += ' ' + attribute.value + ', ';
                                }
                                else{
                                    attributeLabels += attributeCode.charAt(0).toUpperCase() + attributeCode.slice(1) + ': ' + attribute.value + ', ';
                                }
                                break;
                            }
                        }
                    }
                }

                return attributeLabels.slice(0, -2);
            }
        });
    };
});
