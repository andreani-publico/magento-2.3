<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Controller\Adminhtml\Order;

use DrubuNet\Andreani\Model\RLOrder;
use DrubuNet\Andreani\Model\ShippingProcessor;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\OrderRepositoryInterface as SalesOrderRepositoryInterface;
use Magento\Sales\Model\Order;
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
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var \DrubuNet\Andreani\Model\ReverseLogisticsRepository
     */
    private $reverseLogisticsRepository;
    /**
     * @var \DrubuNet\Andreani\Model\ResourceModel\RLOrder\CollectionFactory
     */
    private $rlOrderCollectionFactory;

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
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Registry $registry,
        \DrubuNet\Andreani\Model\ResourceModel\RLOrder\CollectionFactory $rlOrderCollectionFactory
    )
    {
        parent::__construct($context);
        $this->filterFactory = $filterFactory;
        $this->collectionFactory = $collectionFactory;
        $this->shippingProcessor = $shippingProcessor;
        $this->_labelGeneratorFactory = $labelGeneratorFactory;
        $this->_fileFactory = $fileFactory;
        $this->orderRepository = $orderRepository;
        $this->registry = $registry;
        $this->rlOrderCollectionFactory = $rlOrderCollectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $operation = $this->getRequest()->getParam('operation');
        $functionName = lcfirst(str_replace('_', '', ucwords($operation,'_')));
        if($response = $this->{$functionName}()){
            if(is_bool($response)) {
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
            }
            else{
                return $response;
            }
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

    //Generar retiro
    private function generateReverseLogistics(){
        $orderId = $this->getRequest()->getParam('order_id');
        $operation = $this->getRequest()->getParam('rl_operation');
        $labelSuccess = '';
        $labelError = '';

        switch ($operation){
            case 'withdraw_order':
                $labelSuccess = 'Retiro generado con exito!';
                $labelError = 'Error al generar el Retiro';
                break;
            case 'resend_order':
                $labelSuccess = 'Reenvio generado con exito!';
                $labelError = 'Error al generar el Reenvio';
                break;
            case 'change_order':
                $labelSuccess = 'Cambio generado con exito!';
                $labelError = 'Error al generar el Cambio';
                break;
            default:
                $this->messageManager->addErrorMessage(__('Operacion incorrecta para la orden %1', $operation));
                break;
        }

        if($labelSuccess != ''){
            $originAddressData = $this->_request->getParam('origin-address-input');
            $destinationAddressData = $this->_request->getParam('destination-address-input');

            $originAddress = [];
            $destinationAddress = [];
            $items = [];

            foreach (explode(';',$originAddressData) as $fieldValueData){
                $fieldValue = explode('-', $fieldValueData);
                $originAddress[$fieldValue[0]] = $fieldValue[1];
            }

            foreach (explode(';',$destinationAddressData) as $fieldValueData){
                $fieldValue = explode('-', $fieldValueData);
                $destinationAddress[$fieldValue[0]] = $fieldValue[1];
            }

            foreach($this->_request->getParams() as $key => $value){
                if(strpos($key,'item_qty_for_') !== false && $value > 0){
                    $sku = substr($key,strlen('item_qty_for_'));
                    $items[$sku] = $value;
                }
            }

            $orderData = [
                'order_id' => $orderId,
                'operation' => $operation,
                'tracking_number' => '',
                'origin_address' => $originAddress,
                'destination_address' => $destinationAddress,
                'items' => $items
            ];

            $shipmentResult = $this->shippingProcessor->generateAndreaniRLShipping($orderData);
            if($shipmentResult->getStatus()){
                $this->messageManager->addSuccessMessage(__($labelSuccess));
            }
            else{
                $this->messageManager->addErrorMessage(__($labelError));
            }

            return $this->resultRedirectFactory->create()->setUrl($this->getUrl(
                'sales/order/view',
                ['order_id' => $orderId]
            ));
        }
        return true;
    }

    private function massPrintRlLabels(){
        $ids = $this->getRequest()->getParam('andreani_rl_orders_ids');
        $collection = $this->rlOrderCollectionFactory->create()->addFieldToFilter(RLOrder::ID, ['in' => $ids]);
        $labelContent = [];
        /**
         * @var RLOrder $order
         */
        foreach ($collection as $order){
            $linking = $order->getLinking();
            foreach ($linking as $label){
                if(!empty($label)) {
                    $labelContent[] = $this->shippingProcessor->getLabel($label, true);
                }
            }
        }

        $pdfName        = 'guia_masiva_'.date_timestamp_get(date_create()) . '.pdf';
        if(count($labelContent) > 0) {
            $outputPdf = $this->_labelGeneratorFactory->create()->combineLabelsPdf($labelContent);
            return $this->_fileFactory->create(
                $pdfName,
                $outputPdf->render(),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
        else{
            $this->messageManager->addWarningMessage('No se encontraron etiquetas de logistica inversa para los envios seleccionados.');
        }
        return true;
    }


    protected function _isAllowed()
    {
        return true;
    }

}
