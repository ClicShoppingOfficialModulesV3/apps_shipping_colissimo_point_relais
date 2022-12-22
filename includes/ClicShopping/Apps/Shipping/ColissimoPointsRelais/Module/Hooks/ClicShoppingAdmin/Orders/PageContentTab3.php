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

  namespace ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\Hooks\ClicShoppingAdmin\Orders;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\Apps\Orders\Orders\Classes\ClicShoppingAdmin\OrderAdmin;

  use ClicShopping\Apps\Shipping\ColissimoPointsRelais\ColissimoPointsRelais as ColissimoPointsRelaisApp;

  class PageContentTab3 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('ColissimoPointsRelais')) {
        Registry::set('ColissimoPointsRelais', new ColissimoPointsRelaisApp());
      }

      $this->app = Registry::get('ColissimoPointsRelais');

    }

    private function getStatus()
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Order = Registry::get('Order');

      // orders_invoice status Dropdown
      $orders_tracking_statuses = [];
      $orders_status_tracking_array = [];

      $QordersStatusTracking = $this->app->db->prepare('select orders_status_tracking_id,
                                                                 orders_status_tracking_name
                                                         from :table_orders_status_tracking
                                                         where language_id = :language_id
                                                        ');
      $QordersStatusTracking->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QordersStatusTracking->execute();

      while ($QordersStatusTracking->fetch() !== false) {
        $orders_tracking_statuses[] = ['id' => $QordersStatusTracking->valueInt('orders_status_tracking_id'),
          'text' => $QordersStatusTracking->value('orders_status_tracking_name')
        ];
//        $orders_status_tracking_array[$QordersStatusTracking->valueInt('orders_status_tracking_id')] = $QordersStatusTracking->value('orders_status_tracking_name');
      }

      return $orders_tracking_statuses;
    }

    public function display()
    {
      global $order;

      if (!defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_STATUS') || CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Orders/page_content_tab3');


      /*
            $postage = new Postage(['login' =>  CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGIN,
                                    'password' =>  CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_PASSWORD
                                   ]
                                  );
      
            $outputFormat = ['x' => 0,
                             'y' => 0,
                             'outputPrintingType' => 'ZPL_10x15_203dpi'
                            ];
      
            $letter = ['productCode' => 'DOS',
                       'depositDate' => '2018-06-25',
                        'orderNumber' => '15',
                        'commercialName' => 'commercialName'
                      ];
      */

      //   $postage = $postage->generateLabel($outputFormat, $letter, $options = array());
      if (isset($_GET['oID']) && is_numeric($_GET['oID']) && ($_GET['oID'] > 0)) {
        $oID = HTML::sanitize($_GET['oID']);

        $Qorders = $this->app->db->prepare('select *
                                            from :table_orders
                                            where orders_id = :orders_id
                                            ');
        $Qorders->bindInt(':orders_id', $oID);
        $Qorders->execute();


      $contract_number = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGIN;
      $password = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_PASSWORD;
      $store_name = STORE_NAME;
      $script_link = CLICSHOPPING::link('Shop/ext/javascript/colissimo/label.js');


//delivery
      $receiver_company = $Qorders->value('delivery_company');
      $receiver_name = $Qorders->value('delivery_name');
      $receiver_street_address = $Qorders->value('delivery_street_address');
      $receiver_suburb = $Qorders->value('delivery_suburb');
      $receiver_city = $Qorders->value('delivery_city');
      $receiver_postcode = $Qorders->value('delivery_postcode');
      $receiver_state = $Qorders->value('delivery_state');
      $receiver_country = $Qorders->value('delivery_country');
      $receiver_telephone = $Qorders->value('delivery_telephone');
      $receiver_email_address = $Qorders->value('delivery_email_address');


//sender
      $company = $Qorders->value('customers_company');
      $name = $Qorders->value('customers_name');
      $street_address = $Qorders->value('customers_street_address');
      $suburb = $Qorders->value('customers_suburb');
      $city = $Qorders->value('customers_city');
      $postcode = $Qorders->value('customers_postcode');
      $state = $Qorders->value('customers_state');
      $country = $Qorders->value('country');
      $telephone = $Qorders->value('customers_telephone');
      $email_address = $Qorders->value('customers_email_address');


      $output = '';

      $content = '<!-- order tracking start -->';
      $content .= '<div class="separator"></div>';
      $content .= '<div class="row">';
//      $content .= '<span class="col-md-2"><strong>' . $this->app->getDef('entry_status_orders_tracking_name') . '</strong></span>';
//      $content .= '<span class="col-md-4">' . HTML::selectMenu('orders_tracking_id', $this->getStatus(), $order->info['orders_status_tracking']) . '</span>';
      $content .= '<span class="col-md-2"><strong>' . $this->app->getDef('entry_status_orders_tracking_number') . '</strong></span>';
      $content .= '<span class="col-md-4">' . HTML::inputField('orders_tracking_number', $Qorders->value('orders_tracking_number')) . '</span>';
      $content .= '</div>';
      $content .= '<!-- order tracking end -->';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start order tracking     -->
<!-- ######################## -->
<script  src="{$script_link}"></script>
<script>
const colissimo  = require('colissimo') ({ contract_number: '{$contract_number}', password: '{$password}' })

colissimo.label ({
	sender: {
		last_name: '{$name}',
		first_name: '{$name}',
		address: '{$street_address} {$suburb}',
		to_know: 'to know',
		zip_code: '{$postcode}',
		city: '{$city}',
		phone_number: '{$telephone}',
		mail: '{$email_address}'
	},
	receiver: {
		last_name: ''{$receiver_name}',
		first_name: '{$receiver_name}',
		address: '{$receiver_street_address} {$receiver_suburb}',
		to_know: 'to know',
		zip_code: '{$receiver_postcode}',
		city: '{$receiver_city}',
		phone_number: '{$receiver_telephone}',
		mail: '{$receiver_email_address}'
	},
	product: {
		identifier: '1578',				// used to identify a package when you received it. its displayed before the company_name
		insurance_value: 100,			// the amount to insure
		weight: 1.2						// in kg, default 1
	},
	format: {
		commercial_name: '{$store_name}' // used for notifications
	}
}).then (infos => {
	console.log (infos)
}).catch (error => {
	console.error (error)
})
</script>

<script>
$('#contentTab3').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End order tracking      -->
<!-- ######################## -->

EOD;
      return $output;
      }
    }
  }