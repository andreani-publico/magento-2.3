<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel\RlAddress;

use DrubuNet\Andreani\Api\Data\RLAddressInterface;
use DrubuNet\Andreani\Model\RlAddress;
use DrubuNet\Andreani\Model\ResourceModel\RLAddress as RLAddressResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RLAddressInterface::ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(RlAddress::class, RLAddressResource::class);
    }
}
