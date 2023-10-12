<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */



class AdminDeoTranslateController extends ModuleAdminController
{
	public $name = 'deotemplate';
	protected $fields_form = array();
	private $_html = '';
	
	public function __construct()
	{
		parent::__construct();
		$this->theme_dir = DeoHelper::getThemeDir();
		$this->bootstrap = true;
		$this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
		$this->themeName = DeoHelper::getThemeName();
		$this->context = Context::getContext();
		$this->module_path = __PS_BASE_URI__.'modules/'.$this->name.'/';
		$this->tpl_path = _PS_ROOT_DIR_.'/modules/'.$this->name.'/views/templates/admin';
		$this->module_path = __PS_BASE_URI__.'modules/deotemplate/';
		// $this->module_path_resource = $this->module_path.'views/';
		$this->themePath = _PS_ALL_THEMES_DIR_.$this->themeName.'/';
	}

	public function initToolbar()
	{
		parent::initToolbar();
		
		# SAVE AND STAY
		if (Tools::isSubmit('setting_translate')) {
			$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
			$this->context->controller->addJS(DeoHelper::getJsAdminDir().'admin/translate.js');
			$this->page_header_toolbar_btn['SaveAll'] = array(
				'href' => 'javascript:void(0);',
				'desc' => $this->l('Save All'),
				'icon' => 'process-icon-save',
			);
		}
	}

	public function postProcess()
	{
		if (count($this->errors) > 0) {
			return;
		}
		
		if (Tools::isSubmit('submit_goto_translate_'.$this->name)) {
			$iso_code = Tools::getValue('iso_code');
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminDeoTranslate', false).'&token='.Tools::getValue('token').'&iso_code='.$iso_code.'&setting_translate');
		}else if (Tools::isSubmit('submit-tranlsate')) {
			$data = Tools::getValue('data');
			$domain = Tools::getValue('domain');
			$locale = Tools::getValue('locale');
			$iso_code = Tools::getValue('iso_code');
			$id_lang = Language::getIdByIso($iso_code);

			$folder_translate = $this->theme_dir.'translations/'.$locale.'/';
			$file_uri = $folder_translate.$domain.'.'.$locale.'.xlf';
			if (!file_exists($file_uri)){
				$this->errors[] = Tools::displayError('File translate does not exist');
				return false;
			}
			$dom = new DOMDocument();
			$dom->load($file_uri);
			$root = $dom->documentElement;
			$files = $root->getElementsByTagName('file');
			foreach ($files as $file) {
				$trans_units = $file->getElementsByTagName('trans-unit');
				foreach ($trans_units as $trans_unit) {
					$id = $trans_unit->getAttribute('id');
					$source = $trans_unit->getElementsByTagName('source')->item(0)->nodeValue;
					$target = $trans_unit->getElementsByTagName('target')->item(0)->nodeValue;

					foreach ($data as $key => $unit) {
						if ($unit['id'] == $id){
							$trans_unit->getElementsByTagName('target')->item(0)->nodeValue = $unit['target'];

							if (isset($unit['id_translation'])){
								$query = '
								UPDATE '._DB_PREFIX_.'translation 
								SET translation = "'.$unit['target'].'"  
								WHERE id_translation = '.$unit['id_translation'].'
								 ';
								Db::getInstance()->execute($query);
							}else{
								// $domain_exception = ['ShopTheme'];
								// if (!in_array($domain, $domain_exception)){
								// 	$query = '
								// 	INSERT INTO '._DB_PREFIX_.'translation (`id_lang`, `key`, `translation`, `domain`, `theme`)
								// 	VALUES ("'.$id_lang.'","'.$source.'","'.$unit['target'].'","'.$domain.'","'.$this->themeName.'") 
								// 	 ';
								// 	Db::getInstance()->execute($query);
								// }
							}

							unset($data[$key]);
						}
					}
				}
			}

			$dom->saveXML();
			$dom->save($file_uri);

			$array_result['success'] = true;
			$array_result['msg'] = 'Translate success!';
			die(json_encode($array_result));

		}else{
			parent::postProcess(true);
		}
	}

	public function renderList()
	{	
		$content = '';
		if (Tools::getValue('iso_code')) {
			$content = $this->renderListTranslate();
		}else{
			$content = $this->renderListLanguage();
		}

		return $content;
	}

	public function renderListTranslate()
	{	
		$iso_code = Tools::getValue('iso_code');
		$id_lang = Language::getIdByIso($iso_code);
		$detail_language = Language::getLangDetails($iso_code);
		$locale = $detail_language['locale'];
		$folder_translate = $this->theme_dir.'translations/'.$locale.'/';
		if (!is_dir($folder_translate)){
			$this->errors[] = Tools::displayError('Folder translate does not exist');
			
			return false;
		}

		$languages = array();
		foreach (Language::getLanguages(true) as $lang) {
			$languages[] = array('value' => $lang['iso_code'],'text' => $lang['name']);
		}

		$fieldsForm = $translate_domain = $translate_file = array();

		$query = '
		SELECT * FROM  '._DB_PREFIX_.'translation 
		WHERE id_lang = '.(int)$id_lang.'
		 ';
		$data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		foreach ($data as $field) {
			$translate_domain[$field['domain']][$field['key']] = $field;
		}

		
		$source_files = Tools::scandir($folder_translate, 'xlf');
		foreach ($source_files as $filename) {
			$pieces = explode(".", $filename);
			$domain = $pieces[0];
			if ($domain == $locale){
				continue;
			}
			$files_content = simplexml_load_file($folder_translate.$filename);
			foreach ($files_content as $file) {
				// print_r($file);
				foreach ($file as $body) {
					// print_r($body);
					foreach ($body as $trans_unit) {
						// print_r($trans_unit);
						$id = (string) $trans_unit->attributes()->id;
						$source = (string) $trans_unit->source;
						$target = (string) $trans_unit->target;
						$translate_file[$domain][] = array(
							'id' => $id,
							'source' => $source,
							'target' => (isset($translate_domain[$domain][$source])) ? $translate_domain[$domain][$source]['translation'] : $target,
							'id_translation' => (isset($translate_domain[$domain][$source])) ? $translate_domain[$domain][$source]['id_translation'] : 0,
						);
					}
				}
			}
		}

		// print_r($translate_file);
		// die();



		$template = $this->createTemplate('panel.tpl');

		$template->assign(array(
			'translate_file' => $translate_file,
			'translate_domain' => $translate_domain,
			'action_form' => $this->context->link->getAdminLink('AdminDeoTranslate', false).'&token='.Tools::getValue('token').'&iso_code='.$iso_code.'&locale='.$locale.'&setting_translate',
		));

		return $template->fetch();
	}

	public function renderListLanguage()
	{
		$helper = new HelperForm();

		$helper->module = $this;
		$helper->name_controller = 'AdminDeoTranslate';
		$helper->token = Tools::getAdminTokenLite('AdminDeoTranslate');
		$helper->show_toolbar = false;
		$helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
		$helper->submit_action = 'submit_goto_translate_'.$this->name;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminDeoTranslate', false);
		$helper->fields_value = array('iso_code' => '');

		$languages = array();
		foreach (Language::getLanguages(true) as $lang) {
			$languages[] = array('value' => $lang['iso_code'],'text' => $lang['name']);
		}

		$fieldsForm = array();
		$fieldsForm[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Modify translations'),
			),
			'input' => array(
				array(
					'type'       => 'select',
					'label'   => $this->l('Language'),
					'name'       => 'iso_code',
					'class' => 'fixed-width-xxl',
					'required' => true,
					'options' => array(
						'query' => $languages,
						'id'       => 'value',
						'name'       => 'text' ),
					'default' => 'en',
				),
			),
			'submit' => array(
				'title' => $this->l('Setting'),
				'icon' => 'process-icon-configure',
				'class' => 'btn btn-default pull-right'
			),
		);

		return $helper->generateForm($fieldsForm);
	}
}
