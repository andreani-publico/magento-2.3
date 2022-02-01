<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Block\Adminhtml\Edit\Tab\View;

use DrubuNet\Andreani\Api\Data\RLOrderInterface;
use Magento\Customer\Controller\RegistryConstants;

class ReverseLogistics extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \DrubuNet\Andreani\Model\ResourceModel\RLOrder\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \DrubuNet\Andreani\Model\ResourceModel\RLOrder\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    )
    {
        $this->_logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize the orders grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('andreani_order_reverselogisticsgrid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setSortable(false);
//        $this->setPagerVisibility(false);

        $this->setFilterVisibility(false);
        $this->setEmptyText(__('No Shipments Found'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $orderId = $this->getParam('order_id');
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter(RLOrderInterface::ORDER_ID, ['eq' => $orderId]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            RLOrderInterface::ID,
            ['header' => __('ID'), 'index' => RLOrderInterface::ID, 'filter_index' => 'e.id', 'type' => 'number', 'width' => '100px']
        );
        $this->addColumn(
            'operation_label',
            [
                'header' => __('Tipo de envio'),
                'index' => 'operation_label',
            ]
        );
//        $this->addColumn(
//            RLOrderInterface::OPERATION,
//            [
//                'header' => __('Tipo de envio'),
//                'index' => RLOrderInterface::OPERATION,
//            ]
//        );
        $this->addColumn(
            RLOrderInterface::TRACKING_NUMBER,
            [
                'header' => __('Numero de tracking'),
                'index' => RLOrderInterface::TRACKING_NUMBER,
            ]
        );
        $this->addColumn(
            RLOrderInterface::CREATED_AT,
            [
                'header' => __('Fecha de creacion'),
                'index' => RLOrderInterface::CREATED_AT,
            ]
        );

        $this->addColumn('action',
            array(
                'header'=>  __('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => __('View'),
                        'url'   => array('base'=> 'andreani/pages/reverselogistics/operation/view'),
                        'field' => 'andreani_order_id'
                    )
                ),
                'filter'=> false,
                'is_system'	=> true,
                'sortable'  => false,
            ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->setMassactionIdFilter('e.id');
        $this->getMassactionBlock()->setUseAjax(false);
        $this->getMassactionBlock()->setFormFieldName('andreani_rl_orders_ids');

        $this->getMassactionBlock()->addItem(
            'mass_print_rl_labels',
            [
                'label' => __('Imprimir Guias'),
                'url' => $this->getUrl(
                    'andreani/order/operations/', ['operation' => 'mass_print_rl_labels','_current' => true,'form_key' => $this->getFormKey()]
                ),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function _prepareMassactionColumn()
    {
        parent::_prepareMassactionColumn();
        /** needs for correct work of mass action select functionality */
        $this->setMassactionIdField('id');

        return $this;
    }


    /**
     * Get headers visibility
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('andreani/pages/reverselogistics/operation/view', ['andreani_order_id' => $row->getId(),'order_id' => $row->getOrderId()]);
    }
}