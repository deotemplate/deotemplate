<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoSetting.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProfilesModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/controllers/admin/AdminDeoPositions.php');

class AdminDeoHomeController extends ModuleAdminControllerCore
{
	public static $shortcode_lang;
	public static $lang_id;
	public static $language;
	public $error_text = '';
	public $module_name;
	public $module_path;
	// public $module_path_resource;
	public $tpl_path;
	public $theme_dir;
	public $file_content = '';

	public function __construct()
	{
		$this->bootstrap = true;
		$this->show_toolbar = true;
		$this->table = 'deotemplate';
		$this->className = 'DeoTemplateHome';
		$this->context = Context::getContext();
		$this->module_name = 'deotemplate';
		$this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
		// $this->module_path_resource = $this->module_path.'views/';
		$this->tpl_path = _PS_ROOT_DIR_.'/modules/'.$this->module_name.'/views/templates/admin';
		parent::__construct();
		$this->multishop_context = false;
		$this->theme_dir = DeoHelper::getThemeDir();
		DeoHelper::loadShortCode(DeoHelper::getThemeDir());
	}

	public function initPageHeaderToolbar()
	{
		// $this->page_header_toolbar_btn['save'] = array(
		// 	//'short' => $this->l('Save', null, null, false),
		// 	'short' => 'SaveAndStay',
		// 	'href' => 'javascript:;',
		// 	//'desc' => $this->l('Save', null, null, false),
		// 	'desc' => $this->l('Save and stay'),
		// 	'confirm' => 1,
		// 	'js' => 'submitform()'
		// );
		// $current_id = Tools::getValue('id_deotemplate_profiles');
		// if (!$current_id) {
		// 	$profile = DeoTemplateProfilesModel::getActiveProfile('index');
		// 	$current_id = $profile['id_deotemplate_profiles'];
		// }

		// $profile_obj = new DeoTemplateProfilesModel($current_id);
		// $url_preview = Context::getContext()->link->getPageLink('index', null, $this->context->employee->id_lang);
		// if (Configuration::get('PS_REWRITING_SETTINGS') && (isset($profile_obj->friendly_url[$this->context->employee->id_lang])) && $profile_obj->friendly_url[$this->context->employee->id_lang]) {
		// 	$url_preview .= $profile_obj->friendly_url[$this->context->employee->id_lang].'.html';
		// }else{
		// 	$url_preview .= '?id_deotemplate_profiles='.$current_id;
		// }

		// $this->page_header_toolbar_btn['preview'] = array(
		// 	//'short' => $this->l('Save', null, null, false),
		// 	'short' => 'Preview',
		// 	'href' => $url_preview,
		// 	'target' => '_blank',
		// 	//'desc' => $this->l('Save', null, null, false),
		// 	'desc' => $this->l('Preview'),
		// 	'confirm' => 0
		// );

		// $url_customize_color = $this->context->link->getAdminLink('AdminDeoProfiles').'&id_deotemplate_profiles='.$current_id.'&updatedeotemplate_profiles&tab_open=tab_customize';
		// $this->page_header_toolbar_btn['configure'] = array(
		// 	//'short' => $this->l('Save', null, null, false),
		// 	'short' => 'Customize color',
		// 	'href' => $url_customize_color,
		// 	'target' => '_blank',
		// 	//'desc' => $this->l('Save', null, null, false),
		// 	'desc' => $this->l('Customize color'),
		// 	'confirm' => 0
		// );

		parent::initPageHeaderToolbar();
	}

	public function postProcess()
	{
		if (count($this->errors) > 0) {
			if ($this->ajax) {
				$array = array('hasError' => true, 'errors' => $this->errors[0]);
				die(json_encode($array));
			}
			return;
		}
		
		$action = Tools::getValue('action');
		// $type = Tools::getValue('type');
		
		if ($action == 'processPosition') {
			$this->processPosition();
		}
		
		if ($action == 'selectPosition') {
			$this->selectPosition();
		}
		
		if (Tools::isSubmit('submitImportData')) {
			$this->importData(Language::getLanguages(false), (int)$this->context->language->id);
		}
		
		if ($action == 'export') {
			$this->exportData();
		}
		
		// submit save
		if (Tools::isSubmit('submitSaveAndStay')) {
			if (Tools::getValue('data_profile') && Tools::getValue('data_profile') != '') {
				$data_form = json_decode(Tools::getValue('data_profile'), 1);

				if (is_array($data_form)) {
					$id_profile = Tools::getValue('data_id_profile');
					$profile = new DeoTemplateProfilesModel($id_profile);
					$params_profile = json_decode($profile->params);
					$data_widgets_modules = Tools::getValue('data_widgets_modules', json_encode(array()));
					$data_position = json_decode(Tools::getValue('data_position', json_encode(array())));
					$data_elements = Tools::getValue('data_elements', json_encode(array()));
					$data_product_lists = Tools::getValue('data_product_lists', json_encode(array()));
					$data_megamenu_group_active = Tools::getValue('data_megamenu_group_active');


					foreach ($data_position as $name_position => $position_item) {
						$obj_position = new DeoTemplatePositionsModel($position_item->id);
						$obj_position->params = json_encode(array(
							'widgets_modules' => $position_item->widgets_modules, 
							'elements' => $position_item->elements, 
							'product_lists' => $position_item->product_lists, 
						));
						$obj_position->save();
					}

					self::$language = Language::getLanguages(false);
					// $data = array();
		
					$arr_id = array('mobile' => 0, 'header' => 0, 'content' => 0, 'footer' => 0, 'product' => 0);
					foreach ($data_form as $hook) {
						// set disable cache with hook have module/widget disable cache
						if (isset($params_profile->disable_cache_hook->{$hook['name']})){
							$params_profile->disable_cache_hook->{$hook['name']} = (isset($hook['disable_cache']) && $hook['disable_cache']) ? 1 : 0;
						}
					

						$position_id = (int)isset($hook['position_id']) ? $hook['position_id'] : 0;

						$hook['position'] = (isset($hook['position']) && $hook['position']) ? $hook['position'] : '';
						$hook['name'] = (isset($hook['name']) && $hook['name']) ? $hook['name'] : 0;
						$position = Tools::strtolower($hook['position']);
						// Create new position with name is auto random, and save id of new for other positions reuse
						// position for other hook in this position to variable $header, $content...
						if ($position_id == 0 && $arr_id[$position] == 0) {
							$key = DeoSetting::getRandomNumber();
							$position_data = array(
								'name' => $position.$key,
								'position' => $position,
								'position_key' => 'position'.$key);
							$position_id = DeoHelper::autoCreatePosition($position_data);
							$arr_id[$position] = $position_id;
						} else if ($position_id != 0 && $arr_id[$position] == 0) {
							$arr_id[$position] = $position_id;
						}

						$obj_model = new DeoTemplateModel();
						$obj_model->id = $obj_model->getIdbyHookName($hook['name'], $arr_id[$position]);
						$obj_model->hook_name = $hook['name'];
						$obj_model->page = 'index';
						$obj_model->id_deotemplate_positions = $arr_id[$position];
						if (isset($hook['groups'])) {
							foreach (self::$language as $lang) {
								$params = '';
								if (self::$shortcode_lang) {
									foreach (self::$shortcode_lang as &$s_type) {
										foreach ($s_type as $key => $value) {
											$s_type[$key] = $key.'_'.$lang['id_lang'];
											// validate module
											unset($value);
										}
									}
								}
								$obj_model->params[$lang['id_lang']] = '';
								DeoShortCodesBuilder::$lang_id = $lang['id_lang'];
								foreach ($hook['groups'] as $groups) {
									$params = $this->getParamByHook($groups, $params, $hook['name']);
								}
								$obj_model->params[$lang['id_lang']] = $params;
							}
						}

						if ($obj_model->id) {
							$this->clearModuleCache();
							$obj_model->save();
						} else {
							$this->clearModuleCache();
							$obj_model->add();
						}
						$path = _PS_ROOT_DIR_.'/cache/smarty/cache/'.$this->module_name;

						$this->deleteDirectory($path);
					};

					// echo "<pre>";
					// print_r($arr_id);
					// echo "</pre>";
					// die();

					# Fix: keep data  param of setting profile. ( exception + other data )
					isset($params_profile->fullwidth_index_hook) ? $this->config_module['fullwidth_index_hook'] = $params_profile->fullwidth_index_hook : false;
					isset($params_profile->fullwidth_other_hook) ? $this->config_module['fullwidth_other_hook'] = $params_profile->fullwidth_other_hook : false;
					isset($params_profile->fullwidth_content_other_page) ? $this->config_module['fullwidth_content_other_page'] = $params_profile->fullwidth_content_other_page : false;
					isset($params_profile->disable_cache_hook) ? $this->config_module['disable_cache_hook'] = $params_profile->disable_cache_hook : false;
					isset($params_profile->breadcrumb) ? $this->config_module['breadcrumb'] = $params_profile->breadcrumb : false;
					isset($params_profile->mobile_mode) ? $this->config_module['mobile_mode'] = $params_profile->mobile_mode : false;
					isset($params_profile->ajax_cart) ? $this->config_module['ajax_cart'] = $params_profile->ajax_cart : false;
					isset($params_profile->customize) ? $this->config_module['customize'] = $params_profile->customize : false;

					$this->config_module['widgets_modules'] = $data_widgets_modules;
					$this->config_module['elements'] = $data_elements;
					$this->config_module['product_lists'] = $data_product_lists;
					$this->config_module['megamenu_group_active'] = $data_megamenu_group_active;

					$profile->params = json_encode($this->config_module);
					$profile->mobile = $arr_id['mobile'];
					$profile->header = $arr_id['header'];
					$profile->content = $arr_id['content'];
					$profile->footer = $arr_id['footer'];
					$profile->product = $arr_id['product'];
					$profile->save();

					$this->confirmations[] = $this->trans('Save successful', array(), 'Admin.Notifications.Success');
				}else{
					$this->errors[] = $this->trans('Submit data is invalid', array(), 'Admin.Notifications.Success');
				}
			}else{
				$this->errors[] = $this->trans('Not exist data_profile', array(), 'Admin.Notifications.Success');
			}
		}
		
		parent::postProcess();
	}
	
	public function ajaxProcessShowImportForm()
	{
		$id_profile = Tools::getValue('idProfile');
		$helper = new HelperForm();
		$helper->submit_action = 'submitImportData';
		$hook = array();
		$hook[] = array('id' => 'all', 'name' => $this->l('Profile'));
		$hook[] = array('id' => 'mobile', 'name' => $this->l('Position Mobile'));
		foreach (DeoSetting::getHook('mobile') as $val) {
			$hook[] = array('id' => $val, 'name' => '----'.$val);
		}
		$hook[] = array('id' => 'header', 'name' => $this->l('Position Header'));
		foreach (DeoSetting::getHook('header') as $val) {
			$hook[] = array('id' => $val, 'name' => '----'.$val);
		}
		$hook[] = array('id' => 'content', 'name' => $this->l('Position Content'));
		foreach (DeoSetting::getHook('content') as $val) {
			$hook[] = array('id' => $val, 'name' => '----'.$val);
		}
		$hook[] = array('id' => 'footer', 'name' => $this->l('Position Footer'));
		foreach (DeoSetting::getHook('footer') as $val) {
			$hook[] = array('id' => $val, 'name' => '----'.$val);
		}
		$hook[] = array('id' => 'product', 'name' => $this->l('Position Product'));
		foreach (DeoSetting::getHook('product') as $val) {
			$hook[] = array('id' => $val, 'name' => '----'.$val);
		}
		$inputs = array(
			array(
				'type' => 'file',
				'name' => 'importFile',
				'required' => true,
				'label' => $this->l('File'),
				'desc' => $this->l('Only accept xml file'),
			),
			array(
				'type' => 'select',
				'label' => $this->l('Import For'),
				'name' => 'import_for',
				'options' => array(
					'query' => $hook,
					'id' => 'id',
					'name' => 'name'
				),
				'desc' => $this->l('Select hook you want to import. Override all is only avail for import home.xml file'),
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Override'),
				'name' => 'override',
				'is_bool' => true,
				'desc' => $this->l('Override current data or not.'),
				'values' => DeoSetting::returnYesNo()
			),
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => '<input type="hidden" name="id_profile" id="id_profile" value="'.$id_profile.'"/>'
			)
		);
		$fields_form = array(
			'form' => array(
				'action' => Context::getContext()->link->getAdminLink('AdminDeoHome'),
				'input' => $inputs,
				// 'name' => 'importData',
				// 'buttons' => array(array('title' => $this->l('Save'), 'class' => 'button btn')),
				'submit' => array('title' => $this->l('Save'), 'class' => 'button btn btn-danger pull-right'),
				'tinymce' => false,
			),
		);
		$fields_value = array('file' => '', 'import_for' => 'all','override' => 0);
		$helper->fields_value = $fields_value;
		$array = array('hasError' => false, 'result' => $helper->generateForm(array($fields_form)));
		die(json_encode($array));
	}

	
	/**
	 * Show panel : widgets and modules
	 * .group-add
	 * .btn-new-widget
	 */
	public function ajaxProcessRenderList()
	{
		// get list module installed by hook position
		$list_modules = array();
		$reloadModule = false;

		if (Tools::getValue('reloadModule')) {
			# ReLoad : write to config
			$list_modules = DeoHelper::getModules();
			$reloadModule = true;

			$deo_cache_module = DeoHelper::correctEnCodeData(json_encode($list_modules));
			DeoHelper::updateValue('DEO_CACHE_MODULE', $deo_cache_module);
		} else {
			$deo_cache_module = DeoHelper::get('DEO_CACHE_MODULE');
			if ($deo_cache_module === false || $deo_cache_module === '') {
				# First Time : write to config
				$list_modules = DeoHelper::getModules();

				$deo_cache_module = DeoHelper::correctEnCodeData(json_encode($list_modules));
				DeoHelper::updateValue('DEO_CACHE_MODULE', $deo_cache_module);
			} else {
				# Second Time : read from config
				$list_modules = json_decode(DeoHelper::correctDeCodeData($deo_cache_module), true);

			}
		}

		// Get list author
		$author = array();
		foreach ($list_modules as &$mi) {
			$str = Tools::ucwords(Tools::strtolower($mi['author'] ? $mi['author'] : ''));
			if (!in_array($str, $author) && $str) {
				array_push($author, $str);
			}

			$inputs_additional = array(
				'name' =>'DeoModule',
				'name_module' =>$mi['name'],
				'hook' => '',
				'is_display'=> 1,
				'active' => 1,
				'form_id' => 'form_'.DeoSetting::getRandomNumber(),
			);
			if (in_array($mi['name'], DeoHelper::getModulesAccordion())){
				$inputs_additional = array_merge(array('accordion' => 'disable_accordion'), $inputs_additional);
			}
			if (in_array($mi['name'], DeoHelper::getModulesClass())){
				$inputs_additional = array_merge(array('class' => ''), $inputs_additional);
			}
			$mi['config'] = $inputs_additional;
		}


		// Get list of image or shortcodeFile 
		$tpl = $this->createTemplate('shortcodelist.tpl');
		$tpl->assign(array(
			'moduleDir' => _MODULE_DIR_,
			'reloadModule' => $reloadModule,
			'author' => $author,
			'listModule' => $list_modules,
			'shortCodeList' => (!$reloadModule) ? DeoShortCodeBase::getShortCodeInfos() : array(),
		));
		$html = $tpl->fetch();


		// Get list of image or shortcodeFile for sidebar
		$tpl = $this->createTemplate('shortcodelistsidebar.tpl');
		$tpl->assign(array(
			'moduleDir' => _MODULE_DIR_,
			'reloadModule' => $reloadModule,
			'author' => $author,
			'listModule' => $list_modules,
			'shortCodeList' => (!$reloadModule) ? DeoShortCodeBase::getShortCodeInfos() : array(),
		));
		$html_sidebar = $tpl->fetch();
		
		
		$array = array(
			'hasError' => false, 
			'result' => $html,
			'result_sidebar' => $html_sidebar,
		);

		die(json_encode($array));
	}
	
	public function ajaxProcessSaveData()
	{
		$type = Tools::getValue('type');
		$this->saveData('save', $type);
	}
	
	public function saveData($action, $type)
	{
		$data_form = Tools::getValue('dataForm');
		$data_form = json_decode($data_form, 1);

		$data_elements = Tools::getValue('data_elements', json_encode(array()));
		$data_widgets_modules = Tools::getValue('data_widgets_modules', json_encode(array()));
		$data_position = Tools::getValue('data_position', json_encode(array()));
		$data_product_lists = Tools::getValue('data_product_lists', json_encode(array()));
		$data_megamenu_group_active = Tools::getValue('data_megamenu_group_active');


		if (is_array($data_position) && count($data_position)){
			foreach ($data_position as $name_position => $position_item) {
				$obj_position = new DeoTemplatePositionsModel($position_item->id);
				$obj_position->params = json_encode(array(
					'widgets_modules' => $position_item->widgets_modules, 
					'elements' => $position_item->elements, 
					'product_lists' => $position_item->product_lists,
				));
				$obj_position->save();
			}
		}

		self::$language = Language::getLanguages(false);
		$data = array();
		$arr_id = array('mobile' => 0, 'header' => 0, 'content' => 0, 'footer' => 0, 'product' => 0);
		foreach ($data_form as $hook) {
			$position_id = (int)isset($hook['position_id']) ? $hook['position_id'] : '0';
			$hook['position'] = (isset($hook['position']) && $hook['position']) ? $hook['position'] : '';
			$hook['name'] = (isset($hook['name']) && $hook['name']) ? $hook['name'] : 0;
			$position = Tools::strtolower($hook['position']);
			$arr_id[$position] = (isset($arr_id[$position]) && $arr_id[$position]) ? $arr_id[$position] : '';
			// Create new position with name is auto random, and save id of new for other positions reuse
			// position for other hook in this position to variable $header, $content...
			if ($position_id == 0 && $arr_id[$position] == 0) {
				// enable save multithreading
				if ((int) DeoHelper::getConfig('SAVE_PROFILE_MULTITHREARING')) {
					if ((DeoHelper::getConfig('COOKIE_GLOBAL_MOBILE_ID') == 0 && $position == 'mobile')
						|| (DeoHelper::getConfig('COOKIE_GLOBAL_HEADER_ID') == 0 && $position == 'header')
						|| (DeoHelper::getConfig('COOKIE_GLOBAL_CONTENT_ID') == 0 && $position == 'content')
						|| (DeoHelper::getConfig('COOKIE_GLOBAL_FOOTER_ID') == 0 && $position == 'footer')
						|| (DeoHelper::getConfig('COOKIE_GLOBAL_PRODUCT_ID') == 0 && $position == 'product')) {
						$key = DeoSetting::getRandomNumber();
						$position_controller = new AdminDeoPositionsController();
						$position_data = array(
							'name' => $position.$key,
							'position' => $position,
							'position_key' => 'position'.$key
						);
						$position_id = $position_controller->autoCreatePosition($position_data);
						$arr_id[$position] = $position_id;
						switch ($position) {
							case 'mobile':
								DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_MOBILE_ID'), $position_id);
								break;
							case 'header':
								DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_HEADER_ID'), $position_id);
								break;
							case 'content':
								DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_CONTENT_ID'), $position_id);
								break;
							case 'footer':
								DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_FOOTER_ID'), $position_id);
								break;
							case 'product':
								DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_PRODUCT_ID'), $position_id);
								break;
						}
					} else {
						switch ($position) {
							case 'mobile':
								$arr_id[$position] = DeoHelper::getConfig('COOKIE_GLOBAL_MOBILE_ID');
								break;
							case 'header':
								$arr_id[$position] = DeoHelper::getConfig('COOKIE_GLOBAL_HEADER_ID');
								break;
							case 'content':
								$arr_id[$position] = DeoHelper::getConfig('COOKIE_GLOBAL_CONTENT_ID');
								break;
							case 'footer':
								$arr_id[$position] = DeoHelper::getConfig('COOKIE_GLOBAL_FOOTER_ID');
								break;
							case 'product':
								$arr_id[$position] = DeoHelper::getConfig('COOKIE_GLOBAL_PRODUCT_ID');
								break;
						}
					}
				} else {
					$key = DeoSetting::getRandomNumber();
					$position_controller = new AdminDeoPositionsController();
					$position_data = array(
						'name' => $position.$key,
						'position' => $position,
						'position_key' => 'position'.$key
					);
					$position_id = $position_controller->autoCreatePosition($position_data);
					$arr_id[$position] = $position_id;
				}
			} else if ($position_id != 0 && $arr_id[$position] == 0) {
				// enable save multithreading
				if ((int) DeoHelper::getConfig('SAVE_PROFILE_MULTITHREARING')) {
					switch ($position) {
						case 'mobile':
							DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_MOBILE_ID'), $position_id);
							break;
						case 'header':
							DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_HEADER_ID'), $position_id);
							break;
						case 'content':
							DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_CONTENT_ID'), $position_id);
							break;
						case 'footer':
							DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_FOOTER_ID'), $position_id);
							break;
						case 'product':
							DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_PRODUCT_ID'), $position_id);
							break;
					}
				};
				$arr_id[$position] = $position_id;
			}

			$obj_model = new DeoTemplateModel();
			$obj_model->id = $obj_model->getIdbyHookName($hook['name'], $arr_id[$position]);
			$obj_model->hook_name = $hook['name'];
			$obj_model->page = 'index';
			$obj_model->id_deotemplate_positions = $arr_id[$position];
			if (isset($hook['groups'])) {
				foreach (self::$language as $lang) {
					$params = '';
					if (self::$shortcode_lang) {
						foreach (self::$shortcode_lang as &$s_type) {
							foreach ($s_type as $key => $value) {
								$s_type[$key] = $key.'_'.$lang['id_lang'];
								// validate module
								unset($value);
							}
						}
					}
					$obj_model->params[$lang['id_lang']] = '';
					DeoShortCodesBuilder::$lang_id = $lang['id_lang'];
					foreach ($hook['groups'] as $groups) {
						$params = $this->getParamByHook($groups, $params, $hook['name'], $action);
					}
					$obj_model->params[$lang['id_lang']] = $params;
					if ($action == 'export') {
						$data[$lang['iso_code']] = (isset($data[$lang['iso_code']]) && $data[$lang['iso_code']]) ? $data[$lang['iso_code']] : '';
						$data[$hook['name']][$lang['iso_code']] = (isset($data[$hook['name']][$lang['iso_code']]) && $data[$hook['name']][$lang['iso_code']]) ? $data[$hook['name']][$lang['iso_code']] : '';

						if ($type == 'all' || (Tools::strpos($type, 'position') !== false)) {
							$data[$hook['name']][$lang['iso_code']] .= $params;
						} else {
							$data[$lang['iso_code']] .= $params;
						}
					}
				}
			}
			if ($action == 'save') {
				if ($obj_model->id) {
					$this->clearModuleCache();
					$obj_model->save();
				} else {
					$this->clearModuleCache();
					$obj_model->add();
				}
				$path = _PS_ROOT_DIR_.'/cache/smarty/cache/'.$this->module_name;
				$this->deleteDirectory($path);
			}
		};

		if ($action == 'save') {
			if ((int) DeoHelper::getConfig('SAVE_PROFILE_MULTITHREARING')) {
				if (Tools::getValue('dataFirst')) {
					$profile = new DeoTemplateProfilesModel(Tools::getValue('id_profile'));

					# Fix : must keep other data in param. ( exception + other data )
					//print_r($this->config_module);
					$params = json_decode($profile->params, true);
					isset($params['fullwidth_index_hook']) ? $this->config_module['fullwidth_index_hook'] = $params['fullwidth_index_hook'] : false;
					isset($params['fullwidth_other_hook']) ? $this->config_module['fullwidth_other_hook'] = $params['fullwidth_other_hook'] : false;
					isset($params['disable_cache_hook']) ? $this->config_module['disable_cache_hook'] = $params['disable_cache_hook'] : false;
					isset($params['breadcrumb']) ? $this->config_module['breadcrumb'] = $params['breadcrumb'] : false;
					isset($params['mobile_mode']) ? $this->config_module['mobile_mode'] = $params['mobile_mode'] : false;
					isset($params['ajax_cart']) ? $this->config_module['ajax_cart'] = $params['ajax_cart'] : false;
					isset($params['customize']) ? $this->config_module['customize'] = $params['customize'] : false;

					// $this->config_module['elements'] = $data_elements;
					// $this->config_module['widgets_modules'] = $data_widgets_modules;
					// $this->config_module['product_lists'] = $data_product_lists;

					DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_PROFILE_PARAM'), json_encode($this->config_module));
				} else {
					if (isset($this->config_module) && count($this->config_module) > 0) {
						$array_global_profile_param = json_decode(DeoHelper::getConfig('COOKIE_GLOBAL_PROFILE_PARAM'), true);
						$array_global_profile_param = array_merge($this->config_module, $array_global_profile_param);
						DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_PROFILE_PARAM'), json_encode($array_global_profile_param));
					}
				};
				if (Tools::getValue('dataLast')) {
					$profile = new DeoTemplateProfilesModel(Tools::getValue('id_profile'));
					$params = json_decode($profile->params, true);

					$profile->params = DeoHelper::getConfig('COOKIE_GLOBAL_PROFILE_PARAM');
					$profile->mobile = DeoHelper::getConfig('COOKIE_GLOBAL_MOBILE_ID');
					$profile->header = DeoHelper::getConfig('COOKIE_GLOBAL_HEADER_ID');
					$profile->content = DeoHelper::getConfig('COOKIE_GLOBAL_CONTENT_ID');
					$profile->footer = DeoHelper::getConfig('COOKIE_GLOBAL_FOOTER_ID');
					$profile->product = DeoHelper::getConfig('COOKIE_GLOBAL_PRODUCT_ID');
					$profile->save();

					DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_MOBILE_ID'), 0);
					DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_HEADER_ID'), 0);
					DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_CONTENT_ID'), 0);
					DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_FOOTER_ID'), 0);
					DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_PRODUCT_ID'), 0);
					DeoHelper::updateValue(DeoHelper::getConfigName('COOKIE_GLOBAL_PROFILE_PARAM'), '');
				}
			} else {
				$profile = new DeoTemplateProfilesModel(Tools::getValue('id_profile'));

				# Fix : must keep other data in param. ( exception + other data )
				//print_r($this->config_module);
				$params = json_decode($profile->params);
				isset($params->fullwidth_index_hook) ? $this->config_module['fullwidth_index_hook'] = $params->fullwidth_index_hook : false;
				isset($params->fullwidth_other_hook) ? $this->config_module['fullwidth_other_hook'] = $params->fullwidth_other_hook : false;
				isset($params->disable_cache_hook) ? $this->config_module['disable_cache_hook'] = $params->disable_cache_hook : false;
				isset($params->breadcrumb) ? $this->config_module['breadcrumb'] = $params->breadcrumb : false;
				isset($params->mobile_mode) ? $this->config_module['mobile_mode'] = $params->mobile_mode : false;
				isset($params->ajax_cart) ? $this->config_module['ajax_cart'] = $params->ajax_cart : false;
				isset($params->customize) ? $this->config_module['customize'] = $params->customize : false;
				// $this->config_module['widgets_modules'] = $data_widgets_modules;
				// $this->config_module['elements'] = $data_elements;
				// $this->config_module['product_lists'] = $data_product_lists;

				$profile->params = json_encode($this->config_module);
				$profile->mobile = $arr_id['mobile'];
				$profile->header = $arr_id['header'];
				$profile->content = $arr_id['content'];
				$profile->footer = $arr_id['footer'];
				$profile->product = $arr_id['product'];
				$profile->save();
			};
		};

		return $data;
	}

	public function renderList()
	{
		$this->context->controller->addJqueryUI('ui.sortable');
		$this->context->controller->addJqueryUI('ui.draggable');
		$this->context->controller->addJqueryUI('ui.droppable');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/bootstrap-colorpicker.js');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/bootstrap-colorpicker.css');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/form.css');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'animate.css');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/form.js');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/home.js');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/isotope.pkgd.min.js');
		$this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');

		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/jquery-validation-1.9.0/jquery.validate.js');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/jquery-validation-1.9.0/screen.css');
		
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'jquery.tagify.min.js');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'tagify.css');

		$css_files_available = DeoSetting::getCssFilesAvailable();
		Media::addJsDef(array('css_files_available' => $css_files_available));

		// $version = Configuration::get('PS_INSTALL_VERSION');
		// $tiny_path = ($version >= '1.6.0.13') ? 'admin/' : '';
		// $tiny_path .= 'tinymce.inc.js';

		// fix loading TINY_MCE library for all Prestashop_Versions
		$tiny_path = 'tinymce.inc.js';
		if (version_compare(_PS_VERSION_, '1.6.0.13', '>')) {
			$tiny_path = 'admin/tinymce.inc.js';
		}

		$this->context->controller->addJS(_PS_JS_DIR_.$tiny_path);
		$bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
		if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
			$bo_theme = 'default';
		}
		$this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
		$this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
		$this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
		//load javascript for menu tree, Product Carousel widget
		$tree = new HelperTreeCategories('123', null);
		$tree->render();
		$left_sidebar = false;
		$right_sidebar = false;
		$model = new DeoTemplateModel();
		$id_profile = Tools::getValue('id_deotemplate_profiles');
		if (!$id_profile) {
			$result_profile = DeoTemplateProfilesModel::getActiveProfile('index');
			//if empty default profile redirect to other
			if (!$result_profile) {
				$this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoProfiles');
				$this->redirect();
			}
			$id_profile = $result_profile['id_deotemplate_profiles'];
		} else {
			$profile_obj = new DeoTemplateProfilesModel($id_profile);
			if ($profile_obj->id) {
				$result_profile['id_deotemplate_profiles'] = $profile_obj->id;
				$result_profile['name'] = $profile_obj->name;
				$result_profile['mobile'] = $profile_obj->mobile;
				$result_profile['header'] = $profile_obj->header;
				$result_profile['content'] = $profile_obj->content;
				$result_profile['footer'] = $profile_obj->footer;
				$result_profile['product'] = $profile_obj->product;
				$result_profile['page'] = $profile_obj->page;
				$result_profile['data_shop'] = $profile_obj->data_shop;
			}
		}
		if (isset($result_profile) && $result_profile && $result_profile['data_shop']['id_shop'] == $this->context->shop->id) {
			$positions_dum = array();
			// Get default config - data of current position
			$positions_dum['mobile'] = $result_profile['mobile'] ? $model->getAllItemsByPosition('mobile', $result_profile['mobile'], $id_profile) : array('content' => $this->extractHookDefault(DeoHelper::getConfig('LIST_MOBILE_HOOK')), 'dataForm' => array());
			$positions_dum['header'] = $result_profile['header'] ? $model->getAllItemsByPosition('header', $result_profile['header'], $id_profile) : array('content' => $this->extractHookDefault(DeoHelper::getConfig('LIST_HEADER_HOOK')), 'dataForm' => array());
			$positions_dum['content'] = $result_profile['content'] ? $model->getAllItemsByPosition('content', $result_profile['content'], $id_profile) : array('content' => $this->extractHookDefault(DeoHelper::getConfig('LIST_CONTENT_HOOK')), 'dataForm' => array());
			$positions_dum['footer'] = $result_profile['footer'] ? $model->getAllItemsByPosition('footer', $result_profile['footer'], $id_profile) : array('content' => $this->extractHookDefault(DeoHelper::getConfig('LIST_FOOTER_HOOK')), 'dataForm' => array());
			$positions_dum['product'] = $result_profile['product'] ? $model->getAllItemsByPosition('product', $result_profile['product'], $id_profile) : array('content' => $this->extractHookDefault(DeoHelper::getConfig('LIST_PRODUCT_HOOK')), 'dataForm' => array());
			// Extract for display
			$positions = array();
			$position_data_form = array();

			foreach ($positions_dum as $key => $val) {
				$temp = $val['content'];
				$position_data_form[$key] = json_encode($val['dataForm']);
				foreach ($temp as $key_hook => &$row) {
					if (!is_array($row)) {
						$row = array('hook_name' => $key_hook, 'content' => '');
					}

					if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
						if ($key_hook == 'displayLeftColumn' && $temp[$key_hook]['content'] != '') {
							$left_sidebar = true;
						}else if ($key_hook == 'displayRightColumn' && $temp[$key_hook]['content'] != '') {
							$right_sidebar = true;
						}
						$row['class'] = 'col-md-3';
					} else {
						$row['class'] = 'col-md-12';
					}
				}
				if ($key == 'content'){
					foreach ($temp as $key_hook => &$row) {
						if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
							if ($key_hook == 'displayLeftColumn' && $left_sidebar){
								$row['class'] = 'col-md-3';
							}else if ($key_hook == 'displayRightColumn' && $right_sidebar){
								$row['class'] = 'col-md-3';
							}else if (!$left_sidebar && !$right_sidebar){
								$row['class'] = 'col-md-3 hidden';
							}
						} else if ($key_hook == 'displayHome'){
							if ($left_sidebar && $right_sidebar){
								$row['class'] = 'col-md-6';
							}else if ($left_sidebar || $right_sidebar){
								$row['class'] = 'col-md-9';
							}else{
								$row['class'] = 'col-md-12';
							}
						}
					}
				}

				$positions[$key] = $temp;
			}
			
			// Get list position for dropdowns
			$list_positions = array();
			$list_positions['mobile'] = $model->getListPositisionByType('mobile', $this->context->shop->id);
			$list_positions['header'] = $model->getListPositisionByType('header', $this->context->shop->id);
			$list_positions['content'] = $model->getListPositisionByType('content', $this->context->shop->id);
			$list_positions['footer'] = $model->getListPositisionByType('footer', $this->context->shop->id);
			$list_positions['product'] = $model->getListPositisionByType('product', $this->context->shop->id);
			// Get current position name

			$current_position = array();
			$current_position['mobile'] = $this->getCurrentPosition($list_positions['mobile'], $result_profile['mobile']);
			$current_position['header'] = $this->getCurrentPosition($list_positions['header'], $result_profile['header']);
			$current_position['content'] = $this->getCurrentPosition($list_positions['content'], $result_profile['content']);
			$current_position['footer'] = $this->getCurrentPosition($list_positions['footer'], $result_profile['footer']);
			$current_position['product'] = $this->getCurrentPosition($list_positions['product'], $result_profile['product']);
			$data_by_hook = array();
			$data_form = '{}';
			
			$data = $model->getAllItems($result_profile);

			if ($data) {
				$data_by_hook = $data['content'];
				$data_form = json_encode($data['dataForm']);
				foreach ($data_by_hook as $key_hook => &$row) {
					if (!is_array($row)) {
						$row = array('hook_name' => $key_hook, 'content' => '');
					}
					
					if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
						if ($key_hook == 'displayLeftColumn' && $left_sidebar){
							$row['class'] = 'col-md-3';
						}else if ($key_hook == 'displayRightColumn' && $right_sidebar){
							$row['class'] = 'col-md-3';
						} 
						$row['class'] = 'col-md-3';
					} else {
						if ($key_hook == 'displayHome'){
							if ($left_sidebar && $right_sidebar){
								$row['class'] = 'col-md-6';
							}else if ($left_sidebar || $right_sidebar){
								$row['class'] = 'col-md-9';
							}else{
								$row['class'] = 'col-md-12';
							}
						}
					}
				}
				if ($key == 'content'){
					foreach ($temp as $key_hook => &$row) {
						if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
							if ($key_hook == 'displayLeftColumn' && $left_sidebar){
								$row['class'] = 'col-md-3';
							}else if ($key_hook == 'displayRightColumn' && $right_sidebar){
								$row['class'] = 'col-md-3';
							}else if (!$left_sidebar && !$right_sidebar){
								$row['class'] = 'col-md-3 hidden';
							}
						} else if ($key_hook == 'displayHome'){
							if ($left_sidebar && $right_sidebar){
								$row['class'] = 'col-md-6';
							}else if ($left_sidebar || $right_sidebar){
								$row['class'] = 'col-md-9';
							}else{
								$row['class'] = 'col-md-12';
							}
						}
					}
				}
			}

			// Get list item for dropdown export
			$export_items = array();
			$export_items['Mobile'] = DeoSetting::getHook('mobile');
			$export_items['Header'] = DeoSetting::getHook('header');
			$export_items['Content'] = DeoSetting::getHook('content');
			$export_items['Footer'] = DeoSetting::getHook('footer');
			$export_items['Product'] = DeoSetting::getHook('product');
			// get shortcode information
			$shortcode_infos = DeoShortCodeBase::getShortCodeInfos();

			//include all short code default
			$shortcodes = Tools::scandir($this->tpl_path.'/deo_shortcodes', 'tpl');
			$shortcode_form = array();
			foreach ($shortcodes as $s_from) {
				if ($s_from == 'shortcodelist.tpl') {
					continue;
				}
				$shortcode_form[] = $this->tpl_path.'/deo_shortcodes/'.$s_from;
			}
			
			// ROOT//modules/deotemplate/views/templates/admin/deo_home/home.tpl
			$tpl = $this->createTemplate('home.tpl');
			
			$languages = array();
			foreach (Language::getLanguages(false) as $lang) {
				$languages[$lang['iso_code']] = $lang['id_lang'];
			}
			// check enable save multithreading
			if ((int) DeoHelper::getConfig('SAVE_PROFILE_MULTITHREARING')) {
				$check_save_multithreading = 1;
			} else {
				$check_save_multithreading = 0;
			};
			
			// check enable save submit
			if ((int) DeoHelper::getConfig('SAVE_PROFILE_SUBMIT')) {
				$check_save_submit = 1;
			} else {
				$check_save_submit = 0;
			};
			
			// error when submit
			$errorSubmit = '';
			if (Tools::isSubmit('errorSubmit')) {
				$errorSubmit = $this->l('There was an error during save. Please try again and check the value of server config: max_input_vars, make sure it is greater than 30000');
			}
			
			$current_id = Tools::getValue('id_deotemplate_profiles');
			$url_mobile_mode = $this->context->link->getAdminLink('AdminDeoProfiles').'&id_deotemplate_profiles='.$current_id.'&updatedeotemplate_profiles&tab_open=tab_mobile_mode';

			Media::addJsDef(array('deo_shortcode_infos' => json_encode($shortcode_infos)));
			Media::addJsDef(array('deo_languages' => json_encode($languages)));
			Media::addJsDef(array('deo_data_form' => $data_form));
			Media::addJsDef(array('moduleDir' => _MODULE_DIR_));

			$tpl->assign(array(
				'url_mobile_mode' => $url_mobile_mode,
				'positions' => $positions,
				'listPositions' => $list_positions,
				//'positionDataForm' => $position_data_form,
				'dataByHook' => $data_by_hook,
				'exportItems' => $export_items,
				'currentProfile' => $result_profile,
				'currentPosition' => $current_position,
				'profilesList' => $this->getAllProfiles($result_profile['id_deotemplate_profiles']),
				'tplPath' => $this->tpl_path,
				'ajaxShortCodeUrl' => Context::getContext()->link->getAdminLink('AdminDeoShortcodes'),
				'ajaxHomeUrl' => Context::getContext()->link->getAdminLink('AdminDeoHome'),
				'shortcodeForm' => $shortcode_form,
				'moduleDir' => _MODULE_DIR_,
				'imgModuleLink' => DeoHelper::getImgThemeUrl(),
				'deo_debug_mode' => (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true) ? true : false,
				// 'shortcodeInfos' => json_encode($shortcode_infos),
				// 'languages' => json_encode($languages),
				// 'dataForm' => $data_form,
				'errorText' => $this->error_text,
				'imgController' => Context::getContext()->link->getAdminLink('AdminDeoImages'),
				'widthList' => DeoSetting::returnWidthList(),
				'lang_id' => (int)$this->context->language->id,
				'idProfile' => $id_profile,
				'checkSaveMultithreading' => $check_save_multithreading,
				'checkSaveSubmit' => $check_save_submit,
				'errorSubmit' => $errorSubmit,
				'listAnimation' => DeoSetting::getAnimationsColumnGroup(),
				'left_sidebar' => $left_sidebar,
				'right_sidebar' => $right_sidebar,
			));

			$deo_cache_module = Configuration::get('DEO_CACHE_MODULE');
			if ($deo_cache_module === false || $deo_cache_module === '') {
				# First Time : write to config
				$list_modules = DeoHelper::getModules();

				$deo_cache_module = DeoHelper::correctEnCodeData(json_encode($list_modules));
				DeoHelper::updateValue('DEO_CACHE_MODULE', $deo_cache_module);
			} else {
				# Second Time : read from config
				$list_modules = json_decode(DeoHelper::correctDeCodeData($deo_cache_module), true);

			}

			// Get list author
			$author = array();
			foreach ($list_modules as &$mi) {
				$str = Tools::ucwords(Tools::strtolower($mi['author'] ? $mi['author'] : ''));
				if (!in_array($str, $author) && $str) {
					array_push($author, $str);
				}

				$inputs_additional = array(
					'name' => 'DeoModule',
					'name_module' => $mi['name'],
					'hook' => '',
					'is_display' => 1,
					'active' => 1,
					'form_id' => 'form_'.DeoSetting::getRandomNumber(),
				);
				if (in_array($mi['name'], DeoHelper::getModulesAccordion())){
					$inputs_additional = array_merge(array('accordion' => 'disable_accordion'), $inputs_additional);
				}
				if (in_array($mi['name'], DeoHelper::getModulesClass())){
					$inputs_additional = array_merge(array('class' => ''), $inputs_additional);
				}
				$mi['config'] = $inputs_additional;
			}


			// Get list of image or shortcodeFile
			$tpl->assign(array(
				'author' => $author,
				'listModule' => $list_modules,
				'shortCodeList' => DeoShortCodeBase::getShortCodeInfos()
			));


			$current_id = Tools::getValue('id_deotemplate_profiles');
			if (!$current_id) {
				$profile = DeoTemplateProfilesModel::getActiveProfile('index');
				$current_id = $profile['id_deotemplate_profiles'];
			}

			$profile_obj = new DeoTemplateProfilesModel($current_id);
			$url_preview = Context::getContext()->link->getPageLink('index', null, $this->context->employee->id_lang);
			if (Configuration::get('PS_REWRITING_SETTINGS') && (isset($profile_obj->friendly_url[$this->context->employee->id_lang])) && $profile_obj->friendly_url[$this->context->employee->id_lang]) {
				$url_preview .= $profile_obj->friendly_url[$this->context->employee->id_lang].'.html';
			}else{
				$url_preview .= '?id_deotemplate_profiles='.$current_id;
			}

			$url_customize_color = $this->context->link->getAdminLink('AdminDeoProfiles').'&id_deotemplate_profiles='.$current_id.'&updatedeotemplate_profiles&tab_open=tab_customize';

			// Get url preview + customize color
			$tpl->assign(array(
				'url_preview' => $url_preview,
				'url_customize_color' => $url_customize_color,
			));


			return $tpl->fetch();
		} else {
			$this->errors[] = $this->l('Your Profile ID is not exist!');
		}
	}
	
	private function exportData()
	{
		$action = Tools::getValue('action');
		$type = Tools::getValue('type');
		
		$data = $this->saveData($action, $type);
		if ($data) {
			if ($type == 'all') {
				$this->file_content = '<module>';
				foreach ($data as $key => $hook) {
					$this->file_content .= '<'.$key.'>';
					if (is_string($hook)) {
						$hook = array();
					}
					foreach ($hook as $lang => $group) {
						$this->file_content .= '<'.$lang.'>';
						$this->file_content .= '<![CDATA['.$group.']]>';
						$this->file_content .= '</'.$lang.'>';
					}
					$this->file_content .= '</'.$key.'>';
				}
				$this->file_content .= '</module>';
			} else if (Tools::strpos($type, 'position') !== false) {
				// Export position
				$this->file_content = '<'.'position'.'>';
				foreach ($data as $key => $hook) {
					$this->file_content .= '<'.$key.'>';
					if (is_string($hook)) {
						$hook = array();
					}
					foreach ($hook as $lang => $group) {
						$this->file_content .= '<'.$lang.'>';
						$this->file_content .= '<![CDATA['.$group.']]>';
						$this->file_content .= '</'.$lang.'>';
					}
					$this->file_content .= '</'.$key.'>';
				}
				$this->file_content .= '</position>';
			} else if ($type == 'group') {
				//export group
				foreach ($data as $lang => $group) {
					if (is_string($group)) {
						$this->file_content .= '<'.$lang.'>';
						$this->file_content .= '<![CDATA['.$group.']]>';
						$this->file_content .= '</'.$lang.'>';
					}
				}
			} else {
				//export all group in hook
				foreach ($data as $lang => $group) {
					if (is_string($group)) {
						$this->file_content .= '<'.$lang.'>';
						$this->file_content .= '<![CDATA['.$group.']]>';
						$this->file_content .= '</'.$lang.'>';
					}
				}
			}
			$href = $this->createXmlFile($type);
			$array = array('hasError' => false, 'result' => $href);
			die(json_encode($array));
		}
	}
			
	private function importData($language, $lang_id)
	{
		$upload_file = new Uploader('importFile');
		$upload_file->setAcceptTypes(array('xml'));
		$file = $upload_file->process();
		$file = $file[0];
		$files_content = simplexml_load_file($file['save_path']);
		$hook_list = array();
		$hook_list = array_merge($hook_list, explode(',', DeoHelper::getConfig('LIST_MOBILE_HOOK')));
		$hook_list = array_merge($hook_list, explode(',', DeoHelper::getConfig('LIST_HEADER_HOOK')));
		$hook_list = array_merge($hook_list, explode(',', DeoHelper::getConfig('LIST_CONTENT_HOOK')));
		$hook_list = array_merge($hook_list, explode(',', DeoHelper::getConfig('LIST_FOOTER_HOOK')));
		$hook_list = array_merge($hook_list, explode(',', DeoHelper::getConfig('LIST_PRODUCT_HOOK')));
		$import_for = Tools::getValue('import_for');
		$override = Tools::getValue('override');
		self::$language = Language::getLanguages(false);
		$id_profile = Tools::getValue('id_profile');
		$profile = new DeoTemplateProfilesModel($id_profile);
		if (!$profile->id || !$profile->mobile || !$profile->header || !$profile->content || !$profile->footer || !$profile->product) {
			// validate module
			die('Pease click save Profile before run import function. click back to try again!');
		}

		$lang_iso = 'en';
		$lang_list = array();
		foreach ($language as $lang) {
			$lang_list[$lang['iso_code']] = $lang['id_lang'];
			if ($lang['id_lang'] == $lang_id) {
				$lang_iso = $lang['iso_code'];
			}
		}
		// Import all mdoule
		if (isset($files_content->module)) {
			if ($import_for != 'all') {
				$this->errors[] = $this->trans('That is not the file for module, please select other file.', array(), 'Admin.Notifications.Error');
				return 'ERORR_ALL';
			}
			$module = $files_content->module;
			foreach ($hook_list as $hook) {
				$import_hook = $module->{$hook};
				$model = new DeoTemplateModel();
				foreach ($language as $lang) {
					$obj = $model->getIdbyHookNameAndProfile($hook, $profile, $lang_list[$lang['iso_code']]);
					if ($override) {
						$params = DeoHelper::replaceFormId($import_hook->{$lang['iso_code']});
					} else {
						$params = $obj['params'];
						$params .= DeoHelper::replaceFormId($import_hook->{$lang['iso_code']});
					}
					$model->updateDeotemplateLang($obj['id_deotemplate'], $lang_list[$lang['iso_code']], $params);
				}
			}
		} else if (isset($files_content->position)) {
			// Import a position
			$arr_positions = array('mobile', 'header', 'content', 'footer', 'product');
			if (!in_array($import_for, $arr_positions)) {
				$this->errors[] = $this->trans('That is not file for position, please select import for positon: header or content or footer or product', array(), 'Admin.Notifications.Error');
				return 'ERORR_POSITION';
			}
			$position = $files_content->position;
			$hook_name = '';
			if ($import_for == 'mobile') {
				$hook_name = DeoHelper::getConfigName('LIST_MOBILE_HOOK');
			} else if ($import_for == 'header') {
				$hook_name = DeoHelper::getConfigName('LIST_HEADER_HOOK');
			} else if ($import_for == 'content') {
				$hook_name = DeoHelper::getConfigName('LIST_CONTENT_HOOK');
			} else if ($import_for == 'footer') {
				$hook_name = DeoHelper::getConfigName('LIST_FOOTER_HOOK');
			} else if ($import_for == 'product') {
				$hook_name = DeoHelper::getConfigName('LIST_PRODUCT_HOOK');
			}
			$hook_list = explode(',', DeoHelper::get($hook_name));
			foreach ($hook_list as $hook) {
				$import_hook = $position->{$hook};
				$model = new DeoTemplateModel();
				foreach ($language as $lang) {
					$obj = $model->getIdbyHookNameAndProfile($hook, $profile, $lang_list[$lang['iso_code']]);
					if ($override) {
						$params = DeoHelper::replaceFormId($import_hook->{$lang['iso_code']});
					} else {
						$params = $obj['params'];
						$params .= DeoHelper::replaceFormId($import_hook->{$lang['iso_code']});
					}
					$model->updateDeotemplateLang($obj['id_deotemplate'], $lang_list[$lang['iso_code']], $params);
				}
			}
		} else {
			// Import only for a group - a hook
			$arr_positions = array('mobile', 'header', 'content', 'footer', 'product');
			if ($import_for == 'all' || in_array($import_for, $arr_positions)) {
				$this->errors[] = $this->trans('That is not file for module, please select other file.', array(), 'Admin.Notifications.Error');
				return 'ERORR_NOT_ALL';
			}
			$import_hook = $import_for;
			$hook = $import_for;
			foreach ($language as $lang) {
				$model = new DeoTemplateModel();
				$obj = $model->getIdbyHookNameAndProfile($hook, $profile, $lang_list[$lang['iso_code']]);
				if ($override) {
					$params = DeoHelper::replaceFormId($files_content->{$lang['iso_code']});
				} else {
					$params = $obj['params'];
					$params .= DeoHelper::replaceFormId($files_content->{$lang['iso_code']});
				}
				$model->updateDeotemplateLang($obj['id_deotemplate'], $lang_list[$lang['iso_code']], $params);
			}
		}
		// validate module
		unset($lang_iso);
		$this->confirmations[] = $this->trans('Import Success', array(), 'Admin.Notifications.Success');
		return 'ok';
	}

	public function extractHookDefault($str_hook = '')
	{
		$result = array();
		if ($str_hook) {
			$arr = explode(',', $str_hook);
			$len = count($arr);
			for ($i = 0; $i < $len; $i++) {
				$result[$arr[$i]] = $i;
			}
		}
		return $result;
	}

	public function getAllProfiles($id)
	{
		$current_id = Tools::getValue('id_deotemplate_profiles');
		$profile_obj = new DeoTemplateProfilesModel($current_id);
		return $profile_obj->getProfilesInPage($id);
	}

	/**
	 * Get template a position
	 */
	public function selectPosition($id = '')
	{
		$position = Tools::getValue('position');
		$id_position = $id ? $id : (int)Tools::getValue('id');
		$id_duplicate = (int)Tools::getValue('is_duplicate');
		$content = '';
		$tpl_name = 'position.tpl';
		$path = '';

		if (file_exists($this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name)) {
			$path = $this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name;
		} elseif (file_exists($this->getTemplatePath().$this->override_folder.$tpl_name)) {
			$path = $this->getTemplatePath().$this->override_folder.$tpl_name;
		}
		$model = new DeoTemplateModel();
		$positions_dum = $id_position ?
				$model->getAllItemsByPosition($position, $id_position) :
				array('content' => $this->extractHookDefault(DeoHelper::getConfig('LIST_' . Tools::strtoupper($position).'_HOOK')), 'dataForm' => array());
		$list_positions = $model->getListPositisionByType(Tools::strtolower($position), $this->context->shop->id);
		$current_position = $this->getCurrentPosition($list_positions, $id_position);
		$left_sidebar = false;
		$right_sidebar = false;

		foreach ($positions_dum['content'] as $key_hook => &$row) {
			if (!is_array($row)) {
				$row = array('hook_name' => $key_hook, 'content' => '');
			}
			if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
				if ($key_hook == 'displayLeftColumn' && $row['content'] != '') {
					$left_sidebar = true;
				}else if ($key_hook == 'displayRightColumn' && $row['content'] != '') {
					$right_sidebar = true;
				}
				$row['class'] = 'col-md-3';
			} else {
				$row['class'] = 'col-md-12';
			}
		}

		foreach ($positions_dum['content'] as $key_hook => &$row) {
			if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
				if ($key_hook == 'displayLeftColumn' && $left_sidebar){
					$row['class'] = 'col-md-3';
				}else if ($key_hook == 'displayRightColumn' && $right_sidebar){
					$row['class'] = 'col-md-3';
				}else if (!$left_sidebar && !$right_sidebar){
					$row['class'] = 'col-md-3 hidden';
				}
			} else if ($key_hook == 'displayHome'){
				if ($left_sidebar && $right_sidebar){
					$row['class'] = 'col-md-6';
				}else if ($left_sidebar || $right_sidebar){
					$row['class'] = 'col-md-9';
				}else{
					$row['class'] = 'col-md-12';
				}
			}
		}

		$positions = $positions_dum['content'];
		$data_form = json_encode($positions_dum['dataForm']);
		$id_position = $id_duplicate ? 0 : $id_position;
		$this->context->smarty->assign(array(
			'default' => $current_position,
			'position' => $position,
			'listPositions' => $list_positions,
			'config' => $positions,
			'left_sidebar' => $left_sidebar,
			'right_sidebar' => $right_sidebar,
		));
		$content = $this->context->smarty->fetch($path);
		$result = array('status' => 'SUCCESS', 'message' => '', 'html' => $content,
			'position' => $position, 'id' => $id_position, 'data' => $data_form);

		die(json_encode($result));
		// Check this position is using by other profile
	}

	/**
	 * Process: add, update, duplicate a position
	 */
	public function processPosition()
	{
		$name = Tools::getValue('name');
		$position = Tools::getValue('position');
		$id_position = (int)Tools::getValue('id');
		$mode = Tools::getValue('mode');
		if ($mode == 'duplicate') {
			$adapter = new AdminDeoPositionsController();
			$id_position = $adapter->duplicatePosition($id_position, 'ajax', $name);
		} else if ($mode == 'new') {
			$key = DeoSetting::getRandomNumber();
			$name = $name ? $name : $position.$key;
			$position_controller = new AdminDeoPositionsController();

			$position_data = array(
				'name' => $name,
				'position' => $position,
				'position_key' => 'position'.$key,
			);
			$id_position = $position_controller->autoCreatePosition($position_data);
		} else if ($mode == 'edit') {
			// Edit only name
			$position_controller = new AdminDeoPositionsController();
			$position_controller->updateName($id_position, $name);
		}
		// Reload position
		if ($mode == 'new' || $mode == 'duplicate') {
			$this->selectPosition($id_position);
		} else {
			die(json_encode(array('status' => 'SUCCESS')));
		}
	}

	private function getCurrentPosition($list, $id)
	{
		if ($list) {
			foreach ($list as $item) {
				if (isset($item['id_deotemplate_positions']) && $item['id_deotemplate_positions'] == $id) {
					return array('id' => $id, 'name' => $item['name']);
				}
			}
		}
		return array('id' => '0', 'name' => '');
	}
	
	private function getParamByHook($groups, $params, $hook, $action = 'save')
	{	
		$groups['params']['specific_type'] = (isset($groups['params']['specific_type']) && $groups['params']['specific_type']) ? $groups['params']['specific_type'] : '';
		$groups['params']['controller_pages'] = (isset($groups['params']['controller_pages']) && $groups['params']['controller_pages']) ? $groups['params']['controller_pages'] : '';
		$groups['params']['controller_id'] = (isset($groups['params']['controller_id']) && $groups['params']['controller_id']) ? $groups['params']['controller_id'] : '';
		$params .= '[DeoRow'.DeoShortCodesBuilder::converParamToAttr($groups['params'], 'DeoRow', $this->theme_dir).']';
		//check exception page
		// $this->saveExceptionConfig($hook, $groups['params']['specific_type'], $groups['params']['controller_pages'], $groups['params']['controller_id']);
		foreach ($groups['columns'] as $columns) {
			$columns['params']['specific_type'] = (isset($columns['params']['specific_type']) && $columns['params']['specific_type']) ? $columns['params']['specific_type'] : '';
			$columns['params']['controller_pages'] = (isset($columns['params']['controller_pages']) && $columns['params']['controller_pages']) ? $columns['params']['controller_pages'] : '';
			$columns['params']['controller_id'] = (isset($columns['params']['controller_id']) && $columns['params']['controller_id']) ? $columns['params']['controller_id'] : '';
			// $this->saveExceptionConfig($hook, $columns['params']['specific_type'], $columns['params']['controller_pages'], $columns['params']['controller_id']);
			$params .= '[DeoColumn'.DeoShortCodesBuilder::converParamToAttr($columns['params'], 'DeoColumn', $this->theme_dir).']';
			foreach ($columns['widgets'] as $widgets) {
				if ($widgets['type'] == 'DeoTabs' || $widgets['type'] == 'DeoAccordions') {

					$params .= '['.$widgets['type'].DeoShortCodesBuilder::converParamToAttr($widgets['params'], $widgets['type'], $this->theme_dir).']';
					foreach ($widgets['widgets'] as $sub_widgets) {
						$type_sub = Tools::substr($widgets['type'], 0, -1);
						$params .= '['.$type_sub.DeoShortCodesBuilder::converParamToAttr($sub_widgets['params'], str_replace('_', '_sub_', $widgets['type']), $this->theme_dir).']';
						foreach ($sub_widgets['widgets'] as $sub_widget) {
							$params .= '['.$sub_widget['type']
									.DeoShortCodesBuilder::converParamToAttr($sub_widget['params'], $sub_widget['type'], $this->theme_dir).'][/'
									.$sub_widget['type'].']';
						}
						$params .= '[/'.$type_sub.']';
					}
					$params .= '[/'.$widgets['type'].']';
					// print_r($params);
					// die();
				}else if ($widgets['type'] == 'DeoPopup') {
					$params .= '['.$widgets['type'].DeoShortCodesBuilder::converParamToAttr($widgets['params'], $widgets['type'], $this->theme_dir).']';
					foreach ($widgets['widgets'] as $sub_widgets) {
						$params .= '['.$sub_widgets['type'].DeoShortCodesBuilder::converParamToAttr($sub_widgets['params'], $sub_widgets['type'], $this->theme_dir).'][/'.$sub_widgets['type'].']';
					}
					$params .= '[/'.$widgets['type'].']';
				} else {
					$params .= '['.$widgets['type'].DeoShortCodesBuilder::converParamToAttr($widgets['params'], $widgets['type'], $this->theme_dir).'][/'.$widgets['type'].']';
					if ($widgets['type'] == 'DeoModule' && $action == 'save') {
						$is_delete = (int)$widgets['params']['is_display'];
						if ($is_delete) {
							if (!isset($widgets['params']['hook'])) {
								// FIX : Module not choose hook -> error
								$widgets['params']['hook'] = '';
							}
							$this->deleteModuleFromHook($widgets['params']['hook'], $widgets['params']['name_module']);
						}
					} 
					// else if ($widgets['type'] == 'DeoProductCarousel') {
					// 	if ($widgets['params']['order_way'] == 'random') {
					// 		$this->config_module[$hook]['productCarousel']['order_way'] = 'random';
					// 	}
					// }
				}
			}
			$params .= '[/DeoColumn]';
		}
		$params .= '[/DeoRow]';
		return $params;
	}
	
	public function clearModuleCache()
	{
		$module = DeoTemplate::getInstance();
		$module->clearHookCache();
	}
	
	private function deleteDirectory($dir)
	{
		if (!file_exists($dir)) {
			return true;
		}
		if (!is_dir($dir) || is_link($dir)) {
			return unlink($dir);
		}
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}
			if (!$this->deleteDirectory($dir.'/'.$item)) {
				chmod($dir.'/'.$item, 0777);
				if (!$this->deleteDirectory($dir.'/'.$item)) {
					return false;
				}
			}
		}
		return rmdir($dir);
	}
	
	private function deleteModuleFromHook($hook_name, $module_name)
	{
		$res = true;
		$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
				WHERE `id_hook` IN( 
					SELECT `id_hook` FROM `'._DB_PREFIX_.'hook`
					WHERE name ="'.pSQL($hook_name).'") AND `id_module` IN( SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE name ="'.pSQL($module_name).'")';
		$res &= Db::getInstance()->execute($sql);
		return $res;
	}
	
	private function saveExceptionConfig($hook, $type, $page, $ids)
	{
		if (!$type) {
			return;
		}

		if ($type == 'all') {
			if ($type != '') {
				$list = explode(',', $page);
				foreach ($list as $val) {
					$val = trim($val);
					if ($val && (!is_array($this->config_module) || !isset($this->config_module[$hook]) || !isset($this->config_module[$hook]['exception']) || !isset($val, $this->config_module[$hook]['exception']))) {
						$this->config_module[$hook]['exception'][] = $val;
					}
				}
			}
		} else {
			$this->config_module[$hook][$type] = array();
			if ($type != 'index') {
				$ids = explode(',', $ids);
				foreach ($ids as $val) {
					$val = trim($val);
					if (!in_array($val, $this->config_module[$hook][$type])) {
						$this->config_module[$hook][$type][] = $val;
					}
				}
			}
		}
	}
	
	public function createXmlFile($title)
	{
		$file_content = '<?xml version="1.0" encoding="UTF-8"?>';
		$file_content .= '<data>';
		$file_content .= $this->file_content;
		$file_content .= '</data>';
		//save file content to sample data

		$folder = $this->theme_dir.'export/';
		if (!is_dir($folder)) {
			@mkdir($folder, 0755, true);
		}
		if ($title == 'all') {
			$title = 'deotemplate';
		}

		if ($file_content){
			DeoSetting::writeFile($folder, $title.'.xml', $file_content);
		}

		return _THEMES_DIR_.Context::getContext()->shop->theme_name.'/'.'export/'.$title.'.xml';
	}
	
	/**
	 * PERMISSION ACCOUNT demo@demo.com
	 * OVERRIDE CORE
	 * classes\controller\AdminController.php
	 */
	public function getTabSlug()
	{
		if (empty($this->tabSlug)) {
			
			// GET RULE FOLLOW AdminDeoProfiles
			$result = Db::getInstance()->getRow('
				SELECT `id_tab`
				FROM `'._DB_PREFIX_.'tab`
				WHERE UCASE(`class_name`) = "'.'AdminDeoProfiles'.'"
			');
			$profile_id = $result['id_tab'];
			$this->tabSlug = Access::findSlugByIdTab($profile_id);
		}

		return $this->tabSlug;
	}
}
