<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Plugin\Quote\Model;


class ShippingAddressManagement
{
    public function beforeAssign(
        \Magento\Quote\Model\ShippingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
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
                $logMessage = "Method: ShippingAddressManagement::beforeAssign\n";
                $logMessage .= "Message: " . $e->getMessage() . "\n";
                \DrubuNet\Andreani\Helper\Data::log($logMessage, 'andreani_attribute_errors_' . date('Y_m') . '.log');
            }
        }
    }
}
