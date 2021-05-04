<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Controller\Adminhtml\Order;

use DrubuNet\Andreani\Model\ShippingProcessor;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\OrderRepositoryInterface as SalesOrderRepositoryInterface;
use Magento\Shipping\Model\Shipping\LabelGeneratorFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

class Operations extends Action
{
    const ADMIN_RESOURCE = 'DrubuNet_Andreani::shipping_operations';
    const GENERATE_SHIPPING = 'mass_generate_shipping';
    const PRINT_SHIPPING_LABEL = 'print_shipping_label';

    /**
     * @var \Magento\Ui\Component\MassAction\FilterFactory
     */
    private $filterFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ShippingProcessor
     */
    private $shippingProcessor;

    /**
     * @var LabelGeneratorFactory
     */
    private $_labelGeneratorFactory;

    /**
     * @var FileFactory
     */
    private $_fileFactory;

    /**
     * @var SalesOrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Context $context
     * @param \Magento\Ui\Component\MassAction\FilterFactory $filterFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param ShippingProcessor $shippingProcessor
     * @param LabelGeneratorFactory $labelGeneratorFactory
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        \Magento\Ui\Component\MassAction\FilterFactory $filterFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        ShippingProcessor $shippingProcessor,
        LabelGeneratorFactory $labelGeneratorFactory,
        FileFactory $fileFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        parent::__construct($context);
        $this->filterFactory = $filterFactory;
        $this->collectionFactory = $collectionFactory;
        $this->shippingProcessor = $shippingProcessor;
        $this->_labelGeneratorFactory = $labelGeneratorFactory;
        $this->_fileFactory = $fileFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $operation = $this->getRequest()->getParam('operation');
        $functionName = lcfirst(str_replace('_', '', ucwords($operation,'_')));
        if($this->{$functionName}()){
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }
    }

    private function massGenerateShipping(){
        /**
         * @var \Magento\Ui\Component\MassAction\Filter $filter
         */
        $filter = $this->filterFactory->create();
        $collection = $filter->getCollection($this->collectionFactory->create());
        $successCount = 0;
        $failureCount = 0;
        $total = $collection->count();
        $failureDetail = [];
        foreach ($collection as $order){
            $shipmentResult = $this->shippingProcessor->generateAndreaniShipping($order);
            if($shipmentResult->getStatus()){
                $successCount++;
            }
            else{
                $failureCount++;
                $failureDetail[] = "Order #" . $order->getIncrementId() . ' - ' . $shipmentResult->getMessage();
            }
        }

        if($successCount && !$failureCount){
            $this->messageManager->addSuccessMessage(__('Todos los pedidos se generaron con exito!'));
        }
        else{
            if($successCount){
                $this->messageManager->addSuccessMessage(__($successCount . '/' . $total . ' pedidos se generaron con exito!'));
            }
            $this->messageManager->addErrorMessage($failureCount . ' pedidos no se generaron con exito. ' . json_encode($failureDetail));
        }
        return true;
    }

    private function massPrintShippingLabel(){
        /**
         * @var \Magento\Ui\Component\MassAction\Filter $filter
         */
        $filter = $this->filterFactory->create();
        $collection = $filter->getCollection($this->collectionFactory->create());
        /**
         * @var \Magento\Sales\Model\Order $order
         */
        $labelContent = [];
        foreach ($collection as $order){
            if($order->hasShipments()){

                /**
                 * @var \Magento\Sales\Model\Order\Shipment $shipment
                 */
                foreach ($order->getShipmentsCollection() as $shipment){
                    foreach ($shipment->getTracksCollection()->getItems() as $track){
                        $labelContent[] = $this->shippingProcessor->getLabel($track->getTrackNumber());
                    }
                }
            }
        }

        $pdfName        = 'guia_masiva_'.date_timestamp_get(date_create()) . '.pdf';

        if(!empty($labelContent)) {
            $outputPdf = $this->_labelGeneratorFactory->create()->combineLabelsPdf($labelContent);
            return $this->_fileFactory->create(
                $pdfName,
                $outputPdf->render(),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
        else{
            $this->messageManager->addWarningMessage('Los pedidos seleccionados no tienen una etiqueta Andreani disponible.');
        }
        return true;
    }

    private function printShippingLabel(){
        $orderId = $this->getRequest()->getParam('order_id');
        if(!empty($orderId)){
            $labelContent = [];
            $order = $this->orderRepository->get($orderId);
            /**
             * @var \Magento\Sales\Model\Order\Shipment $shipment
             */
            foreach ($order->getShipmentsCollection() as $shipment){
                foreach ($shipment->getTracksCollection()->getItems() as $track){
                    $labelContent[] = $this->shippingProcessor->getLabel($track->getTrackNumber());
                }
            }

            $pdfName        = $order->getIncrementId() . '_' . date_timestamp_get(date_create()) . '.pdf';
            if(!empty($labelContent)) {
                $outputPdf = $this->_labelGeneratorFactory->create()->combineLabelsPdf($labelContent);
                return $this->_fileFactory->create(
                    $pdfName,
                    $outputPdf->render(),
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
            else{
                $this->messageManager->addWarningMessage('El pedido no tiene una etiqueta Andreani disponible.');
            }
        }
        return true;
    }
}
