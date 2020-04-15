/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer'
], function(Component, customerData,quote,customer)
{
    'use strict';
    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-information/address-renderer/default'
        },
        isCustomerLoggedIn: customer.isLoggedIn,
        quote: quote,
        getCountryName: function(countryId) {
            return (countryData()[countryId] != undefined) ? countryData()[countryId].name : "";
        },
        getAltura: function ()
        {
            if(!isCustomerLoggedIn && jQuery('#shipping-new-address-form input[name="altura"]').val())
            {
                return jQuery('#shipping-new-address-form input[name="altura"]').val();
            }

            if(typeof this.address() != "undefined" && typeof this.address().customAttributes != "undefined")
            {
                if(typeof this.address().customAttributes.altura != "undefined"
                    && typeof this.address().customAttributes.altura.value != "undefined")
                    return this.address().customAttributes.altura.value;
                if(typeof this.address().customAttributes.altura != "undefined")
                    return this.address().customAttributes.altura;

                return '';
            }
        },
        getPiso: function ()
        {
            if(!isCustomerLoggedIn && jQuery('#shipping-new-address-form input[name="piso"]').val())
            {
                return ', Piso: ' + jQuery('#shipping-new-address-form input[name="piso"]').val();
            }

            if(typeof this.address() != "undefined" && typeof this.address().customAttributes != "undefined")
            {
                if(typeof this.address().customAttributes.piso != "undefined"
                    && typeof this.address().customAttributes.piso.value != "undefined")
                    return ', Piso: ' + this.address().customAttributes.piso.value;
                if(typeof this.address().customAttributes.piso != "undefined")
                    return ', Piso: ' + this.address().customAttributes.piso;

                return '';
            }
        },
        getDepartamento: function ()
        {
            if(!isCustomerLoggedIn && jQuery('#shipping-new-address-form input[name="departamento"]').val())
            {
                return ', Departamento: ' + jQuery('#shipping-new-address-form input[name="departamento"]').val();
            }

            if(typeof this.address() != "undefined" && typeof this.address().customAttributes != "undefined")
            {
                if(typeof this.address().customAttributes.departamento != "undefined"
                    && typeof this.address().customAttributes.departamento.value != "undefined")
                    return ', Departamento: ' + this.address().customAttributes.departamento.value;
                if(typeof this.address().customAttributes.departamento != "undefined")
                    return ', Departamento: ' + this.address().customAttributes.departamento;

                return '';
            }
        }
    });
});
