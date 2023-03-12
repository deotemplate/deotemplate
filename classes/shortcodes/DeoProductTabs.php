<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProductsModel.php');

class DeoProductTabs extends DeoShortCodeBase
{
	public $name = 'DeoProductTabs';

	public function getInfo()
	{
		return array(
			'label' => 'Product Tabs', 
			'position' => 3,
			'desc' => $this->l('You can show tabs product carousel from categories'),
			'image' => 'tab.png',
			'tag' => 'content carousel',
			'config' => $this->renderDefaultConfig(),
		);
	}

	public function getConfigList()
	{
		$selected_categories = array();
		if (Tools::getIsset('categorybox')) {
			$category_box = Tools::getValue('categorybox');
			$selected_categories = explode(',', $category_box);
		}
		$profile = new DeoTemplateProductsModel();
		$profile_list = $profile->getAllProductProfileByShop();
		$product_active = DeoTemplateProductsModel::getActive();
		$product_class = $product_active['class'];
		$data_class = array(array('plist_key' => 'default', 'class' => $product_class));
		foreach ($profile_list as $item) {
			$data_class[] = array('plist_key' => $item['plist_key'], 'class' => $item['class']);
		}
		$script = '<script>var productTemp = '.json_encode($data_class).';</script>';
		array_unshift($profile_list, array('plist_key' => 'default', 'name' => $this->l('Use Default')));
		$id_root_category = Context::getContext()->shop->getCategory();
		
		$inputs_head = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'label' => $this->l('Title'),
				'desc' => $this->l('Auto hide if leave it blank'),
				'lang' => 'true',
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
		);
		
		$inputs_content = array(
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => $script.'<div class="alert alert-info">'.$this->l('Step 1: Configuration Tabs').'</div>',
			),
			array(
				'type' => 'select',
				'label' => $this->l('Select Type'),
				'name' => 'tab_type',
				'default' => 'tabs-top',
				'options' => array(
					'query' => array(
						array(
							'id' => 'tabs-top',
							'name' => $this->l('Tabs Top'),
						),
						array(
							'id' => 'tabs-below',
							'name' => $this->l('Tabs below'),
						),
						array(
							'id' => 'tabs-left',
							'name' => $this->l('Tabs Left'),
						),
						array(
							'id' => 'tabs-right',
							'name' => $this->l('Tabs Right'),
						)
					),
					'id' => 'id',
					'name' => 'name'
				),
			),
			array(
				'type' => 'text',
				'name' => 'active_tab',
				'label' => $this->l('Active Tab'),
				'default' => '1',
				'desc' => $this->l('Input position (number) to show tab. If Blank, all first tab is active.'),
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Use Fade effect'),
				'name' => 'fade_effect',
				'is_bool' => true,
				'default' => '1',
				'desc' => $this->l('To make tabs fade in.'),
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => $script.'<div class="alert alert-info">'.$this->l('Step 2: Select Category').'</div>',
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
				'default' => 'all'
			),
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => '<div class="sperator"></div>',
			),
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => '<div class="alert alert-info">'.$this->l('Step 3: Product Order And Limit').'</div>',
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
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => '<div class="alert alert-info">'.$this->l('Step 4: Carousel Setting').'</div>'
			),
			array(
				'type' => 'hidden',
				'name' => 'value_by_categories',
				'default' => '1',
				'value' => '1',
			),
		);

		//Slick carousel
		$inputs_slickCarousel = array(
			array(
				'type' => 'switch',
				'label' => $this->l('Vertical'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'name' => 'slick_vertical',
				'values' => DeoSetting::returnYesNo(),
				'default' => '0'
			),
			array(
				'type' => 'switch',
				'name' => 'slick_autoplay',
				'label' => $this->l('Auto play'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'text',
				'name' => 'slick_autoplayspeed',
				'label' => $this->l('Speed auto play'),
				'desc' => $this->l('1000 milliseconds = 1 seconds'),
				'default' => '10000',
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel group_slick_autoplay',
			),
			array(
				'type' => 'switch',
				'name' => 'slick_pauseonhover',
				'label' => $this->l('Pause on Hover'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_mousewheel',
				'label' => $this->l('Scroll Mouse Wheel'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_loopinfinite',
				'label' => $this->l('Loop Infinite'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_arrows',
				'label' => $this->l('Prev/Next Arrows'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_dot',
				'label' => $this->l('Show dot indicators'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_autoheight',
				'label' => $this->l('Auto Height'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_showloading',
				'label' => $this->l('Show Icon When Loading'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_fade',
				'label' => $this->l('Effect Fade'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'desc' => $this->l('Warning: Only work fine when show one slide'),
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'switch',
				'name' => 'slick_lazyload',
				'label' => $this->l('Lazyload'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'select',
				'name' => 'slick_lazyload_type',
				'label' => $this->l('Lazyload Effect'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel group_lazyload_slick',
				'default' => 'ondemand',
				'options' => array('query' => array(
						array('id' => 'ondemand', 'name' => $this->l('ondemand')),
						array('id' => 'progressive', 'name' => $this->l('progressive')),
					),
					'id' => 'id',
					'name' => 'name')
			),
			array(
				'type' => 'html',
				'name' => 'calculate_rate_image',
				'html_content' => '<p class="help-block html">progressive: Loads the visible image as soon as the page is displayed and the other ones after everything else is loaded in the background.</p><p class="help-block html">on-demand: Loads the visible image as soon as the page is displayed and the other ones only when they are displayed.</p>',
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel group_lazyload_slick description',
			),
			array(
				'type' => 'switch',
				'name' => 'slick_centermode',
				'label' => $this->l('Center mode'),
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
			),
			array(
				'type' => 'text',
				'name' => 'slick_row',
				'label' => $this->l('Num Row'),
				'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
				'default' => '1',
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
			),
			array(
				'type' => 'text',
				'name' => 'slick_slidestoshow',
				'label' => $this->l('Slides To Show'),
				'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
				'default' => '5',
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
			),
			array(
				'type' => 'text',
				'name' => 'slick_slidestoscroll',
				'label' => $this->l('Slides To Scroll'),
				'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
				'default' => '1',
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
			),
			array(
				'type' => 'text',
				'name' => 'slick_items_custom',
				'label' => $this->l('Display responsive for other screen'),
				'desc' => $this->l('Example: [[1200, 5],[992, 4],[768, 3], [576, 2],[480, 1]]. The format is [x,y] whereby x=browser width and y=number of slides displayed'),
				'default' => '[[1200, 5],[992, 4],[768, 3], [576, 2],[480, 1]]',
				'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
			),
		);

		$inputs_footer = array(
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => '<div class="alert alert-info">'.$this->l('Step 5: Product Template').'</div>',
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
			)
		);

		$inputs = array_merge($inputs_head, $inputs_content, $inputs_slickCarousel, $inputs_footer);

		return $inputs;
	}

	public function endRenderForm()
	{
		$this->helper->module = new $this->module_name();
	}

	public function ajaxRenderProductCarousel($module)
	{
		$assign = array();
		$params = array();
		$data_form = json_decode(call_user_func('base64'.'_decode', Tools::getValue('data_form')), true);
		$data_form['formAtts']['categorybox'] = Tools::getValue('id_category');
		Context::getContext()->smarty->assign($data_form);

	   	Context::getContext()->smarty->assign('products', $module->getProductsFont($data_form['formAtts']));

		$rate_images = array();
		$imageRetriever = new ImageRetriever(Context::getContext()->link);
		$urls['no_picture_image'] =  $imageRetriever->getNoPictureImage(Context::getContext()->language);
		foreach ($urls['no_picture_image']['bySize'] as $key => $value) {
			$rate_images[$key] = DeoHelper::calculateRateImage($value['width'],$value['height']);
		}
		Context::getContext()->smarty->assign('rate_images', $rate_images);


		$tpl_file = DeoHelper::getTplTemplate('DeoProductSlickCarousel.tpl', $data_form['formAtts']['override_folder']);
		$html = Context::getContext()->smarty->fetch($tpl_file);
		
		return array('html' => $html);
	}


	public function prepareFontContent($assign, $module = null)
	{
		$assign['formAtts']['class'] = ((isset($assign['formAtts']['class']) && $assign['formAtts']['class']) ? $assign['formAtts']['class'].' ' : '').(isset($assign['formAtts']['tab_type']) ? $assign['formAtts']['tab_type'] : '');
        $assign['formAtts']['class'] .= ' '.$this->name;
		$assign['formAtts']['active_tab'] = (int) $assign['formAtts']['active_tab'];
		$assign['formAtts']['active_tab'] = (isset($assign['formAtts']['active_tab']) && $assign['formAtts']['active_tab']) ? $assign['formAtts']['active_tab'] : 1;

		// build data tab
		$tabs = array();
		$id_lang = (int)Context::getContext()->language->id;
		$id_shop = (int)Context::getContext()->shop->id;
		$value_by_categories = isset($assign['formAtts']['value_by_categories']) ? $assign['formAtts']['value_by_categories'] : 0;
		if ($value_by_categories) {
			$id_categories = isset($assign['formAtts']['categorybox']) ? $assign['formAtts']['categorybox'] : '';
			// Validate id_categories in DeoHelper::addonValidInt function . This function is used at any where
			$id_categories = is_array($id_categories) ? $id_categories : explode(',', $id_categories);         
			$assign['formAtts']['active_tab'] = (count($id_categories) < $assign['formAtts']['active_tab']) ? count($id_categories) : $assign['formAtts']['active_tab'];

			foreach ($id_categories as $key => $id_category) {
				$tab = array();
				$category =  new Category($id_category, $id_lang, $id_shop);
				$tab['id'] = $id_category;
				$tab['name'] = $category->name;
				$tab['active_tab'] = 0; 
				if ($assign['formAtts']['active_tab'] == $key+1){
					$tab['active_tab'] =  1; 
					$assign['formAtts']['categorybox'] = $id_category;
				}
				
				$tabs[$key+1] = $tab;
			}

			$assign['tabs'] = $tabs;
		}


		// build data carousel
		if (!DeoHelper::getLazyload()) {
			$assign['formAtts']['slick_lazyload'] = 0;
		}

		$assign['products'] = $module->getProductsFont($assign['formAtts']);

		$assign['productClassWidget'] = $this->getProductClassByPListKey($assign['formAtts']['profile']);
		if (isset($assign['formAtts']['profile']) && $assign['formAtts']['profile'] != 'default' && file_exists(DeoHelper::getConfigDir('theme_products').$assign['formAtts']['profile'] . '.tpl')) {
			$assign['product_item_path'] = DeoHelper::getConfigDir('theme_products') . $assign['formAtts']['profile'].'.tpl';
		} else {
			// Default load file in theme
			$assign['product_item_path'] = 'catalog/_partials/miniatures/product.tpl';
		}

		if (isset($assign['formAtts']['slick_items_custom'])) {
			$assign['formAtts']['slick_items_custom'] = str_replace($this->str_search, $this->str_relace, $assign['formAtts']['slick_items_custom']);
		}
		if (isset($assign['formAtts']['slick_custom'])) {
			$str_relace = array('&', '\"', '\'', '', '', '', '[', ']', '+', '{', '}');
			$assign['formAtts']['slick_custom'] = str_replace($this->str_search, $str_relace, $assign['formAtts']['slick_custom']);
		}
		if (isset($assign['formAtts']['slick_items_custom'])) {
			$assign['formAtts']['slick_items_custom'] = json_decode($assign['formAtts']['slick_items_custom']);
		}
	
		// build data for fake item loading
		if (isset($assign['formAtts']['slick_items_custom']) && $assign['formAtts']['slick_items_custom'] != '') {
			$array_item_custom_tmp = array();
			$array_item_custom = $assign['formAtts']['slick_items_custom'];
			$array_item_custom_tmp['xl'] = $assign['formAtts']['slick_slidestoshow'];
			foreach ($array_item_custom as $array_item_custom_val) {
				$size_window = $array_item_custom_val[0];
				$number_item = $array_item_custom_val[1];
				if ($size_window <= 480) {
					$array_item_custom_tmp['sp'] = $number_item;
				}else if ($size_window <= 576) {
					$array_item_custom_tmp['xs'] = $number_item;
				}else if ($size_window <= 768) {
					$array_item_custom_tmp['sm'] = $number_item;
				}else if ($size_window <= 992) {
					$array_item_custom_tmp['md'] = $number_item;
				}else if ($size_window <= 1200) {
					$array_item_custom_tmp['lg'] = $number_item;
				}else if ($size_window <= 1500) {
					$array_item_custom_tmp['xl'] = $number_item;
					$array_item_custom_tmp['xxl'] = $assign['formAtts']['slick_slidestoshow'];
				}
			};
			$assign['formAtts']['array_fake_item'] = $array_item_custom_tmp;
		}

		unset($assign['formAtts']['categorybox']);
		$data_form['formAtts'] = $assign['formAtts'];
		$data_form['productClassWidget'] = $assign['productClassWidget'];
		$data_form['product_item_path'] = $assign['product_item_path'];
		$assign['data_form'] = call_user_func('base64'.'_encode', json_encode($data_form));
	   
		return $assign;
	}
}
