<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Block\Adminhtml\ReverseLogistics;

use Magento\Sales\Model\Order;

class Create extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        //$this->_objectId = 'order_id';
        $this->_mode = 'create';
        $this->_objectId = 'simple_form';
        $this->_blockGroup = 'DrubuNet_Andreani';
        $this->_controller = 'adminhtml_reverselogistics';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('delete');
        $operation = $this->_request->getParam('operation');
        if($operation != 'view') {
            if($operation == 'resend_order') {
                $label = 'Generar Reenvio';
            }
            elseif($operation == 'change_order'){
                $label = 'Generar Cambio';
            }
            else{
                $label = 'Generar Retiro/Devolución';
            }
            $action = $this->getUrl("andreani/order/operations/operation/$operation",['order_id' => $this->getOrder()->getId()]);
            $this->addButton(
                $operation,
                [
                    'label' => __($label),
                    //'onclick' => 'jQuery(\'#edit_form\').submit()', //'setLocation(\'' . $action . '\')',
                    'onclick' => 'submitShipment(this);',
                    'class' => 'primary'
                ],
                -1
            );
        }
    }

    /**
     * @return Order
     */
    public function getOrder(){
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $header = __('New Shipment for Order #%1',$this->getOrder()->getId());
        return $header;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'sales/order/view',
            ['order_id' => $this->getOrder()->getId()]
        );
    }
}