define([
    'jquery',
    'mage/utils/wrapper',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer'
], function ($, wrapper, ko, Component, customerData, quote, customer) {
    'use strict';

    return function (targetModule) {
        return targetModule.extend({
            defaults: {
                template: 'DrubuNet_Andreani/shipping-information/address-renderer/default'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            quote: quote,
            getAltura: function () {
                if (typeof (this.address()) != "undefined" && typeof (this.address().customAttributes) != "undefined") {
                    if (typeof (this.address().customAttributes.altura) != "undefined") {
                        return this.address().customAttributes.altura.value;
                    } else {
                        let alturaValue = null;
                        $.each(this.address().customAttributes, function (key, value) {
                            if (!isNaN(key)) {
                                if (value.attribute_code == 'altura') {
                                    alturaValue = (typeof value.value.value !== 'undefined') ? value.value.value : value.value;
                                }
                            }
                        });
                        return alturaValue;
                    }
                }

                return '';
            },
            getPiso: function () {
                if(typeof(this.address()) != "undefined" && typeof(this.address().customAttributes) != "undefined") {
                    if (typeof (this.address().customAttributes.piso) != "undefined") {
                        return ', Piso: ' + this.address().customAttributes.piso.value;
                    } else {
                        let pisoValue = null;
                        $.each(this.address().customAttributes , function( key, value ) {
                            if(!isNaN(key)){
                                if(value.attribute_code == 'piso'){
                                    return pisoValue = (typeof value.value.value !== 'undefined') ? value.value.value : value.value;
                                }
                            }
                        });
                        if(pisoValue){
                            return ', Piso: ' + pisoValue;
                        }
                    }
                }
                return '';
            },
            getDepartamento: function () {
                if(typeof(this.address()) != "undefined" && typeof(this.address().customAttributes) != "undefined") {
                    if(typeof(this.address().customAttributes.departamento) != "undefined") {
                        return ', Departamento: ' + this.address().customAttributes.departamento.value;
                    }
                    else {
                        let deptoValue = null;
                        $.each(this.address().customAttributes , function( key, value ) {
                            if(!isNaN(key)){
                                if(value.attribute_code == 'departamento'){
                                    return deptoValue = (typeof value.value.value !== 'undefined') ? value.value.value : value.value;
                                }
                            }
                        });
                        if(deptoValue){
                            return ', Departamento: ' + deptoValue;
                        }
                    }
                }
                return '';
            }
        });
    };
});