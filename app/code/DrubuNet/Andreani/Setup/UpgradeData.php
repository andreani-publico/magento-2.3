<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Setup;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.2', '<'))
        {
            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY,'volumen','apply_to','simple');
        }

        if (version_compare($context->getVersion(), '2.0.0', '<'))
        {
            $this->updateAddressAttributes($setup,$eavSetup);
        }

        $setup->endSetup();
    }

    /**
     * @var ModuleDataSetupInterface $setup
     * @var EavSetup $eavSetup
     */
    private function updateAddressAttributes($setup, $eavSetup){
        /**
         * @var CustomerSetup $customerSetup
         */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $addressAttributes = [
            'dni' => [
                'label' => 'DNI',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 118,
                'visible' => true,
                'system' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ],
            'celular' => [
                'label' => 'Celular',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 200,
                'visible' => true,
                'system' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ],
            'altura' => [
                'label' => 'Altura',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 70,
                'visible' => true,
                'system' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ],
            'piso' => [
                'label' => 'Piso',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 71,
                'visible' => true,
                'system' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ],
            'departamento' => [
                'label' => 'Departamento',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 72,
                'visible' => true,
                'system' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ],
            'observaciones' => [
                'label' => 'Observaciones',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 1000,
                'visible' => true,
                'system' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ]
        ];
        foreach ($addressAttributes as $attributeCode => $attributeParams){
            if($eavSetup->getAttributeId(\Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode)){
               $eavSetup->updateAttribute(
                   \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                   $attributeCode,
                   'required',
                   false
               );
            }
            else{
                $customerSetup->addAttribute(
                    \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                    $attributeCode,
                    $attributeParams
                );
            }
            $attribute = $customerSetup->getEavConfig()
                ->getAttribute(
                    \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                    $attributeCode
                )
                ->addData([
                    'used_in_forms' => [
                        'customer_address_edit',
                        'customer_register_address',
                        'adminhtml_customer_address'
                    ]
                ]);
            $attribute->save();
        }
    }
}
