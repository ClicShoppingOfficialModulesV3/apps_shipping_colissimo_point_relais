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

  use ClicShopping\Apps\Shipping\ColissimoPointsRelais\ColissimoPointsRelais as ColissimoPointsRelais;

  class Update implements \ClicShopping\OM\Modules\HooksInterface
  {

    protected $app;
    protected $ordersTrackingId;
    protected $ordersTrackingNumber;

    public function __construct()
    {
      if (!Registry::exists('ColissimoPointsRelais')) {
        Registry::set('ColissimoPointsRelais', new ColissimoPointsRelais());
      }

      $this->app = Registry::get('ColissimoPointsRelais');

      $this->oID = HTML::sanitize($_GET['oID']);
      $this->ordersTrackingId = HTML::sanitize($_POST['orders_tracking_id']);
      $this->ordersTrackingNumber = HTML::sanitize($_POST['orders_tracking_number']);
    }

    public function execute()
    {
      if (!is_null($this->ordersTrackingId) || !is_null($this->ordersTrackingNumber)) {
        /*
                $QOrdersStatusHistory = $this->app->db->prepare('select orders_status_history_id
                                                                  from :table_orders_status_history
                                                                  where orders_id = :orders_id
                                                                  order by orders_status_history_id desc 
                                                                  limit 1
                                                                ');
                $QOrdersStatusHistory->bindInt('orders_id', $this->oID);
                $QOrdersStatusHistory->execute();
        
        
                $QUpdateStatusHistory = $this->app->db->prepare('update :table_orders_status_history
                                                                  set orders_tracking_number = :orders_tracking_number,
                                                                      orders_status_tracking_id = :orders_status_tracking_id
                                                                  where orders_id = :orders_id
                                                                  and orders_status_history_id  = :orders_status_history_id
                                                                ');
                $QUpdateStatusHistory->bindInt('orders_id', $this->oID);
                $QUpdateStatusHistory->bindInt('orders_status_tracking_id', $this->ordersTrackingId);
                $QUpdateStatusHistory->bindValue('orders_tracking_number', $this->ordersTrackingNumber);
                $QUpdateStatusHistory->bindInt('orders_status_history_id', $QOrdersStatusHistory->valueInt('orders_status_history_id'));
                $QUpdateStatusHistory->execute();
        */
      }
    }
  }