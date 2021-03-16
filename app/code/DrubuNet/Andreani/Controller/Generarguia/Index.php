<?php

namespace DrubuNet\Andreani\Controller\Generarguia;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\TestFramework\Inspection\Exception;
use DrubuNet\Andreani\Helper\Data as AndreaniHelper;
use Magento\Framework\Controller\Result\RawFactory as ResultRawFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Shipping\Model\Shipping\LabelGenerator;

/**
 * Class Generarguia
 *
 * @description
 *
 * @author Drubu Team
 * @package DrubuNet\Andreani\Controller\Generarguia
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var AndreaniHelper
     */
    protected $_andreaniHelper;

    /**
     * @var ResultRawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var ResultFactory
     */
    protected $_resultRedirect;
    private $webservice;
    /**
     * @var LabelGenerator
     */
    private $_labelGenerator;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct
    (
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        AndreaniHelper $andreaniHelper,
        ResultRawFactory $resultRawFactory,
        FileFactory $fileFactory,
        \DrubuNet\Andreani\Model\Webservice $webservice,
        LabelGenerator $labelGenerator
    )
    {
        $this->_resultPageFactory   = $resultPageFactory;
        $this->_resultJsonFactory   = $resultJsonFactory;
        $this->_andreaniHelper      = $andreaniHelper;
        $this->_resultRawFactory    = $resultRawFactory;
        $this->_fileFactory         = $fileFactory;
        $this->_resultRedirect      = $context->getResultFactory();
        $this->webservice = $webservice;
        $this->_labelGenerator = $labelGenerator;

        parent::__construct($context);
    }


    public function execute()
    {
        //Recibe por parámetro el id de la orden, y manda a la librería
        //todos los datos para que genere el html que será posteriormente la guía en PDF.
        $request            = $this->getRequest();
        $result             = $this->_resultJsonFactory->create();
        $helper             = $this->_andreaniHelper;
        $orderId            = $request->getParam('order_id');
        $order              = $helper->getLoadShipmentOrder($orderId);
        $guiasArray         = [];

        if($order->hasShipments())
        {
            $orderShipments = $order->getShipmentsCollection();
            foreach($orderShipments->getData() AS $shipmentData)
            {
                $andreaniDatosGuia = json_decode($shipmentData['andreani_datos_guia'],true);
                $guiasArray[$shipmentData['increment_id']]     = $andreaniDatosGuia;
            }
        }

        $labelContent = [];
        foreach($guiasArray AS $key => $guiaData)
        {
            $nroTrack = $guiaData['response']['bultos'][0]['numeroDeEnvio'];
            $labelContent[] = $this->webservice->getOrderLabel($nroTrack);
        }

        $pdfName    = date_timestamp_get(date_create()).'_'.$order->getIncrementId();
        if(!empty($labelContent)) {
            $outputPdf = $this->_labelGenerator->combineLabelsPdf($labelContent);
            return $this->_fileFactory->create(
                $pdfName,
                $outputPdf->render(),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR, // this pdf will be saved in var directory with the name example.pdf
                'application/pdf'
            );
        }
    }
}


