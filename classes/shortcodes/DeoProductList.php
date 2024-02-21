<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProductsModel.php');

class DeoProductList extends DeoShortCodeBase
{
	public $name = 'DeoProductList';
	public $for_module = 'manage';

	public function getInfo()
	{
		return array(
			'label' => 'Product List',
			'position' => 7,
			'desc' => $this->l('Create Product List'),
			'image' => 'product-list.png',
			'tag' => 'content',
			'config' => $this->renderDefaultConfig(),
		);
	}

	public function getAdditionConfig()
	{
		return array(
			array(
				'type' => '',
				'name' => 'value_by_categories',
				'default' => '0'
			),
			array(
				'type' => '',
				'name' => 'value_by_product_type',
				'default' => '0'
			),
			array(
				'type' => '',
				'name' => 'value_by_manufacture',
				'default' => '0'
			),
			array(
				'type' => '',
				'name' => 'value_by_supplier',
				'default' => '0'
			),
			array(
				'type' => '',
				'name' => 'value_by_product_id',
				'default' => '0'
			),
			array(
				'type' => '',
				'name' => 'value_by_tags',
				'default' => '0'
			)
		);
	}

	public function getConfigList()
	{
		$selected_categories = array();
		if (Tools::getIsset('categorybox')) {
			$category_box = Tools::getValue('categorybox');
			$selected_categories = explode(',', $category_box);
		}
		//get all manufacture
		$manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, true);
		$suppliers = Supplier::getSuppliers();
		// $product_active = DeoTemplateProductsModel::getActive();
		// $product_class = $product_active['class'];
		$profile = new DeoTemplateProductsModel();
		$profile_list = $profile->getAllProductProfileByShop();
		array_unshift($profile_list, array('plist_key' => 'default', 'name' => $this->l('Use Default')));
		$id_root_category = Context::getContext()->shop->getCategory();
		$input = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'label' => $this->l('Title'),
				'desc' => $this->l('Auto hide if leave it blank'), 'lang' => 'true',
				'default' => ''
			),
			array(
				'type' => 'textarea',
				'name' => 'sub_title',
				'label' => $this->l('Sub Title'),
				'lang' => true,
				'values' => '',
				'autoload_rte' => false,
				'default' => '',
			),
			array(
				'type' => 'DeoClass',
				'name' => 'class',
				'label' => $this->l('CSS Class'),
				'default' => ''
			),
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
							'name' => $this->l('Product Ids'),
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
				'desc' => $this->l('Press "Ctrl" and "Mouse Left Click" to choose many items'),
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
				'desc' => $this->l('Press "Ctrl" and "Mouse Left Click" to choose many items'),
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
				'label' => $this->l('Product Ids'),
				'desc' => $this->l('Show product follow product id. Ex 1 or 1,2,3,4 '),
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
					'query' => DeoSetting::getOrderBy(),
					'id' => 'id',
					'name' => 'name'
				),
				'form_group_class' => 'order_type_sub order_type-asc order_type-desc',
				'default' => 'all'
			),
			array(
				'type' => 'select',
				'label' => $this->l('Columns'),
				'name' => 'columns',
				'options' => array('query' => array(
						array('id' => '1', 'name' => $this->l('1 Column')),
						array('id' => '2', 'name' => $this->l('2 Columns')),
						array('id' => '3', 'name' => $this->l('3 Columns')),
						array('id' => '4', 'name' => $this->l('4 Columns')),
						array('id' => '5', 'name' => $this->l('5 Columns')),
						array('id' => '6', 'name' => $this->l('6 Columns')),
					),
					'id' => 'id',
					'name' => 'name'
				),
				'default' => '4',
			),
			array(
				'type' => 'text',
				'name' => 'nb_products',
				'label' => $this->l('Limit'),
				'default' => '10',
			),
			//boostrap carousel end
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => '<div class="alert alert-info">'.$this->l('Step 3: Product Template').'</div>',
			),
			array(
				'type' => 'select',
				'label' => $this->l('Product Template'),
				'name' => 'profile',
				'options' => array(
					'query' => $profile_list,
					'id' => 'plist_key',
					'name' => 'name'
				),
				'default' => 'all'
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Lazyload'),
				'name' => 'lazyload',
				'values' => DeoSetting::returnYesNo(),
				'default' => '1'
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Use Animation For Product Item'),
				'name' => 'use_animation',
				'form_group_class' => ((int) DeoHelper::getConfig('LOAD_LIBRARY_WAYPOINTS')) ? '' : 'hide',
				'values' => DeoSetting::returnYesNo(),
				'default' => '1'
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Use Show More button'),
				'name' => 'use_showmore',
				'desc' => $this->l('Show button to load more product or hidden this function'),
				'values' => DeoSetting::returnYesNo(),
				'default' => '1'
			),
		);

		return $input;
	}

	public function endRenderForm()
	{
		$this->helper->module = new $this->module_name();
	}

	public function prepareFontContent($assign, $module = null)
	{
		// validate module
		unset($module);
		if (!DeoHelper::getLazyload()) {
			$assign['formAtts']['lazyload'] = 0;
		}
		$assign['formAtts']['carousel_type'] = 'no-carousel';
		$n = (int)isset($assign['formAtts']['nb_products']) ? $assign['formAtts']['nb_products'] : '10';
		$p = (int)Tools::getIsset('p') ? Tools::getValue('p') : '1';
		$columns = $assign['formAtts']['columns'];
		$assign['formAtts']['page_number'] = $p;     // current page
		$assign['formAtts']['get_total'] = true;     // sql param
		$module = DeoTemplate::getInstance();
		$total = $module->getProductsFont($assign['formAtts']);
		$total = (is_array($total) && count($total) > 0) ? count($total) : 0;
		$assign['formAtts']['total_page'] = $total_page = ceil($total / $columns);
		$assign['formAtts']['use_showmore'] = $p < $total_page ? '1' : '0';  // show_more  yes/no     ( fix for tpl )
		
		$products = array();
		if ($p <= $total_page) {
			$assign['formAtts']['get_total'] = false;
			$products = $module->getProductsFont($assign['formAtts']);
		}
		if(isset($assign['formAtts']['use_showmore']) && $assign['formAtts']['use_showmore']){
			if($p < $total_page){
				$assign['formAtts']['use_showmore'] = 1;    # show_more
			}else{
				$assign['formAtts']['use_showmore'] = 0;
			}
		}
		$assign['scolumn'] = $columns;
		$assign['products'] = $products;
		$assign['p'] = ceil($n / $columns) + 1;
		
		$assign['productClassWidget'] = $this->getProductClassByPListKey($assign['formAtts']['profile']);
		$data_form['productClassWidget'] = $assign['productClassWidget'];

		if (isset($assign['formAtts']['profile']) && $assign['formAtts']['profile'] != 'default' && file_exists(DeoHelper::getConfigDir('theme_products').$assign['formAtts']['profile'] . '.tpl')) {
			$assign['product_item_path'] = DeoHelper::getConfigDir('theme_products') . $assign['formAtts']['profile'].'.tpl';
		} else {
			// Default load file in theme
			$assign['product_item_path'] = 'catalog/_partials/miniatures/product.tpl';
		}
		
		# DATA FOR AJAX
		$data_form['formAtts'] = $assign['formAtts'];
		$data_form['product_item_path'] = $assign['product_item_path'];
		
		$assign['data_form'] = call_user_func('base64'.'_encode', json_encode($data_form));
		return $assign;
	}

	public function ajaxProcessRender($module)
	{
		$assign = array();
		$params = array();
		$data_form = json_decode(call_user_func('base64'.'_decode', Tools::getValue('data_form')), true);

		$p = Tools::getIsset('p') ? (int)Tools::getValue('p') : 1;
		$columns = (int) $data_form['formAtts']['columns'];

		$data_form['formAtts']['nb_products'] = $columns;
		$data_form['formAtts']['page_number'] = $p;
		$data_form['formAtts']['get_total'] = true;
		$total_page = (int) $data_form['formAtts']['total_page'];

		$is_more = ($p < $total_page) ? 'more' : '';
		$products = array();
		if ($p <= $total_page) {
			$data_form['formAtts']['get_total'] = false;
			$products = $module->getProductsFont($data_form['formAtts']);
		}

		$data_form['products'] = $products;
		$data_form['deoAjax'] = 1;
		$data_form['scolumn'] = $columns;
		Context::getContext()->smarty->assign($data_form);
		
		$rate_images = array();
		$imageRetriever = new ImageRetriever(Context::getContext()->link);
		$urls['no_picture_image'] =  $imageRetriever->getNoPictureImage(Context::getContext()->language);
		foreach ($urls['no_picture_image']['bySize'] as $key => $value) {
			$rate_images[$key] = DeoHelper::calculateRateImage($value['width'],$value['height']);
		}
		Context::getContext()->smarty->assign('rate_images', $rate_images);
		
		$tpl_file = DeoHelper::getTplTemplate('DeoProductList.tpl', $data_form['formAtts']['override_folder']);
		$html = Context::getContext()->smarty->fetch($tpl_file);
		
		return array('html' => $html, 'is_more' => $is_more);
	}
}
