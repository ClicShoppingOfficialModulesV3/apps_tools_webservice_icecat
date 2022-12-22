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

  namespace ClicShopping\Apps\WebService\IceCat\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\WebService\IceCat\IceCat as IceCatApp;

  class PageContentIceCat implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('IceCat')) {
        Registry::set('IceCat', new IceCatApp());
      }

      $this->app = Registry::get('IceCat');
    }

    protected function getProductsEan()
    {
      $Qproducts = $this->app->db->prepare('select products_ean
                                            from :table_products
                                            where products_id =  :products_id
                                            ');
      $Qproducts->bindInt(':products_id', $_GET['pID']);
      $Qproducts->execute();

      return $Qproducts->value('products_ean');
    }

    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!defined('CLICSHOPPING_APP_ICECAT_IC_STATUS') || CLICSHOPPING_APP_ICECAT_IC_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/page_content_icecat');

      if (CLICSHOPPING_APP_ICECAT_IC_STATUS == 'True' && !empty(CLICSHOPPING_APP_ICECAT_IC_LOGIN) && !empty(CLICSHOPPING_APP_ICECAT_IC_PASSWORD)) {

        $image = HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/icecat.png', $this->app->getDef('icon_icecat'));

        $content = '<a href="#" data-toggle="modal" data-refresh="true" data-target="#myModalIcecat">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/icecat.png', $this->app->getDef('text_icecat')) . '</a>';
        $content .= HTML::form('Icecat', $this->app->link('Preview'));
        $content .= HTML::hiddenField('product_id', $_GET['pID']);
        $content .= HTML::hiddenField('cPath', $_GET['cPath']);
        $content .= '<div class="modal fade" id="myModalIcecat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
        $content .= '<div class="modal-dialog" role="document">';
        $content .= '<div class="modal-content">';
        $content .= '<div class="modal-header">';
        $content .= '<h5 class="modal-title" id="exampleModalLabel">' . $this->app->getDef('text_title') . '</h5>';
        $content .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $content .= '<span aria-hidden="true">&times;</span>';
        $content .= '</button>';
        $content .= '</div>';
        $content .= '<div class="modal-body">';
        $content .= $this->app->getDef('text_information');
        $content .= '<div class="separator"></div>';
        $content .= $this->app->getDef('text_ean') . ' ' . HTML::inputField('icecat_ean', $this->getProductsEan());
        $content .= '<div class="row">';
        $content .= '<div class="col-md-6 ml-auto">' . $this->app->getDef('text_sku') . ' ' . HTML::inputField('icecat_sku') . '</div>';
        $content .= '<div class="col-md-6 ml-auto"> + ' . $this->app->getDef('text_brand') . ' ' . HTML::inputField('icecat_brand') . '</div><br />';
        $content .= '</div>';
        $content .= '<div class="separator"></div>';
        $content .= '<strong>' . $this->app->getDef('text_information_option') . '</strong>';
        $content .= '<table class="table table-sm table-hover">';
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_category_name') . ' ' . HTML::checkboxField('icecat_categories_name', 'yes', true) . '<br />';
        $content .= '</tr>';

//dimension
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_dimension_height') . ' ' . HTML::checkboxField('icecat_dimension_height', 'yes', true) . '</td>';
        $content .= '<td>' . $this->app->getDef('text_select_dimension_depth') . ' ' . HTML::checkboxField('icecat_dimension_depth', 'yes', true) . '</td>';
        $content .= '<td>' . $this->app->getDef('text_select_dimension_width') . ' ' . HTML::checkboxField('icecat_dimension_width', 'yes', true) . '</td>';
        $content .= '</tr>';

        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_dimension_weight') . ' ' . HTML::checkboxField('icecat_dimension_weight', 'yes', true) . '</td>';
        $content .= '<td>' . $this->app->getDef('text_select_dimension_type') . ' ' . HTML::checkboxField('icecat_dimension_type', 'yes', true) . '</td>';
        $content .= '</tr>';

// summary description
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_summary_description') . ' ' . HTML::checkboxField('icecat_summary_description', 'yes', true) . '</td>';
        $content .= '</tr>';

// Manufacturer
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_manufacturers') . ' ' . HTML::checkboxField('icecat_manufacturers_name', 'yes', true) . '</td>';
        $content .= '<td>' . $this->app->getDef('text_select_manufacturers_url') . ' ' . HTML::checkboxField('icecat_manufacturers_url', 'yes', true) . '</td>';
        $content .= '</tr>';
// description
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_release_date') . ' ' . HTML::checkboxField('icecat_release_date', 'yes', true) . '</td>';
        $content .= '<td>' . $this->app->getDef('text_select_warrantly') . ' ' . HTML::checkboxField('icecat_warrantly', 'yes', true) . '</td>';
        $content .= '</tr>';
// pdf
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_pdf_manual') . ' ' . HTML::checkboxField('icecat_pdf_manual', 'yes', true) . '</td>';
        $content .= '<td>' . $this->app->getDef('text_select_pdf_url') . ' ' . HTML::checkboxField('icecat_pdf_url', 'yes', true) . '</td>';
        $content .= '</tr>';
// product label
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_logo_image') . ' ' . HTML::checkboxField('icecat_logo_image', 'yes', true) . '</td>';
        $content .= '<td>' . $this->app->getDef('text_select_logo_image_description') . ' ' . HTML::checkboxField('icecat_logo_image_description', 'yes', true) . '</td>';
        $content .= '</tr>';
// options - features
        $content .= '<tr>';
        $content .= '<td>' . $this->app->getDef('text_select_product_option') . ' ' . HTML::checkboxField('icecat_product_option', 'yes', true) . '</td>';
        $content .= '</tr>';
        $content .= '<table>';


        $content .= '</div>';
        $content .= '<div class="modal-footer">';
        $content .= HTML::button($this->app->getDef('button_preview'), null, null, 'info', ['newwindow' => 'blank'], 'sm');
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</form>';

        $output = <<<EOD
<!-- ######################## -->
<!--  Start IceCatAppApp      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow3Sku').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End IceCatAppApp      -->
<!-- ######################## -->

EOD;
        return $output;
      }
    }
  }
