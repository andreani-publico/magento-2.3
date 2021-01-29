<?php
namespace DrubuNet\Andreani\Observer;

use DrubuNet\Andreani\Model\Carrier\AndreaniSucursal;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesEventQuoteSubmitBeforeObserver
 * @package DrubuNet\Andreani\Observer
 */
class SalesOrderPlaceBefore implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    private $addressRepository;

    /**
     * SalesOrderPlaceBefore constructor.
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customer,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    )
    {
        $this->_customerSession = $customer;
        $this->_checkoutSession = $checkoutSession;
        $this->addressRepository = $addressRepository;

    }

    /**
     * Graba en la orden el numero de sucursal andreani que tenga el quote, y el dni en el quote address
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $codigoSucursalAndreani = $this->_checkoutSession->getCodigoSucursalAndreani();
        $order                  = $observer->getEvent()->getOrder();
        $shippingAddress        = $order->getShippingAddress();

        $metodoEnvio = explode('_', $order->getShippingMethod());

        if($metodoEnvio[0] ==  AndreaniSucursal::CARRIER_CODE)
        {
            $order->setCodigoSucursalAndreani($codigoSucursalAndreani);
        }

        $order->setCustomerDni($shippingAddress->getDni());

        

        if($metodoEnvio[0] ==  AndreaniSucursal::CARRIER_CODE)
        {
            $this->_checkoutSession->unsCodigoSucursalAndreani();
            $this->_checkoutSession->unsNombreAndreaniSucursal();
            $this->_checkoutSession->unsCotizacionAndreaniSucursal();
        }


        return $this;
    }
}
