<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->createAddressColumn($installer);
        $this->createExtraColumns($installer);
        $installer->endSetup();
    }

    /**
     * @description Creates andreani_datos_guia, codigo_sucursal_andreani, customer_dni columns
     * @param SchemaSetupInterface $installer
     */
    private function createExtraColumns($installer){
        $salesOrderTable = $installer->getTable('sales_order');
        $salesShipmentTable = $installer->getTable('sales_shipment');
        $quoteTable = $installer->getTable('quote');

        $columns = [
            [
                'columnName' => 'andreani_datos_guia',
                'tableName' => $salesShipmentTable,
                'definition' => [
                    'type' => Table::TYPE_BLOB,
                    'length' => '',
                    'nullable' => true,
                    'comment' => 'Data del WS para generar la guía PDF.',
                    'default' => null
                ]
            ],
            [
                'columnName' => 'codigo_sucursal_andreani',
                'tableName' => $quoteTable,
                'definition' => [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Codigo de sucursal andreani',
                    'default' => null
                ]
            ],
            [
                'columnName' => 'codigo_sucursal_andreani',
                'tableName' => $salesOrderTable,
                'definition' => [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Codigo de sucursal andreani',
                    'default' => null
                ]
            ],
            [
                'columnName' => 'customer_dni',
                'tableName' => $salesOrderTable,
                'definition' => [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Numero de documento del cliente',
                    'default' => null
                ]
            ],
        ];

        foreach ($columns as $column){
            if(!$installer->getConnection()->tableColumnExists($column['tableName'],$column['columnName'])) {
                $installer->getConnection()->addColumn($column['tableName'], $column['columnName'], $column['definition']);
            }
        }
    }

    /**
     * @description Creates shipping address columns for save attributes data in sales_order_address, quote_address table
     * @param SchemaSetupInterface $installer
     */
    private function createAddressColumn($installer){
        $attributes = [
            'dni' => [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Numero de documento del cliente',
                'default' => null
            ],
            'altura' => [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Altura de la calle de la dirección del cliente',
                'default' => null
            ],
            'piso' => [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Número del piso la dirección del cliente',
                'default' => null
            ],
            'departamento' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Departamento del piso la dirección del cliente',
                'default' => null
            ],
            'observaciones' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Observaciones del cliente',
                'default' => null
            ],
            'celular' => [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Celular del cliente',
                'default' => null
            ]
        ];
        $quoteAddress = $installer->getTable('quote_address');
        $salesOrderAddress = $installer->getTable('sales_order_address');
        foreach ($attributes as $code => $value){
            if(!$installer->getConnection()->tableColumnExists($salesOrderAddress,$code)){
                $installer->getConnection()->addColumn($salesOrderAddress, $code, $value);
            }
            if(!$installer->getConnection()->tableColumnExists($quoteAddress,$code)){
                $installer->getConnection()->addColumn($quoteAddress, $code, $value);
            }
        }
    }

}
