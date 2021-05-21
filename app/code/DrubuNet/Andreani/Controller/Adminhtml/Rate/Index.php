<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Controller\Adminhtml\Rate;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'DrubuNet_Andreani::rate_operations';

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('DrubuNet_Andreani::rate_operations')
            ->getConfig()->getTitle()->prepend(__('Gestion de tarifas'));

        return $resultPage;
    }


}
