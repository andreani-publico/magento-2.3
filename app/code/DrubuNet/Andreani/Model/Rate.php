<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Model;

use DrubuNet\Andreani\Api\Data\RateInterface;
use DrubuNet\Andreani\Api\Data\ZoneInterface;
use Magento\Framework\Model\AbstractModel;
use DrubuNet\Andreani\Model\ResourceModel\Rate as RateResourceModel;

class Rate extends AbstractModel implements RateInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(RateResourceModel::class);
    }

    /**
     * @return int
     */
    public function getRateId(): int
    {
        return $this->getData(self::RATE_ID);
    }

    /**
     * @param int|null $rateId
     * @return RateInterface
     */
    public function setRateId(?int $rateId): RateInterface
    {
        return $this->setData(self::RATE_ID, $rateId);
    }

    /**
     * @return string
     */
    public function getRange(): string
    {
        return $this->getData(self::RANGE);
    }

    /**
     * @param string $range
     * @return RateInterface
     */
    public function setRange(string $range): RateInterface
    {
        return $this->setData(self::RANGE, $range);
    }

    /**
     * @return float
     */
    public function getStandardValue(): float
    {
        return $this->getData(self::STANDARD_VALUE);
    }

    /**
     * @param float $value
     * @return RateInterface
     */
    public function setStandardValue(float $value): RateInterface
    {
        return $this->setData(self::STANDARD_VALUE, $value);
    }

    /**
     * @return float
     */
    public function getPickupValue(): float
    {
        return $this->getData(self::PICKUP_VALUE);
    }

    /**
     * @param float $value
     * @return RateInterface
     */
    public function setPickupValue(float $value): RateInterface
    {
        return $this->setData(self::PICKUP_VALUE, $value);
    }

    /**
     * @return float
     */
    public function getPriorityValue(): float
    {
        return $this->getData(self::PRIORITY_VALUE);
    }

    /**
     * @param float $value
     * @return RateInterface
     */
    public function setPriorityValue(float $value): RateInterface
    {
        return $this->setData(self::PRIORITY_VALUE, $value);
    }

    /**
     * @return ZoneInterface
     */
    public function getZone(): ZoneInterface
    {
        // TODO: Implement getZone() method.
    }

    /**
     * @param ZoneInterface $zone
     * @return RateInterface
     */
    public function setZone(ZoneInterface $zone): RateInterface
    {
        // TODO: Implement setZone() method.
    }
}
