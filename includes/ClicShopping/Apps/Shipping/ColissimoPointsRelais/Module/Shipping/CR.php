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

  namespace ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\Shipping;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Shipping\ColissimoPointsRelais\ColissimoPointsRelais as ColissimoPointsRelaisApp;

  use ClicShopping\Sites\Common\B2BCommon;

  use ClicShopping\Apps\Shipping\ColissimoPointsRelais\Classes\Shop\DeliveryChoice;

  class CR implements \ClicShopping\OM\Modules\ShippingInterface
  {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public $icon;
    public mixed $app;
    public $quotes;

    public function __construct()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (Registry::exists('Order')) {
        $CLICSHOPPING_Order = Registry::get('Order');
      }

      if (!Registry::exists('ColissimoPointsRelais')) {
        Registry::set('ColissimoPointsRelais', new ColissimoPointsRelaisApp());
      }

      $this->app = Registry::get('ColissimoPointsRelais');
      $this->app->loadDefinitions('Module/Shop/CR/CR');

      $this->signature = 'ColissimoPointsRelais|' . $this->app->getVersion() . '|1.0';
      $this->api_version = $this->app->getApiVersion();

      $this->code = 'CR';
      $this->title = $this->app->getDef('module_colissimo_points_relais_title');
      $this->public_title = $this->app->getDef('module_colissimo_points_relais_public_title');
      $this->sort_order = defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_SORT_ORDER') ? CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_SORT_ORDER : 0;

// Activation module du paiement selon les groupes B2B
      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        if (B2BCommon::getShippingUnallowed($this->code)) {
          if (CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_STATUS == 'True') {
            $this->enabled = true;
          } else {
            $this->enabled = false;
          }
        }
      } else {
        if (defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_NO_AUTHORIZE') && CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_NO_AUTHORIZE == 'True' && $CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
          if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
            if (CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_STATUS == 'True') {
              $this->enabled = true;
            } else {
              $this->enabled = false;
            }
          }
        }
      }

      if (defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_TAX_CRASS')) {
        if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
          if (B2BCommon::getTaxUnallowed($this->code) || !$CLICSHOPPING_Customer->isLoggedOn()) {
            $this->tax_class = defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_TAX_CRASS') ? CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_TAX_CRASS : 0;

          }
        } else {
          if (B2BCommon::getTaxUnallowed($this->code)) {
            $this->tax_class = defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_TAX_CRASS') ? CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_TAX_CRASS : 0;
          }
        }
      }

      if (($this->enabled === true) && ((int)CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_ZONE > 0)) {
        $check_flag = false;

        $Qcheck = $this->app->db->get('zones_to_geo_zones', 'zone_id', ['geo_zone_id' => (int)CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_ZONE,
          'zone_country_id' => $CLICSHOPPING_Order->delivery['country']['id']
        ],
          'zone_id'
        );

        while ($Qcheck->fetch()) {
          if (($Qcheck->valueInt('zone_id') < 1) || ($Qcheck->valueInt('zone_id') == $CLICSHOPPING_Order->delivery['zone_id'])) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag === false) {
          $this->enabled = false;
        }

        if ($this->shipping_weight > 30) {
          $this->enabled = false;
        }

        if (!defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGIN') || empty(CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGIN)) {
          $this->enabled = false;
        }

        if (!defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_PASSWORD') || empty(CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_PASSWORD)) {
          $this->enabled = false;
        }
      }
    }

    public function quote($method = '')
    {
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_Tax = Registry::get('Tax');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Shipping = Registry::get('Shipping');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      $this->shipping_weight = $CLICSHOPPING_Shipping->getShippingWeight();

      if ($this->shipping_weight >= 0.1 && $this->shipping_weight <= 30) {

        $dest_country = $CLICSHOPPING_Order->delivery['country']['iso_code_2'];

// insurance Fr, DOM and TOM
        $insurance50 = 2.5; // until 50 €
        $insurance200 = 4; // until 200 €
        $insurance300 = 3; // until 300 €
        $insurance400 = 4; // until 400 €
        $insurance500 = 5; // until 500 €

        if (($dest_country == 'FR') OR ($dest_country == 'FX') OR ($dest_country == 'MC')) {
          $auto = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_R1R5;
          $total = $CLICSHOPPING_ShoppingCart->show_total();

          $cost = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_NATIONAL;
          $table = preg_split('#[:,]#', $cost);

          $j = 0;
          $k = 0;

          for ($i = 0; $i < count($table); $i += 2) {
            if ($this->shipping_weight > $table[$i]) continue;
            if (($this->shipping_weight < $table[$i]) && ($j == '0')) {
              if ($auto == 'True') {
                if (($total <= 50) && ($k == '0')) {
                  $methods[] = ['id' => 'R1',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r1_text_title'),
                    'cost' => $table[$i + 1] + $insurance50
                  ];
                  $k++;
                } elseif (($total > 50) && ($total <= 200) && ($k == '0')) {
                  $methods[] = ['id' => 'R2',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r2_text_title'),
                    'cost' => $table[$i + 1] + $insurance200
                  ];
                  $k++;
                } elseif (($total > 200) && ($total <= 300) && ($k == '0')) {
                  $methods[] = ['id' => 'R3',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r3_text_title'),
                    'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance300
                  ];
                  $k++;
                } elseif (($total > 300) && ($total <= 400) && ($k == '0')) {
                  $methods[] = ['id' => 'R4',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r4_text_title'),
                    'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance400
                  ];
                  $k++;
                } elseif (($total > 400) && ($k == '0')) {
                  $methods[] = ['id' => 'R5',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r5_text_title'),
                    'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance500
                  ];
                  $k++;
                }
              } else {
// Apparition du choix pour le client de la methode d'expedition
                if (empty($method) || $method == 'R0') {
                  $methods[] = ['id' => 'R0',
                    'title' => $this->app->getDef('module_colissimo_points_relais_text_title'),
                    'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                  ];
                }
                if (empty($method) || $method == 'R1') {
                  $methods[] = ['id' => 'R1',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r1_text_title'),
                    'cost' => $table[$i + 1] + $insurance50 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                  ];
                }
                if (empty($method) || $method == 'R2') {
                  $methods[] = ['id' => 'R2',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r2_text_title'),
                    'cost' => $table[$i + 1] + $insurance200 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                  ];
                }
                if (empty($method) || $method == 'R3') {
                  $methods[] = ['id' => 'R3',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r3_text_title'),
                    'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance300 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                  ];
                }
                if (empty($method) || $method == 'R4') {
                  $methods[] = ['id' => 'R4',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r4_text_title'),
                    'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance400 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                  ];
                }
                if (empty($method) || $method == 'R5') {
                  $methods[] = ['id' => 'R5',
                    'title' => $this->app->getDef('module_colissimo_points_relais_r5_text_title'),
                    'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance500 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                  ];
                }

                $j = '2';
              }
            }
          }
        } elseif (($dest_country == 'GP') OR ($dest_country == 'MQ') OR ($dest_country == 'GF') OR ($dest_country == 'RE') OR ($dest_country == 'YT') OR ($dest_country == 'PM')) {
          if (CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_DOM_STATUS == 'True') {
            $auto = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_R1R5;
            $total = $CLICSHOPPING_ShoppingCart->show_total();

            $cost = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_DOM;
//              $cost1 = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_COLISSIMOR1_DOM;

            $table = preg_split('#[:,]#', $cost);
//              $table1 = preg_split('#[:,]#', $cost1);

            $j = '0';
            $k = '0';

            for ($i = 0; $i < count($table); $i += 2) {
              if ($this->shipping_weight > $table[$i])
                continue;
              if (($this->shipping_weight < $table[$i]) && ($j == '0')) {
                if ($auto == 'True') {
                  if (($total <= 50) && ($k == '0')) {
                    $methods[] = ['id' => 'DOMR1',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r1_text_title'),
                      'cost' => $table[$i + 1] + $insurance50
                    ];
                    $k++;
                  } elseif (($total > 50) && ($total <= 200) && ($k == '0')) {
                    $methods[] = ['id' => 'DOMR2',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r2_text_title'),
                      'cost' => $table[$i + 1] + $insurance200
                    ];
                    $k++;
                  } elseif (($total > 200) && ($total <= 300) && ($k == '0')) {
                    $methods[] = ['id' => 'DOMR3',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r3_text_title'),
                      'cost' => $table[$i + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance300]
                    ];
                    $k++;
                  } elseif (($total > 300) && ($total <= 400) && ($k == '0')) {
                    $methods[] = ['id' => 'DOMR4',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r4_text_title'),
                      'cost' => $table[$i + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance400]
                    ];
                    $k++;
                  } elseif (($total > 500) && ($k == '0')) {
                    $methods[] = ['id' => 'DOMR5',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r5_text_title'),
                      'cost' => $table[$i + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance500]
                    ];
                    $k++;
                  }
                } else {
                  if ($method == '' || $method == 'DOMR0') {
                    $methods[] = ['id' => 'DOMR0',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r5_text_title'),
                      'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'DOMR1') {
                    $methods[] = ['id' => 'DOMR1',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r1_text_title'),
                      'cost' => $table[$i + 1] + $insurance50 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'DOMR2') {
                    $methods[] = ['id' => 'DOMR2',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r2_text_title'),
                      'cost' => $table[$i + 1] + $insurance200 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'DOMR3') {
                    $methods[] = ['id' => 'DOMR3',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r3_text_title'),
                      'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance300 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'DOMR4') {
                    $methods[] = ['id' => 'DOMR4',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r4_text_title'),
                      'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance400 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'DOMR5') {
                    $methods[] = ['id' => 'DOMR5',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r5_text_title'),
                      'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance500 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  $j = '2';
                }
              }
            }
          }

        } elseif (($dest_country == 'NC') OR ($dest_country == 'PF') OR ($dest_country == 'WF') OR ($dest_country == 'TF')) {
          if (CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_TOM_STATUS == 'True') {

            $auto = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_R1R5;
            $total = $CLICSHOPPING_ShoppingCart->show_total();

            $cost = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_TOM;
            $cost1 = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_COLISSIMOR1_TOM;

            $table = preg_split('#[:,]#', $cost);
            $table1 = preg_split('#[:,]#', $cost1);

            $j = '0';
            $k = '0';

            for ($i = 0; $i < count($table); $i += 2) {
              if ($this->shipping_weight > $table[$i]) continue;

              if (($this->shipping_weight < $table[$i]) && ($j == 0)) {
                if ($auto == 'True') {
                  if (($total <= 50) && ($k == '0')) {
                    $methods[] = ['id' => 'TOMR1',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r1_text_title'),
                      'cost' => $table1[$i + 1] + $insurance50
                    ];
                    $k++;
                  } elseif (($total > 50) && ($total <= 200) && ($k == '0')) {
                    $methods[] = ['id' => 'TOMR2',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r2_text_title'),
                      'cost' => $table1[$i + 1] + $insurance200
                    ];
                    $k++;
                  } elseif (($total > 200) && ($total <= 300) && ($k == '0')) {
                    $methods[] = ['id' => 'TOMR3',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r3_text_title'),
                      'cost' => $table1[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance300
                    ];
                    $k++;
                  } elseif (($total > 300) && ($total <= 400) && ($k == '0')) {
                    $methods[] = ['id' => 'TOMR4',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r4_text_title'),
                      'cost' => $table1[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance400
                    ];
                    $k++;
                  } elseif (($total > 500) && ($k == '0')) {
                    $methods[] = ['id' => 'TOMR5',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r5_text_title'),
                      'cost' => $table1[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance500
                    ];
                    $k++;
                  }
                } else {
                  if ($method == '' || $method == 'TOMR0') {
                    $methods[] = ['id' => 'TOMR0',
                      'title' => $this->app->getDef('module_colissimo_points_relais_text_title'),
                      'cost' => $table[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'TOMR1') {
                    $methods[] = ['id' => 'TOMR1',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r1_text_title'),
                      'cost' => $table1[$i + 1] + $insurance50 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'TOMR2') {
                    $methods[] = ['id' => 'TOMR2',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r2_text_title'),
                      'cost' => $table1[$i + 1] + $insurance200 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'TOMR3') {
                    $methods[] = ['id' => 'TOMR3',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r3_text_title'),
                      'cost' => $table1[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance300 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'TOMR4') {
                    $methods[] = ['id' => 'TOMR4',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r4_text_title'),
                      'cost' => $table1[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance400 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  if ($method == '' || $method == 'TOMR5') {
                    $methods[] = ['id' => 'TOMR5',
                      'title' => $this->app->getDef('module_colissimo_points_relais_r5_text_title'),
                      'cost' => $table1[$i + 1] + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_AD_VALOREM + $insurance500 + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
                    ];
                  }
                  $j = '2';
                }
              }
            }
          }

        } elseif (CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_INT_STATUS == 'True') {
          $dest_zone = 0;

          for ($i = 1; $i <= $this->num_international; $i++) {
            $countries_table = CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_INT_COUNTRIES_ . $i;

            $country = preg_split('#[, ]#', $countries_table);
            if (in_array($dest_country, $country)) {
              $dest_zone = $i;
              break;
            }
          }

          if ($dest_zone == 0) {
            $this->quotes['error'] = $this->app->getDef('module_colissimo_points_relais_intl_invalid_zone');

            return $this->quotes;
          }

          $table = preg_split('#[:,]#', CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_INT_COST_ . $dest_zone);
          $cost = -1;

          for ($i = 0, $n = count($table); $i < $n; $i += 2) {
            if ($this->shipping_weight <= $table[$i]) {
              $cost = $table[$i + 1];
              break;
            }
          }

          if ($cost == -1) {
            $this->quotes['error'] = $this->app->getDef('module_colissimo_points_relais_intl_undefined_rate');

            return $this->quotes;
          }

          $methods[] = [
            'id' => $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code,
            'title' => $this->app->getDef('module_colissimo_points_relais_int_text_way') . ' ' . $CLICSHOPPING_Order->delivery['country']['title'],
            'cost' => $cost + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
          ];
        }


        if (!empty(CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGIN) && (CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_INT_STATUS == 'True' && $dest_country == 'FR') || CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_INT_STATUS == 'False') {
          $title_colissimo = $methods[0]['title'];
          $cost_colissimo = $methods[0]['cost'];
          $id_colissimo = $methods[0]['id'];


          $delivery = new DeliveryChoice(['login' => CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGIN,
              'password' => CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_PASSWORD
            ]
          );

          $result = $delivery->findPickupPoints($CLICSHOPPING_Order->delivery['city'],
            $CLICSHOPPING_Order->delivery['postcode'],
            $dest_country,
            date('d/m/Y'),
            ['address' => $CLICSHOPPING_Order->delivery['street_address'] . ' ' . $CLICSHOPPING_Order->delivery->delivery['suburb']]
          );

          $c = 0;

          if (empty(CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_MAX_FORAGE)) {
            $max_forage = 20;
          } else {
            $max_forage = (int)CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_MAX_FORAGE;
          }

          foreach ($result as $value) {
            if ($c < $max_forage) {
              $latitude = $value->latGeoCoord;
              $longitude = $value->longGeoCoord;

              $geolocalisation = '
                                      <a data-toggle="modal" data-target="#GeoModal' . $c . '"> - <i class="fas fa-map-marked-alt text-primary"></i> ' . $this->app->getDef('module_colissimo_points_relais_map') . '  -</a>
                                      <div class="modal fade" id="GeoModal' . $c . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                          <div class="modal-content">
                                            <div class="modal-body">
                                              <iframe width="450" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=' . $longitude . '%2C' . $latitude . '%2C' . $longitude . '%2C' . $latitude . '&amp;layer=mapnik&amp;marker=' . $latitude . '%2C' . $longitude . '" style="border: 1px solid black"></iframe><br/><small><a target="_blank" href="https://www.openstreetmap.org/?mlat=' . $latitude . '&amp;mlon=' . $longitude . '#map=13/' . $latitude . '/' . $longitude . '">' . $this->app->getDef('module_colissimo_points_relais_zoom_map') . '</a></small>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    ';
              $title_point_relais = '<br />' . $value->name . ' - ' . $value->id . '<br />' . $value->address . ' ' . $value->addressOptional . ' ' . $value->locality . ' ' . $value->city . ' ' . $value->postalCode . ' ' . $value->partialClosed . ' ' . $geolocalisation;

              $methods[] = ['id' => $value->id,
                'title' => $title_point_relais . '<br />' . $title_colissimo . '<br /> ',
                'cost' => $cost_colissimo + CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_HANDLING
              ];

              $c++;
            }
          }

          $this->quotes = ['id' => $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code,
            'module' => $this->title . ' (' . $this->shipping_weight . ' Kg) ',
            'methods' => $methods
          ];


        } else {
          $this->quotes = ['id' => $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code,
            'module' => $this->title . ' (' . $this->shipping_weight . ' Kg)',
            'methods' => $methods
          ];
        }

        if ($this->tax_class > 0) {
          $this->quotes['tax'] = $CLICSHOPPING_Tax->getTaxRate($this->tax_class, $CLICSHOPPING_Order->delivery['country']['id'], $CLICSHOPPING_Order->delivery['zone_id']);
        }


        if (!empty(CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGO) && is_file($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/shipping/' . CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGO)) {
          $this->icon = $CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/shipping/' . CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_LOGO;
          $this->icon = HTML::image($this->icon, $this->title);
        } else {
          $this->icon = '';
        }


        if (!is_null($this->icon)) $this->quotes['icon'] = '<br />&nbsp;&nbsp;&nbsp;' . $this->icon;

        return $this->quotes;
      } else {
        return false;
      }
    }

    public function check()
    {
      return defined('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_STATUS') && (trim(CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_STATUS) != '');
    }

    public function install()
    {
      $this->app->redirect('Configure&Install&module=ColissimoPointsRelais');
    }

    public function remove()
    {
      $this->app->redirect('Configure&Uninstall&module=ColissimoPointsRelais');
    }

    public function keys()
    {
      return array('CLICSHOPPING_APP_COLISSIMO_POINTS_RELAIS_CR_SORT_ORDER');
    }
  }