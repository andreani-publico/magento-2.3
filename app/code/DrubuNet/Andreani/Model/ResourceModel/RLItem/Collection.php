<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel\RLItem;

use DrubuNet\Andreani\Api\Data\RLItemInterface;
use DrubuNet\Andreani\Model\RLItem;
use DrubuNet\Andreani\Model\ResourceModel\RLItem as RLItemResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RLItemInterface::ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(RLItem::class, RLItemResource::class);
    }


}
