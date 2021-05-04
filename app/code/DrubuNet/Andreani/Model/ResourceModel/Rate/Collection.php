<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel\Rate;

use DrubuNet\Andreani\Api\Data\RateInterface;
use DrubuNet\Andreani\Model\Rate;
use DrubuNet\Andreani\Model\ResourceModel\Rate as ResourceRate;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RateInterface::RATE_ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(Rate::class, ResourceRate::class);
    }
}
