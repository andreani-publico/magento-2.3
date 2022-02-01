<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Service;

use DrubuNet\Andreani\Helper\Data as AndreaniHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Webapi\Rest\Request;

class AndreaniApiService
{
    /**
     * @var AndreaniHelper
     */
    private $helper;

    /**
     * @var string
     */
    private $token;

    public function __construct(
        AndreaniHelper $data
    ) {
        $this->helper = $data;
    }

    public function login(){
        $username = $this->helper->getUsername();
        $password = $this->helper->getPassword();
        $response = $this->doRequest($this->helper->getLoginUrl(), [
            'header' => [
                'Authorization: Basic ' . base64_encode($username . ':' . $password),
            ]
        ]);
        if($response->getStatusCode() == 200){
            $this->token = '';
            $headers = $response->getHeaders();
            if(isset($headers['x-authorization-token'][0])) {
                $this->token = trim($headers['x-authorization-token'][0]);
                return $this->token;
            }
        }
        return $response->getReason();

    }

    /**
     * @return DataObject
     */
    public function getProvinces(){
        return $this->getDataFromResponse($this->doRequest($this->helper->getProvincesUrl()));
    }

    /**
     * @return DataObject
     */
    public function getLocations(){
        return $this->getDataFromResponse($this->doRequest($this->helper->getLocationUrl()));
    }

    /**
     * @param DataObject $data
     * @return DataObject
     */
    public function getRates(DataObject $data){
        if(empty($this->token)){
            $this->login();
        }
        return $this->getDataFromResponse($this->doRequest($this->helper->getRatesUrl() . '?' . http_build_query($data->getData())));
    }

    /**
     * @param DataObject $data
     * @return DataObject
     */
    public function createOrder(DataObject $data){
        if(empty($this->token)){
            $this->login();
        }
        return $this->getDataFromResponse($this->doRequest($this->helper->getCreateOrderUrl(),[
            'body' => $data->getData(),
            'header' => ['x-authorization-token: ' . $this->token]
        ], Request::HTTP_METHOD_POST));
    }

    /**
     * @param string $tracking
     * @return DataObject
     */
    public function getLabel($labelData, $isUrl = false){
        if(!$isUrl) {
            $tracking = $labelData;
            $labelUrl = str_replace('{numeroAndreani}', $tracking, $this->helper->getLabelUrl());
        }
        else{
            $labelUrl = $labelData;
        }
        if(empty($this->token)){
            $this->login();
        }
        return $this->getDataFromResponse($this->doRequest($labelUrl,
        [
            'header' => ['x-authorization-token: ' . $this->token]
        ],Request::HTTP_METHOD_GET, false));
    }

    /**
     * @param DataObject $data
     * @return DataObject
     */
    public function getShippingByNumber(DataObject $data){
        if(empty($this->token)){
            $this->login();
        }
        return $this->getDataFromResponse($this->doRequest($this->helper->getShippingByNumberUrl(),[
            'body' => $data->getData(),
            'x-authorization-token' => $this->token
        ], Request::HTTP_METHOD_POST));
    }

    /**
     * @param DataObject $response
     * @return DataObject
     */
    private function getDataFromResponse(DataObject $response){
        if($response->getStatusCode() == 200){
            return $response->getValue();
        }
        return $response->getReason();
    }

    /**
     * @param $uri
     * @param array $params
     * @param string $requestMethod
     * @param bool $parseToArray
     * @return DataObject
     */
    private function doRequest(
        $uri,
        $params = [],
        $requestMethod = Request::HTTP_METHOD_GET,
        $parseToArray = true
    ){
        $response = new DataObject();
        $curl = curl_init();
        $headers = [];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $uri,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $requestMethod,
        ));
        if(isset($params['header'])){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $params['header']);
        }
        if(isset($params['body'])){
            curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($params['body']));
        }

        curl_setopt($curl, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
        );

        $curlResponse = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if($statusCode < 200 || $statusCode >= 300){
            $response->setStatusCode($statusCode);
            $response->setReason(strval($curlResponse));
        }
        else{
            $response->setStatusCode(200);
            $value = $parseToArray ? json_decode($curlResponse,true) : $curlResponse;
            $response->setValue($value);
            $response->setHeaders($headers);
        }

        return $response;
    }
}
