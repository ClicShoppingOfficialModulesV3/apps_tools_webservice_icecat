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

  namespace ClicShopping\Apps\WebService\IceCat\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Preview extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_IceCat = Registry::get('IceCat');

      $this->page->setFile('preview.php');

      $CLICSHOPPING_IceCat->loadDefinitions('Sites/ClicShoppingAdmin/preview');
    }
  }