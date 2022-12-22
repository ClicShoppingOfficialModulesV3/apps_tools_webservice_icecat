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


  namespace ClicShopping\Apps\WebService\IceCat\Sites\ClicShoppingAdmin\Pages\Home\Actions\IceCat;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $productsId;
    protected $currentCategoryId;

    public function __construct()
    {
      if (!Registry::exists('IceCat')) {
        Registry::set('IceCat', new IceCatApp());
      }

      $this->app = Registry::get('IceCat');

      $this->productsId = HTML::sanitize($_POST['pID']); // update
      $this->currentCategoryId = HTML::sanitize($_GET['cPath']); // boxe
    }

    public function execute()
    {
      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

      $CLICSHOPPING_ProductsAdmin->save($this->productsId, 'Update');

      CLICSHOPPING::redirect(null, 'A&Catalog\Products&Products&cPath=' . $this->currentCategoryId . '&pID=' . $this->productsId);
    }
  }