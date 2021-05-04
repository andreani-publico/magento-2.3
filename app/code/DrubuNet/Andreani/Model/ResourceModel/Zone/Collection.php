<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel\Zone;

use DrubuNet\Andreani\Api\Data\ZoneInterface;
use DrubuNet\Andreani\Model\Zone;
use DrubuNet\Andreani\Model\ResourceModel\Zone as ResourceZone;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ZoneInterface::ZONE_ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(Zone::class, ResourceZone::class);
    }
}
