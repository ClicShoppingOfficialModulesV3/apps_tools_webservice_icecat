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

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class PriceScrapping
  {
    protected $error;

    public function __construct()
    {
      require_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/simplehtmldom_1_5/simple_html_dom.php');

      $this->simpleDom = new simple_html_dom_node();
    }

    /**
     * @param $url
     * @return bool
     */
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
    }

    /*
    *  Return all product price
    * @string : $url, listing product url
    * @string : $argument_title : paramters about title search
    * @string : $argument_title : css parameters about title search
    * @string : $parameters_price : css paramters about price search
    * @return : $result array about tile and price
    * help information :http://simplehtmldom.sourceforge.net/manual.htm
    */
    public function getProductPrice($url, $start, $end)
    {

      $html = $this->getHTML($url);

      if ($this->error === false) {
        $pos = stripos($html, $start);
        $str = substr($html, $pos);

        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);

        $str_three = substr($str_two, 0, $second_pos);

        $price = trim($str_three); // remove whitespaces

        preg_match_all('!\d+!', $price, $matches);
        $price_extracted = (float)implode('.', $matches[0]);
      } else {
        $price_extracted = 'Error No Content';
      }

      $html->clear();
      unset($html);

      return $price_extracted;
    }


    /*
    *  Return all Title and Price about an URL
    * @string : $url, listing product url
    * @string : $argument_title : paramters about title search
    * @string : $argument_title : css parameters about title search
    * @string : $parameters_price : css paramters about price search
    * @return : $result array about tile and price
    * help information :http://simplehtmldom.sourceforge.net/manual.htm
     * https://github.com/samacs/simple_html_dom
     * a[class="postlink"]
     * a[href*="phpbb.com"]
     * div#content
     *
    */

    public function getProductListingPrice($url, $parameters_title = null, $parameters_price = null, $keywords = null)
    {
      /*
           if (is_null($keywords)) {
             return false;
           }
     */
      $html = $this->getHTML($url);

      $content = $this->simpleDom->str_get_html($html);

      $i = 0;

      foreach ($content as $price) {
        $item['title'] = html_entity_decode($content->find($parameters_title, $i)->plaintext);

        $normal_price = $content->find($parameters_price, $i)->plaintext;
        preg_match_all('!\d+!', $normal_price, $matches);
        $price_extracted = (float)implode('.', $matches[0]);
        $item['normal_price'] = $price_extracted;

        if ($item['normal_price'] != '' || !is_null($item['normal_price'])) {
          $result[] = $item;
        }

        $i = $i + 1;
      }

      $html->clear();
      unset($html);

      return $result;
    }
  }