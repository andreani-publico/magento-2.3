<?php

namespace DrubuNet\Andreani\Controller\Adminhtml\Tarifa;
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Class Admin
 *
 * @description Action para administrar tarifas de envío Andreani
 *
 * @author Drubu Team
 * @package DrubuNet\Andreani\Controller\Adminhtml\Tarifa
 */
class Admin extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_messageManager;

    protected $appState;

    protected $fileFactory;

    protected $directoryList;

    protected $filesystem;

    private $webservice;

    private $resultRawFactory;

    /**
     * Admin constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \DrubuNet\Andreani\Model\Webservice $webservice,
        \DrubuNet\Andreani\Helper\Data $andreaniHelper
    ) {
        $this->_resultPageFactory   = $resultPageFactory;
        $this->resultRawFactory      = $resultRawFactory;
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->webservice = $webservice;
        $this->_andreaniHelper = $andreaniHelper;
        parent::__construct($context);
    }

    public function executeTemplate()
    {
        $block = $this->_view
            ->getLayout()
            ->createBlock('DrubuNet\Andreani\Block\Generarhtmlmasivo',
                "guiamasiva",
                ['data' => [
                    'order_id' => '76'
                ]
                ])
            ->setData('area', 'frontend')
            //->setTemplate('DrubuNet_Andreani::guiamasiva.phtml');
            ->setTemplate($this->_andreaniHelper->getGuiaMasivaTemplate());

        $html = $block->toHtml();
        echo $html;
        exit;
    }

    public function execute()
    {
        $token = $this->webservice->login();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.andreani.com/v2/360000000015120/etiquetas",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "x-authorization-token:" . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        $outputPdf = \Zend_Pdf::parse($response);

        $file = $this->fileFactory->create(
            'ejemplo_guia.pdf',
            $outputPdf->render(),
            DirectoryList::ROOT,
            'application/pdf'
        );
        return $file;
    }

    public function execute_old()
    {

        $url = 'https://api.qa.andreani.com/v2/ordenes-de-envio/360000000037430/etiquetas';
        $token = $this->webservice->login();
        $headers = array(
            "x-authorization-token: " . $token
        );
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);//contenido del pdf
        curl_close($ch);

        echo "asdasd";exit;

        $file = $this->fileFactory->create(
            'ejemplo_guia.pdf',
            null,
            DirectoryList::ROOT,
            $data,
            'application/pdf'
        );

        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($file); //set content for download file here
        return $resultRaw;

        //return $file;
        //exit;
    }

    public function testDescargarGuia(){
        //URL
        $url = 'https://api.qa.andreani.com/v2/ordenes-de-envio/360000000037430/etiquetas';

//FILE NAME
        $filename = 'etiqueta_test.pdf';

//DOWNLOAD PATH
        $path = $filename;

//FOLDER PATH
        $fp = fopen($path, 'w') or die("Unable to open file!");
        $token = $this->webservice->login();
        $headers = array(
            "x-authorization-token: " . $token
        );

//SETTING UP CURL REQUEST
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);

//CONNECTION CLOSE
        curl_close($ch);
        fclose($fp);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DrubuNet_Andreani::tarifa_admin');
    }

}