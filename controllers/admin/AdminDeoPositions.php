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

require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplatePositionsModel.php');

class AdminDeoPositionsController extends ModuleAdminControllerCore
{
    public $position_js_folder = '';
    public $position_css_folder = '';
    public $position_customize_css_folder = '';
    public $position_customize_setting_folder = '';
    public $module_name = 'deotemplate';
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $theme_dir;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'deotemplate_positions';
        $this->className = 'DeoTemplatePositionsModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->order_by = 'position';
        $this->order_way = 'DESC';
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'deotemplate_positions_shop` ps ON (ps.`id_deotemplate_positions` = a.`id_deotemplate_positions`)';
        parent::__construct();
        $this->fields_list = array(
            'id_deotemplate_positions' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 50,
                'class' => 'fixed-width-xs'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!position',
                'remove_onclick' => true
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!name',
                'remove_onclick' => true
            ),
            'position_key' => array(
                'title' => $this->l('Key'),
                'filter_key' => 'a!position_key',
                'type' => 'text',
                'width' => 140,
                'remove_onclick' => true
            )
        );
        $this->list_no_link = 'no';
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            ),
            'correctlink' => array(
                'text' => $this->l('Correct Image Link'),
                'confirm' => $this->l('Are you sure you want to change image url from old theme to new theme?'),
                'icon' => 'icon-edit'
            ),
            'insertLang' => array(
                'text' => $this->l('Auto Input Data for New Lang'),
                'confirm' => $this->l('Auto insert data for new language?'),
                'icon' => 'icon-edit'
            )
        );
        $this->_where = ' AND ps.id_shop='.(int)$this->context->shop->id;

        $this->theme_dir           = DeoHelper::getThemeDir();
        $this->position_css_folder = DeoHelper::getThemeDir().DeoHelper::getCssDir().'positions/';
        $this->position_js_folder  = DeoHelper::getThemeDir().DeoHelper::getJsDir().'positions/';
        $this->position_customize_css_folder = DeoHelper::getThemeDir().DeoHelper::getCssDir().'customize/';
        $this->position_customize_setting_folder = DeoHelper::getThemeDir().DeoHelper::getJsDir().'customize/';
        
        if (!is_dir($this->position_css_folder)) {
            @mkdir($this->position_css_folder, 0755, true);
        }
        if (!is_dir($this->position_js_folder)) {
            @mkdir($this->position_js_folder, 0755, true);
        }
        if (!is_dir($this->position_customize_css_folder)) {
            @mkdir($this->position_customize_css_folder, 0755, true);
        }
        if (!is_dir($this->position_customize_setting_folder)) {
            @mkdir($this->position_customize_setting_folder, 0755, true);
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        
        # SAVE AND STAY
        if($this->display == 'add' || $this->display == 'edit'){
            $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');

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
        
        # Delete POSITIONS NOT USE
        switch ($this->display) {
            default:
                $this->toolbar_btn['new'] = array(
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->l('Add new'),
                    'class' => 'btn_add_new',
                );
                $this->toolbar_btn['eraser'] = array(
                    'href' => self::$currentIndex.'&deo_delete_position=1&token='.$this->token,
                    'desc' => $this->l('Delete position do not use (fix error when create profile)'),
                    'imgclass' => 'eraser',
                    'class' => 'deo_delete_position',
                );
                if ($this->allow_export) {
                    unset($this->toolbar_btn['export']);
                }
        }
    }
    
    public function processDelete()
    {
        $object = $this->loadObject();
        // Check using other profile
        $result = DeoTemplatePositionsModel::getProfileUsingPosition($object->id);
        if (!$result) {
            $object = parent::processDelete();
            if ($object->position_key) {
                Tools::deleteFile($this->position_css_folder.$object->position.$object->position_key.'.css');
                Tools::deleteFile($this->position_js_folder.$object->position.$object->position_key.'.js');
            }
        } else {
            $name_profile = '';
            $sep = '';
            foreach ($result as $item) {
                $name_profile .= $sep.$item['name'];
                $sep = ', ';
            }
            $this->errors[] = sprintf($this->l('Can not delete position "%s", it is being used by Profile : "%s"'), $object->name, $name_profile);
        }
        return $object;
    }

    public function processBulkDelete()
    {
        // Remove resouce and update table profiles
        $arr = $this->boxes;
        if (!$arr) {
            return;
        }
        
        foreach ($arr as $id) {
            $profiles = DeoTemplatePositionsModel::getProfileUsingPosition($id);
            $object = new DeoTemplatePositionsModel($id);
            if (!$profiles) {
                $object->delete();
                if ($object->position_key) {
                    Tools::deleteFile($this->position_css_folder.$object->position.$object->position_key.'.css');
                    Tools::deleteFile($this->position_js_folder.$object->position.$object->position_key.'.js');
                }
            } else {
                $name_profile = '';
                $sep = '';
                foreach ($profiles as $profile) {
                    $name_profile .= $sep.$profile['name'];
                    $sep = ', ';
                }
                $this->errors[] = sprintf($this->l('Can not delete position "%s", it is being used by Profile : "%s"'), $object->name, $name_profile);
            }
        }
        if (empty($this->errors)) {
            $this->confirmations[] = $this->_conf[1];
        }
    }

    public function renderView()
    {
        $object = $this->loadObject();
        $this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoHome');
        $this->redirect_after .= '&id_deotemplate_positions='.$object->id;
        $this->redirect();
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

                $id_category = (($id_category = (int)Tools::getValue('id_category')) && Tools::getValue('id_product')) ? '&id_category='.$id_category : '';
                $this->redirect_after .= '&conf=5'.$id_category;
            } else {
                $this->errors[] = $this->l('You can not disable default profile, Please select other profile as default');
            }
        } else {
            $this->errors[] = $this->l('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.$this->l('(cannot load object)');
        }

        return $object;
    }

    public function postProcess()
    {
        parent::postProcess();
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::getIsset('duplicatedeotemplate_positions')) {
            $id = Tools::getValue('id_deotemplate_positions');
            $this->duplicatePosition($id, '');
        }

        # Delete POSITIONS NOT USE
        if (Tools::getValue('deo_delete_position') && Tools::getValue('deo_delete_position')) {
            DeoHelper::processDeleteOldPosition();
            $this->confirmations[] = 'POSITIONS NOT USE have been deleted successfully.';
        }
    }

    public function duplicatePosition($id, $type = '', $name = '')
    {
        $id = (int)$id;
        $object = DeoTemplatePositionsModel::getPositionById($id);
        if ($object) {
            $key = DeoSetting::getRandomNumber();
            $old_key = $object['position_key'];
            $name = $name ? $name : $this->l('Duplicate ').$key;
            $data = array('name' => $name, 'position' => $object['position'], 'position_key' => 'duplicate_'.$key, 'params' => $object['params']);
            $model = new DeoTemplatePositionsModel();
            $duplicate_id = $model->addAuto($data);
            AdminDeoShortcodesController::duplcateDataPosition($id, $duplicate_id);
            if ($duplicate_id) {
                //duplicate shortCode
                if ($file_content = Tools::file_get_contents($this->position_js_folder.$data['position'].$old_key.'.js')){
                    DeoSetting::writeFile($this->position_js_folder, $data['position'].$data['position_key'].'.js', $file_content);
                }
                if ($file_content = Tools::file_get_contents($this->position_css_folder.$data['position'].$old_key.'.css')){
                    DeoSetting::writeFile($this->position_css_folder, $data['position'].$data['position_key'].'.css', $file_content);
                }
                if ($file_content = Tools::file_get_contents($this->position_customize_setting_folder.$data['position'].$old_key.'.json')){
                    DeoSetting::writeFile($this->position_customize_setting_folder, $data['position'].$data['position_key'].'.json', $file_content);
                }
                if ($file_content = Tools::file_get_contents($this->position_customize_css_folder.$data['position'].$old_key.'.css')){
                    DeoSetting::writeFile($this->position_customize_css_folder, $data['position'].$data['position_key'].'.css', $file_content);
                }
                if ($type != 'ajax') {
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                    $this->redirect();
                } else {
                    return $duplicate_id;
                }
            } else {
                if ($type != 'ajax') {
                    Tools::displayError('Can not duplicate Position');
                } else {
                    return 0;
                }
            }
        } else if ($type != 'ajax') {
            Tools::displayError('Can not duplicate Position');
        } else {
            return 0;
        }
    }

    public function renderList()
    {
        $this->initToolbar();

        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        return parent::renderList();
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
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Position Manage'),
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
                    'label' => $this->l('Position Key'),
                    'name' => 'position_key',
                    'required' => true,
                    'disabled' => ($this->display == 'edit') ? true : false,
                    'desc' => $this->l('Use it to save as file name of css and js of Position'),
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'position',
                    'disabled' => ($this->display == 'edit') ? true : false,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'mobile',
                                'name' => $this->l('Mobile'),
                            ),
                            array(
                                'id' => 'header',
                                'name' => $this->l('Header'),
                            ),
                            array(
                                'id' => 'content',
                                'name' => $this->l('Content'),
                            ),
                            array(
                                'id' => 'footer',
                                'name' => $this->l('Footer'),
                            ),
                            array(
                                'id' => 'product',
                                'name' => $this->l('Product'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                // array(
                //     'type' => 'textarea',
                //     'label' => $this->l('Custom Css'),
                //     'name' => 'css',
                //     'desc' => sprintf($this->l('Please set write Permission for folder %s'), $this->position_css_folder),
                // ),
                // array(
                //     'type' => 'textarea',
                //     'label' => $this->l('Custom Js'),
                //     'name' => 'js',
                //     'desc' => sprintf($this->l('Please set write Permission for folder %s'), $this->position_js_folder),
                // )
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
                    'icon' => 'process-icon-save'
                ),
                'change-position-key' => array(
                    'title' => $this->l('Change Position Key'),
                    'name' => 'submit'.$this->table.'ChangePositionKey',
                    'type' => 'submit',
                    'class' => 'btn btn-primary pull-right',
                    'icon' => 'process-icon-loading',
                ),
            )
        );
        return parent::renderForm();
    }

    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);
        // if ($obj->id && $obj->position_key) {
        //     $file_value['css'] = Tools::file_get_contents($this->position_css_folder.$obj->position.$obj->position_key.'.css');
        //     $file_value['js'] = Tools::file_get_contents($this->position_js_folder.$obj->position.$obj->position_key.'.js');
        // } else {
        //     $file_value['position_key'] = 'position'.DeoSetting::getRandomNumber();
        // }
        if ($this->display == 'add'){
            $file_value['position_key'] = 'position'.DeoSetting::getRandomNumber();
        }
        return $file_value;
    }

    public function processAdd()
    {
        if ($obj = parent::processAdd()) {
            $this->saveCustomJsAndCss($obj->position.$obj->position_key, '');
        }
    }

    public function processUpdate()
    {
        // Check ifchange position => need delete current file css/js before update
        $old_object = parent::loadObject();
        if ($obj = parent::processUpdate()) {
            $this->saveCustomJsAndCss($obj->position.$obj->position_key, $old_object->position.$obj->position_key);
        }

        if (Tools::isSubmit('submit'.$this->table.'ChangePositionKey')) {
            $old_key = $obj->position_key;
            $new_key = 'position'.DeoSetting::getRandomNumber();
            $position = $obj->position;
            $obj->position_key = $new_key;
            $obj->update();
           

            if ($file_content = Tools::file_get_contents($this->position_js_folder.$position.$old_key.'.js')){
                DeoSetting::writeFile($this->position_js_folder, $position.$new_key.'.js', $file_content);
            }
            if ($file_content = Tools::file_get_contents($this->position_css_folder.$position.$old_key.'.css')){
                DeoSetting::writeFile($this->position_css_folder, $position.$new_key.'.css', $file_content);
            }
            if ($file_content = Tools::file_get_contents($this->position_customize_setting_folder.$position.$old_key.'.json')){
                DeoSetting::writeFile($this->position_customize_setting_folder, $position.$new_key.'.json', $file_content);
            }
            if ($file_content = Tools::file_get_contents($this->position_customize_css_folder.$position.$old_key.'.css')){
                DeoSetting::writeFile($this->position_customize_css_folder, $position.$new_key.'.css', $file_content);
            }

            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoPositions');
            $this->redirect_after .= '&updatedeotemplate_positions&id_deotemplate_positions='.($this->object->id);
            $this->redirect();
        }
    }

    public function saveCustomJsAndCss($key, $old_key = '')
    {
        return true;

        // Delete old file
        if ($old_key) {
            if (Tools::getValue('js') != '') {
                Tools::deleteFile($this->position_js_folder.$old_key.'.js');
                DeoSetting::writeFile($this->position_js_folder, $key.'.js', Tools::getValue('js'));
            }
            
            if (Tools::getValue('css') != '') {
                Tools::deleteFile($this->position_css_folder.$old_key.'.css');
                DeoSetting::writeFile($this->position_css_folder, $key.'.css', Tools::getValue('css'));
            }
        }

    }

    /**
     * Auto create a position for page build profile editing/creating
     * @param type $obj
     */
    public function autoCreatePosition($obj)
    {
        $model = new DeoTemplatePositionsModel();
        $id = $model->addAuto($obj);
        if ($id) {
            $this->saveCustomJsAndCss($obj['position'].$obj['position_key'], '');
        }
        return $id;
    }

    public function updateName($id, $name)
    {
        return DeoTemplatePositionsModel::updateName($id, $name);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Context::getContext()->controller->addJs(DeoHelper::getJsAdminDir().'admin/form_admin_positions.js');
        Media::addJsDefL('deo_confirm_text', $this->l('Are you sure you want to Delete do not use position. Please back-up all thing before?'));
        Media::addJsDefL('deo_form_submit', Context::getContext()->link->getAdminLink('AdminDeoPositions'));
    }
    
    public function displayDuplicateLink($token = null, $id = null, $name = null)
    {
        $controller = 'AdminDeoPositions';
        $token = Tools::getAdminTokenLite($controller);
        $html = '<a href="#" title="Duplicate" onclick="confirm_link(\'\', \'Duplicate Position ID '.$id.'. If you wish to proceed, click &quot;Yes&quot;. If not, click &quot;No&quot;.\', \'Yes\', \'No\', \'index.php?controller='.$controller.'&amp;id_deotemplate_positions='.$id.'&amp;duplicatedeotemplate_positions&amp;token='.$token.'\', \'#\')">
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
        
        if (count($this->errors) <= 0) {
            if( Tools::isSubmit('duplicate'.$this->table) ) {
                if ($this->id_object) {
                    if (!$this->access('add'))
                    {
                        $this->errors[] = $this->trans('You do not have permission to duplicate this.', array(), 'Admin.Notifications.Error');
                    }
                }
            }elseif(Tools::getIsset('deo_delete_position') && Tools::getValue('deo_delete_position')){
                if (!$this->access('delete'))
                {
                    $this->errors[] = $this->trans('You do not have permission to delelte this.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
