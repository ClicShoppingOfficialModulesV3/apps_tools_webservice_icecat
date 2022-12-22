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

//https://royduineveld.nl/icecat-xml-verwerken/
//https://royduineveld.nl/icecat-xml-verwerken-deel-2-de-array-weergeven/
//selecthttp://data.icecat.biz/xml_s3/xml_server3.cgi?ean_upc=0013803146813;lang=en;output=productxml

  namespace ClicShopping\Apps\WebService\IceCat\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class IceCatPreview
  {
    protected $context;
    protected $username;
    protected $password;

    public function __construct($code, $language, $brand = null)
    {
      $this->username = HTML::sanitize(CLICSHOPPING_APP_ICECAT_IC_LOGIN);
      $this->password = HTML::sanitize(CLICSHOPPING_APP_ICECAT_IC_PASSWORD);

      $this->code = HTML::sanitize($code); // ean or sku
      $this->brand = HTML::sanitize($brand);
      $this->lang = html::sanitize($language);
    }

    /*
    *  API search Content
    * @string, $ean, ean of the product
    * @string, $language, language of the product (en, fr ....)
    * @return, $xml, all the data
    */
    private function getSearchProductEanXML()
    {

      set_time_limit(0);

      if (is_null($this->code)) {
        return 0;
      }

      if (!empty($this->code)) {
        if (!empty($this->brand)) {
          $url = 'http://data.Icecat.biz/xml_s3/xml_server3.cgi?prod_id=' . urlencode($this->code) . ';vendor=' . $this->brand . ';lang=' . $this->lang . ';output=productxml';
        } else {
          $url = 'http://data.icecat.biz/xml_s3/xml_server3.cgi?ean_upc=' . urlencode($this->code) . ';lang=' . $this->lang . ';output=productxml';
        }
      }


      $context = stream_context_create(array('http' => array('header' => "Authorization: Basic " . base64_encode($this->username . ":" . $this->password))));

      $data = file_get_contents($url, false, $context);

//      $xml = new \SimpleXMLElement($data);
      $xml = simplexml_load_string($data);

      if ($xml->Product->attributes()->Code == -1) {
        echo $xml->Product->attributes()->ErrorMessage;
        echo '<br /> You must verificate your elements inserted. Try again.';
        exit;
      }

      return $xml;
    }

//************************************************
// EAN
//************************************************


    /*
    * EAN - GTIN  of the product
    * @return $sku, items with array
    */
    public function getEANCode()
    {
      $xml = $this->getSearchProductEanXML();
      $product = $xml->xpath("//EANCode");

      $product_sku = $product[0]->attributes();
      $sku = $product_sku['EAN'];

      return $sku;
    }

//************************************************
// Product
//************************************************

    /*
    * Name of the product name
    * @return $name, items with array
    */
    public function getProductName()
    {
      $xml = $this->getSearchProductEanXML();

      $product = $xml->xpath("//Product");

      $product_name = $product[0]->attributes();
      $name = $product_name['Name'];

      return $name;
    }

    /*
    * tile of the product title
    * @return $title, items with array
    */
    public function getProductTitle()
    {
      $xml = $this->getSearchProductEanXML();

      $product = $xml->xpath("//Product");

      $product_title = $product[0]->attributes();
      $title = $product_title['Title'];

      return $title;
    }

    /*
    * Release of the product
    * @return $release, items with array
    */
    public function getReleaseDate()
    {
      $xml = $this->getSearchProductEanXML();

      $product = $xml->xpath("//Product");

      $product_release = $product[0]->attributes();
      $release = $product_release['ReleaseDate'];

      return $release;
    }

    /*
    * SKU - GTIN  of the product
    * @return $sku, items with array
    */
    public function getSkuGtin()
    {
      $xml = $this->getSearchProductEanXML();
      $product = $xml->xpath("//Product");

      $product_sku = $product[0]->attributes();
      $sku = $product_sku['Prod_id'];

      return $sku;
    }


    /*
    * Images of the product small image
    * @return $image, items with array
    */
    public function getProductImageLowPic()
    {
      $xml = $this->getSearchProductEanXML();
      $product = $xml->xpath("//Product");

      $product_image = $product[0]->attributes();
      $image = $product_image['LowPic'];

      return $image;
    }

    /*
    * Images of the product medium image
    * @return $image, items with array
    */
    public function getProductImageMediumPic()
    {
      $xml = $this->getSearchProductEanXML();
      $product = $xml->xpath("//Product");

      $product_image = $product[0]->attributes();
      $image = $product_image['Pic500x500'];

      return $image;
    }

    /*
    * Images of the product big image
    * @return $image, items with array
    */
    public function getProductImageHighPic()
    {
      $xml = $this->getSearchProductEanXML();
      $product = $xml->xpath("//Product");

      $product_image = $product[0]->attributes();
      $image = $product_image['HighPic'];

      return $image;
    }



//************************************************
// Product description
//************************************************

//IcecatId


    /*
    * Description of the product
    * @return $description, items with array
    */
    public function getProductDescription()
    {
      $xml = $this->getSearchProductEanXML();
      $description = $xml->xpath("//ProductDescription");

      $product_description = $description[0]->attributes();
      $product_description = $product_description['LongDesc'];
      $product_description = utf8_decode(str_replace('\n', '<br /> ', $product_description));
      $product_description = str_replace('<b>', '<strong>', $product_description);
      $product_description = str_replace('<B>', '<strong>', $product_description);
      $product_description = str_replace('</b>', '</strong>', $product_description);
      $product_description = str_replace('</B>', '<strong>', $product_description);
      $product_description = str_replace('<br>', '<br />', $product_description);

      return $product_description;
    }

    /*
    * Warrently of the product
    * @return $warranty_info, items with array
    */
    public function getWarrantly()
    {
      $xml = $this->getSearchProductEanXML();
      $description = $xml->xpath("//ProductDescription");

      $product_warranty_info = $description[0]->attributes();

      $warranty_info = $product_warranty_info['WarrantyInfo'];

      return $warranty_info;
    }

    /*
    * Manufacturer url of the product
    * @return $description, items with array
    */
    public function getProductManufacturerUrl()
    {
      $xml = $this->getSearchProductEanXML();
      $description = $xml->xpath("//ProductDescription");

      $manufacturer_url = $description[0]->attributes();

      $url = $manufacturer_url['URL'];

      return $url;
    }

    /*
    * Product PDF URL
    * @return $pdf_url, items with array
    */
    public function getProductPdfUrl()
    {
      $xml = $this->getSearchProductEanXML();
      $description = $xml->xpath("//ProductDescription");

      $product_pdf_url = $description[0]->attributes();

      $pdf_url = $product_pdf_url['PDFURL'];

      return $pdf_url;
    }

    /*
    * Product PDF URL
    * @return $pdf_url, items with array
    */
    public function getProductManualPdfUrl()
    {
      $xml = $this->getSearchProductEanXML();
      $description = $xml->xpath("//ProductDescription");

      $product_pdf_url = $description[0]->attributes();

      $pdf_url = $product_pdf_url['ManualPDFURL'];

      return $pdf_url;
    }




//************************************************
// Product Feature Logo
//************************************************

    /*
    * Logo Image of the product like energystar
    * @return $logo_image, items with array
    */
    public function getLogoImage()
    {
      $xml = $this->getSearchProductEanXML();
      $featurelogo = $xml->xpath("//FeatureLogo");

      foreach ($featurelogo as $logo) {
        $logo = $logo->attributes();
        $logo_image = '<a href="' . $logo['LogoPic'] . '" target="_blank" rel="noopener"><img src="' . $logo['LogoPic'] . '" alt="' . $this->getProductTitle() . '" title="' . $this->getProductTitle() . '"></a>"';
      }

      return $logo_image;
    }

    /*
    * Description of Logo Image of the product like energystar
    * @return $logo_description, items with array
    */
    public function getLogoImageDescription()
    {
      $xml = $this->getSearchProductEanXML();
      $feature_logo = $xml->xpath("//FeatureLogo");

      foreach ($feature_logo as $item) {
        $logo_description = $item->Descriptions->Description;
      }

      return $logo_description;
    }



//************************************************
// Product Summary Description
//************************************************

    /*
     * Summary of the product
     * @string $string = default short else long
     * @return $summary_description, the summary description
     */
    public function getSummaryDescription($string = 'short')
    {
      $xml = $this->getSearchProductEanXML();
      $SummaryDescription = $xml->xpath("//SummaryDescription");

      if ($string == 'short') {
        $summary_description = $SummaryDescription[0]->ShortSummaryDescription;
      } else {
        $summary_description = $SummaryDescription[0]->LongSummaryDescription;
      }
      return $summary_description;
    }



//************************************************
// Product Supplier
//************************************************

    /*
     * Supplier Name
     * @string $string = default short else long
     * @return $summary_description, the summary description
     */
    public function getSupplier()
    {
      $xml = $this->getSearchProductEanXML();
      $Supplier = $xml->xpath("//Supplier");

      foreach ($Supplier as $item) {
        $name = $item->attributes();
        $name = $name['Name'];
      }

      return $name;
    }


//************************************************
// Product Category
//************************************************

    /*
    * Category Name
    * @return $image, items with array
    */
    public function getCategoryName()
    {
      $xml = $this->getSearchProductEanXML();
      $category_name = $xml->xpath("//Category");

      foreach ($category_name as $item) {
//      $category = $item->attributes();
        $titleXML = new \SimpleXMLElement($item->asXML());
        $category_name = $titleXML->xpath("//Name");
        $category_name = $category_name[0]->attributes();
        $category_name = $category_name['Value'];
      }

      return $category_name;
    }


//************************************************
// Product Options
//************************************************


    /*
    * Product Option Name
    * @return $image, items with array
    */
    public function getProductOption()
    {
      $xml = $this->getSearchProductEanXML();

      $spec_group = $xml->xpath("//CategoryFeatureGroup");
      $spec_item = $xml->xpath("//ProductFeature");

// Set specification groups
      foreach ($spec_group as $group) {
        $p['spec'][(int)$group[0]['ID']]['name'] = (string)$group->FeatureGroup->Name[0]['Value'];
      }

// Set specifications
      foreach ($spec_item as $item) {
        if ($item[0]['Value'] != 'Icecat.biz') {
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['name'] = $item->Feature->Name[0]['Value'];
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['value'] = $item[0]['Value'];
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['sign'] = $item->Feature->Measure->Signs->Sign;
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['pres_value'] = $item[0]['Presentation_Value'];
        }
      }

      // Remove empty specification groups
      foreach ($p['spec'] as $key => $value) {
        if (!isset($value['features'])) {
          unset($p['spec'][$key]);
        }
      }

      $output = '<table class="table table-sm table-hover IceCatOptionTable">';
      $output .= '<thead>';
      $output .= '</thead>';
      $output .= '<tbody>';

      foreach ($p['spec'] as $id => $s) {
        $output .= '<tr>';
        $output .= '<td class="IceCatOptionTitle" colspan="2"><strong>' . $s['name'] . '</strong><br/></td>';

        foreach ($s['features'] as $id => $f) {
          $output .= '<tr>';
          $output .= '<td class="IceCatOptionContent" width="5%"></td>';
          $output .= '<td class="IceCatOptionContent">' . $f['name'] . ' : ' . $f['pres_value'] . '</td>';
          $output .= '</tr>';
        }

        $output .= '</tr>';
      }

      $output .= '</tbody>';
      $output .= '</table>';

      return $output;
    }

    /*
    * getProductDimensionType : type of the product dimension based on width
    * @return $sign, value of the dimension type
    */
    public function getProductDimensionType()
    {
      $xml = $this->getSearchProductEanXML();
      $product_feature = $xml->xpath("//ProductFeature");

      foreach ($product_feature as $item) {
        if ($item[0]['Value'] != 'Icecat.biz') {
          $name = $item->Feature->Name[0]['Value'];

          if (strtolower($name) == 'width') {
            $value = $item->Feature->Measure->Signs->Sign;
          }
        }
      }

      return $value;
    }


    /*
    * getProductDimension : product dimension
    * string : $specificationName : height, width, depthn weight
    * @return $value, value of the dimension
    */

    public function getProductDimension($specificationName)
    {
      $xml = $this->getSearchProductEanXML();
      $product_feature = $xml->xpath("//ProductFeature");

      foreach ($product_feature as $item) {
        if ($item[0]['Value'] != 'Icecat.biz') {
          $name = $item->Feature->Name[0]['Value'];
          if (strtolower($name) == strtolower($specificationName)) {
            if (strtolower($specificationName) == 'weight') {
              $value = round($item[0]['Value'] / 1000, 2);
            } else {
              $value = $item[0]['Value'];
            }
          }
        }
      }

      return $value;
    }


    /*
    * getProductDimension : product dimension
    * string : $specificationName : height, width, depth
    * @return $value, value of the dimension
    */

    public function getProductWeight()
    {
      $xml = $this->getSearchProductEanXML();
      $product_feature = $xml->xpath("//ProductFeature");

      foreach ($product_feature as $item) {
        if ($item[0]['Value'] != 'Icecat.biz') {
          $name = $item->Feature->Name[0]['Value'];
//var_dump($name) . '<br>';
          if (strtolower($name) == strtolower('weight')) {
            $value = $item[0]['Value'];
          }
        }
      }

      return $value;
    }

    /*
    * getDebugProductDimension : product dimension
    * string : $specificationName : height, width, depth
    * @return a value selected
    */
    public function getDebugProductDimension()
    {
      $xml = $this->getSearchProductEanXML();
      $product_feature = $xml->xpath("//ProductFeature");

      foreach ($product_feature as $item) {
        if ($item[0]['Value'] != 'Icecat.biz') {
//          $name = $item->Feature->Name[0]['Value'];
//          $value = $item[0]['Value'];
//          $presentation = $item[0]['Presentation_Value'];

          $sign = $item->Feature->Measure->Signs->Sign;

          echo $sign . '<br>';
        }
      }
    }


    /*
    * Product Gallery
    * string :
    * @return an array about gallery image
    */
    public function getProductGallery()
    {
      $xml = $this->getSearchProductEanXML();
      $product_gallery = $xml->xpath("//ProductGallery");

      return $product_gallery;
    }
  }
