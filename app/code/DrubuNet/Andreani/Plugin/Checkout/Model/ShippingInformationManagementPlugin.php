<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Framework\Exception\InputException;
use Magento\Quote\Model\ShippingAddressManagementInterface;

class ShippingInformationManagementPlugin
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * ShippingInformationManagementPlugin constructor.
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $sExtAttributes = $addressInformation->getShippingAddress()->getExtensionAttributes();
        $bExtAttributes = $addressInformation->getBillingAddress()->getExtensionAttributes();

        $quote = $this->quoteRepository->getActive($cartId);

        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setDni($sExtAttributes->getDni());
        $shippingAddress->setAltura($sExtAttributes->getAltura());
        $shippingAddress->setPiso($sExtAttributes->getPiso());
        $shippingAddress->setDepartamento($sExtAttributes->getDepartamento());
        $shippingAddress->setCelular($sExtAttributes->getCelular());
        $shippingAddress->setObservaciones($sExtAttributes->getObservaciones());
    }
}
