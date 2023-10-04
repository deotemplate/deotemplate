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

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\ObjectPresenter;

require_once(_PS_MODULE_DIR_.'deotemplate/libs/Helper.php');
require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoFrameworkHelper.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoSetting.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProfilesModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProductsModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateShortcodeModel.php');

require_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoWidgetBaseModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoWidgetModel.php');

require_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoProductReviewCriterion.php');
require_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoProductReview.php');
require_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoCompareProduct.php');
require_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoWishList.php');

require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogCategory.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogConfiguration.php');

include_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperOnepagecheckout.php');


class DeoTemplate extends Module implements WidgetInterface
{
	protected $default_language;
	protected $languages;
	protected $theme_name;
	protected $data_index_hook;
	protected $profile_data;
	protected $hook_index_data;
	protected $profile_param;
	protected $path_resource;
	protected $product_active;
	protected $backup_dir;
	protected $header_content;
	protected $deo_has_google_map;
	
	protected $data_template = array();
	protected $_confirmations = array();
	protected $_errors = array();
	protected $_warnings = array();
	private $templateFile;
	public $module_path;
	public $is_gen_rtl;
	// private $param_widgets_menu = array();

	public function __construct()
	{
		$this->name = 'deotemplate';
		$this->module_key = '956120277ab2a148484af2f0ada746a3';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'DeoTemplate';
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		
		$this->displayName = 'Deo Template';
		$this->description = $this->l('Deo Template build content for your site.');
		$this->controllers = array('onepagecheckout', 'advancedsearch', 'blog', 'blogcategory', 'bloghomepage', 'compare', 'mywishlist', 'viewwishlist');
		$this->theme_name = Context::getContext()->shop->theme_name;
		$this->default_language = Language::getLanguage(Context::getContext()->language->id);
		$this->languages = Language::getLanguages();
		$this->module_path = $this->local_path;
		$this->path_resource = $this->_path.'views/';
		$this->templateFile = 'module:deotemplate/views/templates/hook/deotemplate.tpl';

		$this->redirectFriendUrl();

		if (file_exists(_PS_THEME_DIR_ . 'modules/deotemplate/css/styles_rtl.css') || file_exists(_PS_THEME_DIR_ . '/assets/css/modules/deotemplate/css/styles_rtl.css')) {
			$this->is_gen_rtl = true;
		} else {
			$this->is_gen_rtl = false;
		}
	}


	public function redirectFriendUrl()
	{
		// $this->registerHook('actionFrontControllerSetMedia');
		// NEED HOOK TO MODULEROUTES
		if (isset(Context::getContext()->controller->controller_type) && in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront'))) {
			$id_deotemplate_profiles = Tools::getValue('id_deotemplate_profiles');
			if (Configuration::get('PS_REWRITING_SETTINGS') && Tools::getIsset('id_deotemplate_profiles') && $id_deotemplate_profiles) {
				$profile_data = DeoTemplateProfilesModel::getActiveProfile('index');

				if (isset($profile_data['friendly_url']) && $profile_data['friendly_url']) {
					require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoFriendlyUrl.php');
					$friendly_url = DeoFriendlyUrl::getInstance();
					$link = Context::getContext()->link;
					$idLang = Context::getContext()->language->id;
					$idShop = null;
					$relativeProtocol = false;
					
					$url = $link->getBaseLink($idShop, null, $relativeProtocol).$friendly_url->getLangLink($idLang, null, $idShop).$profile_data['friendly_url'].'.html';
					$friendly_url->canonicalRedirection($url);
				}
			}
		}
	}
	
	public static function getInstance()
	{
		static $_instance;
		if (!$_instance) {
			$_instance = new DeoTemplate();
		}
		return $_instance;
	}

	public function install()
	{
		require_once(_PS_MODULE_DIR_.$this->name.'/libs/setup.php');
		
		// build shortcode, create folder for override
		DeoHelper::createShortCode();
		if (!parent::install() || !DeoPageSetup::createTables() || !DeoPageSetup::installConfiguration() || !DeoPageSetup::installModuleTab() || !$this->registerDeoHook()) {
			return false;
		}

		if (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true){
			DeoHelper::processDebugMode(true);
		}else{
			DeoHelper::processDebugMode(false);
		}

		
		# REMOVE FILE INDEX.PHP FOR TRANSLATE
		DeoPageSetup::processTranslateTheme();
			   
		DeoHelper::updateValue('DEOTEMPLATE_OVERRIDED', 1);

		return true;
	}

	public function uninstall()
	{
		require_once(_PS_MODULE_DIR_.$this->name.'/libs/setup.php');
		
		// #shortcode to tinymce :  rollback default file config for tinymce
		// Tools::copy(_PS_MODULE_DIR_.$this->name.'/views/js/shortcode/backup/tinymce.inc.js', _PS_ROOT_DIR_.'/js/admin/tinymce.inc.js');
		
		// #shortcode to tinymce : CATEGORY IN ADMIN
		// Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/category.bundle.js', _PS_ADMIN_DIR_.'/themes/new-theme/public/category.bundle.js');
		// #shortcode to tinymce : CMS PAGE IN ADMIN
		// Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/cms_page_form.bundle.js', _PS_ADMIN_DIR_.'/themes/new-theme/public/cms_page_form.bundle.js');
		
		if (version_compare(_PS_VERSION_, '1.7.8.0', '=')){
			#restore default core prestashop
			Tools::copy(_PS_MODULE_DIR_.$this->name.'/views/js/shortcode/backup/core.js', _PS_ALL_THEMES_DIR_.'core.js');
		}

		if (!parent::uninstall() || !DeoPageSetup::uninstallModuleTab() || !DeoPageSetup::deleteTables() || !DeoPageSetup::uninstallConfiguration() || !$this->unregisterDeoHook()) {
			echo "not uninstall";
			return false;
		}

		// remove overrider folder
		// $this->uninstallOverrides();
		
		// remove config check override for shortcode
		DeoHelper::updateValue('DEOTEMPLATE_OVERRIDED', 0);
		
		return true;
	}
	
	public function hookActionModuleRegisterHookAfter($params)
	{
		if (isset($params['hook_name']) && ($params['hook_name'] == 'header' || $params['hook_name'] == 'displayheader')) {
			$hook_name = 'header';
			$id_hook = Hook::getIdByName($hook_name);
			$id_module = $this->id;
			$id_shop = Context::getContext()->shop->id;
			
			// Get module position in hook
			$sql = 'SELECT MAX(`position`) AS position
				FROM `'._DB_PREFIX_.'hook_module`
				WHERE `id_hook` = '.(int)$id_hook.' AND `id_shop` = '.(int)$id_shop . ' AND id_module != '.(int) $id_module;
			if (!$position = Db::getInstance()->getValue($sql)) {
				$position = 0;
			}

			$sql = 'UPDATE `'._DB_PREFIX_.'hook_module'.'` SET `position` =' . (int)($position + 1) . ' WHERE '
							. '`id_module` = '.(int) $id_module
							. ' AND `id_hook` = '.(int) $id_hook
							. ' AND `id_shop` = '.(int) $id_shop;
			Db::getInstance()->execute($sql);
		}
	}
	
	public function postProcess()
	{
		if (count($this->errors) > 0) {
			return;
		}
	}


	public function getContent()
	{
		$this->errors = array();
		if (!$this->access('configure')) {
			$this->errors[] = $this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error');
			$this->context->smarty->assign('errors', $this->errors);
		}
		
		// $this->postProcess();
		
		$output = '';
		$this->backup_dir = str_replace('\\', '/', _PS_CACHE_DIR_.'backup/modules/deotemplate/');
		
		$create_profile_link = $this->context->link->getAdminLink('AdminDeoProfiles').'&adddeotemplate_profiles';
		$profile_link = $this->context->link->getAdminLink('AdminDeoProfiles');
		$position_link = $this->context->link->getAdminLink('AdminDeoPositions');
		$product_link = $this->context->link->getAdminLink('AdminDeoProducts');
		$blog_link = $this->context->link->getAdminLink('AdminDeoBlogDashboard');
		$detail_link = $this->context->link->getAdminLink('AdminDeoDetails');

		$this->context->smarty->assign(array(
			'create_profile_link' => $create_profile_link,
			'profile_link' => $profile_link,
			'position_link' => $position_link,
			'product_link' => $product_link,
		));

		// $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
		Media::addJsDef(array('js_deo_controller' => 'module_configuration'));
		return $output.$this->renderForm();
	}

	
	/**
	 * sub function of back-up database
	 */
	public function createInsert($data, $table_name, $lines, $sizeof)
	{
		$data_no_lang = 'INSERT INTO `'.$table_name."` VALUES\n";
		$i = 1;
		while ($row = DB::getInstance()->nextRow($data)) {
			$s = '(';
			foreach ($row as $field => $value) {
				if ($field == 'ID_SHOP') {
					$tmp = "'".pSQL('ID_SHOP', true)."',";
				} else {
					$tmp = "'".pSQL($value, true)."',";
				}
				if ($tmp != "'',") {
					$s .= $tmp;
				} else {
					foreach ($lines as $line) {
						if (Tools::strpos($line, '`'.$field.'`') !== false) {
							if (preg_match('/(.*NOT NULL.*)/Ui', $line)) {
								$s .= "'',";
							} else {
								$s .= 'NULL,';
							}
							break;
						}
					}
				}
			}
			$s = rtrim($s, ',');
			if ($i % 200 == 0 && $i < $sizeof) {
				$s .= ");\nINSERT INTO `".$table_name."` VALUES\n";
			} elseif ($i < $sizeof) {
				$s .= "),\n";
			} else {
				$s .= ");\n";
			}
			$data_no_lang .= $s;

			++$i;
		}
		return $data_no_lang;
	}

	public function renderForm()
	{
		Tools::redirectAdmin($this->context->link->getAdminLink('AdminDeoThemeConfigure'));
	}
	

	public function hookPagebuilderConfig($param)
	{
		$config = $param['configName'];
		if ($config == 'profile' || $config == 'mobile'  || $config == 'header' || $config == 'footer' || $config == 'content' || $config == 'product') {
			#GET DETAIL PROFILE
			$cache_name = 'pagebuilderConfig'.'|'.$param['configName'];
			$cache_id = $this->getCacheId($cache_name);
			if (!$this->isCached('module:deotemplate/views/templates/hook/config.tpl', $cache_id)) {
				$deo_type = $config;

				if ($config == 'profile') {
					$deo_type = 'id_deotemplate_profiles';
				} else if ($config == 'product_list_builder') {
					$deo_type = 'plist_key';
				}
				$this->smarty->assign(
					array(
						'deo_config_data' => $this->getConfigData($config),
						'deo_config' => $config,
						'deo_type' => $deo_type,
						'deo_controller' => DeoHelper::getPageName(),
						'deo_current_url' => Context::getContext()->link->getPageLink('index', true),
					)
				);
			}
			return $this->display(__FILE__, 'config.tpl', $cache_id);
		}

		if (!$this->product_active) {
			$this->product_active = DeoTemplateProductsModel::getActive();
		}
		if ($config == 'productClass') {
			// validate module
			return $this->product_active['class'];
		}

		if ($config == 'productKey') {
			$tpl_file = DeoHelper::getConfigDir('theme_products') . $this->product_active['plist_key'].'.tpl';
			if (is_file($tpl_file)) {
				return $this->product_active['plist_key'];
			}
			return;
		}
	}

	public function getConfigData($config)
	{
		if ($config == 'profile') {
			$id_lang = (int)Context::getContext()->language->id;
			$sql = 'SELECT p.`id_deotemplate_profiles` AS `id`, p.`name`, ps.`active`, pl.friendly_url FROM `'._DB_PREFIX_.'deotemplate_profiles` p '
					.' INNER JOIN `'._DB_PREFIX_.'deotemplate_profiles_shop` ps ON (ps.`id_deotemplate_profiles` = p.`id_deotemplate_profiles`)'
					.' INNER JOIN `'._DB_PREFIX_.'deotemplate_profiles_lang` pl ON (pl.`id_deotemplate_profiles` = p.`id_deotemplate_profiles`) AND pl.id_lang='.(int)$id_lang
					.' WHERE ps.id_shop='.(int)Context::getContext()->shop->id;
		} else if ($config == 'product_list_builder') {
			$sql = 'SELECT p.`plist_key` AS `id`, p.`name`, ps.`active`, p.`demo`, p.`responsive`'
					.' FROM `'._DB_PREFIX_.'deotemplate_products` p '
					.' INNER JOIN `'._DB_PREFIX_.'deotemplate_products_shop` ps '
					.' ON (ps.`id_deotemplate_products` = p.`id_deotemplate_products`)'
					.' WHERE ps.id_shop='.(int)Context::getContext()->shop->id;
		} else {
			$sql = 'SELECT p.`id_deotemplate_positions` AS `id`, p.`name`'
					.' FROM `'._DB_PREFIX_.'deotemplate_positions` p '
					.' INNER JOIN `'._DB_PREFIX_.'deotemplate_positions_shop` ps '
					.' ON (ps.`id_deotemplate_positions` = p.`id_deotemplate_positions`)'
					.' WHERE p.`position` = \''.PSQL($config).'\' AND ps.id_shop='.(int)Context::getContext()->shop->id;
		}
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		foreach ($result as &$val) {
			if ($config == 'profile') {
				$val['active'] = 0;
				if ($val['id'] == $this->profile_data['id_deotemplate_profiles']) {
					$val['active'] = 1;
				}
			} else if ($config == 'product_list_builder') {
				if (Tools::getIsset('plist_key')) {
					$val['active'] = 0;
					if ($val['id'] == Tools::getValue('plist_key')) {
						$val['active'] = 1;
					}
				}
			} else {
				$val['active'] = 0;
				if (Tools::getIsset($config)) {
					if ($val['id'] == Tools::getValue($config)) {
						$val['active'] = 1;
					}
				} else {
					if ($val['id'] == $this->profile_data[$config]) {
						$val['active'] = 1;
					}
				}
			}
		}
		return $result;
	}


	/**
	 * Widget
	 */
	public function fontContent($assign, $tpl_name, $cache_id = null)
	{
		if ($assign) {
			foreach ($assign as $key => $ass) {
				$this->smarty->assign(array($key => $ass));
			}
		}
		$override_folder = '';
		if (isset($assign['formAtts']['override_folder']) && $assign['formAtts']['override_folder'] != ''){
			$override_folder = $assign['formAtts']['override_folder'];
		}

		$tpl_file = 'module:deotemplate/'.DeoHelper::getTemplate($tpl_name, $override_folder);
		// if ($this->isCached($tpl_file, $cache_id)) {
		// 	// echo $cache_id.'<br>';
		// 	// echo 'module:deotemplate/'.$tpl_file;
		// 	// return $module->display(__FILE__, $tpl_file, $cache_id);
		// 	return $this->fetch($tpl_file, $cache_id);
		// }
		// return $this->fetch($tpl_file);

		// if ($tpl_name == 'DeoModule.tpl'){
		// 	echo '<pre>';
		// 	print_r($assign['formAtts']);
		// 	echo '</pre>';
		// 	die();
		// 	return $this->fetch($tpl_file);
		// }else{
			return $this->fetch($tpl_file, $cache_id);
		// }


		// if (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true){
		// 	$content = $this->display(__FILE__, $tpl_file);
		// }else{
		// 	$content = $this->display(__FILE__, $tpl_file, $cache_id);
		// }

		// return $content;
	}

	/**
	 * $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null
	 */
	public function getProductsFont($params)
	{   
		//build where
		$where = '';
		$sql_join = '';
		$sql_group = '';
		$sql_order = '';
		$sql_limit = '';

		$id_lang = $this->context->language->id;
		$context = Context::getContext();
		//assign value from params
		$p = isset($params['page_number']) ? $params['page_number'] : 1;
		if ($p < 0) {
			$p = 1;
		}
		$n = isset($params['nb_products']) ? $params['nb_products'] : 10;
		if ($n < 1) {
			$n = 10;
		}
		$order_by = isset($params['order_by']) ? Tools::strtolower($params['order_by']) : 'position';
		$order_way = isset($params['order_way']) ? $params['order_way'] : 'ASC';
		$get_total = isset($params['get_total']) ? $params['get_total'] : false;
		$order_by_prefix = false;
		$random = false;
		if ($order_way == 'random') {
			$random = true;
		}else{
			if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
				$order_by_prefix = 'product_shop';
			} else if ($order_by == 'reference') {
				$order_by_prefix = 'p';
			} else if ($order_by == 'name') {
				$order_by_prefix = 'pl';
			} elseif ($order_by == 'manufacturer') {
				$order_by_prefix = 'm';
				$order_by = 'name';
			} elseif ($order_by == 'position') {
				$order_by = 'date_add';
				$order_by_prefix = 'product_shop';
				// $order_by_prefix = 'cp';
			} elseif ($order_by == 'quantity') {
				$order_by_prefix = 'ps';    // ps_product_sale
				$sql_join .= ' INNER JOIN '._DB_PREFIX_.'product_sale ps ON (ps.id_product= p.`id_product` )';
			}
			if ($order_by == 'price') {
				$order_by = 'orderprice';
				$order_by_prefix = 'p';
			}
		}
		$active = 1;
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by)) {
			die(Tools::displayError());
		}


		// By categories
		$value_by_categories = isset($params['value_by_categories']) ? $params['value_by_categories'] : 0;
		if ($value_by_categories) {
			$id_categories = isset($params['categorybox']) ? $params['categorybox'] : '';
			// Validate id_categories in DeoHelper::addonValidInt function . This function is used at any where
			$id_categories = is_array($id_categories) ? implode(',', $id_categories) : DeoHelper::addonValidInt( $id_categories );         
			if (isset($params['category_type']) && $params['category_type'] == 'default') {
				$where .= ' AND product_shop.`id_category_default` IN ('.pSQL($id_categories).')';
			} else {
				$sql_join .= ' INNER JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product= p.`id_product` )';
				
				$where .= ' AND cp.`id_category` IN ('.pSQL($id_categories).')';
				$sql_group = ' GROUP BY p.id_product';
			}
		}
		// By supplier
		$value_by_supplier = isset($params['value_by_supplier']) ? $params['value_by_supplier'] : 0;
		if ($value_by_supplier && isset($params['supplier'])) {
			// Validate id_categories in DeoHelper::addonValidInt function. This function is used at any where
			$id_suppliers = DeoHelper::addonValidInt( $params['supplier'] );          
			$where .= ' AND p.id_supplier IN ('.pSQL($id_suppliers).')';
		}
		// By product ID
		$value_by_product_id = isset($params['value_by_product_id']) ? $params['value_by_product_id'] : 0;
		if ($value_by_product_id && isset($params['product_id'])) {
			$temp = explode(',', $params['product_id']);
			foreach ($temp as $key => $value) {
				// validate module
				$temp[$key] = (int)$value;
			}

			$product_id = implode(',', array_map('intval', $temp));
			$where .= ' AND p.id_product '.(Tools::strpos($product_id, ',') === false ? '= '.(int)$product_id : 'IN ('.pSQL($product_id).')');
		}
		// By manufacture
		$value_by_manufacture = isset($params['value_by_manufacture']) ? $params['value_by_manufacture'] : 0;
		if ($value_by_manufacture && isset($params['manufacture'])) {
			// Validate id_categories in DeoHelper::addonValidInt function. This function is used at any where
			$id_manufactures = DeoHelper::addonValidInt( $params['manufacture'] );          
			$where .= ' AND p.id_manufacturer IN ('.pSQL($id_manufactures).')';
		}

		// By product type
		$product_type = isset($params['product_type']) ? $params['product_type'] : '';
		$value_by_product_type = isset($params['value_by_product_type']) ? $params['value_by_product_type'] : 0;
		// + new product
		if ($value_by_product_type && $product_type == 'new_product') {
			$where .= ' AND product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'"';
		}
		// + home feature
		if ($value_by_product_type && $product_type == 'home_featured') {
			$ids = array();
			$category = new Category((int) Configuration::get('HOME_FEATURED_CAT'));
			$products = $category->getProducts((int)Context::getContext()->language->id, 1, $n*($p+1), 'position');   // Load more product $n*$p, hidden
			foreach ($products as $product) {
				$ids[$product['id_product']] = 1;
			}
			$ids = array_keys($ids);
			sort($ids);
			$ids = count($ids) > 0 ? implode(',', $ids) : 'NULL';
			$where .= ' AND p.`id_product` IN ('.$ids.')';
		}
		// + price drop
		if ($value_by_product_type && $product_type == 'price_drop') {
			$current_date = date('Y-m-d H:i:s');
			$id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
			$ids = Address::getCountryAndState($id_address);
			$id_country = (isset($ids['id_country']) && $ids['id_country']) ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT');
			$id_country = (int)$id_country;
			$ids_product = SpecificPrice::getProductIdByDate($context->shop->id, $context->currency->id, $id_country, $context->customer->id_default_group, $current_date, $current_date, 0, false);
			$tab_id_product = array();
			foreach ($ids_product as $product) {
				if (is_array($product)) {
					$tab_id_product[] = (int)$product['id_product'];
				} else {
					$tab_id_product[] = (int)$product;
				}
			}
			$where .= ' AND p.`id_product` IN ('.((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0).')';
		}

		// + best seller
		if ($value_by_product_type && $product_type == 'best_sellers' && $random) {
			$sql_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_sale` ps ON ps.`id_product` = p.`id_product`';
		}
		
		$sql = 'SELECT p.`id_product`';
		$sql .= ' FROM `'._DB_PREFIX_.'product` p';
		$sql .= ' INNER JOIN '._DB_PREFIX_.'product_shop product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.(int)$context->shop->id.')';
		$sql .= $sql_join;

		$sql .= ' WHERE product_shop.`id_shop` = '.(int)$context->shop->id.' AND product_shop.`active` = 1 AND product_shop.`visibility` IN ("both", "catalog")'.$where;

		$sql .= $sql_group;

		if ($random === true) {
			$sql_order = ' ORDER BY RAND()';
			$sql_limit = (!$get_total ? ' LIMIT '.(int)$n : '');
		} else {
			$order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';      // $order_way Validate::isOrderWay()
			$sql_order = ' ORDER BY '.(!empty($order_by_prefix) ? '`'.pSQL($order_by_prefix).'`.' : '').'`'.(bqSQL($order_by) == 'orderprice' ? 'price' : bqSQL($order_by)).'` '.pSQL($order_way);
			$sql_limit = (!$get_total ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : '');
		}
		$sql .= $sql_order.' '.$sql_limit;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


		if ($order_by == 'orderprice') {
			Tools::orderbyPrice($result, $order_way);
		}
		// print_r($result);
		// die();
		if (!$result) {
			return array();
		}

		$result = $this->addAttributesProduct($result);

		return $result;
	}

	public function addAttributesProduct($products)
	{
		# 1.7
		$assembler = new ProductAssembler(Context::getContext());
		$presenterFactory = new ProductPresenterFactory(Context::getContext());
		$presentationSettings = $presenterFactory->getPresentationSettings();
		$presenter = new ProductListingPresenter(
			new ImageRetriever(
				Context::getContext()->link
			),
			Context::getContext()->link,
			new PriceFormatter(),
			new ProductColorsRetriever(),
			Context::getContext()->getTranslator()
		);
		
		$products_for_template = array();
		foreach ($products as $rawProduct)
		{
			$product_temp = $presenter->present(
				$presentationSettings,
				$assembler->assembleProduct($rawProduct),
				Context::getContext()->language
			);
			
			# FIX 1.7.5.0
			if(is_object($product_temp) && method_exists($product_temp, 'jsonSerialize'))
			{
				$product_temp = $product_temp->jsonSerialize();
			}
			
			# ADD SHORTCODE TO PRODUCT DESCRIPTION AND PRODUCT SHORT DESCRIPTION
			// $product_temp['description'] = $this->buildShortCode($product_temp['description']);
			// $product_temp['description_short'] = $this->buildShortCode($product_temp['description_short']);
			$products_for_template[] = $product_temp;
		}
		return $products_for_template;
	}

	// register hook back-end
	public function hookActionAdminControllerSetMedia()
	{
		Media::addJsDef(
			array(
				'deotemplate_module_dir' => $this->_path,
				'deo_url_no_image' => __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png',
				'deotemplate_listshortcode_url' => $this->context->link->getAdminLink('AdminDeoShortcode').'&get_listshortcode=1',
				'deo_language' => Language::getLanguages(false),
			)
		);
		DeoHelper::updateValue('shortcode_url_add', $this->context->link->getAdminLink('AdminDeoShortcode'));

		// review
		$this->context->controller->addJS(DeoHelper::getJsAdminDir().'feature/back.js');
		$this->context->controller->addCSS(DeoHelper::getCssAdminDir().'feature/back.css');
		Media::addJsDef(array(
			'deo_url_review' => $this->context->link->getModuleLink('deotemplate', 'review', array(), null, null, null, true),
		));

		$this->autoRestoreSampleData();
		
		if (Tools::getValue('configure') == 'deotemplate') {
			Media::addJsDef(array('autoload_func_name' => 'SetButonSaveToHeader'));
			$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/autoload.js');
			
			Media::addJsDef(array('TopSaveAndStay_Name' => 'submitDeoTemplate'));
			$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
		}

	}

	public function hookActionFrontControllerSetMedia()
	{
		
	}


	public function hookHeader()
	{	
		
		$id_shop = (int) $this->context->shop->id;
		$id_lang = (int) $this->context->language->id;
		$page_name = DeoHelper::getPageName();
		$id_page_name = DeoHelper::getIDPageName();
		DeoHelper::autoUpdateModule();
		// if (!isset(Context::getContext()->controller->controller_name)){
		// 	$sql = 'SELECT p.*, pl.params, pl.id_lang
		// 		FROM '._DB_PREFIX_.'deotemplate p
		// 			LEFT JOIN '._DB_PREFIX_.'deotemplate_shop ps ON (ps.id_deotemplate = p.id_deotemplate AND id_shop='.$id_shop.')
		// 			LEFT JOIN '._DB_PREFIX_.'deotemplate_lang pl ON (pl.id_deotemplate = p.id_deotemplate) 
		// 			WHERE pl.id_lang='.$id_lang.' 
		// 			AND ps.id_shop='.$id_shop;

		// 	$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		// 	$new_array = array();
		// 	DeoHelper::loadShortCode(_PS_THEME_DIR_);

		// 	// echo "<pre>";
		// 	foreach ($result as $key => $value) {
		// 		$new_array[$value['id_deotemplate_positions']][] = $value;
		// 		$name_position_params = DeoHelper::getConfigName($value['hook_name'].'_'.$value['id_deotemplate_positions'].'_'.Language::getIsoById($id_lang));
		// 		// if (!DeoHelper::hasKey($name_position_params)){
		// 			DeoShortCodesBuilder::$is_front_office = 1;
		// 	        DeoShortCodesBuilder::$is_gen_html = 1;
		// 	        DeoShortCodesBuilder::$profile_param = array();
		// 	        $shortcode_builder = new DeoShortCodesBuilder();
		// 	        DeoShortCodesBuilder::$hook_name = $value['hook_name'];
		// 	        $parseJson = $shortcode_builder->parseToJson($value['params']);
		// 			// $shortcode_tags = DeoShortCodesBuilder::$shortcode_tags;
		// 			DeoHelper::updateValue($name_position_params, json_encode($parseJson));
		// 			// print_r($parseJson);
		// 	  		// die();
		// 		// }
				
		// 		// $deo_html_content = $model->parseData($value['hook_name'], $this->hook_index_data[$hook_name], $this->profile_param);
		// 		// DeoHelper::updateValue($name_position_params, Tools::htmlentitiesUTF8($value));
		// 		unset($value[$key]);
		// 	}
		// 	$this->data_template = $new_array;
		// 	// print_r($this->data_template);
		// 	// echo "</pre>";
		// 	// die();
		// }
		

		// var_dump($controller_page_name) ;
		if (isset(Context::getContext()->controller->controller_type) && in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront'))) {
			# WORK AT FRONTEND
			DeoHelper::loadShortCode(_PS_THEME_DIR_);

			$this->profile_data = DeoTemplateProfilesModel::getActiveProfile('index');
			$this->profile_param = json_decode($this->profile_data['params'], true);

			
			// override setting fullwidth for hook displayHome with other pages
			if (($page_name != 'home' || $page_name != 'index') && isset($this->profile_param['fullwidth_content_other_page'])){
				if (Context::getContext()->controller->controller_type == 'modulefront'){
					$controller_key = $id_page_name;
				}else{
					$controller_key = $page_name;
				}

				if ($controller_key == 'module-deotemplate-order'){
					$this->profile_param['fullwidth_other_hook']['displayHome'] = $this->profile_param['fullwidth_content_other_page']['order'];
				}else{
					if (isset($this->profile_param['fullwidth_content_other_page'][$controller_key])){
						$this->profile_param['fullwidth_other_hook']['displayHome'] = $this->profile_param['fullwidth_content_other_page'][$controller_key];
					}
				} 
			}

			// assign varriable fullwidth_hook
			if ($page_name == 'index' || $page_name == 'home') {
				$this->context->smarty->assign(array(
					'fullwidth_hook' => isset($this->profile_param['fullwidth_index_hook']) ? $this->profile_param['fullwidth_index_hook'] : DeoSetting::getIndexHook(3),
				));
			} else {
				$this->context->smarty->assign(array(
					'fullwidth_hook' => isset($this->profile_param['fullwidth_other_hook']) ? $this->profile_param['fullwidth_other_hook'] : DeoSetting::getOtherHook(3),
				));
			}

			// load variables debug mode
			// $deo_debug_mode = (int) DeoHelper::getConfig('DEBUG_MODE');
			// $this->smarty->smarty->assign('deo_debug_mode', $deo_debug_mode);
			// Media::addJsDef(array('deo_debug_mode' => $deo_debug_mode));

			# FIX 1.7
			DeoHelper::setGlobalVariable($this->context, $this->profile_param);
		}

		$this->themeCookieName = DeoHelper::getConfigName('PANEL_CONFIG');
		Media::addJsDefL('deo_cookie_theme', $this->themeCookieName);


		# LOAD VARIABLES MOBILE MODE
		$mobile_friendly = 0;
		$infinite_scroll = 0;
		$is_mobile = $this->context->isMobile();
		$is_tablet = $this->context->isTablet();
		$params_mobile_mode = $this->profile_param['mobile_mode'];
		if ((int) DeoHelper::getConfig('PANELTOOL')) {
			$cookie = DeoFrameworkHelper::getCookie();
			if (isset($cookie[$this->themeCookieName.'_MOBILE_FRIENDLY'])) {
				$mobile_friendly = (int) $cookie[$this->themeCookieName.'_MOBILE_FRIENDLY'];
			}else{
				$mobile_friendly = 1;
				setcookie($this->themeCookieName.'_MOBILE_FRIENDLY', $mobile_friendly, time() + (86400 * 30), '/');
			}

			if ($mobile_friendly){
				$deo_header_mobile = ((int) isset($params_mobile_mode['header_mobile']) && $is_mobile) ? $params_mobile_mode['header_mobile'] : 0;
				$deo_nav_mobile = ((int) isset($params_mobile_mode['nav_mobile']) && $is_mobile) ? $params_mobile_mode['nav_mobile'] : 0;
				$deo_content_mobile = ((int) isset($params_mobile_mode['content_mobile']) && $is_mobile) ? $params_mobile_mode['content_mobile'] : 0;
				$deo_footer_mobile = ((int) isset($params_mobile_mode['footer_mobile']) && $is_mobile) ? $params_mobile_mode['footer_mobile'] : 0;
			}else{
				$deo_header_mobile = 0;
				$deo_nav_mobile = 0;
				$deo_content_mobile = 0;
				$deo_footer_mobile = 0;
			}

			if (isset($cookie[$this->themeCookieName.'_INFINITE_SCROLL'])) {
				$infinite_scroll = (int) $cookie[$this->themeCookieName.'_INFINITE_SCROLL'];
			}else{
				$infinite_scroll = 1;
				setcookie($this->themeCookieName.'_INFINITE_SCROLL', $infinite_scroll, time() + (86400 * 30), '/');
			}

			Media::addJsDef(array(
				'deo_demo_category_link' => $this->context->link->getCategoryLink(Configuration::get('PS_HOME_CATEGORY') , null, null, null, null, false),
			));
		}else{
			$deo_header_mobile = ((int) isset($params_mobile_mode['header_mobile']) && $is_mobile) ? $params_mobile_mode['header_mobile'] : 0;
			$deo_nav_mobile = ((int) isset($params_mobile_mode['nav_mobile']) && $is_mobile) ? $params_mobile_mode['nav_mobile'] : 0;
			$deo_content_mobile = ((int) isset($params_mobile_mode['content_mobile']) && $is_mobile) ? $params_mobile_mode['content_mobile'] : 0;
			$deo_footer_mobile = ((int) isset($params_mobile_mode['footer_mobile']) && $is_mobile) ? $params_mobile_mode['footer_mobile'] : 0;
		}
		$arr_responsive = array(
			'deo_is_mobile' => $is_mobile,
			'deo_is_tablet' => $is_tablet,
			'deo_header_mobile' => $deo_header_mobile,
			'deo_nav_mobile' => $deo_nav_mobile,
			'deo_content_mobile' => $deo_content_mobile,
			'deo_footer_mobile' => $deo_footer_mobile,
		);
		$this->smarty->smarty->assign($arr_responsive);
		Media::addJsDef($arr_responsive);

		if (isset($this->profile_param['breadcrumb'])){
			$breadcrumb = $this->profile_param['breadcrumb'];
			$this->smarty->smarty->assign(array(
				'deo_breadcrumb_image' => $breadcrumb['breadcrumb_image'],
				'deo_breadcrumb_image_fullwidth' => $breadcrumb['breadcrumb_image_fullwidth'],
				'deo_breadcrumb_category_image' => $breadcrumb['breadcrumb_category_image'],
			));
		}

		// notification
		$this->context->controller->addJqueryPlugin('growl', null, true);

		$uri = DeoHelper::getCssDir(false).'fonts.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

		# load css for components
		$array_components = ['customer.css', 'errors.css'];
		foreach ($array_components as $value) {
			$uri = DeoHelper::getCssDir().'components/'.$value;
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		
		// echo $page_name;
		if ($page_name == 'cart'){
			$uri = DeoHelper::getCssDir().'components/cart.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		if ($page_name == 'category'){
			$uri = DeoHelper::getCssDir().'components/categories.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		if ($page_name == 'order' || $page_name == 'orderconfirmation'){
			$uri = DeoHelper::getCssDir().'components/checkout.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$uri = DeoHelper::getCssDir().'components/cart.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		if ($page_name == 'product'){
			$uri = DeoHelper::getCssDir().'components/products.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		if ($page_name == 'contact'){
			$uri = DeoHelper::getCssDir().'components/contact.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		if ($page_name == 'password'){
			$uri = DeoHelper::getCssDir().'components/forgotten-password.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		if ($page_name == 'sitemap'){
			$uri = DeoHelper::getCssDir().'components/sitemap.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
		if ($page_name == 'stores'){
			$uri = DeoHelper::getCssDir().'components/stores.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}

		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_PANR')) {
			$uri = DeoHelper::getJsDir().'TweenMax.min.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$uri = DeoHelper::getJsDir().'panr.jquery.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_WAYPOINTS')) {
			$uri = DeoHelper::getJsDir().'waypoints.min.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_INSTAFEED')) {
			$uri = DeoHelper::getJsDir().'instafeed.min.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_STELLAR')) {
			$uri = DeoHelper::getJsDir().'jquery.stellar.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_OWL_CAROUSEL')) {
			$uri = DeoHelper::getCssDir().'owl.carousel.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			$uri = DeoHelper::getCssDir().'owl.theme.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			
			$uri = DeoHelper::getJsDir().'owl.carousel.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}

		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_SWIPER')) {
			$uri = DeoHelper::getCssDir().'swiper.min.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			
			$uri = DeoHelper::getJsDir().'swiper.min.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}

		$uri = DeoHelper::getCssDir(false).'animate.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

		// add jquery plugin images loaded
		$uri = DeoHelper::getJsDir().'imagesloaded.pkgd.min.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

		// slick carousel
		$uri = DeoHelper::getJsDir().'slick.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

		$uri = DeoHelper::getCssDir().'slick-theme.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

		$uri = DeoHelper::getCssDir().'slick.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

		// zoom image
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_PRODUCT_ZOOM')) {
			$uri = DeoHelper::getJsDir().'jquery.elevateZoom-3.0.8.min.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}

		$uri = DeoHelper::getCssDir().'styles.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

		Media::addJsDef(array(
			'deo_url_ajax' => $this->context->link->getModuleLink('deotemplate', 'ajax'),
			'deo_url_megamenu' => $this->context->link->getModuleLink('deotemplate', 'megamenu'),
		));

		$this->processHookHeaderBlog();
		$this->processHookHeaderFeature();
		$this->processHookHeaderQuickLogin();
		$this->processHookHeaderInfiniteScroll();
		

		$deo_lazyload = DeoHelper::getLazyload();
		$deo_lazy_intersection_observer = (int) DeoHelper::getConfig('LAZY_INTERSECTION_OBSERVER');
		$more_product_img = (int) DeoHelper::getConfig('AJAX_MULTIPLE_PRODUCT_IMAGE');
		$second_img = (int) DeoHelper::getConfig('AJAX_SECOND_PRODUCT_IMAGE');
		$qty_category = (int) DeoHelper::getConfig('AJAX_CATEGORY_QTY');
		$countdown = (int) DeoHelper::getConfig('AJAX_COUNTDOWN');
		$animation = (int) DeoHelper::getConfig('LOAD_LIBRARY_WAYPOINTS');
		$ajax_enable = ($more_product_img || $second_img || $qty_category || $countdown) ? 1 : 0;

		$this->smarty->assign(array(
			'ajax_enable' => $ajax_enable,
			'more_product_img' => $more_product_img,
			'second_img' => $second_img,
			'qty_category' => $qty_category,
			'animation' => $animation,
			'countdown' => $countdown,
		));

		if ($is_mobile) {
			$uri = DeoHelper::getCssDir().'mobile.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}


		$this->context->controller->addJqueryPlugin('fancybox');
		$uri = DeoHelper::getJsDir().'jquery.fancybox-transitions.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		$uri = DeoHelper::getCssDir(false).'jquery.fancybox-transitions.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		
		if ($countdown) {
			$uri = DeoHelper::getCssDir().'countdown.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$uri = DeoHelper::getJsDir().'countdown.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

			// countdownpro
			$uri = DeoHelper::getJsDir().'deocountdown.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		 
			$date = new DateTime();
			Media::addJsDef(array(
				'deo_time_now' => $date->getTimestamp()*1000,
			));
		}

		if ($more_product_img) {
			$this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll'));
		}

		//core themes
		$uri = DeoHelper::getJsDir().'library.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		$uri = DeoHelper::getJsDir().'script.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		$uri = DeoHelper::getJsDir().'themes.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		
		//lazyload
		if ($deo_lazyload) {
			$uri = DeoHelper::getJsDir().'lazyload/lazysizes.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$uri = DeoHelper::getJsDir().'lazyload/ls.bgset.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}
		Media::addJsDef(array(
			'deo_lazyload' => $deo_lazyload,
			'deo_lazy_intersection_observer' => $deo_lazy_intersection_observer,
		));
			
		// add js for html5 youtube video
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_HTML5_VIDEO')) {
			$uri = DeoHelper::getCssDir(false).'mediaelementplayer.min.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			
			$uri = DeoHelper::getJsDir().'mediaelement-and-player.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}

		//add js,css for full page js
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_FULLPAGE')) {
			$uri = DeoHelper::getCssDir(false).'jquery.fullPage.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			
			$uri = DeoHelper::getJsDir().'jquery.fullPage.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		}

		// add js sticky https://github.com/leafo/sticky-sidebar
		$uri = DeoHelper::getJsDir().'resize-sensor.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
		$uri = DeoHelper::getJsDir().'sticky-sidebar.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

		

		// add js, css for Magic360
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_IMAGE360')) {
			$uri = DeoHelper::getCssDir(false).'magic360.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			$uri = DeoHelper::getCssDir(false).'magic360.module.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			
			$uri = DeoHelper::getJsDir().'magic360.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

			Media::addJsDef(array(
				'deo_image360_hint_text' => $this->l('Drag to spin'),
				'deo_image360_mobile_hint_text' => $this->l('Swipe to spin'),
			));
		}
		
		// add js Cookie : jquery.cooki-plugin.js
		if ((int) DeoHelper::getConfig('LOAD_LIBRARY_COOKIE')) {
			$this->context->controller->addJqueryPlugin('cooki-plugin');
		}

		$uri = DeoHelper::getCssDir().'widgets.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 805));
		
		// add unique css file, css of module for all theme, no need override		
		$uri = DeoHelper::getCssDir(false).'unique.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 805));

		if (!$this->product_active) {
			$this->product_active = DeoTemplateProductsModel::getActive();
		}
		$this->smarty->smarty->assign(array('productClassWidget' => $this->product_active['class']));
		
		$tpl_file = DeoHelper::getConfigDir('theme_products') . $this->product_active['plist_key'].'.tpl';
		if (is_file($tpl_file)) {
			$this->smarty->smarty->assign(array('productProfileDefault' => $this->product_active['plist_key']));
			if ($page_name == 'category' && Tools::getIsset('plist_key')){
				$plist_key = Tools::getValue('plist_key');
				$sql = 'SELECT p.`responsive` FROM `'._DB_PREFIX_.'deotemplate_products` p '
					.' INNER JOIN `'._DB_PREFIX_.'deotemplate_products_shop` ps '
					.' ON (ps.`id_deotemplate_products` = p.`id_deotemplate_products`)'
					.' WHERE p.`demo`=1 AND p.`plist_key`="'.$plist_key.'" AND ps.id_shop='.(int)$id_shop;
				$responsive_plist = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
				$responsive_plist = json_decode($responsive_plist);
				if (is_array($responsive_plist) && count($responsive_plist)){
					$colValue = '';

					foreach ($responsive_plist as $array_item) {
						$size_window = $array_item[0];
						$number_item = $array_item[1];

						if ($size_window <= 480) {
							$colValue .= ($number_item == 5) ? ' col-sp-2-4' : ' col-sp-'. 12/$number_item;
						}else if ($size_window <= 576) {
							$colValue .= ($number_item == 5) ? ' col-xs-2-4' : ' col-xs-'. 12/$number_item;
						}else if ($size_window <= 768) {
							$colValue .= ($number_item == 5) ? ' col-sm-2-4' : ' col-sm-'. 12/$number_item;
						}else if ($size_window <= 992) {
							$colValue .= ($number_item == 5) ? ' col-md-2-4' : ' col-md-'. 12/$number_item;
						}else if ($size_window <= 1200) {
							$colValue .= ($number_item == 5) ? ' col-lg-2-4' : ' col-lg-'. 12/$number_item;
						}else if ($size_window <= 1500) {
							$colValue .= ($number_item == 5) ? ' col-xl-2-4' : ' col-xl-'. 12/$number_item;
						}else if ($size_window > 1500) {
							$colValue .= ($number_item == 5) ? ' col-xxl-2-4' : ' col-xxl-'. 12/$number_item;
						}
					};

					$this->smarty->smarty->assign(array('responsive_plist' => $colValue));
				}

				$uri = DeoHelper::getCssDir().'products/'.$plist_key.'.css';
				$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			}else{
				$uri = DeoHelper::getCssDir().'products/'.$this->product_active['plist_key'].'.css';
				$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			}
		}
		// In the case not exist: create new cache file for template
		if (!$this->isCached('module:deotemplate/views/templates/hook/header.tpl', $this->getCacheId('displayHeader'))) {
			if (!$this->hook_index_data) {
				$model = new DeoTemplateModel();
				// $this->hook_index_data = $model->getAllItems($this->profile_data, 1, $this->default_language['id_lang']);
				$this->hook_index_data = $model->getAllItems($this->profile_data, 1, $this->default_language['id_lang'], $this->data_template);
			}
		}


		$is_rtl = $this->context->language->is_rtl;
		$deo_rtl = $this->context->language->is_rtl;
		if ($deo_rtl && version_compare(Configuration::get('PS_VERSION_DB'), '1.7.3.0', '>=')) {
			$directory = _PS_ALL_THEMES_DIR_.$this->theme_name;
			$allFiles = Tools::scandir($directory, 'css', '', true);
			$rtl_file = false;
			foreach ($allFiles as $key => $file) {
				if (Tools::substr(rtrim($file, '.css'), -4) == '_rtl') {
					$rtl_file = true;
					break;
				}
			}
			
			if ($rtl_file) {
				$deo_rtl = false; // to remove class RTL
				// $this->context->controller->unregisterStylesheet('theme-rtl');
				@unlink(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/css/rtl_rtl.css');  // Remove file rtl_rtl.css
				$this->context->controller->registerStylesheet('theme-rtl', '/assets/css/rtl.css', array('media' => 'all', 'priority' => 900));
			}
		}
		// $helper = DeoFrameworkHelper::getInstance();
		
		if ($this->context->language->is_rtl) {
			# OVERRIDE CORE, LOAD RTL.CSS FILE AT BOTTOM
			$this->context->controller->registerStylesheet('theme-rtl', '/assets/css/rtl.css', ['media' => 'all', 'priority' => 9000]);
		}
		
		if ((int) DeoHelper::getConfig('PANELTOOL')) {
			 # ENABLE PANEL TOOL
			$skin = $this->getPanelConfig('DEFAULT_SKIN');
			$primary_custom_color_skin = $this->getPanelConfig('PRIMARY_CUSTOM_COLOR_SKIN');
			$second_custom_color_skin = $this->getPanelConfig('SECOND_CUSTOM_COLOR_SKIN');
			$primary_custom_font = $this->getPanelConfig('PRIMARY_CUSTOM_FONT');
			$second_custom_font = $this->getPanelConfig('SECOND_CUSTOM_FONT');
			$stickey_menu = (int) $this->getPanelConfig('STICKEY_MENU');
			$cookie = DeoFrameworkHelper::getCookie();

			// $uri = DeoHelper::getJsDir().'googlewebfont.json';
			// $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$uri = DeoHelper::getJsDir().'webfont.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$uri = DeoHelper::getCssDir(false).'awesomplete.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			$uri = DeoHelper::getJsDir().'awesomplete.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));


			$uri = DeoHelper::getCssDir(false).'bootstrap-colorpicker.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			$uri = DeoHelper::getCssDir().'paneltool.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			
			$uri = DeoHelper::getJsDir().'bootstrap-colorpicker.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$uri = DeoHelper::getJsDir().'paneltool.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$this->context->controller->addJqueryPlugin('cooki-plugin');

			$uri = DeoHelper::getCssDir().'widgets_modules/DeoPopup.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			# LOAD SKIN CSS IN MODULE
			if ($skin == 'custom-skin'){
				// $uri =  DeoHelper::getCssDir().'skins/skin-custom.css'; 
			}else{
				$uri = DeoHelper::getCssDir().'skins/'.$skin.'/skin.css';
				if ($deo_url_skin = DeoHelper::checkFileOverrideExist($uri)){
					$this->smarty->smarty->assign('deo_url_skin', $deo_url_skin);
					
					// Media::addJsDefL('deo_url_skin', $uri);
					// $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
				}
			}
		}else{
			$skin = DeoHelper::getConfig('DEFAULT_SKIN');
			$primary_custom_color_skin = DeoHelper::getConfig('PRIMARY_CUSTOM_COLOR_SKIN');
			$second_custom_color_skin = DeoHelper::getConfig('SECOND_CUSTOM_COLOR_SKIN');
			$primary_custom_font = DeoHelper::getConfig('PRIMARY_CUSTOM_FONT');
			$second_custom_font = DeoHelper::getConfig('SECOND_CUSTOM_FONT');
			$stickey_menu = (int) DeoHelper::getConfig('STICKEY_MENU');
			if (DeoHelper::getPageName() == 'category') {
				$this->context->controller->addJqueryPlugin('cooki-plugin');
			}

			$uri = ($skin == 'custom-skin') ? 'skins/skin-custom.css' : 'skins/'.$skin.'/skin.css';
			$uri = DeoHelper::getCssDir().$uri;
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 810));

			$uri = DeoHelper::getCssDir().'skins/font-custom.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 810));
		}
		$ps = array(
			'IS_RTL' => $is_rtl,
			'DEO_RTL' => $deo_rtl,
			'deo_skin_default' => $skin,
			'deo_primary_custom_color_skin' => $primary_custom_color_skin,
			'deo_second_custom_color_skin' => $second_custom_color_skin,
			'deo_primary_custom_font' => $primary_custom_font,
			'deo_second_custom_font' => $second_custom_font,
			'deo_stickey_menu' => $stickey_menu,
			'deo_mobile_friendly' => $mobile_friendly,
			'deo_infinite_scroll' => $infinite_scroll,
			'deo_subcategory' => (int) DeoHelper::getConfig('SUBCATEGORY'),
			'deo_back_top' => DeoHelper::getConfig('BACKTOP'),
			'DeoHelper' => DeoHelper::getInstance(),
			'deoConfiguration' => new Configuration(),
		);
		$this->context->smarty->assign($ps);
		
		# LOAD CSS SHORTCODE
		$css_files_available = DeoSetting::getCssFilesAvailable();
		$widgets_modules = json_decode(DeoHelper::getConfig('SHORTCODE_WIDGETS_MODULES'));
		if (is_array($widgets_modules) && count($widgets_modules)){
			foreach ($widgets_modules as $key => $value){
				if (in_array($value, $css_files_available['widgets_modules'])){
					if ($value == 'DeoProductTabs'){
						$uri = DeoHelper::getCssDir().'widgets_modules/DeoTabs.css';
						$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
					}else{
						$uri = DeoHelper::getCssDir().'widgets_modules/'.$value.'.css';
						$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
					}
				}
			}
		}
						
		$elements = json_decode(DeoHelper::getConfig('SHORTCODE_ELEMENTS'));
		if (is_array($elements) && count($elements)){
			foreach ($elements as $key => $value){
				if (in_array($value, $css_files_available['elements'])){
					$uri = DeoHelper::getCssDir().'elements/'.$value.'.css';
					$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
				}
			}
		}

		$product_lists = json_decode(DeoHelper::getConfig('SHORTCODE_PRODUCT_LISTS'));
		if (is_array($product_lists) && count($product_lists)){
			foreach ($product_lists as $key => $value){
				$uri = DeoHelper::getCssDir().'products/'.$value.'.css';
				$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
			}
		}

		# LOAD POSITIONS AND PROFILES
		$this->deo_has_google_map = false;
		$this->loadResouceForProfile($arr_responsive);
		if ($page_name == 'contact' && (int) DeoHelper::getConfig('ENABLE_GOOGLE_MAP') && (int) DeoHelper::getConfig('ENABLE_GOOGLE_MAP_CONTACT_PAGE') && DeoHelper::getConfig('API_KEY_GOOGLE_MAP')){
			$this->deo_has_google_map = true;

			$uri = DeoHelper::getCssDir().'widgets_modules/DeoGoogleMap.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		}
			
		$this->smarty->smarty->assign(array('deo_has_google_map' => $this->deo_has_google_map));
		$this->context->smarty->assign('key_google_map' , '');

		if ($this->deo_has_google_map){
			$deoGoogleMap = array(
				'translation_5'  => $this->l('Click to larger Map'),
				'logo_store'  => Configuration::get('PS_STORES_ICON'),
				'img_store_dir' => _THEME_STORE_DIR_,
				'img_ps_dir' => _PS_IMG_,
			);
			Media::addJsDef(array(
				'deo_gmap' => array(),
				'deoGoogleMap' => $deoGoogleMap,
			));
			$this->context->smarty->assign('key_google_map' , DeoHelper::getConfig('API_KEY_GOOGLE_MAP'));
		}

		$page = $this->smarty->smarty->tpl_vars['page']->value;
		if (isset($this->profile_data['meta_title']) && $this->profile_data['meta_title'] && $page_name == 'index') {
			$page['meta']['title'] = $this->profile_data['meta_title'];
		}
		if (isset($this->profile_data['meta_description']) && $this->profile_data['meta_description'] && $page_name == 'index') {
			$page['meta']['description'] = $this->profile_data['meta_description'];
		}
		if (isset($this->profile_data['meta_keywords']) && $this->profile_data['meta_keywords'] && $page_name == 'index') {
			$page['meta']['keywords'] = $this->profile_data['meta_keywords'];
		}
		$this->smarty->smarty->assign('page', $page);

		# REPLACE LINK FOR MULILANGUAGE
		if ($page_name == 'home' || $page_name == 'index') {
			Media::addJsDef(array('deo_profile_multilang_url' => DeoTemplateProfilesModel::getAllProfileRewrite($this->profile_data['id_deotemplate_profiles'])));
		}

		if ($page_name == 'sitemap') {
			$profile_model = new DeoTemplateProfilesModel();
			$profiles = $profile_model->getAllProfileByShop();
			foreach ($profiles as $key => $profile) {
				if (!isset($profile['friendly_url']) || !$profile['friendly_url']) {
					unset($profiles[$key]);
				}
			}
			$this->smarty->smarty->assign('simap_deo_profiles', $profiles);
		}

		$rate_images = array();
		$imageRetriever = new ImageRetriever(Context::getContext()->link);
		$urls['no_picture_image'] =  $imageRetriever->getNoPictureImage(Context::getContext()->language);
		foreach ($urls['no_picture_image']['bySize'] as $key => $value) {
			$rate_images[$key] = DeoHelper::calculateRateImage($value['width'],$value['height']);
		}
		$this->smarty->smarty->assign('rate_images', $rate_images);
		Media::addJsDef(array(
			'deo_rate_images' => $rate_images,
			'deo_url_no_picture_images' => $urls,
		));

		$product_layouts_reponsive = array();


		$body_classes = $this->smarty->smarty->tpl_vars['page']->value['body_classes'];
		$layouts_page = 'layout-both-columns';
		if (isset($body_classes['layout-full-width']) && $body_classes['layout-full-width']){
			$layouts_page = 'layout-full-width';
		}else if (isset($body_classes['layout-left-column']) && $body_classes['layout-left-column']){
			$layouts_page = 'layout-left-column';
		}else if (isset($body_classes['layout-right-column']) && $body_classes['layout-right-column']){
			$layouts_page = 'layout-right-column';
		}

		$this->smarty->smarty->assign('layouts_page', $layouts_page);
		$this->header_content .=  $this->processHeaderOnepagecheckout().$this->display(__FILE__, 'header.tpl');
		
		return $this->header_content;
	}






	// Infinite Scroll
	public function processHookHeaderInfiniteScroll()
	{	
		if ((int) DeoHelper::getConfig('PANELTOOL')){
			$cookie = DeoFrameworkHelper::getCookie();
			if (isset($cookie[$this->themeCookieName.'_INFINITE_SCROLL']) && !(int)$cookie[$this->themeCookieName.'_INFINITE_SCROLL']){
				return true;
			}
		} 

		if (!empty($this->context->controller->page_name)) {
			$page_name = $this->context->controller->page_name;
		} elseif (!empty($this->context->controller->php_self)) {
			$page_name = $this->context->controller->php_self;
		} elseif (preg_match('#^'.preg_quote($this->context->shop->physical_uri, '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m)) {
			$page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
		} else {
			$page_name = Dispatcher::getInstance()->getController();
			$page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
		}

		// support module deo advancesearch
		$deo_pages = array('module-deotemplate-advancedsearch');
		if (in_array($page_name, $deo_pages)) {
			$page_name = 'deotemplate-advancedsearch';
		}


		$enable_infinite_scroll = false;
		if ((int) DeoHelper::getConfig('ENABLE_INFINITE_SCROLL_' . Tools::strtoupper($page_name)) && (int) DeoHelper::getConfig('ENABLE_INFINITE_SCROLL')
			&& ($page_name != "manufacturer" || Tools::getValue('id_manufacturer') != false)
			&& ($page_name != "supplier" || Tools::getValue('id_supplier') != false)) {

			$enable_infinite_scroll = true;
			$uri = DeoHelper::getJsDir().'deo_infinite_scroll.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

			$uri = DeoHelper::getCssDir().'deo_infinite_scroll.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$this->smarty->assign(array(
				'infinite_scroll_product_list_css_selector' => DeoHelper::getConfig('INFINITE_SCROLL_PRODUCT_LIST_CSS_SELECTOR'),
				'infinite_scroll_pagination_css_selector' => DeoHelper::getConfig('INFINITE_SCROLL_PAGINATION_SELECTOR'),
				'infinite_scroll_item_css_selector' => DeoHelper::getConfig('INFINITE_SCROLL_ITEM_SELECTOR'),
				'infinite_scroll_display_load_more_product' => DeoHelper::getConfig('INFINITE_SCROLL_DISPLAY_LOAD_MORE_PRODUCT'),
				'infinite_scroll_number_page_show_load_more_product' => DeoHelper::getConfig('INFINITE_SCROLL_NUMBER_PAGE_SHOW_LOAD_MORE_PRODUCT'),
				'infinite_scroll_frequency_show_load_more_product' => DeoHelper::getConfig('INFINITE_SCROLL_FREQUENCY_SHOW_LOAD_MORE_PRODUCT'),
				'infinite_scroll_hide_message_end_page' => DeoHelper::getConfig('INFINITE_SCROLL_HIDE_MESSAGE_WHEN_END_PAGE'),
				'infinite_scroll_current_page' => Tools::getValue('page', 1),
				'infinite_scroll_text_message_end_page' => $this->l('No more product to load.'),
				'infinite_scroll_text_back_to_top' => $this->l('Back to top'),
				'infinite_scroll_text_error' => $this->l('Something wrong happened and we can not display further products'),
				'infinite_scroll_text_loadmore' => $this->l('Load more products'),
				'infinite_scroll_has_filter_module' => (int) Module::isEnabled('blocklayered') || (int) Module::isEnabled('blocklayered_mod') || (int) Module::isEnabled('ps_facetedsearch'),
				'infinite_scroll_js_script_after' => DeoHelper::getConfig('INFINITE_SCROLL_JS_SCRIPT_AFTER'),
				'infinite_scroll_js_script_process' => DeoHelper::getConfig('INFINITE_SCROLL_JS_SCRIPT_PROCESS_PRODUCTS'),
				'ps_instant_search' => Configuration::get('PS_INSTANT_SEARCH'),
			));
		}

		$this->smarty->assign(array(
			'enable_infinite_scroll' => $enable_infinite_scroll,
		));
	}





	public function processHookHeaderBlog()
	{
		$uri = DeoHelper::getCssDir().'blog.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
					
		// update language link
		if ((int) DeoHelper::getConfig('ENABLE_BLOG')) {
			$langs = Language::getLanguages(false);
			if (count($langs) > 1) {
				// $config = DeoBlogConfiguration::getInstance();
				$array_list_rewrite = array();
				$array_category_rewrite = array();
				$array_config_category_rewrite = array();
				$array_blog_rewrite = array();
				$array_config_blog_rewrite = array();
				$config_url_use_id = (int) DeoHelper::getConfig('BLOG_URL_USE_ID');
				
				$page_name = DeoHelper::getPageName();
				
				if ($page_name == 'module-deotemplate-blog') {
					if ($config_url_use_id == 1) {
						$id_blog = Tools::getValue('id');
					} else {
						$id_shop = (int)Context::getContext()->shop->id;
						$block_rewrite = Tools::getValue('rewrite');
						$sql = 'SELECT bl.id_deoblog FROM '._DB_PREFIX_.'deoblog_lang bl';
						$sql .= ' INNER JOIN '._DB_PREFIX_.'deoblog_shop bs on bl.id_deoblog=bs.id_deoblog AND id_shop='.(int)$id_shop;
						$sql .= " AND link_rewrite = '".pSQL($block_rewrite)."'";
						if ($row = Db::getInstance()->getRow($sql)) {
							$id_blog = $row['id_deoblog'];
						}
					}
					$blog_obj = new DeoBlog($id_blog);
				}
				
				if ($page_name == 'module-deotemplate-blogcategory') {
					if ($config_url_use_id == 1) {
						$id_category = Tools::getValue('id');
					} else {
						$id_shop = (int)Context::getContext()->shop->id;
						$category_rewrite = Tools::getValue('rewrite');
						$sql = 'SELECT cl.id_deoblog_category FROM '._DB_PREFIX_.'deoblog_category_lang cl';
						$sql .= ' INNER JOIN '._DB_PREFIX_.'deoblog_category_shop cs on cl.id_deoblog_category=cs.id_deoblog_category AND id_shop='.(int)$id_shop;
						$sql .= ' INNER JOIN '._DB_PREFIX_.'deoblog_category cc on cl.id_deoblog_category=cc.id_deoblog_category AND cl.id_deoblog_category != cc.id_parent';  # FIX : PARENT IS NOT THIS CATEGORY
						$sql .= " AND link_rewrite = '".pSQL($category_rewrite)."'";

						if ($row = Db::getInstance()->getRow($sql)) {
							$id_category = $row['id_deoblog_category'];
						}
					}
					$blog_category_obj = new DeoBlogCategory($id_category);
				}
				
				foreach ($langs as $lang) {
					$array_list_rewrite[$lang['iso_code']] = DeoHelper::getConfig('BLOG_LINK_REWRITE');
					
					if (isset($id_blog)) {
						$array_blog_rewrite[$lang['iso_code']] = $blog_obj->link_rewrite[$lang['id_lang']];
						// if ($config_url_use_id == 0) {
						//     $array_config_blog_rewrite[$lang['iso_code']] = $config->get('detail_rewrite_'.$lang['id_lang'], 'detail');
						// }
					}
					
					if (isset($id_category)) {
						$array_category_rewrite[$lang['iso_code']] = $blog_category_obj->link_rewrite[$lang['id_lang']];
						// if ($config_url_use_id == 0) {
						//     $array_config_category_rewrite[$lang['iso_code']] = $config->get('category_rewrite_'.$lang['id_lang'], 'category');
						// }
					}
				};
				
				Media::addJsDef(array(
					'array_list_rewrite' => $array_list_rewrite,
					'array_category_rewrite' => $array_category_rewrite,
					'array_blog_rewrite' => $array_blog_rewrite,
					'array_config_category_rewrite' => $array_config_category_rewrite,
					'array_config_blog_rewrite' => $array_config_blog_rewrite,
					'config_url_use_id' => $config_url_use_id
				));
			}
		}
	}

	public function sidebarBlogCategoryTree()
	{
		$html = '';
		if (DeoHelper::getConfig('BLOG_CATEORY_MENU')) {
			$helper = DeoBlogHelper::getInstance();
			$category = new DeoBlogCategory();
			$id_deoblog_category = (Tools::getIsset('id_deoblog_category')) ? Tools::getValue('id_deoblog_category') : $category->getRoot();
			$tree = $category->getFrontEndTree($id_deoblog_category, $helper);
			$this->smarty->assign('tree', $tree);

			if ($category->id_deoblog_category) {
				# validate module
				$this->smarty->assign('currentCategory', $category);
			}
			$html .= $this->display(__FILE__, 'views/templates/hook/blog/blog_category_tree.tpl');
		}
		
		return $html;
	}
	
	public function sidebarPopularBlog()
	{
		$html = '';
		
		if ((int) DeoHelper::getConfig('BLOG_SHOW_POPULAR')) {
			$limit = (int) DeoHelper::getConfig('BLOG_LIMIT_POPULAR');
			$helper = DeoBlogHelper::getInstance();

			$blogs = array();
			if ($limit > 0) {
				$blogs = DeoBlog::getListBlogs(null, $this->context->language->id, 1, $limit, 'views', 'DESC', array(), true);
				$comment = false;
				if (DeoHelper::getConfig('BLOG_ITEM_COMMENT_ENGINE') == 'local') {
					$comment = DeoBlogComment::getComments(null, null, null, null, null, null, null);
				}
				foreach ($blogs as $key => &$blog) {
					$blog = $helper->formatBlog($blog, false, $comment);
				}
			}

			$this->smarty->assign('blogs', $blogs);
			$html .= $this->display(__FILE__, 'views/templates/hook/blog/blog_popular.tpl');
		}
		
		return $html;
	}
	
	public function sidebarRecentBlog()
	{
		$html = '';
		
		if ((int) DeoHelper::getConfig('BLOG_SHOW_RECENT')) {
			$helper = DeoBlogHelper::getInstance();
			$limit = (int) DeoHelper::getConfig('BLOG_LIMIT_RECENT');

			$blogs = array();
			if ($limit > 0) {
				$blogs = DeoBlog::getListBlogs(null, $this->context->language->id, 1, $limit, 'date_add', 'DESC', array(), true);
				$comment = false;
				if (DeoHelper::getConfig('BLOG_ITEM_COMMENT_ENGINE') == 'local') {
					$comment = DeoBlogComment::getComments(null, null, null, null, null, null, null);
				}
				foreach ($blogs as $key => &$blog) {
					$blog = $helper->formatBlog($blog, false, $comment);
				}
			}

			$this->smarty->assign('blogs', $blogs);
			$html .= $this->display(__FILE__, 'views/templates/hook/blog/blog_recent.tpl');
		}
		
		return $html;
	}

	public function sidebarBlogTag()
	{
		$html = '';
		
		if ((int) DeoHelper::getConfig('BLOG_SHOW_ALL_TAGS')) {
			$blogs = DeoBlog::getListBlogs(null, $this->context->language->id, 1, 100000, 'date_add', 'DESC', array(), true);
			$helper = DeoBlogHelper::getInstance();

			$tags_temp = array();
			foreach ($blogs as $key => $value) {
				$tags_temp = array_merge($tags_temp, explode(",", $value['meta_keywords']));
			}
			// validate module
			unset($key);

			$tags_temp = array_unique($tags_temp);
			$tags = array();
			foreach ($tags_temp as $tag_temp) {
				$tags[] = array(
					'tag' => $tag_temp,
					'link' => $helper->getBlogTagLink($tag_temp)
				);
			}
			
			$this->smarty->assign('tags', $tags);
			$html .= $this->display(__FILE__, 'views/templates/hook/blog/blog_tags.tpl');
		}
		
		return $html;
	}


	/**
	 * Hook ModuleRoutes
	 */
	public function processHookModuleRoutesBlog($route = '', $detail = array())
	{
		// Configuration::deleteByName('PS_ROUTE_module-deoblog-list');
		// Configuration::deleteByName('PS_ROUTE_module-deoblog-blog');
		// Configuration::deleteByName('PS_ROUTE_module-deoblog-category');

		$routes = array();

		$link_rewrite = DeoHelper::getConfig('BLOG_LINK_REWRITE');
		if ($link_rewrite){
			$routes['module-deotemplate-bloghomepage'] = array(
				'controller' => 'bloghomepage',
				'rule' => $link_rewrite.'.html',
				'keywords' => array(
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'deotemplate'
				)
			);

			if (Tools::getIsset('configure') && Tools::getValue('configure') == 'gsitemap') {
				return $routes;
			}

			$rule_blog = $link_rewrite.'/{rewrite}.html';
			$rule_category = $link_rewrite.'/category/{rewrite}.html';
			if ((int) DeoHelper::getConfig('BLOG_URL_USE_ID')) {
			   $rule_blog = $link_rewrite.'/{id}/{rewrite}.html';
			   $rule_category = $link_rewrite.'/category/{id}/{rewrite}.html';
			}

			$routes['module-deotemplate-blog'] = array(
				'controller' => 'blog',
				'rule' => $rule_blog,
				'keywords' => array(
					'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
					'rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'rewrite'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'deotemplate',
					
				)
			);

			$routes['module-deotemplate-blogcategory'] = array(
				'controller' => 'blogcategory',
				'rule' => $rule_category,
				'keywords' => array(
					'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
					'rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'rewrite'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'deotemplate',
							
				)
			);
		}

		return $routes;
	}












	public function processHookHeaderQuickLogin()
	{
		if ((int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE')) {

			// update new direction for media
			// $this->context->controller->addCSS(__PS_BASE_URI__.DeoHelper::getCssDir().'social-login.css');
			// $this->context->controller->addJS(__PS_BASE_URI__.DeoHelper::getJsDir().'social-login.js');
			$uri = DeoHelper::getCssDir().'social-login.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$uri = DeoHelper::getJsDir().'social-login.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

			$deo_variables_social_login = array(
				'term_check' => $this->l('Please read terms and condition and agree with our terms and condition'),
				'processing_text' => $this->l('Processing. Please wait!'),
				'email_valid' => $this->l('Email is not valid'),
				'email_required' => $this->l('Email is required'),
				'password_required' => $this->l('Password is required'),
				'password_long' => $this->l('Password is at least 5 characters long'),
				'password_repeat' => $this->l('Repeat password is not same with password'),
				'firstname_required' => $this->l('First name is required'),
				'lastname_required' => $this->l('Last name is required'),
				'enable_redirect' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_REDIRECT'),
				'check_terms' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_CHECK_TERMS'),
				'myaccount_url' => $this->context->link->getPageLink('my-account', true),
				'module_dir' => $this->_path,
				'is_gen_rtl' => $this->is_gen_rtl,
			);

			Media::addJsDef(array(
				'deo_confirm_delete_account' => $this->l('Are you want to delete account? All account informations has been deleted in our store'),
				'deo_redirect_url' => Context::getContext()->link->getPageLink('index', true),
				'deo_url_ajax_social_login' => $this->context->link->getModuleLink('deotemplate', 'sociallogin'),
				'deo_variables_social_login' => $deo_variables_social_login,
			));

			// check cookie if enable or exist
			if (isset($this->context->cookie->customer_last_activity) && (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_CHECK_COOKIE') && (int) DeoHelper::getConfig('SOCIAL_LOGIN_LIFETIME_COOKIE') != 0) {
				$lifetime_cookie = (int) DeoHelper::getConfig('SOCIAL_LOGIN_LIFETIME_COOKIE') * 60;

				if ($this->context->cookie->customer_last_activity + $lifetime_cookie < time()) {
					$this->context->customer->mylogout();
				} else {
					$this->context->cookie->customer_last_activity = time();
				}
			}


			// load lib social login when not login
			// if (!$this->context->customer->isLogged()) {
			// 	$lang_locale = $this->context->language->locale;
			// 	if ($lang_locale != '') {
			// 		if (Tools::strpos($lang_locale, 'ar-') !== false) {
			// 			$lang_locale = 'ar_AR';
			// 		} else if (Tools::strpos($lang_locale, 'es-') !== false) {
			// 			$lang_locale = 'es_ES';
			// 		} else {
			// 			$lang_locale = str_replace('-', '_', $lang_locale);
			// 		}
			// 	} else {
			// 		$lang_locale = 'en_US';
			// 	}

			// 	$this->context->smarty->assign(array(
			// 		'lang_locale' => $lang_locale,
			// 		'fb_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_ENABLE'),
			// 		'fb_app_id' => DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_APPID'),
			// 		'google_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_ENABLE'),
			// 		'google_client_id' => DeoHelper::getConfigName('SOCIAL_LOGIN_GOOGLE_CLIENTID'),
			// 		'twitter_enable' => (int)DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_ENABLE'),
			// 		'twitter_api_key' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APIKEY'),
			// 		'twitter_api_secret' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APISECRET'),
			// 	));
			// }
		}
	}

	public function hookActionCustomerLogoutAfter()
	{
		// remove cookie if exist
		if (isset($this->context->cookie->customer_last_activity)) {
			unset($this->context->cookie->customer_last_activity);
		}

		// remove cookie of twitter
		if (isset($this->context->cookie->twitter_token)) {
			unset($this->context->cookie->twitter_token);
		}

		if (isset($this->context->cookie->twitter_token_secret)) {
			unset($this->context->cookie->twitter_token_secret);
		}
	}

	// create html of sliderbar and popup to end of body
	public function hookDisplayBeforeBodyClosingTag($params)
	{	
		$output = '';
		if ((int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE') && !$this->context->customer->isLogged()) {
			$output .= $this->buildModal();
			$output .= $this->buildSlideBar();
			$output .= $this->buildModalSocial();

			$lang_locale = $this->context->language->locale;
			if ($lang_locale != '') {
				if (Tools::strpos($lang_locale, 'ar-') !== false) {
					$lang_locale = 'ar_AR';
				} else if (Tools::strpos($lang_locale, 'es-') !== false) {
					$lang_locale = 'es_ES';
				} else {
					$lang_locale = str_replace('-', '_', $lang_locale);
				}
			} else {
				$lang_locale = 'en_US';
			}

			$this->context->smarty->assign(array(
				'lang_locale' => $lang_locale,
				'fb_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_ENABLE'),
				'fb_app_id' => DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_APPID'),
				'google_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_ENABLE'),
				'google_client_id' => DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_CLIENTID'),
				'twitter_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_ENABLE'),
				'twitter_api_key' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APIKEY'),
				'twitter_api_secret' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APISECRET'),
			));
			
			$output .= $this->display(__FILE__, 'social-login/social.tpl');

		}

		return $output;
	}

	// setup for social login
	public function hookDisplayAfterBodyOpeningTag($params)
	{
		// $output = '';
		// if ((int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE') && !$this->context->customer->isLogged()) {
		// 	$lang_locale = $this->context->language->locale;
		// 	if ($lang_locale != '') {
		// 		if (Tools::strpos($lang_locale, 'ar-') !== false) {
		// 			$lang_locale = 'ar_AR';
		// 		} else if (Tools::strpos($lang_locale, 'es-') !== false) {
		// 			$lang_locale = 'es_ES';
		// 		} else {
		// 			$lang_locale = str_replace('-', '_', $lang_locale);
		// 		}
		// 	} else {
		// 		$lang_locale = 'en_US';
		// 	}

		// 	$this->context->smarty->assign(array(
		// 		'lang_locale' => $lang_locale,
		// 		'fb_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_ENABLE'),
		// 		'fb_app_id' => DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_APPID'),
		// 		'google_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_ENABLE'),
		// 		'google_client_id' => DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_CLIENTID'),
		// 		'twitter_enable' => (int)DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_ENABLE'),
		// 		'twitter_api_key' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APIKEY'),
		// 		'twitter_api_secret' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APISECRET'),
		// 	));
		// 	$output .= $this->display(__FILE__, 'social-login/social.tpl');

		// }

		// return $output;
	}

	// display social login in login page
	public function hookDisplayCustomerLoginFormAfter()
	{
		$output_social = '';
		if ((int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE') && (int) DeoHelper::getConfig('SOCIAL_LOGIN_AT_LOGIN_PAGE')) {
			$this->context->smarty->assign(array(
				'fb_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_ENABLE'),
				'fb_app_id' => DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_APPID'),
				'google_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_ENABLE'),
				'google_client_id' => DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_CLIENTID'),
				'twitter_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_ENABLE'),
				'twitter_api_key' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APIKEY'),
				'twitter_api_secret' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APISECRET'),
				'login_page' => 1,
			));

			$output_social = $this->display(__FILE__, 'social-login/sociallogin_form.tpl');
			return $output_social;
		}
	}

	// render modal cart popup
	public function buildQuickLoginForm($layout, $type = '', $enable_sociallogin = 1)
	{
		$output = '';
		if ((int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE')) {
			$social = '';
			if ($enable_sociallogin) {
				$this->context->smarty->assign(array(
					'fb_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_ENABLE'),
					'fb_app_id' => DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_APPID'),
					'google_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_ENABLE'),
					'google_client_id' => DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_CLIENTID'),
					'twitter_enable' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_ENABLE'),
					'twitter_api_key' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APIKEY'),
					'twitter_api_secret' => DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APISECRET'),
					'login_page' => 0,
				));

				$social = $this->display(__FILE__, 'social-login/sociallogin_form.tpl');
			}

			$link_term = '';
			if ((int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_CHECK_TERMS')){
				$cms = new CMS((int) DeoHelper::getConfig('SOCIAL_LOGIN_LINK_TERMS'), Context::getContext()->language->id, Context::getContext()->shop->id);
				$link_term = Context::getContext()->link->getCMSLink($cms, $cms->link_rewrite, (bool) Configuration::get('PS_SSL_ENABLED'));
			}

			$this->context->smarty->assign(array(
				'layout' => $layout,
				'type' => $type,
				'social' => $social,
				'link_term' => $link_term,
				'check_terms' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_CHECK_TERMS'),
				'check_cookie' => (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_CHECK_COOKIE'),
			));

			$output = $this->display(__FILE__, 'social-login/form.tpl');
			
		}

		return $output;
	}

	// build html modal for popup type
	public function buildModal()
	{
		$this->smarty->assign(array(
			'html_form' => $this->buildQuickLoginForm('both')
		));
		$output = $this->display(__FILE__, 'social-login/modal.tpl');

		return $output;
	}

	// build html modal for popup type
	public function buildModalSocial()
	{
		$output = $this->display(__FILE__, 'social-login/modal_social.tpl');

		return $output;
	}

	// build html for slidebar type
	public function buildSlideBar()
	{
		$this->smarty->assign(array(
			'html_form' => $this->buildQuickLoginForm('both')
		));
		$output = $this->display(__FILE__, 'social-login/sidebar.tpl');

		return $output;
	}


	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function processHookHeaderFeature()
	{
		$page_name = DeoHelper::getPageName();
		// fix correct token when guest checkout
		$deo_token = Tools::getToken(false);
		Media::addJsDef(array(
			'deo_token' => $deo_token,
		));
		// update new direction for media
		// $this->context->controller->addCSS(__PS_BASE_URI__.DeoHelper::getCssDir().'feature.css');
		$uri = DeoHelper::getCssDir().'feature.css';
		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
		
		if ($page_name == 'orderconfirmation'){           
			if ($this->context->customer->is_guest) {               
				/* If guest we clear the cookie for security reason */
				$deo_token = Tools::hash(false);
			}       
		}
		
		// ajax cart
		$params_ajax_cart = $this->profile_param['ajax_cart'];
		if (!Configuration::isCatalogMode() && (int) DeoHelper::getConfig('ENABLE_AJAX_CART') && ($params_ajax_cart['enable_dropdown_defaultcart'] || $params_ajax_cart['enable_dropdown_flycart']) && $page_name != 'order' && $page_name != 'cart' && $page_name != 'orderconfirmation'){

			$uri = DeoHelper::getCssDir().'ajax-cart.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$uri = DeoHelper::getCssDir(false).'feature/jquery.mCustomScrollbar.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$uri = DeoHelper::getJsDir().'feature/cart.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$uri = DeoHelper::getJsDir().'feature/jquery.mousewheel.min.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			$uri = DeoHelper::getJsDir().'feature/jquery.mCustomScrollbar.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

			$deo_variables_ajax_cart = array(
				'enable_dropdown_defaultcart' => (int) $params_ajax_cart['enable_dropdown_defaultcart'],
				'type_dropdown_defaultcart' => $params_ajax_cart['type_dropdown_defaultcart'],
				'enable_dropdown_flycart' => (int) $params_ajax_cart['enable_dropdown_flycart'],
				'type_dropdown_flycart' => $params_ajax_cart['type_dropdown_flycart'],
				'enable_overlay_background_flycart' => (int) $params_ajax_cart['enable_overlay_background_flycart'],
				'show_popup_after_add_to_cart' => (int) $params_ajax_cart['show_popup_after_add_to_cart'],
				'open_advance_cart_after_add_to_cart' => (int) $params_ajax_cart['open_advance_cart_after_add_to_cart'],
				'position_vertical_flycart' => $params_ajax_cart['position_vertical_flycart'],
				'position_vertical_value_flycart' => $params_ajax_cart['position_vertical_value_flycart'],
				'position_horizontal_flycart' => $params_ajax_cart['position_horizontal_flycart'],
				'position_horizontal_value_flycart' => $params_ajax_cart['position_horizontal_value_flycart'],
				'enable_update_quantity' => (int) DeoHelper::getConfig('ENABLE_UPDATE_QUANTITY'),
				'show_combination' => (int) DeoHelper::getConfig('SHOW_COMBINATION'),
				'show_customization' => (int) DeoHelper::getConfig('SHOW_CUSTOMIZATION'),
				'type_effect_flycart' => DeoHelper::getConfig('TYPE_EFFECT_FLYCART'),
				// 'width_cart_item' => (int) DeoHelper::getConfig('WIDTH_CART_ITEM'),
				// 'height_cart_item' => (int) DeoHelper::getConfig('HEIGHT_CART_ITEM'),
				'number_cartitem_display' => (int) DeoHelper::getConfig('NUMBER_CART_ITEM_DISPLAY'),
				'enable_notification' => (int) DeoHelper::getConfig('ENABLE_NOTIFICATION'),
				// 'horizontal_position_notification' => DeoHelper::getConfig('POSITION_HORIZONTAL_NOTIFICATION'),
				// 'horizontal_position_value_notification' => DeoHelper::getConfig('POSITION_HORIZONTAL_VALUE_NOTIFICATION'),
				// 'vertical_position_notification' => DeoHelper::getConfig('POSITION_VERTICAL_NOTIFICATION'),
				// 'vertical_position_value_notification' => DeoHelper::getConfig('POSITION_VERTICAL_VALUE_NOTIFICATION'),
				// 'width_notification_notification' => DeoHelper::getConfig('WIDTH_NOTIFICATION'),

				'notification_update_success' => $this->l('The product').' <strong class="deo-special"></strong> '.$this->l('has been updated in your shopping cart'),
				'notification_delete_success' => $this->l('The product').' <strong class="deo-special"></strong> '.$this->l('has been removed from your shopping cart'),
				'notification_add_success' => $this->l('The product').' <strong class="deo-special"></strong> '.$this->l('successfully added to your shopping cart'),
				'notification_update_error' => $this->l('Error updating'),
				'notification_delete_error' => $this->l('Error deleting'),
				'notification_add_error' => $this->l('Error adding. Please go to product detail page and try again'),
				'notification_min_error' => $this->l('The minimum purchase order quantity for the product is').' <strong class="deo-special"></strong>',
				'notification_max_error' => $this->l('There are not enough products in stock'),
				'notification_check_warning' => $this->l('You must enter a quantity'),
			);

			Media::addJsDef(array(
				'ps_stock_management' => (int) Configuration::get('PS_STOCK_MANAGEMENT'),
				'ps_order_out_of_stock' => (int) Configuration::get('PS_ORDER_OUT_OF_STOCK'),
				'deo_variables_ajax_cart' => $deo_variables_ajax_cart,
				'deo_url_ajax_cart' => $this->context->link->getModuleLink('deotemplate', 'cart'),
				'add_cart_error' => $this->l('An error occurred while processing your request. Please try again'),
			));
		}

		// compare
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_COMPARE') && (int) DeoHelper::getConfig('COMPARATOR_MAX_ITEM') > 0) {
			$this->context->controller->addJS(DeoHelper::getJsDir().'feature/compare.js');

			$uri = DeoHelper::getCssDir().'compare.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			// add javascript param for compare
			$compared_products = array();
			if ((int) DeoHelper::getConfig('COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare)) {
				$compared_products = DeoCompareProduct::getDeoCompareProducts($this->context->cookie->id_compare);
			}

			$comparator_max_item = (int) DeoHelper::getConfig('COMPARATOR_MAX_ITEM');

			$productcompare_max_item = sprintf($this->l('You cannot add more than %d product(s) to the product comparison'), $comparator_max_item);
			$deo_variables_compare = array(
				'productcompare_add' => $this->l('The product has been added to list compare'),
				'productcompare_viewlistcompare' => $this->l('Click here to view list compare '),
				'productcompare_remove' => $this->l('The product was successfully removed from list compare'),
				'productcompare_add_error' => $this->l('An error occurred while adding. Please try again'),
				'productcompare_remove_error' => $this->l('An error occurred while removing. Please try again'),
				'comparator_max_item' => $comparator_max_item,
				'compared_products' => (count($compared_products) > 0) ? $compared_products : array(),
				'productcompare_max_item' => $productcompare_max_item,
				'buttoncompare_title_add' => $this->l('Add to Compare'),
				'buttoncompare_title_remove' => $this->l('Remove from Compare'),
			);
			Media::addJsDef(array(
				'deo_url_compare' => $this->context->link->getModuleLink('deotemplate', 'compare'),
				'deo_variables_compare' => $deo_variables_compare,
			));
		}

		// wishlist
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST')) {
			$uri = DeoHelper::getCssDir().'wishlist.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$this->context->controller->addJS(DeoHelper::getJsDir().'feature/wishlist.js');
			// add javascript param for wishlist
			if ($this->context->customer->isLogged()) {
				$isLogged = true;
			} else {
				$isLogged = false;
			}

			$deo_variables_wishlist = array(
				'wishlist_url' => $this->context->link->getModuleLink('deotemplate', 'mywishlist'),
				'wishlist_add' => $this->l('The product was successfully added to your wishlist'),
				'1' => $this->l('Click here to view your wishlist'),
				'wishlist_remove' => $this->l('The product was successfully removed from your wishlist'),
				// 'wishlist_products' => (count($this->array_wishlist_product)>0) ? $this->array_wishlist_product : array(),
				'buttonwishlist_title_add' => $this->l('Add to Wishlist'),
				'buttonwishlist_title_remove' => $this->l('Remove from WishList'),
				'wishlist_loggin_required' => $this->l('You must be logged in to manage your wishlist'),
				'isLogged' => $isLogged,
				'wishlist_view_wishlist' => $this->l('Click here to view wishlist'),
				'wishlist_quantity_required' => $this->l('You must enter a quantity'),
			);

			Media::addJsDef(array(
				'deo_url_ajax_wishlist' => $this->context->link->getModuleLink('deotemplate', 'mywishlist'),
				'deo_variables_wishlist' => $deo_variables_wishlist,
			));
		}

		// rewview
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_REVIEWS')) {
			$uri = DeoHelper::getCssDir().'review.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			if (!(int) Module::isEnabled('productcomments')){
				$this->context->controller->addJS(DeoHelper::getJsDir().'feature/jquery.rating.js');
			}
			$this->context->controller->addJS(DeoHelper::getJsDir().'feature/review.js');
			$deo_variables_review = array(
				'report_txt' => $this->l('Report success!'),
				'useful_txt' => $this->l('Review sussess'),
				'login_required_txt' => $this->l('Please login to use this functions.'),
				'cancel_rating_txt' => $this->l('Cancel rating'),
				'disable_review_form_txt' => $this->l('Not exists a criterion to review for this product or this language'),
				'review_error' => $this->l('An error occurred while processing your request. Please try again'),
			);

			Media::addJsDef(array(
				'deo_url_ajax_review' => $this->context->link->getModuleLink('deotemplate', 'review'),
				'deo_variables_review' => $deo_variables_review,
			));
		}
	}

	// display count product sold
	public function hookdisplayDeoCountSold($params)
	{
		if (!Configuration::isCatalogMode()) {
			$product = $params['product'];
			# FIX 1.7.5.0
			if(is_object($product) && method_exists($product, 'jsonSerialize')){
				$product = $product->jsonSerialize();
			}

			if ($product['quantity_all_versions'] > 0){
				$id_product = $product['id_product'];
				$total_sales = ProductSale::getNbrSales($id_product);
				$count_sold = ($total_sales > 0) ? $total_sales : 0;
				$percent_sold = ($count_sold) ? round(($count_sold/$product['quantity_all_versions'])*100,1).'%' : '0%';

				$templateVars = array(
					'product' => $product,
					'count_sold' => $count_sold,
					'percent_sold' => $percent_sold,
				);

				$this->context->smarty->assign($templateVars);
				return $this->fetch('module:deotemplate/views/templates/hook/products/count_sold.tpl');
			}
		
		}
	}

	// display button add to cart at product list
	public function hookdisplayDeoCartButton($params)
	{
		if (!Configuration::isCatalogMode()) {
			if ((int) DeoHelper::getConfig('ENABLE_AJAX_CART')) {
				// $presenter = new ProductPresenter();
				$product = $params['product'];
				if (DeoHelper::getPageName() == 'order' || DeoHelper::getPageName() == 'cart' || DeoHelper::getPageName() == 'orderconfirmation'){
					$this->link_cart = $product['url'];
				}else{
					$ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
					$this->link_cart = $this->context->link->getPageLink('cart', $ssl);
				}

				
				# FIX 1.7.5.0
				if(is_object($product) && method_exists($product, 'jsonSerialize'))
				{
					$product = $product->jsonSerialize();
				}
			
				$id_product = $product['id_product'];
				if ($this->shouldEnableAddToCartButton($product)) {
					$product['add_to_cart_url'] = $this->getAddToCartURL($product);
				} else {
					$product['add_to_cart_url'] = null;
				}

				if ($product['customizable']) {
					$customization_datas = $this->context->cart->getProductCustomization($id_product, null, true);
				}

				$product['id_customization'] = empty($customization_datas) ? null : $customization_datas[0]['id_customization'];

				// // fix for some case have not
				// if (!isset($product['product_attribute_minimal_quantity'])) {
				// 	// print_r('test');die();
				// 	$product['product_attribute_minimal_quantity'] = Attribute::getAttributeMinimalQty($product['id_product_attribute']);
				// }

				$templateVars = array(
					'static_token' => Tools::getToken(false),
					'product' => $product,
					'link_cart' => $this->link_cart,
					'show_input_quantity' => 0, // FIX SOME CUSTOMER GET BUG - undefined index: show_input_quantity
				);

				$this->context->smarty->assign($templateVars);
				return $this->fetch('module:deotemplate/views/templates/hook/products/add_to_cart.tpl');
			}
		}
	}

	/**
	 * show product_attribute at product list
	 * @param show_name_attribute => show name attribute or not
	 * @param show_value_text => show value attribute by text or button radio 
	 * @param show_color => show color by text = 0 || show color by color or image = 1 
	 * insert this code in tpl : {hook h='displayDeoProductAtribute' product=$product}
	 */
	public function hookDisplayDeoProductAtribute($params)
	{
		if (!Combination::isFeatureActive()) {
			return array();
		}

		$show_color = (isset($params['show_color']) && $params['show_color'] == "true") ? 1 : 0;
		$show_value_text = (isset($params['show_value_text']) && $params['show_value_text'] == "true") ? 1 : 0;
		$show_name_attribute = (isset($params['show_name_attribute']) && $params['show_name_attribute'] == "true") ? 1 : 0;
		
		$id_product = $params['product']['id'];
		
		include_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoFeatureProduct.php');
		$deo_feature_product = new DeoFeatureProduct();
		$groups = $deo_feature_product->getAtributeList($id_product);

		$this->context->smarty->assign(array(
			'product' => $params['product'],
			'groups' => $groups,
			'show_color' => $show_color,
			'show_value_text' => $show_value_text,
			'show_name_attribute' => $show_name_attribute,
			'PS_DISPLAY_UNAVAILABLE_ATTR'	=> (int) Configuration::get('PS_DISP_UNAVAILABLE_ATTR'),
		));

		return $this->fetch('module:deotemplate/views/templates/hook/products/attribute.tpl');
	}

	// display quanlity at product list
	public function hookdisplayDeoCartQuantity($params)
	{
		if (!Configuration::isCatalogMode()) {
			$product = $params['product'];
			$show_label_quantity = (isset($params['show_label_quantity']) && $params['show_label_quantity'] == "true") ? 1 : 0;
			if ((int) DeoHelper::getConfig('ENABLE_AJAX_CART')) {

				$templateVars = array(
					'show_label_quantity' => $show_label_quantity,
					'product' => $product,
					'PS_STOCK_MANAGEMENT' => (int) Configuration::get('PS_STOCK_MANAGEMENT'),
            		'PS_ORDER_OUT_OF_STOCK' => (int) Configuration::get('PS_ORDER_OUT_OF_STOCK'),
				);

				$this->context->smarty->assign($templateVars);
				return $this->fetch('module:deotemplate/views/templates/hook/products/quantity.tpl');
			}
		}
	}

	// display combination at product list
	public function hookdisplayDeoCartCombination($params)
	{
		if (!Configuration::isCatalogMode()) {
			$product = $params['product'];
			if (count($product['attributes']) > 0 && (int) DeoHelper::getConfig('ENABLE_AJAX_CART')) {				
				include_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoFeatureProduct.php');
				$deo_feature_product = new DeoFeatureProduct();
				$product = $deo_feature_product->getCombinations($product);

				$templateVars = array(
					'product' => $product,
					'PS_DISPLAY_UNAVAILABLE_ATTR'	=> (int) Configuration::get('PS_DISP_UNAVAILABLE_ATTR'),
				);

				$this->context->smarty->assign($templateVars);
				return $this->fetch('module:deotemplate/views/templates/hook/products/combination.tpl');
			}
		}
	}

	// display reviews at product detail
	public function hookdisplayDeoProductReviewExtra($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_REVIEWS')) {
			$product = $params['product'];
			$id_product = $product['id_product'];
			$cache_id = $this->getCacheId($id_product);
			$templateFile = 'module:deotemplate/views/templates/hook/feature/product_review_extra.tpl';
			
			if (!$this->isCached( $templateFile, $cache_id))
			{
				$id_guest = (!$id_customer = (int) $this->context->cookie->id_customer) ? (int) $this->context->cookie->id_guest : false;
				// validate module
				unset($id_customer);
				$customerReview = DeoProductReview::getByCustomer((int) $id_product, (int) $this->context->cookie->id_customer, true, (int) $id_guest);

				$average = DeoProductReview::getAverageGrade((int) $id_product);

				$this->context->smarty->assign(array(
					// 'secure_key' => $this->secure_key,
					// 'logged' => $this->context->customer->isLogged(true),
					'allow_guests_extra' => (int) DeoHelper::getConfig('PRODUCT_REVIEWS_ALLOW_GUESTS'),
					//'criterions' => DeoProductReviewCriterion::getByProduct((int) $id_product, $this->context->language->id),
					'averageTotal_extra' => $average['grade'],
					'ratings_extra' => DeoProductReview::getRatings((int) $id_product),
					'too_early_extra' => ($customerReview && (strtotime($customerReview['date_add']) + (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MINIMAL_TIME')) > time()),
					'nbReviews_product_extra' => (int) (DeoProductReview::getReviewNumber((int) $id_product)),
					'id_deofeature_product_review_extra' => $id_product,
					'link_product_review_extra' => $product['link'],
				));
			}
			return $this->fetch($templateFile, $cache_id);
		}
	}

	// display reviews on review tab at product detail
	public function hookdisplayDeoProductPageReviewContent($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_REVIEWS')) {
			$product = $params['product'];
			$id_product = $product['id_product'];

			$id_guest = (!$id_customer = (int) $this->context->cookie->id_customer) ? (int) $this->context->cookie->id_guest : false;
			# validate module
			unset($id_customer);
			$customerReview = DeoProductReview::getByCustomer((int) $id_product, (int) $this->context->cookie->id_customer, true, (int) $id_guest);

			$this->context->smarty->assign(array(
				'reviews' => DeoProductReview::getByProduct((int) $id_product, 1, null, $this->context->cookie->id_customer),
				'allow_guests' => (int) DeoHelper::getConfig('PRODUCT_REVIEWS_ALLOW_GUESTS'),
				'too_early' => ($customerReview && (strtotime($customerReview['date_add']) + (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MINIMAL_TIME')) > time()),
				'allow_report_button' => (int) DeoHelper::getConfig('PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON'),
				'allow_usefull_button' => (int) DeoHelper::getConfig('PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON'),
				'id_product_tab_content' => $id_product,
				'link_product_tab_content' => $product['link'],
			));

			return $this->fetch('module:deotemplate/views/templates/hook/feature/product_page_review.tpl');
		}
	}

	// display review of product at product list
	public function hookdisplayDeoProductListReview($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_REVIEWS')) {
			$product = $params['product'];
			$id_product = $product['id_product'];
			$show_count = (isset($params['show_count']) && $params['show_count'] == "true") ? 1 : 0;
			$show_text_count = (isset($params['show_text_count']) && $params['show_text_count'] == "true") ? 1 : 0;
			$show_zero_review = (isset($params['show_zero_review']) && $params['show_zero_review'] == "true") ? 1 : 0;
			$cache_id = $this->getCacheId($id_product);
			$templateFile = 'module:deotemplate/views/templates/hook/products/review.tpl';

			if (!$this->isCached($templateFile, $cache_id))
			{
				$average = DeoProductReview::getAverageGrade($id_product);
				$this->smarty->assign(array(
					// 'product' => $product,
					'show_count' => $show_count,
					'show_text_count' => $show_text_count,
					'show_zero_review' => $show_zero_review,
					'averageTotal' => $average['grade'],
					'ratings' => DeoProductReview::getRatings($id_product),
					'nbReviews' => (int) DeoProductReview::getReviewNumber($id_product),
				));
			}

			// return $this->fetch($templateFile);
			return $this->fetch($templateFile, $cache_id);
		}
	}

	// display compare button at product list
	public function hookdisplayDeoCompareButton($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_COMPARE') && (int) DeoHelper::getConfig('COMPARATOR_MAX_ITEM') > 0) {
			$page_name = DeoHelper::getPageName();
			$id_product = $params['product']['id_product'];
			$compared_products = array();
			if ((int) DeoHelper::getConfig('COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare)) {
				$compared_products = DeoCompareProduct::getDeoCompareProducts($this->context->cookie->id_compare);
			}
			$added = false;
			if (count($compared_products) > 0 && in_array($id_product, $compared_products)) {
				$added = true;
			}
			$this->smarty->assign(array(
				'added' => $added,
				'id_product' => $id_product,
			));

			return $this->fetch('module:deotemplate/views/templates/hook/products/compare.tpl');
		}
	}

	// display review at compare page
	public function hookdisplayDeoProducReviewCompare($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_REVIEWS')) {
			$list_grades = array();
			$list_product_grades = array();
			$list_product_average = array();
			$list_product_review = array();

			foreach ($params['list_product'] as $id_product) {
				$id_product = (int) $id_product;
				$grades = DeoProductReview::getAveragesByProduct($id_product, $this->context->language->id);
				$criterions = DeoProductReviewCriterion::getByProduct($id_product, $this->context->language->id);
				$grade_total = 0;
				if (count($grades) > 0) {
					foreach ($criterions as $criterion) {
						if (isset($grades[$criterion['id_deofeature_product_review_criterion']])) {
							$list_product_grades[$criterion['id_deofeature_product_review_criterion']][$id_product] = $grades[$criterion['id_deofeature_product_review_criterion']];
							$grade_total += (float) ($grades[$criterion['id_deofeature_product_review_criterion']]);
						} else {
							$list_product_grades[$criterion['id_deofeature_product_review_criterion']][$id_product] = 0;
						}

						if (!array_key_exists($criterion['id_deofeature_product_review_criterion'], $list_grades)) {
							$list_grades[$criterion['id_deofeature_product_review_criterion']] = $criterion['name'];
						}
					}

					$list_product_average[$id_product] = $grade_total / count($criterions);
					$list_product_review[$id_product] = DeoProductReview::getByProduct($id_product, 0, 3);
				}
			}

			if (count($list_grades) < 1) {
				return false;
			}

			$this->context->smarty->assign(array(
				'grades' => $list_grades,
				'product_grades' => $list_product_grades,
				'list_ids_product' => $params['list_product'],
				'list_product_average' => $list_product_average,
				'product_reviews' => $list_product_review,
			));

			return $this->fetch('module:deotemplate/views/templates/hook/feature/review_compare_page.tpl');
		}
	}

	// display wishlist button
	public function hookdisplayDeoWishlistButton($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST')) {
			$page_name = DeoHelper::getPageName();
			$wishlists = array();
			$wishlists_added = array();
			$id_wishlist = false;
			$added_wishlist = false;
			$id_product = $params['product']['id_product'];
			$id_product_attribute = $params['product']['id_product_attribute'];
			if ($this->context->customer->isLogged()) {
				$wishlists = DeoWishList::getByIdCustomer($this->context->customer->id);
				if (empty($this->context->cookie->id_wishlist) === true ||
						DeoWishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false) {
					if (!count($wishlists)) {
						$id_wishlist = false;
					} else {
						$id_wishlist = (int) $wishlists[0]['id_wishlist'];
						$this->context->cookie->id_wishlist = (int) $id_wishlist;
					}
				} else {
					$id_wishlist = $this->context->cookie->id_wishlist;
				}

				$wishlist_products = ($id_wishlist == false ? array() : DeoWishList::getSimpleProductByIdCustomer($this->context->customer->id, $this->context->shop->id));

				$check_product_added = array('id_product' => $id_product, 'id_product_attribute' => $id_product_attribute);

				foreach ($wishlist_products as $key => $wishlist_products_val) {
					if (in_array($check_product_added, $wishlist_products_val)) {
						$added_wishlist = true;
						$wishlists_added[] = $key;
					}
				}
			}

			$this->smarty->assign(array(
				'wishlists_added' => $wishlists_added,
				'wishlists' => $wishlists,
				'added_wishlist' => $added_wishlist,
				'id_wishlist' => $id_wishlist,
				'id_product' => $id_product,
				'id_product_attribute' => $id_product_attribute,
			));

			return $this->fetch('module:deotemplate/views/templates/hook/products/wishlist.tpl');
			
		}
	}

	// add mywishlist link to page my account
	public function hookdisplayCustomerAccount($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST')) {
			$this->context->smarty->assign(array(
				'wishlist_link' => $this->context->link->getModuleLink('deotemplate', 'mywishlist'),
			));
		}

		return $this->display(__FILE__, 'feature/wishlist_link.tpl');
	}

	// copy function from base
	public function shouldEnableAddToCartButton(array $product)
	{
		if (($product['customizable'] == 2 || !empty($product['customization_required']))) {
			$shouldShowButton = false;

			if (isset($product['customizations'])) {
				$shouldShowButton = true;
				foreach ($product['customizations']['fields'] as $field) {
					if ($field['required'] && !$field['is_customized']) {
						$shouldShowButton = false;
					}
				}
			}
		} else {
			$shouldShowButton = true;
		}

		$shouldShowButton = $shouldShowButton && $this->shouldShowAddToCartButton($product);

		if ($product['quantity'] <= 0 && !$product['allow_oosp']) {
			$shouldShowButton = false;
		}

		return $shouldShowButton;
	}

	// copy function from base
	public function shouldShowAddToCartButton($product)
	{
		return (bool) $product['available_for_order'];
	}

	// copy function from base
	public function getAddToCartURL(array $product)
	{
		return $this->context->link->getAddToCartURL($product['id_product'], $product['id_product_attribute']);
	}

	// get list attribute of product
	public function getAttributesResume($id_product, $id_lang, $attribute_value_separator = ' - ', $attribute_separator = ', ')
	{
		if (!Combination::isFeatureActive()) {
			return array();
		}

		$combinations = Db::getInstance()->executeS('SELECT pa.*, product_attribute_shop.*
				FROM `' . _DB_PREFIX_ . 'product_attribute` pa
				' . Shop::addSqlAssociation('product_attribute', 'pa') . '
				WHERE pa.`id_product` = ' . (int) $id_product . '
				GROUP BY pa.`id_product_attribute`');

		if (!$combinations) {
			return false;
		}

		$product_attributes = array();
		foreach ($combinations as $combination) {
			$product_attributes[] = (int) $combination['id_product_attribute'];
		}

		$lang = Db::getInstance()->executeS('SELECT pac.id_product_attribute, GROUP_CONCAT(agl.`name`, \'' . pSQL($attribute_value_separator) . '\',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \'' . pSQL($attribute_separator) . '\') as attribute_designation
				FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
				WHERE pac.id_product_attribute IN (' . implode(',', $product_attributes) . ')
				GROUP BY pac.id_product_attribute');

		foreach ($lang as $k => $row) {
			$combinations[$k]['attribute_designation'] = $row['attribute_designation'];
		}

		//Get quantity of each variations
		foreach ($combinations as $key => $row) {
			$cache_key = $row['id_product'] . '_' . $row['id_product_attribute'] . '_quantity';

			if (!Cache::isStored($cache_key)) {
				$result = StockAvailable::getQuantityAvailableByProduct($row['id_product'], $row['id_product_attribute']);
				Cache::store($cache_key, $result);
				$combinations[$key]['quantity'] = $result;
			} else {
				$combinations[$key]['quantity'] = Cache::retrieve($cache_key);
			}
		}

		return $combinations;
	}

	// get list attribute of product inherit to attribute of its
	public function getProductAttributeWhitelist()
	{
		return array(
			"customizable",
			"available_for_order",
			"customization_required",
			"customizations",
			"allow_oosp",
		);
	}




	// Onepagecheckout
	public function processHeaderOnepagecheckout()
	{	
		$ret = '';
		if ((int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') || (!(int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') && Tools::getIsset('checkout_with_opc'))) {
			// include assets to manipulate content on separate payment page
			if (Tools::getIsset(HelperOnepagecheckout::SEPARATE_PAYMENT_KEY_NAME)) {
				$uri = DeoHelper::getJsDir().'onepagecheckout/separate-payment.js';
				$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

				$uri = DeoHelper::getCssDir().'onepagecheckout/separate-payment.css';
				$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

				$formatted_addresses = array(
					'invoice'  => AddressFormat::generateAddress(new Address($this->context->cart->id_address_invoice),
						array(), '<br>'),
					'delivery' => AddressFormat::generateAddress(new Address($this->context->cart->id_address_delivery),
						array(), '<br>'),
				);

				if (version_compare(_PS_VERSION_, '1.7.3') >= 0) {
					// We need checkout session to read delivery_message
					$deliveryOptionsFinder = new DeliveryOptionsFinder(
						$this->context,
						$this->getTranslator(),
						new ObjectPresenter(),
						new PriceFormatter()
					);

					$session = new CheckoutSession(
						$this->context,
						$deliveryOptionsFinder
					);

					$delivery_message = html_entity_decode($session->getMessage());

				} else {
					$delivery_message = '';
				}

				if (file_exists(_PS_SHIP_IMG_DIR_ . $this->context->cart->id_carrier . '.jpg')) {
					$shipping_logo = _THEME_SHIP_DIR_ . $this->context->cart->id_carrier . '.jpg';
				} else {
					$shipping_logo = false;
				}

				$this->context->smarty->assign(array(
					'formatted_addresses' => $formatted_addresses,
					'shipping_method'     => new Carrier($this->context->cart->id_carrier),
					'shipping_logo'       => $shipping_logo,
					'delivery_message'    => $delivery_message,
					'amazon_ongoing_session' => (class_exists('AmazonPayHelper') && AmazonPayHelper::isAmazonPayCheckout())
				));

				$ret .= $this->context->smarty->fetch('module:deotemplate/views/templates/front/onepagecheckout/_partials/separate-payment.tpl');
			}

		}
		return $ret;
	}

	public function hookActionDispatcher($params = null)
	{
		// Stop-by only for Order and Cart controllers
		if ("OrderController" !== $params['controller_class'] && "CartController" !== $params['controller_class']) {
			return false;
		}

		// Show separate payment page, if this $_GET param is set
		if (Tools::getIsset(HelperOnepagecheckout::SEPARATE_PAYMENT_KEY_NAME) && $this->context->customer->id) {
			return false;
		}

		if (!DeoHelper::getConfig('USE_ONEPAGECHECKOUT_MOBILE') && $this->context->isMobile()){
			return false;
		}

		if ((int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') || (!(int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') && Tools::getIsset('checkout_with_opc'))) {
			
			if ("CartController" === $params['controller_class']) {
				if (Tools::getValue('action') === "show") {
					Tools::redirect('index.php?controller=order');
					exit;
				} else {
					return false;
				}
			}

			$frontControllerDependencies = array(
				'classes/Onepagecheckout/DeoCheckoutFormField.php',
				'classes/Onepagecheckout/DeoCheckoutAddressFormatter.php',
				'classes/Onepagecheckout/DeoCheckoutCustomerFormatter.php',
				'classes/Onepagecheckout/DeoCheckoutAddressForm.php',
				'classes/Onepagecheckout/DeoCheckoutCustomerForm.php',
				'classes/Onepagecheckout/DeoCheckoutCustomerAddressPersister.php',
				'classes/Onepagecheckout/DeoCheckoutCustomerPersister.php',
				'controllers/front/onepagecheckout.php',
			);
			foreach ($frontControllerDependencies as $dependency) {
				if (!$this->includeDependency($dependency)) {
					echo "*** ERROR ***  cannot include ($dependency) file, it's missing or corrupted!";
					exit;
				}
			}

			$checkoutController = new DeoTemplateOnepagecheckoutModuleFrontController();
			$checkoutController->run();

			exit;
		}else{
			return false;
		}

	}

	public function includeDependency($path)
	{
		if (file_exists(_PS_MODULE_DIR_ . $this->name . '/' . $path)) {
			include_once(_PS_MODULE_DIR_ . $this->name . '/' . $path);
			return true;
		} else {
			return false;
		}
	}

	public function hookDisplayOrderConfirmation($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') || (!(int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') && Tools::getIsset('checkout_with_opc'))) {
			if ((int) DeoHelper::getConfig('CLEAN_CHECKOUT_SESSION_AFTER_CONFIRMATION')) {
				unset($this->context->cookie->opc_form_checkboxes);
				unset($this->context->cookie->opc_form_radios);
			}
		}
	}

	// public function hookAdditionalCustomerFormFields($params)
	// {
	// 	$requiredCheckboxes = array();
	// 	if ((int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') || (!(int) DeoHelper::getConfig('ENABLE_ONEPAGECHECKOUT') && Tools::getIsset('checkout_with_opc'))) {
	// 		if (isset($params['get-deo-required-checkboxes']) && $params['get-deo-required-checkboxes']) {
	// 			if ('' != trim(DeoHelper::getConfig('REQUIRED_CHECKBOX_1'))) {
	// 				$requiredCheckboxes[] = (new FormField())
	// 					->setName('required-checkbox-1')
	// 					->setType('checkbox')
	// 					->setLabel(DeoHelper::getConfig('REQUIRED_CHECKBOX_1'))
	// 					->setRequired(true);
	// 			}
	// 			if ('' != trim(DeoHelper::getConfig('REQUIRED_CHECKBOX_2'))) {
	// 				$requiredCheckboxes[] = (new FormField())
	// 					->setName('required-checkbox-2')
	// 					->setType('checkbox')
	// 					->setLabel(DeoHelper::getConfig('REQUIRED_CHECKBOX_2'))
	// 					->setRequired(true);
	// 			}
	// 		}
	// 	}
	// 	return $requiredCheckboxes;
	// }









	// build shortcode by hook
	public function hookDisplayDeoSC($params)
	{
		if (isset($params['sc_key']) && $params['sc_key'] != '') {
			return $this->processShortCode($params['sc_key']);
		}
	}

	// build shortcode by embedded in content
	public function buildShortCode($content)
	{
		// validate module
		$result = preg_replace_callback(
			'~\[DeoSC(.*?)\[\/DeoSC\]~',
			function ($matches_tmp) {
				preg_match_all("~sc_key=(.*?)\]~", $matches_tmp[1], $tmp);
				return self::processShortCode($tmp[1][0]);
			},
			$content
		);
		return $result;
	}

	// get list short code for tinymce
	public function getListShortCodeForEditor()
	{
		$this->smarty->smarty->assign(array(
			'js_dir' => _PS_JS_DIR_,
			'deotemplate_module_dir' => $this->_path,
			'shortcode_url_add' => DeoHelper::get('shortcode_url_add').'&adddeotemplate_shortcode',
			'shortcode_url' => DeoHelper::get('shortcode_url_add'),
			'list_shortcode' => DeoTemplateShortcodeModel::getListShortCode(),
		));
		return $this->display(__FILE__, 'list_shortcode.tpl');
	}

	private function processShortCode($shortcode_key)
	{
		$disable_cache = false;
		if (!Configuration::get('PS_SMARTY_CACHE')) {
			$disable_cache = true;
		}

		$cache_id = $this->getCacheId('deoshortcode', $shortcode_key);
		if ($disable_cache || !$this->isCached($this->templateFile, $cache_id)) {
			$shortcode_html = '';
			$shortcode_obj = DeoTemplateShortcodeModel::getShortCode($shortcode_key);
			if (isset($shortcode_obj['id_deotemplate']) && $shortcode_obj['id_deotemplate'] != '' && $shortcode_obj['id_deotemplate'] != 0) {
				$shortcode_code = DeoTemplateShortcodeModel::getAllItems($shortcode_obj['id_deotemplate'], 1);
				
				if (!empty($shortcode_code)) {
					if (empty(DeoShortCodesBuilder::$shortcode_tags)) {
						DeoHelper::loadShortCode(_PS_THEME_DIR_);
					}
					
					DeoHelper::setGlobalVariable($this->context);
					
					// DeoShortCodesBuilder::$is_front_office = 1;
					// DeoShortCodesBuilder::$is_gen_html = 1;
					// DeoShortCodesBuilder::$profile_param = array();
					$deo_helper = new DeoShortCodesBuilder();
					// DeoShortCodesBuilder::$hook_name = 'deoshortcode';
					
					$shortcode_html = $deo_helper->parse($shortcode_code['deoshortcode']);
				}
			}
			$this->smarty->assign(array('deo_html_content' => $shortcode_html));
		}
		return $this->display(__FILE__, 'deotemplate.tpl', $cache_id);
	}



	public function renderWidget($hookName = null, array $configuration = [])
	{
		if (!isset($this->profile_data['params'])) {
			return '';
		}
			
		$disable_cache = false;
		//some hook need disable cache get from config of profile
		$disable_cache_hook = isset($this->profile_param['disable_cache_hook']) ? $this->profile_param['disable_cache_hook'] : DeoSetting::getCacheHook(3);
		if (isset($disable_cache_hook[$hookName]) && $disable_cache_hook[$hookName]) {
			$disable_cache = true;
		}
		//disable cache when submit newletter
		if (Tools::isSubmit('submitNewsletter')) {
			$disable_cache = true;
		}
		//disable cache
		if (!Configuration::get('PS_SMARTY_CACHE')) {
			$disable_cache = true;
		}

		// echo "<pre>";
		// print_r($assign['formAtts']);
		// echo "</pre>";
	
		// run without cache no create cache
		if ($disable_cache) {
			$deo_html_content = $this->getWidgetVariables($hookName, $configuration);
			
			$this->smarty->assign(array('deo_html_content' => $deo_html_content));
			return $this->fetch($this->templateFile);
		} else {
			$cache_id = $this->getCacheId($hookName);
			if (!$this->isCached($this->templateFile, $cache_id)) {
				$this->smarty->assign(array('deo_html_content' => $this->getWidgetVariables($hookName, $configuration)));
			}
			return $this->fetch($this->templateFile, $cache_id);
		}
	}

	public function getWidgetVariables($hookName = null, array $configuration = [])
	{
		// validate module
		unset($configuration);

		$model = new DeoTemplateModel();
		//get all data from all hook
		if (!$this->hook_index_data) {
			$this->hook_index_data = $model->getAllItems($this->profile_data, 1, $this->default_language['id_lang']);
		}
		if (!isset($this->hook_index_data[$hookName]) || trim($this->hook_index_data[$hookName]) == '') {
			return '';
		}
		//convert short code to html
		return $model->parseData($hookName, $this->hook_index_data[$hookName], $this->profile_param);
	}


	// private function processHook($hook_name, $params = 'null')
	// {
	// 	$cache_id = null;
	// 	$disable_cache_hook = isset($this->profile_param['disable_cache_hook']) ? $this->profile_param['disable_cache_hook'] : DeoSetting::getCacheHook(3);
	// 	$disable_cache = false;
	// 	if (isset($disable_cache_hook[$hook_name]) && $disable_cache_hook[$hook_name]) {
	// 		$disable_cache = true;
	// 	}
	// 	if (Tools::isSubmit('submitNewsletter')) {
	// 		$disable_cache = true;
	// 	}
	// 	if (!Configuration::get('PS_SMARTY_CACHE')) {
	// 		$disable_cache = true;
	// 	}

	// 	$cache_id = $this->getCacheId($hook_name);
	// 	if ($disable_cache || !$this->isCached($this->templateFile, $cache_id)) {
	// 		if ($disable_cache) {
	// 			$cache_id = null;
	// 		}
	// 		$model = new DeoTemplateModel();
	// 		if (!$this->hook_index_data) {
	// 			// $this->hook_index_data = $model->getAllItems($this->profile_data, 1, $this->default_language['id_lang'], $this->data_template);
	// 			$this->hook_index_data = $model->getAllItems($this->profile_data, 1, $this->default_language['id_lang']);
	// 		}
	// 		if (!isset($this->hook_index_data[$hook_name]) || trim($this->hook_index_data[$hook_name]) == '') {
	// 			# NOT DATA BUT SET VARIABLE TO SET CACHE
	// 			$this->smarty->assign(array('deo_html_content' => ''));
	// 			return $this->fetch( $this->templateFile, $cache_id);
	// 		}
	// 		$deo_html_content = $model->parseData($hook_name, $this->hook_index_data[$hook_name], $this->profile_param);

	// 		// foreach (DeoSetting::getPositionsName() as $position){
	// 		// 	if (in_array($hook_name, DeoSetting::getHook($position))){
	// 		// 		$position_name = $position;
	// 		// 		break;
	// 		// 	}
	// 		// }
	// 		// $id_deotemplate_positions = $this->profile_data[$position_name];
	// 		// // $id_deotemplate_positions = $this->profile_data['header'];
			
	// 		// $id_lang = (int) $this->context->language->id;
	// 		// $name_position_params = DeoHelper::getConfigName($hook_name.'_'.$id_deotemplate_positions.'_'.Language::getIsoById($id_lang));
	// 		// // $name_position_params = DeoHelper::getConfigName('DISPLAYTOP_14_EN');
	// 		// $data = DeoHelper::get($name_position_params);
	// 		// $data = (isset($data) && $data) ? json_decode($data, true) : array();
	// 		// $deo_html_content = $model->parseJsonToHtml($data);


	// 		$this->smarty->assign(array('deo_html_content' => $deo_html_content));
	// 	}

	// 	return $this->fetch( $this->templateFile, $cache_id);
	// }

	

	// public function hookDisplayDeoHeaderMobile($params)
	// {
	// 	return $this->processHook('displayDeoHeaderMobile', $params);
	// }

	// public function hookDisplayDeoNavMobile($params)
	// {
	// 	return $this->processHook('displayDeoNavMobile', $params);
	// }

	// public function hookDisplayDeoContentMobile($params)
	// {
	// 	return $this->processHook('displayDeoContentMobile', $params);
	// }

	// public function hookDisplayDeoFooterMobile($params)
	// {
	// 	return $this->processHook('displayDeoFooterMobile', $params);
	// }
	
	// public function hookDisplayBanner($params)
	// {
	// 	return $this->processHook('displayBanner', $params);
	// }

	// public function hookDisplayNav1($params)
	// {
	// 	return $this->processHook('displayNav1', $params);
	// }

	// public function hookDisplayNav2($params)
	// {
	// 	return $this->processHook('displayNav2', $params);
	// }

	// public function hookDisplayNavFullWidth($params)
	// {
	// 	return $this->processHook('displayNavFullWidth', $params);
	// }

	// public function hookDisplayTop($params)
	// {
	// 	return $this->processHook('displayTop', $params);
	// }

	// public function hookDisplaySlideshow($params)
	// {
	// 	return $this->processHook('displaySlideshow', $params);
	// }

	public function hookDisplayRightColumn($params)
	{
		$html = '';
		if ((int) DeoHelper::getConfig('ENABLE_BLOG')) {
			$html .= $this->sidebarBlogCategoryTree();
			$html .= $this->sidebarPopularBlog();
			$html .= $this->sidebarRecentBlog();
			$html .= $this->sidebarBlogTag();
		}

		return $html;
	}

	public function hookDisplayLeftColumn($params)
	{
		$html = '';
		if ((int) DeoHelper::getConfig('ENABLE_BLOG')) {
			$html .= $this->sidebarBlogCategoryTree();
			$html .= $this->sidebarPopularBlog();
			$html .= $this->sidebarRecentBlog();
			$html .= $this->sidebarBlogTag();
		}

		return $html;
	}

	// public function hookDisplayHome($params)
	// {
	// 	return $this->processHook('displayHome', $params);
	// }

	// public function hookDisplayFooterBefore($params)
	// {
	// 	return $this->processHook('displayFooterBefore', $params);
	// }

	// public function hookDisplayFooter($params)
	// {
	// 	return $this->processHook('displayFooter', $params);//.$this->header_content;
	// }

	// public function hookDisplayFooterAfter($params)
	// {
	// 	return $this->processHook('displayFooterAfter', $params);
	// }

	// public function hookDisplayFooterProduct($params)
	// {
	// 	return $this->processHook('displayFooterProduct', $params);
	// }

	public function hookDisplayRightColumnProduct($params)
	{
		$html = '';
		if ((int) DeoHelper::getConfig('ENABLE_BLOG')) {
			$html .= $this->sidebarBlogCategoryTree();
			$html .= $this->sidebarPopularBlog();
			$html .= $this->sidebarRecentBlog();
			$html .= $this->sidebarBlogTag();
		}

		return $html;
	}

	public function hookDisplayLeftColumnProduct($params)
	{
		$html = '';
		if ((int) DeoHelper::getConfig('ENABLE_BLOG')) {
			$html .= $this->sidebarBlogCategoryTree();
			$html .= $this->sidebarPopularBlog();
			$html .= $this->sidebarRecentBlog();
			$html .= $this->sidebarBlogTag();
		}

		return $html;
	}
	
	// public function hookDisplayProductAdditionalInfo($params)
	// {
	// 	return $this->processHook('displayProductAdditionalInfo', $params);
	// }
	
	// public function hookDisplayReassurance($params)
	// {
	// 	return $this->processHook('displayReassurance', $params);
	// }


	public function hookDisplayDeoProfileProduct($params)
	{
		DeoHelper::setGlobalVariable($this->context);
		$html = '';
		$tpl_file = '';

		// if (isset($params['typeProduct']) && $params['typeProduct'] == 'onepagecheckout') {
		// 	$id_shop = Context::getContext()->shop->id;
		// 	// get layout of one page check out by parameter in URL
		// 	if (Tools::getValue('layout')) {
		// 		$sql = 'SELECT a.`plist_key`, a.`fullwidth`, a.`params`, a.`class_checkout` FROM `'._DB_PREFIX_.'deotemplate_onepagecheckout` AS a INNER JOIN `'._DB_PREFIX_.'deotemplate_onepagecheckout_shop` AS b ON a.`id_deotemplate_onepagecheckout` = b.`id_deotemplate_onepagecheckout' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND a.`plist_key` = "'.pSQL(Tools::getValue('layout')).'"';
		// 		$result = Db::getInstance()->getRow($sql);
		// 	}
		// 	// get default
		// 	else{
		// 		$sql = 'SELECT a.`plist_key`, a.`fullwidth`, a.`params`, a.`class_checkout` FROM `'._DB_PREFIX_.'deotemplate_onepagecheckout` AS a INNER JOIN `'._DB_PREFIX_.'deotemplate_onepagecheckout_shop` AS b ON a.`id_deotemplate_onepagecheckout` = b.`id_deotemplate_onepagecheckout' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND b.`active` = 1';
		// 		$result = Db::getInstance()->getRow($sql);
		// 	}
		// 	if (isset($result)) {
		// 		$param = json_decode($result['params'], true);
		// 		$class_in_param = (isset($param['class'])) ? $param['class'] : '';
		// 		$class = array($class_in_param, $result['class_checkout']);
		// 		$class_checkout = implode(" ",$class);

		// 		$uri = DeoHelper::getCssDir().'onepagecheckout/'.$result['plist_key'].'.css';
		// 		$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

		// 		$uri = DeoHelper::getJsDir().'onepagecheckout/'.$result['plist_key'].'.js';
		// 		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
				
		// 		$this->smarty->smarty->assign('class_checkout', $class_checkout);
		// 	}

		// 	$tpl_file = DeoHelper::getConfigDir('theme_onepagecheckout') . $result['plist_key'].'.tpl';

		// 	$html .= Context::getContext()->smarty->fetch($tpl_file);
		// 	return $html;
		// }
		
		if (isset($params['ony_global_variable'])) {
			# {hook h='displayDeoProfileProduct' ony_global_variable=true}
			return $html;
		}

		if (!isset($params['product'])) {
			return 'Not exist product to load template';
		} else if (isset($params['profile'])) {
			# {hook h='displayDeoProfileProduct' product=$product profile=$productProfileDefault}
			$tpl_file = DeoHelper::getConfigDir('theme_products') . $params['profile'].'.tpl';
		} else if (isset($params['load_file'])) {
			# {hook h='displayDeoProfileProduct' product=$product load_file='templates/catalog/_partials/miniatures/product.tpl'}
			$tpl_file = _PS_ALL_THEMES_DIR_.$this->theme_name.'/' . $params['load_file'];
		} else if (isset($params['typeProduct']) && $params['typeProduct'] == 'detail') {
			// load default product tpl when do not have product profile
			if ($params['product']['productLayout'] != '') {
				$tpl_file = DeoHelper::getConfigDir('theme_details') . $params['product']['productLayout'].'.tpl';
			} else {
				$tpl_file = _PS_ALL_THEMES_DIR_.$this->theme_name.'/templates/catalog/product.tpl';
			}
		}

		if (empty($tpl_file)) {
			return 'Not exist profile to load template';
		}

		Context::getContext()->smarty->assign(array(
			'product' => $params['product'],
		));
		$html .= Context::getContext()->smarty->fetch($tpl_file);
		return $html;
	}

	public function hookDisplayDeoPanelTool($params)
	{
		$product_lists = $product_pages = $skins = $customize = array();
		$panelTool = DeoHelper::getConfig('PANELTOOL');
		if ($panelTool) {
			$id_shop = $this->context->shop->id;
			// get product detail layout
			$sql = 'SELECT a.* FROM `'._DB_PREFIX_.'deotemplate_details` as a
					INNER JOIN `'._DB_PREFIX_.'deotemplate_details_shop` ps ON (ps.`id_deotemplate_details` = a.`id_deotemplate_details`) WHERE ps.id_shop='.(int)$id_shop;
			$product_pages = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

			// get one page checkout layout
			$sql = 'SELECT a.* FROM `'._DB_PREFIX_.'deotemplate_onepagecheckout` as a
					INNER JOIN `'._DB_PREFIX_.'deotemplate_onepagecheckout_shop` ps ON (ps.`id_deotemplate_onepagecheckout` = a.`id_deotemplate_onepagecheckout`) WHERE ps.id_shop='.(int)$id_shop;
			$onepagecheckout_pages = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


			// get product lists
			$sql = 'SELECT p.* FROM `'._DB_PREFIX_.'deotemplate_products` p '
					.' INNER JOIN `'._DB_PREFIX_.'deotemplate_products_shop` ps '
					.' ON (ps.`id_deotemplate_products` = p.`id_deotemplate_products`)'
					.' WHERE p.`demo` = 1 AND ps.id_shop='.(int)$id_shop;
			$product_lists = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			
			// get skin
			$skins = DeoFrameworkHelper::getSkins();

			// get blog styles
			$blog_styles = DeoFrameworkHelper::getBlogStyles();

			// get plist_key detail default
			if (DeoHelper::getPageName() == 'product'){
				$detail_default = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT a.`plist_key` FROM `'._DB_PREFIX_.'deotemplate_details` AS a INNER JOIN `'._DB_PREFIX_.'deotemplate_details_shop` AS b ON a.`id_deotemplate_details` = b.`id_deotemplate_details' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND b.`active` = 1');
				if (isset($detail_default)){
					$this->context->smarty->assign('detail_default', $detail_default);
				}
			}

			// get plist_key detail default
			if (DeoHelper::getPageName() == 'order'){
				$onepagecheckout_default = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT a.`plist_key` FROM `'._DB_PREFIX_.'deotemplate_onepagecheckout` AS a INNER JOIN `'._DB_PREFIX_.'deotemplate_onepagecheckout_shop` AS b ON a.`id_deotemplate_onepagecheckout` = b.`id_deotemplate_onepagecheckout' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND b.`active` = 1');
				if (isset($onepagecheckout_default)){
					$this->context->smarty->assign('onepagecheckout_default', $onepagecheckout_default);
				}
			}

			// get product list default
			if (DeoHelper::getPageName() == 'category'){
				$product_list_default = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT p.`plist_key` FROM `'._DB_PREFIX_.'deotemplate_products` p '
					.' INNER JOIN `'._DB_PREFIX_.'deotemplate_products_shop` ps '
					.' ON (ps.`id_deotemplate_products` = p.`id_deotemplate_products`)'
					.' WHERE ps.id_shop="'.(int)$id_shop.'" AND ps.`active` = 1');
				if (isset($product_list_default)){
					$this->context->smarty->assign('product_list_default', $product_list_default);
				}
			}

			// get customize
			$profile = $this->profile_data;
			$pfile = new DeoTemplateProfilesModel();
			$id_positions = array($this->profile_data['content'], $this->profile_data['header'], $this->profile_data['footer']);
			$list_position = $pfile->getPositionsForProfile($id_positions);

			$data_position = array(
				'content' => '',
				'header' => '',
				'footer' => '',
			);
			foreach ($list_position as $key => $position) {
				if ($position['position'] == 'content'){
					$data_position['content'] = $position['position_key'];
				}elseif ($position['position'] == 'header'){
					$data_position['header'] = $position['position_key'];
				}elseif ($position['position'] == 'footer'){
					$data_position['footer'] = $position['position_key'];
				}
			}

			$customize = DeoFrameworkHelper::getCustomize($data_position);

			$this->context->smarty->assign(array(
				'skins' => $skins,
				'blog_styles' => $blog_styles,
				'blog_link' => new DeoBlogHelper(),
				'customize' => $customize,
				'panelTool' => $panelTool,
				'product_pages' => $product_pages,
				'onepagecheckout_pages' => $onepagecheckout_pages,
				'product_lists' => $product_lists,
				'deo_cookie_theme' => $this->themeCookieName,
			));

			return $this->fetch('module:deotemplate/views/templates/front/paneltool.tpl');
		}
	}

	public function hookActionShopDataDuplication()
	{
		$this->clearHookCache();
	}

	/**
	 * Register hook again to after install/change any theme
	 */
	public function hookActionObjectShopUpdateAfter()
	{
		// Retrieve hooks used by the module
		// $sql = 'SELECT `id_hook` FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = '.(int)$this->id;
		// $result = Db::getInstance()->executeS($sql);
		// foreach ($result as $row) {
		//    $this->unregisterHook((int)$row['id_hook']);
		//    $this->unregisterExceptions((int)$row['id_hook']);
		// }
	}
	
	/**
	 * FIX BUG 1.7.3.3 : install theme lose hook displayHome, displayDeoProfileProduct
	 * because ajax not run hookActionAdminBefore();
	 */
	public function autoRestoreSampleData()
	{
		if (Hook::isModuleRegisteredOnHook($this, 'actionAdminBefore', (int)Context::getContext()->shop->id)) {
			$theme_manager = new stdclass();
			$theme_manager->theme_manager = 'theme_manager';
			$this->hookActionAdminBefore(array(
				'controller' => $theme_manager,
			));
		}
	}
	
	/**
	 * Run only one when install/change theme
	 */
	public function hookActionAdminBefore($params)
	{
		if (isset($params) && isset($params['controller']) && isset($params['controller']->theme_manager)) {
			// Validate : call hook from theme_manager
		} else {
			// Other module call this hook -> duplicate data
			return;
		}

		$this->unregisterHook('actionAdminBefore');
		
		# FIX THEME_CHILD NOT EXIST TPL FILE -> AUTO COPY TPL FILE FROM THEME_PARENT
		$assets = Context::getContext()->shop->theme->get('assets');
		$theme_parent = Context::getContext()->shop->theme->get('parent');
		if( is_array($assets) && isset($assets['use_parent_assets']) && $assets['use_parent_assets'] && $theme_parent )
		{
			$from = _PS_ALL_THEMES_DIR_.$theme_parent.'/modules/deotemplate';
			$to =   _PS_ALL_THEMES_DIR_.DeoHelper::getInstallationThemeName().'/modules/deotemplate';
			DeoHelper::createDir($to);
			Tools::recurseCopy($from, $to);
		}
		
		# FIX : update Prestashop by 1-Click module -> NOT NEED RESTORE DATABASE
		$deo_version = Configuration::get('DEO_CURRENT_VERSION');
		if ($deo_version != false) {
			$ps_version = Configuration::get('PS_VERSION_DB');
			$versionCompare =  version_compare($deo_version, $ps_version);
			if ($versionCompare != 0) {
				// Just update Prestashop
				Configuration::updateValue('DEO_CURRENT_VERSION', $ps_version);
				return;
			}
		}
		
		# WHENE INSTALL THEME, INSERT HOOK FROM DATASAMPLE IN THEME
		$hook_from_theme = false;
		if (file_exists(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php')) {
			require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php');
			$sample = new DeoDataSample();
			if ($sample->processHook($this->name)) {
				$hook_from_theme = true;
			};
		}
		
		# INSERT HOOK FROM MODULE_DATASAMPLE
		if ($hook_from_theme == false) {
			$this->registerDeoHook();
		}

		# INSERT DATABASE FROM THEME_DATASAMPLE
		if (file_exists(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php')) {
			require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php');
			$sample = new DeoDataSample();
			$sample->processImport($this->name);
		}
		
		# REMOVE FILE INDEX.PHP FOR TRANSLATE
		if (file_exists(_PS_MODULE_DIR_.'deotemplate/libs/setup.php')) {
			require_once(_PS_MODULE_DIR_.'deotemplate/libs/setup.php');
			DeoPageSetup::processTranslateTheme();
		}

		# INSTALL SAMPLE IF NOT EXIST FOLDER samples.xml
		DeoPageSetup::installSampleModule();

	}
	
	public function getCacheId($hook_name = null, $shortcode_key = null)
	{
		$cache_array = array();
		$cache_array[] = $this->name;
		if (DeoTemplateProfilesModel::getIdProfileFromRewrite() && !$shortcode_key) {
			$cache_array[] = 'profile_'.DeoTemplateProfilesModel::getIdProfileFromRewrite();
		}

		$cache_array[] = $hook_name;
		if ($this->profile_param && isset($this->profile_param[$hook_name]) && $this->profile_param[$hook_name]) {
			//$cache_array[] = $hook_name;
			$current_page = DeoHelper::getPageName();
			//show ocurrentPagenly in controller
			if (isset($this->profile_param[$hook_name][$current_page])) {
				$cache_array[] = $current_page;
				if ($current_page != 'index' && $cache_id = DeoSetting::getControllerId($current_page, $this->profile_param[$hook_name][$current_page])) {
					$cache_array[] = $cache_id;
				}
			// } elseif (isset($this->profile_param[$hook_name]['productCarousel'])) {
			// 	$random = round(rand(1, max((int) DeoHelper::getConfig('PRODUCT_MAX_RANDOM'), null, null, 1)));
			// 	$cache_array[] = "p_carousel_$random";
			} else if (isset($this->profile_param[$hook_name]['exception']) && in_array($cache_array, $this->profile_param[$hook_name]['exception'])) {
				//show but not in controller
				$cache_array[] = $current_page;
			}
		}
		if (Configuration::get('PS_SSL_ENABLED')) {
			$cache_array[] = 'SSL_'.(int)Tools::usingSecureMode();
		}
		if (Shop::isFeatureActive()) {
			$cache_array[] = 'shop_'.(int)$this->context->shop->id;
		}
		if (Group::isFeatureActive()) {
			$cache_array[] = 'c_group_'.(int)GroupCore::getCurrent()->id;
		}
		if (Language::isMultiLanguageActivated()) {
			$cache_array[] = 'la_'.(int)$this->context->language->id;
		}
		if (Currency::isMultiCurrencyActivated()) {
			$cache_array[] = 'curcy_'.(int)$this->context->currency->id;
		}
		$cache_array[] = 'ctry_'.(int)$this->context->country->id;
		if (Tools::getValue('plist_key') ) {
			$cache_array[] = 'plist_key_'.Tools::getValue('plist_key');
		}
		if (Tools::getValue('mobile') && (in_array($hook_name, DeoSetting::getHook('mobile')) || $hook_name == 'pagebuilderConfig|mobile')) {
			$cache_array[] = 'mobile_'.Tools::getValue('mobile');
		}
		if (Tools::getValue('header') && (in_array($hook_name, DeoSetting::getHook('header')) || $hook_name == 'pagebuilderConfig|header')) {
			$cache_array[] = 'header_'.Tools::getValue('header');
		}
		if (Tools::getValue('content') && (in_array($hook_name, DeoSetting::getHook('content')) || $hook_name == 'pagebuilderConfig|content')) {
			$cache_array[] = 'content_'.Tools::getValue('content');
		}
		if (Tools::getValue('product') && (in_array($hook_name, DeoSetting::getHook('product')) || $hook_name == 'pagebuilderConfig|product')) {
			$cache_array[] = 'product_'.Tools::getValue('product');
		}
		if (Tools::getValue('footer') && (in_array($hook_name, DeoSetting::getHook('footer')) || $hook_name == 'pagebuilderConfig|footer')) {
			$cache_array[] = 'footer_'.Tools::getValue('footer');
		}
		
		// update cache for shortcode
		if ($shortcode_key) {
			$cache_array[] = 'shortcodekey_'.$shortcode_key;
		}
		
		return implode('|', $cache_array);
	}

	/**
	 * Overide function display of Module.php
	 * @param type $file
	 * @param type $template
	 * @param null $cache_id
	 * @param type $compile_id
	 * @return type
	 */
	public function display($file, $template, $cache_id = null, $compile_id = null)
	{
		if (($overloaded = Module::_isTemplateOverloadedStatic(basename($file, '.php'), $template)) === null) {
			return sprintf($this->l('No template found "%s"'), $template);
		} else {
			if (Tools::getIsset('live_edit')) {
				$cache_id = null;
			}
			$this->smarty->assign(array(
				'module_dir' => __PS_BASE_URI__.'modules/'.basename($file, '.php').'/',
				'module_template_dir' => ($overloaded ? _THEME_DIR_ : __PS_BASE_URI__).'modules/'.basename($file, '.php').'/',
				// 'allow_push' => $this->allow_push
			));
			if ($cache_id !== null) {
				Tools::enableCache();
			}
			$result = $this->getCurrentSubTemplate($template, $cache_id, $compile_id)->fetch();
			if ($cache_id !== null) {
				Tools::restoreCacheSettings();
			}
			$this->resetCurrentSubTemplate($template, $cache_id, $compile_id);
			return $result;
		}
	}

	public function clearHookCache()
	{
		# CLEAR CACHE ALL HOOKS
		$this->_clearCache($this->templateFile);   
	}
	
	// add clear cache for shortcode
	public function clearShortCodeCache($shortcode_key)
	{
		$cache_id = $this->getCacheId('deoshortcode', $shortcode_key);
		
		$this->_clearCache('deotemplate.tpl', $cache_id);
	}

	public function hookCategoryAddition()
	{
		$this->clearHookCache();
	}

	public function hookCategoryUpdate()
	{
		$this->clearHookCache();
	}

	public function hookCategoryDeletion()
	{
		$this->clearHookCache();
	}

	public function hookAddProduct()
	{
		$this->clearHookCache();
	}

	public function hookUpdateProduct()
	{
		$this->clearHookCache();
	}

	public function hookDeleteProduct()
	{
		$this->clearHookCache();
	}

	public function hookDisplayBackOfficeHeader()
	{
		// create cache for widget module if not exist
		$deo_cache_module = DeoHelper::get('DEO_CACHE_MODULE');
		if ($deo_cache_module === false || $deo_cache_module === '') {
			$list_modules = DeoHelper::getModules();

			$deo_cache_module = DeoHelper::correctEnCodeData(json_encode($list_modules));
			DeoHelper::updateValue('DEO_CACHE_MODULE', $deo_cache_module);
		}

		DeoHelper::autoUpdateModule();
		if (method_exists($this->context->controller, 'addJquery')) {
			// validate module
			$this->context->controller->addJquery();
		}

		// fix home page redirect to profile page
		if (get_class($this->context->controller) == 'AdminPsThemeCustoConfigurationController' && Context::getContext()->shop->theme_name != 'classic') {
			Media::addJsDef(
				array(
					'deo_profile_url' => $this->context->link->getAdminLink('AdminDeoProfiles'),
					'deo_profile_txt_redirect' => $this->l('Please access this link to use this feature easily'),
					'deo_check_theme_name' => Context::getContext()->shop->theme_name
				)
			);
		}

		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'fonts.css');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/general.css');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/blog.css');

		if('AdminLegacyLayoutControllerCore' != get_class($this->context->controller)){
			$this->context->controller->addJS(DeoHelper::getJsAdminDir().'admin/setting.js');
		}

		if (!DeoHelper::isRelease()) {
			Media::addJsDef(array('js_deo_dev' => 1));
		}
	}

	public function loadResouceForProfile($arr_responsive)
	{
		$profile = $this->profile_data;
		$is_mobile = $arr_responsive['deo_is_mobile'];
		$is_tablet = $arr_responsive['deo_is_tablet'];
		$header_mobile = $arr_responsive['deo_header_mobile'];
		$nav_mobile = $arr_responsive['deo_nav_mobile'];
		$content_mobile = $arr_responsive['deo_content_mobile'];
		$footer_mobile = $arr_responsive['deo_footer_mobile'];

		$arr = array();
		if ($profile['mobile'] && $is_mobile && ($header_mobile || $nav_mobile || $content_mobile || $footer_mobile)) {
			$arr[] = $profile['mobile'];
		}
		if ($profile['header'] && !($is_mobile && $header_mobile)) {
			$arr[] = $profile['header'];
		}
		if ($profile['content'] && !($is_mobile && $content_mobile)) {
			$arr[] = $profile['content'];
		}
		if ($profile['footer'] && !($is_mobile && $footer_mobile)) {
			$arr[] = $profile['footer'];
		}
		if ($profile['product']) {
			$arr[] = $profile['product'];
		}

		if (count($arr) > 0) {
			$model = new DeoTemplateProfilesModel();
			$list_positions = $model->getPositionsForProfile($arr);
			
			$page_name = DeoHelper::getPageName();
			if ($list_positions) {
				$array_css = array();
				foreach ($list_positions as $item) {
					if (!is_null($item['params'])){
						$params = json_decode($item['params'], false);
						if (is_object($params) && !($page_name != 'index' && $item['position'] == 'content')){
							# LOAD CSS MODULES AND WIDGET POSITION
							$widgets_modules = $params->widgets_modules;
							if (is_array($widgets_modules) && count($widgets_modules)){
								$css_files_available = DeoSetting::getCssFilesAvailable();
								foreach ($widgets_modules as $key => $value){
									if (in_array($value, $css_files_available['widgets_modules'])){
										if ($value == 'DeoProductTabs'){
											$uri = DeoHelper::getCssDir().'widgets_modules/DeoTabs.css';
											$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
										}else{
											$uri = DeoHelper::getCssDir().'widgets_modules/'.$value.'.css';
											$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
										}
									}

									// extend actions
									if ($value == 'DeoMegamenu'){
										// megamenu
										$link = new Link();
										$current_link = $link->getPageLink('', false, $this->context->language->id);
										$this->smarty->assign('current_link', $current_link);
										$this->header_content .= $this->display(__FILE__, 'views/templates/hook/megamenu/javascript_parameter.tpl');

										$uri = DeoHelper::getCssDir().'megamenu/typo.css';
										$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
										
										$uri = DeoHelper::getJsDir().'megamenu/megamenu.js';
										$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
									}else if ($value == 'DeoInstagram'){
										// instagram
										$uri = DeoHelper::getJsDir().'instafeed.min.js';
										$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
									}else if ($value == 'DeoAdvancedSearch'){
										// advanced search
										$uri = DeoHelper::getJsDir().'advancedsearch/advancedsearch.js';
										$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
										$uri = DeoHelper::getJsDir().'advancedsearch/advancedsearch.autocomplete.js';
										$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
									}else if ($value == 'DeoImage360'){
										// DeoImage360
										$uri = DeoHelper::getCssDir(false).'magic360.css';
										Context::getContext()->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

										$uri = DeoHelper::getCssDir(false).'magic360.module.css';
										Context::getContext()->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

										$uri = DeoHelper::getJsDir().'magic360.js';
										Context::getContext()->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));

										Media::addJsDef(array(
											'deo_image360_hint_text' => $this->l('Drag to spin'),
											'deo_image360_mobile_hint_text' => $this->l('Swipe to spin'),
										));
									}else if ($value == 'DeoGoogleMap' && (int) DeoHelper::getConfig('ENABLE_GOOGLE_MAP') && DeoHelper::getConfig('API_KEY_GOOGLE_MAP')){
										$this->deo_has_google_map = true;
									}else if ($value == 'DeoGallery'){
										$uri = DeoHelper::getJsDir().'admin/isotope.pkgd.min.js';
										Context::getContext()->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
									}
								}
							}

							# LOAD CSS ELEMENTS
							$css_files_available = DeoSetting::getCssFilesAvailable();
							$elements = false;
							if (isset($params->elements) && $params->elements){
								$elements = $params->elements;
								$elements = count($elements) ? $elements : false;
							}
							if ($elements){
								foreach ($elements as $key => $value){
									if (in_array($value, $css_files_available['elements'])){
										$uri = DeoHelper::getCssDir().'elements/'.$value.'.css';
										$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
									}
								}
							}

							# LOAD CSS PRODUCT LISTS
							if ($page_name == 'index'){
								$product_lists = false;
								if (isset($params->product_lists) && $params->product_lists){
									$product_lists = $params->product_lists;
									$product_lists = count($product_lists) ? $product_lists : false;
								}
								if ($product_lists){
									foreach ($product_lists as $key => $value){
										$uri = DeoHelper::getCssDir().'products/'.$value.'.css';
										$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));
									}
								}
							}
						}
					}

					$name = $item['position'].$item['position_key'];
					if (DeoHelper::getConfig('PANELTOOL')) {
						$uri = DeoHelper::getCssDir().'positions/'.$name.'.css';
						if (DeoHelper::checkDirFileOverrideExist($uri) && filesize(_PS_THEME_DIR_.$uri)){
							if ($uri = DeoHelper::checkFileOverrideExist($uri)){
								$array_css[] = $uri;
							}
						}

						$uri = DeoHelper::getCssDir().'customize/'.$name.'.css';
						if (DeoHelper::checkDirFileOverrideExist($uri) && filesize(_PS_THEME_DIR_.$uri)){
							$uri = DeoHelper::checkFileOverrideExist($uri);
							if ($uri && isset($this->profile_param['customize']) && $this->profile_param['customize']){
								$array_css[] = $uri;
							}
						}
					}else{
						$uri = DeoHelper::getCssDir().'positions/'.$name.'.css';
						$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 820));
						

						$uri = DeoHelper::getCssDir().'customize/'.$name.'.css';
						if (isset($this->profile_param['customize']) && $this->profile_param['customize']){
							$this->context->controller->registerStylesheet(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 820));
						}
					}


					$uri = DeoHelper::getJsDir().'positions/'.$name.'.js';
					$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
				}

				$this->smarty->smarty->assign(array(
					'deo_url_css_positions' => $array_css
				));
			}
		}

		if (DeoHelper::getConfig('PANELTOOL')) {
			$uri =  DeoHelper::getCssDir().'profiles/'.$profile['profile_key'].'.css';
			if ($uri = DeoHelper::checkFileOverrideExist($uri)){
				$this->smarty->smarty->assign('deo_url_css_profile', $uri);
			}
		}else{
			$uri = DeoHelper::getCssDir().'profiles/'.$profile['profile_key'].'.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 820));
			
		}

		$uri = DeoHelper::getJsDir().'profiles/'.$profile['profile_key'].'.js';
		$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
	}

	public function loadCssPositions()
	{
		
	}


	public function getProfileData()
	{
		return $this->profile_data;
	}


	public function hookProductMoreImg($list_pro)
	{
		//get product info
		$product_list = $this->getProducts($list_pro);

		$this->smarty->assign(array(
			'rate_images' => Tools::getValue('deo_rate_images'),
			'urls' => Tools::getValue('deo_url_no_picture_images'),
		));

		$obj = array();
		foreach ($product_list as $product) {
			$this->smarty->assign('product', $product);
			$obj[] = array('id' => $product['id_product'], 'content' => ($this->display(__FILE__, 'productMoreImage.tpl')));
		}
		return $obj;
	}

	public function hookProductOneImg($list_pro)
	{
		$protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
		$use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
		$protocol_content = ($use_ssl) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);

		$id_lang = Context::getContext()->language->id;
		$where = ' WHERE i.`id_product` IN ('.$list_pro.') AND (ish.`cover`=0 OR ish.`cover` IS NULL) AND ish.`id_shop` = '.Context::getContext()->shop->id;
		$order = ' ORDER BY i.`id_product`,`position`';
		$limit = ' LIMIT 0,1';
		//get product info
		$list_img = $this->getAllImages($id_lang, $where, $order, $limit);

		$saved_img = array();
		$obj = array();

		$image_name = 'home';
		$image_name .= '_default';
		foreach ($list_img as $product) {
			if (!in_array($product['id_product'], $saved_img)) {
				$obj[] = array(
					'id' => $product['id_product'],
					'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
					'name' => $product['name'],
					);
			}
			$saved_img[] = $product['id_product'];
		}
		return $obj;
	}

	public function hookProductCdown($countdown)
	{
		$product_list = $this->getProducts($countdown);
		$obj = array();
		foreach ($product_list as $product) {
			$this->smarty->assign('product', $product);
			$obj[] = array('id' => $product['id_product'], 'content' => ($this->display(__FILE__, 'productCountdown.tpl')));
		}
		return $obj;
	}
	
	public function hookModuleRoutes($params)
	{
		$routes = array();
		$model = new DeoTemplateProfilesModel();
		$allProfileArr = $model->getAllProfileByShop();
		foreach ($allProfileArr as $allProfileItem) {
			if (isset($allProfileItem['friendly_url']) && $allProfileItem['friendly_url']) {
				$routes['module-deotemplate-'.$allProfileItem['friendly_url']] = array(
					'controller' => 'home',
					'rule' => $allProfileItem['friendly_url'].'.html',
					'keywords' => array(
					),
					'params' => array(
						'fc' => 'module',
						'module' => 'deotemplate'
					)
				);
			}
		}

		$routes['module-deotemplate-advancedsearch'] = array(
			'controller' => 'advancedsearch',
			'rule' => 'advancedsearch.html',
			'keywords' => array(
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'deotemplate'
			)
		);

		$routes['module-deotemplate-sociallogin'] = array(
			'controller' => 'sociallogin',
			'rule' => 'sociallogin.html',
			'keywords' => array(
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'deotemplate'
			)
		);

		$routes['module-deotemplate-onepagecheckout'] = array(
			'controller' => 'onepagecheckout',
			'rule' => 'onepagecheckout.html',
			'keywords' => array(
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'deotemplate'
			)
		);

		$routes = array_merge($routes, $this->processHookModuleRoutesBlog());

		return $routes;
	}

	public function getProducts($product_list, $colors = array())
	{
		$id_lang = Context::getContext()->language->id;
		$product_list = DeoHelper::addonValidInt( $product_list );         # We validate id_categories in DeoHelper::addonValidInt function . This function is used at any where
		$context = Context::getContext();
		$id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
		$ids = Address::getCountryAndState($id_address);
		$id_country = (isset($ids['id_country']) && $ids['id_country']) ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT');
		$sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name,sp.`id_specific_price`
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
				LEFT JOIN `'._DB_PREFIX_.'specific_price` sp ON (sp.`id_product` = p.`id_product`
					AND sp.`id_shop` IN(0, '.(int)$context->shop->id.')
					AND sp.`id_currency` IN(0, '.(int)$context->currency->id.')
					AND sp.`id_country` IN(0, '.(int)$id_country.')
					AND sp.`id_group` IN(0, '.(int)$context->customer->id_default_group.')
					AND sp.`id_customer` IN(0, '.(int)$context->customer->id.')
					AND sp.`reduction` > 0
				)
				WHERE pl.`id_lang` = '.(int)$id_lang.' AND p.`id_product` in ('.pSQL($product_list).')';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if ($product_list) {
			$tmp_img = array();
			$cover_img = array();
			$where = ' WHERE i.`id_product` IN ('.pSQL($product_list).') AND ish.`id_shop` = '.Context::getContext()->shop->id;
			$order = ' ORDER BY i.`id_product`,`position`';

			$list_img = $this->getAllImages($id_lang, $where, $order);
			foreach ($list_img as $val) {
				$tmp_img[$val['id_product']][$val['id_image']] = $val;
				if ($val['cover'] == 1) {
					$cover_img[$val['id_product']] = $val['id_image'];
				}
			}
		}

		$now = date('Y-m-d H:i:s');
		$finish = $this->l('Expired');
		foreach ($result as &$val) {
			$time = false;
			if (isset($tmp_img[$val['id_product']])) {
				$val['images'] = $tmp_img[$val['id_product']];
				$val['id_image'] = $cover_img[$val['id_product']];
			} else {
				$val['images'] = array();
			}

			$val['specific_prices'] = self::getSpecificPriceById($val['id_specific_price']);
			if (isset($val['specific_prices']['from']) && $val['specific_prices']['from'] > $now) {
				$time = strtotime($val['specific_prices']['from']);
				$val['finish'] = $finish;
				$val['check_status'] = 0;
				$val['lofdate'] = Tools::displayDate($val['specific_prices']['from']);
			} elseif (isset($val['specific_prices']['to']) && $val['specific_prices']['to'] > $now) {
				$time = strtotime($val['specific_prices']['to']);
				$val['finish'] = $finish;
				$val['check_status'] = 1;
				$val['lofdate'] = Tools::displayDate($val['specific_prices']['to']);
			} elseif (isset($val['specific_prices']['to']) && $val['specific_prices']['to'] == '0000-00-00 00:00:00') {
				$val['js'] = 'unlimited';
				$val['finish'] = $this->l('Unlimited');
				$val['check_status'] = 1;
				$val['lofdate'] = $this->l('Unlimited');
			} else if (isset($val['specific_prices']['to'])) {
				$time = strtotime($val['specific_prices']['to']);
				$val['finish'] = $finish;
				$val['check_status'] = 2;
				$val['lofdate'] = Tools::displayDate($val['specific_prices']['from']);
			}
			if ($time) {
				$val['js'] = array(
					'month' => date('m', $time),
					'day' => date('d', $time),
					'year' => date('Y', $time),
					'hour' => date('H', $time),
					'minute' => date('i', $time),
					'seconds' => date('s', $time)
				);
			}
		}

		unset($colors);
		return Product::getProductsProperties($id_lang, $result);
	}

	public static function getSpecificPriceById($id_specific_price)
	{
		if (!SpecificPrice::isFeatureActive()) {
			return array();
		}

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
						SELECT *
						FROM `'._DB_PREFIX_.'specific_price` sp
						WHERE `id_specific_price` ='.(int)$id_specific_price);

		return $res;
	}

	public function getAllImages($id_lang, $where, $order)
	{
		$id_shop = Context::getContext()->shop->id;
		$sql = 'SELECT DISTINCT i.`id_product`, ish.`cover`, i.`id_image`, il.`legend`, i.`position`,pl.`link_rewrite`, pl.`name` 
				FROM `'._DB_PREFIX_.'image` i
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product`) AND pl.`id_lang` = '.(int)$id_lang.'
				LEFT JOIN `'._DB_PREFIX_.'image_shop` ish ON (ish.`id_image` = i.`id_image` AND ish.`id_shop` = '.(int)$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.pSql($where).' '.pSQL($order);
		return Db::getInstance()->executeS($sql);
	}

	// show category and tags of product
	public function hookdisplayProductInformation($params)
	{
		$return = '';
		$product_id = $params['product']->id;
		$category_id = $params['product']->id_category_default;
		$cat = new Category($category_id, $this->context->language->id);
		$product_tags = Tag::getProductTags($product_id);
		$product_tags = $product_tags[(int)$this->context->cookie->id_lang];
		$return .= '<div class =category>Category: <a href="'.$this->context->link->getCategoryLink($category_id, $cat->link_rewrite).'">'.$cat->name.'</a>.</div>';
		$return .= '<div class="producttags clearfix">';
		$return .= 'Tag: ';
		if ($product_tags && count($product_tags) > 1) {
			$count = 0;
			foreach ($product_tags as $tag) {
				$return .= '<a href="'.$this->context->link->getPageLink('search', true, null, "tag=$tag").'">'.$tag.'</a>';
				if ($count < count($product_tags) - 1) {
					$return .= ',';
				} else {
					$return .= '.';
				}
				$count++;
			}
		}
		$return .= '</div>';
		return $return;
	}
	
	/**
	 * alias from DeoHelper::getConfig()
	 */
	public function getConfigName($name)
	{
		return DeoHelper::getConfigName($name);
	}
	
	/**
	 * alias from DeoHelper::getConfig()
	 */
	public function getConfig($name)
	{
		return DeoHelper::getConfig($name);
	}
	
	/**
	 * get Value of configuration based on actived theme
	 */
	public function getPanelConfig($key, $default = '')
	{
		if (Tools::getIsset($key)) {
			# validate module
			return Tools::getValue($key);
		}

		$cookie = DeoFrameworkHelper::getCookie();
		if (isset($cookie[$this->themeCookieName.'_'.$key])) {
			return $cookie[$this->themeCookieName.'_'.$key];
		}else{
			if (DeoHelper::hasKey(DeoHelper::getConfigName($key))){
				setcookie($this->themeCookieName.'_'.$key, DeoHelper::getConfig($key), time() + (86400 * 30), '/');
			}
		}

		unset($default);
		return DeoHelper::getConfig($key);
	}

	public function generateDeoHtmlMessage()
	{
		$html = '';
		if (count($this->_confirmations)) {
			foreach ($this->_confirmations as $string) {
				$html .= $this->displayConfirmation($string);
			}
		}
		if (count($this->_errors)) {
			$html .= $this->displayError($this->_errors);
		}
		if (count($this->_warnings)) {
			$html .= $this->displayWarning($this->_warnings);
		}
		return $html;
	}

	/**
	 * Common method
	 * Resgister all hook for module
	 */
	public function registerDeoHook()
	{
		$res = true;
		$res &= $this->registerHook('header');
		$res &= $this->registerHook('actionShopDataDuplication');
		$res &= $this->registerHook('displayBackOfficeHeader');
		$res &= $this->registerHook('actionAdminControllerSetMedia');
		$res &= $this->registerHook('filterProductContent');
		$res &= $this->registerHook('filterCategoryContent');
		$res &= $this->registerHook('filterHtmlContent');
		$res &= $this->registerHook('filterCmsContent');
		$res &= $this->registerHook('filterCategoryContent');
		$res &= $this->registerHook('moduleRoutes');
		$res &= $this->registerHook('displayDeoGoogleMap');
		$res &= $this->registerHook('displayDeoHeaderMobile');
		$res &= $this->registerHook('displayDeoNavMobile');
		$res &= $this->registerHook('displayDeoContentMobile');
		$res &= $this->registerHook('displayDeoFooterMobile');

		// feature
		$res &= $this->registerHook('displayDeoCountSold');
		$res &= $this->registerHook('displayDeoCartButton');
		$res &= $this->registerHook('displayDeoCartQuantity');
		$res &= $this->registerHook('displayDeoCartCombination');
		$res &= $this->registerHook('displayDeoProductReviewExtra');
		$res &= $this->registerHook('displayDeoProductPageReviewContent');
		$res &= $this->registerHook('displayDeoProductListReview');
		$res &= $this->registerHook('displayDeoCompareButton');
		$res &= $this->registerHook('displayDeoWishlistButton');
		$res &= $this->registerHook('displayDeoProducReviewCompare');
		$res &= $this->registerHook('displayCustomerAccount');
		$res &= $this->registerHook('displayDeoProductAtribute');
		

		// blog
		$res &= $this->registerHook('displayTop');
		$res &= $this->registerHook('leftColumn');
		$res &= $this->registerHook('rightColumn');
		$res &= $this->registerHook('moduleRoutes');

		// social login
		$res &= $this->registerHook('actionCustomerLogoutAfter');
		$res &= $this->registerHook('displayBeforeBodyClosingTag');
		$res &= $this->registerHook('displayAfterBodyOpeningTag');
		$res &= $this->registerHook('displayCustomerLoginFormAfter');

		# register hook to show when paging
		$res &= $this->registerHook('pagebuilderConfig');
		
		# register hook to show category and tags of product
		$res &= $this->registerHook('displayProductInformation');
		
		# register hook again to after install/change theme
		$res &= $this->registerHook('actionObjectShopUpdateAfter');
		
		# Multishop create new shop
		$res &= $this->registerHook('actionAdminShopControllerSaveAfter');
		
		$res &= $this->registerHook('displayProductAdditionalInfo');
		$res &= $this->registerHook('displayReassurance');
		$res &= $this->registerHook('displayDeoProfileProduct');
		$res &= $this->registerHook('displayDeoPanelTool');
		
		# MoveEndHeader
		$res &= $this->registerHook('actionModuleRegisterHookAfter');

		# register hook for deoshortcode
		$res &= $this->registerHook('displayDeoSC');
		$res &= $this->registerHook('displayMaintenance');              // DeoShortCode for maintain page
		$res &= $this->registerHook('actionOutputHTMLBefore');          // DeoShortCode for maintain page
		
		// $res &= $this->registerHook('filterCmsContent');         // DeoShortCode for cms page
		// $res &= $this->registerHook('filterHtmlContent');        // DeoShortCode for manufacturer page


		# onepagecheckout
		$res &= $this->registerHook('actionDispatcher');
		$res &= $this->registerHook('displayOrderConfirmation');
		// $res &= $this->registerHook('additionalCustomerFormFields');


		foreach (DeoSetting::getHook('all') as $value) {
			$res &= $this->registerHook($value);
		}


		return true;
	}


	/**
	 * Common method
	 * Unresgister all hook for module
	 */
	public function unregisterDeoHook()
	{
		$res = true;
		$res &= $this->unregisterHook('header');
		$res &= $this->unregisterHook('actionShopDataDuplication');
		$res &= $this->unregisterHook('displayBackOfficeHeader');
		$res &= $this->unregisterHook('actionAdminControllerSetMedia');
		$res &= $this->unregisterHook('filterProductContent');
		$res &= $this->unregisterHook('filterCategoryContent');
		$res &= $this->unregisterHook('filterHtmlContent');
		$res &= $this->unregisterHook('filterCmsContent');
		$res &= $this->unregisterHook('filterCategoryContent');
		$res &= $this->unregisterHook('moduleRoutes');
		$res &= $this->unregisterHook('displayDeoGoogleMap');
		$res &= $this->unregisterHook('displayDeoHeaderMobile');
		$res &= $this->unregisterHook('displayDeoNavMobile');
		$res &= $this->unregisterHook('displayDeoContentMobile');
		$res &= $this->unregisterHook('displayDeoFooterMobile');

		// feature
		$res &= $this->unregisterHook('displayDeoCountSold');
		$res &= $this->unregisterHook('displayDeoCartButton');
		$res &= $this->unregisterHook('displayDeoCartQuantity');
		$res &= $this->unregisterHook('displayDeoCartCombination');
		$res &= $this->unregisterHook('displayDeoProductReviewExtra');
		$res &= $this->unregisterHook('displayDeoProductPageReviewContent');
		$res &= $this->unregisterHook('displayDeoProductListReview');
		$res &= $this->unregisterHook('displayDeoCompareButton');
		$res &= $this->unregisterHook('displayDeoWishlistButton');
		$res &= $this->unregisterHook('displayDeoProducReviewCompare');
		$res &= $this->unregisterHook('displayCustomerAccount');
		$res &= $this->unregisterHook('displayDeoProductAtribute');

		// blog
		$res &= $this->unregisterHook('displayTop');
		$res &= $this->unregisterHook('leftColumn');
		$res &= $this->unregisterHook('rightColumn');
		$res &= $this->unregisterHook('moduleRoutes');

		// social login
		$res &= $this->unregisterHook('actionCustomerLogoutAfter');
		$res &= $this->unregisterHook('displayBeforeBodyClosingTag');
		$res &= $this->unregisterHook('displayAfterBodyOpeningTag');
		$res &= $this->unregisterHook('displayCustomerLoginFormAfter');

		# register hook to show when paging
		$res &= $this->unregisterHook('pagebuilderConfig');
		
		# register hook to show category and tags of product
		$res &= $this->unregisterHook('displayProductInformation');
		
		# register hook again to after install/change theme
		$res &= $this->unregisterHook('actionObjectShopUpdateAfter');
		
		# Multishop create new shop
		$res &= $this->unregisterHook('actionAdminShopControllerSaveAfter');
		
		$res &= $this->unregisterHook('displayProductAdditionalInfo');
		$res &= $this->unregisterHook('displayReassurance');
		$res &= $this->unregisterHook('displayDeoProfileProduct');
		$res &= $this->unregisterHook('displayDeoPanelTool');
		# MoveEndHeader
		$res &= $this->unregisterHook('actionModuleRegisterHookAfter');

		# register hook for deoshortcode
		$res &= $this->unregisterHook('displayDeoSC');
		$res &= $this->unregisterHook('displayMaintenance');              // DeoShortCode for maintain page
		$res &= $this->unregisterHook('actionOutputHTMLBefore');          // DeoShortCode for maintain page
		
		// $res &= $this->unregisterHook('filterCmsContent');         // DeoShortCode for cms page
		// $res &= $this->unregisterHook('filterHtmlContent');        // DeoShortCode for manufacturer page


		# onepagecheckout
		$res &= $this->unregisterHook('actionDispatcher');
		$res &= $this->unregisterHook('displayOrderConfirmation');
		// $res &= $this->unregisterHook('additionalCustomerFormFields');

		foreach (DeoSetting::getHook('all') as $value) {
			$res &= $this->unregisterHook($value);
		}

		return true;
	}
	
	/**
	 * @Action Create new shop, choose theme then auto restore datasample.
	 */
	public function hookActionAdminShopControllerSaveAfter($param)
	{
		if (Tools::getIsset('controller') !== false && Tools::getValue('controller') == 'AdminShop'
				&& Tools::getIsset('submitAddshop') !== false && Tools::getValue('submitAddshop')
				&& Tools::getIsset('theme_name') !== false && Tools::getValue('theme_name')) {
			$shop = $param['return'];
			
			if (file_exists(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php')) {
				require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php');
				$sample = new DeoDataSample();
				DeoHelper::$id_shop = $shop->id;
				$sample->_id_shop = $shop->id;
				$sample->processImport('deotemplate');
			}
		}
	}


	public function hookfilterCategoryContent($params)
	{
		$params['object']['description'] = $this->buildShortCode($params['object']['description']);
		return $params;
	}


	public function hookfilterProductContent($params)
	{
		$id_shop = Context::getContext()->shop->id;
		$params['object']['description'] = $this->buildShortCode($params['object']['description']);
		$params['object']['description_short'] = $this->buildShortCode($params['object']['description_short']);

		// get layout of product detail by parameter in URL
		if (Tools::getValue('layout')) {
			$sql = 'SELECT a.`plist_key`, a.`fullwidth`, a.`params`, a.`class_detail` FROM `'._DB_PREFIX_.'deotemplate_details` AS a INNER JOIN `'._DB_PREFIX_.'deotemplate_details_shop` AS b ON a.`id_deotemplate_details` = b.`id_deotemplate_details' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND a.`plist_key` = "'.pSQL(Tools::getValue('layout')).'"';
			$result = Db::getInstance()->getRow($sql);
		}
		// get default
		else{
			$sql = 'SELECT a.`plist_key`, a.`fullwidth`, a.`params`, a.`class_detail` FROM `'._DB_PREFIX_.'deotemplate_details` AS a INNER JOIN `'._DB_PREFIX_.'deotemplate_details_shop` AS b ON a.`id_deotemplate_details` = b.`id_deotemplate_details' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND b.`active` = 1';
			$result = Db::getInstance()->getRow($sql);
		}
		if (isset($result)) {
			$params['object']['productLayout'] = $result['plist_key'];
			$params['object']['layout_fullwidth'] = $result['fullwidth'];

			$param = json_decode($result['params'], true);
			$class_in_param = (isset($param['class'])) ? $param['class'] : '';
			$class = array($class_in_param, $result['class_detail']);
			$class_detail = implode(" ",$class);

			$uri = DeoHelper::getCssDir().'details/'.$result['plist_key'].'.css';
			$this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 800));

			$uri = DeoHelper::getJsDir().'details/'.$result['plist_key'].'.js';
			$this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 800));
			
			$this->smarty->smarty->assign('class_detail', $class_detail);
		}

		return $params;
	}
	
	public function hookfilterCmsContent($params)
	{
		$params['object']['content'] = $this->buildShortCode($params['object']['content']);
		return $params;
	}
	
	public function hookfilterHtmlContent($params)
	{
		if($params['type'] == 'manufacturer'){
			$params['object']['description'] = $this->buildShortCode($params['object']['description']);
			$params['object']['short_description'] = $this->buildShortCode($params['object']['short_description']);
			return $params;
		}

		if($params['type'] == 'supplier'){
			$params['object']['description'] = $this->buildShortCode($params['object']['description']);
			return $params;
		}
	}
	
	public $is_maintain = false;
	public function hookDisplayMaintenance()
	{
		$this->is_maintain = true;
	}
	
	public function hookActionOutputHTMLBefore(&$params)
	{
		if($this->is_maintain){
			$params['html'] = $this->buildShortCode($params['html']);
		}
	}


	/**
	 * PERMISSION ACCOUNT demo@demo.com
	 */
	public function getPermission($variable, $employee = null)
	{
		if ($variable == 'configure'){
			// Allow see form if permission is : configure, view
			$configure = Module::getPermissionStatic($this->id, 'configure', $employee);
			$view = Module::getPermissionStatic($this->id, 'view', $employee);
			return ($configure || $view);
		}
		
		return Module::getPermissionStatic($this->id, $variable, $employee);
	}
	
	/**
	 * PERMISSION ACCOUNT demo@demo.com
	 */
	public function access($action)
	{
		$employee = null;
		return Module::getPermissionStatic($this->id, $action, $employee);
	}
	


	/**
	 * show product_attribute at product list
	 * @param zoom => zoom rate
	 * @param width => width size 
	 * @param height => height size 
	 * @param show_store => show stores
	 * @param not_all => show all stores
	 * insert this code in tpl : {hook h='displayDeoGoogleMap'}
	 */
	public function hookDisplayDeoGoogleMap($params)
	{
		if ((int) DeoHelper::getConfig('ENABLE_GOOGLE_MAP') && DeoHelper::getConfig('API_KEY_GOOGLE_MAP')){
			if (DeoHelper::getPageName() == 'contact' && !(int) DeoHelper::getConfig('ENABLE_GOOGLE_MAP_CONTACT_PAGE')){
				return false;
			}

			$is_display_store = (isset($params['show_store'])) ? (($params['show_store'] == "true") ? 1 : 0) : (int) DeoHelper::getConfig('ENABLE_STORE_ON_MAP_CONTACT_PAGE');
			$zoom = (isset($params['zoom'])) ? $params['zoom'] : (int) DeoHelper::getConfig('ZOOM_GOOGLE_MAP_CONTACT_PAGE');
			$width = (isset($params['width'])) ? $params['zoom'] : DeoHelper::getConfig('WIDTH_GOOGLE_MAP_CONTACT_PAGE');
			$height = (isset($params['height'])) ? $params['height'] : DeoHelper::getConfig('HEIGHT_GOOGLE_MAP_CONTACT_PAGE');
			$not_all = (isset($params['not_all'])) ? (($params['not_all'] == "true") ? 1 : 0) : (int) DeoHelper::getConfig('SHOW_SELECT_STORE_ON_MAP_CONTACT_PAGE');

			$base_model = new DeoTemplateModel();
			$data_list = $base_model->getAllStoreByShop();
			$store_ids = json_decode(DeoHelper::getConfig('LIST_STORE_CONTACT_PAGE'));

			$markers = array();
			if ($not_all) {
				foreach ($store_ids as $id) {
					foreach ($data_list as $store) {
						if ($id == $store['id_store']) {
							$markers[] = $store;
							break;
						}
					}
				}
			} else {
				$markers = $data_list;
			}

			foreach ($markers as &$marker) {
				$address = $this->processStoreAddress($marker);
				$marker['other'] = $this->renderStoreWorkingHours($marker);
				$marker['address'] = $address;
				$marker['has_store_picture'] = file_exists(_PS_STORE_IMG_DIR_.(int)$marker['id_store'].'.jpg');
			}

			$this->context->smarty->assign(array(
				'marker_list' => json_encode($markers),
				'marker_center' => json_encode($this->getMarkerCenter($markers)),
				'zoom' => $zoom,
				'width' => $width,
				'height' => $height,
				'is_display_store' => $is_display_store,
			));

			return $this->fetch('module:deotemplate/views/templates/hook/displayDeoGoogleMap.tpl');
		}
	}

	/**
	 * Get formatted string address
	 */
	public function processStoreAddress($store)
	{
		$ignore_field = array(
			'firstname',
			'lastname'
		);
		$out_datas = array();
		$address_datas = AddressFormat::getOrderedAddressFields($store['id_country'], false, true);
		$state = (isset($store['id_state'])) ? new State($store['id_state']) : null;
		foreach ($address_datas as $data_line) {
			$data_fields = explode(' ', $data_line);
			$addr_out = array();
			$data_fields_mod = false;
			foreach ($data_fields as $field_item) {
				$field_item = trim($field_item);
				if (!in_array($field_item, $ignore_field) && !empty($store[$field_item])) {
					$addr_out[] = ($field_item == 'city' && $state && isset($state->iso_code) && Tools::strlen($state->iso_code)) ?
							$store[$field_item].', '.$state->iso_code : $store[$field_item];
					$data_fields_mod = true;
				}
			}
			if ($data_fields_mod) {
				$out_datas[] = implode(' ', $addr_out);
			}
		}
		$out = implode('<br/>', $out_datas);
		return $out;
	}

	public function renderStoreWorkingHours($store)
	{
		$days = array();
		$days[1] = $this->l('Monday');
		$days[2] = $this->l('Tuesday');
		$days[3] = $this->l('Wednesday');
		$days[4] = $this->l('Thursday');
		$days[5] = $this->l('Friday');
		$days[6] = $this->l('Saturday');
		$days[7] = $this->l('Sunday');
		
		$hours_temp = $store['hours'];
		$hours_temp = json_decode($hours_temp);
		$hours = array();
		// fix when stores do not have the data of open time
		if (count($hours_temp) > 0) {
			foreach ($hours_temp as $h) {
				$hours[] = implode(' | ', $h);
			}
		}
		
		if (!empty($hours)) {
			$result = '';
			for ($i = 1; $i < 8; $i++) {
				if (isset($hours[(int)$i - 1])) {
					Context::getContext()->smarty->assign(array(
						'days' => $days,
						'hours' => $hours,
						'i' => $i,
					));
					
					$file_name = _PS_MODULE_DIR_.'deotemplate/views/templates/front/shortcodes/DeoGoogleMap.tpl';
					$result .= Context::getContext()->smarty->fetch($file_name);
				}
			}
			return $result;
		}
		return false;
	}

	public function getMarkerCenter($markers)
	{
		$lat = 24.05404;
		$long = 135.778736;
		return (is_array($markers) && count($markers) > 0) ? $markers[0] : array('latitude' => $lat, 'longitude' => $long);
	}
}
