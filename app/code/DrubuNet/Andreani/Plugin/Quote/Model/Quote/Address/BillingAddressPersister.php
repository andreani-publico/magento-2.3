<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Plugin\Quote\Model\Quote\Address;

class BillingAddressPersister
{
    public function beforeSave(
        \Magento\Quote\Model\Quote\Address\BillingAddressPersister $subject,
        $quote,
        \Magento\Quote\Api\Data\AddressInterface $address,
        $useForShipping = false
    ) {

        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {
            try {
                $address->setDni($extAttributes->getDni());
                $address->setAltura($extAttributes->getAltura());
                $address->setPiso($extAttributes->getPiso());
                $address->setDepartamento($extAttributes->getDepartamento());
                $address->setCelular($extAttributes->getCelular());
                $address->setObservaciones($extAttributes->getObservaciones());
            } catch (\Exception $e) {
                \DrubuNet\Andreani\Helper\Data::log('Error in BillingAddressPersister ' . $e->getMessage(),'andreani_attributes.log');
            }
        }
    }
}
