<?php

namespace DrubuNet\Andreani\Model\Source;
use Magento\Framework\Option\ArrayInterface;
/**
 * Class WebserviceMethod
 *
 * @description arma el array con el tipo de webservice a consumir.
 * @author Drubu Team
 * @package DrubuNet\Andreani\Model\Source
 */
class TipoDocumento implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            'DNI'    => 'DNI',
            'CUIT'     => 'CUIT',
            'CUIL'     => 'CUIL',
        ];
    }
}
