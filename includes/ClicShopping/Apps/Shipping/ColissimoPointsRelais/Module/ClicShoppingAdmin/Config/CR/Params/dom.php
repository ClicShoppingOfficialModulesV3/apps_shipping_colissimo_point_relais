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

  class dom extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0.250:.45,0.5:945,0.75:79.45,1:14.35,2:19.50,5:29.35,10:47.10,30:105';
    public $sort_order = 1070;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_dom_title');
      $this->description = $this->app->getDef('cfg_colissimo_dom_desc');
    }
  }
