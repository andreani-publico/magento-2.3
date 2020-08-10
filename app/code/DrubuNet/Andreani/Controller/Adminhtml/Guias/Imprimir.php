<?php

namespace DrubuNet\Andreani\Controller\Adminhtml\Guias;

use DrubuNet\Andreani\Helper\Data as AndreaniHelper;
use Magento\Framework\App\Response\Http\FileFactory;

class Imprimir extends  \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AndreaniHelper
     */
    protected $_andreaniHelper;

    /**
     * @var FileFactory
     */
    protected $fileFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry    $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        AndreaniHelper $andreaniHelper
    )
    {
        parent::__construct($context);
        $this->_coreRegistry        = $coreRegistry;
        $this->resultPageFactory    = $resultPageFactory;
        $this->fileFactory          = $fileFactory;
        $this->_andreaniHelper      = $andreaniHelper;
    }
    /**
     * Add New Row Form page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params             = $this->getRequest()->getParams();
        $incrementId        = $params['increment_id'];
        $order_id           = $params['order_id'];
        
        if($incrementId && $order_id)
        {
            try
            {
                $helper             = $this->_andreaniHelper;

                $shipmentOrder      = $helper->loadByIncrementId($incrementId);
                $andreaniDatosGuia  = $shipmentOrder->getAndreaniDatosGuia();
                if($this->_andreaniHelper->getWebserviceMethod() == 'soap') {
                    $guiaContent = json_decode(unserialize($andreaniDatosGuia));

                    $object = $guiaContent->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                    $helper->crearCodigoDeBarras($object->NumeroAndreani);
                }
                else{
                    $guiaContent = json_decode($andreaniDatosGuia, true);
                    $object = $guiaContent['response']['bultos'][0]['numeroDeEnvio'];
                    $helper->crearCodigoDeBarras($object);
                }

                $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager   = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
//                $urlHtml        = $storeManager->getStore()->getBaseUrl().'andreani/generarguia/shipmentguia/increment_id/'.$incrementId;
                $pdfName        = $incrementId.'_'.date_timestamp_get(date_create());

                /**
                 * Crea el bloque dinámicamente y le pasa los parámetros por array para
                 * que renderice la guía en html.
                 */
                if($this->_andreaniHelper->getWebserviceMethod() == 'soap'){
                    $block = $this->_view
                        ->getLayout()
                        ->createBlock('DrubuNet\Andreani\Block\Shipmentguia',
                            "shipmentguia",
                            ['data' => [
                                'increment_id' => $incrementId
                            ]
                            ])
                        ->setData('area', 'frontend')
                        ->setTemplate('DrubuNet_Andreani::shipmentguia.phtml');
                }
                else{
                    $block = $this->_view
                        ->getLayout()
                        ->createBlock('DrubuNet\Andreani\Block\Shipmentguia',
                            "shipmentguia",
                            ['data' => [
                                'increment_id' => $incrementId
                            ]
                            ])
                        ->setData('area', 'frontend')
                        ->setTemplate('DrubuNet_Andreani::shipmentguiarest.phtml');
                }

                $html = $block->toHtml();

                /**
                 * Espera recibir "true" después de mandarle al método del helper
                 * que se encarga de generar la guía en HTML. El tercer parámetro
                 * fuerza la descarga (D) o fuerza el almacenamiento en el filesystem (F)
                 */
                $result = $helper->generateHtml2Pdf($pdfName,$html,'D');
                if(is_bool($result) && $result)
                {
                    $this->messageManager->addSuccess( __('La guía se generó correctamente.') );
                    ($this->_andreaniHelper->getWebserviceMethod() == 'soap') ? unlink($helper->getDirectoryPath('media')."/andreani/".$object->NumeroAndreani.'.png') : unlink($helper->getDirectoryPath('media')."/andreani/".$object.'.png');
                }
                else{
                    $this->messageManager->addErrorMessage(__('Hubo un problema generando la guía. ' . $result));
                }

            }catch (\Exception $e)
            {
                $this->messageManager->addErrorMessage(__('Hubo un problema generando la guía. Inténtelo de nuevo.'));
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create()->setPath('sales/shipment/index');
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DrubuNet_Andreani::guias_edit');
    }

}