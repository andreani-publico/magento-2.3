<?php

namespace DrubuNet\Andreani\Block\Adminhtml\Edit;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \DrubuNet\Andreani\Helper\Data $andreaniHelper
    ) {
        $this->registry    = $registry;
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Dotdigitalgroup_Email';
        $this->_controller = 'adminhtml_rules';
        $data              = [];
        parent::__construct($context, $data);
        $orderId = $registry->registry('current_order')->getId();
        $isResendConfigured = $andreaniHelper->getRlContractByType('resend_contract') != '';
        $isChangeConfigured = $andreaniHelper->getRlContractByType('change_contract') != '';
        $isWithdrawConfigured = $andreaniHelper->getRlContractByType('withdraw_contract') != '';

        if($isResendConfigured) {
            $resendOrderUrl = $this->getUrl('andreani/pages/reverselogistics/operation/resend_order',['order_id' => $orderId]);
            $this->addButton(
                'resend_order',
                [
                    'label' => __('Generar Reenvio'),
                    'onclick' => 'setLocation(\'' . $resendOrderUrl . '\')',
                    'class' => 'reset'
                ],
                -1
            );
        }
        if($isChangeConfigured) {
            $changeOrderUrl = $this->getUrl('andreani/pages/reverselogistics/operation/change_order',['order_id' => $orderId]);
            $this->addButton(
                'change_order',
                [
                    'label' => __('Generar Cambio'),
                    'onclick' => 'setLocation(\'' . $changeOrderUrl . '\')',
                    'class' => 'reset'
                ],
                -1
            );
        }
        if($isWithdrawConfigured) {
            $withdrawOrderUrl = $this->getUrl('andreani/pages/reverselogistics/operation/withdraw_order',['order_id' => $orderId]);
            $this->addButton(
                'withdraw_order',
                [
                    'label' => __('Generar Retiro/Devolución'),
                    'onclick' => 'setLocation(\'' . $withdrawOrderUrl . '\')',
                    'class' => 'reset'
                ],
                -1
            );
        }

        if(!$isResendConfigured && !$isChangeConfigured && !$isWithdrawConfigured){
            $configureAndreaniRlUrl = $this->getUrl('adminhtml/system_config/edit/section/shipping');
            $this->addButton(
                'configure_andreani_rl',
                [
                    'label' => __('Configurar contratos de logistica inversa'),
                    'onclick' => 'setLocation(\'' . $configureAndreaniRlUrl . '\')',
                    'class' => 'reset'
                ],
                -1
            );
        }

        $this->buttonList->remove('save');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');


    }
}
