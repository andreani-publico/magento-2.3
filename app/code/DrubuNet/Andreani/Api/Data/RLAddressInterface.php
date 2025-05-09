<?php
/**
 * @author Drubu Team
 * @copyright Copyright (c) 2021 Drubu
 * @package DrubuNet_Andreani
 */
namespace DrubuNet\Andreani\Api\Data;

use Magento\Framework\DataObject;

interface RLAddressInterface
{
    const ID = 'id';
    const ADDRESS_TYPE = 'address_type';
    const CUSTOMER_FIRSTNAME = 'customer_firstname';
    const CUSTOMER_LASTNAME = 'customer_lastname';
    const CUSTOMER_VAT_ID = 'customer_vat_id';
    const CUSTOMER_TELEPHONE = 'customer_telephone';
    const ADDRESS_STREET = 'street';
    const ADDRESS_NUMBER = 'number';
    const ADDRESS_FLOOR = 'floor';
    const ADDRESS_APARTMENT = 'apartment';
    const ADDRESS_OBSERVATIONS = 'observations';
    const ADDRESS_POSTCODE = 'postcode';
    const ADDRESS_CITY = 'city';
    const ADDRESS_REGION = 'region';
    const ADDRESS_COUNTRY = 'country';
    const ADDRESS_BETWEEN_STREETS = 'between_streets';
    const CREATED_AT = 'created_at';


    public function getId();

    /**
     * @return string
     */
    public function getAddressType();

    /**
     * @return string
     */
    public function getCustomerFirstname();

    /**
     * @return string
     */
    public function getCustomerLastname();

    /**
     * @return string
     */
    public function getCustomerVatId();

    /**
     * @return string
     */
    public function getCustomerTelephone();

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @return string
     */
    public function getNumber();

    /**
     * @return string
     */
    public function getFloor();

    /**
     * @return string
     */
    public function getApartment();

    /**
     * @return string
     */
    public function getObservations();

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getRegion();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return string
     */
    public function getBetweenStreets();

    /**
     * @return string
     */
    public function getCreatedAt();

    public function setId($id);

    /**
     * @param $addressType
     * @return RLAddressInterface
     */
    public function setAddressType($addressType);

    /**
     * @param $customerFirstname
     * @return RLAddressInterface
     */
    public function setCustomerFirstname($customerFirstname);

    /**
     * @param $customerLastname
     * @return RLAddressInterface
     */
    public function setCustomerLastname($customerLastname);

    /**
     * @param $customerVatId
     * @return RLAddressInterface
     */
    public function setCustomerVatId($customerVatId);

    /**
     * @param $customerTelephone
     * @return RLAddressInterface
     */
    public function setCustomerTelephone($customerTelephone);

    /**
     * @param $street
     * @return RLAddressInterface
     */
    public function setStreet($street);

    /**
     * @param $number
     * @return RLAddressInterface
     */
    public function setNumber($number);

    /**
     * @param $floor
     * @return RLAddressInterface
     */
    public function setFloor($floor);

    /**
     * @param $apartment
     * @return RLAddressInterface
     */
    public function setApartment($apartment);

    /**
     * @param $observations
     * @return RLAddressInterface
     */
    public function setObservations($observations);

    /**
     * @param $postcode
     * @return RLAddressInterface
     */
    public function setPostcode($postcode);

    /**
     * @param $city
     * @return RLAddressInterface
     */
    public function setCity($city);

    /**
     * @param $region
     * @return RLAddressInterface
     */
    public function setRegion($region);

    /**
     * @param $country
     * @return RLAddressInterface
     */
    public function setCountry($country);

    /**
     * @param $betweenStreets
     * @return RLAddressInterface
     */
    public function setBetweenStreets($betweenStreets);

    /**
     * @param $createdAt
     * @return RLAddressInterface
     */
    public function setCreatedAt($createdAt): RLAddressInterface;

}
