<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Setup;

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
        if(version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->upgradeTo201($setup);
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

    private function upgradeTo201(SchemaSetupInterface $setup){
        if(!$setup->getConnection()->tableColumnExists('quote','andreani_rate_without_tax')) {
            $setup->getConnection()->addColumn('quote', 'andreani_rate_without_tax', [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Mappeo para valorDeclaradoSinImpuestos',
                'default' => null
            ]);
        }
        if(!$setup->getConnection()->tableColumnExists('sales_order','andreani_rate_without_tax')) {
            $setup->getConnection()->addColumn('sales_order', 'andreani_rate_without_tax', [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 10,
                'comment' => 'Mappeo para valorDeclaradoSinImpuestos',
                'default' => null
            ]);
        }
    }
}
