<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\Source;

class TelephoneType implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            1    => 'Trabajo',
            2     => 'Celular',
            3     => 'Casa',
            4     => 'Otros',
        ];
    }
}
