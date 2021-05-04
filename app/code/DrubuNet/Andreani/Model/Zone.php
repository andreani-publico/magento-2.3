<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Model;

use DrubuNet\Andreani\Api\Data\ZoneInterface;
use Magento\Framework\Model\AbstractModel;
use DrubuNet\Andreani\Model\ResourceModel\Zone as ZoneResourceModel;

class Zone extends AbstractModel implements ZoneInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ZoneResourceModel::class);
    }

    /**
     * @return int
     */
    public function getZoneId(): int
    {
        return $this->getData(self::ZONE_ID);
    }

    /**
     * @param int|null $zoneId
     * @return ZoneInterface
     */
    public function setZoneId(?int $zoneId): ZoneInterface
    {
        return $this->setData(self::ZONE_ID, $zoneId);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return ZoneInterface
     */
    public function setName(string $name): ZoneInterface
    {
        return $this->setData(self::NAME, $name);
    }
}
