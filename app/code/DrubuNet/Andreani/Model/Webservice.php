<?php

namespace DrubuNet\Andreani\Model;

/**
 * Class Webservice
 *
 * @description Clase que interactua con el WS de Andreani.
 * @author Drubu Team
 * @package DrubuNet\Andreani\Model
 */
class Webservice
{
    const MODE_DEV       = 'dev';
    const MODE_PROD      = 'prod';

    protected $helper;
    protected $httpClientFactory;
    private $url;
    private $token;

    public function __construct(
        \DrubuNet\Andreani\Helper\Data $helper,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    )
    {
        $this->helper       = $helper;
        $this->httpClientFactory = $httpClientFactory;
        $this->url = $this->helper->getModo() == $this::MODE_DEV ? 'https://api.qa.andreani.com' : 'https://api.andreani.com';
    }

    /**
     * @description Obtengo el token necesario para comunicarme con los servicios de Andreani
     * @return string
     */
    public function login(){
        $username = ($this->helper->getUsuario());
        $password = ($this->helper->getPass());
        $encoded['code'] = "Authorization";
        $encoded['value'] = 'Basic ' . base64_encode($username . ':' . $password);
        $url = $this->url . '/login';
        $this->token = $this->doRequest($url,\Zend_Http_Client::GET, $encoded)->getHeader("x-authorization-token");
        return $this->token;
    }

    /**
     * Impongo la orden en el servicio
     */
    public function generarEnvio($params){
        $url = $this->url . '/v2/ordenes-de-envio';
        if(empty($this->token)){
            $this->login();
        }
        $encoded['code'] = "x-authorization-token";
        $encoded['value'] = $this->token;
        return $this->doPost($url,\Zend_Http_Client::POST, $encoded,json_encode($params))->getBody();//json_decode(,true);
    }

    /**
     * @param $nroOrden
     * @description Obtengo el detalle de un envio por número Andreani.
     * @return string
     */
    public function getOrderCreated($nroOrden){
        $url = $this->url . '/v2/ordenes-de-envio/' . $nroOrden;
        if(empty($this->token)){
            $this->login();
        }
        $encoded['code'] = "x-authorization-token";
        $encoded['value'] = $this->token;
        return $this->doRequest($url,\Zend_Http_Client::GET, $encoded)->getBody();
    }

    /**
     * @description Lista todas (o una) las sucursales de Andreani que son pausibles de admitir o retirar envíos
     * @return array
     */
    public function getSucursales(){
        $url = $this->url . '/v2/sucursales?canal=B2C&seHaceAtencionAlCliente=true';
        if(empty($this->token)){
            $this->login();
        }
        $encoded['code'] = "x-authorization-token";
        $encoded['value'] = $this->token;
        return json_decode($this->doRequest($url,\Zend_Http_Client::GET, $encoded)->getBody(),true);
    }

    /**
     * No soportado en V2
     * @param $nroOrden
     * @return string
     * @description Devuelve todos los movimientos de un envío por número Andreani.
     */
    public function getTrazasDeEnvio($nroOrden){
        $url = $this->url . '/v1/envios/' . $nroOrden . '/trazas';
        if(empty($this->token)){
            $this->login();
        }
        $encoded['code'] = "x-authorization-token";
        $encoded['value'] = $this->token;
        return $this->doRequest($url,\Zend_Http_Client::GET, $encoded)->getBody();
    }

    /**
     * No soportado en V2
     * @param $nroOrden
     * @return string
     * @description Devuelve la información de un envío por Número Andreani.
     */
    public function getEnvio($nroOrden){
        $url = $this->url . '/v1/envios/' . $nroOrden;
        if(empty($this->token)){
            $this->login();
        }
        $encoded['code'] = "x-authorization-token";
        $encoded['value'] = $this->token;
        return $this->doRequest($url,\Zend_Http_Client::GET, $encoded)->getBody();
    }

    /**
     * @param $params
     * @return string
     * @description Devuelve la tarifa de un envio a partir de parametros.
     */
    public function cotizarEnvio($params){
        $url = $this->url . '/v1/tarifas';
        $encoded = null;
        return json_decode($this->doRequest($url,\Zend_Http_Client::GET, $encoded,($params))->getBody(),true);
    }

    /**
     * No soportado en V2
     * @description Listado de las provinicas reconocidas según ISO-3166-2:AR
     * @return array
     */
    public function getRegions(){
        $url = $this->url . '/v1/regiones';
        if(empty($this->token)){
            $this->login();
        }
        $encoded = null;
        return json_decode($this->doRequest($url,\Zend_Http_Client::GET, $encoded)->getBody(),true);
    }

    private function doRequest($url, $method, $header = null, $params = null){
        $client = $this->httpClientFactory->create();
        $client->setUri($url);
        $client->setMethod($method);
        $client->setHeaders(\Zend_Http_Client::CONTENT_TYPE, 'application/json');
        $client->setHeaders('Accept','application/json');
        if(!is_null($header)) {
            $client->setHeaders($header['code'], $header['value']);
        }
        if(!is_null($params)) {
            if($method === \Zend_Http_Client::GET) {
                $client->setParameterGet($params); //json
            }
            else if ($method === \Zend_Http_Client::POST){
                $client->setRawData($params);
            }
        }

        return $client->request();
    }
    private function doPost($url, $method, $header = null, $params = null){
        $client = $this->httpClientFactory->create();
        $client->setUri($url);
        $client->setMethod($method);
        $client->setHeaders(\Zend_Http_Client::CONTENT_TYPE, 'application/json');
        $client->setHeaders('Accept','application/json');
        $client->setHeaders("x-authorization-token",$this->token);
        $client->setRawData($params); //json

        return $client->request();
    }
}
