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

  class sort_order extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public $default = '0';
    public $app_configured = false;
    public $sort_order = 300;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_points_relais_sort_order_title');
      $this->description = $this->app->getDef('cfg_colissimo_points_relais_sort_order_description');
    }
  }
