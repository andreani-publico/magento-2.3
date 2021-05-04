<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Api\Data;

interface RateInterface
{
    const RATE_ID = 'tarifa_id';
    const RANGE = 'rango';
    const STANDARD_VALUE = 'valor_estandar';
    const PICKUP_VALUE = 'valor_sucursal';
    const PRIORITY_VALUE = 'valor_urgente';
    const ZONE_ID = 'zona_id';

    /**
     * @return int
     */
    public function getRateId(): int;

    /**
     * @param int|null $rateId
     * @return RateInterface
     */
    public function setRateId(?int $rateId): RateInterface;

    /**
     * @return string
     */
    public function getRange(): string;

    /**
     * @param string $range
     * @return RateInterface
     */
    public function setRange(string $range): RateInterface;

    /**
     * @return float
     */
    public function getStandardValue(): float;

    /**
     * @param float $value
     * @return RateInterface
     */
    public function setStandardValue(float $value): RateInterface;

    /**
     * @return float
     */
    public function getPickupValue(): float;

    /**
     * @param float $value
     * @return RateInterface
     */
    public function setPickupValue(float $value): RateInterface;

    /**
     * @return float
     */
    public function getPriorityValue(): float;

    /**
     * @param float $value
     * @return RateInterface
     */
    public function setPriorityValue(float $value): RateInterface;

    /**
     * @return ZoneInterface
     */
    public function getZone(): ZoneInterface;

    /**
     * @param int|ZoneInterface $zone
     * @return RateInterface
     */
    public function setZone(ZoneInterface $zone): RateInterface;
}
