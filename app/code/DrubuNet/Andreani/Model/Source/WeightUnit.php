<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\Source;

class WeightUnit implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            'kilos' => 'kg / m3',
            'gramos'=> 'gramos / cm3'
        ];
    }
}
