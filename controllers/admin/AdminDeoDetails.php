<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateDetailsModel.php');

class AdminDeoDetailsController extends ModuleAdminControllerCore
{
	private $theme_name = '';
	public $module_name = 'deotemplate';
	public $tpl_save = '';
	public $file_content = array();
	public $explicit_select;
	public $order_by;
	public $order_way;
	public $profile_css_folder;
	public $module_path;
	// public $module_path_resource;
	public $str_search = array();
	public $str_relace = array();
	public $theme_dir;

	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'deotemplate_details';
		$this->className = 'DeoTemplateDetailsModel';
		$this->lang = false;
		$this->explicit_select = true;
		$this->allow_export = true;
		$this->can_import = true;
		$this->context = Context::getContext();
		$this->_join = '
			INNER JOIN `'._DB_PREFIX_.'deotemplate_details_shop` ps ON (ps.`id_deotemplate_details` = a.`id_deotemplate_details`)';
		$this->_select .= ' ps.active as active, ';

		$this->order_by = 'id_deotemplate_details';
		$this->order_way = 'DESC';
		parent::__construct();
		$this->fields_list = array(
			'id_deotemplate_details' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 50,
				'class' => 'fixed-width-xs'
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 140,
				'type' => 'text',
				'filter_key' => 'a!name'
			),
			'plist_key' => array(
				'title' => $this->l('Product List Key'),
				'filter_key' => 'a!plist_key',
				'type' => 'text',
				'width' => 140,
			),
			'class_detail' => array(
				'title' => $this->l('Class'),
				'width' => 140,
				'type' => 'text',
				'filter_key' => 'a!class_detail',
				'orderby' => false
			),
			'active' => array(
				'title' => $this->l('Is Default'),
				'active' => 'status',
				'filter_key' => 'ps!active',
				'align' => 'text-center',
				'type' => 'bool',
				'class' => 'fixed-width-sm',
				'orderby' => false
			),
		);
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);
		$this->theme_dir = DeoHelper::getThemeDir();

		$this->_where = ' AND ps.id_shop='.(int)$this->context->shop->id;
		$this->theme_name = Context::getContext()->shop->theme_name;
		$this->profile_css_folder = $this->theme_dir.'modules/'.$this->module_name.'/';
		$this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
		// $this->module_path_resource = $this->module_path.'views/';
		$this->str_search = array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APOCBRACKET_', '_APCCBRACKET_',);
		$this->str_relace = array('&', '\"', '\'', '\t', '\r', '\n', '[', ']', '{', '}');
	}
	
	public function initToolbar()
	{
		parent::initToolbar();

		# SAVE AND STAY
		if($this->display == 'add' || $this->display == 'edit'){
			$this->page_header_toolbar_btn['SaveAndStay'] = array(
				'href' => 'javascript:void(0);',
				'desc' => $this->l('Save and stay'),
				'js' => 'TopSaveAndStay()',
				'icon' => 'process-icon-save',
			);
			Media::addJsDef(array('TopSaveAndStay_Name' => 'submitAdd'.$this->table.'AndStay'));
			
			$this->page_header_toolbar_btn['Save'] = array(
				'href' => 'javascript:void(0);',
				'desc' => $this->l('Save'),
				'js' => 'TopSave()',
				'icon' => 'process-icon-save',
			);
			Media::addJsDef(array('TopSave_Name' => 'submitAdd'.$this->table));
		}
		
		# SHOW LINK EXPORT ALL FOR TOOLBAR
		switch ($this->display) {
			default:
				$this->toolbar_btn['generate'] = array(
					'href' => self::$currentIndex . '&generateall&token=' . $this->token,
					'desc' => $this->l('Regenerate'),
					'class' => 'btn_add_new icon-save',
				);
				$this->toolbar_btn['new'] = array(
					'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
					'desc' => $this->l('Add new'),
					'class' => 'btn_add_new',
				);
				if (!$this->display && $this->can_import) {
					$this->toolbar_btn['import'] = array(
						'href' => self::$currentIndex . '&import' . $this->table . '&token=' . $this->token,
						'desc' => $this->trans('Import', array(), 'Admin.Actions'),
						'class' => 'btn_xml_import',
					);
				}
				if ($this->allow_export) {
					$this->toolbar_btn['export'] = array(
						'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
						'desc' => $this->l('Export'),
						'class' => 'btn_xml_export',
					);
					Media::addJsDef(array('record_id' => 'deotemplate_detailsBox[]'));
				}
		}
	}
	
	/**
	 * OVERRIDE CORE
	 */
	public function processExport($text_delimiter = '"')
	{
		$record_id = Tools::getValue('record_id');
		$file_name = 'product_detail_all.xml';
		# VALIDATE MODULE
		unset($text_delimiter);
		
		if($record_id){
			$record_id_str = implode(", ", $record_id);
			$this->_where = ' AND a.id_deotemplate_details IN ( '.pSQL($record_id_str).' )';
			$file_name = 'product_detail.xml';
		}        
		
		$this->getList($this->context->language->id, null, null, 0, false);
		if (!count($this->_list)) {
			return;
		}

		$data = $this->_list;
		$this->file_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$this->file_content .= '<data>' . "\n";
		$this->file_content .= '<product_details>' . "\n";
		if ($data) {
			foreach ($data as $product_detail) {
				$this->file_content .= '<record>' . "\n";
				foreach ($product_detail as $key => $value) {
					$this->file_content .= '    <'.$key.'>';
					$this->file_content .= '<![CDATA['.$value.']]>';
					$this->file_content .= '</'.$key.'>' . "\n";
				}
				$this->file_content .= '</record>' . "\n";
			}
		}
		$this->file_content .= '</product_details>' . "\n";
		$this->file_content .= '</data>' . "\n";
		header('Content-type: text/xml');
		// Tools::redirect(false, false, null, 'Content-type: text/xml');
		header('Content-Disposition: attachment; filename="'.$file_name.'"');
		// Tools::redirect(false, false, null, 'Content-Disposition: attachment; filename="'.$file_name.'"');

		echo $this->file_content;
		die();
	}

	public function processImport()
	{
		$upload_file = new Uploader('importFile');
		$upload_file->setAcceptTypes(array('xml'));
		$file = $upload_file->process();
		$file = $file[0];
		if( !isset($file['save_path']))
		{
			$this->errors[]        = $this->trans('Failed to import.', array(), 'Admin.Notifications.Error');
			return;
		}
		$files_content = simplexml_load_file($file['save_path']);
		$override = Tools::getValue('override');
		
		if (isset($files_content->product_details) && $files_content->product_details)
		{
			foreach ($files_content->product_details->children() as $product_details) {
				if ($override) {
					
				}else{
					$num = DeoSetting::getRandomNumber();
					$obj_model = new DeoTemplateDetailsModel();
					$obj_model->plist_key = 'detail'.$num;
					$obj_model->name = $product_details->name->__toString();
					$obj_model->class_detail = $product_details->class_detail->__toString();
					$obj_model->params = $product_details->params->__toString();
					$obj_model->active = 0;
					$obj_model->url_img_preview = $product_details->url_img_preview->__toString();
					if ($obj_model->save()) {
						$this->saveTplFile($obj_model->plist_key, $obj_model->params);
					}
				}
			}
			$this->confirmations[] = $this->trans('Successful importing.', array(), 'Admin.Notifications.Success');
		}else{
			$this->errors[]        = $this->trans('Failed to import.', array(), 'Admin.Notifications.Error');
		}
	}
	
	public function renderView()
	{
		$object = $this->loadObject();
		if ($object->page == 'product_detail') {
			$this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoTemplateProductDetail');
		} else {
			$this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoHome');
		}
		$this->redirect_after .= '&id_deotemplate_details='.$object->id;
		$this->redirect();
	}

	public function postProcess()
	{
		parent::postProcess();
		if (count($this->errors) > 0) {
			return;
		}

		if (Tools::getIsset('duplicatedeotemplate_details')) {
			$id = Tools::getValue('id_deotemplate_details');
			$model = new DeoTemplateDetailsModel($id);
			$duplicate_object = $model->duplicateObject();
			$duplicate_object->name = $this->l('Duplicate of').' '.$duplicate_object->name;
			$old_key = $duplicate_object->plist_key;
			$duplicate_object->plist_key = 'detail'.DeoSetting::getRandomNumber();
			$duplicate_object->params = $model->params;     # FIX 1751 : empty 
			$duplicate_object->update();
			if ($duplicate_object->addShop()) {
				//duplicate shortCode
				if ($file_content = Tools::file_get_contents(DeoHelper::getConfigDir('theme_details').$old_key.'.tpl')){
					DeoSetting::writeFile(DeoHelper::getConfigDir('theme_details'), $duplicate_object->plist_key.'.tpl', $file_content);
				}

				// duplicate css
				if ($file_content = Tools::file_get_contents(DeoHelper::getCssDir().'details/'.$old_key.'.css')){
	                DeoSetting::writeFile(DeoHelper::getCssDir().'details/', $duplicate_object->plist_key.'.css', $file_content);
	            }

                // duplicate js
                if ($file_content = Tools::file_get_contents(DeoHelper::getJsDir().'details/'.$old_key.'.js')){
                	DeoSetting::writeFile(DeoHelper::getJsDir().'details/', $duplicate_object->plist_key.'.js', $file_content);
                }

				$this->redirect_after = self::$currentIndex.'&token='.$this->token;
				$this->redirect();
			} else {
				Tools::displayError('Can not duplicate Profiles');
			}
		}
		if (Tools::isSubmit('saveELement')) {
			parent::validateRules();
			if (count($this->errors)) {
				$this->display = 'edit';
				return false;
			}

			$filecontent = Tools::getValue('filecontent');
			$fileName = Tools::getValue('fileName');
			DeoHelper::createDir(DeoHelper::getConfigDir('theme_details'));
			if ($filecontent){
				DeoSetting::writeFile(DeoHelper::getConfigDir('theme_details'), $fileName.'.tpl', $filecontent);
			}
		}

		if (Tools::isSubmit('generateall')) {
			$query = new DbQuery();
			$query->from('deotemplate_details', 'detailprofile');
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
			foreach ($result as $value) {
				$data_form = str_replace($this->str_search, $this->str_relace, $value['params']);
				$data_form = json_decode($value['params'], true);
				$this->saveTplFile($value['plist_key'], $value['params'], $value['class_detail'], $data_form['class']);
			}
			Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token'));
		}
	}

	public function convertObjectToTpl($object_form)
	{
		$tpl = '';

		foreach ($object_form as $object) {
			if (isset($object['form']['active']) && $object['form']['active'] == 0){
				continue;
			}

			if ($object['name'] == 'group') {
				$tpl .= (isset($object['form']['container']) && $object['form']['container']) ? '{if isset($page.body_classes["layout-full-width"]) && $page.body_classes["layout-full-width"]}<div class="container">{/if}' : '';
				$tpl .= '<div class="'.$object['form']['class'].'">';
				$tpl .= $this->convertObjectToTpl($object['columns']);
				$tpl .= '</div>';
				$tpl .= (isset($object['form']['container']) && $object['form']['container']) ? '{if isset($page.body_classes["layout-full-width"]) && $page.body_classes["layout-full-width"]}</div>{/if}' : '';
			} else if ($object['name'] == 'column') {
				$tpl .= '<div class="'.$this->convertToColumnClass($object['form']).'">';
				$tpl .= $this->convertObjectToTpl($object['sub']);
				$tpl .= '</div>';
			} else if ($object['name'] == 'box') {
				$tpl .= '<div class="'.$object['form']['css'].'">';
				$tpl .= $this->convertObjectToTpl($object['element']);
				$tpl .= '</div>';
			} else if ($object['name'] == 'code') {
				$tpl .= $object['code'];
			} else {
				// if (!isset($this->file_content[$object['name']])) {
					$this->returnFileContent($object['name']);
					// add config to type gallery
					if ($object['name'] == "product_cover_thumbnails") {
						$strdata = '';

						if (isset($object['form']['responsive']) && !$object['form']['responsive']){
							$object['form']['breakpoints'] = 'false';
						}
						unset($object['form']['responsive']);

						if (isset($object['form']['zoom']) && $object['form']['zoom'] != 'out'){
							unset($object['form']['position'], $object['form']['zoomWidth'], $object['form']['zoomHeight']);
						}
						$this->file_content[$object['name']] = str_replace('home_default', $object['form']['size'], $this->file_content[$object['name']]); 
						// die($this->file_content[$object['name']]);
						
						if (isset($object['form']['lazyload']) && $object['form']['lazyload'] && DeoHelper::getLazyload()){

						}else{
							$this->file_content[$object['name']] = str_replace('lazyload=true', 'lazyload=false', $this->file_content[$object['name']]); 
						}

						$col_product_image_show_all = array();
						if ($object['form']['type'] == "gallery"){
							$this->file_content[$object['name']] = str_replace('gallery=false', 'gallery=true', $this->file_content[$object['name']]); 
							if (isset($object['form']['column_xxl'])){
								$col_product_image_show_all[] = 'col-xxl-'.$this->formatToColumnClass($object['form']['column_xxl']);
							} 
							if (isset($object['form']['column_xl'])){
								$col_product_image_show_all[] = 'col-xl-'.$this->formatToColumnClass($object['form']['column_xl']);
							} 
							if (isset($object['form']['column_lg'])){
								$col_product_image_show_all[] = 'col-lg-'.$this->formatToColumnClass($object['form']['column_lg']);
							} 
							if (isset($object['form']['column_md'])){
								$col_product_image_show_all[] = 'col-md-'.$this->formatToColumnClass($object['form']['column_md']);
							} 
							if (isset($object['form']['column_sm'])){
								$col_product_image_show_all[] = 'col-sm-'.$this->formatToColumnClass($object['form']['column_sm']);
							} 
							if (isset($object['form']['column_xs'])){
								$col_product_image_show_all[] = 'col-xs-'.$this->formatToColumnClass($object['form']['column_xs']);
							} 
							if (isset($object['form']['column_sp'])){
								$col_product_image_show_all[] = 'col-sp-'.$this->formatToColumnClass($object['form']['column_sp']);
							} 

							$this->file_content[$object['name']] = 
							preg_replace('/col_thumbnail="(.*?)"/', 'col_thumbnail="'.implode(" ", $col_product_image_show_all).'"', $this->file_content[$object['name']]);
							// str_replace('deo-col-thumbnail', implode(" ", $col_product_image_show_all), $this->file_content[$object['name']]);

							$strdata .= ' data-col="'.implode(" ", $col_product_image_show_all).'"';
						}

						$col_product_image_with_thumb = array();
						if ($object['form']['type'] == "thumbnail"){
							$array_item_custom = json_decode($object['form']['breakpoints'], true);
							foreach ($array_item_custom as $array_item_custom_val) {
								$size_window = $array_item_custom_val[0];
								$number_item = $array_item_custom_val[1];
								if ($size_window <= 480) {
									$col_product_image_with_thumb[] = 'loading-sp-'.$number_item;
								}else if ($size_window <= 576) {
									$col_product_image_with_thumb[] = 'loading-xs-'.$number_item;
								}else if ($size_window <= 768) {
									$col_product_image_with_thumb[] = 'loading-sm-'.$number_item;
								}else if ($size_window <= 992) {
									$col_product_image_with_thumb[] = 'loading-md-'.$number_item;
								}else if ($size_window <= 1200) {
									$col_product_image_with_thumb[] = 'loading-lg-'.$number_item;
								}else if ($size_window <= 1500) {
									$col_product_image_with_thumb[] = 'loading-xl-'.$number_item;
								}else if ($size_window > 1500) {
									$col_product_image_with_thumb[] = 'loading-xxl-'.$number_item;
								}
							};

							$this->file_content[$object['name']] = 
							preg_replace('/col_thumbnail="(.*?)"/', 'col_thumbnail="'.(($object['form']['thumb'] == "left" || $object['form']['thumb'] == "right") ? "loading-vertical " : ""). implode(" ", $col_product_image_with_thumb).'"', $this->file_content[$object['name']]);
							// str_replace('deo-col-thumbnail', (($object['form']['thumb'] == "left" || $object['form']['thumb'] == "right") ? "loading-vertical " : ""). implode(" ", $col_product_image_with_thumb), $this->file_content[$object['name']]);

							$strdata .= ' data-col-loading="'.(($object['form']['thumb'] == "left" || $object['form']['thumb'] == "right") ? "loading-vertical " : "").implode(" ", $col_product_image_with_thumb).'"';
						}

						foreach ($object['form'] as $key => $value) {
							$strdata .= ' data-'.$key.'="'.$value.'"';
						}

						$this->file_content[$object['name']] = str_replace('id="content">', 'id="content"'.$strdata.'>', $this->file_content[$object['name']]);
					}else if ($object['name'] == "product_more_info") {
						$this->file_content[$object['name']] = preg_replace('/type="(.*?)"/', 'type="'.$object['form']['type'].'"', $this->file_content[$object['name']]);
					}	
				// }
				// add class
				$tpl .= $this->file_content[$object['name']];
			}
		}
		return $tpl;
	}

	public function formatToColumnClass($col){
		if ($col == 5) 
			return '2-4';
		else
			return 12/$col;
	}

	public function convertToColumnClass($form)
	{
		$class = '';
		foreach ($form as $key => $val) {
			// check class name of column
			if ($key == 'active' || $key == 'form_id') continue;
			
			if ($key == 'class') {
				if ($val != '') {
					$class .= ($class == '') ? $val : ' '.$val;
				}
			} else {
				$class .= ($class == '') ? 'col-'.$key.'-'.$val : ' col-'.$key.'-'.$val;
			}
		}
		return $class;
	}

	public function returnFileContent($pelement)
	{
		$tpl_dir = DeoHelper::getConfigDir('theme_details').$pelement.'.tpl';
		if (!file_exists($tpl_dir)) {
			$tpl_dir = DeoHelper::getConfigDir('module_details').$pelement.'.tpl';
		}
		$this->file_content[$pelement] = Tools::file_get_contents($tpl_dir);
		return $this->file_content[$pelement];
	}

	public function renderList()
	{
		if (Tools::getIsset('pelement')) {
			$helper = new HelperForm();
			$helper->submit_action = 'saveELement';
			$inputs = array(
				array(
					'type' => 'textarea',
					'name' => 'filecontent',
					'label' => $this->l('File Content'),
					'desc' => $this->l('Please carefully when edit tpl file'),
				),
				array(
					'type' => 'hidden',
					'name' => 'fileName',
				)
			);
			$fields_form = array(
				'form' => array(
					'legend' => array(
						'title' => sprintf($this->l('You are Editing file: %s'), Tools::getValue('pelement').'.tpl'),
						'icon' => 'icon-cogs'
					),
					'action' => Context::getContext()->link->getAdminLink('AdminDeoShortcodes'),
					'input' => $inputs,
					'name' => 'importData',
					'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button btn btn-default pull-right'
					),
					'tinymce' => false,
				),
			);
			$helper->tpl_vars = array(
				'fields_value' => $this->getFileContent()
			);
			return $helper->generateForm(array($fields_form));
		}
		$this->initToolbar();
		$this->addRowAction('edit');
		$this->addRowAction('duplicate');
		$this->addRowAction('delete');
		return $this->ImportForm() . parent::renderList();
	}

	public function getFileContent()
	{
		$pelement = Tools::getValue('pelement');
		$tpl_dir = DeoHelper::getConfigDir('theme_details').$pelement.'.tpl';
		if (!file_exists($tpl_dir)) {
			$tpl_dir = DeoHelper::getConfigDir('module_details').$pelement.'.tpl';
		}
		return array('fileName' => $pelement, 'filecontent' => Tools::file_get_contents($tpl_dir));
	}

	public function setHelperDisplay(Helper $helper)
	{
		parent::setHelperDisplay($helper);
		$this->helper->module = DeoTemplate::getInstance();
	}

	public function returnObjElemnt($params, $element_by_name)
	{
		$result = array();

		foreach ($params as $key => $value) {
			$value['dataForm'] = (isset($value['form'])) ? json_encode($value['form']) : array();

			if (isset($element_by_name[$value['name']])) {
				$value['config'] = $element_by_name[$value['name']];
			}

			if ($value['name'] == 'group'){
				$value['columns'] = $this->returnObjElemnt($value['columns'], $element_by_name);
			}

			if ($value['name'] == 'column'){
				$value['sub'] = $this->returnObjElemnt($value['sub'], $element_by_name);
			}

			if ($value['name'] == 'box'){
				$value['element'] = $this->returnObjElemnt($value['element'], $element_by_name);
			}

			$result[$key] = $value;
		}

		return $result;
	}

	public function renderForm()
	{
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
			
			if ($obj = parent::processUpdate()) {
	            if ($this->object->data_shop['id_shop'] != Context::getContext()->shop->id){
	                $this->errors[] = $this->l('This ID is not exist in this store!');
	                return false;
	            }
	        }
		}


		$this->initToolbar();
		$this->context->controller->addJqueryUI('ui.sortable');
		$this->context->controller->addJqueryUI('ui.draggable');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
		//$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/form.js');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/detail.js');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/form.css');
		$this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');

		$product_more_info = DeoSetting::getProductMoreInfo();
		$thumbnail_position = DeoSetting::getThumbnailPosition();
		Media::addJsDef(array('deo_product_more_info' => $product_more_info));
		Media::addJsDef(array('deo_thumbnail_position' => $thumbnail_position));
		Media::addJsDef(array('deo_message_box' => $this->l('You can not drag group to group!')));
		Media::addJsDef(array('deo_message_delete' => $this->l('Do you want to delete it?')));

		$source_file = Tools::scandir(DeoHelper::getConfigDir('theme_details'), 'tpl');
		if (is_dir(DeoHelper::getConfigDir('theme_details'))) {
			$source_template_file = Tools::scandir(DeoHelper::getConfigDir('theme_details'), 'tpl');
			$source_file = array_merge($source_file, $source_template_file);
		}

		$this->object->params = str_replace($this->str_search, $this->str_relace, $this->object->params);

		$config_dir = DeoHelper::getConfigDir('theme_details') . 'config.json';
		if (!file_exists($config_dir)) {
			$config_dir = DeoHelper::getConfigDir('module_details') . 'config.json';
		}
		$elements = json_decode(Tools::file_get_contents($config_dir), true);

		$element_by_name = array();
		foreach ($elements as $k1 => $groups) {
			foreach ($groups['group'] as $k2 => $group) {
				$elements[$k1]['group'][$k2]['dataForm'] = (isset($group['data-form'])) ? json_encode($group['data-form']) : '';
				if (isset($group['data-form'])){
					$group['dataForm'] = json_encode($group['data-form']);
				}
				if (isset($group['file'])) {
					$element_by_name[$group['file']] = $group;
				}
			}
		}
		
		$params = array();
		$params['objectForm'] = array();
		if (isset($this->object->params) && $this->object->params) {            
			$params = json_decode($this->object->params, true);
			if ($params['objectForm']) {
				$params['objectForm'] = $this->returnObjElemnt($params['objectForm'], $element_by_name);
			}
		}

		$imageType = ImageType::getImagesTypes('products');
		$dataDefaultGroup = array('container' => '1','class' => '','active'=>'1');
		$dataDefaultColumn = array('class' => '','active'=>'1');

		// print_r($params);
		
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Layout Product Page Manager'),
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name'),
					'name' => 'name',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Key'),
					'name' => 'plist_key',
					'readonly' => 'readonly',
				),
				array(
					'label' => $this->l('Class'),
					'type' => 'text',
					'name' => 'class_detail',
					'width' => 140
				),
				array(
					'label' => $this->l('Url Image Preview'),
					'type' => 'text',
					'name' => 'url_img_preview',
					'desc' => $this->l('Only for preview'),
					'width' => 140
				),
				array(
					'type' => 'product_page_builder',
					'name' => 'product_page_builder',
					'params' => $params,
					'elements' => $elements,
					'imageType' => $imageType,
					'deo_debug_mode' => (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true) ? true : false,
					'dataDefaultGroup' => json_encode($dataDefaultGroup),
					'dataDefaultColumn' => json_encode($dataDefaultColumn),
					'product_more_info' => $product_more_info,
					'thumbnail_position' => $thumbnail_position,
					'demodetaillink' => 'index.php?controller=AdminDeoDetails'.'&token='.Tools::getAdminTokenLite('AdminDeoDetails').'&adddeotemplate_details',
					'element_by_name' => $element_by_name,
					'widthList' => DeoSetting::returnWidthList(),
					'columnGrids' => DeoSetting::getColumnGrid(),
				),
				array(
					'type' => 'hidden',
					'name' => 'params'
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			),
			'buttons' => array(
				'save-and-stay' => array(
					'title' => $this->l('Save and Stay'),
					'name' => 'submitAdd'.$this->table.'AndStay',
					'type' => 'submit',
					'class' => 'btn btn-default pull-right',
					'icon' => 'process-icon-save')
			)
		);

		return parent::renderForm();
	}
	
	public function ImportForm()
	{
		$helper = new HelperForm();
		$helper->submit_action = 'import' . $this->table;
		$inputs = array(
			array(
				'type' => 'file',
				'name' => 'importFile',
				'label' => $this->l('File'),
				'desc' => $this->l('Only accept xml file'),
			),
		);
		$fields_form = array(
			'form' => array(
				'action' => Context::getContext()->link->getAdminLink('AdminDeoDetailsController'),
				'input' => $inputs,
				'submit' => array('title' => $this->l('Import'), 'class' => 'button btn btn-success'),
				'tinymce' => false,
			),
		);
		$helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
		$helper->identifier = $this->identifier;
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'xml_import';
		$html = $helper->generateForm(array($fields_form));

		return $html;
	}

	public function replaceSpecialStringToHtml($arr)
	{
		foreach ($arr as &$v) {
			if ($v['name'] == 'code') {
				// validate module
				$v['code'] = str_replace($this->str_search, $this->str_relace, $v['code']);
			} else {
				if ($v['name'] == 'group') {
					foreach ($v as &$f) {
						if ($f['name'] == 'code') {
							// validate module
							$f['code'] = str_replace($this->str_search, $this->str_relace, $f['code']);
						}
					}
				}
			}
		}
		return $arr;
	}

	public function getFieldsValue($obj)
	{
		$file_value = parent::getFieldsValue($obj);
		if (!$obj->id) {
			$num = DeoSetting::getRandomNumber();
			$file_value['plist_key'] = 'detail'.$num;
			$file_value['name'] = $file_value['plist_key'];
			// $file_value['class_detail'] = 'detail-'.$num;
		}
		return $file_value;
	}

	public function processAdd()
	{
		if ($obj = parent::processAdd()) {
			$this->saveTplFile($obj->plist_key, $obj->params);
		}
	}

	public function processUpdate()
	{
		if ($obj = parent::processUpdate()) {
			$this->saveTplFile($obj->plist_key, $obj->params);
		}
	}

	public function processDelete()
	{
		$object = $this->loadObject();
		Tools::deleteFile(DeoHelper::getConfigDir('theme_details').$object->plist_key.'.tpl');
		parent::processDelete();
	}

	//save file
	public function saveTplFile($plist_key, $params = '', $class_detail = null, $main_class = null)
	{
		$data_form = str_replace($this->str_search, $this->str_relace, $params);
		$data_form = json_decode($data_form, true);

		if (!isset($data_form['objectForm'])){
			return;
		}
		// $class_detail = (isset($class_detail)) ? $class_detail : Tools::getValue('class_detail', '');
		// $main_class = (isset($main_class)) ? $main_class : Tools::getValue('main_class', '');
		// $class = array($class_detail, $main_class);

		$objectForm = $data_form['objectForm'];
		$tpl_grid = $this->returnFileContent('header_product');
		// $tpl_grid = str_replace('class="product-detail', 'class="product-detail '.implode(" ",$class), $tpl_grid);
		$tpl_grid .= $this->convertObjectToTpl($objectForm);
		$tpl_grid .= $this->returnFileContent('footer_product');
		
		$tpl_grid = preg_replace('/\{\*[\s\S]*?\*\}/', '', $tpl_grid);
		$folder = DeoHelper::getConfigDir('theme_details');
		if (!is_dir($folder)) {
			@mkdir($folder, 0755, true);
		}
		$file = $plist_key.'.tpl';
		//$tpl_grid = preg_replace('/\{\*[\s\S]*?\*\}/', '', $tpl_grid);
		//$tpl_grid = str_replace(" mod='deotemplate'", '', $tpl_grid);

		if ($file_content = DeoHelper::getLicenceTPL().$tpl_grid){
			DeoSetting::writeFile($folder, $file, $file_content);
		}
	}

	public function processStatus()
	{
		if (Validate::isLoadedObject($object = $this->loadObject())) {
			if ($object->toggleStatus()) {
				$matches = array();
				if (preg_match('/[\?|&]controller=([^&]*)/', (string)$_SERVER['HTTP_REFERER'], $matches) !== false && Tools::strtolower($matches[1]) != Tools::strtolower(preg_replace('/controller/i', '', get_class($this)))) {
					$this->redirect_after = preg_replace('/[\?|&]conf=([^&]*)/i', '', (string)$_SERVER['HTTP_REFERER']);
				} else {
					$this->redirect_after = self::$currentIndex.'&token='.$this->token;
				}
			}
		} else {
			$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
					.'<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
		}
		return $object;
	}
	
	/**
	 * SHOW LINK DUPLICATE FOR EACH ROW
	 */
	public function displayDuplicateLink($token = null, $id = null, $name = null)
	{
		$controller = 'AdminDeoDetails';
		$token = Tools::getAdminTokenLite($controller);
		$html = '<a href="#" title="Duplicate" onclick="confirm_link(\'\', \'Duplicate Product Details ID '.$id.'. If you wish to proceed, click &quot;Yes&quot;. If not, click &quot;No&quot;.\', \'Yes\', \'No\', \'index.php?controller='.$controller.'&id_deotemplate_details='.$id.'&duplicatedeotemplate_details&token='.$token.'\', \'#\')">
			<i class="icon-copy"></i> Duplicate
		</a>';
		
		// validate module
		unset($name);
		
		return $html;
	}
	
	/**
	 * PERMISSION ACCOUNT demo@demo.com
	 * OVERRIDE CORE
	 */
	public function access($action, $disable = false)
	{
		if (Tools::getIsset('update'.$this->table) && Tools::getIsset($this->identifier)) {
			// Allow person see "EDIT" form
			$action = 'view';
		}
		return parent::access($action, $disable);
	}
	
	/**
	 * PERMISSION ACCOUNT demo@demo.com
	 * OVERRIDE CORE
	 */
	public function initProcess()
	{
		parent::initProcess();
		# SET ACTION : IMPORT DATA
		if ($this->can_import && Tools::getIsset('import' . $this->table)) {
			if ($this->access('edit')) {
				$this->action = 'import';
			}
		}
		
		if (count($this->errors) <= 0) {
			if( Tools::isSubmit('duplicate'.$this->table) ) {
				if ($this->id_object) {
					if (!$this->access('add'))
					{
						$this->errors[] = $this->trans('You do not have permission to duplicate this.', array(), 'Admin.Notifications.Error');
					}
				}
			}elseif(Tools::getIsset('saveELement') && Tools::getValue('saveELement')){
				if (!$this->access('edit'))
				{
					$this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
				}
			}elseif($this->can_import && Tools::getIsset('import' . $this->table)){
				if (!$this->access('edit')) {
					$this->errors[] = $this->trans('You do not have permission to import data.', array(), 'Admin.Notifications.Error');
				}
			}
		}
	}
}
