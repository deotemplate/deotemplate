<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


include_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperMegamenu.php');
include_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoMegamenuModel.php');
include_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoMegamenuGroupModel.php');

require_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoWidgetBaseModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoWidgetModel.php');

class AdminDeoMegamenuController extends ModuleAdminControllerCore
{
	
	private $_html = '';
	private $html = '';
	private $configs = '';
	protected $params = null;
	public $_languages;
	public $_defaultFormLanguage;
	public $base_config_url;
	public $widget;
	public $theme_name;
	public $tabs;
	private $current_group = array('id_group' => 0, 'title' => '', 'group_type' => '');
	public $group_data = array(
		'id_deomegamenu_group' => '',
		'title' => null,
		'id_shop' => '',
		'active' => '1',
		'group_type' => 'horizontal',
		'tab_style' => '0',
		'show_cavas' => '1',
		'type_sub' => 'left',
		'group_class' => '',
	);

	public function __construct()
	{
		$this->name = 'deotemplate';
		$this->table = 'deomegamenu';
		$this->bootstrap = true;
		$this->secure_key = Tools::encrypt($this->name);
		
		parent::__construct();
		$this->base_config_url = AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getValue('token');
		$this->languages();
		$this->theme_name = Context::getContext()->shop->theme->getName();
		$this->img_path = _PS_ALL_THEMES_DIR_.$this->theme_name.'/'.DeoHelper::getThemeMediaDir('img').'/img/icons/';

		$this->widget = new DeoWidgetModel();
	}

	public function initPageHeaderToolbar()
	{
		if (Tools::getValue('configuregroup') && Tools::getValue('liveeditor')){
			$this->page_header_toolbar_btn['save'] = array(
				'short' => 'Save',
				'href' => 'javascript:void(0)',
				'desc' => $this->l('Save'),
			);

			$this->page_header_toolbar_btn['groups'] = array(
				'short' => 'GroupsMenu',
				'href' => $this->context->link->getAdminLink('AdminDeoMegamenu'),
				'target' => '_blank',
				'desc' => $this->l('Groups Menu'),
				'icon' => 'icon-sitemap'
			);
		}else{
			$this->page_header_toolbar_btn['import_groups'] = array(
				'short' => 'ImportGroupMenu',
				'href' => 'javascript:void(0)',
				'desc' => $this->l('Import Group Menu'),
				'icon' => 'icon-cloud-upload'
			);
		}

		$this->page_header_toolbar_btn['widgets'] = array(
			'short' => 'Widgets',
			'href' => $this->context->link->getAdminLink('AdminDeoWidgetsMegamenu'),
			'target' => '_blank',
			'desc' => $this->l('Widgets'),
			'icon' => 'icon-list-alt'
		);

		parent::initPageHeaderToolbar();
	}

	public function languages()
	{
		$cookie = $this->context->cookie;
		$allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		if ($allow_employee_form_lang && !$cookie->employee_form_lang) {
			$cookie->employee_form_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		}
		$use_lang_from_cookie = false;
		$this->_languages = Language::getLanguages(false);
		if ($allow_employee_form_lang) {
			foreach ($this->_languages as $lang) {
				if ($cookie->employee_form_lang == $lang['id_lang']) {
					$use_lang_from_cookie = true;
				}
			}
		}
		if (!$use_lang_from_cookie) {
			$this->_defaultFormLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
		} else {
			$this->_defaultFormLanguage = (int)$cookie->employee_form_lang;
		}
	}


	public function postProcess()
	{
		if (!$this->access('configure')) {
			$this->displayWarning($this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error'));
			return;
		}

		if (count($this->errors) > 0) {
			$this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');
			if ($this->ajax) {
				$array = array('hasError' => true, 'errors' => $this->errors[0]);
				die(json_encode($array));
			}
			return;
		}
		
		if (Tools::isSubmit('savesubmenu')) {
			# add + edit menu
			$this->saveMenu();
		}
		if (Tools::isSubmit('deletemenu')) {
			$this->deleteMenu();
		}
		if (Tools::isSubmit('save'.$this->name) && Tools::getValue('delete_many_menu')) {
			$this->delete_many_menu();
		}
		if (Tools::isSubmit('save'.$this->name) && Tools::getValue('doduplicate')) {
			$this->duplicateMenu();
		}
		if (Tools::isSubmit('submitGroup') || Tools::isSubmit('submitSaveAndStay')) {
			# add + edit group
			if ($this->postValidation()) {
				$this->saveGroup();
			}
		}
		if (Tools::isSubmit('deletegroup')) {
			if ($this->postValidation()) {
				$this->deleteGroup();
			}
		}
		if (Tools::isSubmit('changeGStatus')) {
			if ($this->postValidation()) {
				$this->changeStatusGroup();
			}
		}
		if (Tools::getValue('correctmodule')) {
			$this->correctModule();
		}
		
		if (Tools::getValue('doupdategrouppos') && Tools::isSubmit('updateGroupPosition')) {
			$this->changePositionGroup();
		}
		
		if (Tools::getValue('doupdatepos') && Tools::isSubmit('updatePosition')) {
			$this->changePositionMenu();
		}
		
		if (Tools::getValue('liveeditor') && Tools::getValue('do') == 'ajxsavemenu') {
			$this->ajxsavemenu();
		}

		if (Tools::getValue('liveeditor') && Tools::getValue('do') == 'ajxgensubmenu') {
			$this->ajxgensubmenu();
		}

		if (Tools::getValue('liveeditor') && Tools::getValue('do') == 'ajxchangeposition') {
			$this->ajxchangeposition();
		}

		if (Tools::getValue('load_form_submenu')) {
			$this->renderFormConfig();
		}

		if (Tools::getValue('getListWidgets')) {
			die(json_encode($this->loadwidgets(Tools::getValue('backoffice'))));
		}
		
		if (Tools::isSubmit('duplicategroup')) {
			if ($this->postValidation()) {
				$this->duplicateGroup();
			}
		}

		if (Tools::isSubmit('exportgroup')) {
			$this->exportGroup();
		}

		if (Tools::isSubmit('importgroup')) {
			if ($this->postValidation()) {
				if ($this->importGroup()) {
					Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu').'&success=importgroup');
				} else {
					$this->html .= $this->displayError($this->l('The file could not be import.'));
				}
			}
		}
	}

	public function loadwidgets($backoffice = 1)
	{
		$result = array('success' => false);
		$model = new DeoWidgetModel();

		$result['data'] = $model->loadWidgetsData($backoffice);
		$result['success'] = true;
		
		die(json_encode($result));
	}

	public function renderList()
	{
		// move from postProcess only return html
		if (Tools::getValue('liveeditor')) {
			if (Tools::getValue('do')) {
				switch (Tools::getValue('do')) {
					case 'ajxsavemenu':
						echo $this->ajxsavemenu();
						break;
					case 'ajxgenmenu':
						echo $this->ajxgenmenu();
						break;
					case 'loadwidget':
						echo $this->loadwidget();
						break;
					case 'getwidget':
						echo $this->getWidget();
						break;    
					default:
						break;
				}
				die;
			}
		}


		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');
		// $this->displaySuccessMessage();
		$tpl = $this->createTemplate('group_list.tpl');
		$media_dir = DeoHelper::getMediaDir();

		$this->context->controller->addJqueryUI('ui.sortable');
		$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.sortable.min.js');
		$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.tabs.min.js');
		$this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/jquery.nestable.js');
		$this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/form.js');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');

		$this->context->controller->addCss(__PS_BASE_URI__.'js/jquery/ui/themes/base/jquery.ui.tabs.css');
		$this->context->controller->addCss(__PS_BASE_URI__.$media_dir.'css/megamenu/admin/form.css');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'fonts.css');
		$admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $admin_webpath);
		$bo_theme = ((Validate::isLoadedObject($this->context->employee)
			&& $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');

		if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $bo_theme . DIRECTORY_SEPARATOR . 'template')) {
			$bo_theme = 'default';
		}

		if ($this->context->controller->ajax) {
			$html = '<script type="text/javascript" src="' . __PS_BASE_URI__ . $admin_webpath . '/themes/' . $bo_theme . '/js/vendor/typeahead.min.js"></script>';
			$html .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . $admin_webpath . '/themes/' . $bo_theme . '/js/
			tree.js"></script>';
		} else {
			$this->context->controller->addJs(__PS_BASE_URI__ . $admin_webpath . '/themes/' . $bo_theme . '/js/vendor/typeahead.min.js');
			$this->context->controller->addJs(__PS_BASE_URI__ . $admin_webpath . '/themes/' . $bo_theme . '/js/tree.js');
		}

		if (Tools::getIsset('configuregroup')) {
			if (Shop::isFeatureActive() || Shop::getTotalShops(false, null) >= 2) {
				$shop_context = Shop::getContext();
				$context = Context::getContext();

				$noShopSelection = $shop_context == Shop::CONTEXT_ALL || ($context->controller->multishop_context_group == false && $shop_context == Shop::CONTEXT_GROUP);
				if ($noShopSelection) {
					// $current_shop_value = '';
					$this->errors[] = $this->l('We not support this setting for All Stores');
					return false;
				} elseif ($shop_context == Shop::CONTEXT_GROUP) {
					// $current_shop_value = 'g-' . Shop::getContextShopGroupID();
					$this->errors[] = $this->l('We not support this setting for Group Stores');
					return false;
				} else {
					// $current_shop_value = 's-' . Shop::getContextShopID();
				}

				if (Tools::getIsset('id_group')) {
					$sql = 'SELECT id_shop FROM ' ._DB_PREFIX_.'deomegamenu_group WHERE id_deomegamenu_group = '.Tools::getValue('id_group');
					$data_shop = Db::getInstance()->getRow($sql);

					if ($data_shop['id_shop'] != Context::getContext()->shop->id){
						$this->errors[] = $this->l('This ID is not exist in this store!');
						return false;
					}
				}
			}

			$return = $this->renderGroupConfig();
			return $return;
		}else{
			$mod_group = new DeoMegamenuGroupModel();
			$id_shop = $this->context->shop->id;
			$groups = $mod_group->getGroups(null, $id_shop);
			
			$languages = Language::getLanguages(false);
			foreach ($languages as $lang) {
				$this->group_data['title'][$lang['id_lang']] = '';
				$this->group_data['title_fo'][$lang['id_lang']] = '';
			}

			// echo "<pre>";
			foreach ($groups as $key => $group) {
				$params = json_decode(DeoMegamenuHelper::base64Decode($group['params']), true);
				
				if (!$params) return false;

				// $this->current_group['group_type'] = ($params['group_type'] == 'horizontal')?'Horizontal':'Vertical';
				$groups[$key] = array_merge($groups[$key],$params);
				$group_result = array();
				foreach ($params as $k => $v) {
					$group_result[$k] = $v;
				}
				$obj_group = new DeoMegamenuGroupModel($group['id_deomegamenu_group']);
				foreach ($languages as $lang) {
					$group_result['title'][$lang['id_lang']] = $obj_group->title[$lang['id_lang']];
					$group_result['title_fo'][$lang['id_lang']] = $obj_group->title_fo[$lang['id_lang']];
				}
				// $group_result['title'] = $group['title'];
				$group_result['id_deomegamenu_group'] = $group['id_deomegamenu_group'];
				$group_result['id_shop'] = $group['id_shop'];
				$group_result['hook'] = $group['hook'];
				$group_result['active'] = $group['active'];
				$group_result['tab_style'] = isset($group['tab_style']) ? $group['tab_style'] : 0;


				if ($group_result) {
					$this->group_data = array_merge($this->group_data, $group_result);
				}


				$groups[$key]['status'] = $this->displayGStatus($group['id_deomegamenu_group'], $group['active']);
				
			}

			$tpl->assign(array(
				'link' => $this->context->link,
				'update_group_position_link' => $this->context->link->getAdminLink('AdminDeoMegamenu'),
				'groups' => $groups,
				'languages' => $this->context->controller->getLanguages(),
				'msecure_key' => $this->secure_key,
			));

			$this->displaySuccessMessage();
			return $this->html.$tpl->fetch();
		}        
	}
	
	public function renderGroupConfig()
	{
		// get data init for form
		$mod_group = new DeoMegamenuGroupModel();
		$id_shop = $this->context->shop->id;
		$groups = $mod_group->getGroups(null, $id_shop);
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang) {
			$this->group_data['title'][$lang['id_lang']] = '';
			$this->group_data['title_fo'][$lang['id_lang']] = '';
		}
		foreach ($groups as $key => $group) {
			if ($group['id_deomegamenu_group'] == Tools::getValue('id_group')) {
				$this->current_group['id_group'] = $group['id_deomegamenu_group'];
				$this->current_group['title'] = $group['title'];

				$params = json_decode(DeoMegamenuHelper::base64Decode($group['params']), true);
				
				$this->current_group['group_type'] = ($params['group_type'] == 'horizontal')?'Horizontal':'Vertical';
				if ($params) {
					$group_result = array();
				}
				foreach ($params as $k => $v) {
					$group_result[$k] = $v;
				}
				$obj_group = new DeoMegamenuGroupModel($group['id_deomegamenu_group']);
				foreach ($languages as $lang) {
					$group_result['title'][$lang['id_lang']] = $obj_group->title[$lang['id_lang']];
					$group_result['title_fo'][$lang['id_lang']] = $obj_group->title_fo[$lang['id_lang']];
				}
				// $group_result['title'] = $group['title'];
				$group_result['id_deomegamenu_group'] = $group['id_deomegamenu_group'];
				$group_result['id_shop'] = $group['id_shop'];
				$group_result['hook'] = $group['hook'];
				$group_result['active'] = $group['active'];
				$group_result['tab_style'] = $group['tab_style'];

				if ($group_result) {
					$this->group_data = array_merge($this->group_data, $group_result);
				}
			}
		}

		// build form 
		$description = $this->l('Add New Group');
		if (!Tools::isSubmit('deletegroup') && !Tools::isSubmit('duplicategroup') && !Tools::isSubmit('addmenuproductlayout') && !Tools::isSubmit('importgroup') && !Tools::isSubmit('importwidgets') && !Tools::isSubmit('addNewGroup') && $this->current_group['id_group']) {
			$description = $this->l('Editting:').' '.$this->current_group['title'];
		}

		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $description,
				'icon' => 'icon-cogs'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Group Name'),
					'name' => 'title_group',
					'lang' => true,
					'required' => 1,
					'desc' => $this->l('Show on Back End'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Title Heading'),
					'name' => 'title_fo_group',
					'desc' => $this->l('Show on Front End'),
					'lang' => true,
					'form_group_class' => 'title-vertical',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Group Class CSS'),
					'name' => 'group[group_class]',
					'lang' => false,
					'class' => 'group-class',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Group Type'),
					'name' => 'group[group_type]',
					'id' => 'group_type',
					'options' => array('query' => array(
							array('id' => 'horizontal', 'name' => $this->l('Horizontal')),
							array('id' => 'vertical', 'name' => $this->l('Vertical')),
						),
						'id' => 'id',
						'name' => 'name'),
					'default' => 'horizontal',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Align'),
					'name' => 'group[type_sub]',
					'id' => 'type_sub',
					'options' => array('query' => array(
							array('id' => 'left', 'name' => $this->l('Left')),
							array('id' => 'right', 'name' => $this->l('Right')),
						),
						'id' => 'id',
						'name' => 'name'
					),
					'default' => 'left',
					'form_group_class' => 'group-type-group group-vertical',
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Tab Style'),
					'name' => 'tab_style',
					'id' => 'tab_style',
					'values' => DeoSetting::returnYesNo(),
					'default' => '0',
					'desc' =>  $this->l('Use tab style for all horizontal menu'),
					'form_group_class' => 'group-type-group group-horizontal',
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Menu Mobile'),
					'name' => 'group[show_cavas]',
					'id' => 'show_cavas',
					'values' => DeoSetting::returnYesNo(),
					'default' => '1',
					'desc' =>  $this->l('Menu friendly on mobile devices'),
					'form_group_class' => 'group-type-group group-horizontal',
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Enable'),
					'name' => 'active',
					'is_bool' => true,
					'values' => $this->getSwitchValue('active'),
				),
			),
			'submit' => array(
				'title' => $this->l('Save')
			),
			'buttons' => array(
				'save-and-stay' => array(
					'title' => $this->l('Save and Stay'),
					'name' => 'submitSaveAndStay',
					'type' => 'submit',
					'value' => 'saveandstay',
					'class' => 'btn btn-default pull-right',
					'icon' => 'process-icon-save'
				)
			)
		);

		if (Tools::isSubmit('id_group') && DeoMegamenuGroupModel::groupExists((int)Tools::getValue('id_group'))) {
			$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_group');
		} else if ($this->current_group['id_group'] && DeoMegamenuGroupModel::groupExists($this->current_group['id_group'])) {
			$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_group');
		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->name_controller = 'AdminDeoMegamenu';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		// $this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitGroup';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminDeoMegamenu', false).'&configure='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminDeoMegamenu');

		$helper->tpl_vars = array(
			'fields_value' => $this->getGroupFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);
		
		$this->context->smarty->assign(array(
			'generate_form' => $helper->generateForm($this->fields_form),
			'list_widgets' => $this->showCategoryListWidgets(),
		));

		// return $helper->generateForm(array($fields_form));

	   
		$this->showLiveEditorSetting();
		

		$content = Context::getContext()->smarty->fetch($this->createTemplate('liveeditor.tpl'));
		return $content;
	}


	/**
	 * show live editor tools
	 */
	protected function showLiveEditorSetting()
	{
		$media_dir = DeoHelper::getMediaDir();
		$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.dialog.min.js');
		$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.draggable.min.js');
		$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.droppable.min.js');

		// load js + css for widget
		$this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
		$this->context->controller->addJS(__PS_BASE_URI__.'js/admin/tinymce.inc.js');
		$this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/jquery-validation-1.9.0/jquery.validate.js');
		$this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/megamenu/admin/jquery-validation-1.9.0/screen.css');
		$this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/megamenu/admin/admin.css');
		$this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/show.js');
		$this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/form.js');
		$this->context->controller->addCss(__PS_BASE_URI__.$media_dir.'css/megamenu/admin/liveeditor.css');
		$this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/liveeditor.js');
		$this->context->controller->addCSS(DeoHelper::getThemeUri().DeoHelper::getCssDir().'megamenu/typo.css');

		// show modal select image
		$bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
		if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
			$bo_theme = 'default';
		}
		$this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
		$this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
		$this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');

		
		$id_group = Tools::getValue('id_group');
		// $group_obj = new DeoMegamenuGroupModel($id_group, $this->context->language->id);
		$base_url_module = 'index.php?controller=AdminModules&configure=deotemplate&token='.Tools::getAdminTokenLite('AdminModules');
		$base_url_widget = 'index.php?controller=AdminDeoWidgetsMegamenu&token='.Tools::getAdminTokenLite('AdminDeoWidgetsMegamenu');
		$base_config_url = AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu');
		$liveedit_action = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=livesave';
		$action_backlink = $base_config_url.'&editgroup=1&id_group='.$id_group;
		$action_widget = $base_url_module.'&liveeditor=1&id_group='.$id_group.'&do=getwidget';
		$action_submenu = $base_config_url.'&load_form_submenu=1&id_group='.$id_group;
		// $action_addwidget = $base_config_url.'&liveeditor=1&do=addwidget';
		
		// $action_loadwidget = $this->context->link->getModuleLink('deotemplate', 'megamenu');
		$action_loadwidget = (Shop::getTotalShops() > 1) ? $base_config_url : $this->context->link->getModuleLink('deotemplate', 'megamenu');
		$ajxgenmenu = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=ajxgenmenu';
		$ajxgensubmenu = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=ajxgensubmenu';
		$ajxsavemenu = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=ajxsavemenu';
		$ajxchangeposition = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=ajxchangeposition';

		// $group_title = $group_obj->title;
		
		// $params = json_decode(DeoMegamenuHelper::base64Decode($group_obj->params), true);
		// $group_type = $params['group_type'];
		// $group_type_sub = $params['type_sub'];
		$id_shop = $this->context->shop->id;
		$shop = Shop::getShop($id_shop);
		// if (!empty($shop)) {
		//     $live_site_url = $shop['uri'];
		// } else {
		//     $live_site_url = __PS_BASE_URI__;
		// }
		// $model = $this->widget;
		// $widgets = $model->getWidgets($id_shop);
		// $type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs', 'tab', 'accordion', 'specialcarousel');
		// foreach ($widgets as $key => $widget) {
		//     if (in_array($widget['type'], $type_menu)) {
		//         unset($widgets[$key]);
		//     }
		// }
		
		$this->context->smarty->assign(array(
			'liveedit_action' => $liveedit_action,
			// 'widgets' => $widgets,
			// 'group_title' => $group_title,
			// 'group_type' => $group_type,
			// 'group_type_sub' => $group_type_sub,
			// 'live_site_url' => $live_site_url,
			'action_backlink' => $action_backlink,
			'ajxgenmenu' => $ajxgenmenu,
			'ajxsavemenu' => $ajxsavemenu,
			'ajxgensubmenu' => $ajxgensubmenu,
			'ajxchangeposition' => $ajxchangeposition,
			'action_submenu' => $action_submenu,
			'action_widget' => $action_widget,
			'action_loadwidget' => $action_loadwidget,
			'id_shop' => $id_shop,
			'link' => $this->context->link,
			'widthList' => DeoSetting::returnWidthList(),
			'base_url_widget' => $base_url_widget,
		));
		
		// return $this->display(__FILE__, 'liveeditor.tpl');
	}

	public function showCategoryListWidgets()
	{
		$tpl = $this->createTemplate('widget.tpl');
		$form = '';
		$widget_selected = '';
		$id = (int)Tools::getValue('id_deomegamenu_widgets');
		$key = (int)Tools::getValue('key');
		if (Tools::getValue('id_deomegamenu_widgets')) {
			$model = new DeoWidgetModel((int)Tools::getValue('id_deomegamenu_widgets'));
		} else {
			$model = new DeoWidgetModel();
		}
		$model->loadEngines();
		$model->id_shop = Context::getContext()->shop->id;

		$types = $model->getTypes();
		if ($key) {
			$widget_data = $model->getWidetByKey($key, Context::getContext()->shop->id);
		} else {
			$widget_data = $model->getWidetById($id, Context::getContext()->shop->id);
		}

		$id = (int)$widget_data['id'];
		$widget_selected = trim(Tools::strtolower(Tools::getValue('wtype')));
		if ($widget_data['type']) {
			$widget_selected = $widget_data['type'];
			// $disabled = true;
		}

		$form = $model->getForm($widget_selected, $widget_data);
		$is_using_managewidget = 1;
		$tpl->assign(array(
			'types' => $types,
			'form' => $form,
			'is_using_managewidget' => $is_using_managewidget,
			'widget_selected' => $widget_selected,
			'table' => $this->table,
			'max_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
			'PS_ALLOW_ACCENTED_CHARS_URL' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'action' => AdminController::$currentIndex.'&add'.$this->table.'&token='.$this->token,
		));
		
		return $tpl->fetch();

	}
	
	public function getSwitchValue($id)
	{
		return array(array('id' => $id.'_on', 'value' => 1, 'label' => $this->l('Yes')),
			array('id' => $id.'_off', 'value' => 0, 'label' => $this->l('No')));
	}
	
	public function getGroupFieldsValues()
	{
		$group = array();
		$field = array('id_deomegamenu_group', 'title', 'title_fo', 'id_shop', 'hook', 'active', 'tab_style');
		foreach ($this->group_data as $key => $value) {
			if (in_array($key, $field)) {
				if ($key == 'id_deomegamenu_group') {
					# module validation
					$group['id_group'] = $value;
				}else if ($key == 'tab_style' || $key == 'active'){
					$group[$key] = $value;
				}else {
					# module validation
					$group[$key.'_group'] = $value;
				}
			}else{
				$group['group['.$key.']'] = $value;
			}
		}
		return $group;
	}
	
	public function renderFormConfig()
	{
		$this->widget->loadEngines();
		$id_lang = $this->context->language->id;
		$id_deomegamenu = (int)Tools::getValue('id_deomegamenu');
		$id_group = (int)Tools::getValue('id_group');
		$obj = new DeoMegamenuModel($id_deomegamenu);
		$obj->setModule($this);
		$categories = DeoMegamenuHelper::getCategories();
		$cms_categories = DeoMegamenuHelper::getCMSCategories();
		$manufacturers = Manufacturer::getManufacturers(false, $id_lang, true);
		$suppliers = Supplier::getSuppliers(false, $id_lang, true);
		$cmss = CMS::listCms($this->context->language->id, false, true);
		$menus = $obj->getDropdown(null, $obj->id_parent, $id_group);
		$this->context->smarty->assign(array(
			'live_editor_url' => AdminController::$currentIndex.'&configure='.$this->name.'&liveeditor=1&id_group='.$id_group.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu'))
		);
		if (isset($id_deomegamenu) && $id_deomegamenu != '') {
			foreach ($menus as $key => $menus_val) {
				if ($menus_val ['id'] == $id_deomegamenu) {
					unset($menus[$key]);
				}
			}
		}
		$page_controller = array();
		foreach (Meta::getPages() as $page) {
			if (Tools::strpos($page, 'module') === false) {
				$array_tmp = array();
				$array_tmp['link'] = $page;
				$array_tmp['name'] = $page;
				array_push($page_controller, $array_tmp);
			}
		}
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
		$desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
						<span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
						<span class="btn-image">
							<a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
							<a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
						</span>
					<span class="help-block">'.$this->l('Use image icon if no use icon Class').'</span>';
		
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => ($id_deomegamenu)?$this->l('Edit Mega Menu Item.'):$this->l('Create New Mega Menu Item.'),
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'label' => $this->l('Megamenu ID'),
					'name' => 'id_deomegamenu',
					'default' => 0,
				),
				array(
					'type' => 'hidden',
					'label' => $this->l('Group ID'),
					'name' => 'id_group',
					'default' => $id_group,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Title'),
					'name' => 'title',
					'required' => true,
					'lang' => true,
					'default' => '',
					'desc' => '<span class="text-danger">'.$this->l('Can not be left blank. If Menu Type is html, It will be replace by html source code').'</span>',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Sub Title'),
					'lang' => true,
					'name' => 'text',
					'cols' => 40,
					'rows' => 10,
					'default' => '',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Parent ID'),
					'name' => 'id_parent',
					'options' => array('query' => $menus,
						'id' => 'id',
						'name' => 'title'),
					'form_group_class' => 'hide',
				),
				
				array(
					'type' => 'switch',
					'label' => $this->l('Active'),
					'name' => 'active',
					'values' => DeoSetting::returnYesNo(),
					'default' => '1',
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Show Text Menu'),
					'name' => 'show_title',
					'values' => DeoSetting::returnYesNo(),
					'default' => '1',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Type submenu'),
					'name' => 'sub_with',
					'options' => array(
						'query' => array(
							array('id' => 'none', 'name' => $this->l('Hide')),
							array('id' => 'submenu', 'name' => $this->l('Submenu')),
							array('id' => 'widget', 'name' => $this->l('Widget')),
						),
						'id' => 'id',
						'name' => 'name'),
					'default' => 'submenu',
					'desc' => $this->context->smarty->fetch($this->createTemplate('button_live_edit_tool.tpl')),
					'form_group_class' => 'hide',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Menu Type'),
					'name' => 'type',
					'id' => 'menu_type',
					'desc' => $this->l('Select a menu link type and fill data for following input'),
					'options' => array('query' => array(
							array('id' => 'url', 'name' => $this->l('Url')),
							array('id' => 'category', 'name' => $this->l('Category')),
							array('id' => 'product', 'name' => $this->l('Product')),
							array('id' => 'manufacture', 'name' => $this->l('Manufacture')),
							array('id' => 'supplier', 'name' => $this->l('Supplier')),
							array('id' => 'cms', 'name' => $this->l('Cms')),
							array('id' => 'cms_category', 'name' => $this->l('Cms Category')),
							array('id' => 'html', 'name' => $this->l('Html')),
							array('id' => 'controller', 'name' => $this->l('Page Controller'))
						),
						'id' => 'id',
						'name' => 'name'),
					'default' => 'url',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Product ID'),
					'name' => 'product_type',
					'id' => 'product_type',
					'class' => 'menu-type-group',
					'default' => '',
				),
				array(
					'type' => 'select',
					'label' => $this->l('CMS Type'),
					'name' => 'cms_type',
					'id' => 'cms_type',
					'options' => array('query' => $cmss,
						'id' => 'id_cms',
						'name' => 'meta_title'),
					'default' => '',
					'class' => 'menu-type-group',
				),
				array(
					'type' => 'text',
					'label' => $this->l('URL'),
					'name' => 'url',
					'id' => 'url_type',
					'required' => true,
					'lang' => true,
					'class' => 'url-type-group-lang',
					'default' => '',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Category Type'),
					'name' => 'category_type',
					'id' => 'category_type',
					'options' => array('query' => $categories,
						'id' => 'id_category',
						'name' => 'name'),
					'default' => '',
					'class' => 'menu-type-group',
				),
				array(
					'type' => 'select',
					'label' => $this->l('CMS Category Type'),
					'name' => 'cms_category_type',
					'id' => 'cms_category_type',
					'options' => array('query' => $cms_categories ,
						'id' => 'id_cms_category',
						'name' => 'name'),
					'default' => '',
					'class' => 'menu-type-group',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Manufacture Type'),
					'name' => 'manufacture_type',
					'id' => 'manufacture_type',
					'options' => array('query' => $manufacturers,
						'id' => 'id_manufacturer',
						'name' => 'name'),
					'default' => '',
					'class' => 'menu-type-group',
					
				),
				array(
					'type' => 'select',
					'label' => $this->l('Supplier Type'),
					'name' => 'supplier_type',
					'id' => 'supplier_type',
					'options' => array('query' => $suppliers,
						'id' => 'id_supplier',
						'name' => 'name'),
					'default' => '',
					'class' => 'menu-type-group',
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('HTML Type'),
					'name' => 'content_text',
					'desc' => $this->l('This menu is only for display content,PLease do not select it for menu level 1'),
					'lang' => true,
					'default' => '',
					'autoload_rte' => true,
					'class' => 'menu-type-group-lang',
				),
				array(
					'type' => 'select',
					'label' => $this->l('List Page Controller'),
					'name' => 'controller_type',
					'id' => 'controller_type',
					'options' => array('query' => $page_controller,
						'id' => 'link',
						'name' => 'name'),
					'default' => '',
					'class' => 'menu-type-group',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Parameter of page controller'),
					'name' => 'controller_type_parameter',
					'id' => 'controller_type_parameter',
					'default' => '',
					'class' => 'menu-type-group',
					'desc' => 'Eg: ?a=1&b=2',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Target Open'),
					'name' => 'target',
					'options' => array('query' => array(
							array('id' => '_self', 'name' => $this->l('Self')),
							array('id' => '_blank', 'name' => $this->l('Blank')),
							array('id' => '_parent', 'name' => $this->l('Parent')),
							array('id' => '_top', 'name' => $this->l('Top'))
						),
						'id' => 'id',
						'name' => 'name'),
					'default' => '_self',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Menu Class'),
					'name' => 'menu_class',
					'display_image' => true,
					'default' => ''
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Menu Icon Font'),
					'name' => 'icon_class',
					'default' => '',
					'desc' => $this->context->smarty->fetch($this->createTemplate('icon_front_guide.tpl')),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Menu Icon Image'),
					'name' => 'image',
					'default' => '',
					'class' => 'hide',
					'desc' => $desc,
					'form_group_class' => 'image-choose',
				),
				// array(
				//     'type' => 'file',
				//     'label' => $this->l('Menu Icon Image'),
				//     'name' => 'image',
				//     'display_image' => true,
				//     'default' => '',
				//     'desc' => $this->l('Use image icon if no use icon Class'),
				//     'thumb' => '',
				//     'title' => $this->l('Icon Preview'),
				// ),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button btn btn-default btn-sm pull-right save'
			)
		);

		foreach ($this->fields_form[0]['form']['input'] as $key => &$item) {
			if (!$id_deomegamenu){
				if ($item['name'] == 'id_deomegamenu'){
					unset($this->fields_form[0]['form']['input'][$key]);
				}
			}
		}

		$helper = new HelperForm();
		$helper->module = $this;
		$helper->show_cancel_button = true;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminDeoMegamenu');
		foreach (Language::getLanguages(false) as $lang) {
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);
		}

		$helper->currentIndex = AdminController::$currentIndex.'&savesubmenu';
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		// $helper->title = $this->displayName;
		$helper->submit_action = 'save';
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues($obj),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		$helper->toolbar_btn = array(
			'back' => array(
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu'),
				'desc' => $this->l('Back to list')
			)
		);

		die(json_encode($helper->generateForm($this->fields_form)));
	}

	public function getConfigFieldsValues($obj)
	{
		$languages = Language::getLanguages(false);
		$fields_values = array();
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
		$this->image_base_url = Tools::htmlentitiesutf8($protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__).'themes/'.$this->theme_name.'/'.DeoHelper::getThemeMediaDir('img').'img/icons/';
		foreach ($this->fields_form as $k => $f) {
			foreach ($f['form']['input'] as $j => $input) {
				if (isset($obj->{trim($input['name'])})) {
					$data = $obj->{trim($input['name'])};

					if ($input['name'] == 'image' && $data) {
						$thumb = $this->image_base_url.$data;
						$this->fields_form[$k]['form']['input'][$j]['thumb'] = $thumb;
					}

					if (isset($input['lang'])) {
						foreach ($languages as $lang) {
							# validate module
							$fields_values[$input['name']][$lang['id_lang']] = isset($data[$lang['id_lang']]) ? $data[$lang['id_lang']] : $input['default'];
						}
					} else {
						# validate module
						$fields_values[$input['name']] = isset($data) ? $data : $input['default'];
					}
				} else {
					if (isset($input['lang'])) {
						foreach ($languages as $lang) {
							$v = Tools::getValue('title', DeoHelper::get($input['name'], $lang['id_lang']));
							$fields_values[$input['name']][$lang['id_lang']] = $v ? $v : $input['default'];
						}
					} else {
						$v = Tools::getValue($input['name'], DeoHelper::get($input['name']));
						$fields_values[$input['name']] = $v ? $v : $input['default'];
					}
					if ($input['name'] == $obj->type.'_type') {
						# validate module
						$fields_values[$input['name']] = $obj->item;
					}
					if ($input['name'] == $obj->type.'_type_parameter') {
						$fields_values[$input['name']] = $obj->item_parameter;
					}
				}
			}
		}

		
		return $fields_values;
	}

	/**
	 * render menu tree using for editing
	 */
	protected function ajxgenmenu()
	{
		$result = array('success' => false);
		if (Tools::getValue('id_group')){
			$parent = '1';
			$params = array('params' => array());
			$get_params_widget = array();
			$list_root_menu = DeoMegamenuModel::getMenusRoot((int)Tools::getValue('id_group'));

			/* reset configuration mega menu */
			if (Tools::getValue('doreset')) {
				DeoMegamenuModel::resetParamsWidget();
			}

			// get array id menu => param widget
			if (count($list_root_menu) > 0) {
				foreach ($list_root_menu as $list_root_menu_item) {
					$menu_obj = new DeoMegamenuModel($list_root_menu_item['id_deomegamenu']);
					$menu_params_widget = $menu_obj->getParamsWidget();
					if ($menu_params_widget != '') {
						$get_params_widget[$list_root_menu_item['id_deomegamenu']] = json_decode(DeoMegamenuHelper::base64Decode($menu_params_widget));
					}
				}
			}

			$params['params'] = $get_params_widget;
			$obj = new DeoMegamenuModel($parent);
			$obj->setModule($this);
			// print_r($params['params']);

			$group_id = Tools::getValue('id_group');
			$group = DeoMegamenuGroupModel::getGroupByID($group_id);
			$group_params = array(
				'id_group' => $group_id,
				'params' => $group['params']
			);

			$tree = $obj->getFrontTree(0, true, $params['params'], $group_params);
			$this->context->smarty->assign(array(
				'tree' => $tree,
			));
			$result['success'] = true;
			$result['data'] = $this->context->smarty->fetch($this->createTemplate('ajxgenmenu.tpl'));
		}

		die(json_encode($result));
	}
	
	/*
	* re-load list widget
	*/
	protected function loadwidget()
	{
		$id_shop = $this->context->shop->id;
		$model = $this->widget;
		$widgets = $model->getWidgets($id_shop);
		$type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs', 'tab', 'accordion', 'specialcarousel');
		foreach ($widgets as $key => $widget) {
			if (in_array($widget['type'], $type_menu)) {
				unset($widgets[$key]);
			}
		}
		
		$return = '';
		$this->context->smarty->assign(array(
			'widgets' => $widgets,
		));
		$return = $this->context->smarty->fetch($this->local_path.'views/templates/admin/list_widget.tpl');
		
		echo $return;
	}

	/**
	 * Ajax Menu : gen sub menu
	 */
	public function ajxgensubmenu()
	{   
		$id = Tools::getValue('id');
		$id_group = Tools::getValue('id_group');
		$subwith = Tools::getValue('subwith');
		$result = array(
			'success' => false,
		);
		$output = '';
		if ($id && $subwith) {
			$menu_obj = new DeoMegamenuModel();
			if ($subwith == 'widget'){
				$attrw = '';
				$menu = $menu_obj->getInfo($id);
				$menu['megaconfig'] = json_decode(DeoMegamenuHelper::base64Decode($menu['params_widget']));
				if (isset($menu['megaconfig']->align) && $menu['megaconfig']->align != 'aligned-fullwidth'){
					$attrw .= ' style="width:'.$menu['megaconfig']->subwidth.'px;"';
				}

				if (isset($menu['megaconfig']->rows)){
					$output .= '<div class="dropdown-widget dropdown-menu" '.$attrw.' ><div class="dropdown-menu-inner">';
					foreach ($menu['megaconfig']->rows as $row) {
						$output .= '<div class="row">';
						foreach ($row->cols as $col) {
							$colclass = (isset($col->colclass) && !empty($col->colclass)) ? ($col->colclass) : '';
							$output .= '<div class="mega-col '.$menu_obj->getColumnWidthConfig($col). '" '.$menu_obj->getColumnDataConfig($col).'> <div class="mega-col-inner '.$colclass.'">';
							// $output .= $obj->renderWidgetsInCol($col);
							$output .= '</div></div>';
						}
						$output .= '</div>';
					}
					$output .= '</div></div>';
					$result['data'] = $output;
					$result['success'] = true;
				}
			}else{
				$level = 1;
				$menu_obj->setModule($this);
				$group = DeoMegamenuGroupModel::getGroupByID($id_group);
				$group_params = array(
					'id_group' => $id_group,
					'params' => $group['params']
				);
				$output .= trim($menu_obj->getFrontTree($id, true, array(), $group_params, null));
				if ($output){
					$result['data'] = $output;
					$result['success'] = true;
				}

				// $menu_obj = new DeoMegamenuModel();
				// $menus = $menu_obj->getChild($id);
				// if (count($menus)){
				//     $output .= $this->gensubmenu($menus,$level);
				//     $result['data'] = $output;
				//     $result['success'] = true;
				// }
			}
		}
		die(json_encode($result));
	}

	// public function gensubmenu($menus,$level)
	// {   
	//     $output = '';
	//     $menu_obj = new DeoMegamenuModel();
	//     $theme_name = Context::getContext()->shop->theme->getName();
	//     $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
	//     $menu_obj->image_base_url = Tools::htmlentitiesutf8($protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__).'themes/'.$theme_name.'/'.DeoHelper::getThemeMediaDir('img').'img/icons/';
	//     $output .= '<div class="dropdown-menu level'.$level.'"><div class="dropdown-menu-inner"><div class="row"><div class="col-sp-12 mega-col" data-colwidth="12" data-type="menu" ><div class="inner"><ul>';
	//     foreach ($menus as $menu) {
	//         $menu_class = '';
	//         $menu_class = (!$menu["active"]) ? $menu_class.' menu-disable' : $menu_class;
	//         if ($menu["sub_with"] == 'widget') {
	//             $menu_class .= ' enable-widget';
	//         }else if($menu["sub_with"] == 'submenu'){
	//             $menu_class .= ' enable-submenu';
	//         }else{
	//             $menu_class .= ' none';
	//         }
	//         if ($menu['type'] == 'html') {
	//             $output .= '<li data-menu-type="'.$menu['type'].'" class="nav-item '.$menu['menu_class'].$menu_class.'" '.$menu_obj->renderAttrs($menu).'><a href="'.$menu_obj->getLink($menu).'" target="'.$menu['target'].'" class="nav-link has-category has-subhtml">';
	//             if ($menu['icon_class']) {
	//                 # validate module
	//                 if ($menu['icon_class'] != strip_tags($menu['icon_class'])) {
	//                     $output .= '<span class="hasicon menu-icon-class">'.$menu['icon_class'];
	//                 } else {
	//                     $output .= '<span class="hasicon menu-icon-class"><i class="'.$menu['icon_class'].'"></i>';
	//                 }
	//                 // $output .= '<span class="hasicon menu-icon-class"><span class="'.$menu['icon_class'].'"></span>';
	//                 // $output .= '<span class="hasicon menu-icon-class"><span class="material-icons">'.$menu['icon_class'].'</span>';
	//             } elseif ($menu['image']) {
	//                 # validate module
	//                 $output .= '<span class="hasicon menu-icon" style="background:url(\''.$menu_obj->image_base_url.$menu['image'].'\') no-repeat;">';
	//             }
	//             if ($menu['show_title'] == 1) {
	//                 // $output .= '<span class="menu-title">'.$menu['title'].'bbbb</span>';
	//                 $output .= '<span class="menu-title">'.$menu['title'].'</span>';
	//             }
	//             if ($menu['text']) {
	//                 $output .= '<span class="sub-title">'.$menu['text'].'</span>';
	//             }
	//             if ($menu['description']) {
	//                 # validate module
	//                 $output .= '<span class="menu-desc">'.$menu['description'].'</span>';
	//             }
	//             if ($menu['image'] || $menu['icon_class']) {
	//                 # validate module
	//                 $output .= '</span>';
	//             }
	//             $output .= '</a>';
	//             if ($menu['content_text']) {
	//                 # validate module
	//                 $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
	//             }
	//             $output .= '</li>';
	//         }else{
	//             if ($menu["sub_with"] != 'widget') {
	//                 $output .= '<li data-menu-type="'.$menu['type'].'" class="nav-item parent dropdown-submenu '.$menu['menu_class'].$menu_class.'" '.$menu_obj->renderAttrs($menu).'>';

	//                 $output .= '<a class="nav-link dropdown-toggle has-category" data-toggle="dropdown" href="'.$menu_obj->getLink($menu).'" target="'.$menu['target'].'">';

	//                 if ($menu['icon_class']) {
	//                     # validate module
	//                     if ($menu['icon_class'] != strip_tags($menu['icon_class'])) {
	//                         $output .= '<span class="hasicon menu-icon-class">'.$menu['icon_class'];
	//                     } else {
	//                         $output .= '<span class="hasicon menu-icon-class"><i class="'.$menu['icon_class'].'"></i>';
	//                     }
	//                 } elseif ($menu['image']) {
	//                     # validate module
	//                     $output .= '<span class="hasicon menu-icon" style="background:url(\''.$menu_obj->image_base_url.$menu['image'].'\') no-repeat;">';
	//                 }
	//                 if ($menu['show_title'] == 1) {
	//                     //$output .= '<span class="menu-title">'.$menu['title'].'aaaa</span>';
	//                     $output .= '<span class="menu-title">'.$menu['title'].'</span>';
	//                 }
	//                 if ($menu['text']) {
	//                     $output .= '<span class="sub-title">'.$menu['text'].'</span>';
	//                 }
	//                 if ($menu['description']) {
	//                     # validate module
	//                     $output .= '<span class="menu-desc">'.$menu['description'].'</span>';
	//                 }
	//                 if ($menu['image'] || $menu['icon_class']) {
	//                     # validate module
	//                     $output .= '</span>';
	//                 }
				   
	//                 if ($menu['is_group'] == 1) {
	//                     $output .= '</a>';
	//                 } else {
	//                     $output .= '<b class="caret"></b></a>';
	//                 }
					
	//                 $menu_obj_children = new DeoMegamenuModel();
	//                 $children = $menu_obj_children->getChild($menu['id_deomegamenu']);
	//                 if (count($children)){
	//                     $output .= $this->gensubmenu($children,$level+1);
	//                 }

	//                 $output .= '</li>';
	//             } 
	//         }
	//     }
	//     $output .= '</ul></div></div></div></div></div>';

	//     return $output;
	// }

	/**
	 * Ajax Menu : Save
	 */
	public function ajxsavemenu()
	{
		if (Tools::getValue('params_widget')) {
			$params_widget = trim(html_entity_decode(Tools::getValue('params_widget')));
			$array_params_widget = json_decode($params_widget, true);

			if (count($array_params_widget) > 0) {
				foreach ($array_params_widget as $key => $value) {
					$menu_obj = new DeoMegamenuModel((int)$key);
					$menu_obj->updateParamsWidget(DeoMegamenuHelper::base64Encode(json_encode($value)));
				}
			}
		}
		if (Tools::getValue('params_subwith')) {
			$params_subwith = trim(html_entity_decode(Tools::getValue('params_subwith')));
			$array_params_subwith = json_decode($params_subwith, true);
			if (count($array_params_subwith) > 0) {
				foreach ($array_params_subwith as $key => $value) {
					$menu_obj = new DeoMegamenuModel((int)$key);
					$menu_obj->updateSubWith($value);
				}
			}
		}

		die($this->l('Save menu success'));
	}


	/**
	 * Ajax change position
	 */
	public function ajxchangeposition()
	{
		$id_nav_1 = Tools::getValue('id_nav_1');
		$id_nav_2 = Tools::getValue('id_nav_2');
		$position_nav_1 = Tools::getValue('position_nav_1');
		$position_nav_2 = Tools::getValue('position_nav_2');
		$result = array(
			'success' => false,
		);

		$nav_1 = new DeoMegamenuModel($id_nav_1);
		$nav_1->position = $position_nav_2;
		$nav_1->save();

		$nav_2 = new DeoMegamenuModel($id_nav_2);
		$nav_2->position = $position_nav_1;
		$nav_2->save();


		$result['success'] = true;
		$result['msg'] = $this->l('Update position success');

		die(json_encode($result));
	}

	public function getWidget()
	{
		if(Tools::getIsset('allWidgets')){
			$dataForm = json_decode( Tools::getValue('dataForm'), 1);
			//print_r($dataForm);die;
			foreach ($dataForm as $key => &$widget) {
				//print_r($this->renderwidget($widget['id_shop'], $widget['id_widget']));die;
				$widget['html'] = $this->renderwidget($widget['id_shop'], $widget['id_widget']);
			}
			die(json_encode($dataForm));
		}
	}

	protected function getCacheId($name = null, $hook = '')
	{
		$cache_array = array(
			$name !== null ? $name : $this->name,
			$hook,
			date('Ymd'),
			(int)Tools::usingSecureMode(),
			(int)$this->context->shop->id,
			(int)Group::getCurrent()->id,
			(int)$this->context->language->id,
			(int)$this->context->currency->id,
			(int)$this->context->country->id,
			(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
		);
		return implode('|', $cache_array);
	}

	/**
	 * render widgets
	 */
	public function renderwidget($id_shop, $widgets)
	{
		if (!$id_shop) {
			$id_shop = Context::getContext()->shop->id;
		}
		// $widgets = Tools::getValue('widgets');

		$widgets = explode('|',$widgets);

		$this->context->smarty->assign(array(
			'link' => $this->context->link,
			// 'PS_CATALOG_MODE' => Configuration::get('PS_CATALOG_MODE'),
			// 'priceDisplay' => Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer),
		));

		if (!empty($widgets)) {
			unset($widgets[0]);
			$model = $this->widget;
			$model->setTheme(Context::getContext()->shop->theme->getName());
			$model->langID = $this->context->language->id;
			$model->loadWidgets($id_shop);
			$model->loadEngines();
			$output = '';
			foreach ($widgets as $wid) {
				$content = $model->renderContent($wid);
				$output .= $this->getWidgetContent($wid, $content['type'], $content['data']);
			}

			return $output;
		}
		// die;
		return '';
	}

	public function getWidgetContent($id, $type, $data, $show_widget_id = 1)
	{

		# validate module
		unset($show_widget_id);
		$data['id_lang'] = $this->context->language->id;


		$id_shop = $this->context->shop->id;
		$model = $this->widget;
		$widgets = $model->getWidgets($id_shop);
		$type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs', 'tab', 'accordion', 'specialcarousel');
		foreach ($widgets as $key => $widget) {
			if (in_array($widget['type'], $type_menu)) {
				unset($widgets[$key]);
			}
		}

		// print_r(Context::getContext()->smarty);
		// die();

		$this->context->smarty->assign($data);
		$this->context->smarty->assign('id_widget', $id);
		$this->context->smarty->assign('widgets',$widgets);

		// $output = $this->display(__FILE__, 'views/templates/hook/megamenu/widgets/widget_'.$type.'.tpl');
		// return $output;
		
		$tpl_dir = DeoHelper::getThemeDir().'modules/deotemplate/views/templates/hook/megamenu/widgets/widget_'.$type.'.tpl';
		if (!file_exists($tpl_dir)) {
			$tpl_dir = _PS_MODULE_DIR_.'deotemplate/views/templates/hook/megamenu/widgets/widget_'.$type.'.tpl';
		}
		$content = Context::getContext()->smarty->fetch($tpl_dir);
		return $content;
	}


	public function checkVersion($version)
	{
		$versions = array(
			'3.0.0'
		);
		if ($version && $version == $versions[count($versions) - 1]) {
			return;
		}
		foreach ($versions as $ver) {
			if (!$version || ($version && $version < $ver)) {
				if ($this->checktable()) {
					$checkcolumn = Db::getInstance()->executeS("
						SELECT * FROM INFORMATION_SCHEMA.COLUMNS
							WHERE TABLE_SCHEMA = '"._DB_NAME_."'
								AND TABLE_NAME='"._DB_PREFIX_."deomegamenu_lang'
								AND COLUMN_NAME ='url'
					");
					if (count($checkcolumn) < 1) {
						Db::getInstance()->execute('
							ALTER TABLE `'._DB_PREFIX_.'deomegamenu_lang`
								ADD `url` varchar(255) DEFAULT NULL');
						$menus = Db::getInstance()->executeS('SELECT `id_deomegamenu`, `id_parent`, `url` FROM `'._DB_PREFIX_.'deomegamenu`');
						if ($menus) {
							foreach ($menus as $menu) {
								if ($menu['id_parent'] != 0) {
									$megamenu = new DeoMegamenuModel((int)$menu['id_deomegamenu']);
									foreach ($megamenu->url as &$url) {
										$url = $menu['url'] ? $menu['url'] : '';
										# validate module
										$validate_module = $url;
										unset($validate_module);
									}
									$megamenu->update();
								}
							}
						}
						Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'deomegamenu` DROP `url`');
						Configuration::updateValue('BTMEGAMENU_VERSION', $ver);
					}
				}
			}
		}
	}

	public function checktable()
	{
		$checktable = Db::getInstance()->executeS("
						SELECT * FROM INFORMATION_SCHEMA.COLUMNS
						WHERE TABLE_SCHEMA = '"._DB_NAME_."'
						AND TABLE_NAME='"._DB_PREFIX_."deomegamenu_lang'
				");
		if (count($checktable) < 1) {
			return false;
		} else {
			return true;
		}
	}

	public function checkFolderIcon()
	{
		if (file_exists($this->img_path) && is_dir($this->img_path)) {
			return;
		}
		if (!file_exists($this->img_path) && !is_dir($this->img_path)) {
			@mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/', 0777, true);
			// update direction css, js, img for 1.7.4.0
			@mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/', 0777, true);
			@mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/', 0777, true);
			// update direction css, js, img for 1.7.4.0
			@mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/', 0777, true);
			
			if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
				@copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/index.php');
			}
			if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
				@copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/index.php');
			}
			@mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/img/', 0777, true);
			@mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/img/', 0777, true);
			if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/img/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
				@copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/img/index.php');
			}
			if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/img/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
				@copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/img/index.php');
			}
			@mkdir($this->img_path, 0777, true);
			if (!file_exists($this->img_path.'index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
				@copy(_PS_IMG_DIR_.'index.php', $this->img_path.'index.php');
			}
		}
	}
	
	public function displayGStatus($id_group, $active)
	{
		# Status Image
		$title = ((int)$active == 0 ? $this->l('Click to Enabled') : $this->l('Click to Disabled'));
		$img = ((int)$active == 0 ? 'disabled.gif' : 'enabled.gif');

		# Status Link
		if ($active == DeoMegamenuGroupModel::GROUP_STATUS_DISABLE) {
			$change_group_status = DeoMegamenuGroupModel::GROUP_STATUS_ENABLE;
		} elseif ($active == DeoMegamenuGroupModel::GROUP_STATUS_ENABLE) {
			$change_group_status = DeoMegamenuGroupModel::GROUP_STATUS_DISABLE;
		}

		$html = '<a href="'.AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu').'&changeGStatus='.$change_group_status.'&id_group='.(int)$id_group.'" title="'.$title.'"><img src="'._PS_ADMIN_IMG_.''.$img.'" alt="" /></a>';
		return $html;
	}
	
	public function postValidation()
	{
		$errors = array();

		if (Tools::isSubmit('submitGroup')) {
			if (Tools::isSubmit('id_group')) {
				if (!Validate::isInt(Tools::getValue('id_group')) && !DeoMegamenuGroupModel::groupExists(Tools::getValue('id_group'))) {
					$errors[] = $this->l('Invalid id_group');
				}
			}
		}

		/* Display errors if needed */
		if (count($errors)) {
			$this->error_text .= implode('<br>', $errors);
			$this->html .= $this->displayError(implode('<br/>', $errors));
			return false;
		}

		/* Returns if validation is ok */
		return true;
	}
	
	
	// function get list group for DeoTemplate
	public function getGroups()
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = Context::getContext()->language->id;
		$sql = 'SELECT *
				FROM '._DB_PREFIX_.'deomegamenu_group gr
				LEFT JOIN '._DB_PREFIX_.'deomegamenu_group_lang grl ON gr.id_deomegamenu_group = grl.id_deomegamenu_group AND grl.id_lang = '.(int)$id_lang.'
				WHERE gr.id_shop = '.(int)$id_shop.' ORDER BY gr.id_deomegamenu_group';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}
	
	
	public function importGroup()
	{
		$this->renderGroupConfig = true;
		$type = Tools::strtolower(Tools::substr(strrchr($_FILES['import_file']['name'], '.'), 1));
		if (isset($_FILES['import_file']) && $type == 'txt' && isset($_FILES['import_file']['tmp_name']) && !empty($_FILES['import_file']['tmp_name'])) {
			$content = Tools::file_get_contents($_FILES['import_file']['tmp_name']);
			$content = json_decode(DeoMegamenuHelper::base64Decode($content), true);
			if (!is_array($content) || !isset($content['id_deomegamenu_group']) || $content['id_deomegamenu_group'] == '') {
				return false;
			}
			$language_field = array('title', 'text', 'url', 'content_text', 'submenu_content_text');
			$languages = Language::getLanguages();
			$shop_id = $this->context->shop->id;
			$lang_list = array();
			foreach ($languages as $lang) {
				# module validation
				$lang_list[$lang['iso_code']] = $lang['id_lang'];
			}

			$override_group = Tools::getValue('override_group');
			$override_widget = Tools::getValue('override_widget');
			if ($override_group && DeoMegamenuGroupModel::groupExists($content['id_deomegamenu_group'], $shop_id)) {
				$mod_group = new DeoMegamenuGroupModel($content['id_deomegamenu_group']);
				//edit group
				$mod_group = DeoMegamenuGroupModel::setDataForGroup($mod_group, $content, true);
				if (!$mod_group->update()) {
					# module validation
					return false;
				}
				$this->removeAllMenuOfGroup($content['id_deomegamenu_group']);

				if (count($content['list_menu']) > 0) {
					$list_new_id = array();
					foreach ($content['list_menu'] as $menu) {
						$mod_menu = new DeoMegamenuModel();
						foreach ($menu as $key => $val) {
							if (in_array($key, $language_field)) {
								foreach ($val as $key_lang => $val_lang) {
									# module validation
									$mod_menu->{$key}[$lang_list[$key_lang]] = $val_lang;
								}
							} else {
								# module validation
								if ($key == 'id_group') {
									$mod_menu->{$key} = $mod_group->id;
								} elseif ($key == 'id_parent') {
									if ($val != 0) {
										$mod_menu->{$key} = $list_new_id[$val];
									} else {
										$mod_menu->{$key} = $val;
									}
								} else {
									$mod_menu->{$key} = $val;
								}
							}
						}
						
						$mod_menu->id = 0;
						if (!$mod_menu->add()) {
							return false;
						}
						$list_new_id[$menu['id_deomegamenu']] = $mod_menu->id;
					}
				}
			} else {
				$mod_group = new DeoMegamenuGroupModel();
				$mod_group = DeoMegamenuGroupModel::setDataForGroup($mod_group, $content, false);
				if (!$mod_group->add()) {
					# module validation
					return false;
				}
				
				if (count($content['list_menu']) > 0) {
					$list_new_id = array();
					foreach ($content['list_menu'] as $menu) {
						$mod_menu = new DeoMegamenuModel();
						foreach ($menu as $key => $val) {
							if (in_array($key, $language_field)) {
								foreach ($val as $key_lang => $val_lang) {
									# module validation
									$mod_menu->{$key}[$lang_list[$key_lang]] = $val_lang;
								}
							} else {
								# module validation
								if ($key == 'id_group') {
									$mod_menu->{$key} = $mod_group->id;
								} elseif ($key == 'id_parent') {
									if ($val != 0) {
										$mod_menu->{$key} = $list_new_id[$val];
									} else {
										$mod_menu->{$key} = $val;
									}
								} else {
									$mod_menu->{$key} = $val;
								}
							}
						}
						
						$mod_menu->id = 0;
						if (!$mod_menu->add()) {
							return false;
						}
						$list_new_id[$menu['id_deomegamenu']] = $mod_menu->id;
					}
				}
			}
			// import widget
			if (count($content['list_widget']) > 0) {
				if (!$this->processImportWidgets($content['list_widget'], $override_widget, $shop_id)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}
	
	
	// remove all menu of group when delete group or when import override
	public function removeAllMenuOfGroup($id_group)
	{
		$res = true;
		
		$list_menu = DeoMegamenuGroupModel::getMenuParentByGroup($id_group);
		if (count($list_menu) > 0) {
			foreach ($list_menu as $key => $list_menu_item) {
				$mod_menu = new DeoMegamenuModel($list_menu_item['id_deomegamenu']);
				$res = $mod_menu->delete();
			}
			// validate module
			unset($key);
		}
		
		return $res;
	}
	
	private function saveMenu()
	{
		$result = array('success' => false);
		if (!$id_group = Tools::getValue('id_group')) {
			$result['msg'] = $this->l('An error occurred while attempting to save.');
		} else {
			if ($id_deomegamenu = Tools::getValue('id_deomegamenu')) {
				# validate module
				$megamenu = new DeoMegamenuModel((int)$id_deomegamenu);
			} else {
				# validate module
				$megamenu = new DeoMegamenuModel();
			}

			$keys = DeoMegamenuHelper::getConfigKey(false);
			$post = DeoMegamenuHelper::getPost($keys, false);
			$keys = DeoMegamenuHelper::getConfigKey(true);
			$post += DeoMegamenuHelper::getPost($keys, true);

			$this->copyFromPost($megamenu, $this->table);
			// $megamenu->copyFromPost($post);

			$megamenu->id_shop = $this->context->shop->id;

			$megamenu->id_group = $id_group;
			if ($megamenu->type && $megamenu->type != 'html' && Tools::getValue($megamenu->type.'_type')) {
				# validate module
				$megamenu->item = Tools::getValue($megamenu->type.'_type');
				$megamenu->item_parameter = Tools::getValue($megamenu->type.'_type_parameter');
			}
			$url_default = '';
			foreach ($megamenu->url as $menu_url) {
				if ($menu_url) {
					$url_default = $menu_url;
					break;
				}
			}
			if ($url_default) {
				foreach ($megamenu->url as &$menu_url) {
					if (!$menu_url) {
						$menu_url = $url_default;
					}
				}
			}
			if ($megamenu->validateFields(false) && $megamenu->validateFieldsLang(false)) {
				if (!Tools::getValue('id_deomegamenu')) {
					# Auto set position when create menu
					$megamenu->position = DeoMegamenuModel::getLastPosition((int)$megamenu->id_parent);
				}

				$megamenu->save();
				$megamenu->cleanPositions($megamenu->id_parent);
				$lang_id = $this->context->language->id;
				$megamenu->content_text = $megamenu->content_text[$lang_id];
				// $megamenu->description = $megamenu->description[$lang_id];
				$megamenu->text = $megamenu->text[$lang_id];
				$megamenu->title = $megamenu->title[$lang_id];
				$megamenu->url = $megamenu->url[$lang_id];
				$megamenu->image = ($megamenu->image) ? DeoHelper::getImgThemeUrl().$megamenu->image : '#';
				$result['data'] = $megamenu;
			} else {
				# validate module
				$errors = array();
				$errors[] = $this->l('An error occurred while attempting to save.');
				$errors[] = $this->l('Do not let the requirement fields (*) are empty.');
				if (!Validate::isUnsignedInt(Tools::getValue('colums'))) {
					$errors[] = $this->l('"Colums" is invalid. Must an integer validity (unsigned).');
				}
			}
			if (isset($errors) && count($errors)) {
				$result['msg'] = implode('<br/>', $errors);
			} else {
				$result['success'] = true;
				if (Tools::getValue('id_deomegamenu')) {
					$result['msg']=  $this->l('Update Successful.');
				}else{
					$result['msg']=  $this->l('Added Menu Successful.');
				}
			}

			die(json_encode($result));
		}
	}

	/**
	 * Override function copyFromPost from AdminController
	 * Copy data values from $_POST to object.
	 */
	protected function copyFromPost(&$object, $table)
	{
		parent::copyFromPost($object, $table);
		if ((int) DeoHelper::getConfig('DEBUG_MODE')){
			$id_lang_default = Configuration::get('PS_LANG_DEFAULT');
			$class_vars = get_class_vars(get_class($object));
			$fields = array();
			if (isset($class_vars['definition']['fields'])) {
				$fields = $class_vars['definition']['fields'];
			}

			foreach ($fields as $field => $params) {
				if (array_key_exists('lang', $params) && $params['lang']) {
					foreach (Language::getIDs(false) as $id_lang) {
						if (Tools::isSubmit($field . '_' . (int) $id_lang)) {
							$object->{$field}[(int) $id_lang] = (Tools::getValue($field . '_' . (int) $id_lang) == '' && Tools::getValue($field . '_' . (int) $id_lang_default) != '') ? Tools::getValue($field . '_' . (int) $id_lang_default) : Tools::getValue($field . '_' . (int) $id_lang);
						}
					}
				}
			}
		}
	}
	
	private function deleteMenu()
	{
		$result = array('success' => false);
		if (!$id_group = Tools::getValue('id_group')) {
			$result['msg'] =$this->l('An error occurred while attempting to delete.');
		} else {
			$obj = new DeoMegamenuModel((int)Tools::getValue('id_deomegamenu'));
			$obj->delete();
			$result['success'] = true;
			$result['msg'] = $this->l('Delete Successful.');
		}

		die(json_encode($result));
	}
	
	private function delete_many_menu()
	{
		if (!$id_group = Tools::getValue('id_group')) {
			$this->html .= $this->displayError($this->l('An error occurred while attempting to delete.'));
		} else {
			$list = array_filter(explode( ',', trim(Tools::getValue('list'), ',')));
			if(is_array($list) && $list)
			{
				foreach ($list as $key => $id) {
					$obj = new DeoMegamenuModel((int)$id);
					if($obj->id)
					{
						$obj->delete();
					}
				}
			}
			Tools::redirectAdmin(AdminController::$currentIndex.'&editgroup=1&id_group='.$id_group.'&successful=1&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
	}
	
	private function duplicateMenu()
	{
		if (!Tools::getValue('id_deomegamenu') || !$id_group = Tools::getValue('id_group')) {
			$this->html .= $this->displayError($this->l('An error occurred while attempting to duplicate.'));
		} else {
			$mod_menu = new DeoMegamenuModel((int)Tools::getValue('id_deomegamenu'));
			$mod_new_menu = new DeoMegamenuModel();

			$defined = $mod_new_menu->getDefinition('DeoMegamenu');
			foreach ($defined['fields'] as $ke => $val) {
				# module validation
				unset($val);

				if ($ke == 'id') {
					continue;
				}

				if ($ke == 'title') {
					$tmp = array();
					foreach ($mod_menu->title as $kt => $vt) {
						$tmp[$kt] = $this->l('Duplicate of').' '.$vt;
					}

					$mod_new_menu->{$ke} = $tmp;
				} elseif ($ke == 'position') {
					$mod_new_menu->{$ke} = DeoMegamenuModel::getLastPosition((int)$mod_menu->id_parent);
				} else {
					$mod_new_menu->{$ke} = $mod_menu->{$ke};
				}
			}
			$errors = array();
			if (!$mod_new_menu->add()) {
				$errors[] =  $this->l('The menu could not be duplicate.');
			}

			if (isset($errors) && count($errors)) {
				$this->html .= $this->displayError(implode('<br />', $errors));
			} else {
				Tools::redirectAdmin(AdminController::$currentIndex.'&editgroup=1&id_group='.$id_group.'&successful=1&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
			}
		}
	}
	
	private function saveGroup()
	{
		$this->renderGroupConfig = true;
		
		$langs = Language::getLanguages(false);
		$errors = array();
		
		# ACTION - add,edit for GROUP
		/* Sets ID if needed */
		if (Tools::getValue('id_group')) {
			$mod_group = new DeoMegamenuGroupModel((int)Tools::getValue('id_group'));
			if (!Validate::isLoadedObject($mod_group)) {
				$this->displayWarning($this->l('Invalid id_group'));
				return;
			}
		} else {
			$mod_group = new DeoMegamenuGroupModel();
		}

		/* Sets position */
		foreach ($langs as $lang) {
			$mod_group->title[$lang['id_lang']] = Tools::getValue('title_group_'.$lang['id_lang']);
			$mod_group->title_fo[$lang['id_lang']] = Tools::getValue('title_fo_group_'.$lang['id_lang']);
		}
		/* Sets active */
		$mod_group->active = (int)Tools::getValue('active');

		/* Sets tab_style */
		$mod_group->tab_style = (int)Tools::getValue('tab_style');

		$context = Context::getContext();
		$mod_group->id_shop = $context->shop->id;
		$mod_group->hook = Tools::getValue('hook_group');
		if (!Tools::getValue('id_group')) {
			$mod_group->position = $mod_group->getLastPosition($context->shop->id);
			$mod_group->randkey = DeoMegamenuHelper::genKey();
		}
		$params = Tools::getValue('group');
		$mod_group->params = DeoMegamenuHelper::base64Encode(json_encode($params));


		if ($mod_group->validateFields(false) && $mod_group->validateFieldsLang(false)) {
			# Add new
			if (!Tools::getValue('id_group')) {
				if (!$mod_group->add()) {
					$this->displayWarning($this->l('The group could not be created.'));
				} else {
					if (Tools::isSubmit('submitSaveAndStay')){
						Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu').'&configuregroup=1&liveeditor=1&editgroup=1&id_group='.$mod_group->id);
					}else{
						Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu'));
					}
				}
			} 
			# Update
			else {
				if (!$mod_group->update()) {
					$this->displayWarning($this->l('The group could not be updated.'));
				} else {
					if (Tools::isSubmit('submitSaveAndStay')){
						Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu').'&configuregroup=1&liveeditor=1&editgroup=1&id_group='.$mod_group->id);
					}else{
						Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu'));
					}
				}
			}
		} else {
		   $this->displayWarning($this->l('An error occurred while attempting to save. Do not let the requirement fields (*) are empty'));
		}
	}
	
	private function deleteGroup()
	{
		$this->renderGroupConfig = true;
		$mod_group = new DeoMegamenuGroupModel((int)Tools::getValue('id_group'));
		# Delete slider of group
		$res = $mod_group->delete();

		if (!$res) {
			$this->displayWarning($this->l('Could not delete'));
		} else {
			$res = $this->removeAllMenuOfGroup(Tools::getValue('id_group'));

			if (!$res) {
				$this->displayWarning($this->l('Could not delete'));
			} else {
				$this->displayInformation($this->l('Group deleted'));
				Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu').'&success=delete');
			}
		}
	}
	
	private function duplicateGroup()
	{
		$this->renderGroupConfig = true;
		$langs = Language::getLanguages(false);
		if (!$id_group = Tools::getValue('id_group')) {
			$this->displayWarning($this->l('An error occurred while attempting to duplicate.'));
		} else {
			$mod_group = new DeoMegamenuGroupModel((int)Tools::getValue('id_group'));
			$mod_new_group = new DeoMegamenuGroupModel();
			$defined = $mod_new_group->getDefinition('DeoMegamenuGroupModel');
			foreach ($defined['fields'] as $ke => $val) {
				# module validation
				unset($val);

				if ($ke == 'id') {
					continue;
				}

				if ($ke == 'title') {
					foreach ($langs as $lang) {
						$mod_new_group->{$ke}[$lang['id_lang']] = $this->l('Duplicate of').' '.$mod_group->{$ke}[$lang['id_lang']];
					}
				} elseif ($ke == 'position') {
					$mod_new_group->{$ke} = DeoMegamenuGroupModel::getLastPosition(Context::getContext()->shop->id);
				} elseif ($ke == 'randkey') {
					$mod_new_group->{$ke} = DeoMegamenuHelper::genKey();
				} else {
					$mod_new_group->{$ke} = $mod_group->{$ke};
				}
			}
			$list_menu = DeoMegamenuGroupModel::getMenuByGroup($id_group);
			if (!$mod_new_group->add()) {
				$this->displayWarning($this->l('The group could not be duplicate.'));
			} else {
				// copy menu of old group to new group
				$list_menu = DeoMegamenuGroupModel::getMenuByGroup($id_group);

				$res = true;
				if (count($list_menu) > 0) {
					$list_new_id = array();
					foreach ($list_menu as $list_menu_item) {
						$mod_menu = new DeoMegamenuModel($list_menu_item['id_deomegamenu']);
						$mod_new_menu = new DeoMegamenuModel();

						$defined = $mod_new_menu->getDefinition('DeoMegamenuModel');
						foreach ($defined['fields'] as $ke => $val) {
							# module validation
							unset($val);

							if ($ke == 'id') {
								continue;
							}

							if ($ke == 'id_group') {
								$mod_new_menu->{$ke} = $mod_new_group->id;
							} elseif ($ke == 'id_parent') {
								if ($mod_menu->{$ke} != 0) {
									$mod_new_menu->{$ke} = $list_new_id[$mod_menu->{$ke}];
								} else {
									$mod_new_menu->{$ke} = $mod_menu->{$ke};
								}
							} else {
								$mod_new_menu->{$ke} = $mod_menu->{$ke};
							}
						}
						if (!$mod_new_menu->add()) {
							$res = false;
						} else {
							$list_new_id[$list_menu_item['id_deomegamenu']] = $mod_new_menu->id;
						}
					}
				}
				if ($res) {
					// update widget with new menu id
					$this->displayInformation($this->l('Group duplicated'));
					Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu').'&success=duplicategroup');
				} else {
					$this->displayWarning($this->l('The group could not be duplicate.'));
				}
			}
		}

	}
	
	private function exportGroup()
	{
		$this->renderGroupConfig = true;
		// export group process
		if (Tools::getValue('exportgroup')) {
			$languages = Language::getLanguages();
			$group = DeoMegamenuGroupModel::getGroupByID(Tools::getValue('id_group'));
			$obj_group = new DeoMegamenuGroupModel(Tools::getValue('id_group'));
			foreach ($languages as $lang) {
				# module validation
				$group['title'][$lang['iso_code']] = $obj_group->title[$lang['id_lang']];
				$group['title_fo'][$lang['iso_code']] = $obj_group->title_fo[$lang['id_lang']];
			}
			// add list menu of group
			// $menus = $this->getMenusByGroup(Tools::getValue('id_group'));
			$id_group = (int)Tools::getValue('id_group');
			$menus = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT btm.*, btml.*
					FROM '._DB_PREFIX_.'deomegamenu btm
					LEFT JOIN '._DB_PREFIX_.'deomegamenu_lang btml ON (btm.id_deomegamenu = btml.id_deomegamenu)
					WHERE btm.id_group = '.(int)$id_group.'
					ORDER BY btm.id_parent ASC');
			
			$language_field = array('title', 'text', 'url', 'content_text', 'submenu_content_text');
		   
			$lang_list = array();
			foreach ($languages as $lang) {
				# module validation
				$lang_list[$lang['id_lang']] = $lang['iso_code'];
			}
			
			$list_widgets = '';
			
			foreach ($menus as $menus_item) {
				if (Tools::getValue('widgets')) {
					if ($menus_item['params_widget'] != '') {
						$list_widgets .= DeoMegamenuHelper::base64Decode($menus_item['params_widget']);
					}
				} else {
					$menus_item['params_widget'] = '';
				}
				foreach ($menus_item as $key => $value) {
					if ($key == 'id_lang') {
						$curent_lang = $lang_list[$value];
						continue;
					}
					if (in_array($key, $language_field)) {
						$group['list_menu'][$menus_item['id_deomegamenu']][$key][$curent_lang] = $value;
					} else {
						# module validation
						$group['list_menu'][$menus_item['id_deomegamenu']][$key] = $value;
					}
				}
			}
			
			if (Tools::getValue('widgets')) {
				$extra_file_name = 'with_widgets';
			} else {
				// $group['params_widget'] = '';
				$extra_file_name = 'without_widgets';
			}

			// override remove case check with/without widgets
			$extra_file_name = time();

			// add list menu of group
			$group['list_widget'] = array();
			if ($list_widgets != '' && Tools::getValue('widgets')) {
				// $group_widget = DeoMegamenuHelper::base64Decode($group['params_widget']);
				$model = new DeoWidgetModel();
				$widget_shop = $model->getWidgets();
				if (count($widget_shop) > 0) {
					foreach ($widget_shop as $widget_shop_item) {
						if (Tools::strpos($list_widgets, 'wid-'.$widget_shop_item['key_widget']) !== false) {
							$params_widget = DeoMegamenuHelper::base64Decode($widget_shop_item['params']);
							foreach ($languages as $lang) {
								# module validation
								if (Tools::strpos($params_widget, '_'.$lang['id_lang'].'"') !== false) {
									$params_widget = str_replace('_'.$lang['id_lang'].'"', '_'.$lang['iso_code'].'"', $params_widget);
								}
							}
							$widget_shop_item['params'] = DeoMegamenuHelper::base64Encode($params_widget);
							$group['list_widget'][] = $widget_shop_item;
						}
					}
				}
			}
			header('Content-Type: plain/text');
			// Tools::redirect(false, false, null, 'Content-Type: plain/text');
			header('Content-Disposition: Attachment; filename=export_megamenu_group_'.Tools::getValue('id_group').'_'.$extra_file_name.'_'.time().'.txt');
			// Tools::redirect(false, false, null, 'Content-Disposition: Attachment; filename=export_megamenu_group_'.Tools::getValue('id_group').'_'.$extra_file_name.'_'.time().'.txt');
			header('Pragma: no-cache');
			// Tools::redirect(false, false, null, 'Pragma: no-cache');
			die(DeoMegamenuHelper::base64Encode(json_encode($group)));
		}
	}

	
	private function changeStatusGroup()
	{
		$this->renderGroupConfig = true;
		if (Tools::isSubmit('changeGStatus') && Tools::isSubmit('id_group')) {
			# ACTION - Change status for GROUP : enable or disable a group
			$mod_group = new DeoMegamenuGroupModel((int)Tools::getValue('id_group'));
			$change_status = Tools::getValue('changeGStatus');
			$mod_group->update_flag = false;

			if ($change_status == DeoMegamenuGroupModel::GROUP_STATUS_DISABLE && $mod_group->active != $change_status) {
				$mod_group->active = DeoMegamenuGroupModel::GROUP_STATUS_DISABLE;
				$mod_group->update_flag = true;
			} elseif ($change_status == DeoMegamenuGroupModel::GROUP_STATUS_ENABLE && $mod_group->active != $change_status) {
				$mod_group->active = DeoMegamenuGroupModel::GROUP_STATUS_ENABLE;
				$mod_group->update_flag = true;
			}
			if (true == $mod_group->update_flag) {
				$res = $mod_group->update();
				// $this->html .= ($res ? $this->displayInformation($this->l('Change status of group was successful')) : $this->displayError($this->l('The configuration could not be updated.')));
				if ($res){
					Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoMegamenu'));
				}
			}
		}
	}

	private function changePositionMenu()
	{
		$list = Tools::getValue('list');
		$root = 0;
		$child = array();
		foreach ($list as $id => $parent_id) {
			if ($parent_id <= 0) {
				# validate module
				$parent_id = $root;
			}
			$child[$parent_id][] = $id;
		}
		$res = true;
		foreach ($child as $id_parent => $menus) {
			$i = 0;
			foreach ($menus as $id_deomegamenu) {
				$sql = 'UPDATE `'._DB_PREFIX_.'deomegamenu` SET `position` = '.(int)$i.', id_parent = '.(int)$id_parent.'
						WHERE `id_deomegamenu` = '.(int)$id_deomegamenu;
				$res &= Db::getInstance()->execute($sql);
				$i++;
			}
		}
		$array = array('hasError' => false, 'errors' => $this->l('Update Positions Done'));
		die(json_encode($array));
	}
	
	private function changePositionGroup()
	{
		$list_group = Tools::getValue('list_group');
		$i = 0;
		foreach ($list_group as $id_deomegamenu_group => $val) {
			# validate module
			unset($val);
			$sql = 'UPDATE `'._DB_PREFIX_.'deomegamenu_group` SET `position` = '.(int)$i.'
					WHERE `id_deomegamenu_group` = '.(int)$id_deomegamenu_group;
			Db::getInstance()->execute($sql);
			$i++;
		}
		$array = array('hasError' => false, 'errors' => $this->l('Update Group Positions Done'));
		die(json_encode($array));
	}
	
	private function displaySuccessMessage()
	{
		if (count($this->errors) > 0) {
			return;
		}
		
		if (Tools::getValue('success')) {
			switch (Tools::getValue('success')) {
				case 'add':
					$this->html = $this->displayInformation($this->l('Group added'));
					break;
				case 'update':
					$this->html = $this->displayInformation($this->l('Group updated'));
					break;
				case 'delete':
					$this->html = $this->displayInformation($this->l('Group deleted'));
					break;
				case 'duplicategroup':
					$this->html = $this->displayInformation($this->l('Duplicate group is successful'));
					break;
				case 'importgroup':
					$this->html = $this->displayInformation($this->l('Import group is successful'));
					break;
				case 'importwidgets':
					$this->html = $this->displayInformation($this->l('Import widgets is successful'));
					break;
			}
		}
	}
}
