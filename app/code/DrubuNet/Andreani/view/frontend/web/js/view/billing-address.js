/*******************************************************************************
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

define([
    'ko',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/set-billing-address',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'Magento_Checkout/js/model/billing-address-postcode-validator'
],
function (
    ko,
    _,
    Component,
    customer,
    addressList,
    quote,
    createBillingAddress,
    selectBillingAddress,
    checkoutData,
    checkoutDataResolver,
    customerData,
    setBillingAddressAction,
    globalMessageList,
    $t,
    billingAddressPostcodeValidator
) {
    return function (BillingAddress) {
        return BillingAddress.extend({
            defaults: {
                detailsTemplate: 'DrubuNet_Andreani/billing-address/details',
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
