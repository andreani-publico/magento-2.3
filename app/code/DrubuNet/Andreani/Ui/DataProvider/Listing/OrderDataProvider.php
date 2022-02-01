<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Ui\DataProvider\Listing;

use Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory;

class OrderDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
    }

    public function getCollection()
    {
        if(!$this->collection){
            $collection = $this->collectionFactory->create();
            $select = $collection->getSelect();

            $select->join(
                ["so" => $collection->getTable("sales_order")],
                'main_table.entity_id = so.entity_id',
                array('shipping_method')
            );
            $select->joinLeft(
                ["ss" => $collection->getTable("sales_shipment")],
                'main_table.entity_id = ss.order_id',
                array('order_id')
            );

            $methodsCondition =
                '\'' . \DrubuNet\Andreani\Model\Carrier\PickupDelivery::CARRIER_CODE . '_' . \DrubuNet\Andreani\Model\Carrier\PickupDelivery::METHOD_CODE . '\', ' .
                '\'' . \DrubuNet\Andreani\Model\Carrier\StandardDelivery::CARRIER_CODE . '_' . \DrubuNet\Andreani\Model\Carrier\StandardDelivery::METHOD_CODE . '\', ' .
                '\'' . \DrubuNet\Andreani\Model\Carrier\BiggerDelivery::CARRIER_CODE . '_' . \DrubuNet\Andreani\Model\Carrier\BiggerDelivery::METHOD_CODE . '\', ' .
                '\'' . \DrubuNet\Andreani\Model\Carrier\PriorityDelivery::CARRIER_CODE . '_' . \DrubuNet\Andreani\Model\Carrier\PriorityDelivery::METHOD_CODE . '\'';

            $select->where("ss.order_id IS NULL and so.shipping_method in ($methodsCondition)");

            $this->collection = $collection;
        }
        return $this->collection;
    }
}