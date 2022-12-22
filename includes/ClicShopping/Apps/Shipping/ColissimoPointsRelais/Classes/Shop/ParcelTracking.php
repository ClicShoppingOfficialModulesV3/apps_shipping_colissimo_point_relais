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
  use ClicShopping\Apps\Shipping\ColissimoPointsRelais\Classes\Shop\Resources\ParcelStatus;

  /**
   * Implementation of Parcel Tracking Web Service
   * https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_suivi.pdf
   */
  class ParcelTracking extends Client
  {
    const SERVICE_URL = 'https://www.coliposte.fr/tracking-chargeur-cxf/TrackingServiceWS?wsdl';

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
     * Retrieve Parcel status by it's ID
     *
     * @param string $id Colissimo parcel number
     * @param array $options Additional parameters
     *
     * @return ParcelStatus
     */
    public function getStatusByID($id, $options = [])
    {
      $options = array_merge(
        array(
          'skybillNumber ' => $id,
        ),
        $options
      );

      $result = $this->soapExec(
        'track',
        $options
      );

      $result = $result->return;

      if ($result->errorCode != 0) {
        throw new \Exception(
          'Failed to get status: ' . $result->errorMessage
        );
      }

      $parcelStatus = new ParcelStatus($result);

      return $parcelStatus;
    }
  }