<?php
namespace DrubuNet\Andreani\Plugin\Checkout;


class LayoutProcessorPlugin
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $customerAddressFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory $agreementCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\AddressFactory $customerAddressFactory
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->checkoutSession = $checkoutSession;
        $this->customerAddressFactory = $customerAddressFactory;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $attributesConfig = [
            'altura' => [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => '.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'altura'
                ],
                'dataScope' => '.custom_attributes.altura',
                'label' => 'Altura',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                    'min_text_length' => 1,
                    'required-entry' => true,
                    'validate-number' => true
                ],
                'sortOrder' => 71,
                'id' => 'altura'
            ],
            'departamento' => [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => '.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'departamento'
                ],
                'dataScope' => '.custom_attributes.departamento',
                'label' => 'Departamento',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                ],
                'sortOrder' => 73,
                'id' => 'departamento'
            ],
            'celular' => [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => '.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'celular'
                ],
                'dataScope' => '.custom_attributes.celular',
                'label' => 'Celular',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                    'min_text_length' => 7,
                    'max_text_length' => 20,
                    'validate-number' => true,
                    'validate-digits' => true
                ],
                'sortOrder' => 200,
                'id' => 'celular'
            ],
            'piso' => [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => '.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'piso'
                ],
                'dataScope' => '.custom_attributes.piso',
                'label' => 'Piso',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                ],
                'sortOrder' => 72,
                'id' => 'piso'
            ],
            'dni' => [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => '.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'dni'
                ],
                'dataScope' => '.custom_attributes.dni',
                'label' => 'DNI',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                    'validate-digits' => true,
                    'max_text_length' => 8,
                    'min_text_length' => 6,
                    'validate-number' => true,
                    'required-entry' => true
                ],
                'sortOrder' => 52,
                'id' => 'dni'
            ],
            'observaciones' => [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => '.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'observaciones'
                ],
                'dataScope' => '.custom_attributes.observaciones',
                'label' => 'Observaciones',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                ],
                'sortOrder' => 250,
                'id' => 'observaciones'
            ]
        ];
        foreach ($attributesConfig as $attributeCode => $attributeValue){
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children']))
            {
                foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as $key => $payment)
                {
                    // Skip extra children like before-place-order, paypal-captcha and braintree-recaptcha
                    if (!preg_match('/-form$/', $key)) {
                        continue;
                    }

                    $paymentCode = 'billingAddress'.str_replace('-form','',$key);
                    $attributeValue['config']['customScope'] = $paymentCode . '.custom_attributes';
                    $attributeValue['dataScope'] = $paymentCode . '.custom_attributes.' . $attributeCode;
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children'][$attributeCode] = $attributeValue;
                }

            }

            if(isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset'])
            ){
                $attributeValue['config']['customScope'] = 'shippingAddress.custom_attributes';
                $attributeValue['dataScope'] = 'shippingAddress.custom_attributes.' . $attributeCode;
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$attributeCode] = $attributeValue;
            }
        }

        return $jsLayout;
    }
}
