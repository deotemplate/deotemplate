<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

if (!class_exists('DeoShortCodeBase')) {
    require_once(_PS_MODULE_DIR_.'deotemplate/libs/Helper.php');
    require_once(_PS_MODULE_DIR_.'deotemplate/controllers/admin/AdminDeoShortcodes.php');
    require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoShortCodesBuilder.php');

    abstract class DeoShortCodeBase
    {
        /*
         * override it for each widget
         */
        public $name = '';
        /**
         * override when using tinymcs
         */
        public $tinymce = 0;
        public $module_name = 'deotemplate';
        public $id_shop = 0;
        public $fields_form = array();
        public $types = array();
        public $config_list = array();
        public $str_search;
        public $str_relace;
        public $str_relace_html;
        public $str_relace_html_admin;
        public $theme_img_module;
        public $theme_dir;

        public function __construct()
        {
            $this->str_search = DeoHelper::getStrSearch();
            $this->str_relace = DeoHelper::getStrReplace();
            $this->str_relace_html = DeoHelper::getStrReplaceHtml();
            $this->str_relace_html_admin = DeoHelper::getStrReplaceHtmlAdmin();
            // Not run with multi_shop (ex block carousel cant get image in backend multi_shop)
            $this->theme_img_module = DeoHelper::getImgThemeUrl();
            $this->theme_dir = DeoHelper::getThemeDir();
        }
        /*
         * if file is not exist in theme folder, will get it in module folder, this function only apply for font end
         */

        public function getDirOfFile($path_theme, $file, $path_module = '')
        {
            if (file_exists(DeoHelper::getThemeDir().$path_theme.'/'.$file)) {
                // validate module
                return DeoHelper::getThemeDir().$path_theme.'/'.$file;
            } else {
                if ($path_module) {
                    return _PS_MODULE_DIR_.'deotemplate/'.$path_module.$file;
                } else {
                    return _PS_MODULE_DIR_.'deotemplate/'.$path_theme.$file;
                }
            }
        }

        /**
         * Get class name of product item by plistkey (using in widgets: DeoProductList and DeoProductCarousel)
         */
        public function getProductClassByPListKey($plist = '')
        {
            // Against SQL injections
            // $plist = pSQL($plist ? $plist : '');
            $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'deotemplate_products WHERE plist_key="'.pSQL($plist).'" LIMIT 1');
            if ($result) {
                return $result[0]['class'];
            }
            return 'profile-default';
        }

        /**
         * abstract method to return html widget form
         */
        public function getInfo()
        {
            return array('key' => 'base', 'label' => 'Widget Base');
        }

        public static function getUrlProfileEdit()
        {
            $id_profile = Tools::getIsset('id_deotemplate_profiles') ? Tools::getValue('id_deotemplate_profiles') : '';
            if (!$id_profile) {
                $profile = DeoTemplateProfilesModel::getActiveProfile('index');
                $id_profile = $profile['id_deotemplate_profiles'];
            }
            // $controller = 'AdminDeoHome';
            // $id_lang = Context::getContext()->language->id;
            // $params = array('token' => Tools::getAdminTokenLite($controller));
            // $url_profile_edit = $admin_dir.'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
            $url_profile_edit = Context::getContext()->link->getAdminLink('AdminDeoProfiles').
                    '&id_deotemplate_profiles='.$id_profile.'&updatedeotemplate_profiles';
            return $url_profile_edit;
        }

        public static function getShortCodeInfos()
        {
            $shortcode_dir = _PS_MODULE_DIR_.'deotemplate/classes/shortcodes/';
            $source_file = Tools::scandir($shortcode_dir);
            $short_code_list = array();
            $is_sub_tab = Tools::getValue('subTab');
            foreach ($source_file as $value) {
                $fileName = basename($value, '.php');
                if ($fileName == 'index' || $fileName == 'DeoColumn' || $fileName == 'DeoRow' || ($is_sub_tab && ($fileName == 'DeoTabs' || $fileName == 'DeoAccordions'))) {
                    continue;
                }
                require_once($shortcode_dir.$value);
                $obj = new $fileName;
                $short_code_list[$fileName] = array_merge($obj->getInfo(),array('name' => $fileName));
            }
            return $short_code_list;
        }

        /**
         * abstract method to return widget data
         */
        public function renderContent($args, $data)
        {
            // validate module
            unset($args);
            unset($data);
            return true;
        }
        
        public function getTranslator()
        {
            static $translator;
            if (!$translator) {
                // $translator = Context::getContext()->controller->module->getTranslator(); // Run at Backend
                $translator = Context::getContext()->getTranslator();       // Run at Backend + Frontend. Test with DeoGmap Widget
            }
            return $translator;
        }

        protected function trans($id, array $parameters = array(), $domain = 'Modules.Deotemplate.Admin', $locale = null)
        {
            return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
        }
        
        public function l($string, $specific = false)
        {
            if (Tools::getValue('type_shortcode')) {
            // FIX Translate in loading widget ajax
                return TranslateCore::getModuleTranslation($this->module_name, $string, ($specific) ? $specific : Tools::getValue('type_shortcode'));
            } else {
                # TRANSLATE FOR MODULE - CALL MODULE
//                return Module::getInstanceByName('DeoTemplate')->l($string, $specific);

                # TRANSLATE FOR MODULE - CALL CORE
//                return TranslateCore::getModuleTranslation($this->module_name, $string, ($specific) ? $specific : $this->module_name);
                
                # TRANSLATE FOR WIDGET
                $str = TranslateCore::getModuleTranslation($this->module_name, $string, $this->name);
                
                if (Tools::getIsset('controller') && (Tools::getValue('controller')=='AdminDeoHome' || Tools::getValue('controller')=='AdminDeoShortcode' )) {
                    # FIX TRANSLATE FRANCE HAS ' -> error JS
                    $str = str_replace('\'', ' ', $str);
                }
                return $str;
                                
            }
        }
        
        public function getInputValues($type, $value)
        {
            if ($type == 'switchYesNo') {
                return array(array('id' => $value.'_on', 'value' => 1, 'label' => $this->l('Yes')),
                    array('id' => $value.'_off', 'value' => 0, 'label' => $this->l('No')));
            }
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
                            $fields_values[$input['name']][$lang['id_lang']] = isset($obj[$input['name'].'_'
                                            .$lang['id_lang']]) ? $obj[$input['name'].'_'.$lang['id_lang']] : $input['default'];
                        }
                    } else if (isset($obj[trim($input['name'])])) {
                        $value = $obj[trim($input['name'])];

                        if ($input['name'] == 'image' && $value) {
                            // $thumb = __PS_BASE_URI__.'modules/'.$this->name.'/img/'.$value;
                            $thumb = DeoHelper::getImgThemeUrl().$value;
                            $this->fields_form[$k]['form']['input'][$j]['thumb'] = $thumb;
                        }
                        $fields_values[$input['name']] = $value;
                    } else {
                        $v = Tools::getValue($input['name'], DeoHelper::get($input['name']));
                        $fields_values[$input['name']] = $v ? $v : $input['default'];
                    }
                }
            }
            if (isset($data['id_deowidgets'])) {
                $fields_values['id_deowidgets'] = $data['id_deowidgets'];
            }
            return $fields_values;
        }

        /**
         * Return config value for each shortcode
         */
        public function getConfigValue()
        {
            $config_val = array();
            //return addition config
            $a_config = $this->getAdditionConfig();
            if ($a_config) {
                $this->config_list = array_merge($this->config_list, $a_config);
            }
            foreach ($this->config_list as $config) {
                $config['lang'] = (isset($config['lang']) && $config['lang']) ? $config['lang'] : '';
                $config['name'] = (isset($config['name']) && $config['name']) ? $config['name'] : '';
                $config['default'] = (isset($config['default']) && $config['default']) ? $config['default'] : '';

                if ($config['lang']) {
                    $config_val[$config['name']] = array();
                    foreach (Language::getLanguages(false) as $lang) {
                        //$config_val[$config['name']] = Tools::getValue($config['name'], $config['default']);
                        $config_val[$config['name']][$lang['id_lang']] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config['name'].'_'.$lang['id_lang'], $config['default']));
                    }
                } else if (false !== Tools::strpos($config['name'], '[]')) {
                    $get_val_name = str_replace('[]', '', $config['name']);
                    $config_val[$config['name']] = explode(',', Tools::getValue($get_val_name, $config['default']));
                } else {
                    $config_val[$config['name']] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config['name'], $config['default']));
                }
            }
            //$config_val[$config['name']] = Tools::getValue($config['name'], $config['default']);
            $config_val['override_folder'] = Tools::getValue('override_folder', '');
            return $config_val;
        }

        /**
         * Override in each shource code to return config list
         */
        public function getConfigList()
        {
        }

        /**
         * Return AdditionConfig list, when you use override of input in helper
         */
        public function getAdditionConfig()
        {
        }

        public function preparaAdminContent($atts, $tag_name = null)
        {
            if ($tag_name == null) {
                $tag_name = $this->name;
            }
            //need reprocess
            if (is_array($atts)) {
                $atts = array_diff($atts, array(''));
                if (!isset(DeoShortCodesBuilder::$shortcode_lang[$tag_name])) {
                    $inputs = $this->getConfigList();
                    $lang_field = array();
                    foreach ($inputs as $input) {
                        if (isset($input['lang']) && $input['lang']) {
                            $lang_field[] = $input['name'];
                        }
                    }
                    DeoShortCodesBuilder::$shortcode_lang[$tag_name] = $lang_field;
                } else {
                    $lang_field = DeoShortCodesBuilder::$shortcode_lang[$tag_name];
                }
                foreach ($atts as $key => $val) {
                    if ($lang_field && in_array($key, $lang_field)) {
                        $key .= '_'.DeoShortCodesBuilder::$lang_id;
                    }
                    if (!isset(DeoShortCodesBuilder::$data_form[$atts['form_id']][$key])) {
                        //find language fields
                        // if (Tools::strpos($key, '_array') !== false) {
                        //     $key = str_replace ('_array', '[]', $key);
                        // }
                        DeoShortCodesBuilder::$data_form[$atts['form_id']][$key] = $val;
                    }
                }
            }
        }

        /**
         * Get content for normal short code - from shource controller
         */
        public function adminContent($atts, $content = null, $tag_name = null, $is_gen_html = null)
        {
            // validate module
            unset($tag_name);
            $this->preparaAdminContent($atts);
            if ($is_gen_html) {
                foreach ($atts as $key => $val) {
                    if (Tools::strpos($key, 'content') !== false || Tools::strpos($key, 'link') !== false || Tools::strpos($key, 'url') !== false || Tools::strpos($key, 'alt') !== false || Tools::strpos($key, 'tit') !== false || Tools::strpos($key, 'name') !== false || Tools::strpos($key, 'desc') !== false || Tools::strpos($key, 'itemscustom') !== false) {
                        $atts[$key] = str_replace($this->str_search, $this->str_relace_html, $val);
                    }
                }
                $assign = array();
                $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
                if (isset($atts['content_html'])) {
                    $atts['content_html'] = str_replace($this->str_search, $this->str_relace_html, $atts['content_html']);
                }
                $assign['formAtts'] = $atts;
                $w_info = $this->getInfo();
                $w_info['name'] = $this->name;
                $assign['deoInfo'] = $w_info;
                if ($this->name == 'DeoColumn') {
                    $assign['colClass'] = $this->convertColWidthToClass($atts);
                    $assign['widthList'] = DeoSetting::returnWidthList();
                }
                // add parameter to create animation for group/column
                if ($this->name == 'DeoRow' || $this->name == 'DeoColumn') {
                    $assign['listAnimation'] = DeoSetting::getAnimationsColumnGroup();
                }
                
                $controller = new AdminDeoShortcodesController();
                return $controller->adminContent($assign, $this->name.'.tpl');
            } else {
                DeoShortCodesBuilder::doShortcode($content);
            }
        }

        public function prepareFontContent($assign, $module = null)
        {
            // validate module
            unset($module);
            return $assign;
        }

        /**
         * Get content for normal short code - from shource controller
         */
        public function fontContent($atts, $content = null)
        {
            $is_active = $this->isWidgetActive(array('formAtts' => $atts));
            if (!$is_active) {
                return '';
            }
            $module = DeoTemplate::getInstance();

            $assign = array();
            $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
            foreach ($atts as $key => $val) {
                if (Tools::strpos($key, 'content') !== false || Tools::strpos($key, 'link') !== false || Tools::strpos($key, 'url') !== false || Tools::strpos($key, 'alt') !== false || Tools::strpos($key, 'tit') !== false || Tools::strpos($key, 'name') !== false || Tools::strpos($key, 'desc') !== false || Tools::strpos($key, 'itemscustom') !== false) {
                    $atts[$key] = str_replace($this->str_search, $this->str_relace_html, $val);
                }
            }

            if (!isset($atts['class'])) {
                $atts['class'] = '';
            }
            if (isset($atts['specific_type']) && $atts['specific_type']) {
                $current_page = DeoHelper::getPageName();
                
                //$current_hook = DeoShortCodesBuilder::$hook_name;
                if ($atts['specific_type'] == 'all') {
                    $ex_page = explode(',', isset($atts['controller_pages']) ? $atts['controller_pages'] : '');
                    $ex_page = array_map('trim', $ex_page);
                    if (in_array($current_page, $ex_page)) {
                        return '';
                    }

                    # Front modules controller       fc=module    module=...    controller=...
                    $current_page = Tools::getValue('fc').'-'.Tools::getValue('module').'-'.Tools::getValue('controller');
                    if (in_array($current_page, $ex_page)) {
                        return '';
                    }
                } else {
                    if ($current_page != $atts['specific_type']) {
                        return '';
                    }
                    if ($current_page == 'category' || $current_page == 'product' || $current_page == 'cms') {
                        $ids = explode(',', $atts['controller_id']);
                        $ids = array_map('trim', $ids);
                        if ($atts['controller_id'] != '' && !DeoSetting::getControllerId($current_page, $ids)) {
                            return '';
                        }
                    }
                }
            }
            if ($this->name == 'DeoColumn') {
                $atts['class'] = $this->convertColWidthToClass($atts). ' ' .$atts['class'];
            }
            $atts['class'] .= ' '.$this->name;
            $atts['class'] = trim($atts['class']);
            $atts['rtl'] = Context::getContext()->language->is_rtl;
            $assign['formAtts'] = $atts;
            
            
            # FIX 1.7 GLOBAL VARIABLE
            $assign['img_manu_dir'] = _THEME_MANU_DIR_;

            $assign['comparator_max_item'] = (int)Configuration::get('PS_COMPARATOR_MAX_ITEM');
            $assign['compared_products'] = array();
            $assign['tpl_dir'] = DeoHelper::getThemeDir();
            $assign['PS_CATALOG_MODE'] = (int) Configuration::get('PS_CATALOG_MODE');
            $assign['priceDisplay'] = ProductCore::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);
            $assign['PS_STOCK_MANAGEMENT'] = (int) Configuration::get('PS_STOCK_MANAGEMENT');
            $assign['PS_ORDER_OUT_OF_STOCK'] = (int) Configuration::get('PS_ORDER_OUT_OF_STOCK');
            $assign['page_name'] = DeoHelper::getPageName();
            $assign['deo_helper'] = DeoHelper::getInstance();
            isset($assign['formAtts']['override_folder']) ? true : $assign['formAtts']['override_folder'] = '';

            $assign = $this->prepareFontContent($assign, $module);


            // echo "<pre>";
            // $data = array(
            //     array('name' => $this->name, 'attr' => $atts),
            // );

            // print_r($assign['deo_html_content']);
            // die('sss');

            // print_r($data);
            // $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcodeJsonToHtml($data);


            
            $override_folder = '';
            if (isset($atts['override_folder']) && $atts['override_folder'] != ''){
                $override_folder = $atts['override_folder'];
            }
            
            
            // echo $cache_id.'<br>';
            // var_dump($module->isCached($tpl_file, $cache_id));
            // if ($module->isCached($tpl_file, $cache_id)) {
            //     // die('ssss');
            //     // return $module->display(__FILE__, $tpl_file, $cache_id);
            //     return $module->fetch('module:deotemplate/'.$tpl_file, $cache_id);
            // }
            // echo "<pre>";
            // print_r($assign['formAtts']);
            // echo "</pre>";

            if ((isset($atts['disable_cache']) && $atts['disable_cache']) || (Tools::strpos($atts['class'], 'DeoCustomerActions') !== false) || (Tools::strpos($atts['class'], 'DeoSocialLogin') !== false)){
               
                return $module->fontContent($assign, $this->name.'.tpl');
            }else{

                $tpl_file = DeoHelper::getTemplate($this->name.'.tpl', $override_folder);
                $cache_id = $module->getCacheId('module:deotemplate/'.$tpl_file, $atts['form_id']);
                return $module->fontContent($assign, $this->name.'.tpl', $cache_id);
            }
            
            
            // return $module->fontContent($assign, $this->name.'.tpl');
        }

        public function isWidgetActive($assign)
        {
            $flag = true;

            if (isset($assign['formAtts']['active']) && $assign['formAtts']['active'] == 0) {
                $flag = false;
            }

            return $flag;
        }

        public function convertColWidthToClass($atts)
        {
            $class = '';
            // 1.7 Update Module
            if (!array_key_exists('xxl', $atts)) {
                $class .= 'col-xxl-12';
            }
            if (!array_key_exists('xl', $atts)) {
                $class .= 'col-xl-12';
            }
            foreach ($atts as $key => $val) {
                if ($key == 'xxl' || $key == 'xl' || $key == 'lg' || $key == 'md' || $key == 'sm' || $key == 'xs' || $key == 'sp') {
                    $class .= ' col-'.$key.'-'.$val;
                }
            }
            return $class;
        }

        /**
         * Return config form
         */
        public function renderForm()
        {
            $helper = new HelperForm();
            $helper->show_toolbar = false;
            $helper->table = (isset($this->table) && $this->table) ? $this->table : '';
            $helper->name_controller = 'form_'.$this->name;
            $lang = new Language((int)Context::getContext()->language->id);     // current lang in admin
            $default_lang = $lang->id;
            $this->fields_form = array();
            $helper->identifier = (isset($this->identifier) && $this->identifier) ? $this->identifier : '';
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            foreach (Language::getLanguages(false) as $lang) {
                $helper->languages[] = array(
                    'id_lang' => $lang['id_lang'],
                    'iso_code' => $lang['iso_code'],
                    'name' => $lang['name'],
                    'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
                );
            }
            $helper->default_form_language = $default_lang;
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
                    Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
            $this->config_list = $this->getConfigList();
            //add code for override tpl folder
            if ($this->name != 'DeoRow' && $this->name != 'DeoColumn' && $this->name != 'DeoModule') {
                $this->config_list[count($this->config_list)] = array(
                    'type' => 'text',
                    'name' => 'override_folder',
                    'label' => $this->l('Override Folder', 'shortcodes'),
                    'desc' => $this->l('[Developer Only] System will auto create folder, you can put tpl of this shortcode to the folder. You can use this function to show 2 different layout', 'shortcodes'),
                    'default' => ''
                );
            }

            $w_info = $this->getInfo();
            $title_widget = array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="modal-widget-title">'.$w_info['label'].'</div>',
            );
            array_unshift($this->config_list, $title_widget);
            $helper->submit_action = $this->name;
            $field_value = $this->getConfigValue();
            $this->addConfigList($field_value);
            $fields_form = array(
                'form' => array(
                    'input' => $this->config_list,
                    'name' => $this->name,
                    'class' => $this->name,
                    'tinymce' => $this->tinymce
                ),
            );
            $helper->tpl_vars = array(
                'fields_value' => $field_value,
                'widthList' => DeoSetting::returnWidthList(),
            );
            
            $this->helper = $helper;
            if (method_exists($this, $method_name = 'endRenderForm')) {
                $this->$method_name();
            }
            
            return $this->helper->generateForm(array($fields_form));
        }

        public function renderDefaultConfig($is_sub = false, $keep_name=false){
            $result = array();

            $config = $this->getConfigList($is_sub, $keep_name);

            if (count($config) <= 0 || !is_array($config)) {
                return $result;
            }

            $result['form_id'] = 'form_'.DeoSetting::getRandomNumber();
            foreach ($config as $field) {
                if (isset($field['lang']) && $field['lang']){
                    $languages = Language::getLanguages(false);
                    foreach ($languages as $lang) {
                        $result[$field['name'].'_'.$lang['id_lang']] = (isset($field['default'])) ? $field['default'] : '';
                    }
                } else if (false !== Tools::strpos($field['name'], '[]')) {
                    $get_val_name = str_replace('[]', '', $field['name']);
                    $result[$get_val_name] = (isset($field['default'])) ? $field['default'] : '';
                }else{
                    $result[$field['name']] = (isset($field['default'])) ? $field['default'] : '';
                }
            }

            $result['override_folder'] = '';
            if (($field['name'] != 'DeoTabs' || $field['name'] != 'DeoAccordions') && !$is_sub){
                $result['active'] = 1;
            }

            return $result;
        }

        /**
         * Widget can override this method and add more config at here
         */
        public function addConfigList($field_value)
        {
            // validate module
            unset($field_value);
        }
        
        /**
         * Widget can override this method and add more config at here
         */
        public function displayModuleExceptionList()
        {
            return '';
        }

        public function ajaxProcessRender($module)
        {
            // validate module
            unset($module);
        }


        /**
         * alias from DeoHelper::getConfigName()
         */
        public function getConfigName($name)
        {
            return DeoHelper::getConfigName($name);
        }
    }
 
}
