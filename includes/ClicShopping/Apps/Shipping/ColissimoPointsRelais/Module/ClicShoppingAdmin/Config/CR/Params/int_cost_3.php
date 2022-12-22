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

  class int_cost_3 extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '1:22.50, 2:30.10, 3:39.50, 4:48.90, 5:58.30, 6:67.70, 7:77.10, 8:86.50, 9:95.90, 10:105.30, 15:128.80, 20:152.30';
    public $sort_order = 1250;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_int_cost_3_title');
      $this->description = $this->app->getDef('cfg_colissimo_int_cost_3_desc');
    }
  }
