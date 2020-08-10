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
        FileFactory $fileFactory
    )
    {
        $this->_resultPageFactory   = $resultPageFactory;
        $this->_resultJsonFactory   = $resultJsonFactory;
        $this->_andreaniHelper      = $andreaniHelper;
        $this->_resultRawFactory    = $resultRawFactory;
        $this->_fileFactory         = $fileFactory;
        $this->_resultRedirect      = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return $this
     */
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
                if($this->_andreaniHelper->getWebserviceMethod() == 'soap') {
                    $andreaniDatosGuia = json_decode(unserialize($shipmentData['andreani_datos_guia']));
                }
                else{
                    $andreaniDatosGuia = json_decode($shipmentData['andreani_datos_guia'],true);
                }
                $guiasArray[$shipmentData['increment_id']]     = $andreaniDatosGuia;
            }

        }


        foreach($guiasArray AS $key => $guiaData)
        {
            if($this->_andreaniHelper->getWebserviceMethod() == 'soap') {
                $object = $guiaData->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                $helper->crearCodigoDeBarras($object->NumeroAndreani);
            }
            else{
                $helper->crearCodigoDeBarras($guiaData['response']['bultos'][0]['numeroDeEnvio']);
            }
        }

        $pdfName    = date_timestamp_get(date_create()).'_'.$order->getIncrementId();

        /**
         * Crea el bloque dinámicamente y le pasa los parámetros por array para
         * que renderice la guía en html.
         */
        $block = $this->_view
            ->getLayout()
            ->createBlock('DrubuNet\Andreani\Block\Generarhtml',
                "guia",
                ['data' => [
                    'order_id' => $orderId
                ]
                ])
            ->setData('area', 'frontend')
            ->setTemplate($helper->getGuiaTemplate());

        $html = $block->toHtml();

        /**
         * Espera recibir "true" después de mandarle al método del helper
         * que se encarga de generar la guía en HTML. El tercer parámetro
         * fuerza la descarga (D) o fuerza el almacenamiento en el filesystem (F)
         */
        $resultPDF = $helper->generateHtml2Pdf($pdfName,$html,'D');
        if(is_bool($resultPDF) && $resultPDF)
        {
            foreach($guiasArray AS $key => $guiaData)
            {
                if($this->_andreaniHelper->getWebserviceMethod() == 'soap') {
                    $object = $guiaData->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                    unlink($helper->getDirectoryPath('media') . "/andreani/" . $object->NumeroAndreani . '.png');
                }
                else{
                    unlink($helper->getDirectoryPath('media') . "/andreani/" . $guiaData['response']['bultos'][0]['numeroDeEnvio'] . '.png');
                }
            }
        }

        $resultRedirect = $this->_resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}


