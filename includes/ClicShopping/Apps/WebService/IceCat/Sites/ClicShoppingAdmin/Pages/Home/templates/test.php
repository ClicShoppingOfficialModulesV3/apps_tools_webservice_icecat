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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use DiDom\Document;
  use DiDom\Query;

  use ClicShopping\Apps\WebService\IceCat\Classes\ClicShoppingAdmin\PriceScrappingDimDom;

  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);


  $CLICSHOPPING_PriceScrapping = new PriceScrappingDimDom();

  $url = 'https://www.cdiscount.com/high-tech/televiseurs/tv-4k-uhd/toutes-les-tv-4k-uhd/l-106262305.html#_his_';
  $parameters_content = 'div.lpTopBox';
  $parameters_title = 'div.prdtBTit';
  $parameters_price = 'span.price';
//  var_dump($CLICSHOPPING_PriceScrapping->getProductListingPrice($url, $parameters_content, $parameters_title, $parameters_price));


  $url = 'https://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords=ordinateur';
  $parameters_content = '#atfResults';
  $parameters_title = 'h2';
  $parameters_price = 'span.a-offscreen';
//  var_dump($CLICSHOPPING_PriceScrapping->getProductListingPrice($url, $parameters_content, $parameters_title, $parameters_price));

  /*
    $url = 'https://www.rueducommerce.fr/recherche/ordinateur-samsung?sort=score&universe=MC-3540&view=grid';
    $parameters_content = 'section.grid';
    $parameters_title = 'span[itemprop=name]';
    $parameters_price = 'div.price';
    var_dump($CLICSHOPPING_PriceScrapping->getProductListingPrice($url, $parameters_content, $parameters_title, $parameters_price));
  */
  /*
    $url = 'https://www.ldlc.com/navigation/galaxy+note+8/';
    $parameters_content = '#productListingWrapper';
    $parameters_title = 'a.nom';
    $parameters_price = 'span.price';
    $text_search = 'Samsung Galaxy Note 8';
    $result = $CLICSHOPPING_PriceScrapping->getProductListingPrice($url, $parameters_content, $parameters_title, $parameters_price);
  */
  $search_keywords = 'Galaxy%20note%208';
  $url = 'https://www.bestbuy.ca/fr-CA/Search/SearchResults.aspx?query=' . $search_keywords;
  $parameters_content = '#ctl00_CC_ListingProduct';
  $parameters_title = 'h4.prod-title';
  $parameters_price = 'span.amount';
  $text_search = 'Galaxy Note8 de 64 Go';
  $result = $CLICSHOPPING_PriceScrapping->getProductListingPrice($url, $parameters_content, $parameters_title, $parameters_price);

  /*
      $url =  'http://www.canadiantire.ca/en/sports-rec/bikes-accessories/bikes/hybrid-bikes.html?adlocation=LIT_Category_Product_HybridBikesCat_en';
      $parameters_content = 'div.assortment-right-column';
      $parameters_title = 'h3.product-tile-srp__title';
      $parameters_price = 'div.product-tile__price';
      $text_search = null;
      $result = $CLICSHOPPING_PriceScrapping->getProductListingPrice($url, $parameters_content, $parameters_title, $parameters_price);
    */
  if ($CLICSHOPPING_MessageStack->exists('scrapping')) {
    echo $CLICSHOPPING_MessageStack->get('scrapping');
  }

  if (!is_null($result)) {
    ?>
    <table class="table table-sm table-hover table-striped">
      <thead>
      <tr class="dataTableHeadingRow">
        <td>title</td>
        <td>Prix</td>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td colspan="2"><span class="alert-success"
                              role="alert"><?php echo HTML::link($url, $url, 'target="_blank"'); ?></span></td>
      </tr>
      <?php
        foreach ($result as $item) {
          $price = $item['1'];
          if (!empty($text_search)) {
            if (strstr($item['0'], $text_search)) {
              echo '<tr>';
              echo '<td>' . $item['0'] . '</td>';
              echo '<td>' . $item['1'] . '</td>';
              echo '</tr>';
            }
          } else {
            echo '<tr>';
            echo '<td>' . $item['0'] . '</td>';
            echo '<td>' . $item['1'] . '</td>';
            echo '</tr>';
          }
        }
      ?>
      </tbody>
    </table>
    <?php
  } else {
    echo '<div class="separator"></div>';
    echo '<div class="alert alert-info text-center">No result identified</div>';
  }
