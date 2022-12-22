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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\FileSystem;

  use ClicShopping\Apps\WebService\IceCat\Classes\ClicShoppingAdmin\IceCatFileImport;

  $CLICSHOPPING_IceCat = Registry::get('IceCat');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();


  $CLICSHOPPING_IceCatFileImport = new IceCatFileImport($code, 'EN', $brand);

?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/icecat.png', $CLICSHOPPING_IceCat->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-3 pageHeading"><?php echo $CLICSHOPPING_IceCat->getDef('heading_title'); ?></span>
          <span class="col-md-8 text-end">
<?php
  echo HTML::button($CLICSHOPPING_IceCat->getDef('button_configure'), null, $CLICSHOPPING_IceCat->link('Configure'), 'success') . ' ';
  echo HTML::button($CLICSHOPPING_IceCat->getDef('button_listing'), null, $CLICSHOPPING_IceCat->link('ItemsListing'), 'primary');
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div style="padding-left: 10px" ;>
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li
        class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_IceCat->getDef('title_icecat_configuration_google_ai') . '</a>'; ?></li>
    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
        <div class="tab-pane active" id="tab1">
          <div class="col-md-12 mainTitle">
            <strong><?php echo $CLICSHOPPING_IceCat->getDef('text_info_new_export'); ?></strong></div>
          <div class="adminformTitle">

            <div class="form-group row">
              <label for="export"
                     class="col-5 col-form-label"><?php echo 'Veuillez selectionner votre type de fichier'; ?></label>
              <div class="col-md-5">
                <?php
                  $array[] = ['id' => '0', 'text' => 'Select'];
                  $array[] = ['id' => '1', 'text' => 'Free XML daily'];
                  $array[] = ['id' => '2', 'text' => 'Free All XML'];
                  $array[] = ['id' => '3', 'text' => 'Icecat Not free XML daily'];
                  $array[] = ['id' => '4', 'text' => 'Icecat Not free All XML'];

                  echo HTML::selectMenu('file_type', $array);

                  //daily.index.xml

                  $xml = $CLICSHOPPING_IceCatFileImport->getIceCatFileContent();

                  // Prod_ID - EAN - IsApproved = 1

                  /*
                    $product = $xml->xpath("//file");
                    foreach ($product as $item) {
                      $product = $item[0]->attributes();
                  //    SUPPLIER or  ICECAT
                  
                      if ($product['Quality'] == 'ICECAT') {
                  
                        echo 'Prod_ID : ' . $product['Prod_ID'] .'<br>';
                        echo 'On_Market : ' . $product['On_Market'] .'<br>';
                        echo 'Model_Name : ' . $product['Model_Name'] .'<br>';
                        echo 'HighPic : ' . $product['HighPic'] .'<br>';
                        echo 'Date_Added : ' . $product['Date_Added'] .'<br>';
                        echo 'Quality : ' . $product['Quality'] .'<br>';
                        echo 'Product_View : ' . $product['Product_View'] .'<br>';
                        echo 'Catid : ' . $product['Catid'] .'<br>';
                  
                        ECHO '<hr>';
                      } else {
                  
                      }
                    }
                  */


                  /*
                    $product = $xml->xpath("//EAN_UPC");
                  
                    foreach ($product as $item) {
                        $product_ean = $item[0]->attributes();
                  
                        if ($product_ean['IsApproved'] == 1) {
                          $sku = $product_ean['Value'];
                          echo $sku . '<br />';
                        }
                    }
                  */
                  /*
                    $country_market = $xml->xpath("//Country_Markets");
                    foreach ($country_market as $item) {
                      $titleXML = new \SimpleXMLElement($item->asXML());
                      $country = $titleXML->xpath("//Country_Market");
                  
                      $country = $country[0]->attributes();
                  
                      if ($country['Value'] == 'FR') {
                        echo $country['Value'];
                        echo '<br />';
                      }
                    }
                  */


                ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>