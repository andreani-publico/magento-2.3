<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use DrubuNet\Andreani\Api\Data\ZoneInterface;
use DrubuNet\Andreani\Api\Data\RateInterface;

class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeRepositoryInterface;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param AttributeRepositoryInterface $attributeRepositoryInterface
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        AttributeRepositoryInterface $attributeRepositoryInterface
    )
    {
        $this->eavSetupFactory              = $eavSetupFactory;
        $this->customerSetupFactory         = $customerSetupFactory;
        $this->attributeSetFactory          = $attributeSetFactory;
        $this->attributeRepositoryInterface = $attributeRepositoryInterface;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**Creacion del atributo volumen si no existe*/
        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'volumen')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'volumen',
                [
                    'frontend' => '',
                    'label' => 'Volumen',
                    'input' => 'text',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'apply_to' => '',
                    'visible_on_front' => false,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'used_in_product_listing' => true
                ]
            );
        }

        /**
         * Atributos customer_address
         */
        $customerAddressSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerAddressEntity = $customerAddressSetup->getEavConfig()->getEntityType(
            \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS
        );
        $attributeCustomerAddressSetId      = $customerAddressEntity->getDefaultAttributeSetId();
        $attributeCustomerAddressSet        = $this->attributeSetFactory->create();
        $attributeCustomerAddressGroupId    = $attributeCustomerAddressSet->getDefaultGroupId($attributeCustomerAddressSetId);

        $infoIdsAndreniZona =
            [
                [ ZoneInterface::NAME => 'Local'],
                [ ZoneInterface::NAME => 'Interior 1'],
                [ ZoneInterface::NAME => 'Interior 2']
            ];

        $setup->getConnection()
            ->insertArray($setup->getTable('drubunet_andreani_zona'),['nombre'], $infoIdsAndreniZona);

        $infoIdsAndreniTarifa =
            [
                [1000,130,120,200,1],
                [1400,130,120,200,1],
                [1920,150,150,200,1],
                [2440,150,150,200,1],
                [3004,150,150,200,1],
                [5290,360,240,200,1],
                [1000,130,120,220,2],
                [1400,130,120,220,2],
                [1920,200,150,220,2],
                [2440,200,200,250,2],
                [3004,240,200,250,2],
                [5290,360,360,260,2],
                [1000,130,130,250,3],
                [1400,130,130,250,3],
                [1920,200,150,250,3],
                [2440,200,200,280,3],
                [3004,240,240,280,3],
                [5290,360,360,280,3]
            ];

        $setup->getConnection()
            ->insertArray($setup->getTable('drubunet_andreani_tarifa'),
                [RateInterface::RANGE,RateInterface::STANDARD_VALUE,RateInterface::PICKUP_VALUE,RateInterface::PRIORITY_VALUE,RateInterface::ZONE_ID], $infoIdsAndreniTarifa);

        $setup->endSetup();
    }
}
