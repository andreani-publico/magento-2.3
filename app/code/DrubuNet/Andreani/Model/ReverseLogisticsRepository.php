<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model;


use DrubuNet\Andreani\Api\Data\RLOrderInterface;
use DrubuNet\Andreani\Helper\Data as AndreaniHelper;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use DrubuNet\Andreani\Model\RLOrderFactory;
use DrubuNet\Andreani\Model\ResourceModel\RLOrder as ResourceRLOrder;
use DrubuNet\Andreani\Model\ResourceModel\RLOrder\CollectionFactory as RLOrderCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

class ReverseLogisticsRepository
{
    /**
     * @var \DrubuNet\Andreani\Model\RLOrderFactory
     */
    private $rlOrderFactory;

    /**
     * @var ResourceRLOrder
     */
    private $rlOrderResource;

    /**
     * @var RLOrderCollectionFactory
     */
    private $rlOrderCollectionFactory;

    /**
     * @var AndreaniHelper
     */
    private $andreaniHelper;

    public function __construct(
        RLOrderFactory  $rlOrderFactory,
        ResourceRLOrder $rlOrderResource,
        RLOrderCollectionFactory $rlOrderCollectionFactory,
        AndreaniHelper $andreaniHelper
    )
    {
        $this->rlOrderFactory = $rlOrderFactory;
        $this->rlOrderResource = $rlOrderResource;
        $this->rlOrderCollectionFactory = $rlOrderCollectionFactory;
        $this->andreaniHelper = $andreaniHelper;
    }

    public function createEmptyOrder(OrderInterface $order, $operation){
        /**
         * @var RLOrderInterface $andreaniOrder
         */
        $andreaniOrder = $this->rlOrderFactory->create();
        $originAddress = [
            'customer_firstname' => $this->andreaniHelper->getSenderFullname(),
            'customer_lastname' => '',
            'customer_telephone' => $this->andreaniHelper->getSenderPhoneNumber(),
            'customer_vat_id' => $this->andreaniHelper->getSenderId(),
            'street' => $this->andreaniHelper->getOrigStreet(),
            'number' => $this->andreaniHelper->getOrigNumber(),
            'floor' => $this->andreaniHelper->getOrigFloor(),
            'apartment' => $this->andreaniHelper->getOrigApartment(),
            'observations' => '',
            'postcode' => $this->andreaniHelper->getOrigPostcode(),
            'city' => $this->andreaniHelper->getOrigCity(),
            'region' => $this->andreaniHelper->getOrigRegion(),
            'country' => $this->andreaniHelper->getOrigCountry(),
            'between_streets' => $this->andreaniHelper->getOrigBetweenStreets()
        ];
        $shippingAddress = $order->getShippingAddress();
        $destinationAddress = [
            'customer_firstname' => $shippingAddress->getFirstname(),
            'customer_lastname' => $shippingAddress->getLastname(),
            'customer_telephone' => $shippingAddress->getTelephone(),
            'customer_vat_id' => $shippingAddress->getDni(),
            'street' => $shippingAddress->getStreetLine(1),
            'number' => $shippingAddress->getAltura(),
            'floor' => $shippingAddress->getPiso(),
            'apartment' => $shippingAddress->getDepartamento(),
            'observations' => $shippingAddress->getObservaciones(),
            'postcode' => $shippingAddress->getPostcode(),
            'city' => $shippingAddress->getCity(),
            'region' => $shippingAddress->getRegion(),
            'country' => 'Argentina',
            'between_streets' => ''
        ];

        $andreaniOrder->setDestinationAddress($destinationAddress);
        $andreaniOrder->setOriginAddress($originAddress);
        $andreaniOrder->setOperation($operation);
        $andreaniOrder->setOrderId($order->getEntityId());

        return $andreaniOrder;
    }

    public function save(RLOrderInterface $rlOrder){
        try {
            if($rlOrder->getId()){
                $rlOrder = $this->getById($rlOrder->getId())->addData($rlOrder->getData());
            }
            $this->rlOrderResource->save($rlOrder);
        }catch (\Exception $e){
            if($rlOrder->getId()){
                throw new CouldNotSaveException(
                    __(
                        'Unable to save ordersPrepared with ID %1. Error: %2',
                        [$rlOrder->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new ordersPrepared. Error: %1', $e->getMessage()));
        }
    }

    /**
     * @param array $data
     */
    public function setAndSaveOrderData($data){
        /**
         * @var RLOrder $orderModel
         */
        $orderModel = $this->rlOrderFactory->create();
        foreach ($data as $key => $value){
            $orderModel->{$this->getFunctionFormat($key)}($value);
        }
        $this->save($orderModel);
    }

    public function getList($orderId): array
    {
        $rlOrders = [];
        $rlOrderCollection = $this->rlOrderCollectionFactory->create();
        $rlOrderCollection->addFieldToFilter(RLOrderInterface::ORDER_ID, $orderId);
        foreach ($rlOrderCollection as $order){
            $this->rlOrderResource->load($order,$order->getId());
            $rlOrders[] = $order;
        }
        return $rlOrders;
    }

    /**
     * @param $id
     * @return RLOrder
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        /** @var \DrubuNet\Andreani\Model\RLOrder $rlOrder */
        $rlOrder = $this->rlOrderFactory->create();
        $this->rlOrderResource->load($rlOrder, $id);
        if (!$rlOrder->getId()) {
            throw new NoSuchEntityException(__('RLOrder with specified ID "%1" not found.', $id));
        }
        return $rlOrder;
    }

    public function getByOrderId($orderId){
        $rlOrder = null;
        $rlOrderCollection = $this->rlOrderCollectionFactory->create();
        $rlOrderCollection->addFieldToFilter(RLOrderInterface::ORDER_ID, $orderId);
        if($rlOrderCollection->count() > 0){
            $rlOrder = $rlOrderCollection->getFirstItem();
        }
        return $rlOrder;
    }

    private function getFunctionFormat($name){
        $pos = strpos($name, '_');
        while (($pos = strpos($name, '_')) !== false) {
            $start = substr($name, 0, $pos);
            $end = substr($name, $pos + 1);
            $name = $start . ucfirst($end);
        }
        return 'set' . ucfirst($name);
    }
}
