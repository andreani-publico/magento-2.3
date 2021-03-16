<?php
/**
 * Class Data
 *
 * @description Helper base para el módulo Andreani
 * @author Drubu Team
 * @package DrubuNet\Andreani\Helper
 *
 */

namespace DrubuNet\Andreani\Helper;

ini_set('max_execution_time',3000);
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
//use Spipu\Html2Pdf\Html2Pdf as Html2Pdf;
//use Spipu\Html2Pdf\Exception\Html2PdfException as Html2PdfException;

class Data extends AbstractHelper
{
    /**
     * Constantes para usar con los métodos del WS.
     */
    const ENVMODPROD 						= 'prod';
    const ENVMODTEST 						= 'dev';
    const COTIZACION 						= 'cotizacion';
    const TRAZABILIDAD 						= 'trazabilidad';
    const IMPRESIONCONSTANCIA 				= 'impresionconstancia';
    const OBTESTADODIST 					= 'obtenerestadodistribucion';
    const SUCURSALES 						= 'sucursales';
    const CONFIRCOMPRA 						= 'confirmacioncompra';
    const GENENVIOENTREGARETIROIMPRESION 	= 'generarenviosdeentregayretirocondatosdeimpresion';
    const ANULARENVIO 	                    = 'anularenvio';
    const MEDIDA_GRAMOS                     = 'gramos';
    const MEDIDA_KILOS                      = 'kilos';
    const COTIZACION_TABLA                  = 'tabla';
    const COTIZACION_ONLINE                 = 'webservice';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @var AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var AddressFactory
     */
    protected $_productRepository;

    /**
     * @var AddressFactory
     */
    protected $_directoryList;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var Shipment
     */
    protected $_orderShipment;

    /**
     * @var ProductLoader
     */
    protected $_productloader;

    private $orderRepository;
    private $searchCriteriaBuilder;

    /**
     * Data constructor.
     * @param Session $checkoutSession
     * @param Cart $cart
     * @param ScopeConfigInterface $scopeConfig
     * @param DirectoryList $directoryList
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        Session $checkoutSession,
        Cart $cart,
        ScopeConfigInterface $scopeConfig,
        DirectoryList $directoryList,
        StoreManagerInterface $storeManagerInterface,
        ProductFactory $productFactory,
        Order $order,
        Shipment $shipment,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->_checkoutSession         = $checkoutSession;
        $this->_cart                    = $cart;
        $this->_scopeConfig             = $scopeConfig;
        $this->_directoryList           = $directoryList;
        $this->_storeManagerInterface   = $storeManagerInterface;
        $this->_productloader           = $productFactory;
        $this->_order                   = $order;
        $this->_orderShipment           = $shipment;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @description devuelve el usuario, que se configuró por admin.
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->getConfig('shipping/andreani_configuracion/usuario');
    }

    /**
     * @description devuelve el número de cliente que se configuró por admin.
     * @return mixed
     */
    public function getNroCliente()
    {
        return $this->getConfig('shipping/andreani_configuracion/numero_cliente');
    }

    /**
     * @description devuelve la contraseña asignada al usuario que se configuró por admin.
     * @return mixed
     */
    public function getPass()
    {
        return $this->getConfig('shipping/andreani_configuracion/password');
    }

    /**
     * @description devuelve el método de envío a sucursal, que se configuró por admin.
     * @return mixed
     */
    public function getMetodo()
    {
        return $this->getConfig('shipping/andreani_configuracion/metodo');
    }

    /**
     * @description devuelve la unidad de medida, que se configuró por admin.
     * @return mixed
     */
    public function getModo()
    {
        return $this->getConfig('shipping/andreani_configuracion/modo');
    }

    /**
     * @return mixed
     */
    public function getDebugHabilitado()
    {
        return $this->getConfig('shipping/andreani_configuracion/log_generacion_guias');
    }

    /**
     * @description determina si está activa o no la caché de sucursales optimizada que se configuró por admin.
     * @return mixed
     */
    public function getCache()
    {
        return $this->getConfig('shipping/andreani_configuracion/cache');
    }

    /**
     * @description devuelve el path del logo de la empresa que se cargó por admin.
     * @return string
     */
    public function getlogoEmpresaPath()
    {
//        $storeUrl           = $this->_storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $storeUrl           = $this->getDirectoryPath('media');
        $logoEmpresa        = $this->getConfig('shipping/andreani_configuracion/upload_image');
        if($logoEmpresa!='')
        {
            $logoEmpresaPath    = $storeUrl.'/andreani/logo_empresa/'.$logoEmpresa;
        }
        else
        {
            $logoEmpresaPath = $storeUrl."/andreani/logo_empresa/default/logo_blanco.png";
        }
        return $logoEmpresaPath ;

    }

    /**
     * @param $filename
     * @return string
     */
    public function getGuiaPdfPath($filename)
    {
        $storeUrl           = $this->_storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
//        $storeUrl           = $this->getDirectoryPath('media');
        $guiaPdfPath        = $storeUrl.'andreani/'.$filename.'.pdf';
        return $guiaPdfPath ;
    }

    /**
     * @param $numeroAndreani
     * @return string
     */
    public function getCodigoBarras($numeroAndreani)
    {
        return $this->getDirectoryPath('media') . "/andreani/{$numeroAndreani}.png";
        //return $this->_storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."andreani/{$numeroAndreani}.png";

    }

    /**
     * @description a partir de la interface devuele la dirección de la url de la tienda.
     * @param $path
     * @param $params
     * @return mixed
     * @internal param $type
     */
    public function getStoreUrl($path,$params)
    {
        return $this->_storeManagerInterface->getStore()->getUrl($path,$params);
    }

    /**
     * @return mixed
     */
    public function getStoreManagerInterface()
    {
        return $this->_storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @description devuelve la unidad de medida, que se configuró por admin.
     * @return mixed
     */
    public function getUnidadMedida()
    {
        return $this->getConfig('shipping/andreani_configuracion/unidad_medida');
    }

    /**
     * @description devuelve el peso máximo habilitado, que se configuró por admin.
     * @return mixed
     */
    public function getPesoMaximo()
    {
        return $this->getConfig('shipping/andreani_configuracion/peso_maximo');
    }

    /**
     * @description devuelve el tipo de cotizacion: por webservice o por tarifario con rangos de peso seteados en el admin.
     * @return mixed
     */
    public function getTipoCotizacion()
    {
        return $this->getConfig('shipping/andreani_configuracion/tipo_cotizacion');
    }

    /**
     * @description devuelve el número de contrato del método "Andreani Sucursal"
     * que se configuró por admin.
     * @return mixed
     */
    public function getSucursalContrato()
    {
        return $this->getConfig('carriers/andreanisucursal/contrato');
    }

    /**
     * @description devuelve el número de contrato del método "Andreani Urgente"
     * que se configuró por admin.
     * @return mixed
     */
    public function getUrgenteContrato()
    {
        return $this->getConfig('carriers/andreaniurgente/contrato');
    }

    /**
     * @description devuelve el número de contrato del método "Andreani Estandar"
     * que se configuró por admin.
     * @return mixed
     */
    public function getEstandarContrato()
    {
        return $this->getConfig('carriers/andreaniestandar/contrato');
    }

    public function getContratoPorEnvio($carrier){
        $carrierCode = explode('_', $carrier)[0];
        $contrato = "";
        switch($carrierCode)
        {
            case 'andreaniestandar' :
                $contrato = $this->getEstandarContrato();
                break;
            case 'andreaniurgente' :
                $contrato = $this->getUrgenteContrato();
                break;
            case 'andreanisucursal' :
                $contrato = $this->getSucursalContrato();
                break;
            default:
                $contrato = '';
                break;
        }
        return $contrato;
    }

    /**
     * @description Obtiene la configuración del modo de generación de la guía.
     * @return mixed
     */
    public function getGeneracionGuiasConfig()
    {
        return $this->getConfig('shipping/andreani_configuracion/generacion_guia');
    }

    /**
     * @description Obtiene la configuración del tiempo de almacenamiento de guías.
     * @return mixed
     */
    public function getAlmacenamientoGuiasConfig()
    {
        return $this->getConfig('shipping/andreani_configuracion/almacenamiento_guias');
    }

    public function getCalleOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/calle');
    }

    public function getAlturaOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/numero');
    }

    public function getLocalidadOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/localidad');
    }

    public function getCodigoPostalOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/codigoPostal');
    }

    public function getRegionOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/region');
    }

    public function getPaisOrigenEnvio(){
        $pais = $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/pais');
        return $pais ? $pais : 'Argentina';
    }

    public function getPisoOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/piso');
    }

    public function getDepartamentoOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/departamento');
    }

    public function getEntreCallesOrigenEnvio(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_origen/entreCalle');
    }

    public function getNombreCompletoRemitente(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_remitente/nombreCompleto');
    }

    public function getEmailRemitente(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_remitente/eMail');
    }

    public function getDocumentoTipoRemitente(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_remitente/documentoTipo');
    }

    public function getDocumentoNumeroRemitente(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_remitente/documentoNumero');
    }

    public function getTelefonoTipoRemitente(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_remitente/telefonoTipo');
    }

    public function getTelefonoNumeroRemitente(){
        return $this->getConfig('shipping/andreani_configuracion/andreani_datos_remitente/telefonoNumero');
    }

    /**
     * @description devuelve el objeto del "quote" que está en sesión.
     * @return Quote
     */
    public function getQuote()
    {
        return $this->_cart->getQuote();
    }

    /**
     * @description devuelve el objeto de la dirección del cliente, apartir de
     * la dirección de envío por defecto.
     * @return mixed
     */
    public function getDefaultShippingData()
    {
        $defaultShippingId = $this->getQuote()->getCustomer()->getDefaultShipping();
        $address = $this->_addressFactory->create()->load($defaultShippingId);
        return $address->getData();
    }

    /**
     * @param $mensaje String
     * @param $archivo String
     */
    public static function log($mensaje,$archivo)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$archivo);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($mensaje);
    }

    /**
     * @description devuelve los mails transaccionales configurados en el sitio.
     * @return array
     */
    public function getTransEmails($type=null)
    {
        $transEmails = [];

        $transEmails['contact_general'] = [
            'name'=> $this->getConfig('trans_email/ident_general/name'),
            'email'=> $this->getConfig('trans_email/ident_general/email')

        ];

        $transEmails['representante_ventas'] = [
            'name'=> $this->getConfig('trans_email/ident_sales/name'),
            'email'=> $this->getConfig('trans_email/ident_sales/email')

        ];

        $transEmails['atencion_cliente'] = [
            'name'=> $this->getConfig('trans_email/ident_support/name'),
            'email'=> $this->getConfig('trans_email/ident_support/email')

        ];

        if($type)
        {
            if($type=='andreani')
            {
                $andreaniTransEmail = $this->getConfig('shipping/andreani_configuracion/andreani_trans_emails');
                $transEmails = $transEmails[$andreaniTransEmail];
            }
            else
            {
                $transEmails = $transEmails[$type];
            }
        }
        return $transEmails;
    }

    /**
     * @description Método que espera un parámetro con el método que definirá la url a traer para el
     * WS; además, es posible pasarle el ambiente (testing o prod) para que traiga la url correspondiente.
     * @param $method
     * @param null $enviroment
     * @return mixed
     */
    public function getWSMethodUrl($method,$enviroment=null)
    {
        if($enviroment == self::ENVMODPROD)
        {
            $configField = 'shipping/andreani_configuracion/andreani_ws_prod_urls/';
        }
        else
        {
            $configField = 'shipping/andreani_configuracion/andreani_ws_dev_urls/';
        }

        switch($method)
        {
            case self::COTIZACION:
                $url = $this->getConfig($configField.$method);
                break;
            case self::TRAZABILIDAD:
                $url = $this->getConfig($configField.$method);
                break;
            case self::IMPRESIONCONSTANCIA:
                $url = $this->getConfig($configField.$method);
                break;
            case self::OBTESTADODIST:
                $url = $this->getConfig($configField.$method);
                break;
            case self::SUCURSALES:
                $url = $this->getConfig($configField.$method);
                break;
            case self::CONFIRCOMPRA:
                $url = $this->getConfig($configField.$method);
                break;
            case self::GENENVIOENTREGARETIROIMPRESION:
                $url = $this->getConfig($configField.$method);
                break;
            default:
                $url = '';
                break;
        }

        return $url;
    }

    /**
     * @description recibe el parámetro del directorio que se desea acceder, si no se pasa el parámetro,
     * por defecto devuelve la ruta de la raíz del proyecto.
     * -----------------------------------------------------
     * Paths:
     * -> app
     * -> etc
     * -> lib_internal
     * -> lib_web
     * -> pub
     * -> static
     * -> var
     * -> tmp
     * -> cache
     * -> log
     * -> session
     * -> setup
     * -> di
     * -> generation
     * -> upload
     * -> composer_home
     * -> view_preprocessed
     * -> html
     * -----------------------------------------------------
     * @return mixed
     */
    public function getDirectoryPath($path=null)
    {
        if($path)
        {
            return $this->_directoryList->getPath($path);
        }
        else
        {
            return $this->_directoryList->getRoot();
        }
    }


    /**
     * @description recibe el número de andreani y crea el código de barras a partir del texto.
     * @param $text
     */
    public function crearCodigoDeBarras($text)
    {

    }

//    /**
//     * @description Método que se encarga de generar el pdf con la guía.
//     * @param $pdfName
//     * @param $html
//     * @param $action
//     * @return bool|\Exception|Html2PdfException
//     */
//    public function generateHtml2Pdf($pdfName,$html,$action)
//    {
//        try{
//            $filePath = $this->getDirectoryPath('media')."/andreani/";
//            if (!file_exists($filePath) || !is_dir($filePath)) {
//                mkdir("{$filePath}", 0777,true);
//            }
//
//
//            $pdf = new Html2Pdf(
//                'P',
//                'A4',
//                'en',
//                true,
//                'UTF-8',
//                array(0, 0, 0, 0)
//            );
//
//            $pdf->setDefaultFont('Helvetica');
//            $pdf->writeHTML($html);
//
//            if($action == 'D')
//            {
//                $pdf->output($pdfName.'.pdf', $action);
//            }
//            else
//            {
//                $pdf->output($filePath.$pdfName.'.pdf', $action);
//            }
//            return true;
//
//        }catch (Html2PdfException $e) {
//            $this->log($e,'generateHtml2Pdf_error.log');
//            return $e->getMessage();
//        }
//    }

    /**
     * @description devuelve la versión del soap correspondiente al método que se desea consultar al WS.
     * @param $method
     * @param null $enviroment
     * @return mixed
     */
    public function getSoapVersion($method,$enviroment=null)
    {
        if($enviroment == self::ENVMODPROD)
        {
            $configField = 'shipping/andreani_configuracion/andreani_ws_prod_urls/';
        }
        else
        {
            $configField = 'shipping/andreani_configuracion/andreani_ws_dev_urls/';
        }

        switch($method)
        {
            case self::COTIZACION:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            case self::TRAZABILIDAD:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            case self::IMPRESIONCONSTANCIA:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            case self::OBTESTADODIST:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            case self::SUCURSALES:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            case self::CONFIRCOMPRA:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            case self::GENENVIOENTREGARETIROIMPRESION:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            case self::ANULARENVIO:
                $soapVersion = $this->getConfig($configField.$method.'_soap_version');
                break;
            default:
                $soapVersion = '';
                break;
        }

        return $soapVersion;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    /**
     * @description devuelve orden por ID.
     * @param $orderId
     * @return $this
     */
    public function getLoadShipmentOrder($orderId)
    {
        return $this->_order->load($orderId);
    }

    /**
     * @description Carga por increment_id, el objeto correspondiente
     * a esa orden.
     * @param $incrementId
     * @return $this
     */
    public function loadByIncrementId($incrementId)
    {
        return $this->_orderShipment->loadByIncrementId($incrementId);
    }

    /**
     * @description Devuelve la url para qeu el cliente pueda ver el estado de su envio en andreani
     *
     * @param $trackingNumber
     * @return string
     */
    public function getTrackingUrl($trackingNumber)
    {
        return $this->getConfig('shipping/andreani_configuracion/tracking_url').DIRECTORY_SEPARATOR.$trackingNumber;
    }

    public function isActiveCalculadorPdp()
    {
        return $this->getConfig('shipping/andreani_configuracion/calculadorpdp');
    }


    public function isActiveAndreaniEstandar()
    {
        return $this->getConfig('carriers/andreaniestandar/active');
    }

    public function isActiveAndreaniUrgente()
    {
        return $this->getConfig('carriers/andreaniurgente/active');
    }

    public function isActiveAndreaniSucursal()
    {
        return $this->getConfig('carriers/andreanisucursal/active');
    }

    public function getPrecioFijo()
    {
        return $this->getConfig('shipping/andreani_configuracion/precio_fijo');
    }

    public function getVolumenFijo()
    {
        return $this->getConfig('shipping/andreani_configuracion/volumen_fijo');
    }

    public function getGuiaTemplate()
    {
        if($this->getWebserviceMethod() == 'soap')
        {
            return 'DrubuNet_Andreani::guia.phtml';
        }
        else
        {
            return 'DrubuNet_Andreani::guiarest.phtml';
        }
    }

    public function getGuiaMasivaTemplate()
    {
        if($this->getWebserviceMethod() == 'soap')
        {
            return 'DrubuNet_Andreani::guiamasiva.phtml';
        }
        else
        {
            return 'DrubuNet_Andreani::guiamasivarest.phtml';
        }

    }

    public function getWebserviceMethod(){
        return $this->getConfig('shipping/andreani_configuracion/tipo_ws');
    }

    public function getOrderByIncrementId($incrementId){
        return $this->getOrderInformation('increment_id',$incrementId);
    }

    private function getOrderInformation($field, $value){
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($field, $value, 'eq')->create();
        return $this->orderRepository->getList($searchCriteria)->getFirstItem();
    }
}
