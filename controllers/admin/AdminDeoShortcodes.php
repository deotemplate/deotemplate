<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoSetting.php');
require_once(_PS_MODULE_DIR_.'deotemplate/controllers/admin/AdminDeoPositions.php');

class AdminDeoShortcodesController extends ModuleAdminControllerCore
{
    public static $shortcode_lang;
    public static $language;
    public static $lang_id;
    public $file_content = '';
    protected $max_image_size = null;
    public $theme_name;
    public $config_module;
    public $hook_assign;
    public $module_name;
    public $module_path;
    public $tpl_controller_path;
    public $tpl_front_path;
    public $shortcode_dir;
    public $shortcode_override_dir;
    public $theme_dir;
    public $theme_url;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->show_toolbar = true;
        $this->table = 'deotemplate';
        $this->className = 'DeoTemplateShortCode';
        $this->context = Context::getContext();
        $this->module_name = 'deotemplate';
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
        $this->tpl_controller_path = _PS_ROOT_DIR_.'/modules/'.$this->module_name.'/views/templates/admin/deo_shortcodes/';
        $this->tpl_front_path = _PS_ROOT_DIR_.'/modules/'.$this->module_name.'/views/templates/font/';
        $this->shortcode_dir = _PS_MODULE_DIR_.'deotemplate/classes/shortcodes/';

        self::$language = Language::getLanguages(false);
        parent::__construct();
        $this->theme_dir = _PS_THEME_DIR_;
        $this->theme_url = _THEMES_DIR_.Context::getContext()->shop->theme_name.'/';
        $this->shortcode_override_dir = $this->theme_dir.'modules/deotemplate/classes/shortcodes/';
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->theme_name = Context::getContext()->shop->theme_name;
        $this->hook_assign = array('rightcolumn', 'leftcolumn', 'home', 'top', 'footer');
    }
    
    public function init()
    {
        if (Tools::getIsset('type_shortcode')) {
            # Run AJAX here for Hight Speed
            $this->ajaxLoadWidget();
        }
        parent::init();
    }

    /**
     * Duplicate all data relate with profile
     * @param type int $old_id : current profile id is duplicating
     * @param type int $new_id : new profile id after duplicated
     */
    public static function duplicateData($old_id, $new_id)
    {
        // $context = Context::getContext();
        $positions = array();
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'deotemplate_profiles` p
                WHERE p.id_deotemplate_profiles='.(int)$old_id;
        $result = Db::getInstance()->getRow($sql);
        if ($result) {
            $positions[] = $result['header'];
            $positions[] = $result['content'];
            $positions[] = $result['footer'];
            $positions[] = $result['product'];
            $positions[] = $result['mobile'];
        }
        $sql_update = 'UPDATE '._DB_PREFIX_.'deotemplate_profiles ';
        $sep = ' SET ';
        $is_update = false;
        // Duplicate positions
        foreach ($positions as $item) {
            $id = (int)$item;
            $object = DeoTemplatePositionsModel::getPositionById($id);
            if ($object) {
                $key = DeoSetting::getRandomNumber();
                $old_key = $object['position_key'];
                $name = 'Duplicate '.$object['position'].' '.$key;
                $data = array('name' => $name, 'position' => $object['position'], 'position_key' => 'duplicate_'.$object['position'].$key, 'params' => $object['params']);
                $model = new DeoTemplatePositionsModel();
                $duplicate_id = $model->addAuto($data);
                if ($duplicate_id) {
                    $position_controller = new AdminDeoPositionsController();
                    $sql_update .= $sep.$data['position'].'='.$duplicate_id;
                    $sep = ', ';
                    $is_update = true;
                    self::duplcateDataPosition($id, $duplicate_id);
                    if ($file_content = Tools::file_get_contents($position_controller->position_js_folder.$data['position'].$old_key.'.js')){
                        DeoSetting::writeFile($position_controller->position_js_folder, $data['position'].$data['position_key'].'.js', $file_content);
                    }
                    if ($file_content = Tools::file_get_contents($position_controller->position_css_folder.$data['position'].$old_key.'.css')){
                        DeoSetting::writeFile($position_controller->position_css_folder, $data['position'].$data['position_key'].'.css', $file_content);
                    }
                    if ($file_content = Tools::file_get_contents($position_controller->position_customize_setting_folder.$data['position'].$old_key.'.json')){
                        DeoSetting::writeFile($position_controller->position_customize_setting_folder, $data['position'].$data['position_key'].'.json', $file_content);
                    }
                    if ($file_content = Tools::file_get_contents($position_controller->position_customize_css_folder.$data['position'].$old_key.'.css')){
                        DeoSetting::writeFile($position_controller->position_customize_css_folder, $data['position'].$data['position_key'].'.css', $file_content);
                    }
                }
            }
        }
        if ($is_update) {
            $sql_update .= ' WHERE id_deotemplate_profiles='.(int)$new_id;
            Db::getInstance()->execute($sql_update);
        }
    }

    /**
     * Duplicate a position: dulicate data in table deotemplate_lang; deotemplate; deotemplate_shop;
     * @param type int $old_id: position id for duplicate
     * @param type int $duplicate_id: new position id
     */
    public static function duplcateDataPosition($old_id, $duplicate_id)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        // Get list deotemplate for copy
        $sql = 'SELECT * from '._DB_PREFIX_.'deotemplate p WHERE p.id_deotemplate_positions='.(int)$old_id;
        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $item) {
            // Duplicate to tables deotemplate
            $sql = 'INSERT INTO '._DB_PREFIX_.'deotemplate (id_deotemplate_positions, hook_name)
                VALUES("'.(int)$duplicate_id.'", "'.pSQL($item['hook_name']).'")';
            Db::getInstance()->execute($sql);
            // Duplicate to tables deotemplate_shop
            $id_new = Db::getInstance()->Insert_ID();
            $sql = 'INSERT INTO '._DB_PREFIX_.'deotemplate_shop (id_deotemplate, id_shop)
                    VALUES('.(int)$id_new.', '.(int)$id_shop.')';
            Db::getInstance()->execute($sql);
            // Copy data and languages
            $sql = 'SELECT * from '._DB_PREFIX_.'deotemplate_lang p
                     WHERE p.id_deotemplate='.(int)$item['id_deotemplate'];
            $old_data = Db::getInstance()->executeS($sql);
            foreach ($old_data as $temp) {
                $sql = 'INSERT INTO '._DB_PREFIX_."deotemplate_lang (id_deotemplate, id_lang, params)
                VALUES('".(int)$id_new."', '".(int)$temp['id_lang']."', '".DeoHelper::replaceFormId($temp['params'])."')";       // Not pSQL at here because  duplicate profile is error
                Db::getInstance()->execute($sql);
            }
        }
    }


    public function adminContent($assign, $tpl_name)
    {
        // die($this->tpl_controller_path.$tpl_name);
        if (file_exists($this->tpl_controller_path.$tpl_name)) {
            $tpl = $this->createTemplate($tpl_name);
        } else {
            $tpl = $this->createTemplate('DeoGeneral.tpl');
        }
        $assign['moduleDir'] = _MODULE_DIR_;
        foreach ($assign as $key => $ass) {
            $tpl->assign(array($key => $ass));
        }
        return $tpl->fetch();
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    /**
     * AJAX :
     * - load widget or module
     * - call method of widget
     */
    private function ajaxLoadWidget()
    {
        $type_shortcode = Tools::ucfirst(Tools::getValue('type_shortcode'));
        $type = Tools::getValue('type');
        $shor_code_dir = '';

        // Add new widget
        if ($type === 'widget') {
            if (!$shor_code_dir = DeoSetting::requireShortCode($type_shortcode.'.php', $this->theme_dir)) {
                die($this->l('This short code is not exist'));
            }
            if (class_exists($type_shortcode) != true) {
                // validate module
                require_once($shor_code_dir);
            }

            $obj = new $type_shortcode;
            die($obj->renderForm());
        } elseif ($type === 'module') {
            // Custom a module
            $shor_code_dir = DeoSetting::requireShortCode('DeoModule.php', $this->theme_dir);
            if (class_exists('DeoModule') != true) {
                // validate module
                require_once($shor_code_dir);
            }
            $obj = new DeoModule();
            die($obj->renderForm());
        }
        
        
        
        # RUN WIDGET METHOD - BEGIN
        $result = array(
            'hasError' => false,
            'error' => '',
            'success' => false,
            'information' => '',
        );
        $type_shortcode = Tools::ucfirst(Tools::getValue('type_shortcode'));
        if (Tools::getIsset('type_shortcode') && $type_shortcode) {
            if ($shor_code_dir = DeoSetting::requireShortCode($type_shortcode.'.php', $this->theme_dir)) {
                if (class_exists($type_shortcode) != true) {
                    require_once($shor_code_dir);
                }
                $obj = new $type_shortcode;
                $method_name = Tools::getValue('method_name', '');
                $method_name = 'ajaxCallBack'.Tools::toCamelCase($method_name, true);

                if (method_exists($obj, $method_name)) {
                    # RUN WIDGET METHOD
                    $obj->{$method_name}();
                    $result['information'] = $method_name . $this->l(' is successful');
                } else {
                    $result['error'] =  sprintf($this->l('%s method is not exist'), $method_name);
                }
            } else {
                   $result['error'] = sprintf($this->l('%s is not found'), $type_shortcode.'.php');
            }
        } else {
            $result['error'] = $this->l('type_shortcode param is empty');
        }
        
        if (count($result['error'])) {
            $result['hasError'] = true;
        }
        
        die(json_encode($result));
        # RUN WIDGET METHOD - END
    }
    
    public function viewAccess($disable = true)
    {
        return true;
        return $this->access('view', $disable);
    }
}
