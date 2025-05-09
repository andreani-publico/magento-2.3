<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Model;

use DrubuNet\Andreani\Api\Data\RLAddressInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use DrubuNet\Andreani\Model\ResourceModel\RLAddress as RLAddressResourceModel;

class RLAddress extends AbstractModel implements RLAddressInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(RLAddressResourceModel::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getAddressType()
    {
        return $this->getData(self::ADDRESS_TYPE);
    }

    /**
     * @return string
     */
    public function getCustomerFirstname()
    {
        return $this->getData(self::CUSTOMER_FIRSTNAME);
    }

    /**
     * @return string
     */
    public function getCustomerLastname()
    {
        return $this->getData(self::CUSTOMER_LASTNAME);
    }

    /**
     * @return string
     */
    public function getCustomerVatId()
    {
        return $this->getData(self::CUSTOMER_VAT_ID);
    }

    /**
     * @return string
     */
    public function getCustomerTelephone()
    {
        return $this->getData(self::CUSTOMER_TELEPHONE);
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->getData(self::ADDRESS_STREET);
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->getData(self::ADDRESS_NUMBER);
    }

    /**
     * @return string
     */
    public function getFloor()
    {
        return $this->getData(self::ADDRESS_FLOOR);
    }

    /**
     * @return string
     */
    public function getApartment()
    {
        return $this->getData(self::ADDRESS_APARTMENT);
    }

    /**
     * @return string
     */
    public function getObservations()
    {
        return $this->getData(self::ADDRESS_OBSERVATIONS);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(self::ADDRESS_POSTCODE);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData(self::ADDRESS_CITY);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(self::ADDRESS_REGION);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(self::ADDRESS_COUNTRY);
    }

    /**
     * @return string
     */
    public function getBetweenStreets()
    {
        return $this->getData(self::ADDRESS_BETWEEN_STREETS);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param $addressType
     * @return RLAddressInterface
     */
    public function setAddressType($addressType)
    {
        return $this->setData(self::ADDRESS_TYPE, $addressType);
    }

    /**
     * @param $customerFirstname
     * @return RLAddressInterface
     */
    public function setCustomerFirstname($customerFirstname)
    {
        return $this->setData(self::CUSTOMER_FIRSTNAME, $customerFirstname);
    }

    /**
     * @param $customerLastname
     * @return RLAddressInterface
     */
    public function setCustomerLastname($customerLastname)
    {
        return $this->setData(self::CUSTOMER_LASTNAME, $customerLastname);
    }

    /**
     * @param $customerVatId
     * @return RLAddressInterface
     */
    public function setCustomerVatId($customerVatId)
    {
        return $this->setData(self::CUSTOMER_VAT_ID, $customerVatId);
    }

    /**
     * @param $customerTelephone
     * @return RLAddressInterface
     */
    public function setCustomerTelephone($customerTelephone)
    {
        return $this->setData(self::CUSTOMER_TELEPHONE, $customerTelephone);
    }

    /**
     * @param $street
     * @return RLAddressInterface
     */
    public function setStreet($street)
    {
        return $this->setData(self::ADDRESS_STREET, $street);
    }

    /**
     * @param $number
     * @return RLAddressInterface
     */
    public function setNumber($number)
    {
        return $this->setData(self::ADDRESS_NUMBER, $number);
    }

    /**
     * @param $floor
     * @return RLAddressInterface
     */
    public function setFloor($floor)
    {
        return $this->setData(self::ADDRESS_FLOOR, $floor);
    }

    /**
     * @param $apartment
     * @return RLAddressInterface
     */
    public function setApartment($apartment)
    {
        return $this->setData(self::ADDRESS_APARTMENT, $apartment);
    }

    /**
     * @param $observations
     * @return RLAddressInterface
     */
    public function setObservations($observations)
    {
        return $this->setData(self::ADDRESS_OBSERVATIONS, $observations);
    }

    /**
     * @param $postcode
     * @return RLAddressInterface
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::ADDRESS_POSTCODE, $postcode);
    }

    /**
     * @param $city
     * @return RLAddressInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::ADDRESS_CITY, $city);
    }

    /**
     * @param $region
     * @return RLAddressInterface
     */
    public function setRegion($region)
    {
        return $this->setData(self::ADDRESS_REGION, $region);
    }

    /**
     * @param $country
     * @return RLAddressInterface
     */
    public function setCountry($country)
    {
        return $this->setData(self::ADDRESS_COUNTRY, $country);
    }

    /**
     * @param $betweenStreets
     * @return RLAddressInterface
     */
    public function setBetweenStreets($betweenStreets)
    {
        return $this->setData(self::ADDRESS_BETWEEN_STREETS, $betweenStreets);
    }

    /**
     * @param $createdAt
     * @return RLAddressInterface
     */
    public function setCreatedAt($createdAt): RLAddressInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
