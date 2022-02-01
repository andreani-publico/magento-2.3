<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Block\Adminhtml\ReverseLogistics\Create;

use DrubuNet\Andreani\Model\RLOrder;
use Magento\Sales\Api\OrderRepositoryInterface;

class Form extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->orderRepository = $orderRepository;
    }

    /**
     * Retrieve invoice order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
        //return $this->orderRepository->get($this->getOrderIdByParam());
    }

    /**
     * Retrieve source
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    /**
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('items', \Magento\Shipping\Block\Adminhtml\Create\Items::class);
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }

    /**
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        $rlOperation = $this->_request->getParam('operation');
        return $this->getUrl('andreani/order/operations/operation/generate_reverse_logistics/rl_operation/' . $rlOperation,['order_id' => $this->getOrder()->getId()]);
    }

    public function getAndreaniOriginAddress(){
        /**
         * @var RLOrder $andreaniOrder
         */
        $andreaniOrder = $this->_coreRegistry->registry('current_andreani_order');
        $originAddress = $andreaniOrder->getOriginAddress()->getData();
        $html = "%customer_firstname %customer_lastname<br>%street %number, %floor %apartment<br>%city, %postcode, %region<br>Telefono: %customer_telephone";
        $valueInput = '';
        foreach ($originAddress as $field => $value){
            $html = str_replace('%'.$field, $value, $html);
            $valueInput .= $field . '-' . $value . ';';
        }

        return [
            'template' => $html,
            'input' => substr($valueInput,0, strlen($valueInput)-1),
            'data' => $originAddress
        ];
    }

    public function getAndreaniDestinationAddress(){
        /**
         * @var RLOrder $andreaniOrder
         */
        $andreaniOrder = $this->_coreRegistry->registry('current_andreani_order');
        $destinationAddress = $andreaniOrder->getDestinationAddress()->getData();

        $html = $this->getAndreaniAddressTemplate();
        $valueInput = '';

        foreach ($destinationAddress as $field => $value){
            $html = str_replace('%'.$field, $value, $html);
            $valueInput .= $field . '-' . $value . ';';
        }

        return [
            'template' => $html,
            'input' => substr($valueInput,0, strlen($valueInput)-1),
            'data' => $destinationAddress
        ];
    }

    public function getAndreaniAddressTemplate(){
        return "%customer_firstname %customer_lastname (DNI: %customer_vat_id)<br>%street %number, %floor %apartment<br>%city, %postcode, %region<br>Telefono: %customer_telephone<br>Observaciones: %observations";
    }

    public function isAndreaniViewOperation(){
        return $this->_request->getParam('operation') == 'view';
    }

    public function getAndreaniOrderItems(){
        return $this->_coreRegistry->registry('current_andreani_order')->getItems();
    }
}