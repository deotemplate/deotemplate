<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class DeoWidgetProductlist extends DeoWidgetBaseModel
{
    public $name = 'product_list';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Product List'), 'explain' => $this->l('Create Products List'));
    }

    public function renderForm($args, $data)
    {
        $helper = $this->getFormHelper();
        $types = array(
            array(
                'value' => 'newest',
                'text' => $this->l('New Product')
            ),
            array(
                'value' => 'bestseller',
                'text' => $this->l('Bestseller')
            ),
            array(
                'value' => 'special',
                'text' => $this->l('Special')
            ),
        );


        $types[] = array(
            'value' => 'featured',
            'text' => $this->l('Home Featured')
        );

        $source = array(
            array(
                'value' => 'pcategories', // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Category')    // The value of the text content of the  <option> tag.
            ),
            array(
                'value' => 'ptype',
                'name' => $this->l('Product')
            ),
            array(
                'value' => 'pmanufacturers',
                'name' => $this->l('Manufacturers')
            ),
            array(
                'value' => 'pproductids',
                'name' => $this->l('Product ID')
        ));

        $orderby = array(
            array(
                'order' => 'date_add', // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Date Add')    // The value of the text content of the  <option> tag.
            ),
            array(
                'order' => 'date_upd', // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Date Update')    // The value of the text content of the  <option> tag.
            ),
            array(
                'order' => 'name',
                'name' => $this->l('Name')
            ),
            array(
                'order' => 'id_product',
                'name' => $this->l('Product Id')
            ),
            array(
                'order' => 'price',
                'name' => $this->l('Price')
            ),
        );

        $orderway = array(
            array(
                'orderway' => 'ASC', // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Ascending')    // The value of the text content of the  <option> tag.
            ),
            array(
                'orderway' => 'DESC', // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Descending')    // The value of the text content of the  <option> tag.
            ),
            array(
                'orderway' => 'random', // The value of the 'value' attribute of the <option> tag.
                'name' => $this->l('Random')    // The value of the text content of the  <option> tag.
            ),
        );

        $manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, true);
        $suppliers = Supplier::getSuppliers();
        $selected_categories = array();
        if ($data) {
            if ($data['params'] && isset($data['params']['categorybox']) && $data['params']['categorybox'] != '') {
                $selected_categories = $data['params']['categorybox'];
            }
        }
        $id_root_category = Context::getContext()->shop->getCategory();
        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget Product List.'),
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Step 1: Product Filter').'</div>',
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'value_by',
                    'label' => $this->l('Select By'),
                    'class' => 'checkbox-group',
                    'desc' => $this->l('Select Product Condition'),
                    'values' => array(
                        'query' => array(
                            array(
                                'id' => 'categories',
                                'name' => $this->l('Categories'),
                                'val' => '1'
                            ),
                            array(
                                'id' => 'product_type',
                                'name' => $this->l('Product Type'),
                                'val' => '1'
                            ),
                            array(
                                'id' => 'manufacture',
                                'name' => $this->l('Manufacture'),
                                'val' => '1'
                            ),
                            array(
                                'id' => 'supplier',
                                'name' => $this->l('Supplier'),
                                'val' => '1'
                            ),
                            array(
                                'id' => 'product_id',
                                'name' => $this->l('Product ID'),
                                'val' => '1'
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'categories',
                    'label' => $this->l('Select Category'),
                    'name' => 'categorybox',
                    'desc' => $this->l('You can select one or more, if not select we will not search by category'),
                    'tree' => array(
                        'root_category' => $id_root_category,
                        'use_search' => false,
                        'id' => 'categorybox',
                        'use_checkbox' => true,
                        'selected_categories' => $selected_categories,
                    ),
                    'form_group_class' => 'value_by_categories',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product of Category'),
                    'name' => 'category_type',
                    'options' => array(
                        'query' => array(
                            array('id' => 'all', 'name' => $this->l('Get All Product of Category')),
                            array('id' => 'default', 'name' => $this->l('Get Product if category is default category of product'))),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'value_by_categories',
                    'default' => 'all'
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="sperator"></div>',
                    'form_group_class' => 'value_by_categories',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Type'),
                    'name' => 'product_type',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'all',
                                'name' => $this->l('All Product'),
                            ),
                            array(
                                'id' => 'new_product',
                                'name' => $this->l('New Product'),
                            ),
                            array(
                                'id' => 'best_sellers',
                                'name' => $this->l('Best Sellers'),
                            ),
                            array(
                                'id' => 'price_drop',
                                'name' => $this->l('Price Drop'),
                            ),
                            array(
                                'id' => 'home_featured',
                                'name' => $this->l('Home Featured'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'value_by_product_type',
                    'default' => 'all',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="sperator"></div>',
                    'form_group_class' => 'value_by_product_type',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Manufacture'),
                    'name' => 'manufacture[]',
                    'multiple' => true,
                    'options' => array(
                        'query' => $manufacturers,
                        'id' => 'id_manufacturer',
                        'name' => 'name'
                    ),
                    'default' => 'all',
                    'form_group_class' => 'value_by_manufacture',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="sperator"></div>',
                    'form_group_class' => 'value_by_manufacture',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Supplier'),
                    'name' => 'supplier[]',
                    'multiple' => true,
                    'options' => array(
                        'query' => $suppliers,
                        'id' => 'id_supplier',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'value_by_supplier',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="sperator"></div>',
                    'form_group_class' => 'value_by_supplier',
                ),
                array(
                    'type' => 'text',
                    'name' => 'product_id',
                    'label' => $this->l('Product ID'),
                    'desc' => $this->l('Show product follow product id. Example: 1 or 1,2,3,4 '),
                    'default' => '',
                    'form_group_class' => 'value_by_product_id',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="sperator"></div>',
                    'form_group_class' => 'value_by_product_id',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Step 2: Product Order And Limit').'</div>',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order Way'),
                    'class' => 'form-action',
                    'name' => 'order_way',
                    'options' => array(
                        'query' => array(
                            array('id' => 'asc', 'name' => $this->l('Ascending')),
                            array('id' => 'desc', 'name' => $this->l('Decreasing')),
                            array('id' => 'random', 'name' => $this->l('Random'))),        // remove to increase speed
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default' => 'all'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order By'),
                    'name' => 'order_by',
                    'options' => array(
                        'query' => array(
                                array(
                                    'id' => 'id_product', 'name' => self::l('ID')),
                                array(
                                    'id' => 'name', 'name' => self::l('Name')),
                                array(
                                    'id' => 'reference', 'name' => self::l('Reference')),
                                array(
                                    'id' => 'price', 'name' => self::l('Base price')),
                                array(
                                    'id' => 'date_add', 'name' => self::l('Date add')),
                                array(
                                    'id' => 'date_upd', 'name' => self::l('Date update')
                                ),
                                array(
                                    'id' => 'quantity', 'name' => self::l('Sales (only for Best Sales)')
                                ),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'order_type_sub order_type-asc order_type-desc',
                    'default' => 'all'
                ),
                array(
                    'type' => 'text',
                    'name' => 'nb_products',
                    'label' => $this->l('Limit'),
                    'default' => '10',
                ),
            )
        );

        
        $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'],$new_field['input']);
        array_unshift($this->fields_form[0]['form'], $new_field['legend']);

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($data),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => $default_lang
        );
        unset($args);

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        $module = new DeoTemplate();
        $products = $module->getProductsFont($setting);
        $setting['products'] = (count($products)) ? $products : array();

        // $products = array();
        // $orderby = ($setting['orderby']) ? ($setting['orderby']) : 'position';
        // $orderway = ($setting['orderway']) ? ($setting['orderway']) : 'ASC';
        // $plimit = ($setting['limit']) ? (int)($setting['limit']) : 6;
        // $langID = (int)Context::getContext()->language->id;
        // switch ($setting['source']) {
        //     case 'ptype':
        //         switch ($setting['ptype']) {
        //             case 'newest':
        //                 $products = Product::getNewProducts($langID, 0, $plimit, false, $orderby, $orderway);
        //                 break;
        //             case 'featured':
        //                 $category = new Category(Context::getContext()->shop->getCategory(), $langID);
        //                 //$nb = (int)$setting['limit'];
        //                 $products = $category->getProducts($langID, 1, $plimit, $orderby, $orderway);
        //                 break;
        //             case 'bestseller':
        //                 $products = ProductSale::getBestSalesLight($langID, 0, $plimit);
        //                 break;
        //             case 'special':
        //                 $products = Product::getPricesDrop($langID, 0, $plimit, false, $orderby, $orderway);
        //                 break;
        //             case 'random':
        //                 $random = true;
        //                 $products = $this->getProducts('WHERE  p.id_product > 0', $langID, 1, $plimit, $orderby, $orderway, false, true, $random, $plimit);
        //                 DeoHelper::updateValue('BTMEGAMENU_CURRENT_RANDOM_CACHE', '1');
        //                 break;
        //         }
        //         break;
        //     case 'pproductids':
        //         $where = '';
        //         if (empty($setting['pproductids'])) {
        //             return false;
        //         }
        //         if ($pproductids = $setting['pproductids']) {
        //             $where = 'WHERE  p.id_product IN  ('.pSQL($pproductids).')';
        //         }

        //         $products = $this->getProducts($where, (int)Context::getContext()->language->id, 1, $plimit, $orderby, $orderway);
        //         break;
        //     case 'pcategories':
        //         $where = '';
        //         $catids = (isset($setting['categories']) && $setting['categories']) ? ($setting['categories']) : array();
        //         if ($catids) {
        //             $categorys = implode(',', $catids);
        //             $where = 'WHERE  cp.id_category IN  ('.pSQL($categorys).')';
        //         }
        //         $products = $this->getProducts($where, (int)Context::getContext()->language->id, 1, $plimit, $orderby, $orderway);
        //         break;
        //     case 'pmanufacturers':
        //         $where = '';
        //         $manufacturers = ($setting['pmanufacturer']) ? ($setting['pmanufacturer']) : array();
        //         if ($manufacturers) {
        //             $manufacturers = implode(',', $manufacturers);
        //             $where = 'WHERE  p.id_manufacturer IN  ('.pSQL($manufacturers).')';
        //         }
        //         $products = $this->getProducts($where, (int)Context::getContext()->language->id, 1, $plimit, $orderby, $orderway);
        //         break;
        // }
        // //Context::getContext()->controller->addColorsToProductList($products);
        
        // #1.7
        // $assembler = new ProductAssembler(Context::getContext());

        // $presenterFactory = new ProductPresenterFactory(Context::getContext());
        // $presentationSettings = $presenterFactory->getPresentationSettings();
        // $presenter = new ProductListingPresenter(
        //     new ImageRetriever(
        //         Context::getContext()->link
        //     ),
        //     Context::getContext()->link,
        //     new PriceFormatter(),
        //     new ProductColorsRetriever(),
        //     Context::getContext()->getTranslator()
        // );
        
        // $rate_images = array();
        // $imageRetriever = new ImageRetriever(Context::getContext()->link);
        // $urls['no_picture_image'] =  $imageRetriever->getNoPictureImage(Context::getContext()->language);
        // foreach ($urls['no_picture_image']['bySize'] as $key => $value) {
        //     $rate_images[$key] = DeoTemplate::calculateRateImage($value['width'],$value['height']);
        // }
        // $setting['urls'] = $urls;
        
        // $products_for_template = array();
        // if (isset($products) && is_array($products)) {
        //     foreach ($products as $rawProduct) {
        //         $products_for_template[] = $presenter->present(
        //             $presentationSettings,
        //             $assembler->assembleProduct($rawProduct),
        //             Context::getContext()->language
        //         );
        //     }
        // }
        // $setting['products'] = $products_for_template;
        // 
        // 
        // $currency = array();
        // $fields = array('name', 'iso_code', 'iso_code_num', 'sign');
        // foreach ($fields as $field_name) {
        //     $currency[$field_name] = Context::getContext()->currency->{$field_name};
        // }
        // $setting['currency'] = $currency;
        
        
        

        // $rate_images = array();
        // $imageRetriever = new ImageRetriever(Context::getContext()->link);
        // $urls['no_picture_image'] =  $imageRetriever->getNoPictureImage(Context::getContext()->language);
        // foreach ($urls['no_picture_image']['bySize'] as $key => $value) {
        //     $rate_images[$key] = DeoTemplate::calculateRateImage($value['width'],$value['height']);
        // }
        // $setting['rate_images'] = $rate_images;

        // $setting['products'] = $products;
        // $setting['homeSize'] = Image::getSize(ImageType::getFormattedName('home'));
        $output = array('type' => 'product_list', 'data' => $setting);
        unset($args);

        return $output;
    }

    /**
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     */
    public function getConfigKey($multi_lang = 0)
    {
        if ($multi_lang == 0) {
            return array(
                'value_by_categories',
                'value_by_product_type',
                'value_by_manufacture',
                'value_by_supplier',
                'value_by_product_id',
                'categorybox',
                'category_type',
                'product_type',
                'manufacture',
                'supplier',
                'product_id',
                'nb_products',
                'order_way',
                'order_by',
            );
        } elseif ($multi_lang == 1) {
            return array(
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
