<?php

namespace DrubuNet\Andreani\Controller\Adminhtml\Guias;

use DrubuNet\Andreani\Helper\Data as AndreaniHelper;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Shipping\Model\Shipping\LabelGenerator;

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

    private $webservice;
    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry    $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        AndreaniHelper $andreaniHelper,
        LabelGenerator $labelGenerator,
        \DrubuNet\Andreani\Model\Webservice $webservice
    )
    {
        parent::__construct($context);
        $this->_coreRegistry        = $coreRegistry;
        $this->resultPageFactory    = $resultPageFactory;
        $this->fileFactory          = $fileFactory;
        $this->labelGenerator               = $labelGenerator;
        $this->_andreaniHelper      = $andreaniHelper;
        $this->webservice = $webservice;
    }

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
                $guiaContent = json_decode($andreaniDatosGuia, true);
                $object = $guiaContent['response']['bultos'][0]['numeroDeEnvio'];

                $pdfName        = $incrementId.'_'.date_timestamp_get(date_create());

                $result = $this->webservice->getOrderLabel($object);
                if(!empty($result)) {
                    $outputPdf = $this->labelGenerator->combineLabelsPdf([$result]);
                    return $this->fileFactory->create(
                        $pdfName,
                        $outputPdf->render(),
                        \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    );
                }
                else{
                    $this->messageManager->addErrorMessage("No se encontraron pdfs a descargar");
                }

            }catch (\Exception $e)
            {
                $this->messageManager->addErrorMessage(__('Hubo un problema generando la guía. Inténtelo de nuevo.'));
                $resultRedirect = $this->resultRedirectFactory->create()->setPath('sales/shipment/index');
                return $resultRedirect;
            }
        }


    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DrubuNet_Andreani::guias_edit');
    }

}
