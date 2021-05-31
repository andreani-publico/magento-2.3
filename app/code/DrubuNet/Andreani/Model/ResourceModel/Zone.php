<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel;

use DrubuNet\Andreani\Api\Data\ZoneInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Zone extends AbstractDb
{
    const TABLE = 'drubunet_andreani_zona';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(self::TABLE, ZoneInterface::ZONE_ID);
    }
}
