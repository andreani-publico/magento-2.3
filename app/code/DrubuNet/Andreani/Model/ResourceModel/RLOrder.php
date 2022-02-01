<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\ResourceModel;

use DrubuNet\Andreani\Api\Data\RLAddressInterface;
use DrubuNet\Andreani\Api\Data\RLItemInterface;
use DrubuNet\Andreani\Api\Data\RLOrderInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RLOrder extends AbstractDb
{
    const TABLE = 'drubunet_andreani_logisticainversa';

    /**
     * @var RLAddress
     */
    private $RLAddressResource;

    /**
     * @var RLItem
     */
    private $RLItemResource;

    /**
     * @var \DrubuNet\Andreani\Model\RLAddressFactory
     */
    private $RLAddressModelFactory;

    /**
     * @var \DrubuNet\Andreani\Model\ResourceModel\RLItem\CollectionFactory
     */
    private $RLItemCollectionFactory;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(self::TABLE, RLOrderInterface::ID);
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \DrubuNet\Andreani\Model\RLAddressFactory $RLAddressModelFactory,
        \DrubuNet\Andreani\Model\ResourceModel\RLAddress $RLAddressResource,
        \DrubuNet\Andreani\Model\ResourceModel\RLItem $RLItemResource,
        \DrubuNet\Andreani\Model\ResourceModel\RLItem\CollectionFactory $RLItemCollectionFactory,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->RLAddressResource = $RLAddressResource;
        $this->RLAddressModelFactory = $RLAddressModelFactory;
        $this->RLItemResource = $RLItemResource;
        $this->RLItemCollectionFactory = $RLItemCollectionFactory;
    }

    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        /**
         * @var \DrubuNet\Andreani\Model\RLOrder $order
         */
        $order = $object;
        //1. Save addresses
        /**
         * @var RLAddressInterface $originAddress
         */
        $originAddress = $object->getOriginAddress();
        /**
         * @var RLAddressInterface $destinationAddress
         */
        $destinationAddress = $object->getDestinationAddress();

        $originAddress->setAddressType(0);
        $destinationAddress->setAddressType(1);
        $this->RLAddressResource->save($originAddress);
        $this->RLAddressResource->save($destinationAddress);

        $order->setOriginAddressId($originAddress->getId());
        $order->setDestinationAddressId($destinationAddress->getId());

        $saveReference = parent::save($object);

        /**
         * @var RLItemInterface $item
         */
        foreach ($object->getItems() as $item){
            $item->setParentId($object->getId());
            $this->RLItemResource->save($item);
        }


        return $saveReference;
    }

    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        parent::load($object, $value, $field);
        /**
         * @var \DrubuNet\Andreani\Model\RLOrder $order
         */
        $order = $object;
        $originAddressId = $order->getData(RLOrderInterface::ORIGIN_ADDRESS_ID);
        $destinationAddressId = $order->getData(RLOrderInterface::DESTINATION_ADDRESS_ID);

        $originAddress = $this->RLAddressModelFactory->create();
        $this->RLAddressResource->load($originAddress,$originAddressId);
        $order->setData(RLOrderInterface::ORIGIN_ADDRESS,$originAddress);

        $destinationAddress = $this->RLAddressModelFactory->create();
        $this->RLAddressResource->load($destinationAddress,$destinationAddressId);
        $order->setData(RLOrderInterface::DESTINATION_ADDRESS,$destinationAddress);

        $itemCollection = $this->RLItemCollectionFactory->create()->addFieldToFilter(RLItemInterface::PARENT_ID,$order->getId());
        $orderItems = [];
        foreach ($itemCollection as $item){
            $orderItems[] = $item;
        }
        $order->setData(RLOrderInterface::ORDER_ITEMS,$orderItems);
    }
}
