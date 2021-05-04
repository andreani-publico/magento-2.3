<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\Source;

class DocumentType implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            'DNI'    => 'DNI',
            'CUIT'     => 'CUIT',
            'CUIL'     => 'CUIL',
        ];
    }
}
