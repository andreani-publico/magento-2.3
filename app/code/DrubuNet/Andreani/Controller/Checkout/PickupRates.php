<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */

namespace DrubuNet\Andreani\Controller\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\CsrfAwareActionInterface;

class PickupRates implements ActionInterface,CsrfAwareActionInterface
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private  $request;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var \DrubuNet\Andreani\Model\ShippingProcessor
     */
    private $shippingProcessor;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        CheckoutSession $checkoutSession,
        \DrubuNet\Andreani\Model\ShippingProcessor $shippingProcessor,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    )
    {
        $this->request = $context->getRequest();
        $this->resultFactory = $resultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->shippingProcessor = $shippingProcessor;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $request    = $this->request;
        $price = 0;
        $status = true;
        if(!$this->checkoutSession->getFreeShipping()) {
            $storeId = $request->getParam('store_id') ? $request->getParam('store_id') : null;
            $storeZip = $request->getParam('store_zip') ? $request->getParam('store_zip') : null;
            $storeName = $request->getParam('store_name') ? $request->getParam('store_name') : null;

            $rate = $this->shippingProcessor->getRate($this->checkoutSession->getQuote()->getAllItems(), $storeZip, $storeId);

            $price = $rate->getPrice();
            $status = $rate->getStatus();
            if($status){
                $quote = $this->quoteRepository->getActive($this->checkoutSession->getQuoteId());
                $quote->setCodigoSucursalAndreani($storeId);
                $this->quoteRepository->save($quote);

                $this->checkoutSession->setNombreAndreaniSucursal($storeName);
                $this->checkoutSession->setCotizacionAndreaniSucursal($price);
            }
        }
        $jsonResponse = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $jsonResponse->setData(['price' => $price, 'status' => $status]);

        return $jsonResponse;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function createCsrfValidationException(RequestInterface $request): ? \Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }
}
