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

  /**
   * Created by PhpStorm.
   * User: oitsuki
   * Date: 21/11/17
   * Time: 08:55
   */

  namespace ClicShopping\Apps\WebService\IceCat\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class PriceScrappingDimDom
  {

    protected $error;
    protected $query;
    protected $document;
    protected $element;

    public function __construct()
    {
      require_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/DimDom/vendor/autoload.php');
      $this->document = new \DiDom\Document();
      $this->query = new \DiDom\Query();
      $this->errors = new \DiDom\Errors();
    }


    private function getHTML($url)
    {

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 10.10; labnol;) ctrlq.org");
      curl_setopt($curl, CURLOPT_FAILONERROR, true);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $html = curl_exec($curl);
      curl_close($curl);

      if (is_null($html) || $html === false) {
        $html = file_get_contents($url);
        $this->error = false;

        if (is_null($html)) {
          print_r('error no content');
          $this->error = true;
          return false;
        }
      }

      return $html;
    }

    /*
    *  Return all Title and Price about an URL
    * @string : $url, listing product url
    * @string : $argument_title : paramters about title search
    * @string : $parameters_price : css paramters about price search
    * @return : $result array about tile and price
    * help information :http://simplehtmldom.sourceforge.net/manual.htm
     * https://github.com/samacs/simple_html_dom
     * a[class="postlink"]
     * a[href*="phpbb.com"]
     * div#content
     *
    */

    public function getProductListingPrice($url = null, $parameters_content = null, $parameters_title = null, $parameters_price = null)
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (is_null($url) || is_null($parameters_title) || is_null($parameters_price)) {
        return false;
      }

      $html = $this->getHTML($url);
      $element = $this->document->loadHtml($html);

      if (!is_null($parameters_content)) {
        $content = $element->first($parameters_content); // content of the div
        $price = $content->find($parameters_price); // price content inside the div
        $title = $content->find($parameters_title); // tile inside the div
      } else {
        $price = $element->find($parameters_price); // price content inside the div
        $title = $element->find($parameters_title); // tile inside the div
      }

      $i = 0;

      if (!empty($price) && !empty($title)) {
        foreach ($price as $value) {
          $product_price[$i] = $value->text();

          preg_match_all('!\d+!', preg_replace('/\s/', '', $product_price[$i]), $matches);
          $price_extracted = (float)implode('.', $matches[0]);
          $item['normal_price'] = $price_extracted;

          $price_result[] = $item['normal_price'];
          $i = $i + 1;
        }

        foreach ($title as $value) {
          $product_title[$i] = html_entity_decode($value->text());
          $title_result[] = $product_title[$i];
          $i = $i + 1;
        }

        $result = array_map(null, $title_result, $price_result);

        return $result;
      } else {

        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_price_title_css'), 'error', 'scrapping');
//        $result = '<div class="alert alert warning">CSS Price or Title has a problem. Please Check.<br />Note : Take becarefull at your synthax and all website does\'nt allow to scrap their prices</div>';
      }
    }
  }