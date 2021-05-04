# Módulo de envíos ANDREANI 2.3 / 2.4

## Requisitos
```
Magento version >= 2.3 
```

## Instalación

Para concretar la instalacion del módulo es necesario realizar los siguientes pasos. Primeramente, parados en la carpeta root del proyecto:

1. Pegar la carpeta de app sobre el root de magento. Combine las carpetas que ya existan.
2. Ejecute los siguientes comandos de magento:
```
	php bin/magento setup:upgrade
	php bin/magento cache:clean
```




## Configuración

1. En el admin de Magento, ingresar a Store -> Configuration -> Sales -> Shipping Methods
2. Configurar los contratos provistos por andreani 
3. En el admin de Magento, ingresar a Store -> Configuration -> Sales -> Shipping Settings
4. Configurar el numero de cliente, credenciales, datos del origen del envio y del remitente.
