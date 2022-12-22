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

  class int_cost_1 extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '1:16.05, 2:17.65, 3:21.30, 4:24.95, 5:28.60, 6:32.25, 7:35.90, 8:39.55, 9:43.20, 10:46.85, 15:53.85, 20:60.85, 25:67.85, 30:74.85';
    public $sort_order = 1210;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_int_cost_1_title');
      $this->description = $this->app->getDef('cfg_colissimo_int_cost_1_desc');
    }
  }
