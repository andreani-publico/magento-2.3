<?php

namespace DrubuNet\Andreani\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    public function __construct(
        Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    //sections
    const PICKUP_CARRIER_SECTION = 'carriers/andreanisucursal/';
    const STANDARD_CARRIER_SECTION = 'carriers/andreaniestandar/';
    const PRIORITY_CARRIER_SECTION = 'carriers/andreaniurgente/';
    const SHIPPING_SECTION = 'shipping/andreani_configuration/';

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path/*,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE*/
        );
    }

    /*
     * Core config
     */
    public function getUsername(){
        return $this->getConfig(self::SHIPPING_SECTION . 'username');
    }

    public function getPassword(){
        return $this->encryptor->decrypt($this->getConfig(self::SHIPPING_SECTION . 'password'));
    }

    public function getClientNumber(){
        return $this->getConfig(self::SHIPPING_SECTION . 'client_number');
    }

    public function getWeightUnit(){
        return $this->getConfig(self::SHIPPING_SECTION . 'weight_unit');
    }

    public function getProductFixedPrice(){
        return $this->getConfig(self::SHIPPING_SECTION . 'product_fixed_price');
    }

    public function getProductFixedVolume(){
        return $this->getConfig(self::SHIPPING_SECTION . 'product_fixed_volume');
    }

    public function isProductionMode(){
        return boolval($this->getConfig(self::SHIPPING_SECTION . 'production_mode'));
    }

    public function getLoginUrl(){
        return $this->isProductionMode() ?
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_prod_urls/login') :
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_dev_urls/login');
    }

    public function getProvincesUrl(){
        return $this->isProductionMode() ?
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_prod_urls/provinces') :
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_dev_urls/provinces');
    }

    public function getLocationUrl(){
        return $this->isProductionMode() ?
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_prod_urls/locations') :
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_dev_urls/locations');
    }

    public function getRatesUrl(){
        return $this->isProductionMode() ?
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_prod_urls/rates') :
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_dev_urls/rates');
    }

    public function getCreateOrderUrl(){
        return $this->isProductionMode() ?
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_prod_urls/createOrder') :
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_dev_urls/createOrder');
    }

    public function getLabelUrl(){
        return $this->isProductionMode() ?
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_prod_urls/label') :
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_dev_urls/label');
    }

    public function getShippingByNumberUrl(){
        return $this->isProductionMode() ?
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_prod_urls/shippingByNumber') :
            $this->getConfig(self::SHIPPING_SECTION . 'andreani_rest_dev_urls/shippingByNumber');
    }

    public function getOrigStreet(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/street');
    }

    public function getOrigNumber(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/number');
    }

    public function getOrigCity(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/city');
    }

    public function getOrigPostcode(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/postcode');
    }

    public function getOrigRegion(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/region');
    }

    public function getOrigCountry(){
        $pais = $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/country');
        return $pais ? $pais : 'Argentina';
    }

    public function getOrigFloor(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/floor');
    }

    public function getOrigApartment(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/apartment');
    }

    public function getOrigBetweenStreets(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_origin_info/betweenStreets');
    }

    public function getSenderFullname(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_sender_info/fullname');
    }

    public function getSenderEmail(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_sender_info/email');
    }

    public function getSenderIdType(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_sender_info/idType');
    }

    public function getSenderId(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_sender_info/idNumber');
    }

    public function getSenderPhoneType(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_sender_info/phoneType');
    }

    public function getSenderPhoneNumber(){
        return $this->getConfig(self::SHIPPING_SECTION . 'andreani_sender_info/phoneNumber');
    }

    public function isDebugEnable(){
        return boolval($this->getConfig(self::SHIPPING_SECTION . 'debug_mode'));
    }

    /*
     * Shipping method config
     */
    public function getContractByType($type){
        return $this->getConfig("carriers/$type/contract");
    }

    public function getTitleByType($type){
        return $this->getConfig("carriers/$type/title");
    }


    /**
     * @param $mensaje String
     * @param $archivo String
     */
    public static function log($mensaje,$archivo)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$archivo);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($mensaje);
    }
}
