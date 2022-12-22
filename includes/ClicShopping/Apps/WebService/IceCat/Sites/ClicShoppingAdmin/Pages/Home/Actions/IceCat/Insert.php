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


  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $cPath;
    protected $manufacturers_id;
    protected $template;
    protected $categories_id;

    public function __construct()
    {
      if (!Registry::exists('IceCat')) {
        Registry::set('IceCat', new IceCatApp());
      }

      $this->app = Registry::get('IceCat');
      $this->template = Registry::get('TemplateAdmin');

      $this->manufacturers_name = HTML::sanitize($_POST['manufacturers_name']);
      $this->manufacturers_url = HTML::sanitize($_POST['manufacturers_url']);

      if (!Registry::exists('ProductsAdmin')) {
        Registry::set('ProductsAdmin', new ProductsAdmin());
      }

      $this->productsAdmin = Registry::get('ProductsAdmin');
    }

    private function getManufacturers()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!is_null($this->manufacturers_name)) {
        $QManufacturer_name = $this->app->db->prepare('select manufacturers_id,
                                                              manufacturers_name
                                                        from :table_manufacturers
                                                        where manufacturers_name = :manufacturers_name
                                                      ');

        $QManufacturer_name->bindValue(':manufacturers_name', $this->manufacturers_name);
        $QManufacturer_name->execute();

        if ($QManufacturer_name->value('manufacturers_name') == $this->manufacturers_name) {
          $this->manufacturers_id = $QManufacturer_name->value('manufacturers_id');
        } else {
          $sql_data_array = ['manufacturers_name' => $this->manufacturers_name];
          $insert_sql_data = ['date_added' => 'now()'];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          $this->app->db->save('manufacturers', $sql_data_array);

          $this->manufacturers_id = $this->app->db->lastInsertId();

          $languages = $CLICSHOPPING_Language->getLanguages();

          for ($i = 0, $n = count($languages); $i < $n; $i++) {
            $manufacturers_url_array = $_POST['manufacturers_url'];
            $language_id = $languages[$i]['id'];

            $sql_data_array = ['manufacturers_id' => $this->manufacturers_id];

            $insert_sql_data = ['manufacturers_url' => HTML::sanitize($manufacturers_url_array[$language_id]),
              'languages_id' => (int)$language_id,
            ];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $this->app->db->save('manufacturers_info', $sql_data_array);
          }
        }
      }
    }

    public function getProductImage()
    {
      $icecat_products_image = HTML::sanitize($_POST['icecat_products_image']);
      $product_image = pathinfo($icecat_products_image)['basename'];

      if (!is_null($icecat_products_image)) {
        $content = @file_get_contents($icecat_products_image);
        file_put_contents($this->template->getDirectoryPathTemplateShopImages() . 'products/' . $product_image, $content);
      }

      $product_image = 'products/' . $product_image;

      return $product_image;
    }

    public function getProductMediumImage()
    {
      $icecat_products_image_medium = HTML::sanitize($_POST['icecat_products_image_medium']);
      $product_image_medium = pathinfo($icecat_products_image_medium)['basename'];

      if (!is_null($icecat_products_image_medium)) {
        $content = @file_get_contents($icecat_products_image_medium);
        file_put_contents($this->template->getDirectoryPathTemplateShopImages() . 'products/' . $product_image_medium, $content);
      }

      $product_image = 'products/' . $product_image_medium;

      return $product_image;
    }

    public function getProductZoomImage()
    {
      $icecat_products_image_zoom = HTML::sanitize($_POST['icecat_products_image_zoom']);
      $product_image_zoom = pathinfo($icecat_products_image_zoom)['basename'];

      if (!is_null($icecat_products_image_zoom)) {
        $content = @file_get_contents($icecat_products_image_zoom);
        file_put_contents($this->template->getDirectoryPathTemplateShopImages() . 'products/' . $product_image_zoom, $content);
      }

      $product_image = 'products/' . $product_image_zoom;

      return $product_image;
    }


    public function SaveGallery()
    {
      $images = $_POST['image_gallery'];

      for ($i = 0, $n = count($images); $i < $n; $i++) {
        $images[$i] = HTML::sanitize($images[$i]);

        $image = pathinfo($images[$i])['basename'];

        if (!is_null($images[$i])) {
          $content = @file_get_contents($images[$i]);
          file_put_contents($this->template->getDirectoryPathTemplateShopImages() . 'products/' . $image, $content);
        }

        $product_image = 'products/' . $image;

        $sql_data_array = ['products_id' => $this->products_id,
          'image' => $product_image,
          'sort_order' => 0
        ];

        $this->app->db->save('products_images', $sql_data_array);
      }
    }

    private function getCategories()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      foreach ($_POST['categories_name'] as $categories_name) {
        $QCategory_name = $this->app->db->prepare('select categories_id,
                                                          categories_name
                                                    from :table_categories_description
                                                    where categories_name like :categories_name
                                                    limit 1
                                                  ');

        $QCategory_name->bindValue(':categories_name', '%' . $categories_name . '%');
        $QCategory_name->execute();

        if ($QCategory_name->value('categories_name') == $categories_name) {
          $this->categories_id = $QCategory_name->valueInt('categories_id');
        }
      }

      IF (is_null($this->categories_id)) {
        $sql_data_array = ['sort_order' => 0];

        $insert_sql_data = ['parent_id' => 0,
          'date_added' => 'now()',
          'virtual_categories' => 0
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('categories', $sql_data_array);

        $categories_id = $this->app->db->lastInsertId();

        $i = 1;
        $languages = $CLICSHOPPING_Language->getLanguages();
        $count_languages_id = count($languages);

        foreach ($_POST['categories_name'] as $item) {
          if ($i <= $count_languages_id) {
            $sql_data_array = ['categories_name' => HTML::sanitize($item)];

            $insert_sql_data = ['categories_id' => $categories_id,
              'language_id' => $i
            ];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $this->app->db->save('categories_description', $sql_data_array);

            $i = $i + 1;
          }
        }
      }
    }

    protected function SaveProductsDescription()
    {
      $id = null;

      $_POST['products_image'] = $this->getProductImage();
      $_POST['products_image_medium'] = $this->getProductMediumImage();
      $_POST['products_image_zoom'] = $this->getProductZoomImage();

      $this->productsAdmin->save($id, 'Insert');
    }

    protected function UpdateProductsToCategories()
    {

      $Qproducts = $this->app->db->prepare('select products_id
                                            from :table_products
                                            order by products_id DESC
                                            limit 1
                                          ');
      $Qproducts->execute();
      $this->products_id = $Qproducts->valueInt('products_id');

      if (!is_null($this->products_id)) {
        $Qupdate = $this->app->db->prepare('update :table_products_to_categories
                                            set categories_id = :categories_id
                                            where products_id = :products_id
                                          ');
        $Qupdate->bindInt(':products_id', (int)$this->products_id);
        $Qupdate->bindInt(':categories_id', (int)$this->categories_id);
        $Qupdate->execute();
      }
    }


    protected function saveProductsGroup()
    {

      $QcustomersGroup = $this->app->db->prepare('select distinct customers_group_id
                                                  from :table_customers_groups
                                                ');
      $QcustomersGroup->execute();
      $customer_group = $QcustomersGroup->fetchAll();

      foreach ($customer_group as $customers_groups_id) {
        $this->app->db->save('products_groups', [
            'customers_group_id' => (int)$customers_groups_id['customers_group_id'],
            'customers_group_price' => 0,
            'products_id' => (int)$this->products_id,
            'products_price' => 0,
            'price_group_view' => 0,
            'products_group_view' => 0,
            'orders_group_view' => 1,
            'products_quantity_unit_id_group' => 0,
            'products_model_group' => null,
            'products_quantity_fixed_group' => 1,
          ]
        );
      }
    }


    public function execute()
    {
      $this->getCategories();
      $_POST['move_to_category_id'] = (int)$this->categories_id;

      $this->getManufacturers();
      $_POST['manufacturers_id'] = (int)$this->manufacturers_id;

// always after move_to_category_id;
      $this->SaveProductsDescription();
      $this->UpdateProductsToCategories();

      $this->SaveGallery();
      $this->saveProductsGroup();

      CLICSHOPPING::redirect(null, 'A&Catalog\Products&Products&cPath=' . $this->categories_id . '&cID=');
    }
  }