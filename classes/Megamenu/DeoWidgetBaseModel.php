<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
	# module validation
	exit;
}
if (!class_exists('DeoWidgetBaseModel')) {
	abstract class DeoWidgetBaseModel
	{
		public $widget_name = 'base';
		public $name = 'deotemplate';
		public $id_shop = 0;
		public $fields_form = array();
		public $types = array();
		public $theme_img_module;
		// public $translator = Context::getContext()->getTranslator();

		// add parameter
		public function __construct()
		{
			$this->theme_img_module = _THEME_IMG_DIR_.'modules/deotemplate/';
		}
		/**
		 * abstract method to return html widget form
		 */
		public function getWidgetInfo()
		{
			return array('key' => 'base', 'label' => 'Widget Base');
		}

		/**
		 * abstract method to return html widget form
		 */
		public function renderForm($args, $data)
		{
			# validate module
			unset($args);
			unset($data);
			return false;
		}

		/**
		 * abstract method to return widget data
		 */
		public function renderContent($args, $data)
		{
			# validate module
			unset($args);
			unset($data);
			return false;
		}

		/**
		 * Get translation for a given module text
		 *
		 * Note: $specific parameter is mandatory for library files.
		 * Otherwise, translation key will not match for Module library
		 * when module is loaded with eval() Module::getModulesOnDisk()
		 *
		 * @param string $string String to translate
		 * @param boolean|string $specific filename to use in translation key
		 * @return string Translation
		 */
		public function l($string, $specific = false)
		{
			return Translate::getModuleTranslation($this->name, $string, ($specific) ? $specific : $this->name);
		}

		/**
		 * Asign value for each input of Data form
		 */
		public function getConfigFieldsValues($data = null)
		{
			$languages = Language::getLanguages(false);
			$fields_values = array();
			$obj = isset($data['params']) ? $data['params'] : array();
			foreach ($this->fields_form as $k => $f) {
				foreach ($f['form']['input'] as $j => $input) {
					if (isset($input['lang'])) {
						foreach ($languages as $lang) {
							$fields_values[$input['name']][$lang['id_lang']] = isset($obj[$input['name'].'_'.$lang['id_lang']]) ? Tools::stripslashes($obj[$input['name'].'_'.$lang['id_lang']]) : $input['default'];
						}
					} else {
						if (isset($obj[$input['name']])) {
							$value = $obj[$input['name']];
							if ($input['name'] == 'image' && $value) {
								$thumb = __PS_BASE_URI__.'modules/'.$this->name.'/img/'.$value;
								$this->fields_form[$k]['form']['input'][$j]['thumb'] = $thumb;
							}

							$fields_values[$input['name']] = Tools::stripslashes($value);
						} else {
							if ($input['type'] === 'checkbox' && isset($input['values'])){
								foreach ($input['values']['query'] as $input_checkbox) {
									$value = isset($obj[$input['name'].'_'.$input_checkbox['id']]) ? $obj[$input['name'].'_'.$input_checkbox['id']] : '';
									$fields_values[$input['name'].'_'.$input_checkbox['id']] = Tools::stripslashes($value);
								}
							}else{
								$v = Tools::getValue($input['name'], Configuration::get($input['name']));
								$fields_values[$input['name']] = $v ? $v : ((isset($input['default'])) ? $input['default'] : false);
							}
						}
					}
				}
			}
			if (isset($data['id_deomegamenu_widgets'])) {
				$fields_values['id_deomegamenu_widgets'] = $data['id_deomegamenu_widgets'];
			}
			
			// update for new plugin facebook like
			if (isset($data['params']['tabdisplay_timeline'])) {
				$fields_values['tabdisplay_timeline'] = $data['params']['tabdisplay_timeline'];
			}
			// update for new plugin facebook like
			if (isset($data['params']['tabdisplay_events'])) {
				$fields_values['tabdisplay_events'] = $data['params']['tabdisplay_events'];
			}
			// update for new plugin facebook like
			if (isset($data['params']['tabdisplay_messages'])) {
				$fields_values['tabdisplay_messages'] = $data['params']['tabdisplay_messages'];
			}
			return $fields_values;
		}

		public function getFormHelper()
		{
			$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			$accordion_type = array(
				array(
					'value' => 'full',
					'text' => $this->l('Normal')
				),
				array(
					'value' => 'accordion',
					'text' => $this->l('Accordion')
				),
				array(
					'value' => 'accordion_small_screen',
					'text' => $this->l('Accordion at tablet (screen <= 768px)')
				),
				array(
					'value' => 'accordion_mobile_screen',
					'text' => $this->l('Accordion at mobile (screen <= 576px)')
				),
			);
			$href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=icon';
			$desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
						<span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
						<span class="btn-image">
							<a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
							<a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
						</span>
					</span>';
			$no_image = __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png';
			$modal = '';
			if (!Tools::isSubmit('load_form_widget')){
			$modal .= '<div id="modal_select_image" class="modal fade" role="dialog" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
							<span class="sr-only">'.$this->l('Close').'</span></button>
							<h4 class="modal-title2">'.$this->l('Image manager').'</h4>
						  </div>
						  <div class="modal-body"></div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->l('Close').'</button>
						  </div>
						</div>
					  </div>
					</div>
					<div id="deo_loading" class="deo-loading" style="display: none;">
						<div class="spinner">
							<div class="item-1"></div>
							<div class="item-2"></div>
							<div class="item-3"></div>
						</div>
					</div>';
			}

			$this->fields_form[0]['form'] = array(
				// 'legend' => array(
				//     'title' => $this->l('Widget Info.'),
				// ),
				'input' => array(
					array(
						'type' => 'html',
						'name' => 'default_html',
						'html_content' => '<div class="alert alert-info">'.$this->l('Title Widget').'</div>',
					),
					array(
						'type' => 'hidden',
						'label' => $this->l('Megamenu ID'),
						'name' => 'id_deomegamenu_widgets',
						'default' => 0,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Widget Name'),
						'name' => 'widget_name',
						'default' => '',
						'required' => true,
						'desc' => $this->l('Using for show in Listing Widget Management')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Widget Title'),
						'name' => 'widget_title',
						'default' => '',
						'lang' => true,
						'desc' => $this->l('This tile will be showed as header of widget block. Empty to disable')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Widget Link Title'),
						'name' => 'link_title',
						'default' => '',
						'lang' => true,
						'desc' => $this->l('This link will be showed as header of widget block. Empty to disable')
					),
					array(
						'type' => 'hidden',
						'label' => $this->l('Widget Type'),
						'name' => 'widget_type',
						'id' => 'widget_type',
						'default' => Tools::getValue('wtype'),
					),
					array(
						'type' => 'text',
						'name' => 'class',
						'label' => $this->l('CSS Class'),
						'default' => ''
					),
					array(
						'type' => 'select',
						'label' => $this->l('Use Accordion'),
						'name' => 'accordion_type',
						'desc' => 'If you use accordion title not empty.',
						'options' => array(
							'query' => $accordion_type,
							'id' => 'value',
							'name' => 'text' ),
						'default' => 'full',
					),

					array(
						'type' => 'switch',
						'label' => $this->l('Use image Icon'),
						'name' => 'icon_use_image_link',
						'values' => DeoSetting::returnYesNo(),
						'default' => '0',
						'class' => 'icon_use_image_link',
						'form_group_class' => 'row-level2 group-config-text-image'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Lazy load Icon'),
						'name' => 'icon_lazyload',
						'values' => DeoSetting::returnYesNo(),
						'default' => '0',
						'class' => 'icon_lazyload',
						'form_group_class' => 'row-level2 group-config-text-image'
					),
					array(
						'type' => 'text',
						'label' => $this->l('Rate size image icon'),
						'name' => 'icon_rate_image',
						'default' => '0',
						'suffix' => '%',
						'class' => 'icon_rate_image',
						'form_group_class' => 'row-level2 rate_lazyload_group_icon rate_value_icon group-config-text-image',
					),
					array(
						'type' => 'html',
						'default' => '',
						'name' => 'icon_html_calc_rate_image',
						'html_content' => '<a href="javascript:void(0)" class="calc-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div><div class="virtual-image-link"></div>',
						'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
						'form_group_class' => 'row-level2 rate_lazyload_group_icon group_calc_rate_image_icon group-config-text-image',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Image Link icon'),
						'name' => 'icon_image_link',
						'default' => '',
						'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
						'form_group_class' => 'row-level2 select_image_link_group_icon group-config-text-image',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Image Icon'),
						'name' => 'icon_image',
						'default' => '',
						'class' => 'hide',
						'desc' => $desc.$modal,
						'form_group_class' => 'row-level2 image-choose-icon group-config-text-image',
					),
					// array(
					//     'type' => 'select',
					//     'label' => $this->l('Widget Type'),
					//     'id' => 'widget_type',
					//     'name' => 'widget_type',
					//     'options' => array(
					//         'query' => $this->types,
					//         'id' => 'type',
					//         'name' => 'label'
					//     ),
					//     'default' => Tools::getValue('wtype'),
					//     'desc' => $this->l('Select a alert style')
					// ),
					array(
						'type' => 'html',
						'name' => 'default_html',
						'html_content' => '<div class="alert alert-info">'.$this->l('Content Widget').'</div>',
					),
				),
				'buttons' => array(
					array(
						'title' => $this->l('Save And Stay'),
						'icon' => 'process-icon-save',
						'class' => 'pull-right save-and-stay',
						'type' => 'submit',
						'name' => 'saveandstaydeowidget'
					),
					array(
						'title' => $this->l('Save'),
						'icon' => 'process-icon-save',
						'class' => 'pull-right save',
						'type' => 'submit',
						'name' => 'savedeowidget'
					),
				)
			);

			$helper = new HelperForm();
			$helper->show_cancel_button = true;
			$helper->module = $this;
			$helper->name_controller = $this->name;
			$helper->identifier = $this->name;
			$helper->token = Tools::getAdminTokenLite('AdminDeoWidgetsMegamenu');
			foreach (Language::getLanguages(false) as $lang) {
				$helper->languages[] = array(
					'id_lang' => $lang['id_lang'],
					'iso_code' => $lang['iso_code'],
					'name' => $lang['name'],
					'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
				);
			}
			$helper->currentIndex = AdminController::$currentIndex.'&widgets=1&rand='.rand().'&wtype='.Tools::getValue('wtype');
			$helper->default_form_language = $default_lang;
			$helper->allow_employee_form_lang = $default_lang;
			$helper->toolbar_scroll = true;
			$helper->title = $this->name;
			$helper->submit_action = 'adddeomegamenu_widgets';

			# validate module
			$helper->toolbar_btn = array(
				'back' =>
				array(
					'desc' => $this->l('Back'),
					'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoWidgetsMegamenu').'&widgets=1&rand='.rand(),
				),
			);

			return $helper;
		}

		public function getManufacturers($id_shop)
		{
			if (!$id_shop) {
				$id_shop = $this->context->shop->id;
			}
			$pmanufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT m.`id_manufacturer`,m.`name`
			FROM `'._DB_PREFIX_.'manufacturer` m
			LEFT JOIN `'._DB_PREFIX_.'manufacturer_shop` ms ON (m.`id_manufacturer` = ms.`id_manufacturer` AND ms.`id_shop` = '.(int)$id_shop.')');
			return $pmanufacturers;
		}

		public function getProducts($where, $id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null)
		{
			# validate module
			unset($check_access);
			if (!$context) {
				$context = Context::getContext();
			}

			$front = true;
			if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
				$front = false;
			}

			if ($p < 1) {
				$p = 1;
			}
			if (empty($order_by)) {
				$order_by = 'position';
			} else {
				/* Fix for all modules which are now using lowercase values for 'orderBy' parameter */
				$order_by = Tools::strtolower($order_by);
			}

			if (empty($order_way)) {
				$order_way = 'ASC';
			}
			if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
				$order_by_prefix = 'p';
			} elseif ($order_by == 'name') {
				$order_by_prefix = 'pl';
			} elseif ($order_by == 'manufacturer') {
				$order_by_prefix = 'm';
				$order_by = 'name';
			} elseif ($order_by == 'position') {
				$order_by_prefix = 'cp';
			}

			if ($order_by == 'price') {
				$order_by = 'orderprice';
			}

			if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
				die(Tools::displayError());
			}

			$id_supplier = (int)Tools::getValue('id_supplier');

			/* Return only the number of products */
			if ($get_total) {
				$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					'.pSQL($where).'
					'.pSQL($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
						pSQL($active ? ' AND product_shop.`active` = 1' : '').
						pSQL($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
				return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
			}

			$sql = 'SELECT DISTINCT p.id_product, p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
					Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				'.$where.'
				AND  product_shop.`id_shop` = '.(int)$context->shop->id.'
				AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop='.(int)$context->shop->id.')
				AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$context->shop->id.')
					'.($active ? ' AND product_shop.`active` = 1' : '')
					.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');
			if ($random === true) {
				$sql .= ' ORDER BY RAND()';
				$sql .= ' LIMIT 0, '.(int)$random_number_products;
			} else {
				$order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';      // $order_way Validate::isOrderWay()
				$sql .= ' ORDER BY '.(isset($order_by_prefix) ? '`'.pSQL($order_by_prefix).'`.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way).'
			LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
			}

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			if ($order_by == 'orderprice') {
				Tools::orderbyPrice($result, $order_way);
			}

			if (!$result) {
				return array();
			}
			/* Modify SQL result */
			return Product::getProductsProperties($id_lang, $result);
		}

		public static function getImageList($path)
		{
			if (!file_exists($path) && !is_dir($path)) {
				@mkdir($path, 0777, true);
			}

			$items = array();
			$handle = opendir($path);
			if (!$handle) {
				return $items;
			}
			while (false !== ($file = readdir($handle))) {
				//if (is_dir($path . $file))
				$items[$file] = $file;
			}
			unset($items['.'], $items['..'], $items['.svn']);
			return $items;
		}
	}

}
