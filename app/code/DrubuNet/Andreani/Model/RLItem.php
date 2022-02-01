<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Model;

use DrubuNet\Andreani\Api\Data\RLItemInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use DrubuNet\Andreani\Model\ResourceModel\RLItem as RLItemResourceModel;

class RLItem extends AbstractModel implements RLItemInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(RLItemResourceModel::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * @param int $orderId
     * @return RLItemInterface
     */
    public function setParentId(int $orderId): RLItemInterface
    {
        return $this->setData(self::PARENT_ID, $orderId);
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->getData(self::SKU);
    }

    /**
     * @param string $sku
     * @return RLItemInterface
     */
    public function setSku(string $sku): RLItemInterface
    {
        return $this->setData(self::SKU, $sku);
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
     * @return RLItemInterface
     */
    public function setName(string $name): RLItemInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->getData(self::QTY);
    }

    /**
     * @param int $qty
     * @return RLItemInterface
     */
    public function setQty(int $qty): RLItemInterface
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param $createdAt
     * @return RLItemInterface
     */
    public function setCreatedAt($createdAt): RLItemInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
