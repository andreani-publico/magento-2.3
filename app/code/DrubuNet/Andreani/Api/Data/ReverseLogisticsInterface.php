<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Api\Data;

use Magento\Framework\DataObject;

interface ReverseLogisticsInterface
{
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const OPERATION = 'operation';
    const TRACKING_NUMBER = 'tracking_number';
    const CREATED_AT = 'created_at';
    const SELLER_STREET = 'seller_street';
    const SELLER_NUMBER = 'seller_number';
    const SELLER_FLOOR = 'seller_floor';
    const SELLER_APARTMENT = 'seller_apartment';
    const SELLER_OBSERVATIONS = 'seller_observations';
    const SELLER_POSTCODE = 'seller_postcode';
    const SELLER_CITY = 'seller_city';
    const SELLER_REGION = 'seller_region';
    const SELLER_COUNTRY = 'seller_country';
    const SELLER_BETWEEN_STREETS = 'seller_between_streets';
    const CUSTOMER_STREET = 'customer_street';
    const CUSTOMER_NUMBER = 'customer_number';
    const CUSTOMER_FLOOR = 'customer_floor';
    const CUSTOMER_APARTMENT = 'customer_apartment';
    const CUSTOMER_OBSERVATIONS = 'customer_observations';
    const CUSTOMER_POSTCODE = 'customer_postcode';
    const CUSTOMER_CITY = 'customer_city';
    const CUSTOMER_REGION = 'customer_region';
    const CUSTOMER_COUNTRY = 'customer_country';
    const CUSTOMER_BETWEEN_STREETS = 'customer_between_streets';


    public function getId();

    public function setId($id);

    /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * @param int $orderId
     * @return ReverseLogisticsInterface
     */
    public function setOrderId(int $orderId): ReverseLogisticsInterface;

    /**
     * @return string
     */
    public function getOperation(): string;

    /**
     * @param string $operation
     * @return ReverseLogisticsInterface
     */
    public function setOperation(string $operation): ReverseLogisticsInterface;

    /**
     * @return string
     */
    public function getTrackingNumber(): string;

    /**
     * @param string $trackingNumber
     * @return ReverseLogisticsInterface
     */
    public function setTrackingNumber(string $trackingNumber): ReverseLogisticsInterface;

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     * @return ReverseLogisticsInterface
     */
    public function setCreatedAt($createdAt): ReverseLogisticsInterface;

    /**
     * @return DataObject
     */
    public function getSellerAddress(): DataObject;

    /**
     * @param array|DataObject $sellerAddress
     * @return ReverseLogisticsInterface
     */
    public function setSellerAddress($sellerAddress): ReverseLogisticsInterface;

    /**
     * @return DataObject
     */
    public function getCustomerAddress(): DataObject;

    /**
     * @param array|DataObject $customerAddress
     * @return ReverseLogisticsInterface
     */
    public function setCustomerAddress($customerAddress): ReverseLogisticsInterface;
}
