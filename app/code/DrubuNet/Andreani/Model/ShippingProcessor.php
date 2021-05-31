<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model;


use DrubuNet\Andreani\Service\AndreaniApiService;
use Magento\Framework\DataObject;
use Magento\Sales\Api\ShipOrderInterfaceFactory;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Shipping\Model\Shipping\LabelGeneratorFactory;

class ShippingProcessor
{
    /**
     * @var \DrubuNet\Andreani\Helper\Data
     */
    private $andreaniHelper;

    /**
     * @var AndreaniApiService
     */
    private $andreaniApiService;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ShipOrderInterfaceFactory
     */
    private $shipOrderFactory;

    /**
     * @var \Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory
     */
    private $shipmentTrackCreationFactory;

    /**
     * @var LabelGeneratorFactory
     */
    private $labelGeneratorFactory;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterfaceFactory
     */
    private $shipmentRepositoryFactory;

    public function __construct(
        \DrubuNet\Andreani\Helper\Data $andreaniHelper,
        AndreaniApiService $andreaniApiService,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        ShipOrderInterfaceFactory $shipOrderFactory,
        \Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory $shipmentTrackCreationFactory,
        \Magento\Sales\Api\ShipmentRepositoryInterfaceFactory $shipmentRepositoryFactory,
        LabelGeneratorFactory $labelGeneratorFactory
    )
    {
        $this->andreaniHelper = $andreaniHelper;
        $this->andreaniApiService = $andreaniApiService;
        $this->cartRepository = $cartRepository;
        $this->shipOrderFactory = $shipOrderFactory;
        $this->shipmentTrackCreationFactory = $shipmentTrackCreationFactory;
        $this->labelGeneratorFactory = $labelGeneratorFactory;
        $this->shipmentRepositoryFactory = $shipmentRepositoryFactory;
    }

    /**
     * @param $items
     * @param $zip
     * @param $method
     * @param string $store_id
     * @return DataObject
     */
    public function getRate($items, $zip, $method, $store_id = ''){
        $rate = new DataObject();
        $price = -1;
        $status = false;
        $packageWeight = $this->getPackageWeightByItems($items); //pesoTotal, valorDeclarado y volumen
        if(true /*$this->andreaniHelper->getTipoCotizacion() == $this->andreaniHelper::COTIZACION_ONLINE*/) {
            $params = array(
                "cpDestino" => $zip,//CP de la sucursal, viene en la info
                "contrato" => $this->andreaniHelper->getContractByType($method),//nro contrato, config,
                "cliente" => $this->andreaniHelper->getClientNumber(),//Codigo cliente, config,
                "sucursalOrigen" => $store_id,//Codigo sucursal
                "bultos" => [
                    [
                        "valorDeclarado" => $packageWeight['amount'],//total de la compra
                        "volumen" => $packageWeight['volume'],//volumen
                        "kilos" => $packageWeight['weight']//peso
                    ]
                ]
            );

            $paramsObj = new DataObject();
            $paramsObj->setData($params);
            $ratesResult = $this->andreaniApiService->getRates($paramsObj);
            if($this->andreaniHelper->isDebugEnable()){
                $statusMsge = isset($ratesResult["tarifaConIva"]["total"]) ? 'successful' : 'with errors';
                $logMessage = "Method: getRate for $method\n";
                $logMessage .= "Status: $statusMsge\n";
                $logMessage .= "Request: " . json_encode($params) . "\n";
                $logMessage .= "Response: " . json_encode($ratesResult) . "\n";
                \DrubuNet\Andreani\Helper\Data::log($logMessage, 'andreani_rest_' . date('Y_m') . '.log');
            }
            if(isset($ratesResult["tarifaConIva"]["total"])){
                $price = $ratesResult["tarifaConIva"]["total"];
                $status = true;
            }
        }
//        elseif($this->andreaniHelper->getTipoCotizacion() == $this->andreaniHelper::COTIZACION_TABLA)
//        {
//
//            /** @var $tarifa \DrubuNet\Andreani\Model\Tarifa */
//            $tarifa = $this->_tarifaFactory->create();
//
//            $costoEnvio = $tarifa->cotizarEnvio(
//                [
//                    'cpSucursal'=> $store['cp'],
//                    'peso'          => $bultosData['kilos'],
//                    'tipo'          => \DrubuNet\Andreani\Model\Carrier\AndreaniSucursal::CARRIER_CODE
//                ]
//            );
//        }

        $rate->setPrice($price);
        $rate->setStatus($status);

        return $rate;
    }

    public function getLabel($tracking){
        $label = null;
        try{
            $label = $this->andreaniApiService->getLabel($tracking);
        }catch (\Exception $e){
            $logMessage = "Method: getLabel\n";
            $logMessage .= "Status: with errors\n";
            $logMessage .= "Request: $tracking\n";
            $logMessage .= "Message: " . $e->getMessage() . "\n";
            \DrubuNet\Andreani\Helper\Data::log($logMessage, 'andreani_rest_' . date('Y_m') . '.log');
        }
        return $label;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return DataObject
     */
    public function generateAndreaniShipping($order){
        $shipmentResult = new \Magento\Framework\DataObject;
        $shippingLabelContent = [];
        $shipmentResult->setStatus(false);
        try {
            $carrierCode = $order->getShippingMethod(true)->getCarrierCode();
            $packageWeight = $this->getPackageWeightByItems($order->getAllItems());
            if($order->hasShipments()){
                $productFixedVolume = $this->andreaniHelper->getProductFixedVolume();
                $productFixedPrice = $this->andreaniHelper->getProductFixedPrice();
                $valorTotal = $pesoTotal = 0;
                $itemsArray = [];
                foreach ($order->getAllItems() AS $orderItem)
                {
                    if (!$orderItem->getQtyShipped() || $orderItem->getIsVirtual()) {
                        continue;
                    }

                    $qtyShipped = $orderItem->getQtyShipped();

                    if($productFixedPrice == '') {
                        $productPrice = $orderItem->getPrice();
                    }
                    else{
                        $productPrice = $productFixedPrice;
                    }

                    $valorTotal += $qtyShipped * $productPrice;
                    $pesoTotal  += $qtyShipped * $orderItem->getWeight();

                    $itemsArray[$orderItem->getId()] = [
                        'qty' => $qtyShipped,
                        'customs_value' => $orderItem->getPrice(),
                        'price' => $orderItem->getPrice(),
                        'name' => $orderItem->getName(),
                        'weight'=> $orderItem->getWeight(),
                        'product_id' => $orderItem->getProductId(),
                        'order_item_id' => $orderItem->getId()
                    ];
                }

                if($this->andreaniHelper->getWeightUnit() == 'gramos'){
                    $pesoTotal = $pesoTotal / 1000;
                }

                $packageWeight = [
                    'items' => $itemsArray,
                    'amount' => $valorTotal,
                    'weight' => $pesoTotal
                ];
                foreach ($order->getShipmentsCollection() as $shipment){
                    $tracksCollection = $shipment->getTracksCollection();
                    foreach ($tracksCollection as $track){
                        $shippingLabelContent[] = $this->getLabel($track->getTrackNumber());
                    }
                    $this->generatePackageWithLabel($shipment->getId(), $shippingLabelContent, $packageWeight);
                    $shipmentResult->setShipmentId($shipment->getId());
                }
                $shipmentResult->setStatus(true);
            }
            else {
                //Creo el pedido en andreani
                $contract = $this->andreaniHelper->getContractByType($carrierCode);
                $params = array(
                    "contrato" => $contract,
                    "origen" => array(
                        "postal" => array(
                            "codigoPostal" => $this->andreaniHelper->getOrigPostcode(),
                            "calle" => $this->andreaniHelper->getOrigStreet(),
                            "numero" => $this->andreaniHelper->getOrigNumber(),
                            "localidad" => $this->andreaniHelper->getOrigCity(),
                            "region" => $this->andreaniHelper->getOrigRegion(),
                            "pais" => $this->andreaniHelper->getOrigCountry(),
                        )
                    ),
                    "destino" => "",
                    "remitente" => array(
                        "nombreCompleto" => $this->andreaniHelper->getSenderFullname(),
                        "email" => $this->andreaniHelper->getSenderEmail(),
                        "documentoTipo" => $this->andreaniHelper->getSenderIdType(),
                        "documentoNumero" => $this->andreaniHelper->getSenderId(),
                        "telefonos" => [
                            array(
                                "tipo" => intval($this->andreaniHelper->getSenderPhoneType()),
                                "numero" => $this->andreaniHelper->getSenderPhoneNumber()
                            )
                        ]
                    ),
                    "destinatario" => [
                        array(
                            "nombreCompleto" => $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname(),
                            "email" => $order->getCustomerEmail(),
                            "documentoTipo" => "DNI",
                            "documentoNumero" => $order->getShippingAddress()->getDni() ? $order->getShippingAddress()->getDni() : '',
                            "telefonos" => [
                                array(
                                    "tipo" => 1,
                                    "numero" => $order->getShippingAddress()->getCelular() ? $order->getShippingAddress()->getCelular() : $order->getShippingAddress()->getTelephone()
                                )
                            ]
                        ),
                    ],
                    //"productoAEntregar" => $packageWeight['names'],
                    "bultos" => [
                        array(
                            "kilos" => $packageWeight['weight'],
                            //"largoCm" => 10,
                            //"altoCm" => 50,
                            //"anchoCm" => 10,
                            "volumenCm" => $packageWeight['volume'],
                            "valorDeclaradoSinImpuestos" => floatval($packageWeight['amount']),
                            //"valorDeclaradoConImpuestos" => 1452,
                            "referencias" => [
                                array(
                                    "meta" => "idCliente",
                                    "contenido" => $order->getIncrementId()
                                ),
                                array(
                                    "meta" => "observaciones",
                                    "contenido" => substr($packageWeight['names'],0,255)
                                ),
                            ]
                        )
                    ]
                );
                if (!empty($order->getCodigoSucursalAndreani())) {//es retiro en sucursal
                    $params['destino'] = array(
                        "sucursal" => array(
                            "id" => $order->getCodigoSucursalAndreani(),
                        )
                    );
                }
                else {
                    $params['destino'] = array(
                        "postal" => array(
                            "codigoPostal" => $order->getShippingAddress()->getPostCode(),
                            "calle" => $order->getShippingAddress()->getStreetLine(1) . ' ' . $order->getShippingAddress()->getStreetLine(2),
                            "numero" => $order->getShippingAddress()->getAltura() ? $order->getShippingAddress()->getAltura() : '',
                            "localidad" => $order->getShippingAddress()->getCity(),
                            "region" => $order->getShippingAddress()->getRegion(),
                            "pais" => "Argentina",
                            "componentesDeDireccion" => []
                        )
                    );
                    if($order->getShippingAddress()->getPiso() != 0){
                        $params['destino']['postal']['componentesDeDireccion'][] = array(
                            "meta" => "piso",
                            "contenido" => $order->getShippingAddress()->getPiso()
                        );
                    }
                    if($order->getShippingAddress()->getDepartamento() != ''){
                        $params['destino']['postal']['componentesDeDireccion'][] = array(
                            "meta" => "departamento",
                            "contenido" => $order->getShippingAddress()->getDepartamento()
                        );
                    }

                    if(!empty($order->getShippingAddress()->getObservaciones())){
                        $params['bultos'][0]['referencias'][1]['contenido'] = substr($order->getShippingAddress()->getObservaciones() . ' ' .$params['bultos'][0]['referencias'][1]['contenido'],0,255);
                    }
                }
                $componentesDeDireccion = array();
                $pisoOrigenEnvio = $this->andreaniHelper->getOrigFloor();
                $departamentoOrigenEnvio = $this->andreaniHelper->getOrigApartment();
                $entreCallesOrigenEnvio = $this->andreaniHelper->getOrigBetweenStreets();

                if (!empty($pisoOrigenEnvio)) {
                    $componentesDeDireccion[] = array(
                        "meta" => "piso",
                        "contenido" => $pisoOrigenEnvio
                    );
                }

                if (!empty($departamentoOrigenEnvio)) {
                    $componentesDeDireccion[] = array(
                        "meta" => "departamento",
                        "contenido" => $departamentoOrigenEnvio
                    );
                }

                if (!empty($entreCallesOrigenEnvio)) {
                    $componentesDeDireccion[] = array(
                        "meta" => "entreCalle",
                        "contenido" => $entreCallesOrigenEnvio
                    );
                }

                if (!empty($componentesDeDireccion)) {
                    $params['origen']['postal']['componentesDeDireccion'] = $componentesDeDireccion;
                }

                $response = $this->andreaniApiService->createOrder(new DataObject($params));

                if (!is_array($response)) {
                    if ($this->andreaniHelper->isDebugEnable()) {
                        $logMessage = "\nOrder #{$order->getIncrementId()}\n";
                        $logMessage .= "Method: createOrder\n";
                        $logMessage .= "Status: with errors\n";
                        $logMessage .= "Request: " . json_encode($params) . "\n";
                        $logMessage .= "Response: " . json_encode($response) . "\n";
                        \DrubuNet\Andreani\Helper\Data::log($logMessage, 'andreani_errores_rest_' . date('Y_m') . '.log');
                    }
                    $shipmentResult->setMessage($response);
                    return $shipmentResult;
                } else {
                    if ($this->andreaniHelper->isDebugEnable()) {
                        $logMessage = "\nOrder #{$order->getIncrementId()}\n";
                        $logMessage .= "Method: createOrder\n";
                        $logMessage .= "Status: successful\n";
                        $logMessage .= "Request: " . json_encode($params) . "\n";
                        $logMessage .= "Response: " . json_encode($response) . "\n";
                        \DrubuNet\Andreani\Helper\Data::log($logMessage, 'andreani_rest_' . date('Y_m') . '.log');
                    }
                }


                //Creo el shipment de magento
                $tracks = [];
                if (count($response['bultos']) > 0) {
                    $carrierTitle = $this->andreaniHelper->getTitleByType($carrierCode);
                    foreach ($response['bultos'] as $andreaniTrack) {
                        $tracking = $andreaniTrack['numeroDeEnvio'];
                        /**
                         * @var \Magento\Sales\Api\Data\ShipmentTrackCreationInterface $shipmentTrackCreation
                         */
                        $shipmentTrackCreation = $this->shipmentTrackCreationFactory->create();
                        $shipmentTrackCreation
                            ->setCarrierCode($carrierCode)
                            ->setTitle($carrierTitle)
                            ->setTrackNumber($tracking);

                        $tracks[] = $shipmentTrackCreation;
                        $shippingLabelContent[] = $this->getLabel($tracking);
                    }
                }

                /**
                 * @var \Magento\Sales\Model\ShipOrder $shipOrder
                 */
                $shipOrder = $this->shipOrderFactory->create();
                $shipmentId = $shipOrder->execute($order->getId(), [], true, false, null, $tracks, [], null);

                $this->generatePackageWithLabel($shipmentId, $shippingLabelContent, $packageWeight);

                $shipmentResult->setShipmentId($shipmentId);
                $shipmentResult->setStatus(true);
            }
        }
        catch (\Exception $e){
            $shipmentResult->setMessage($e->getMessage());
        }
        return $shipmentResult;
    }

    private function generatePackageWithLabel($shipmentId, $shippingLabelContent, $packageWeight){
        //creo el shipping label
        /**
         * @var \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
         */
        $shipmentRepository = $this->shipmentRepositoryFactory->create();
        /**
         * @var \Magento\Sales\Api\Data\ShipmentInterface $shipment
         */
        $shipment = $shipmentRepository->get($shipmentId);
        if (count($shippingLabelContent) > 0) {
            /**
             * @var LabelGenerator $labelGenerator
             */
            $labelGenerator = $this->labelGeneratorFactory->create();
            $outpuPdf = $labelGenerator->combineLabelsPdf($shippingLabelContent);
            $shipment->setShippingLabel($outpuPdf->render());
        }
        $shipment->setPackages([
            1 => [
                'items' => $packageWeight['items'],
                'params' => [
                    'weight' => $packageWeight['weight'],
                    'container' => 1,
                    'customs_value' => $packageWeight['amount']
                ]
            ]]);

        $shipmentRepository->save($shipment);
    }

    /**
     * @param $items
     * @return array
     */
    private function getPackageWeightByItems($items){
        $productFixedVolume = $this->andreaniHelper->getProductFixedVolume();
        $productFixedPrice = $this->andreaniHelper->getProductFixedPrice();
        $pesoTotal       = 0;
        $volumenTotal    = 0;
        $valorProductos  = 0;
        $productsNamesArray = [];
        $itemsArray = [];

        foreach($items as $_item)
        {
            if($_item->getProductType() != 'simple') {
                continue;
            }

            $_producto = $_item->getProduct();
            $productsNamesArray[] = $_producto->getSku() . ' - ' . $_producto->getName();

            if($_item->getParentItem())
                $_item = $_item->getParentItem();

            if($_item instanceof \Magento\Sales\Model\Order\Item) {
                if($productFixedVolume == '') {
                    $volumenTotal += (int)$_producto->getResource()
                            ->getAttributeRawValue($_producto->getId(), 'volumen', $_producto->getStoreId()) * $_item->getQtyOrdered();
                }
                else{
                    $volumenTotal += intval($productFixedVolume) * $_item->getQtyOrdered();
                }

                $pesoTotal += $_item->getQtyOrdered() * $_item->getWeight();

                if($productFixedPrice == '') {
                    if ($_producto->getCost()) {
                        $valorProductos += $_producto->getCost() * $_item->getQtyOrdered();
                    }
                    else {
                        $valorProductos += $_item->getPrice() * $_item->getQtyOrdered();
                    }
                }
                else{
                    $valorProductos += intval($productFixedPrice) * $_item->getQtyOrdered();
                }

                $itemsArray[$_item->getId()] = [
                    'qty' => $_item->getQtyToShip(),
                    'customs_value' => $_item->getPrice(),
                    'price' => $_item->getPrice(),
                    'name' => $_item->getName(),
                    'weight'=> $_item->getWeight(),
                    'product_id' => $_item->getProductId(),
                    'order_item_id' => $_item->getId()
                ];
            }
            else{
                if($productFixedVolume == '') {
                    $volumenTotal += (int)$_producto->getResource()
                            ->getAttributeRawValue($_producto->getId(), 'volumen', $_producto->getStoreId()) * $_item->getQty();
                }
                else{
                    $volumenTotal += intval($productFixedVolume) * $_item->getQty();
                }

                $pesoTotal += $_item->getQty() * $_item->getWeight();

                if($productFixedPrice == '') {
                    if ($_producto->getCost()) {
                        $valorProductos += $_producto->getCost() * $_item->getQty();
                    }
                    else {
                        $valorProductos += $_item->getPrice() * $_item->getQty();
                    }
                }
                else{
                    $valorProductos += intval($productFixedPrice) * $_item->getQty();
                }
            }
        }

        if($this->andreaniHelper->getWeightUnit() == 'gramos'){
            $pesoTotal = $pesoTotal / 1000;
        }

        return array(
            'amount' => $valorProductos,
            'volume' => $volumenTotal,
            'weight' => $pesoTotal,
            'names' => implode(',', $productsNamesArray),
            'items' => $itemsArray
        );
    }

}
