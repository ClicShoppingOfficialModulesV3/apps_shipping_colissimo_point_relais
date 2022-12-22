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

  class tom extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0.25:11.40,0.5:11.40,0.75:11.40,1:17.10,2:30.10,5:50.50,10:98.50,30:250';
    public $sort_order = 1140;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_tom_title');
      $this->description = $this->app->getDef('cfg_colissimo_tom_desc');
    }
  }
