<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Shipping\ColissimoPointsRelais\Classes\Shop\Resources;

  class ParcelStatus
  {
    public $id;
    public $code;
    public $date;
    public $status;
    public $site;
    public $city;
    public $zipCode;
    public $countryCode;

    public function __construct($parameters)
    {
      $this->id = $parameters['skybillNumber'];
      $this->code = $parameters['eventCode'];
      $this->date = $parameters['eventDate'];
      $this->status = $parameters['eventLibelle'];
      $this->site = $parameters['eventSite'];
      $this->city = $parameters['recipientCity'];
      $this->zipCode = $parameters['recipientZipCode'];
      $this->countryCode = $parameters['recipientCountryCode'];
    }
  }