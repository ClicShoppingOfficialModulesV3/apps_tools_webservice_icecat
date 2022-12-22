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

  namespace ClicShopping\Apps\WebService\IceCat\Module\ClicShoppingAdmin\Config\IC\Params;

  class password extends \ClicShopping\Apps\WebService\IceCat\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public $default = '';
    public $app_configured = true;
    public $sort_order = 100;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_icecat_password_title');
      $this->description = $this->app->getDef('cfg_icecat_password_description');
    }
  }
