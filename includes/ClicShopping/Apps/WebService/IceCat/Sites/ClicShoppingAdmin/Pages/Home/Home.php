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

  namespace ClicShopping\Apps\WebService\IceCat\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\WebService\IceCat\IceCat;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_IceCat = new IceCat();
      Registry::set('IceCat', $CLICSHOPPING_IceCat);

      $this->app = Registry::get('IceCat');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
