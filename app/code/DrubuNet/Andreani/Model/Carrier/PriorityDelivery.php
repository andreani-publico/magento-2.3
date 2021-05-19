<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\Carrier;

use DrubuNet\Andreani\Model\ShippingProcessor;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;

class PriorityDelivery extends AbstractCarrier implements CarrierInterface
{
    const CARRIER_CODE = 'andreaniurgente';
    const METHOD_CODE = 'urgente';
    /**
     * @var string
     */
    protected $_code = self::CARRIER_CODE;

    /**
     * @var bool
     */
    protected $_isFixed = true;
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var ShippingProcessor
     */
    protected $shippingProcessor ;

    /**
     * @var \DrubuNet\Andreani\Helper\Data
     */
    protected $andreaniHelper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \DrubuNet\Andreani\Service\AndreaniApiService $andreaniApiService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \DrubuNet\Andreani\Model\ShippingProcessor $shippingProcessor,
        \DrubuNet\Andreani\Helper\Data $andreaniHelper,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->shippingProcessor = $shippingProcessor;
        $this->andreaniHelper = $andreaniHelper;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();

        $shippingPrice = $this->getShippingPrice($request);

        if ($shippingPrice !== false) {
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * Returns shipping price
     *
     * @param RateRequest $request
     * @return bool|float
     */
    private function getShippingPrice(RateRequest $request)
    {
        $shippingPrice = false;
        $rate = $this->shippingProcessor->getRate($request->getAllItems(), $request->getDestPostcode(), \DrubuNet\Andreani\Model\Carrier\PriorityDelivery::CARRIER_CODE);
        if(!$request->getFreeShipping()) {
            if($rate->getStatus()){
                $shippingPrice = $rate->getPrice();
                $this->checkoutSession->setAndreaniPriorityRateWithoutTax($rate->getPriceWithoutTax());
            }
            if(!is_bool($shippingPrice)) {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);
            }
        }
        else{
            $shippingPrice = 0;
            if($rate->getStatus()){
                $this->checkoutSession->setAndreaniPriorityRateWithoutTax($rate->getPriceWithoutTax());
            }
        }

        return $shippingPrice;
    }

    /**
     * Creates result method
     *
     * @param int|float $shippingPrice
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function createResultMethod($shippingPrice)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier(self::CARRIER_CODE);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod(self::METHOD_CODE);
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);
        return $method;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    public function isShippingLabelsAvailable()
    {
        return true;
    }
}
