define(
    [
        'mage/utils/wrapper',
        'ko'
    ] ,
    function (wrapper, ko) {
        'use strict';

        return function (billingAddressView) {
            return billingAddressView.extend(
                {
                    defaults: {
                        detailsTemplate: 'DrubuNet_Andreani/billing-address/details'
                    },

                    getCustomAttributeValue: function (attribute_code) {
                        var candidate;

                        if (this.currentBillingAddress() && this.currentBillingAddress().customAttributes) {
                            if (this.currentBillingAddress().customAttributes[attribute_code]) {
                                return this.currentBillingAddress().customAttributes[attribute_code];
                            }

                            if (Array.isArray(this.currentBillingAddress().customAttributes)) {
                                candidate = this.currentBillingAddress().customAttributes.find(function (attr) {
                                    return attr.attribute_code === attribute_code;
                                });

                                if (candidate) {
                                    return candidate.value;
                                }
                            }
                        }

                        return '';
                    },

                    /**
                     * Renders altura field
                     *
                     * @returns {String}
                     */
                    getAltura: function () {
                        return this.getCustomAttributeValue('altura');
                    },

                    getPiso: function () {
                        var piso = this.getCustomAttributeValue('piso');

                        if (piso) {
                            return ', Piso: ' + piso;
                        }
                    },

                    getDepartamento: function () {
                        var depto = this.getCustomAttributeValue('departamento');

                        if (depto) {
                            return ', Departamento: ' + depto;
                        }
                    }
                },
            );
        }
    }
);