<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Model\Source;


class AttributesOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Eav\Model\Attribute
     */
    protected $attributeCollection;

    public function __construct
    (
        \Magento\Eav\Model\Attribute $attributeCollection
    )
    {
        $this->attributeCollection = $attributeCollection;
    }

    public function toOptionArray()
    {
        $attributes = $this->attributeCollection->getCollection()->addFieldToFilter("entity_type_id", 4);

        $options = [];
        foreach ($attributes as $attribute){
            $options[$attribute->getAttributeCode()] =  $attribute->getAttributeCode();
        }
        return $options;
    }
}