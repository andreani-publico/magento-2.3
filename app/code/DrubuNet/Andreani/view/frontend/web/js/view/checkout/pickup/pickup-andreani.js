
/*
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'mage/translate',
    'Magento_Catalog/js/price-utils'
], function ($, ko, Component, url, quote, shippingService, t, priceUtils) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'DrubuNet_Andreani/checkout/pickup/pickup-andreani'
        },

        initialize: function (config) {
            this.provinces = ko.observableArray();
            this.provinces(Object.keys(checkoutConfig.andreani.stores));
            this.selectedProvince = ko.observable();
            this.cities = ko.observableArray();
            this.selectedCity = ko.observable();
            this.stores = ko.observableArray();
            this.selectedStore = ko.observable();
            this.andreaniErrorMessage = ko.observable();
            this._super();
        },

        initObservable: function () {
            this._super();

            this.showProvinceSection = ko.computed(function() {
                return this.provinces().length != 0
            }, this);
            this.showCitySection = ko.computed(function() {
                return this.cities().length != 0
            }, this);
            this.showStoreSection = ko.computed(function() {
                return this.stores().length != 0
            }, this);

            this.selectedMethod = ko.computed(function() {
                var method = quote.shippingMethod();
                return method != null ? method.carrier_code + '_' + method.method_code : null;
            }, this);

            return this;
        },


        getCotizacionStore:function(){
            storeService.getCotizacionStore(quote.shippingAddress(), this);
        },

        provinceChange: function(obj, event){
            if(this.selectedProvince() && this.selectedProvince() in checkoutConfig.andreani.stores) {
                this.cities(Object.keys(checkoutConfig.andreani.stores[this.selectedProvince()]));
            }
            else{
                this.cities([]);
            }
            this.selectedCity(null);
        },
        cityChange: function(obj, event){
            if(this.selectedProvince() && this.selectedCity() && this.selectedProvince() in checkoutConfig.andreani.stores && this.selectedCity() in checkoutConfig.andreani.stores[this.selectedProvince()]) {
                this.stores(Object.keys(checkoutConfig.andreani.stores[this.selectedProvince()][this.selectedCity()])); //borro las stores
            }
            else{
                this.stores([]);
            }
            this.selectedStore(null);
        },
        storeChange: async function (obj, event) {
            if (this.selectedStore()){
                var self = this;
                $.ajax({
                    url: url.build('andreani/checkout/pickuprates'),
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    data: {
                        store_id: checkoutConfig.andreani.stores[this.selectedProvince()][this.selectedCity()][this.selectedStore()].codigo,
                        store_name: this.selectedStore(),
                        quote_id: quote.getQuoteId(),
                        address_zip: checkoutConfig.andreani.stores[this.selectedProvince()][this.selectedCity()][this.selectedStore()].direccion.codigoPostal
                    },
                    complete: function (response) {
                        if(response.status == 200 && response.responseJSON.status){
                            let costoEnvio = priceUtils.formatPrice(response.responseJSON.price, quote.getPriceFormat());
                            jQuery('#label_method_sucursal_andreanisucursal').siblings('.col-price').children('span').text(costoEnvio);
                            self.andreaniErrorMessage('');
                        }
                        else{
                            self.andreaniErrorMessage('No se encontraron cotizaciones para la sucursal seleccionada');
                        }
                    },
                    error: function (xhr, status, errorThrown) {
                    }
                });
            }
        },
    });

});
