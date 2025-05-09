<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel\RlOrder;

use DrubuNet\Andreani\Api\Data\RLOrderInterface;
use DrubuNet\Andreani\Model\RLOrder;
use DrubuNet\Andreani\Model\ResourceModel\RLOrder as RLOrderResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RLOrderInterface::ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(RLOrder::class, RLOrderResource::class);
    }
}
