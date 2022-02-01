<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Api\Data;

interface RLItemInterface
{
    const ID = 'id';
    const PARENT_ID = 'parent_id';
    const SKU = 'sku';
    const NAME = 'name';
    const QTY = 'qty';
    const CREATED_AT = 'created_at';


    public function getId();

    public function setId($id);

    /**
     * @return int
     */
    public function getParentId(): int;

    /**
     * @param int $orderId
     * @return RLItemInterface
     */
    public function setParentId(int $orderId): RLItemInterface;

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @param string $sku
     * @return RLItemInterface
     */
    public function setSku(string $sku): RLItemInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return RLItemInterface
     */
    public function setName(string $name): RLItemInterface;

    /**
     * @return int
     */
    public function getQty(): int;

    /**
     * @param int $qty
     * @return RLItemInterface
     */
    public function setQty(int $qty): RLItemInterface;

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     * @return RLItemInterface
     */
    public function setCreatedAt($createdAt): RLItemInterface;
}
