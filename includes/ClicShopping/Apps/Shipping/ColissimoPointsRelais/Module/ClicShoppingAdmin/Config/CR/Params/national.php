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

  class national extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0.250:4.95,0.5:6.25,750:7.10,1:7.80,2:8,80:5:13.35,10:19.50,30:27.80';
    public $sort_order = 1000;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_national_title');
      $this->description = $this->app->getDef('cfg_colissimo_national_desc');
    }
  }
