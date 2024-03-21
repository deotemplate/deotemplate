<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoHelper
{
	public static function getInstance()
	{
		static $_instance;
		if (!$_instance) {
			$_instance = new DeoHelper();
		}
		return $_instance;
	}
	
	public static function getStrSearch()
	{
		return array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APPLUS_', '_APOCBRACKET_', '_APCCBRACKET_');
	}

	public static function getStrReplace()
	{
		return array('&', '"', '\'', '\t', '\r', '\n', '[', ']', '+', '{', '}');
	}

	public static function getStrReplaceHtml()
	{
		return array('&', '"', '\'', '    ', '', '', '[', ']', '+', '{', '}');
	}

	public static function getStrReplaceHtmlAdmin()
	{
		return array('&', '"', '\'', '    ', '', '_APNEWLINE_', '[', ']', '+', '{', '}');
	}

	public static function loadShortCode($theme_dir)
	{
		/**
		 * load source code
		 */
		if (!defined('_PS_LOAD_ALL_SHORCODE_')) {
			$source_file = Tools::scandir(_PS_MODULE_DIR_.'deotemplate/classes/shortcodes');

			foreach ($source_file as $value) {
				$fileName = basename($value, '.php');
				if ($fileName == 'index') {
					continue;
				}
				require_once(DeoSetting::requireShortCode($value, $theme_dir));
				$obj = new $fileName;
				DeoShortCodesBuilder::addShortcode($fileName, $obj);
			}
			$obj = new DeoTabs();
			DeoShortCodesBuilder::addShortcode('DeoTab', $obj);
			$obj = new DeoAccordions();
			DeoShortCodesBuilder::addShortcode('DeoAccordion', $obj);
			define('_PS_LOAD_ALL_SHORCODE_', true);
		}
	}

	public static function correctDeCodeData($data)
	{
		$functionName = 'b'.'a'.'s'.'e'.'6'.'4'.'_'.'decode';
		return call_user_func($functionName, $data);
	}

	public static function correctEnCodeData($data)
	{
		$functionName = 'b'.'a'.'s'.'e'.'6'.'4'.'_'.'encode';
		return call_user_func($functionName, $data);
	}

	public static function getModulesAccordion()
	{
		return array('ps_emailsubscription', 'ps_socialfollow', 'ps_contactinfo');
	}

	public static function getModulesClass()
	{
		return array('ps_emailsubscription', 'ps_socialfollow','ps_currencyselector', 'ps_customersignin','ps_languageselector', 'ps_searchbar','ps_shoppingcart');
	}

	public static function log($msg, $is_ren = true)
	{
		// DeoHelper::log();
		if ($is_ren) {
		//echo "\r\n$msg";
			if (!is_dir(_PS_ROOT_DIR_.'/log')) {
				@mkdir(_PS_ROOT_DIR_.'/log', 0755, true);
			}
			error_log("\r\n".date('m-d-y H:i:s', time()).': '.$msg, 3, _PS_ROOT_DIR_.'/log/deotemplate-errors.log');
		}
	}

	public static function udate($format = 'm-d-y H:i:s', $utimestamp = null)
	{
		if (is_null($utimestamp)) {
			$utimestamp = microtime(true);
		}
		$t = explode(" ", microtime());
		return date($format, $t[1]).substr((string)$t[0], 1, 4);
	}

	/**
	 * generate array to use in create helper form
	 */
	public static function getArrayOptions($ids = array(), $names = array(), $val = 1)
	{
		$res = array();
		foreach ($names as $key => $value) {
			// module validate
			unset($value);

			$res[] = array(
				'id' => $ids[$key],
				'name' => $names[$key],
				'val' => $val,
			);
		}
		return $res;
	}
	
	/**
	 * DeoHelper::getPageName()
	 * Call method to get page_name in PS v1.7.0.0
	 */
	public static function getPageName()
	{
		static $page_name;
		if (!$page_name) {
			if (!empty(Context::getContext()->controller->page_name)) {
				$page_name = Context::getContext()->controller->page_name;
			} elseif (!empty(Context::getContext()->controller->php_self)) {
				$page_name = Context::getContext()->controller->php_self;
			} elseif (preg_match('#^'.preg_quote(Context::getContext()->shop->physical_uri, '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m)) {
				$page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
			} else {
				$page_name = Dispatcher::getInstance()->getController();
				$page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
			}
		}
		
		if ($page_name == 'home' || $page_name == 'module-deotemplate-home') {
			$page_name = 'index';
		}

		return $page_name;
	}

	/**
	 * DeoHelper::getIDPageName()
	 * Call method to get page_name in PS v1.7.0.0
	 */
	public static function getIDPageName()
	{
		static $page_name_controller;
		if (!$page_name_controller) {
			$page_name_controller = Context::getContext()->controller->page_name;
		}

		return $page_name_controller;
	}

	/**
	 * DeoHelper::getLazyload()
	 * get global lazyload variable
	 */
	public static function getLazyload()
	{
		static $lazyload;

		if ((int) DeoHelper::getConfig('PANELTOOL') && !Configuration::get('PS_SMARTY_CACHE')) {
			$cookie = DeoFrameworkHelper::getCookie();
			$lazyload = (isset($cookie[DeoHelper::getConfigName('PANEL_CONFIG').'_LAZYLOAD'])) ? (int) $cookie[DeoHelper::getConfigName('PANEL_CONFIG').'_LAZYLOAD'] : (int) DeoHelper::getConfig('LAZYLOAD');
		}else{
			if (!$lazyload) {
				$lazyload = (int) DeoHelper::getConfig('LAZYLOAD');
			}
		}

		
		return $lazyload;
	}
	
	/**
	 * Set global variable for site at Frontend
	 */
	public static function setGlobalVariable($context, $profile_param = null)
	{
		static $global_variable;
		if (!$global_variable) {
			# Currency
			$currency = array();
			$fields = array('name', 'iso_code', 'iso_code_num', 'sign');
			foreach ($fields as $field_name) {
				if($context && isset($context->currency)){
					$currency[$field_name] = $context->currency->{$field_name};
				}
			}

			$global_variable = 1;
			
			$context->smarty->assign(array(
				'currency'          			=> $currency,
				'tpl_dir'             			=> DeoHelper::getThemeDir(),           
				'tpl_uri'             			=> _THEME_DIR_,
				'link' 							=> $context->link,                           
				'link_deo' 						=> $context->link,                          
				'page_name' 					=> self::getPageName(),                
				'PS_CATALOG_MODE' 				=> (int) Configuration::get('PS_CATALOG_MODE'),
				'PS_STOCK_MANAGEMENT' 			=> (int) Configuration::get('PS_STOCK_MANAGEMENT'),  
				'PS_ORDER_OUT_OF_STOCK' 		=> (int) Configuration::get('PS_ORDER_OUT_OF_STOCK'),
				'PS_DISPLAY_UNAVAILABLE_ATTR'	=> (int) Configuration::get('PS_DISP_UNAVAILABLE_ATTR'),
				'more_product_img'    			=> (int) self::getConfig('AJAX_MULTIPLE_PRODUCT_IMAGE'),     
				'second_img'   					=> (int) self::getConfig('AJAX_SECOND_PRODUCT_IMAGE'),  
				'countdown'    					=> (int) self::getConfig('AJAX_COUNTDOWN'), 
				'module_deotemplate' 			=> Module::getInstanceByName('deotemplate'), 
				'deo_lazyload' 					=> self::getLazyload(),
			));

			$context->smarty->assign(array(
				'megamenu_group_tab_active' => isset($profile_param['megamenu_group_active']) ? $profile_param['megamenu_group_active'] : false,
			));
		}
	}
	
	public static function getImgThemeUrl($folder = null)
	{
		# DeoHelper::getImgThemeUrl()
		static $img_theme_url;

		if (!$img_theme_url  || !isset($img_theme_url[$folder])) {
			// Not exit image or icon
			$folder = rtrim($folder, '/');
			if(empty($folder)){
				$img_theme_url[$folder] = DeoHelper::getThemeUri().'assets/img/'.'modules/deotemplate/';
			}else{
				$img_theme_url[$folder] = DeoHelper::getThemeUri().'assets/img/'.'modules/deotemplate/'.$folder .'/';
			}
		}
		return $img_theme_url[$folder];
	}
	
	public static function getImgThemeDir($folder = 'images', $path = '')
	{
		static $img_theme_dir;
		
		if (empty($folder)) {
			$folder = 'images';
		}
		if (empty($path)) {
			$path = 'assets/img/modules/deotemplate';
		}
		if (!$img_theme_dir || !isset($img_theme_dir[$folder])) {
			$img_theme_dir[$folder] = _PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/'.$path.'/'.$folder.'/';
		}
		return $img_theme_dir[$folder];
	}
	
	public static function getCssAdminDir()
	{
		static $css_folder;
		
		if (!$css_folder) {
			if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
				$css_folder = __PS_BASE_URI__.'modules/deotemplate/views/css/';
			}else{
				$css_folder = __PS_BASE_URI__.'modules/deotemplate/css/';
			}
		}
		
		return $css_folder;
	}
	
	public static function getCssDir($override = true)
	{
		static $css_folder;
		
		if (!$css_folder) {
			if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
				$css_folder = 'modules/deotemplate/views/css/';
			}else{
				$css_folder = 'modules/deotemplate/css/';
			}

		}
		
		if (!$override){
			return $css_folder;
		}

		if (!self::isRelease()) {
			return 'assets/css/'.$css_folder;
		}

		return $css_folder;
	}
	
	public static function getJsDir()
	{
		static $js_folder;
		
		if (!$js_folder) {
			if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
				$js_folder = 'modules/deotemplate/views/js/';
			}else{
				$js_folder = 'modules/deotemplate/js/';
			}
		}
		return $js_folder;
	}
	
	public static function getJsAdminDir()
	{
		static $js_folder;
		
		if (!$js_folder) {
			if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
				$js_folder = __PS_BASE_URI__.'modules/deotemplate/views/js/';
			}else{
				$js_folder = __PS_BASE_URI__.'modules/deotemplate/js/';
			}
		}
		return $js_folder;
	}
	
	public static function getThemeKey()
	{
		static $theme_key;
		if (!isset($theme_key)) {
			$theme_key = DeoHelper::getThemeName();
		}
		
		return $theme_key;
	}

	/**
	 * check key configuration exist
	 */
	public static function hasKey($key, $idLang = null, $idShopGroup = null, $idShop = null)
	{
        if (Shop::isFeatureActive()) {
	        if ($idShop === null) {
	            $idShop = Shop::getContextShopID(true);
	        }

	        if ($idShopGroup === null) {
	            $idShopGroup = Shop::getContextShopGroupID(true);
	        }
        }

        return Configuration::hasKey($key, $idLang, $idShopGroup, $idShop);
	}


	/**
	 * get value configuration
	 */
	public static function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
	{
		if (Shop::isFeatureActive()) {
	        if ($idShop === null) {
	            $idShop = Shop::getContextShopID(true);
	        }

	        if ($idShopGroup === null) {
	            $idShopGroup = Shop::getContextShopGroupID(true);
	        }
		}

		// var_dump($key,$idShop,$idShopGroup);
		// echo "<br>";
		if (self::hasKey($key, $idLang, $idShopGroup, $idShop)){
			$result = Configuration::get($key, $idLang, $idShopGroup, $idShop, $default);
		}else{
			$result = Configuration::get($key, $idLang, $default);
		}

		return $result;
	}


	/**
	 * get several configuration values (in one language only).
	 */
	public static function getMultiple($keys, $idLang = null, $idShopGroup = null, $idShop = null)
	{
		if (Shop::isFeatureActive()) {
	        if ($idShop === null) {
	            $idShop = Shop::getContextShopID(true);
	        }

	        if ($idShopGroup === null) {
	            $idShopGroup = Shop::getContextShopGroupID(true);
	        }
		}

	
		if (self::hasKey($key, $idLang, $idShopGroup, $idShop)){
			$result = Configuration::getMultiple($key, $idLang, $idShopGroup, $idShop);
		}else{
			$result = Configuration::getMultiple($key, $idLang);
		}

		return $result;
	}
	

	/**
	 * get update value configuration
	 */
	public static function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
	{
		if (Shop::isFeatureActive()) {
	        if ($idShop === null) {
	            $idShop = Shop::getContextShopID(true);
	        }

	        if ($idShopGroup === null) {
	            $idShopGroup = Shop::getContextShopGroupID(true);
	        }
		}

		return Configuration::updateValue($key, $values, $html, $idShopGroup, $idShop);
	}

	/**
	 * get ID configuration by key name
	 */
	public static function getIdByName($key, $idShopGroup = null, $idShop = null)
	{
		if (Shop::isFeatureActive()) {
	        if ($idShop === null) {
	            $idShop = Shop::getContextShopID(true);
	        }

	        if ($idShopGroup === null) {
	            $idShopGroup = Shop::getContextShopGroupID(true);
	        }
		}

		return Configuration::getIdByName($key, $idShopGroup, $idShop);
	}


	/**
	 * get delete configuration by ID
	 */
	public static function deleteById($key)
	{
		$id = self::getIdByName($key);
		return Configuration::deleteById($id);
	}


	/**
	 * get delete configuration by key name
	 */
	public static function deleteByName($key)
	{
		if (Shop::isFeatureActive()) {
			$id = self::getIdByName($key);
			return self::deleteById($id);
		}else{
			return Configuration::deleteByName($key);
		}
	}

	/**
	 * get configuration name with prefix DEO
	 */
	public static function getConfigName($name)
	{
		return Tools::strtoupper(self::getThemeKey().'_'.$name);
	}
	
	/**
	 * get configuration value with prefix DEO
	 */
	public static function getConfig($key, $default = null)
	{
		return self::get(self::getConfigName($key), null, null, null, $default);
	}
	
	public static function autoUpdateModule()
	{
		$module = Module::getInstanceByName('deotemplate');
		if ((int) self::getConfig('CORRECT_MOUDLE') != $module->version) {
			// Latest update DeoTemplate version
			Configuration::updateValue(DeoHelper::getConfigName('CORRECT_MOUDLE'), $module->version);
			DeoHelper::processCorrectModule();
			DeoHelper::processCorrectChildTheme();
			DeoHelper::processCopyImageChildTheme();
		}
	}
	
	public static function processCorrectModule($quickstart = false)
	{
		$instance_module = Module::getInstanceByName('deotemplate');

		$instance_module->unregisterHook('displayDeoCartAttribute');
		// $instance_module->registerHook('displayDeoProductTabContent');
		

		# update shortcode
		$instance_module->registerHook('displayDeoSC');
		$instance_module->registerHook('displayMaintenance');              // DeoShortCode for maintain page
		$instance_module->registerHook('actionOutputHTMLBefore');          // DeoShortCode for maintain page

		$instance_module->registerHook('displayDeoProductAtribute');
		$instance_module->registerHook('displayDeoCartCombination');

		$instance_module->registerHook('displayDeoGoogleMap');

		$instance_module->registerHook('displayDeoHeaderMobile');
		$instance_module->registerHook('displayDeoNavMobile');
		$instance_module->registerHook('displayDeoContentMobile');
		$instance_module->registerHook('displayDeoFooterMobile');
		$instance_module->registerHook('displayDeoCartButton');
		$instance_module->registerHook('displayDeoCountSold');

		$instance_module->registerHook('actionFrontControllerSetMedia');
		
		$instance_module->registerHook('actionDispatcher');
		$instance_module->registerHook('displayOrderConfirmation');
		// $instance_module->registerHook('additionalCustomerFormFields');

		$instance_module->registerHook('displayDeoTopLeftSidebar');
		$instance_module->registerHook('displayDeoBottomLeftSidebar');
		$instance_module->registerHook('displayDeoTopRightSidebar');
		$instance_module->registerHook('displayDeoBottomRightSidebar');

		Configuration::updateValue(DeoHelper::getConfigName('LIST_CONTENT_HOOK'), implode(',', DeoSetting::getHook('content')));
		Configuration::updateValue(DeoHelper::getConfigName('LIST_MOBILE_HOOK'), implode(',', DeoSetting::getHook('mobile')));

		Configuration::updateValue(DeoHelper::getConfigName('SHORTCODE_WIDGETS_MODULES'), json_encode(array()));
		Configuration::updateValue(DeoHelper::getConfigName('SHORTCODE_ELEMENTS'), json_encode(array()));
		Configuration::updateValue(DeoHelper::getConfigName('SHORTCODE_PRODUCT_LISTS'), json_encode(array()));

		# update tab translate
		self::addTabController('AdminDeoOnepagecheckout','One Page Checkout Builder', 5);
		self::addTabController('AdminDeoOnepagecheckoutConfigure','One Page Checkout Configuration', 5);
		self::addTabController('AdminDeoTranslate','Translate Theme');

		// rename
		// self::renameTabController('AdminDeoDetails','Products Details Builder');

		// remove tab
		// self::removeTabController('AdminDeoTemplateModule');
		 
		// create column
		DeoFrameworkHelper::DeoCreateColumn('deotemplate_profiles', 'mobile', 'int(11) UNSIGNED');
		DeoFrameworkHelper::DeoRenameColumn('deotemplate_products', 'type', 'demo', 'tinyint(1)');
		DeoFrameworkHelper::DeoCreateColumn('deotemplate_products', 'responsive', 'VARCHAR(256)');
		DeoFrameworkHelper::DeoRenameColumn('deotemplate_details', 'type', 'fullwidth', 'tinyint(1)');
		DeoFrameworkHelper::DeoCreateColumn('deomegamenu_group', 'tab_style', 'tinyint(1)');
		
		// create table one page checkout
		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_onepagecheckout` (
			`id_deotemplate_onepagecheckout` int(11) NOT NULL AUTO_INCREMENT,
			`plist_key` varchar(255),
			`name` varchar(255),
			`class_checkout` varchar(255),
			`params` text,
			`type` TINYINT(1),
			`url_img_preview` varchar(255),
			`fullwidth` TINYINT(1),
			`active` TINYINT(1),
		PRIMARY KEY (`id_deotemplate_onepagecheckout`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
		');

		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deotemplate_onepagecheckout_shop` (
			`id_deotemplate_onepagecheckout` int(11) NOT NULL AUTO_INCREMENT,
			`id_shop` int(10) unsigned NOT NULL,
			`active` TINYINT(1),
			PRIMARY KEY (`id_deotemplate_onepagecheckout`, `id_shop`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
		');

		// create missing table pagenotfound, sekeyword, statssearch
		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pagenotfound` (
			`id_pagenotfound` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_shop` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`id_shop_group` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`request_uri` VARCHAR(256) NOT NULL,
			`http_referer` VARCHAR(256) NOT NULL,
			`date_add` DATETIME NOT NULL,
			PRIMARY KEY(`id_pagenotfound`),
			INDEX (`date_add`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
		');

		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sekeyword` (
			`id_sekeyword` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_shop` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`id_shop_group` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`keyword` VARCHAR(256) NOT NULL,
			`date_add` DATETIME NOT NULL,
			PRIMARY KEY(`id_sekeyword`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
		');

		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'statssearch` (
			`id_statssearch` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_shop` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`id_shop_group` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`keywords` VARCHAR(255) NOT NULL,
			`results` INT(6) NOT NULL DEFAULT 0,
			`date_add` DATETIME NOT NULL,
			PRIMARY KEY(`id_statssearch`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
		');

		
		// change file name combination product list
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "add_to_cart_attribute", "add_to_cart_combination");
		');

		// detail
		// Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_details` SET `params` = replace(`params`, "type", "zoom");
		// ');
		// Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_details` SET `params` = replace(`params`, "product_image_with_thumb", "product_cover_thumbnails");
		// ');
		// Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_details` SET `params` = replace(`params`, "product_image_show_all", "product_cover_thumbnails");
		// ');
		// Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_details` SET `params` = replace(`params`, \"view\", \"thumb\");
		// ');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_details` SET `params` = replace(`params`, "deo_countdown_pro", "countdown");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_details` SET `params` = replace(`params`, "deo_product_review_extra", "product_review_extra");
		');
		

		// update name file product list
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "add_to_cart_combination", "combination");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "add_to_cart_quantity", "quantity");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "deo_countdown_pro", "countdown");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "deo_list_attribute", "attribute");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "deo_more_image_product_pro", "more_image");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "product_description", "description");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "product_description_short", "description_short");
		');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products` SET `params` = replace(`params`, "product_variants", "variants");
		');

		// class data copyright
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_lang` SET `params` = replace(`params`, "2020", "2021")');

		// update key instagram
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_lang` SET `params` = replace(`params`, "IGQVJXc2ZA2OGhtQ0VIcHNLVzVlN3BmRkhMWl9EdlkxZA1Y2RVNyQlY3Q3RHOWZAOZAkNCWE5hTVJVREdhZAFRrc2E0dkNoWXpSTUhlZAlI5bWxjdjRYOEozR1pBajMzdFEwYjAtQjYzMkxQUU91ZAGdzXzFiVgZDZD", "IGQVJYNWhPLUpJZA0owS3cwVk5FNkFqZAUFLaEhFcDBCeDcxZA0JoZAnlyWjFvT0JLQTg5UlhFek5CQXB1SzY2QWVfc3hCM0VDWHAydWdUUm51R29yWFBWb1hpWF9fclJLaE5CeHRzY1V5VHVxd2h5UE90aAZDZD")');

		// class effect hover brand
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_lang` SET `params` = replace(`params`, "opacity-hover", "rotate-hover")');

		// change class tabs
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_lang` SET `params` = replace(`params`, "product-tabs-", "tabs-")');


		// Rename hooks: 
		// displayLeftColumn => displayDeoTopLeftSidebar
		// displayRightColumn => displayDeoTopRightSidebar
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate` SET `hook_name` = replace(`hook_name`, "displayLeftColumn", "displayDeoTopLeftSidebar")');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate` SET `hook_name` = replace(`hook_name`, "displayRightColumn", "displayDeoTopRightSidebar")');

		// Update multiple store for review
		$result_check = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'deofeature_product_review_criterion_shop"');
		if (count($result_check) == 0){
			Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_criterion_shop` (
				`id_deofeature_product_review_criterion` int(10) UNSIGNED NOT NULL,
				`id_shop` int(10) UNSIGNED NOT NULL,
				PRIMARY KEY(`id_shop`,`id_deofeature_product_review_criterion`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			$results = Db::getInstance()->executeS('SELECT id_deofeature_product_review_criterion FROM '._DB_PREFIX_.'deofeature_product_review_criterion');
			if (count($results)){
				foreach ($results as $row) {
					Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'deofeature_product_review_criterion_shop(id_deofeature_product_review_criterion, id_shop) VALUES ('.$row["id_deofeature_product_review_criterion"].', '.Context::getContext()->shop->id.')');
				}
			}
		}

		$result_check = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'deofeature_product_review_shop"');
		if (count($result_check) == 0){
			Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deofeature_product_review_shop` (
				`id_deofeature_product_review` int(10) UNSIGNED NOT NULL,
				`id_shop` int(10) UNSIGNED NOT NULL,
				PRIMARY KEY(`id_shop`,`id_deofeature_product_review`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			$results = Db::getInstance()->executeS('SELECT id_deofeature_product_review FROM '._DB_PREFIX_.'deofeature_product_review');
			if (count($results)){
				foreach ($results as $row) {
					Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'deofeature_product_review_shop(id_deofeature_product_review, id_shop) VALUES ('.$row["id_deofeature_product_review"].', '.Context::getContext()->shop->id.')');
				}
			}
		}

		$result_check = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'deoblog_comment` LIKE "id_shop"');
		if (!empty($result_check)){
			$results = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'deoblog_comment` DROP PRIMARY KEY, ADD PRIMARY KEY (id_deoblog_comment)');
			DeoFrameworkHelper::DeoRemoveColumn('deoblog_comment', 'id_shop');

			Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'deoblog_comment_shop` (
				`id_deoblog_comment` int(10) UNSIGNED NOT NULL,
				`id_shop` int(10) UNSIGNED NOT NULL,
				PRIMARY KEY(`id_shop`,`id_deoblog_comment`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
			');

			$results = Db::getInstance()->executeS('SELECT id_deoblog_comment FROM '._DB_PREFIX_.'deoblog_comment');
			if (count($results)){
				foreach ($results as $row) {
					Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'deoblog_comment_shop(id_deoblog_comment, id_shop) VALUES ('.$row["id_deoblog_comment"].', '.Context::getContext()->shop->id.')');
				}
			}
		}


		# Empty File css -> auto delete file
		if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
			$common_folders = array(_PS_THEME_URI_.'assets/css/', _PS_THEME_URI_.'assets/js/', _PS_THEME_URI_, _PS_PARENT_THEME_URI_, __PS_BASE_URI__);
			foreach ($common_folders as $common_folder) {
				$version_path = (version_compare(_PS_VERSION_, '1.7.4.0', '>=')) ? 'modules/deotemplate/views/' : 'modules/deotemplate/';

				$cur_dir = self::getPathFromUri( $common_folder.$version_path.'css/positions/' );
				$position_css_files = @Tools::scandir($cur_dir, 'css');
				foreach ($position_css_files as $cur_file) {
					if (filesize($cur_dir.$cur_file) === 0) {
						Tools::deleteFile($cur_dir.$cur_file);
					}
				}

				$cur_dir = self::getPathFromUri( $common_folder.$version_path.'js/positions/' );
				$position_js_files = @Tools::scandir($cur_dir, 'js');
				foreach ($position_js_files as $cur_file) {
					if (filesize($cur_dir.$cur_file) === 0) {
						Tools::deleteFile($cur_dir.$cur_file);
					}
				}

				$cur_dir = self::getPathFromUri( $common_folder.$version_path.'css/profiles/' );
				$profile_css_files = @Tools::scandir($cur_dir, 'css');
				foreach ($profile_css_files as $cur_file) {
					if (filesize($cur_dir.$cur_file) === 0) {
						Tools::deleteFile($cur_dir.$cur_file);
					}
				}

				$cur_dir = self::getPathFromUri( $common_folder.$version_path.'js/profiles/' );
				$profile_js_files = @Tools::scandir($cur_dir, 'js');
				foreach ($profile_js_files as $cur_file) {
					if (filesize($cur_dir.$cur_file) === 0) {
						Tools::deleteFile($cur_dir.$cur_file);
					}
				}

				$cur_dir = self::getPathFromUri( $common_folder.$version_path.'css/customize/' );
				$profile_css_files = @Tools::scandir($cur_dir, 'css');
				foreach ($profile_css_files as $cur_file) {
					if (filesize($cur_dir.$cur_file) === 0) {
						Tools::deleteFile($cur_dir.$cur_file);
					}
				}

				$cur_dir = self::getPathFromUri( $common_folder.$version_path.'js/customize/' );
				$profile_js_files = @Tools::scandir($cur_dir, 'js');
				foreach ($profile_js_files as $cur_file) {
					if (filesize($cur_dir.$cur_file) === 0) {
						Tools::deleteFile($cur_dir.$cur_file);
					}
				}
			}

			if (file_exists(_PS_MODULE_DIR_.'deotemplate/deotemplate.php') && Module::isInstalled('deotemplate')) {
				// @tam_thoi hook vao 'displayBanner'
				require_once(_PS_MODULE_DIR_.'deotemplate/deotemplate.php');
				$instance_module = DeoTemplate::getInstance();
				$instance_module->registerHook('displayBanner');

			}
		}
		
		# FIX : update Prestashop by 1-Click module -> LOST HOOK
		$deo_version = Configuration::get('DEO_CURRENT_VERSION');
		if ($deo_version == false) {
			$ps_version = Configuration::get('PS_VERSION_DB');
			Configuration::updateValue('DEO_CURRENT_VERSION', $ps_version);
		}

		# FIX THEME_CHILD NOT EXIST TPL FILE -> AUTO COPY TPL FILE FROM THEME_PARENT
		// $assets = Context::getContext()->shop->theme->get('assets');
		// $theme_parent = Context::getContext()->shop->theme->get('parent');
		// if( is_array($assets) && isset($assets['use_parent_assets']) && $assets['use_parent_assets'] && $theme_parent )
		// {
		//     $from = _PS_ALL_THEMES_DIR_.$theme_parent.'/modules/deotemplate';
		//     $to =   _PS_ALL_THEMES_DIR_.DeoHelper::getInstallationThemeName().'/modules/deotemplate';
		//     DeoHelper::createDir($to);
		//     Tools::recurseCopy($from, $to);
		// }
		
		# FIX AJAX ERROR WHEN MODULE NOT HAS AUTHOR
		Configuration::updateValue('DEO_CACHE_MODULE', '');
	}

	public static function processCopyImageChildTheme()
	{
		# FIX THEME_CHILD NOT EXIST IMAGE -> AUTO COPY IMAGE FROM THEME_PARENT
		$theme_parent = Context::getContext()->shop->theme->get('parent');
		if(isset($theme_parent) && $theme_parent)
		{
			$from = _PS_ALL_THEMES_DIR_.$theme_parent.'/assets/img/modules/deotemplate';
			$to =   _PS_ALL_THEMES_DIR_.DeoHelper::getInstallationThemeName().'/assets/img/modules/deotemplate';
			DeoHelper::createDir($to);
			Tools::recurseCopy($from, $to);
		}
	}

	public static function processCorrectChildTheme()
	{
		# FIX THEME_CHILD NOT EXIST FILE -> AUTO COPY FILE FROM THEME_PARENT
		$theme_parent = Context::getContext()->shop->theme->get('parent');
		if(isset($theme_parent) && $theme_parent)
		{
			$from = _PS_ALL_THEMES_DIR_.$theme_parent.'/modules/deotemplate';
			$to =   _PS_ALL_THEMES_DIR_.DeoHelper::getInstallationThemeName().'/modules/deotemplate';
			DeoHelper::createDir($to);
			Tools::recurseCopy($from, $to);

			// copy config.xml
			$from = _PS_ALL_THEMES_DIR_.$theme_parent.'/config.xml';
			$to =   _PS_ALL_THEMES_DIR_.DeoHelper::getInstallationThemeName().'/config.xml';
			Tools::copy($from, $to);
		}

	}
	
	public static function processDeleteOldPosition()
	{
		$sql = 'SELECT mobile,header,content,footer,product FROM `'._DB_PREFIX_.'deotemplate_profiles` GROUP BY id_deotemplate_profiles';
		$result = Db::getInstance()->executeS($sql);
		$list_exits_position = array();
		foreach ($result as $val) {
			foreach ($val as $v) {
				if (!in_array($v, $list_exits_position) && $v) {
					$list_exits_position[] = $v;
				}
			}
		}
		if ($list_exits_position) {
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'deotemplate_positions` WHERE id_deotemplate_positions NOT IN ('.pSQL(implode(',', $list_exits_position)).')';
			
			$list_delete_position = Db::getInstance()->executes($sql);
			foreach ($list_delete_position as $row) {
				$object = new DeoTemplatePositionsModel($row['id_deotemplate_positions']);
				$object->delete();
				if ($object->position_key) {
					Tools::deleteFile(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/modules/deotemplate/css/positions/'.$object->position.$object->position_key.'.css');
					Tools::deleteFile(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/modules/deotemplate/js/positions/'.$object->position.$object->position_key.'.js');
				}
			}
		}
	}

	public static function setActiveTabController($controller, $active)
	{
		$id = Tab::getIdFromClassName($controller);
		if (isset($id)) {
			$active = ($active) ? 1 : 0;
			$sql = 'UPDATE '._DB_PREFIX_.'tab t
					SET t.`active`="'.$active.'"
					WHERE id_tab = "'.$id.'"';
			$row =  Db::getInstance()->execute($sql);

			return true;
		}

		return false;
	}

	public static function renameTabController($controller, $name)
	{
		$id = Tab::getIdFromClassName($controller);
		if (isset($id)) {
			$sql = 'UPDATE '._DB_PREFIX_.'tab_lang t
					SET t.`name`="'.$name.'"
					WHERE id_tab = "'.$id.'"';
			$row =  Db::getInstance()->execute($sql);

			return true;
		}

		return false;
	}

	public static function addTabController($controller, $name, $position = null, $parent = 'AdminDeoTemplate', $translate = 'Modules.Deotemplate.Admin')
	{
		//create new tab blog comment
		$id = Tab::getIdFromClassName($controller);
		if (!$id) {
			$id_parent = Tab::getIdFromClassName($parent);
			$tab = array(
				'class_name' => $controller,
				'name' => $name,
			);
			$newtab = new Tab();
			$newtab->class_name = $tab['class_name'];
			$newtab->id_parent = isset($tab['id_parent']) ? $tab['id_parent'] : $id_parent;
			$newtab->module = 'deotemplate';
			if ($position){
				$newtab->position = $position;
			}
			foreach (Language::getLanguages() as $l) {
				$newtab->name[$l['id_lang']] = Context::getContext()->getTranslator()->trans($tab['name'], array(), $translate);
			}
			$newtab->save();
		}
	}

	public static function removeTabController($controller)
	{
		$id = Tab::getIdFromClassName($controller);
		if ($id) {
			$tab = new Tab($id);
			$tab->delete();
		}
	}
	
	/**
	 * Check is Release or Developing
	 * Release      : load css in themes/THEME_NAME/modules/MODULE_NAME/ folder
	 * Developing   : load css in themes/THEME_NAME/assets/css/ folder
	 */
	public static function isRelease()
	{
		if (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true) {
			# CASE DEV
			return false;
		}
		
		# Release
		return true;
	}
	
	public static $path_css;
	public static function getFullPathCss($file, $directories = array())
	{
		if (self::$path_css) {
			$directories = self::$path_css;
		} else {
			/**
			 * DEFAULT
			 * => D:\localhost\prestashop\themes/base/
			 * =>
			 * => D:\localhost\prestashop\
			 */
			$directories = array(DeoHelper::getThemeDir(), _PS_PARENT_THEME_DIR_, _PS_ROOT_DIR_);
			if (!self::isRelease()) {
				$directories = array(DeoHelper::getThemeDir().'assets/css/',DeoHelper::getThemeDir(), _PS_PARENT_THEME_DIR_, _PS_ROOT_DIR_);
			}
		}
		
		foreach ($directories as $baseDir) {
			$fullPath = realpath($baseDir.'/'.$file);
			if (is_file($fullPath)) {
				return $fullPath;
			}
		}
		return false;
	}
	
	public static function getUriFromPath($fullPath)
	{
		$uri = str_replace(
			_PS_ROOT_DIR_,
			rtrim(__PS_BASE_URI__, '/'),
			$fullPath
		);

		return str_replace(DIRECTORY_SEPARATOR, '/', $uri);
	}
	
	/**
	 * Live Theme Editor
	 */
	public static function getFileList($path, $e = null, $nameOnly = false)
	{
		$output = array();
		$directories = glob($path.'*'.$e);
		if ($directories) {
			foreach ($directories as $dir) {
				$dir = basename($dir);
				if ($nameOnly) {
					$dir = str_replace($e, '', $dir);
				}
				$output[$dir] = $dir;
			}
		}
		return $output;
	}
	
	/**
	 * When install theme, still get old_theme
	 */
	public static function getInstallationThemeName()
	{
		$theme_name = '';
		if (Tools::getValue('controller') == 'AdminThemes' && Tools::getValue('action') == 'enableTheme') {
			# Case install theme
			$theme_name = Tools::getValue('theme_name');
		} else if (Tools::getValue('controller') == 'AdminShop' && Tools::getValue('submitAddshop')) {
			# Case install theme
			$theme_name = Tools::getValue('theme_name');
		} else if ( preg_match('#/improve/design/themes/(?P<themeName>[a-zA-Z0-9_.-]+)/enable#sD', $_SERVER['REQUEST_URI'], $matches) ) {
			if(isset($matches['themeName']) && $matches['themeName']) {
				$theme_name = $matches['themeName'];
			}
		}
		
		if (empty($theme_name)) {
			$theme_name = DeoHelper::getThemeName();
		}
		return $theme_name;
	}
	
	static $id_shop;
	/**
	 * FIX Install multi theme
	 * DeoHelper::getIDShop();
	 */
	public static function getIDShop()
	{
		if ((int)self::$id_shop) {
			$id_shop = (int)self::$id_shop;
		} else {
			$id_shop = (int)Context::getContext()->shop->id;
		}
		return $id_shop;
	}
	
	/*
	 * get theme in SINGLE_SHOP or MULTI_SHOP
	 * DeoHelper::getThemeName()
	 */
	public static function getThemeName()
	{
		static $theme_name;
		if (!$theme_name) {
			# DEFAULT SINGLE_SHOP
			$theme_name = Context::getContext()->shop->theme_name;

			# GET THEME_NAME MULTI_SHOP
			if (Shop::getTotalShops(false, null) >= 2) {
				$id_shop = Context::getContext()->shop->id;

				$shop_arr = Shop::getShop($id_shop);
				if (is_array($shop_arr) && !empty($shop_arr)) {
					$theme_name = $shop_arr['theme_name'];
				}
			}
		}
		
		return $theme_name;
	}

	public static function processDebugMode($debug = false)
	{
		if ($debug){
			DeoHelper::setActiveTabController('AdminDeoHook', true);
			DeoHelper::setActiveTabController('AdminDeoBlogs', true);
			// DeoHelper::setActiveTabController('AdminDeoShortcode', true);
			DeoHelper::setActiveTabController('AdminDeoBlogComments', true);
			DeoHelper::setActiveTabController('AdminDeoBlogCategories', true);
			DeoHelper::setActiveTabController('AdminDeoDeveloperConfigure', true);
			DeoHelper::setActiveTabController('AdminDeoTranslate', true);
			Configuration::updateValue(DeoHelper::getConfigName('DEBUG_MODE'), 1);
		}else{
			DeoHelper::setActiveTabController('AdminDeoHook', false);
			DeoHelper::setActiveTabController('AdminDeoBlogs', false);
			// DeoHelper::setActiveTabController('AdminDeoShortcode', false);
			DeoHelper::setActiveTabController('AdminDeoBlogComments', false);
			DeoHelper::setActiveTabController('AdminDeoBlogCategories', false);
			DeoHelper::setActiveTabController('AdminDeoDeveloperConfigure', false);
			DeoHelper::setActiveTabController('AdminDeoTranslate', false);
			Configuration::updateValue(DeoHelper::getConfigName('DEBUG_MODE'), 0);
		}
	}
	
	public static function fullCopy( $source, $target )
	{
		if (is_dir($source)) {
			@mkdir($target);
			$d = dir($source);
			while (FALSE !== ( $name = $d->read())) {
				if ($name == '.' || $name == '..' ) {
					continue;
				}
				$entry = $source . '/' . $name;
				if (is_dir($entry)) {
					self::fullCopy($entry, $target . '/' . $name);
					continue;
				}
				
				@copy($entry, $target . '/' . $name);
			}

			$d->close();
		} else {
			copy($source, $target);
		}
	}

	public static function getThemeDir(){
		return _PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/';
	}
	
	public static function getThemeUri()
	{
		return __PS_BASE_URI__.'themes/'.Context::getContext()->shop->theme->getName().'/';
	}

	public static function getTemplate($tpl_name, $override_folder = '')
	{
		$module_name = 'deotemplate';
		$hook_name = DeoShortCodesBuilder::$hook_name;

		if (isset($override_folder) && file_exists(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name."/modules/$module_name/views/templates/hook/$override_folder/$tpl_name")) {
			$tpl_file = "views/templates/hook/$override_folder/$tpl_name";
		} elseif (file_exists(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/modules/'.$module_name.'/views/templates/hook/'.$hook_name.'/'.$tpl_name) || file_exists(_PS_MODULE_DIR_.$module_name.'/views/templates/hook/'.$hook_name.'/'.$tpl_name)) {
			$tpl_file = 'views/templates/hook/'.$hook_name.'/'.$tpl_name;
		} elseif (file_exists(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/modules/'.$module_name.'/views/templates/hook/'.$tpl_name) || file_exists(_PS_MODULE_DIR_.$module_name.'/views/templates/hook/'.$tpl_name)) {
			$tpl_file = 'views/templates/hook/'.$tpl_name;
		} else {
			$tpl_file = 'views/templates/hook/DeoGeneral.tpl';
		}
		
		return $tpl_file;
	}
	
	/**
	 * get Full path in tpl
	 */
	public static function getTplTemplate($tpl_name='', $override_folder = '')
	{
		$module_name = 'deotemplate';
		$hook_name = DeoShortCodesBuilder::$hook_name;
		
		$path_theme = _PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/modules/'.$module_name.'/views/templates/hook/';
		$path_module = _PS_MODULE_DIR_.$module_name.'/views/templates/hook/';
		
		if (file_exists($path_theme.$override_folder.'/'.$tpl_name)) {
			# THEMES / OVERRIDE
			$tpl_file = $path_theme.$override_folder.'/'.$tpl_name;
		} elseif (file_exists($path_module.$override_folder.'/'.$tpl_name)) {
			# MODULE / OVERRIDE
			$tpl_file = $path_module.$override_folder.'/'.$tpl_name;
		} elseif (file_exists($path_theme.$hook_name.'/'.$tpl_name)) {
			# THEME / HOOK_NAME
			$tpl_file = $path_theme.$hook_name.'/'.$tpl_name;
		} elseif (file_exists($path_module.$hook_name.'/'.$tpl_name)) {
			# MODULE / HOOK_NAME
			$tpl_file = $path_module.$hook_name.'/'.$tpl_name;
		} elseif (file_exists($path_theme.$tpl_name)) {
			# THEME / HOOK
			$tpl_file = $path_theme.$tpl_name;
		} elseif (file_exists($path_module.$tpl_name)) {
			# MODULE / HOOK
			$tpl_file = $path_module.$tpl_name;
		} elseif (file_exists($path_theme.'/DeoGeneral.tpl')) {
			# THEME / GENERATE
			$tpl_file = $path_theme.'/DeoGeneral.tpl';
		} else {
			# MODULE / GENERATE
			$tpl_file = $path_module.'/DeoGeneral.tpl';
		}
		return $tpl_file;
	}

	public static function checkFileOverrideExist($uri){
		if (file_exists(DeoHelper::getThemeDir().$uri)){
			return _PS_THEME_URI_.$uri;
		}else if(file_exists(_PS_ROOT_DIR_.'/'.$uri)){
			return _PS_ROOT_URI_.'/'.$uri;
		}
		return false;
	}

	public static function checkDirFileOverrideExist($uri){
		if (file_exists(DeoHelper::getThemeDir().$uri)){
			return DeoHelper::getThemeDir().$uri;
		}else if(file_exists(_PS_ROOT_DIR_.'/'.$uri)){
			return _PS_ROOT_DIR_.'/'.$uri;
		}
		return false;
	}

	/**
	 * Copy method from ROOT\src\Adapter\Assets\AssetUrlGeneratorTrait.php
	 */
	public static function getPathFromUri($fullUri)
	{
		return _PS_ROOT_DIR_.str_replace(rtrim(__PS_BASE_URI__, '/'), '', $fullUri);
	}
	
	public static function getShortcodeTemplatePath( $file_name )
	{
		$path = _PS_MODULE_DIR_.'deotemplate/views/templates/admin/shortcodes/' . $file_name;
		return $path;
	}
	
	public static function createShortCode($correct = false, $quickstart = false)
	{
		#shortcode to tinymce :  backup file
		// if (!file_exists(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/tinymce.inc.js')){
		// 	Tools::copy(_PS_ROOT_DIR_.'/js/admin/tinymce.inc.js', _PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/tinymce.inc.js');
		// }

		// @mkdir(_PS_ROOT_DIR_.'/js/admin/', 0755, true);

		#shortcode to tinymce :  override file
		// Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/tinymce.inc.js', _PS_ROOT_DIR_.'/js/admin/tinymce.inc.js');

		#shortcode to tinymce : copy folder plugin of shortcode for tinymce
		// @mkdir(_PS_ROOT_DIR_.'/js/tiny_mce/plugins/deotemplate/', 0755, true);
		// Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/deotemplate/index.php', _PS_ROOT_DIR_.'/js/tiny_mce/plugins/deotemplate/index.php');
		// Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/deotemplate/plugin.min.js', _PS_ROOT_DIR_.'/js/tiny_mce/plugins/deotemplate/plugin.min.js');

		// @mkdir(_PS_ROOT_DIR_.'/override/controllers/front/listing/', 0755, true);
		// Tools::copy(_PS_ROOT_DIR_.'/override/controllers/front/index.php', _PS_ROOT_DIR_.'/override/controllers/front/listing/index.php');

		if (($correct && !DeoHelper::get('DEOTEMPLATE_OVERRIDED')) || ($correct && $quickstart)){
			$instance_module = DeoTemplate::getInstance();
			$instance_module->installOverrides();
			DeoHelper::updateValue('DEOTEMPLATE_OVERRIDED', 1);
		}


		// if (version_compare(_PS_VERSION_, '1.7.6', '>=')){
		// 	#CATEGORY 
		// 	if (!file_exists(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/category.bundle.js')){
		// 		Tools::copy(_PS_ADMIN_DIR_.'/themes/new-theme/public/category.bundle.js', _PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/category.bundle.js');
		// 	}
		// 	Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/category.bundle.js', _PS_ADMIN_DIR_.'/themes/new-theme/public/category.bundle.js');
			
		// 	#CMS
		// 	if (!file_exists(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/cms_page_form.bundle.js')){
		// 		Tools::copy(_PS_ADMIN_DIR_.'/themes/new-theme/public/cms_page_form.bundle.js', _PS_MODULE_DIR_.'deotemplate/views/js/shortcode/backup/cms_page_form.bundle.js');
		// 	}
		// 	Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/cms_page_form.bundle.js', _PS_ADMIN_DIR_.'/themes/new-theme/public/cms_page_form.bundle.js');
		// }

		// if (version_compare(_PS_VERSION_, '1.7.8.0', '=')){
		// 	#fix error 1780 :  can't add to cart when click to icon or text
		// 	Tools::copy(_PS_MODULE_DIR_.'deotemplate/views/js/shortcode/core.js', _PS_ALL_THEMES_DIR_.'core.js');
		// }
	}
		
	
	public static function createDir($path = '')
	{
		if (!file_exists($path))
		{
			if (!@mkdir($path, 0755, true))
			{
				die("Please create folder ".$path." and set permission 755");
			}
		}
	}

	public static function deleteDirectory($dir) {
		if (!file_exists($dir)) {
			return true;
		}

		if (!is_dir($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (!DeoHelper::deleteDirectory($dir.'/'.$item)) {
				return false;
			}

		}

		return rmdir($dir);
	}
	
	public static function getConfigDir($key = 'theme_profile_logo', $value = '')
	{
		static $data;
		if (!$data )
		{
			$data = array(
				'module_img_admin' => _PS_ROOT_DIR_.'/modules/deotemplate/img/admin/',
				'module_products' => _PS_ROOT_DIR_.'/modules/deotemplate/views/templates/front/products/',
				'module_details' => _PS_ROOT_DIR_.'/modules/deotemplate/views/templates/front/details/',
				'module_profiles' => _PS_ROOT_DIR_.'/modules/deotemplate/views/templates/front/profiles/',
				'module_onepagecheckout' => _PS_ROOT_DIR_.'/modules/deotemplate/views/templates/front/onepagecheckout/',
				
				'theme_deo_image' => DeoHelper::getThemeDir().'assets/img/modules/deotemplate/images/',          // DeoHelper::getImgThemeDir()
				'theme_deo_icon' => DeoHelper::getThemeDir().'assets/img/modules/deotemplate/icon/',             // DeoHelper::getImgThemeDir('icon')
				'theme_profile_logo' => DeoHelper::getThemeDir().'profiles/images/',
				'theme_profile_js' => DeoHelper::getThemeDir().'modules/deotemplate/js/profiles/',
				'theme_profile_css' => DeoHelper::getThemeDir().'modules/deotemplate/css/profiles/',
				'theme_position_js' => DeoHelper::getThemeDir().'modules/deotemplate/js/positions/',
				'theme_position_css' => DeoHelper::getThemeDir().'modules/deotemplate/css/positions/',
				'theme_product_lists_css' => DeoHelper::getThemeDir().'modules/deotemplate/css/product_lists/',
				'theme_export_profile' => DeoHelper::getThemeDir().'profiles_export/',
				'theme_download_profile' => DeoHelper::getThemeDir().'profiles_download/',
				'theme_image_deotemplate' => DeoHelper::getThemeDir().'assets/img/modules/deotemplate/',
				'theme_products' => DeoHelper::getThemeDir().'products/',
				'theme_details' => DeoHelper::getThemeDir().'details/',
				'theme_profiles' => DeoHelper::getThemeDir().'profiles/',
				'theme_onepagecheckout' => DeoHelper::getThemeDir().'onepagecheckout/',
			);
			if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
				$data['theme_products'] = DeoHelper::getThemeDir().'modules/deotemplate/views/templates/front/products/';
				$data['theme_details'] = DeoHelper::getThemeDir().'modules/deotemplate/views/templates/front/details/';
				$data['theme_profiles'] = DeoHelper::getThemeDir().'modules/deotemplate/views/templates/front/profiles/';
				$data['theme_onepagecheckout'] = DeoHelper::getThemeDir().'modules/deotemplate/views/templates/front/onepagecheckout/';
			}
		}
		
		if(isset($data[$key.$value]))
		{
			return $data[$key.$value];
		}else{
			return '';
		}
	}
	
	public static function getModules()
	{
		$not_module = array('deotemplate', 'themeconfigurator', 'themeinstallator', 'cheque');
		$where = '';
		if (count($not_module) == 1) {
			$where = ' WHERE m.`name` <> \''.$not_module[0].'\'';
		} elseif (count($not_module) > 1) {
			$where = ' WHERE m.`name` NOT IN (\''.implode("','", $not_module).'\')';
		}
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$sql = 'SELECT m.name, m.id_module
				FROM `'._DB_PREFIX_.'module` m
				JOIN `'._DB_PREFIX_.'module_shop` ms ON (m.`id_module` = ms.`id_module` AND ms.`id_shop` = '.(int)$id_shop.')
				'.$where;
		$module_list = Db::getInstance()->ExecuteS($sql);
		$module_info = ModuleCore::getModulesOnDisk(true);
		$modules = array();
		foreach ($module_list as $m) {
			foreach ($module_info as $mi) {
				if ($m['name'] === $mi->name) {
					$m['tab'] = (isset($mi->tab) && $mi->tab) ? $mi->tab : '';
					$m['interest'] = (isset($mi->interest) && $mi->interest) ? $mi->interest : '';
					$m['author'] = (isset($mi->author) && $mi->author) ? Tools::ucwords(Tools::strtolower($mi->author)) : '';
					$m['image'] = (isset($mi->image) && $mi->image) ? $mi->image : '';
					$m['avg_rate'] = (isset($mi->avg_rate) && $mi->avg_rate) ? $mi->avg_rate : '';
					$m['description'] = (isset($mi->description) && $mi->description) ? $mi->description : '';
					$sub = '';
					if (isset($mi->description) && $mi->description) {
						// Get sub word 50 words from description
						$sub = Tools::substr($mi->description, 0, 50);
						$spo = Tools::strrpos($sub, ' ');
						$sub = Tools::substr($mi->description, 0, ($spo != -1 ? $spo : 0)).'...';
					}
					$m['description_short'] = $sub;
					break;
				}
			}

			$m['tab'] = (isset($m['tab']) && $m['tab']) ? $m['tab'] : '';
			$m['interest'] = (isset($m['interest']) && $m['interest']) ? $m['interest'] : '';
			$m['author'] = (isset($m['author']) && $m['author']) ? $m['author'] : '';
			$m['image'] = (isset($m['image']) && $m['image']) ? $m['image'] : '';
			$m['avg_rate'] = (isset($m['avg_rate']) && $m['avg_rate']) ? $m['avg_rate'] : '';
			$m['description'] = (isset($m['description']) && $m['description']) ? $m['description'] : '';
			$m['description_short'] = (isset($m['description_short']) && $m['description_short']) ? $m['description_short'] : '';
			
			$modules[] = $m;
		}
		return $modules;
	}
	
	public static $replaced_element;
	public static function replaceFormId($param)
	{
		preg_match_all('/form_id="([^\"]+)"/i', $param, $matches, PREG_OFFSET_CAPTURE);
		foreach ($matches[0] as $row) {
			if (!isset(self::$replaced_element[$row[0]])) {
				$form_id = 'form_id="form_'.DeoSetting::getRandomNumber().'"';
				self::$replaced_element[$row[0]] = $form_id;
			} else {
				$form_id = self::$replaced_element[$row[0]];
			}
			$param = str_replace($row[0], $form_id, $param);
		}
		preg_match_all('/ id="([^\"]+)"/i', $param, $matches, PREG_OFFSET_CAPTURE);
		foreach ($matches[0] as $row) {
			if (!isset(self::$replaced_element[$row[0]])) {
				if (Tools::strpos($row[0], 'tab')) {
					$form_id = ' id="tab_'.DeoSetting::getRandomNumber().'"';
				} else if (Tools::strpos($row[0], 'accordion')) {
					$form_id = ' id="accordion_'.DeoSetting::getRandomNumber().'"';
				} else if (Tools::strpos($row[0], 'collapse')) {
					$form_id = ' id="collapse_'.DeoSetting::getRandomNumber().'"';
				} else {
					$form_id = '';
				}
				self::$replaced_element[$row[0]] = $form_id;
			} else {
				$form_id = self::$replaced_element[$row[0]];
			}
			if ($form_id) {
				$param = str_replace($row[0], $form_id, $param);
				//ifreplace id="accordion_8223663723713862" to new id="accordion_8223663723713862"
				if (Tools::strpos($row[0], 'accordion')) {
					$parent_id = Tools::substr(str_replace(' id="accordion_', 'accordion_', $row[0]), 0, -1);
					$parent_form_id = Tools::substr(str_replace(' id="accordion_', 'accordion_', $form_id), 0, -1);
					$param = str_replace(' parent_id="'.$parent_id.'"', ' parent_id="'.$parent_form_id.'"', $param);
				}
			}
		}
		return $param;
	}
	
	/**
	 * String to int to string
	 * DeoHelper::addonValidInt($id_categories);
	 */
	public static function addonValidInt($str_ids = '')
	{
		return implode(',' , array_map('intval', explode(',', $str_ids)));
	}
	
	public static function getLicenceTPL()
	{
		return Tools::file_get_contents( _PS_MODULE_DIR_.'deotemplate/views/templates/admin/licence_tpl.txt');
	}
	
	/**
	 * COPY FROM  modules\deotemplate\controllers\admin\AdminDeoPositions.php
	 * @TODO remove  modules\deotemplate\controllers\admin\AdminDeoPositions.php
	 */
	public static function autoCreatePosition($obj)
	{
		$model = new DeoTemplatePositionsModel();
		$id = $model->addAuto($obj);
		if ($id) {
			self::saveCustomJsAndCss($obj['position'].$obj['position_key'], '');
		}
		return $id;
	}

	/**
	 * COPY FROM  modules\deotemplate\controllers\admin\AdminDeoPositions.php
	 * @TODO remove  modules\deotemplate\controllers\admin\AdminDeoPositions.php
	 */
	public static function saveCustomJsAndCss($key, $old_key = '')
	{
		if ($old_key) {
			Tools::deleteFile(DeoHelper::getThemeDir().DeoHelper::getCssDir().'positions/'.$old_key.'.css');
			Tools::deleteFile(DeoHelper::getThemeDir().DeoHelper::getJsDir().'positions/'.$old_key.'.js');
		}
		if (Tools::getValue('js') != '') {
			DeoSetting::writeFile(DeoHelper::getThemeDir().DeoHelper::getJsDir().'positions/', $key.'.js', Tools::getValue('js'));
		}
		if (Tools::getValue('css') != '') {
			DeoSetting::writeFile(DeoHelper::getThemeDir().DeoHelper::getCssDir().'positions/', $key.'.css', Tools::getValue('css'));
		}
	}


	public static function getMediaDir()
	{
		$media_dir = '';
		if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
			$media_dir = 'modules/deotemplate/views/';
		} else {
			$media_dir = 'modules/deotemplate/';
		}
		return $media_dir;
	}

	public static function getThemeMediaDir($media = null)
	{
		$media_dir = '';

		if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
			if ($media == 'img') {
				$media_dir = 'assets/img/modules/deotemplate/';
			}
			
			if ($media == 'css') {
				$media_dir = 'assets/css/modules/deotemplate/views/';
			}
		} else {
			$media_dir = 'modules/deotemplate/';
		}
		return $media_dir;
	}

	public function convertArrayToKeyId($array,$key)
	{   
		$new_array = array();
		if (is_array($array)){
			foreach ($array as $value) {
				 $new_array[$value[$key]] = $value;
			}
		}

		return $new_array;
	}


	/**
	 * Calculate rate image
	 */
	public static function calculateRateImage($width,$height)
	{
		return round(($height/$width)*100,1).'%';
	}

	/**
	 * Calculate rate image product by formatted name image
	 */
	public static function calculateRateImageProductByFormattedName($image)
	{
		$imageSize = Image::getSize(ImageType::getFormattedName($image));
		return DeoHelper::calculateRateImage($imageSize['width'],$imageSize['height']);
	}

	/**
	 * Calculate rate image product by name image
	 */
	public static function calculateRateImageProduct($image)
	{
		$imageSize = Image::getSize($image);
		return DeoHelper::calculateRateImage($imageSize['width'],$imageSize['height']);
	}

	/**
	 * Convet rgb to hex
	 */
	public static function convertRGBToHex($string)
	{
		preg_match('/\((.*?)\)/i', $string, $match);
		$color_rgb = split(",", $match[1]);
		$color = sprintf("#%02x%02x%02x", $color_rgb[0], $color_rgb[1], $color_rgb[2]);
		
		return array('hex' => $color, 'opacity' => (isset($color_rgb[3]) ? $color_rgb[3] : 1));
	}

	/**
	 * Convet hex to rgb
	 */
	public static function convertHexToRgb($color, $opacity = false) {

		$default = 'rgb(0,0,0)';
	 
		//Return default if no color provided
		if (empty($color))
			return $default; 
	 
		//Sanitize $color if "#" is provided 
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if ($opacity){
			if (abs($opacity) > 1){
				$opacity = 1.0;
			}
			$output = 'rgba('.implode(", ",$rgb).', '.$opacity.')';
		} else {
			$output = 'rgb('.implode(", ",$rgb).')';
		}

		//Return rgb(a) color string
		return $output;
	}

	public static function processCompressImages(){

		$root_img = _PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/assets/img';
		self::loopDir($root_img);

		$root_img_deotemplate = _PS_IMG_DIR_.'/deotemplate';
		self::loopDir($root_img_deotemplate);

		return true;
	}

	public static function loopDir($path)
	{
			if (!is_dir($path)) {
					return false;
			}

			$objects = scandir($path);
			foreach ($objects as $name) {
					if ($name != '.' && $name != '..') {
							if (is_dir($path.'/'.$name)) {
									self::loopDir($path.'/'.$name);
							}else{
									self::compressByPhp($path, $name);
							}
					}
			}
	}

	public static function png_has_transparency($filename)
	{
			if (Tools::strlen($filename) == 0 || !file_exists($filename))
					return false;
			if (ord(call_user_func('file_get_contents', $filename, false, null, 25, 1)) & 4) {
					return true;
			}
			$contents = Tools::file_get_contents($filename);
			if (stripos($contents, 'PLTE') !== false && stripos($contents, 'tRNS') !== false)
					return true;

			return false;
	}

	public static function compressByPhp($path, $name)
	{
		$source = $path .'/'. $name;
		$destination = $path .'/'. $name;
		$temp = $path . 'temp-' . $name;
		$file_size_old = filesize($source);

		$quality = (int) DeoHelper::getConfig('QUALITY_IMAGE_COMPRESS');

		if ($quality <= 0){
			$quality = 80;
		}elseif ($quality > 100){
			return false;
		}
			
		$image = @getimagesize($source);
		$default = false;

		
		if ($image) {
			ini_set('gd.jpeg_ignore_warning', 1);
			$widthImage = $image[0];
			$heightImage = $image[1];

			// default compress small image with quality 80
			if ($widthImage <= 260) {
					$default = true;
			}

			$imageCanves = @imagecreatetruecolor($widthImage, $heightImage);
			switch (Tools::strtolower($image['mime'])) {
				case 'image/jpeg':
					$NewImage = @imagecreatefromjpeg($source);
					break;
				case 'image/JPEG':
					$NewImage = @imagecreatefromjpeg($source);
					break;
				case 'image/png':
					$NewImage = @imagecreatefrompng($source);
					break;
				case 'image/PNG':
					$NewImage = @imagecreatefrompng($source);
					break;
				case 'image/GIF':
					$NewImage = @imagecreatefromgif($source);
					break;
				default:
					return false;
			}

			if (self::png_has_transparency($source)){
					return false;
			}

			ini_set('gd.jpeg_ignore_warning', 1);
			$temp2 = $path . 'temp2-' . $name;
			Tools::copy($source, $temp2);

			$white = imagecolorallocate($imageCanves, 255, 255, 255);
			imagefill($imageCanves, 0, 0, $white);
			// Resize Image
			if (imagecopyresampled($imageCanves, $NewImage, 0, 0, 0, 0, $widthImage, $heightImage, $widthImage, $heightImage)) {
					// copy file

					if (imagejpeg($imageCanves, $destination, $default ? 80 : $quality)) {
							imagedestroy($imageCanves);
							if (Tools::copy($destination, $temp)) {
									$file_size = Tools::ps_round(@filesize($temp) / 1024, 2);
									if ($file_size > $file_size_old) {
											Tools::copy($temp2, $destination);
											$file_size = $file_size_old;
									}
									@unlink($temp);
									@unlink($temp2);
									if(file_exists($path.'fileType')){
											@unlink($path.'fileType');
									}

									return true;
							}
					}
			}
		}

		@unlink($temp2);
		if(file_exists($path.'fileType')){
				@unlink($path.'fileType');
		}

		return false;
	}
}
