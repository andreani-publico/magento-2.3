<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \DrubuNet\Andreani\Service\AndreaniApiService
     */
    private $apiService;

    public function __construct(
        \DrubuNet\Andreani\Service\AndreaniApiService $apiService
    )
    {
        $this->apiService = $apiService;
    }

    public function getConfig(){
        $stores = $this->apiService->getLocations();
        $result = [];
        if (is_array($stores)){
            foreach ($stores as $store){
                $storeName = $store["direccion"]['calle'] . ' ' . $store["direccion"]['numero'] . ', CP: ' . $store["direccion"]['codigoPostal'];
                if($store['direccion']['localidad'] == 'C.A.B.A.'){
                    $result[$store['direccion']['localidad']][ucwords(strtolower($store['descripcion']))][$storeName] = $store;
                }
                else {
                    $result[$store['direccion']['provincia']][$store['direccion']['localidad']][$storeName] = $store;
                }
            }
        }
        return [
            'andreani' => [
                'stores' => $result
            ]
        ];
    }
}