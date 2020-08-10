define(
    [
        'jquery',
        'DrubuNet_Andreani/js/view/checkout/shipping/model/resource-url-manager',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/shipping-service',
        'DrubuNet_Andreani/js/view/checkout/shipping/model/sucursales-registry',
        'DrubuNet_Andreani/js/view/checkout/shipping/model/provincias-registry',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Catalog/js/price-utils'
    ],
    function ($,resourceUrlManager, quote, customer, storage, urlBuilder, shippingService, sucursalesRegistry,provinciasRegistry, errorProcessor,priceUtils) {
        'use strict';

        return {
            /**
             * Get nearest machine list for specified address
             * @param {Object} address
             */
            getSucursalList: function (address, form, andreanisucursal_provincia, andreanisucursal_localidad) {
                shippingService.isLoading(true);
                var cacheKey = address.getCacheKey() + "_" + andreanisucursal_provincia + "_" + andreanisucursal_localidad,
                    cache = sucursalesRegistry.get(cacheKey),
                    serviceUrl = resourceUrlManager.getUrlForSucursalList(andreanisucursal_provincia,andreanisucursal_localidad);

                if (cache) {
                    form.setSucursalList(cache);
                    shippingService.isLoading(false);
                } else {
                    storage.get(
                        serviceUrl, false
                    ).done(
                        function (result) {
                            sucursalesRegistry.set(cacheKey, result);
                            form.setSucursalList(result);
                            $('#andreanisucursal-errores').hide();
                        }
                    ).fail(
                        function (response) {
                            $('#andreanisucursal-errores > span').text('Hubo un error obteniendo las sucursales');
                            $('#andreanisucursal-errores').show();
                            errorProcessor.process(response);
                        }
                    ).always(
                        function () {
                            shippingService.isLoading(false);
                        }
                    );
                }
            },
            /**
             * Get nearest machine list for specified address
             * @param {Object} address
             */
            getProvinciaList: function (address, form) {
                shippingService.isLoading(true);
                var cacheKey = address.getCacheKey(),
                    cache = provinciasRegistry.get(cacheKey),
                    serviceUrl = resourceUrlManager.getUrlForProvinciaList(quote);

                if (cache) {
                    form.setProvinciaList(cache);
                    shippingService.isLoading(false);
                } else {
                    storage.get(
                        serviceUrl, false
                    ).done(
                        function (result) {
                            provinciasRegistry.set(cacheKey, result);
                            form.setProvinciaList(result);
                            $('#andreanisucursal-errores').hide();
                        }
                    ).fail(
                        function (response) {
                            $('#andreanisucursal-errores > span').text('Hubo un error obteniendo las sucursales');
                            $('#andreanisucursal-errores').show();

                            errorProcessor.process(response);
                        }
                    ).always(
                        function () {
                            shippingService.isLoading(false);
                        }
                    );
                }
            },

            getLocalidadList: function (address, form, andreanisucursal_provincia) {
                shippingService.isLoading(true);
                var cacheKey = address.getCacheKey() + "_" + andreanisucursal_provincia,
                    cache = provinciasRegistry.get(cacheKey),
                    serviceUrl = resourceUrlManager.getUrlForLocalidadList(andreanisucursal_provincia);

                if (cache) {
                    form.setLocalidadList(cache);
                    shippingService.isLoading(false);
                } else {
                    storage.get(
                        serviceUrl, false
                    ).done(
                        function (result) {
                            provinciasRegistry.set(cacheKey, result);
                            form.setLocalidadList(result);
                            $('#andreanisucursal-errores').hide();
                        }
                    ).fail(
                        function (response) {
                            $('#andreanisucursal-errores > span').text('Hubo un error obteniendo las sucursales');
                            $('#andreanisucursal-errores').show();
                            errorProcessor.process(response);
                        }
                    ).always(
                        function () {
                            shippingService.isLoading(false);
                        }
                    );
                }
            },

            getCotizacionSucursal: function (address, form) {
                shippingService.isLoading(true);
                //var cacheKey = address.getCacheKey() + "_cotizacion_" + quote.shippingAddress().extensionAttributes.andreanisucursal_provincia + "_" + quote.shippingAddress().extensionAttributes.andreanisucursal_localidad,
                    //cache = sucursalesRegistry.get(cacheKey),
                var serviceUrl = resourceUrlManager.getUrlForCotizacionSucursal(quote);
                storage.post(
                    serviceUrl,
                    JSON.stringify({"sucursal":form.getSucursal()}),
                    true
                )
                    .done(function (response) {
                        let costoEnvio = priceUtils.formatPrice(response[0].shippingPrice, quote.getPriceFormat());
                        jQuery(jQuery(jQuery("#label_method_sucursal_andreanisucursal").parent()[0].childNodes[3]).children('span')).text(costoEnvio)
                        $('#andreanisucursal-errores').hide();
                    })
                    .fail(function (response) {
                        $('#andreanisucursal-errores > span').text('Hubo un error obteniendo la cotizacion');
                        $('#andreanisucursal-errores > span').show();
                    })
                    .always(function () {
                        shippingService.isLoading(false);
                    });
            },

        };
    }
);
/*
            else
            {
                shippingService.isLoading(true);
                storage.post(
                '%URL for shipping rate estimation%',
                    JSON.stringify({
                       // '%address parameters%'
            }),
                false
            ).done(
                    function (result) {
                        rateRegistry.set(address.getKey(), result);
                        shippingService.setShippingRates(result);
                    }
                ).fail(
                    function (response) {
                        shippingService.setShippingRates([]);
                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        shippingService.isLoading(false);
                    }
                );
            }
 */
