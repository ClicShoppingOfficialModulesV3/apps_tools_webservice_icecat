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


  //http://live.icecat.biz/api/?shopname=openIcecat-live&lang=us&content=&icecat_id=34524578
  //http://icecat.us/us/p/samsung/lh98qmfplgc/signage-displays-8806088606101-Samsung-QM98F-Digital-signage-flat-panel-98-LED-4K-Ultra-HD-Black-34524578.html

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\WebService\IceCat\Classes\ClicShoppingAdmin\IceCatPreview;
  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin;

  $CLICSHOPPING_IceCat = Registry::get('IceCat');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if (!Registry::exists('ProductsAdmin')) {
    Registry::set('ProductsAdmin', new ProductsAdmin());
  }

  $CLICSHOPPING_Products = Registry::get('ProductsAdmin');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $error = false;
  //EAN 8806088606101
  //EAN 4712900236903
  //sku PDA01E-00101KDU
  //$code = 'PDA01E-00101KDU';

  if (!empty($_POST['icecat_ean'])) {
    $code = str_replace(' ', '', $_POST['icecat_ean']);
    $code = HTML::sanitize($code); // ean or sku
  } else {
    $code = str_replace(' ', '', $_POST['icecat_sku']); // ean or sku
    $code = HTML::sanitize($code);
    $brand = HTML::sanitize($_POST['icecat_brand']);

    if (empty($brand)) {
      $error = true;
    }
  }

  if (empty($code)) {
    $error = true;
  }

  if (!empty($_POST['product_id'])) {
    $id = '&pID=' . HTML::sanitize($_POST['product_id']);
    $link = $CLICSHOPPING_IceCat->link('IceCat&Update');
  } else {
    $link = $CLICSHOPPING_IceCat->link('IceCat&Insert');
  }

  if (!empty($_POST['cPath'])) $cPath = HTML::sanitize($_POST['cPath']);

  if ($error === true) {
    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_IceCat->getDef('alert_error'), 'danger', 'Products');
    CLICSHOPPING::redirect(CLICSHOPPING::link(null, 'A&Catalog\Products&Edit&cPath=' . $cPath . $id));
  }

  $languages = $CLICSHOPPING_Language->getLanguages();

  echo HTML::form('save', $link);

  //status
  echo HTML::hiddenField('products_status', '0');
  echo HTML::hiddenField('products_view', '1');
  echo HTML::hiddenField('orders_view', '1');
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
  echo HTML::button($CLICSHOPPING_IceCat->getDef('button_create'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_IceCat->getDef('button_cancel'), null, CLICSHOPPING::link(null, 'A&Catalog\Products&Edit&cPath=' . $cPath . $id), 'warning');
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div style="padding-left: 10px;">

    <div class="col-md-12 mainTitle">
      <strong><?php echo $CLICSHOPPING_IceCat->getDef('text_info_new_export'); ?></strong></div>
    <div class="adminformTitle">
      <?php

        $CLICSHOPPING_IceCatByEan = new IceCatPreview($code, 'EN', $brand);


        echo '<strong>Common all languages </strong><br />';
        echo '<br /><strong>Product</strong><br />';
        // products
        $getSkuGtin = $CLICSHOPPING_IceCatByEan->getSkuGtin();
        echo HTML::hiddenField('products_model', $getSkuGtin);
        echo HTML::hiddenField('products_sku', $getSkuGtin);
        echo 'Product SKU  : ' . $getSkuGtin;
        echo '<br />';

        $getEANCode = $CLICSHOPPING_IceCatByEan->getEANCode();
        echo HTML::hiddenField('products_ean', $getEANCode);
        echo 'Product EAN  : ' . $getEANCode;
        echo '<br />';

        echo '<br /><strong>Manufacturer</strong><br />';

        if (isset($_POST['icecat_manufacturers_name'])) {
          $getSupplier = $CLICSHOPPING_IceCatByEan->getSupplier();
          echo HTML::hiddenField('manufacturers_name', $getSupplier);
          echo 'Brand : ' . $getSupplier;
        }

        if (isset($_POST['icecat_manufacturers_url'])) {
          echo '<br />';
          $getProductManufacturerUrl = $CLICSHOPPING_IceCatByEan->getProductManufacturerUrl();
          echo HTML::hiddenField('manufacturers_url', $getProductManufacturerUrl);
          echo 'Url Manufacturer : ' . $getProductManufacturerUrl;
          echo '<br />';
        }


        if (isset($_POST['icecat_logo_image'])) {
          echo '<br />';
          echo 'Certification<br />';
          $logoImage = $CLICSHOPPING_IceCatByEan->getLogoImage();
          if (!empty($logoImage)) {
            $logo_image = $logoImage . '<br />';
            echo '<br />' . $logoImage;
          }
        }

        if (isset($_POST['icecat_pdf_url'])) {
          echo '<br />';
          $pdfUrl = $CLICSHOPPING_IceCatByEan->getProductPdfUrl();
          if (!empty($pdfUrl)) {
            $pdf_url = $pdfUrl . '<br />';
            echo '<br />Url Pdf : ' . $pdfUrl;
          }
        }

        if (isset($_POST['icecat_pdf_manual'])) {
          echo '<br />';
          $pdfManual = $CLICSHOPPING_IceCatByEan->getProductManualPdfUrl();
          if (!empty($pdfManual)) {
            $pdf_manual = $pdfManual . '<br>';
            echo '<br />Pdf Manual Pdf : ' . $pdfManual;
          }
        }

        if (isset($_POST['icecat_warrantly'])) {
          echo '<br />';
          $WarrantlyDate = $CLICSHOPPING_IceCatByEan->getWarrantly();
          if (!empty($WarrantlyDate)) {
            $warrantly = $WarrantlyDate . '<br />';
            echo 'Warrantly : ' . $WarrantlyDate;
          }
        }

        if (isset($_POST['icecat_release_date'])) {
          echo '<br />';
          $releaseDate = $CLICSHOPPING_IceCatByEan->getReleaseDate();
          if (!empty($releaseDate)) {
            $release_date = $releaseDate . '<br />';
            echo 'Product Release Date : ' . $releaseDate;
          }
        }

        //**************************
        // Dimension
        //**************************
        $getProductDimensionWidth = $CLICSHOPPING_IceCatByEan->getProductDimension('width');
        $getProductDimensionHeight = $CLICSHOPPING_IceCatByEan->getProductDimension('height');
        $getProductDimensionDepth = $CLICSHOPPING_IceCatByEan->getProductDimension('depth');
        $getProductDimensionWeight = $CLICSHOPPING_IceCatByEan->getProductDimension('weight');
        $getProductDimensionTypeWidth = $CLICSHOPPING_IceCatByEan->getProductDimensionType('width');

        if (isset($_POST['icecat_dimension_width'])) echo HTML::hiddenField('products_dimension_width', $getProductDimensionWidth);
        if (isset($_POST['icecat_dimension_height'])) echo HTML::hiddenField('products_dimension_height', $getProductDimensionHeight);
        if (isset($_POST['icecat_dimension_depth'])) echo HTML::hiddenField('products_dimension_depth', $getProductDimensionDepth);
        if (isset($_POST['icecat_dimension_weight'])) echo HTML::hiddenField('products_weight', $getProductDimensionWeight);
        if (isset($_POST['icecat_dimension_type'])) echo HTML::hiddenField('products_dimension_type', $getProductDimensionTypeWidth);

        echo '<hr>';

        echo '<br /><strong>Dimensions</strong><br />';
        echo 'Width : ' . $getProductDimensionWidth . ' ' . $getProductDimensionTypeWidth;
        echo '<br />';
        echo 'Height : ' . $getProductDimensionHeight . ' ' . $getProductDimensionTypeWidth;
        echo '<br />';
        echo 'Depth : ' . $getProductDimensionDepth . ' ' . $getProductDimensionTypeWidth;
        echo '<br />';
        echo 'Dimension type : ' . $getProductDimensionTypeWidth;
        echo '<br />';
        echo 'Weight kg : ' . $getProductDimensionWeight;

        //**************************
        // image
        //**************************
        echo '<hr>';

        echo '<br /><strong>Product Images</strong><br />';
        $product_image = $CLICSHOPPING_IceCatByEan->getProductImageLowPic();
        echo HTML::hiddenField('icecat_products_image', $product_image);
        echo 'LowPic : ' . $product_image;
        echo '<br />';

        $product_image_medium_pic = $CLICSHOPPING_IceCatByEan->getProductImageMediumPic();
        echo HTML::hiddenField('icecat_products_image_medium', $product_image_medium_pic);
        echo 'MediumPic : ' . $product_image_medium_pic;
        echo '<br />';

        $product_image_high_pic = $CLICSHOPPING_IceCatByEan->getProductImageHighPic();
        echo HTML::hiddenField('icecat_products_image_zoom', $product_image_high_pic);
        echo 'HightPic : ' . $product_image_high_pic;
        echo '<br />';

        echo '<br /><strong>Product Images Gallery</strong><br />';
        $product_gallery = $CLICSHOPPING_IceCatByEan->getProductGallery();

        $i = 0;

        foreach ($product_gallery[0] as $image) {
          $gallery_image = $image->attributes();
          $gallery_image = $gallery_image['Pic'];

          echo HTML::hiddenField('image_gallery[' . $i . ']', $gallery_image);

          echo $gallery_image . '<br>';
          $i = $i + 1;
        }


        echo '<hr />';

        for ($i = 0, $n = count($languages); $i < $n; $i++) {
          $languages_code = strtoupper($languages[$i]['code']);
          $CLICSHOPPING_IceCatByEan = new IceCatPreview($code, $languages_code, $brand);

          echo '<br />';
          echo $CLICSHOPPING_Language->getImage($languages[$i]['code']);

//**************************
// Title / Categories / Summary description /
//**************************
          echo '<hr>';
// product name
          $product_name = $CLICSHOPPING_IceCatByEan->getProductTitle();
          echo '<strong>Product Name</strong> : ' . $product_name;
          echo HTML::hiddenField('products_name[' . $languages[$i]['id'] . ']', $product_name);
          echo '<br />';

// category name
          if (isset($_POST['icecat_categories_name'])) {
            $category_name = $CLICSHOPPING_IceCatByEan->getCategoryName();
            echo '<strong>Category Name : </strong>' . $category_name;
            echo HTML::hiddenField('categories_name[' . $languages[$i]['id'] . ']', $category_name);
          }
          echo '<br />';


          echo $CLICSHOPPING_Language->getImage($languages[$i]['code']);
//**************************
// Summary
//**************************
          echo '<hr>';
          echo '<br /><strong>Summary description & header tag</strong><br />';

          $summary_description_short = $CLICSHOPPING_IceCatByEan->getSummaryDescription('short');

// header_tag
          echo '<strong>Title Tag</strong> :' . $product_name;
          echo HTML::hiddenField('products_head_title_tag[' . $languages[$i]['id'] . ']', $product_name);
          echo '<br />';

          $getProductName = $CLICSHOPPING_IceCatByEan->getProductName();
          echo '<strong>Keywords Tag : </strong>' . $getProductName . ',' . $summary_description_short;

          $keywords = $getProductName . ',' . $summary_description_short;
          echo HTML::hiddenField('products_head_keywords_tag[' . $languages[$i]['id'] . ']', $keywords);
          echo '<br />';

// header_tag
          if (isset($_POST['icecat_summary_description'])) {
            echo '<strong>Description tag : </strong>' . $summary_description_short;
            echo HTML::hiddenField('products_head_desc_tag[' . $languages[$i]['id'] . ']', $summary_description_short);
            echo '<br />';

// product summary
            $summary_description_long = $CLICSHOPPING_IceCatByEan->getSummaryDescription('long');
            echo '<strong>Product description Summary : </strong>' . $summary_description_long;
            echo HTML::hiddenField('products_description_summary[' . $languages[$i]['id'] . ']', $summary_description_long);
            echo '<br />';
          }

//*************************
//  Certification
// ************************
          echo '<hr>';
          echo '<br /><strong>Others informations</strong><br />';

          echo $CLICSHOPPING_Language->getImage($languages[$i]['code']);

          if (isset($_POST['icecat_logo_image_description'])) {
            echo '<br />Certification description : <br />';
            $logoImageDescription = $CLICSHOPPING_IceCatByEan->getLogoImageDescription();
            $logo_image_description = $logoImageDescription . '<br /><br />';
            echo '<br />' . $logoImageDescription;
          }

//**************************
// Options
//**************************
          echo '<hr>';
          if (isset($_POST['icecat_product_option'])) {
            echo '<br /><strong>Caracteristics Options</strong><br />';
            $productOptions = $CLICSHOPPING_IceCatByEan->getProductOption();
            $product_options = $productOptions . '<br />';
            echo '<br />' . $product_options;
          }

//**************************
// Description
//**************************
          echo '<hr>';
          echo '<strong>Description</strong><br />';
          echo '<br />';
          $product_description = CLICSHOPPING::utf8Encode($CLICSHOPPING_IceCatByEan->getProductDescription());

          if (is_null($product_description) || empty($product_description)) {
            $product_description = $CLICSHOPPING_IceCatByEan->getSummaryDescription('long');
          }

          echo $product_description;
          $all_products_element = $product_description . '<br /><br />' . $release_date . $warrantly . $pdf_url . $pdf_manual . $logo_image . $logo_image_description . $product_options;

          echo HTML::hiddenField('products_description[' . $languages[$i]['id'] . ']', $all_products_element);
        }
      ?>
    </div>
  </div>
</div>
