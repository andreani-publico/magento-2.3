<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

abstract class AbstractOrder extends Action
{
    const ADMIN_RESOURCE = 'DrubuNet_Andreani::shipping_operations';


    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('DrubuNet_Andreani::shipping_operations');

        return $resultPage;
    }
}
