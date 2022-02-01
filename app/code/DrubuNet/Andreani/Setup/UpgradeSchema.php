<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Setup;

use DrubuNet\Andreani\Api\Data\RLAddressInterface;
use DrubuNet\Andreani\Api\Data\RLItemInterface;
use DrubuNet\Andreani\Api\Data\RLOrderInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use DrubuNet\Andreani\Api\Data\ZoneInterface;
use DrubuNet\Andreani\Api\Data\RateInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if(version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->upgradeTo200($setup);
        }
        if(version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->upgradeTo202($setup);
        }
        $setup->endSetup();
    }

    private function upgradeTo200(SchemaSetupInterface $setup){
        $tables2delete = [
            'ids_andreani_guia_generada',
            'ids_andreani_provincia',
            'ids_andreani_codigo_postal',
            'ids_andreani_sucursal',
            'drubunet_andreani_sucursal',
            'drubunet_andreani_codigo_postal',
            'drubunet_andreani_provincia',
            'drubunet_andreani_guia_generada'
        ];

        foreach ($tables2delete as $table) {
            if($setup->tableExists($table)) {
                $setup->getConnection()->dropTable($table);
            }
        }

        $tables2rename = [
            [
                'from' => 'ids_andreani_zona',
                'to' => 'drubunet_andreani_zona'
            ],
            [
                'from' => 'ids_andreani_tarifa',
                'to' => 'drubunet_andreani_tarifa'
            ]
        ];

        foreach ($tables2rename as $table) {
            if($setup->tableExists($table['from'])) {
                $setup->getConnection()->renameTable($table['from'],$table['to']);
            }
        }

        /*
         * Creates drubunet_andreani_zona table if not exist
         */
        if(!$setup->tableExists('drubunet_andreani_zona')){
            $drubunetAndreaniZona = $setup->getConnection()
                ->newTable($setup->getTable('drubunet_andreani_zona'))
                ->addColumn(
                    ZoneInterface::ZONE_ID,
                    Table::TYPE_SMALLINT,
                    6,
                    ['identity' => true, 'nullable' => false, 'primary' => true]
                )
                ->addColumn(ZoneInterface::NAME, Table::TYPE_TEXT, 40, ['nullable' => false]);

            $setup->getConnection()->createTable($drubunetAndreaniZona);
        }

        /*
         * Creates drubunet_andreani_tarifa table if not exist
         */
        if(!$setup->tableExists("drubunet_andreani_tarifa")) {
            $drubunetAndreaniTarifa = $setup->getConnection()
                ->newTable($setup->getTable('drubunet_andreani_tarifa'))
                ->addColumn(
                    RateInterface::RATE_ID,
                    Table::TYPE_SMALLINT,
                    6,
                    ['identity' => true, 'nullable' => false, 'primary' => true]
                )
                ->addColumn(RateInterface::RANGE, Table::TYPE_DECIMAL, '10,2', ['nullable' => false])
                ->addColumn(RateInterface::STANDARD_VALUE, Table::TYPE_DECIMAL, '10,2', ['nullable' => false])
                ->addColumn(RateInterface::PICKUP_VALUE, Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null])
                ->addColumn(RateInterface::PRIORITY_VALUE, Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null])
                ->addColumn(ZoneInterface::ZONE_ID, Table::TYPE_SMALLINT, 6, ['nullable' => false])
                ->addIndex(
                    $setup->getIdxName('drubunet_andreani_tarifa', [RateInterface::ZONE_ID]),
                    [RateInterface::ZONE_ID]
                )
                ->addForeignKey(
                    $setup->getFkName('drubunet_andreani_tarifa', RateInterface::ZONE_ID, 'drubunet_andreani_zona', ZoneInterface::ZONE_ID),
                    RateInterface::ZONE_ID,
                    'drubunet_andreani_zona',
                    ZoneInterface::ZONE_ID,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );

            $setup->getConnection()->createTable($drubunetAndreaniTarifa);
        }

        if(!$setup->getConnection()->tableColumnExists('quote','codigo_sucursal_andreani')) {
            $setup->getConnection()->addColumn('quote', 'codigo_sucursal_andreani', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Codigo de sucursal andreani',
                'default' => null
            ]);
        }
    }

    private function upgradeTo202(SchemaSetupInterface $setup)
    {
        if(!$setup->tableExists("drubunet_andreani_logisticainversa")) {
            $drubunetAndreaniLogisticaInversa = $setup->getConnection()
                ->newTable($setup->getTable('drubunet_andreani_logisticainversa'))
                ->addColumn(
                    RLOrderInterface::ID,
                    Table::TYPE_INTEGER,
                    6,
                    ['identity' => true, 'nullable' => false, 'primary' => true]
                )
                ->addColumn(RLOrderInterface::ORDER_ID, Table::TYPE_INTEGER, 6, ['nullable' => false])
                ->addColumn(RLOrderInterface::OPERATION, Table::TYPE_TEXT, 20, ['nullable' => false])
                ->addColumn(RLOrderInterface::OPERATION_LABEL, Table::TYPE_TEXT, 30, ['nullable' => false])
                ->addColumn(RLOrderInterface::TRACKING_NUMBER, Table::TYPE_TEXT, 30, ['nullable' => false])
                ->addColumn(RLOrderInterface::LINKING, Table::TYPE_TEXT, 400, ['nullable' => false])
                ->addColumn(RLOrderInterface::ORIGIN_ADDRESS_ID, Table::TYPE_INTEGER, 10, ['nullable' => false])
                ->addColumn(RLOrderInterface::DESTINATION_ADDRESS_ID, Table::TYPE_INTEGER, 10, ['nullable' => false])
                ->addColumn(RLOrderInterface::CREATED_AT, Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT])
                ->addIndex(
                    $setup->getIdxName('drubunet_andreani_logisticainversa', [RLOrderInterface::ID]),
                    [RLOrderInterface::ID]
                );

            $setup->getConnection()->createTable($drubunetAndreaniLogisticaInversa);
        }
        if(!$setup->tableExists("drubunet_andreani_logisticainversa_direcciones")) {
            $drubunetAndreaniLogisticaInversaDirecciones = $setup->getConnection()
                ->newTable($setup->getTable('drubunet_andreani_logisticainversa_direcciones'))
                ->addColumn(
                    RLAddressInterface::ID,
                    Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'nullable' => false, 'primary' => true])
                ->addColumn(RLAddressInterface::ADDRESS_TYPE, Table::TYPE_SMALLINT, 1, ['nullable' => false])
                ->addColumn(RLAddressInterface::CUSTOMER_FIRSTNAME, Table::TYPE_TEXT, 20, ['nullable' => false])
                ->addColumn(RLAddressInterface::CUSTOMER_LASTNAME, Table::TYPE_TEXT, 20, ['nullable' => false])
                ->addColumn(RLAddressInterface::CUSTOMER_VAT_ID, Table::TYPE_TEXT, 10, ['nullable' => false])
                ->addColumn(RLAddressInterface::CUSTOMER_TELEPHONE, Table::TYPE_TEXT, 10, ['nullable' => false])

                ->addColumn(RLAddressInterface::ADDRESS_STREET, Table::TYPE_TEXT, 100, ['nullable' => false])
                ->addColumn(RLAddressInterface::ADDRESS_NUMBER, Table::TYPE_TEXT, 10, ['nullable' => false])
                ->addColumn(RLAddressInterface::ADDRESS_FLOOR, Table::TYPE_TEXT, 40, ['nullable' => true])
                ->addColumn(RLAddressInterface::ADDRESS_APARTMENT, Table::TYPE_TEXT, 40, ['nullable' => true])
                ->addColumn(RLAddressInterface::ADDRESS_BETWEEN_STREETS, Table::TYPE_TEXT, 100, ['nullable' => true])
                ->addColumn(RLAddressInterface::ADDRESS_OBSERVATIONS, Table::TYPE_TEXT, 100, ['nullable' => true])
                ->addColumn(RLAddressInterface::ADDRESS_POSTCODE, Table::TYPE_TEXT, 20, ['nullable' => false])
                ->addColumn(RLAddressInterface::ADDRESS_CITY, Table::TYPE_TEXT, 100, ['nullable' => false])
                ->addColumn(RLAddressInterface::ADDRESS_REGION, Table::TYPE_TEXT, 100, ['nullable' => false])
                ->addColumn(RLAddressInterface::ADDRESS_COUNTRY, Table::TYPE_TEXT, 100, ['nullable' => false])
                ->addColumn(RLAddressInterface::CREATED_AT, Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT])
                ->addIndex(
                    $setup->getIdxName('drubunet_andreani_logisticainversa_direcciones', [RLAddressInterface::ID]),
                    [RLAddressInterface::ID]
                );

            $setup->getConnection()->createTable($drubunetAndreaniLogisticaInversaDirecciones);
        }
        if(!$setup->tableExists("drubunet_andreani_logisticainversa_productos")) {
            $drubunetAndreaniLogisticaInversaProductos = $setup->getConnection()
                ->newTable($setup->getTable('drubunet_andreani_logisticainversa_productos'))
                ->addColumn(
                    RLItemInterface::ID,
                    Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'nullable' => false, 'primary' => true])
                ->addColumn(RLItemInterface::PARENT_ID, Table::TYPE_INTEGER, 6, ['nullable' => false])
                ->addColumn(RLItemInterface::SKU, Table::TYPE_TEXT, 100, ['nullable' => false])
                ->addColumn(RLItemInterface::NAME, Table::TYPE_TEXT, 100, ['nullable' => false])
                ->addColumn(RLItemInterface::QTY, Table::TYPE_SMALLINT, 3, ['nullable' => false])
                ->addColumn(RLItemInterface::CREATED_AT, Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT])
                ->addIndex(
                    $setup->getIdxName('drubunet_andreani_logisticainversa_productos', [RLItemInterface::ID]),
                    [RLItemInterface::ID]
                );

            $setup->getConnection()->createTable($drubunetAndreaniLogisticaInversaProductos);
        }


    }
}
