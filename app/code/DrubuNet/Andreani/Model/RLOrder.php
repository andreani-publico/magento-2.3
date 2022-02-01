<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Model;

use DrubuNet\Andreani\Api\Data\RLAddressInterface;
use DrubuNet\Andreani\Api\Data\RLItemInterface;
use DrubuNet\Andreani\Api\Data\RLOrderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use DrubuNet\Andreani\Model\ResourceModel\RLOrder as RLOrderResourceModel;
use DrubuNet\Andreani\Model\RLAddressFactory as RLAddressModelFactory;
use DrubuNet\Andreani\Model\RLItemFactory as RLItemModelFactory;

class RLOrder extends AbstractModel implements RLOrderInterface
{
    private $RLAddressModelFactory;

    private $RLItemModelFactory;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(RLOrderResourceModel::class);
    }

    public function __construct(
        RLAddressModelFactory $RLAddressModelFactory,
        RLItemModelFactory $RLItemModelFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->RLAddressModelFactory = $RLAddressModelFactory;
        $this->RLItemModelFactory = $RLItemModelFactory;
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
    public function getOrderId(): int
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return RLOrderInterface
     */
    public function setOrderId(int $orderId): RLOrderInterface
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->getData(self::OPERATION);
    }

    /**
     * @param string $operation
     * @return RLOrderInterface
     */
    public function setOperation(string $operation): RLOrderInterface
    {
        if($operation == 'resend_order') {
            $label = 'Reenvio';
        }
        elseif($operation == 'change_order'){
            $label = 'Cambio';
        }
        else{
            $label = 'Retiro/Devolución';
        }
        $this->setOperationLabel($label);
        return $this->setData(self::OPERATION, $operation);
    }

    /**
     * @return string
     */
    public function getOperationLabel(): string
    {
        return $this->getData(self::OPERATION_LABEL);
    }

    /**
     * @param string $operationLabel
     * @return RLOrderInterface
     */
    public function setOperationLabel(string $operationLabel): RLOrderInterface
    {
        return $this->setData(self::OPERATION_LABEL, $operationLabel);
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->getData(self::TRACKING_NUMBER);
    }

    /**
     * @param string $trackingNumber
     * @return RLOrderInterface
     */
    public function setTrackingNumber(string $trackingNumber): RLOrderInterface
    {
        return $this->setData(self::TRACKING_NUMBER, $trackingNumber);
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
     * @return RLOrderInterface
     */
    public function setCreatedAt($createdAt): RLOrderInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return RLItemInterface[]
     */
    public function getItems()
    {
        return $this->getData(self::ORDER_ITEMS);
    }

    /**
     * @param array|DataObject[] $items
     * @return RLOrderInterface
     */
    public function setItems($items)
    {
        $orderItems = [];
        $fields = [
            'sku',
            'qty',
            'name'
        ];

        foreach ($items as $item){
            $orderItem = $this->RLItemModelFactory->create();
            foreach ($fields as $field){
                $orderItem->setData($field, $item[$field]);
            }
            $orderItems[] = $orderItem;
        }

        return $this->setData(self::ORDER_ITEMS, $orderItems);
    }

    /**
     * @param $addressId
     * @return RLOrderInterface
     */
    public function setOriginAddressId($addressId): RLOrderInterface
    {
        return $this->setData(self::ORIGIN_ADDRESS_ID, $addressId);
    }

    /**
     * @return RLAddressInterface
     */
    public function getOriginAddress(): RLAddressInterface
    {
        $originAddress = $this->getData(self::ORIGIN_ADDRESS);
        if(!$originAddress){
            $originAddress = $this->RLAddressModelFactory->create();
        }
        return $originAddress;
    }

    /**
     * @param array|DataObject $sellerAddress
     * @return RLOrderInterface
     */
    public function setOriginAddress($sellerAddress): RLOrderInterface
    {
        $originAddress = $this->getOriginAddress();
        $fields = [
            'customer_firstname',
            'customer_lastname',
            'customer_telephone',
            'customer_vat_id',
            'street',
            'number',
            'floor',
            'apartment',
            'observations',
            'postcode',
            'city',
            'region',
            'country',
            'between_streets'
        ];
        foreach ($fields as $field){
            $originAddress->setData($field, $sellerAddress[$field]);
        }
        return $this->setData(self::ORIGIN_ADDRESS, $originAddress);
    }

    /**
     * @param $addressId
     * @return RLOrderInterface
     */
    public function setDestinationAddressId($addressId): RLOrderInterface
    {
        return $this->setData(self::DESTINATION_ADDRESS_ID, $addressId);
    }

    /**
     * @return RLAddressInterface
     */
    public function getDestinationAddress(): RLAddressInterface
    {
        $destination = $this->getData(self::DESTINATION_ADDRESS);
        if(!$destination){
            $destination = $this->RLAddressModelFactory->create();
        }
        return $destination;
    }

    /**
     * @param array|DataObject $customerAddress
     * @return RLOrderInterface
     */
    public function setDestinationAddress($customerAddress): RLOrderInterface
    {
        $destination = $this->getDestinationAddress();

        $fields = [
            'customer_firstname',
            'customer_lastname',
            'customer_telephone',
            'customer_vat_id',
            'street',
            'number',
            'floor',
            'apartment',
            'observations',
            'postcode',
            'city',
            'region',
            'country',
            'between_streets'
        ];
        foreach ($fields as $field){
            $destination->setData($field, $customerAddress[$field]);
        }
        return $this->setData(self::DESTINATION_ADDRESS, $destination);
    }

    public function getData($key = '', $index = null)
    {
        $orderData = parent::getData($key, $index);

        if($key == '' && $index == null){
            $items = [];
            foreach ($this->getItems() as $item) {
                $items[] = $item->getData();
            }

            $orderData[RLOrderInterface::ORDER_ITEMS] = $items;
            $orderData[RLOrderInterface::ORIGIN_ADDRESS] = $this->getOriginAddress()->getData();
            $orderData[RLOrderInterface::DESTINATION_ADDRESS] = $this->getDestinationAddress()->getData();
        }
        return $orderData;
    }

    /**
     * @return mixed
     */
    public function getLinking()
    {
        return explode(';',$this->getData(self::LINKING));
    }

    /**
     * @param mixed $linking
     * @return RLOrderInterface
     */
    public function setLinking($linking): RLOrderInterface
    {
        return $this->setData(self::LINKING, implode(';',$linking));
    }
}
