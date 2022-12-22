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

  class int_countries_3 extends \ClicShopping\Apps\Shipping\ColissimoPointsRelais\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'AF, AO, AI, AG, AM, AZ, BJ, BM, BW, BF, BI, CM, CA, CV, CF, TD, CG, CI, CY, CJ, US, UM';
    public $sort_order = 1240;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_colissimo_int_countries_3_title');
      $this->description = $this->app->getDef('cfg_colissimo_int_countries_3_desc');
    }
  }
