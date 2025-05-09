<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Block\Adminhtml\Order\Tab;


use Magento\Sales\Model\Order\Shipment;

class ReverseLogistics extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
//    protected $_template = 'order/tab/view/reverse_logistics_grid.phtml';
    protected $_coreRegistry;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return 'Andreani - Logistica Inversa';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return 'Logistica Inversa';
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        if ($this->getOrder()->getIsVirtual()) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    public function getTabUrl()
    {
        return $this->getUrl('andreani/tab/reverselogistics', ['_current' => true]);
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

}