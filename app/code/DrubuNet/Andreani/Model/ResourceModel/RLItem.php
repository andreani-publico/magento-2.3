<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel;

use DrubuNet\Andreani\Api\Data\RLItemInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RLItem extends AbstractDb
{
    const TABLE = 'drubunet_andreani_logisticainversa_productos';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(self::TABLE, RLItemInterface::ID);
    }
}
