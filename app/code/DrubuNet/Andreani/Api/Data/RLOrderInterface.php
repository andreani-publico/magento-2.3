<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Api\Data;

use Magento\Framework\DataObject;

interface RLOrderInterface
{
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const OPERATION = 'operation';
    const OPERATION_LABEL = 'operation_label';
    const TRACKING_NUMBER = 'tracking_number';
    const LINKING = 'linking';
    const ORIGIN_ADDRESS_ID = 'origin_address_id';
    const ORIGIN_ADDRESS = 'origin_address';
    const DESTINATION_ADDRESS_ID = 'destination_address_id';
    const DESTINATION_ADDRESS = 'destination_address';
    const CREATED_AT = 'created_at';
    const ORDER_ITEMS = 'items';



    public function getId();

    public function setId($id);

    /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * @param int $orderId
     * @return RLOrderInterface
     */
    public function setOrderId(int $orderId): RLOrderInterface;

    /**
     * @return string
     */
    public function getOperation(): string;

    /**
     * @param string $operation
     * @return RLOrderInterface
     */
    public function setOperation(string $operation): RLOrderInterface;

    /**
     * @return string
     */
    public function getOperationLabel(): string;

    /**
     * @param string $operationLabel
     * @return RLOrderInterface
     */
    public function setOperationLabel(string $operationLabel): RLOrderInterface;

    /**
     * @return string
     */
    public function getTrackingNumber(): string;

    /**
     * @param string $trackingNumber
     * @return RLOrderInterface
     */
    public function setTrackingNumber(string $trackingNumber): RLOrderInterface;

    /**
     * @return mixed
     */
    public function getLinking();

    /**
     * @param mixed $linking
     * @return RLOrderInterface
     */
    public function setLinking($linking): RLOrderInterface;

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     * @return RLOrderInterface
     */
    public function setCreatedAt($createdAt): RLOrderInterface;

    /**
     * @param $addressId
     * @return RLOrderInterface
     */
    public function setOriginAddressId($addressId): RLOrderInterface;


    /**
     * @return RLAddressInterface
     */
    public function getOriginAddress(): RLAddressInterface;

    /**
     * @param array|DataObject $sellerAddress
     * @return RLOrderInterface
     */
    public function setOriginAddress($sellerAddress): RLOrderInterface;

    /**
     * @param $addressId
     * @return RLOrderInterface
     */
    public function setDestinationAddressId($addressId): RLOrderInterface;

    /**
     * @return RLAddressInterface
     */
    public function getDestinationAddress(): RLAddressInterface;

    /**
     * @param array|DataObject $customerAddress
     * @return RLOrderInterface
     */
    public function setDestinationAddress($customerAddress): RLOrderInterface;

    /**
     * @return RLItemInterface[]
     */
    public function getItems();

    /**
     * @param array|DataObject[] $items
     * @return RLOrderInterface
     */
    public function setItems($items);
}
