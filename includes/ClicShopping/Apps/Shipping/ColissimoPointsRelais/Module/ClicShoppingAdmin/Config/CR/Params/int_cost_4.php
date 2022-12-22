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

  namespace ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\CR\Params;

  class int_cost_4 extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '1:25.40, 2:38.10, 3:50.80, 4:63.50, 5:76.20, 6:88.90, 7:101.60, 8:114.30, 9:127.00, 10:139.70, 15:164.70, 20:189.70';
    public $sort_order = 1270;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_int_cost_4_title');
      $this->description = $this->app->getDef('cfg_colissimo_int_cost_4_desc');
    }
  }
