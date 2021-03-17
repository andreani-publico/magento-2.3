<?php
/**
 * Created by PhpStorm.
 * User: Pablo Garcia
 * Email: pablolgarcia@gmail.com
 * Date: 25/10/18
 * Time: 17:08
 */

namespace DrubuNet\Andreani\Controller\Adminhtml\Order;

use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use DrubuNet\Andreani\Helper\Data as AndreaniHelper;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

class MassImprimirGuias extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @var AndreaniHelper
     */
    protected $_andreaniHelper;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;
    /**
     * @var FileFactory
     */
    private $_fileFactory;

    /**
     * @var \DrubuNet\Andreani\Model\Webservice
     */
    private $webservice;

    /**
     * @var LabelGenerator
     */
    private $_labelGenerator;

    /**
     * MassImprimirGuias constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param AndreaniHelper $andreaniHelper
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        AndreaniHelper $andreaniHelper,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        FileFactory $fileFactory,
        \DrubuNet\Andreani\Model\Webservice $webservice,
        LabelGenerator $labelGenerator
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->_andreaniHelper = $andreaniHelper;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->_fileFactory = $fileFactory;
        $this->webservice = $webservice;
        $this->_labelGenerator = $labelGenerator;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $ordersShipment = $this->shipmentCollectionFactory->create()
            ->setOrderFilter(['in' => $collection->getAllIds()]);

        if ($ordersShipment->getSize())
        {
            foreach ($ordersShipment as $order)
            {
                $guiaContent = $order->getAndreaniDatosGuia();
                if ($guiaContent) {
                    $guiasContent[$order->getIncrementId()] = json_decode($guiaContent,true);
                }
            }

            try
            {
                /**
                 * si el contenido de las guías no está vacío
                 */
                if (!empty($guiasContent))
                {
                    $result = $this->_generarGuias($guiasContent,$collection->getAllIds());
                    if(is_bool($result) && !$result){
                        $this->messageManager->addErrorMessage(__('Hubo un problema imprimiendo las guías. Inténtelo de nuevo.'));
                        return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
                    }
                    if(is_string($result)){
                        $this->messageManager->addErrorMessage(__('Hubo un problema imprimiendo las guías. ' . $result));
                        return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('No hay guías creadas para las órdenes seleccionadas.'));
                    return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
                }
            }
            catch (\Exception $e)
            {
                $this->messageManager->addErrorMessage(__('No hay guías creadas para las órdenes seleccionadas.'));
                return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
            }
        } else {
            $this->messageManager->addErrorMessage(__('No hay guías creadas para las órdenes seleccionadas.'));
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
    }

    /**
     * Genera las guías en pdf para ser descargadas.
     * @param $guiasContent
     * @param $ordersIds
     */
    protected function _generarGuias($guiasContent,$ordersIds)
    {
        try
        {
            $helper             = $this->_andreaniHelper;
            $order_id = '';

            /**
             * Concatena los ID para mandarlos por comas en la URL.
             */
            foreach($ordersIds AS $key => $orderId)
            {
                $order_id.=$orderId.',';
            }

            $order_id = rtrim($order_id, ',');

            $labelContent = [];
            foreach($guiasContent AS $key => $guiaData)
            {
                $nroTrack = $guiaData['response']['bultos'][0]['numeroDeEnvio'];
                $labelContent[] = $this->webservice->getOrderLabel($nroTrack);
            }

            $pdfName        = 'guia_masiva_'.date_timestamp_get(date_create());

            if(!empty($labelContent)) {
                $outputPdf = $this->_labelGenerator->combineLabelsPdf($labelContent);
                return $this->_fileFactory->create(
                    $pdfName,
                    $outputPdf->render(),
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Chequea permisos.
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DrubuNet_Andreani::guias_admin');
    }
}
