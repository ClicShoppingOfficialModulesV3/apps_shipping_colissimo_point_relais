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

  namespace ClicShopping\Apps\Shipping\ColissimoPointsRelais\Classes\Shop;

  use ClicShopping\Apps\Shipping\ColissimoPointsRelais\Classes\Shop\Client;
  use ClicShopping\Apps\Shipping\ColissimoPointsRelais\Classes\Shop\Resources\PickupPoint;

  /**
   * Implementation of Delivery Choice Web Service
   * https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_livraison.pdf
   */
  class DeliveryChoice extends Client
  {
    const SERVICE_URL = 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl';

    /**
     * Construct Method
     *
     * @param array $credentials Contains login and password for authentication
     * @param array $options Additional parameters to submit to the web services
     */
    public function __construct($credentials, $options = [])
    {
      parent::__construct($credentials, self::SERVICE_URL, $options);
    }

    /**
     * Retrieve available pickup points by selectors
     *
     * @param string $city City name
     * @param string $zipCode Zip Code
     * @param string $countryCode ISO 3166 country code
     * @param string $shippingDate Shipping date (DD/MM/YYYY)
     * @param array $options Additional parameters
     *
     * @return PickupPoint[]
     */
    public function findPickupPoints($city, $zipCode, $countryCode, $shippingDate, $options = [])
    {
      $options = array_merge(
        array(
          'city' => $city,
          'zipCode' => $zipCode,
          'countryCode' => $countryCode,
          'shippingDate' => $shippingDate,
        ),
        $options
      );

      $result = $this->soapExec(
        'findRDVPointRetraitAcheminement',
        $options
      );

      $result = $result->return;

      if ($result->errorCode != 0) {
        throw new \Exception(
          'Failed to request delivery points: ' . $result->errorMessage
        );
      }

      $pickupPoints = array_map(
        function ($pointRetrait) {
          return new PickupPoint($pointRetrait);
        },

        $result->listePointRetraitAcheminement
      );

      return $pickupPoints;
    }

    /**
     * Retreive pickup point by ID
     *
     * @param int $id Pickup point ID
     * @param string $shippingDate Shipping date (DD/MM/YYYY)
     * @param array $options Additional parameters
     *
     * @return PickupPoint
     */

    public function findPickupPointByID($id, $shippingDate, $options = [])
    {
      $options = array_merge(
        array(
          'id' => $id,
          'date' => $shippingDate,
        ),
        $options
      );

      $result = $this->soapExec(
        'findPointRetraitAcheminementByID',
        $options
      );

      $result = $result->return;

      if ($result->errorCode != 0) {
        throw new \Exception(
          'Failed to request delivery points: ' . $result->errorMessage
        );
      }

      $pickupPoint = new PickupPoint($result->pointRetraitAcheminement);

      return $pickupPoint;
    }
  }