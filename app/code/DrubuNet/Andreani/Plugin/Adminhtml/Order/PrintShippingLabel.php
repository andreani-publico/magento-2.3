<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Plugin\Adminhtml\Order;

use Magento\Backend\Model\UrlInterface;
use Magento\Sales\Model\OrderRepository;

class PrintShippingLabel
{
    /**
     * @var UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(
        UrlInterface $backendUrl
    ) {
        $this->_backendUrl = $backendUrl;
    }
    public function beforeSetLayout( \Magento\Sales\Block\Adminhtml\Order\View $subject ){
        $order = $subject->getOrder();
        if(strpos($order->getShippingMethod(),'andreani') !== false && $this->hasTracking($order)) {
            $sendOrder = $this->_backendUrl->getUrl(
                'andreani/order/operations/operation/print_shipping_label',
                ['order_id' => $subject->getOrderId()]
            );
            $subject->addButton(
                'printShippingLabel',
                [
                    'label' => __('Imprimir guias andreani'),
                    'onclick' => "setLocation('" . $sendOrder . "')",
                    'class' => 'ship'
                ]
            );
        }

        return null;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    private function hasTracking($order){
        $hasTracking = false;
        if($order->hasShipments()){
            /**
             * @var \Magento\Sales\Model\Order\Shipment $shipment
             */
            foreach ($order->getShipmentsCollection() as $shipment){
                foreach ($shipment->getTracksCollection()->getItems() as $track){
                    return !empty($track->getTrackNumber());
                }
            }
        }
        return $hasTracking;
    }
}
