<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */



include_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperOnepagecheckout.php');

class AdminDeoOnepagecheckoutConfigureController extends ModuleAdminController
{
	public $name = 'deotemplate';
	public $config = null;
	public $submitSaveSetting = false;

	public function __construct()
	{
		parent::__construct();
		$this->bootstrap = true;
		$this->context = Context::getContext();

		$this->local_path = _PS_MODULE_DIR_.'modules/'.$this->name.'/';
		$this->_path = __PS_BASE_URI__.'modules/'.$this->name.'/';

		$this->helper_opc = new HelperOnepagecheckout();

		// // Check DB required fields (Customer and Address objects)
		// $tmpCustomer    = new Customer();
		// $requiredFields = $tmpCustomer->getFieldsRequiredDatabase();
		// foreach ($requiredFields as $field) {
		// 	return "[required fields error] " . $field['object_name'] . ':' . $field['field_name'];
		// }
		// if (class_exists('CustomerAddress')) {
		// 	$tmpAddress     = new CustomerAddress();
		// 	$requiredFields = $tmpAddress->getFieldsRequiredDatabase();
		// 	foreach ($requiredFields as $field) {
		// 		return "[required fields error] " . $field['object_name'] . ':' . $field['field_name'];
		// 	}
		// }
		// // Legacy Address object
		// if (class_exists('Address')) {
		// 	$tmpAddress     = new Address();
		// 	$requiredFields = $tmpAddress->getFieldsRequiredDatabase();
		// 	foreach ($requiredFields as $field) {
		// 		return "[required fields error (legacy Address object)] " . $field['object_name'] . ':' . $field['field_name'];
		// 	}
		// }
	}

	public function initContent()
	{
		if (!$this->viewAccess()) {
			$this->errors[] = $this->l('You do not have permission to view this.');
			return;
		}

		$this->initPageHeaderToolbar();
		
		$this->content .= $this->renderForm();

		$this->content .= $this->renderKpis();
		$this->content .= $this->renderList();
		$this->content .= $this->renderOptions();

		// if we have to display the required fields form
		if ($this->required_database) {
			$this->content .= $this->displayRequiredFields();
		}

		$this->context->smarty->assign(array(
			'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
			'debug_mode' => (bool)_PS_MODE_DEV_,
			'content' => $this->content,
			'lite_display' => $this->lite_display,
			'url_post' => self::$currentIndex.'&token='.$this->token,
			'show_page_header_toolbar' => $this->show_page_header_toolbar,
			'page_header_toolbar_title' => $this->page_header_toolbar_title,
			'title' => $this->page_header_toolbar_title,
			'toolbar_btn' => $this->page_header_toolbar_btn,
			'page_header_toolbar_btn' => $this->page_header_toolbar_btn
		));
	}

	public function initPageHeaderToolbar()
	{
		$this->page_header_toolbar_title = $this->l('Deo One Page Checkout');
		$this->page_header_toolbar_btn = array();

		$this->page_header_toolbar_btn['Save'] = array(
            'href' => 'javascript:void(0);',
            'desc' => $this->l('Save'),
            'js' => 'TopSave()',
            'icon' => 'process-icon-save',
        );
        Media::addJsDef(array('TopSave_Name' => 'submitAddconfiguration'));

		return parent::initPageHeaderToolbar();
	}
	
	public function postProcess()
	{   
		if (count($this->errors) > 0) {
			return;
		}

		if ("1" == Tools::getIsset('reset-old-config')) {
			$this->resetConfigBlocksLayout();
			$this->resetConfigAccountFields();
			$this->resetConfigInvoiceFields();
			$this->resetConfigDeliveryFields();
		}

		if (((bool)Tools::getIsset('ajax_request')) == true) {
			$this->ajaxCall();
			die();
		}

		
		if (Tools::isSubmit('submitAddconfiguration')) {
			$this->submitSaveSetting = true; 
		}
	}

	public function setMedia($isNewTheme = false)
	{
		parent::setMedia($isNewTheme);
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/general.js');
		$this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');

		$this->context->controller->addCSS(DeoHelper::getCssAdminDir().'onepagecheckout/admin/back.css');
		$this->context->controller->addJS(DeoHelper::getJsAdminDir().'onepagecheckout/admin/html5sortable.min.js');
		$this->context->controller->addJS(DeoHelper::getJsAdminDir().'onepagecheckout/admin/split.min.js');
		$this->context->controller->addJS(DeoHelper::getJsAdminDir().'onepagecheckout/admin/back.js');
	}

	public function renderForm()
	{
		Media::addJsDefL('deoonepagecheckout_reset_conf_for', $this->l('Reset default configuration for'));
		Media::addJsDefL('deoonepagecheckout_init_html_editor', $this->l('Use HTML text editor'));

		// // Check DB required fields (Customer and Address objects)
		// $tmpCustomer    = new Customer();
		// $requiredFields = $tmpCustomer->getFieldsRequiredDatabase();
		// foreach ($requiredFields as $field) {
		// 	return "[required fields error] " . $field['object_name'] . ':' . $field['field_name'];
		// }
		// if (class_exists('CustomerAddress')) {
		// 	$tmpAddress     = new CustomerAddress();
		// 	$requiredFields = $tmpAddress->getFieldsRequiredDatabase();
		// 	foreach ($requiredFields as $field) {
		// 		return "[required fields error] " . $field['object_name'] . ':' . $field['field_name'];
		// 	}
		// }
		
		// // Legacy Address object
		// if (class_exists('Address')) {
		// 	$tmpAddress     = new Address();
		// 	$requiredFields = $tmpAddress->getFieldsRequiredDatabase();
		// 	foreach ($requiredFields as $field) {
		// 		return "[required fields error (legacy Address object)] " . $field['object_name'] . ':' . $field['field_name'];
		// 	}
		// }

	
		$tabs =  array(
			'tab_general' => $this->l('General'),
			'tab_customer_address' => $this->l('Customer and Address'),
			'tab_shipping_payment' => $this->l('Shipping and Payment'),
			'tab_fields' => $this->l('Fields input'),
		);

		// custom template
		$inputs_general = array(
			array(
				'type' => 'switch',
				'label' => $this->l('Use One Page Checkout'),
				'name' => DeoHelper::getConfigName('ENABLE_ONEPAGECHECKOUT'),
				'is_bool' => true,
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_general',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Use One Page Checkout for mobile devices'),
				'name' => DeoHelper::getConfigName('USE_ONEPAGECHECKOUT_MOBILE'),
				'is_bool' => true,
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_general',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Blocks update loader'),
				'desc' => $this->l('Display loading animation whenever blocks on checkout form are updated through Ajax.'),
				'name' => DeoHelper::getConfigName('BLOCKS_UPDATE_LOADER'),
				'is_bool' => true,
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_general',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show product stock info'),
				'desc' => $this->l('Display in-stock, out-of-stock, or missing quantity in cart summary.'),
				'name' => DeoHelper::getConfigName('SHOW_PRODUCT_STOCK_INFO'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_general',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Clean remembered status of checkboxes and radios'),
				'desc' => $this->l('Clean remembered status of checkboxes (Terms & conditions, Customer privacy, ...) after order is confirmed'),
				'name' => DeoHelper::getConfigName('CLEAN_CHECKOUT_SESSION_AFTER_CONFIRMATION'),
				'is_bool' => true,
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_general',
			),
		);

		$inputs_customer_address = array(
			array(
				'type' => 'switch',
				'label' => $this->l('Allow guest checkout for registered'),
				'desc' => $this->l('Allow even registered customers to checkout as guest, so that no log-in is required. Be careful with this function because it can make fake order and your data can be trash with customers have same email.'),
				'name' => DeoHelper::getConfigName('ALLOW_GUEST_CHECKOUT_FOR_REGISTERED'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show "Create an account" checkbox'),
				'desc' => $this->l('Instead of password field, show checkbox to create an account. "password" must not be required in Customer Fields below.'),
				'name' => DeoHelper::getConfigName('CREATE_ACCOUNT_CHECKBOX'),
				'is_bool' => true,
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show "I am a business" checkbox'),
				'desc' => $this->l('Show checkbox on top of Invoice address, which would expand Company and tax fields'),
				'name' => DeoHelper::getConfigName('SHOW_I_AM_BUSINESS'),
				'is_bool' => true,
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address show_business_fields',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Business fields'),
				'desc' => $this->l('Comma separated list of fields shown in separate section for business customers and hidden for customers not a business. Example: company, dni, vat_number'),
				'name' => DeoHelper::getConfigName('BUSINESS_FIELDS'),
				'default' => 'company, dni, vat_number',
				'form_group_class' => 'tab_customer_address group_show_business_fields',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Business disabled fields'),
				'desc' => $this->l('Comma separated list of fields hidden for business customers and visible only for customers not a business. Example: company, dni, vat_number'),
				'name' => DeoHelper::getConfigName('BUSINESS_DISABLED_FIELDS'),
				'default' => '',
				'form_group_class' => 'tab_customer_address group_show_business_fields',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show "I am private customer" checkbox'),
				'desc' => $this->l('Show checkbox on top of Invoice address, which would expand as dni field'),
				'name' => DeoHelper::getConfigName('SHOW_I_AM_PRIVATE'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address show_private_customer',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Private customer fields'),
				'desc' => $this->l('Comma separated list of fields shown in separate section for private customers'),
				'name' => DeoHelper::getConfigName('PRIVATE_FIELDS'),
				'default' => 'dni',
				'form_group_class' => 'tab_customer_address group_show_private_customer',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show "Delivery Address" checkbox'),
				'desc' => $this->l('Show checkbox to expand "Delivery Address" (second address) in invoice address use when your client want to delivery to different address with invoice address'),
				'name' => DeoHelper::getConfigName('OFFER_SECOND_ADDRESS'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Always expand "Delivery Address"'),
				'desc' => $this->l('Make both addresses (invoice + delivery) visible right away. If "Delivery Address" (second address) checkbox is enabled it always ticked'),
				'name' => DeoHelper::getConfigName('EXPAND_SECOND_ADDRESS'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Mark required fields (*)'),
				'desc' => $this->l('Show red star next to required fields label'),
				'name' => DeoHelper::getConfigName('MARK_REQUIRED_FIELDS'),
				'is_bool' => true,
				'default' => '1',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Always ticked "Sign up Newsletter" checkbox'),
				'desc' => $this->l('Newsletter checkbox always ticked when register.Module ps_emailsubscription have to enabled!'),
				'name' => DeoHelper::getConfigName('NEWSLETTER_CHECKED'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show phone prefix'),
				'desc' => $this->l('Help customer easier when display phone prefix number in front of phone number fields, dynamic change based when change country'),
				'name' => DeoHelper::getConfigName('SHOW_CALL_PREFIX'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Initialize Address'),
				'desc' => $this->l('On initial load, set the address object. Enable if your shipping methods depend on address ID or if you use delivery date/time widget.'),
				'name' => DeoHelper::getConfigName('INITIALIZE_ADDRESS'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_customer_address',
			),
		);
	

		$paymentOptions        = Hook::getHookModuleExecList('paymentOptions');
		$paymentOptionsCombo   = array();
		$paymentOptionsCombo[] = array('id' => 'none', 'name' => ' - no selection - ');
		foreach ($paymentOptions as $option) {
			$paymentOptionsCombo[] = array('id' => $option['module'], 'name' => $option['module']);
		}

		$enforcedSeparatePaymentModules = array('xps_checkout', 'braintreeofficial');
		$separate_payment_required = false;
		foreach ($enforcedSeparatePaymentModules as $moduleName) {
			if (Module::isInstalled($moduleName) && Module::isEnabled($moduleName)) {
				$separate_payment_required = true;
			}
		}

		$inputs_shipping_payment = array(
			array(
				'type' => 'text',
				'label' => $this->l('Shipping required fields'),
				'desc' => $this->l('Comma separated list of fields that need to be filled-in to show shipping options. Example: id_state, postcode, city'),
				'default' => '',
				'name' => DeoHelper::getConfigName('SHIPPING_REQUIRED_FIELDS'),
				'form_group_class' => 'tab_shipping_payment',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show country when choose carriers'),
				'desc' => $this->l('Show shipping country name in carriers selection.'),
				'name' => DeoHelper::getConfigName('SHOW_SHIPPING_COUNTRY_IN_CARRIERS'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_shipping_payment',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Remove white spaces from postcode'),
				'desc' => $this->l('Help customer easier when postcode field is modified, white spaces inner are removed automatically'),
				'name' => DeoHelper::getConfigName('POSTCODE_REMOVE_SPACES'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_shipping_payment',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show Order Message'),
				'desc' => $this->l('Show text box for customer add comment about order'),
				'name' => DeoHelper::getConfigName('SHOW_ORDER_MESSAGE'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_shipping_payment',
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Display payment options on separate page'),
				'desc' => $this->l('Final payment options list will be displayed on separate page. Optional for any payment method, but required if you use payment module: Prestashop Checkout or Braintree Official'),
				'name' => DeoHelper::getConfigName('SEPARATE_PAYMENT'),
				'is_bool' => false,
				'default' => '0',
				'values' => DeoSetting::returnYesNo(),
				'form_group_class' => 'tab_shipping_payment show_separate_payment',
			),
			array(
				'type' => 'select',
				'label' => $this->l('Default payment method'),
				'desc' => $this->l('Payment method will be ticked by default'),
				'name' => DeoHelper::getConfigName('DEFAULT_PAYMENT_METHOD'),
				'options' => array(
					'query' => $paymentOptionsCombo,
					'id' => 'id',
					'name' => 'name'),
				'default' => 'local',
				'form_group_class' => 'tab_shipping_payment group_show_separate_payment',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Payment required fields'),
				'desc' => $this->l('Comma separated list of fields that need to be filled-in to show payment options. Example: id_state, firstname, lastname'),
				'name' => DeoHelper::getConfigName('PAYMENT_REQUIRED_FIELDS'),
				'default' => '',
				'form_group_class' => 'tab_shipping_payment group_show_separate_payment',
			),
		);

		$inputs_fields = array(
			array(
				'type' => 'hidden',
				'name' => DeoHelper::getConfigName('CUSTOMER_FIELDS'),
				'form_group_class' => 'tab_fields customer_fields_input',
			),
			array(
				'type' => 'hidden',
				'name' => DeoHelper::getConfigName('INVOICE_FIELDS'),
				'form_group_class' => 'tab_fields invoice_fields_input',
				
			),
			array(
				'type' => 'hidden',
				'name' => DeoHelper::getConfigName('DELIVERY_FIELDS'),
				'form_group_class' => 'tab_fields delivery_fields_input',
			),
			// array(
			// 	'type'         => 'textarea',
			// 	'label'        => $this->l('Required Checkbox No.1'),
			// 	'name'         => DeoHelper::getConfigName('REQUIRED_CHECKBOX_1'),
			// 	'desc'         => $this->l('Arbitrary checkbox that user needs to confirm to proceed with order, enter content text to display. You can add label also with link.').'<br>'.$this->l('Example: ').'<b>'.$this->l('I agree with <a href="content/2-legal-notice">privacy policy</a>').'</b>',
			// 	'lang'         => true,
			// 	'default' 	   => '',
			// 	'autoload_rte' => false,
			// 	'class'        => 'tinymce-on-demand',
			// 	'rows'         => '50',
			// 	'form_group_class' => 'tab_fields',
			// ),
			// array(
			// 	'type'         => 'textarea',
			// 	'label'        => $this->l('Required Checkbox No.2'),
			// 	'name'         => DeoHelper::getConfigName('REQUIRED_CHECKBOX_2'),
			// 	'desc'         => $this->l('Arbitrary checkbox that user needs to confirm to proceed with order, enter content text to display. You can add label also with link.').'<br>'.$this->l('Example: ').'<b>'.$this->l('I agree with <a href="content/2-legal-notice">privacy policy</a>').'</b>',
			// 	'lang'         => true,
			// 	'default' 	   => '',
			// 	'autoload_rte' => false, 
			// 	'class'        => 'tinymce-on-demand',
			// 	'rows'         => '50',
			// 	'form_group_class' => 'tab_fields',
			// ),
		);


		$inputs_hidden = array(
			array(
				'type' => 'hidden',
				'name' => 'tab_open',
			),
		);
		
		$inputs_header = array(
			array(
				'type' => 'tabConfig',
				'name' => 'title',
				'values' => $tabs,
				'default' => Tools::getValue('tab_open') ? Tools::getValue('tab_open') : 'tab_general',
				'save' => false,
			)
		);
		$inputs = array_merge($inputs_header, $inputs_general, $inputs_customer_address, $inputs_shipping_payment, $inputs_fields);

		$fields_form = array(
			'input' => $inputs,
			'submit' => array(
				'class' => 'btn btn-default pull-right '.get_class($this),
				'title' => $this->l('Save'),
			),
		);
 

		$this->fields_form = $fields_form;


		if ($this->submitSaveSetting && Tools::isSubmit('submitAddconfiguration')) {
			# SAVING CONFIGURATION
			$this->saveThemeConfigs();
			$this->confirmations[] = 'Your configurations have been saved successfully.';
		}


		// return parent::renderForm();
		$form = parent::renderForm();

		// SECTION Address fields
		$customerFieldsSortable = $this->renderCustomerFields();
		// Inject our address sortable form in address-fields section
		$re     = '/name="'.DeoHelper::getConfigName('CUSTOMER_FIELDS').'.*?<\/div>/s';
		$subst  = '$0 ' . $customerFieldsSortable;
		$form = preg_replace($re, $subst, $form, 1);


		// SECTION Address fields
		$addressSortable = $this->renderAddressFields();
		// Inject our address sortable form in address-fields section
		$re     = '/name="'.DeoHelper::getConfigName('INVOICE_FIELDS').'.*?<\/div>/s';
		$subst  = '$0 ' . $addressSortable;
		$form = preg_replace($re, $subst, $form, 1);


		return $form;
	}

	public function saveThemeConfigs()
	{
		$languages = Language::getLanguages(false);
		foreach ($this->fields_form['input'] as $input) {
			if (isset($input['lang']) && $input['lang']) {
				$data = array();
				foreach ($languages as $lang) {
					$value = Tools::getValue(trim($input['name']).'_'.$lang['id_lang']);
					$data[$lang['id_lang']] = $value ? $value : $input['default'];
				}
				Configuration::updateValue(trim($input['name']), $data, true);
			} else {
				if (isset($input['save']) && $input['save']) {
					// NOT SAVE
				} else {
					if (Tools::strpos($input['name'], '[]')){
						$input_name_conf = Tools::str_replace_once('[]', '', $input['name']);
						$value = (!empty(Tools::getValue(trim($input_name_conf)))) ? Tools::getValue(trim($input_name_conf)) : array();
						$value = json_encode($value);
						Configuration::updateValue(trim($input_name_conf), $value);
					}else{
						$value = Tools::getValue(trim($input['name']), Configuration::get($input['name']));

						// don't change SEPARATE_PAYMENT value, when field is "disabled"
						if ($input['name'] == DeoHelper::getConfigName('SEPARATE_PAYMENT')) {
							foreach (HelperOnepagecheckout::enforcedSeparatePaymentModules as $moduleName) {
								if (Module::isInstalled($moduleName) && Module::isEnabled($moduleName)) {
									return '';
								}
							}
						}

						// if ($input['name']== DeoHelper::getConfigName('CLEAN_CHECKOUT_SESSION_AFTER_CONFIRMATION') && (int) $value) {
						// 	unset($this->context->cookie->opc_form_checkboxes);
						// 	unset($this->context->cookie->opc_form_radios);
						// }

						if ($this->helper_opc->isJsonField($input['name'])) {
							$decodedString = json_decode(trim($value), true); // true = return array instead of stdObject
							if (!is_array($decodedString)){
								if (Tools::strpos(Tools::strtolower(DeoHelper::getConfigName($input['name'])), 'customer_fields') !== false){
									$value = $this->helper_opc->customer_fields;
								}elseif(Tools::strpos(Tools::strtolower(DeoHelper::getConfigName($input['name'])), 'invoice_fields') !== false){
									$value = $this->helper_opc->invoice_fields;
								}elseif(Tools::strpos(Tools::strtolower(DeoHelper::getConfigName($input['name'])), 'delivery_fields') !== false){
									$value = $this->helper_opc->delivery_fields;
								}
								$decodedString = $value;
							}

							// Special treatment for password field - whenever its 'required' status is updated here
							// on config page, let's update PS core config value also
							if ($input['name'] == DeoHelper::getConfigName('CUSTOMER_FIELDS')) {
								Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', !($decodedString['password']['visible'] && $decodedString['password']['required']));
							}
							
							Configuration::updateValue($input['name'], json_encode($decodedString));
						} else {
							Configuration::updateValue($input['name'], $value);
						}
					}
					
				}
			}
		}
	}


	private function ajaxCall()
	{
		$action = Tools::getValue('action');

		switch ($action) {
			case 'resetAccountFields':
				$this->resetConfigAccountFields();
				break;
			case 'resetInvoiceFields':
				$this->resetConfigInvoiceFields();
				break;
			case 'resetDeliveryFields':
				$this->resetConfigDeliveryFields();
				break;
		}
	}


	private function resetConfigAccountFields()
	{
		Configuration::deleteByName(DeoHelper::getConfigName('CUSTOMER_FIELDS'));
	}

	private function resetConfigInvoiceFields()
	{
		Configuration::deleteByName(DeoHelper::getConfigName('INVOICE_FIELDS'));
	}

	private function resetConfigDeliveryFields()
	{
		Configuration::deleteByName(DeoHelper::getConfigName('DELIVERY_FIELDS'));
	}


	private function renderCustomerFields()
	{	
		$this->context->smarty->assign(array(
			'label'  => $this->l('Register Fields'),
			'fields' => $this->helper_opc->parseFormFields('customer_fields'),
		));

		$result = $this->context->smarty->fetch('module:deotemplate/views/templates/admin/deo_onepagecheckout_configure/customer-fields.tpl');

		return $result;
	}

	private function renderAddressFields()
	{
		$this->context->smarty->assign(array(
			'addressLabel'      => $this->l('Invoice Address Fields'),
			'addressTypeFields' => 'invoice-fields',
			'fields'            => $this->helper_opc->parseFormFields('invoice_fields'),
		));

		$result = $this->context->smarty->fetch('module:deotemplate/views/templates/admin/deo_onepagecheckout_configure/address-fields.tpl');

		$this->context->smarty->assign(array(
			'addressLabel'      => $this->l('Delivery Address Fields'),
			'addressTypeFields' => 'delivery-fields',
			'fields'            => $this->helper_opc->parseFormFields('delivery_fields'),
		));

		$result .= $this->context->smarty->fetch('module:deotemplate/views/templates/admin/deo_onepagecheckout_configure/address-fields.tpl');

		return $result;
	}



	public function getFieldsValue($obj)
	{
		unset($obj);
		$languages = Language::getLanguages(false);
		foreach ($this->fields_form as $f) {
			foreach ($f['form']['input'] as $input) {
				if (isset($input['lang'])) {
					foreach ($languages as $lang) {
						if (Tools::getIsset($input['name'].'_'.$lang['id_lang'])){
							$val = Tools::getValue($input['name'].'_'.$lang['id_lang']);
						}else if (Configuration::hasKey($input['name'], $lang['id_lang'])){
							$val = Configuration::get($input['name'], $lang['id_lang']);
						}
						$input['default'] = isset($input['default']) ? $input['default'] : '';
						$this->fields_values[$input['name']][$lang['id_lang']] = isset($val) ? $val : $input['default'];
					}
				} else {
					if ($this->helper_opc->isJsonField($input['name'])) {
						$val = '';
						if (Tools::getIsset($input['name'])){
							$val = Tools::getValue($input['name']);
						}else if (Configuration::hasKey($input['name'])){
							$val = Configuration::get($input['name']);
						}

						$decodedString = json_decode(trim($val), true); // true = return array instead of stdObject
						if (!is_array($decodedString)){
							if (Tools::strpos(Tools::strtolower(DeoHelper::getConfigName($input['name'])), 'customer_fields') !== false){
								$val = $this->helper_opc->customer_fields;
							}elseif(Tools::strpos(Tools::strtolower(DeoHelper::getConfigName($input['name'])), 'invoice_fields') !== false){
								$val = $this->helper_opc->invoice_fields;
							}elseif(Tools::strpos(Tools::strtolower(DeoHelper::getConfigName($input['name'])), 'delivery_fields') !== false){
								$val = $this->helper_opc->delivery_fields;
							}
							$val = json_encode($val);
						}
					}else{
						if (Tools::strpos($input['name'], '[]')){
							$input_name_conf = Tools::str_replace_once('[]', '', $input['name']);
							if (Tools::getIsset($input_name_conf)){
								$val = Tools::getValue($input_name_conf);
							}else if (Configuration::hasKey($input_name_conf)){
								$val = Configuration::get($input_name_conf);
							}
						}else{
							if (Tools::getIsset($input['name'])){
								$val = Tools::getValue($input['name']);
							}else if (Configuration::hasKey($input['name'])){
								$val = Configuration::get($input['name']);
							}
						}
					}

					$input['default'] = isset($input['default']) ? $input['default'] : '';
					$this->fields_values[$input['name']] = isset($val) ? $val : $input['default'];
					unset($val);
				}
			}
		}

		return $this->fields_values;
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


	public function setHelperDisplay(Helper $helper)
	{
		parent::setHelperDisplay($helper);
		$this->helper->module = DeoTemplate::getInstance();
	}
}
