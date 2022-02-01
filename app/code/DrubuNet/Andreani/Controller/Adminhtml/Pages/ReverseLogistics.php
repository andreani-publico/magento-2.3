<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Controller\Adminhtml\Pages;

use DrubuNet\Andreani\Model\ReverseLogisticsRepository;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class ReverseLogistics extends Action
{
    const ADMIN_RESOURCE = 'DrubuNet_Andreani::shipping_operations';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ReverseLogisticsRepository
     */
    private $reverseLogisticsRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        OrderRepositoryInterface $orderRepository,
        ReverseLogisticsRepository $reverseLogisticsRepository
    )
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->orderRepository = $orderRepository;
        $this->reverseLogisticsRepository = $reverseLogisticsRepository;
    }

    public function execute()
    {
        if(!$this->_request->getParam('andreani_order_id')) {
            $currentOrder = $this->orderRepository->get($this->_request->getParam('order_id'));
            $andreaniOrder = $this->reverseLogisticsRepository->createEmptyOrder($currentOrder, $this->_request->getParam('operation'));
        }
        else{
            $andreaniOrder = $this->reverseLogisticsRepository->getById($this->_request->getParam('andreani_order_id'));
            $currentOrder = $this->orderRepository->get($andreaniOrder->getOrderId());
        }
        $this->registry->register('current_order', $currentOrder);
        $this->registry->register('current_andreani_order', $andreaniOrder);

        $this->_view->loadLayout();
        //$this->_setActiveMenu('Magento_Sales::sales_order');
        if(!$this->_request->getParam('andreani_order_id')) {
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Generando un %1 del pedido %2', $andreaniOrder->getOperationLabel(), $currentOrder->getIncrementId()));
        }
        else{
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('%1 del pedido %2',$andreaniOrder->getOperationLabel(),$currentOrder->getIncrementId()));
        }
        $this->_view->renderLayout();
    }
}
