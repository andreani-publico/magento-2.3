<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */


namespace DrubuNet\Andreani\Observer\Sales\Order;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AfterPlaceOrder for move date information from quote to order, 'sales_model_service_quote_submit_success' event
 */
class AfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var Session
     */
    private $checkoutSession;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        Session $checkoutSession
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->addressRepository = $addressRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        if (!$order = $observer->getEvent()->getOrder()) {
            return $this;
        }

        $quote = $this->quoteRepository->get($order->getQuoteId());
        $qBillingAddress = $quote->getBillingAddress();
        $qShippingAddress = $quote->getShippingAddress();

        $oBillingAddress = $order->getBillingAddress();
        $oShippingAddress = $order->getShippingAddress();

        $oShippingAddress->setDni($qShippingAddress->getDni());
        $oShippingAddress->setAltura($qShippingAddress->getAltura());
        $oShippingAddress->setPiso($qShippingAddress->getPiso());
        $oShippingAddress->setDepartamento($qShippingAddress->getDepartamento());
        $oShippingAddress->setObservaciones($qShippingAddress->getObservaciones());
        $oShippingAddress->setCelular($qShippingAddress->getCelular());

        $oBillingAddress->setDni($qBillingAddress->getExtensionAttributes()->getDni());
        $oBillingAddress->setAltura($qBillingAddress->getExtensionAttributes()->getAltura());
        $oBillingAddress->setPiso($qBillingAddress->getExtensionAttributes()->getPiso());
        $oBillingAddress->setDepartamento($qBillingAddress->getExtensionAttributes()->getDepartamento());
        $oBillingAddress->setObservaciones($qBillingAddress->getExtensionAttributes()->getObservaciones());
        $oBillingAddress->setCelular($qBillingAddress->getExtensionAttributes()->getCelular());

        if($order->getShippingMethod() == \DrubuNet\Andreani\Model\Carrier\PickupDelivery::CARRIER_CODE . '_' . \DrubuNet\Andreani\Model\Carrier\PickupDelivery::METHOD_CODE) {
            $order->setCodigoSucursalAndreani($quote->getCodigoSucursalAndreani());
            $order->setShippingDescription($order->getShippingDescription() . ' - ' . $this->checkoutSession->getNombreAndreaniSucursal());
        }

        $this->orderRepository->save($order);

        $billingAddressId = $oBillingAddress->getCustomerAddressId();
        $shippingAddressId = $oShippingAddress->getCustomerAddressId();

        if(!empty($billingAddressId)){
            $billingAddress = $this->addressRepository->getById($billingAddressId);
            $needSave1 = $this->updateValue($billingAddress,'dni',$qBillingAddress->getExtensionAttributes()->getDni());
            $needSave2 = $this->updateValue($billingAddress,'altura',$qBillingAddress->getExtensionAttributes()->getAltura());
            $needSave3 = $this->updateValue($billingAddress,'piso',$qBillingAddress->getExtensionAttributes()->getPiso());
            $needSave4 = $this->updateValue($billingAddress,'departamento',$qBillingAddress->getExtensionAttributes()->getDepartamento());
            $needSave5 = $this->updateValue($billingAddress,'observaciones',$qBillingAddress->getExtensionAttributes()->getObservaciones());
            $needSave6 = $this->updateValue($billingAddress,'celular',$qBillingAddress->getExtensionAttributes()->getCelular());
            if($needSave1 || $needSave2 || $needSave3 || $needSave4 || $needSave5 || $needSave6){
                $this->addressRepository->save($billingAddress);
            }
        }
        if(!empty($shippingAddressId)) {
            $shippingAddress = $this->addressRepository->getById($shippingAddressId);
            $needSave1 = $this->updateValue($shippingAddress,'dni',$qShippingAddress->getDni());
            $needSave2 = $this->updateValue($shippingAddress,'altura',$qShippingAddress->getAltura());
            $needSave3 = $this->updateValue($shippingAddress,'piso',$qShippingAddress->getPiso());
            $needSave4 = $this->updateValue($shippingAddress,'departamento',$qShippingAddress->getDepartamento());
            $needSave5 = $this->updateValue($shippingAddress,'observaciones',$qShippingAddress->getObservaciones());
            $needSave6 = $this->updateValue($shippingAddress,'celular',$qShippingAddress->getCelular());
            if($needSave1 || $needSave2 || $needSave3 || $needSave4 || $needSave5 || $needSave6){
                $this->addressRepository->save($shippingAddress);
            }
        }

        $this->checkoutSession->unsCotizacionAndreaniSucursal();
        $this->checkoutSession->unsNombreAndreaniSucursal();

        return $this;
    }

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @param string $attributeCode
     * @param $newvalue
     */
    private function updateValue(&$address,$attributeCode,$newvalue){
        $result = false;
        $currentValue = ($attribute = $address->getCustomAttribute($attributeCode)) ? $attribute->getValue() : '';
        if(!empty($newvalue) && $newvalue != $currentValue){
            $address->setCustomAttribute($attributeCode, $newvalue);
            $result = true;
        }
        return $result;
    }

}
