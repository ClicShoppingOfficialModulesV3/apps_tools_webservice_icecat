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

  namespace ClicShopping\Apps\WebService\IceCat\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Zip;
  use ClicShopping\OM\Cache;

  class IceCatFileImport
  {
    protected $context;
    protected $username;
    protected $password;
    protected $IceCatDirectory;

    public function __construct($code = null, $language, $brand = null)
    {
      $this->username = HTML::sanitize(CLICSHOPPING_APP_ICECAT_IC_LOGIN);
      $this->password = HTML::sanitize(CLICSHOPPING_APP_ICECAT_IC_PASSWORD);

      $this->code = HTML::sanitize($code); // ean or sku
      $this->brand = HTML::sanitize($brand);
      $this->lang = html::sanitize($language);
      $this->IceCatDirectory = CLICSHOPPING::BASE_DIR . 'Work/IceCat/';
    }

    /**
     * Check directory if exeist or not
     * @param
     * @return Boolean true / false
     * @access public
     */
    private function checkDirectoryIceCat()
    {

      if (!is_dir($this->IceCatDirectory)) {
        $dir = @mkdir($this->IceCatDirectory, 0777, true);

        if ($dir === true) {
          return true;
        } else {
          echo 'Impossible to create the directory, please try in manual';
        }
      } elseif (FileSystem::isWritable($this->IceCatDirectory)) {
        return true;
      } else {
        return false;
      }
    }


    public function selectFile()
    {

      if (isset($_POST['DailyCatalog'])) {
        $file = $this->IceCatDirectory . 'daily.index.xml.gz';
      } else {
        $file = $this->IceCatDirectory . 'files.index.xml.gz';
      }

      if (isset($_POST['FreeDailyCatalog'])) {
        $file = $this->IceCatDirectory . 'daily.index.xml.gz';
      } else {
        $file = $this->IceCatDirectory . 'files.index.xml.gz';
      }


      $file = $this->IceCatDirectory . 'daily.index.xml.gz';
      return $file;
    }


    /*
    * getIceCatFile
    * @string, $ean, ean of the product
    * @string, $language, language of the product (en, fr ....)
    * @return, $xml, all the data
    */
    public function getIceCatFile()
    {

      set_time_limit(0);

      $this->checkDirectoryIceCat();

      if (isset($_POST['DailyCatalog'])) {
        $url = 'https://data.icecat.biz/export/level4/' . $this->lang . '/daily.index.xml.gz';
      } else {
        $url = 'https://data.icecat.biz/export/level4/' . $this->lang . '/files.index.xml.gz';
      }

      if (isset($_POST['FreeDailyCatalog'])) {
        $url = 'https://data.Icecat.biz/export/freexml/' . $this->lang . '/daily.index.xml.gz';
      } else {
        $url = 'https://data.icecat.biz/export/freexml/' . $this->lang . '/files.index.xml.gz';
      }

      $url = 'https://data.icecat.biz/export/freexml/FR/daily.index.xml.gz';

      $localfile = $this->selectFile();

      if ($this->checkDirectoryIceCat() === true) {

        $localfile = fopen($localfile, 'wb'); // open with write enable
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $localfile);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600); # optional: -1 = unlimited, 3600 = 1 hour
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);

        curl_exec($ch);

        if (curl_errno($ch)) {
          echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);
        fclose($localfile);
      }
    }

    public function ExtractGzip()
    {
// Raising this value may increase performance
      $buffer_size = 4096; // read 4kb at a time
      $out_file_name = str_replace('.gz', '', $this->selectFile());

// Open our files (in binary mode)
      $file = gzopen($this->selectFile(), 'rb');
      $out_file = fopen($out_file_name, 'wb');

// Keep repeating until the end of the input file
      while (!gzeof($file)) {
// Read buffer-size bytes
// Both fwrite and gzread and binary-safe
        fwrite($out_file, gzread($file, $buffer_size));
      }

// Files are done, close files
      fclose($out_file);
      gzclose($file);
    }


    public function getIceCatFileContent()
    {
      if (is_file($this->selectFile())) {
        $data = file_get_contents($this->IceCatDirectory . '/daily.index.xml');

//        $xml = new \SimpleXMLElement($data);
        $xml = simplexml_load_string($data);

        return $xml;
      } else {
        return 'File does not exist : ' . $this->selectFile();
      }
    }


    public function getProdEanUpc($prod_id)
    {
      $xml = $this->getIceCatFileContent();

//      $eans = $xml->xpath("/Product/EANCode");

      $product = $xml->xpath("/ICECAT-interface/files.index/");
      // $product_attr       = $product[0]->attributes();
      $p['id'] = $product->Country_Markets;

      var_dump($p['id']);
      exit;

//      var_dump($xml);
//      exit;
      foreach ($eans as $ean) {
        $ean = $ean[0]['EAN'];
        var_dump($ean);
      }


      $product_name = $product[0]->attributes();
      $name = $product_name['Name'];


      $sku = $product_sku['EAN'];

      return $sku;
    }


    public function icecat_to_array($data = array())
    {
      // Extract data array
      extract($data);


      // Load into Simple XML Element
      $xml = $this->getIceCatFileContent();

      // Set xpaths
      $product = $xml->xpath("//Product");
      $product_attr = $product[0]->attributes();


      var_dump($product_attr);
      exit;


      // Does Icecat give errors?
      if ($product_attr['ErrorMessage']) {
        $errors[3] = (string)$product_attr['ErrorMessage'];
        goto the_end;
      }

      $category = $xml->xpath("/ICECAT-interface/Product/Category");

      $description = $xml->xpath("/ICECAT-interface/Product/ProductDescription");
      $description_attr = $description[0]->attributes();

      $supplier = $xml->xpath("/ICECAT-interface/Product/Supplier");
      $supplier_attr = $supplier[0]->attributes();

      $images = $xml->xpath("/ICECAT-interface/Product/ProductGallery");

      $eans = $xml->xpath("/ICECAT-interface/Product/EANCode");

      $featurelogo = $xml->xpath("/ICECAT-interface/Product/FeatureLogo");

      $spec_group = $xml->xpath("/ICECAT-interface/Product/CategoryFeatureGroup");
      $spec_item = $xml->xpath("/ICECAT-interface/Product/ProductFeature");

      $related = $xml->xpath("/ICECAT-interface/Product/ProductRelated");

      // Set product information
      $p['id'] = (int)$product_attr['ID'];
      $p['name'] = (string)$product_attr['Name'];
      $p['title'] = (string)$product_attr['Title'];
      $p['sku'] = (string)$product_attr['Prod_id'];
      $p['release'] = (string)$product_attr['ReleaseDate'];
      $p['img_thumb'] = (string)$product_attr['ThumbPic'];
      $p['img_small'] = (string)$product_attr['LowPic'];
      $p['img_mid'] = (string)$product_attr['Pic500x500'];
      $p['img_high'] = (string)$product_attr['HighPic'];
      $p['pdf_spec'] = (string)$description_attr['PDFURL'];
      $p['pdf_manual'] = (string)$description_attr['ManualPDFURL'];
      $p['descr_long'] = str_replace('\n', '<br />', (string)$description_attr['LongDesc']);
      $p['descr_short'] = (string)$description_attr['ShortDesc'];
      $p['url'] = (string)$description_attr['URL'];
      $p['warrenty'] = (string)$description_attr['WarrantyInfo'];
      $p['category'] = (string)$category[0]->Name[0]['Value'];
      $p['category_id'] = (int)$category[0]['ID'];

      // Set brand
      $p['brand_id'] = (int)$supplier_attr['ID'];
      $p['brand_name'] = (string)$supplier_attr['Name'];

      // Set images
      foreach ($images[0] as $image) {
        $image_attr = $image->attributes();
        $p['image'][(int)$image_attr['ProductPicture_ID']]['thumb'] = (string)$image_attr['ThumbPic'];
        $p['image'][(int)$image_attr['ProductPicture_ID']]['small'] = (string)$image_attr['LowPic'];
        $p['image'][(int)$image_attr['ProductPicture_ID']]['mid'] = (string)$image_attr['Pic500x500'];
        $p['image'][(int)$image_attr['ProductPicture_ID']]['high'] = (string)$image_attr['Pic'];
      }

      // Set EAN numbers
      foreach ($eans as $ean) {
        $p['ean'][] = (string)$ean[0]['EAN'];
      }

      // Set featurelogos
      foreach ($featurelogo as $logo) {
        $logo_attr = $logo->attributes();
        $p['featurelogo'][(int)$logo_attr['Feature_ID']]['image'] = (string)$logo_attr['LogoPic'];
        $p['featurelogo'][(int)$logo_attr['Feature_ID']]['descr'] = trim((string)$logo->Descriptions->Description);
      }

      // Set specification groups
      foreach ($spec_group as $group) {
        $p['spec'][(int)$group[0]['ID']]['name'] = (string)$group->FeatureGroup->Name[0]['Value'];
      }

      // Set specifications
      foreach ($spec_item as $item) {
        if ($item[0]['Value'] != 'Icecat.biz') {
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['name'] = (string)$item->Feature->Name[0]['Value'];
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['value'] = (string)$item[0]['Value'];
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['sign'] = (string)$item->Feature->Measure->Signs->Sign;
          $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['pres_value'] = (string)$item[0]['Presentation_Value'];
        }
      }

      // Remove empty specification groups
      foreach ($p['spec'] as $key => $value) {
        if (!isset($value['features'])) {
          unset($p['spec'][$key]);
        }
      }

      // Related products
      foreach ($related as $test) {
        $p['related'][(int)$test->Product[0]['ID']]['name'] = (string)$test->Product[0]['Name'];
        $p['related'][(int)$test->Product[0]['ID']]['category'] = (int)$test[0]['Category_ID'];
        $p['related'][(int)$test->Product[0]['ID']]['sku'] = (string)$test->Product[0]['Prod_id'];
        $p['related'][(int)$test->Product[0]['ID']]['img'] = (string)$test->Product[0]['ThumbPic'];
        $p['related'][(int)$test->Product[0]['ID']]['brand'] = (string)$test->Product->Supplier[0]['Name'];
        $p['related'][(int)$test->Product[0]['ID']]['brand_id'] = (string)$test->Product->Supplier[0]['ID'];
      }

      the_end:

      // Return errors if set, else product information
      if (isset($errors)) {
        return $errors;
      } else {
        return $p;
      }
    }


    public function read()
    {
      if (null === $this->getIceCatFileContent()) {
        // for example purpose, we should use XML Parser to read line per line
        $this->xml = simplexml_load_file($this->filePath, 'SimpleXMLIterator');
        $this->xml->rewind();
      }

      if ($data = $this->xml->current()) {
        $item = [];
        foreach ($data->attributes() as $attributeName => $attributeValue) {
          $item[$attributeName] = (string)$attributeValue;
        }
        $this->xml->next();

        return $item;
      }

      return null;
    }

  }