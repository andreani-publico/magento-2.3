<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Api\Data;

interface ZoneInterface
{
    const ZONE_ID = 'zona_id';
    const NAME = 'nombre';

    /**
     * @return int
     */
    public function getZoneId(): int;

    /**
     * @param int|null $zoneId
     * @return ZoneInterface
     */
    public function setZoneId(?int $zoneId): ZoneInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return ZoneInterface
     */
    public function setName(string $name): ZoneInterface;
}
