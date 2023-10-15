<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */



require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProfilesModel.php');

/**
 * NOT extends ModuleAdminControllerCore, because override tpl : ROOT/modules/deotemplate/views/templates/admin/deo_theme_configuration/helpers/form/form.tpl
 */
class AdminDeoProfilesController extends ModuleAdminController
{
    private $theme_name = '';
    public $profile_js_folder = '';
    public $profile_css_folder = '';
    public $module_name = 'deotemplate';
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $theme_dir;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'deotemplate_profiles';
        $this->className = 'DeoTemplateProfilesModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;

        parent::__construct();
        $this->theme_dir = _PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name.'/';

        $this->context = Context::getContext();

        $this->order_by = 'page';
        $this->order_way = 'DESC';
        $alias = 'sa';

        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->employee->id_lang;
        $this->_join .= 'JOIN `'._DB_PREFIX_.'deotemplate_profiles_shop` 
                sa ON (a.`id_deotemplate_profiles` = sa.`id_deotemplate_profiles` AND sa.id_shop = '.$id_shop.')';
        $this->_join .= 'JOIN `'._DB_PREFIX_.'deotemplate_profiles_lang` 
                la ON (a.`id_deotemplate_profiles` = la.`id_deotemplate_profiles` AND la.id_lang = '.$id_lang.')';
        $this->_select .= 'sa.active as active, la.friendly_url as friendly_url';

        $this->fields_list = array(
            'id_deotemplate_profiles' => array(
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
            'profile_key' => array(
                'title' => $this->l('Key'),
                'filter_key' => 'a!profile_key',
                'type' => 'text',
                'width' => 140,
            ),
            'friendly_url' => array(
                'title' => $this->l('Friendly URL'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'la!friendly_url'
            ),
            'active' => array(
                'title' => $this->l('Is Default'),
                'active' => 'status',
                'filter_key' => $alias.'!active',
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
            ),
            'insertLang' => array(
                'text' => $this->l('Auto Input Data for New Lang'),
                'confirm' => $this->l('Auto insert data for new language?'),
                'icon' => 'icon-edit'
            )
        );

        if ((int) DeoHelper::getConfig('DEBUG_MODE')){
            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'confirm' => $this->l('Delete selected items?'),
                    'icon' => 'icon-trash'
                ),
                'insertLang' => array(
                    'text' => $this->l('Auto Input Data for New Lang'),
                    'confirm' => $this->l('Auto insert data for new language?'),
                    'icon' => 'icon-edit'
                )
            );
        }else{
            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'confirm' => $this->l('Delete selected items?'),
                    'icon' => 'icon-trash'
                )
            );
        }

        $this->_where = ' AND sa.id_shop='.(int)$this->context->shop->id;
        $this->theme_name = Context::getContext()->shop->theme_name;
        
        $this->profile_css_folder = DeoHelper::getThemeDir().DeoHelper::getCssDir().'profiles/';
        $this->profile_js_folder = DeoHelper::getThemeDir().DeoHelper::getJsDir().'profiles/';
        $this->position_customize_css_folder = DeoHelper::getThemeDir().DeoHelper::getCssDir().'customize/';
        $this->position_customize_setting_folder = DeoHelper::getThemeDir().DeoHelper::getJsDir().'customize/';

        
        if (!is_dir($this->profile_css_folder)) {
            @mkdir($this->profile_css_folder, 0755, true);
        }
        if (!is_dir($this->profile_js_folder)) {
            @mkdir($this->profile_js_folder, 0755, true);
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
    }
    
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('tagify');
    }

    public function processDelete()
    {
        $object = $this->loadObject();
        
        if ($object && !$object->active) {
            $object = parent::processDelete();
            if ($object->profile_key) {
                Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
                Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
            }
        } else {
            $this->errors[] = Tools::displayError('Can not delete Default Profile.');
        }
        return $object;
    }

    public function processBulkDelete()
    {
        $arr = $this->boxes;
        if (!$arr) {
            return;
        }
        foreach ($arr as $id) {
            $object = new $this->className($id);
            if ($object && !$object->active) {
                $object->delete();
                if ($object->profile_key) {
                    Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
                    Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
                }
            } else {
                $this->errors[] = Tools::displayError('Can not delete Default Profile.');
            }
        }
        if (empty($this->errors)) {
            $this->confirmations[] = $this->_conf[1];
        }
    }

    public function renderView()
    {
        //echo 'here';die;
        $object = $this->loadObject();
        if ($object->page == 'product_detail') {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoTemplateProductDetail');
        } else {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoHome');
        }
        $this->redirect_after .= '&id_deotemplate_profiles='.$object->id;
        $this->redirect();
    }
    
    public function displayViewLink($token = null, $id, $name = null)
    {
        // validate module
        unset($name);
        $token = Context::getContext()->link->getAdminLink('AdminDeoHome');
        $href = $token . '&id_deotemplate_profiles='.$id;
        $html = '<a href="'.$href.'" class="btn btn-default" title="'.$this->l('Change Layout').'"><i class="icon-cog"></i> '.$this->l('Change Layout').'</a>';
        return $html;
    }

    public function displayEditLink($token = null, $id, $name = null)
    {
        // validate module
        unset($name);
        $token = Context::getContext()->link->getAdminLink('AdminDeoProfiles');
        $href = $token . '&id_deotemplate_profiles='.$id.'&updatedeotemplate_profiles';
        $html = '<a href="'.$href.'" title="'.$this->l('Edit').'"><i class="icon-cog"></i> '.$this->l('Edit').'</a>';
        return $html;
    }

    public function processBulkinsertLang()
    {
        // Remove resouce and update table profiles
        $arr = $this->boxes;
        if (!$arr) {
            // validate module
            $arr[] = Tools::getValue('id');
        }

        if (!$arr) {
            return;
        }
        foreach ($arr as $item) {
            if ($item) {
                //has profile id
                $pfile = new DeoTemplateProfilesModel($item);
                $id_positions = array($pfile->mobile, $pfile->header, $pfile->content, $pfile->footer, $pfile->product);
                $list_position = $pfile->getPositionsForProfile($id_positions);
                $list_pos_id = array();
                foreach ($list_position as $v) {
                    // validate module
                    $list_pos_id[] = $v['id_deotemplate_positions'];
                }
                $s_model = new DeoTemplateModel();
                $list_short_c = $s_model->getAllItemsByPositionId($list_pos_id);
                $context = Context::getContext();
                $id_lang = (int)$context->language->id;
                foreach ($list_short_c as $shor_code) {
                    $s_model = new DeoTemplateModel($shor_code['id']);
                    if ($s_model->params) {
                        foreach ($s_model->params as $key => $value) {
                            if ($key != $id_lang) {
                                // validate module
                                $s_model->params[$key] = $s_model->params[$id_lang];
                            }
                            // validate module
                            unset($value);
                        }
                    }
                    $s_model->save();
                }
            }
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
            } else {
                $this->errors[] = Tools::displayError('You can not disable default profile, Please select other profile as default');
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                    .'<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
        return $object;
    }

    public function postProcess()
    {
        parent::postProcess();
        if (count($this->errors) > 0) {
            return;
        }
        if (Tools::getIsset('duplicatedeotemplate_profiles')) {
            // $context = Context::getContext();
            // $id_shop = $context->shop->id;
            $id = Tools::getValue('id_deotemplate_profiles');
            $model = new DeoTemplateProfilesModel($id);
            
            if($model){
                $old_key = $model->profile_key;
                $model->profile_key = $profile_key = 'profile'.DeoSetting::getRandomNumber();
                $model->id = null;
                $model->name = $this->l('Duplicate of ') . $model->name;
                $model->active = '';
                $model->friendly_url = array();
                $duplicate_object = $model->save();
                
                if($duplicate_object){
                    //duplicate shortCode
                    $id_new = $model->id;
                    if ($file_content = Tools::file_get_contents($this->profile_js_folder.$old_key.'.js')){
                        DeoSetting::writeFile($this->profile_js_folder, $profile_key.'.js', $file_content);
                    }
                    if ($file_content = Tools::file_get_contents($this->profile_css_folder.$old_key.'.css')){
                        DeoSetting::writeFile($this->profile_css_folder, $profile_key.'.css', $file_content);
                    }
                     
                    AdminDeoShortcodesController::duplicateData($id, $id_new);
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                    $this->redirect();
                }else{
                    Tools::displayError('Can not create new profile');
                }
            } else {
                Tools::displayError('Profile is not exist to duplicate');
            }
        }
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/form.css');
        $tpl_name = 'list.tpl';
        $path = '';
        if (file_exists($this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name)) {
            $path = $this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name;
        } elseif (file_exists($this->getTemplatePath().$this->override_folder.$tpl_name)) {
            $path = $this->getTemplatePath().$this->override_folder.$tpl_name;
        }
        $model = new DeoTemplateProfilesModel();
        $list_profiles = $model->getAllProfileByShop();
        // Build url for back from live edit page, it is stored in cookie and read in fontContent function below
        $controller = 'AdminDeoHome';
        $id_lang = Context::getContext()->language->id;
        $url_edit_profile_token = Tools::getAdminTokenLite($controller);
        $params = array('token' => $url_edit_profile_token);
        $url_edit_profile = dirname($_SERVER['PHP_SELF']).'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);

        $url_preview = Context::getContext()->link->getPageLink('index', null, $this->context->employee->id_lang);
        $enable_friendly_url = false;
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $enable_friendly_url = true;
        }

        $profile_link = $this->context->link->getAdminLink('AdminDeoProfiles').'&adddeotemplate_profiles';
        $this->context->smarty->assign(array(
            'enable_friendly_url' => $enable_friendly_url,
            'profile_link' => $profile_link,
            'url_preview' => $url_preview,
            'list_profile' => $list_profiles,
            'url_profile_detail' => $this->context->link->getAdminLink('AdminDeoProfiles'),
            'url_edit_profile_token' => $url_edit_profile_token,
            'url_edit_profile' => $url_edit_profile));
        $content = $this->context->smarty->fetch($path);

        return parent::renderList().$content;
        //return parent::renderList();
    }

    public function getLiveEditUrl($live_edit_params)
    {
        $lang = '';
        $admin_dir = dirname($_SERVER['PHP_SELF']);
        $admin_dir = Tools::substr($admin_dir, strrpos($admin_dir, '/') + 1);
        $dir = str_replace($admin_dir, '', dirname($_SERVER['SCRIPT_NAME']));
        if (Configuration::get('PS_REWRITING_SETTINGS') && count(Language::getLanguages(true)) > 1) {
            $lang = Language::getIsoById(Context::getContext()->employee->id_lang).'/';
        }
        $url = Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().$dir.$lang.
                Dispatcher::getInstance()->createUrl('index', (int)Context::getContext()->language->id, $live_edit_params);
        return $url;
    }

    public function renderForm()
    {
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/general.js');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/bootstrap-colorpicker.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/bootstrap-colorpicker.css');

        $type_dropdown = array(
            array(
                'id_type' => 'dropdown',
                'name_type' => $this->l('Dropdown'),
            ),
            array(
                'id_type' => 'dropup',
                'name_type' => $this->l('Dropup'),
            ),
            array(
                'id_type' => 'slidebar_left',
                'name_type' => $this->l('Slidebar Left'),
            ),
            array(
                'id_type' => 'slidebar_right',
                'name_type' => $this->l('Slidebar Right'),
            ),
            array(
                'id_type' => 'slidebar_top',
                'name_type' => $this->l('Slidebar Top'),
            ),
            array(
                'id_type' => 'slidebar_bottom',
                'name_type' => $this->l('Slidebar Bottom'),
            ),
        );
        $type_vertical = array(
            array(
                'id_type' => 'top',
                'name_type' => $this->l('Top'),
            ),
            array(
                'id_type' => 'bottom',
                'name_type' => $this->l('Bottom'),
            ),
        );
        $type_horizontal = array(
            array(
                'id_type' => 'left',
                'name_type' => $this->l('Left'),
            ),
            array(
                'id_type' => 'right',
                'name_type' => $this->l('Right'),
            ),
        );
        $type_effect = array(
            array(
                'id_type' => 'none',
                'name_type' => $this->l('None'),
            ),
            array(
                'id_type' => 'fade',
                'name_type' => $this->l('Fade'),
            ),
            array(
                'id_type' => 'shake',
                'name_type' => $this->l('Shake'),
            ),
        );

        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        // $groups = Group::getGroups($this->default_form_language, true);
        // UNSET GROUP_BOX
        // if ($this->object->id == '') {
        //     $model = new DeoTemplateProfilesModel();
        //     $list_profiles = $model->getAllProfileByShop();
        //     foreach ($list_profiles as $profile) {
        //         $group_boxs = $profile['group_box'];
        //         $aray_group_box = explode(',', $group_boxs);
        //         foreach ($aray_group_box as $group_box) {
        //             if ($group_box!=1&&$group_box!=2&&$group_box!=3) {
        //                 while ($group = current($groups)) {
        //                     if ($group['id_group'] == $group_box) {
        //                         unset($groups[key($groups)]);
        //                     }
        //                     next($groups);
        //                 }
        //             }
        //         }
        //     }
        // }
        
        $tabs =  array(
            'tab_general' => $this->l('General'),
            'tab_breadcrumb' => $this->l('Breadcrumb'),
            'tab_mobile_mode' => $this->l('Mobile Mode'),
            'tab_ajax_cart' => $this->l('Ajax Cart'),
            'tab_layout' => $this->l('Layout'),
            // 'tab_customize_js_css' => $this->l('Custommize JS - CSS'),
        );

        $inputs_general = array(
            array(
                'type' => 'text',
                'label' => $this->l('Homepage name'),
                'name' => 'name',
                'required' => true,
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Profile Key'),
                'name' => 'profile_key',
                'readonly' => 'readonly',
                'desc' => $this->l('Use it to save as file name of css and js of profile'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Friendly URL'),
                'name' => 'friendly_url',
                'lang' => true,
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Meta title'),
                'name' => 'meta_title',
                'id' => 'name', // for copyMeta2friendlyURL compatibility
                'lang' => true,
                'class' => 'copyMeta2friendlyURL',
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Meta description'),
                'name' => 'meta_description',
                'lang' => true,
                'cols' => 40,
                'rows' => 10,
                'class' => 'deo-textarea-autosize',
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'tags',
                'label' => $this->l('Meta keywords'),
                'name' => 'meta_keywords',
                'lang' => true,
                'desc' => $this->l('To add "tags" click in the field, write something, and then press "Enter."'),
                'form_group_class' => 'tab_general',
            ),
        );

        $inputs_breadcrumb = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Breadcrumb Image'),
                'name' => 'breadcrumb_image',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Yes: Show breadcrumb with background image. You can change background image of Breadcrumb by replace file bg_breadcrumb.jpg at: ').'<b>'._THEME_DIR_.'assets/img/</b>',
                'form_group_class' => 'tab_breadcrumb',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Fullwidth Breadcrumb Image'),
                'name' => 'breadcrumb_image_fullwidth',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_breadcrumb',
            ),
            
            array(
                'type' => 'switch',
                'label' => $this->l('Replace Breadcrumb Image by image category for on category page'),
                'name' => 'breadcrumb_category_image',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Replace image breadcrumb by category image on category page if exist'),
                'form_group_class' => 'tab_breadcrumb',
            ),
        );

        $inputs_mobile_mode = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Header Mobile'),
                'name' => 'header_mobile',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Yes: Replace layout position Header = hook displaydeoHeaderMobile. You can use this hook to build simple header with Homepage Builder (hook displayDeoHeaderMobile).'),
                'form_group_class' => 'tab_mobile_mode',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Navigator Mobile'),
                'name' => 'nav_mobile',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Yes: Use navigator friendly with mobile and tablet device. If you use this hook will auto hidden Advance Cart, Back To Top default on other hook. And you can use this hook to build navigator with Homepage Builder (hook displayDeoNavMobile)'),
                'form_group_class' => 'tab_mobile_mode',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Content Mobile'),
                'name' => 'content_mobile',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Yes: Replace layout position Content = hook displaydeoContentMobile. You can use this hook to create content own layout for mobile and tablet device with Homepage Builder (hook displayDeoContentMobile).'),
                'form_group_class' => 'tab_mobile_mode',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Footer Mobile'),
                'name' => 'footer_mobile',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Yes: Replace layout position Footer = hook displaydeoFooterMobile. You can use this hook to build simple footer with Homepage Builder (hook displayDeoFooterMobile).'),
                'form_group_class' => 'tab_mobile_mode',
            ),
        );

        $inputs_ajax_cart = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Configuration for ajax Default Cart').'</div>',
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Default Cart'),
                'name' => 'enable_dropdown_defaultcart',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Type Popup Default Cart'),
                'name' => 'type_dropdown_defaultcart',
                'options' => array(
                    'query' => $type_dropdown,
                    'id' => 'id_type',
                    'name' => 'name_type'
                ),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Configuration for Advance Cart').'</div>',
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Advance Cart'),
                'name' => 'enable_dropdown_flycart',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),

            array(
                'type' => 'select',
                'label' => $this->l('Type Popup Advance Cart'),
                'name' => 'type_dropdown_flycart',
                'options' => array(
                    'query' => $type_dropdown,
                    'id' => 'id_type',
                    'name' => 'name_type'
                ),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Overlay Background Advance Cart'),
                'name' => 'enable_overlay_background_flycart',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Popup Infomation Product After Add To Cart'),
                'name' => 'show_popup_after_add_to_cart',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Open Advance Cart After Add To Cart'),
                'name' => 'open_advance_cart_after_add_to_cart',
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Position Vertical Advance Cart'),
                'name' => 'position_vertical_flycart',
                'options' => array(
                    'query' => $type_vertical,
                    'id' => 'id_type',
                    'name' => 'name_type'
                ),
                'desc' => $this->l('Position vertical on your screen'),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Value Position Vertical Advance Cart'),
                'name' => 'position_vertical_value_flycart',
                'suffix' => 'px',
                'class' => 'fixed-width-lg',
                'desc' => $this->l('Unit have to is pixcel (px). Example: 10px'),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Position Horizontal Advance Cart'),
                'name' => 'position_horizontal_flycart',
                'options' => array(
                    'query' => $type_horizontal,
                    'id' => 'id_type',
                    'name' => 'name_type'
                ),
                'desc' => $this->l('Position horizontal on your screen'),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Value Position Horizontal Advance Cart'),
                'name' => 'position_horizontal_value_flycart',
                'suffix' => 'px',
                'class' => 'fixed-width-lg',
                'desc' => $this->l('Unit have to is pixcel (px). Example: 10px'),
                'form_group_class' => 'tab_ajax_cart',
            ),
        );

        $inputs_layout = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-warning">'.$this->l('This function below only for developer. You should not change it layout will be break').'</div>',
                'form_group_class' => 'tab_layout',
            ),
            array(
                'type' => 'checkbox',
                'name' => 'fullwidth_index_hook',
                'label' => $this->l('Hooks layout full width for Home page'),
                'class' => 'checkbox-group',
                'desc' => $this->l('The setting full width for above Hooks, apply for Home page'),
                'values' => array(
                    'query' => self::getCheckboxIndexHook(),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_layout',
            ),
            array(
                'type' => 'checkbox',
                'name' => 'fullwidth_other_hook',
                'label' => $this->l('Hooks layout full width for other pages'),
                'class' => 'checkbox-group',
                'desc' => $this->l('The setting full width for above Hooks, apply for all Ototherher pages (not Home page)'),
                'values' => array(
                    'query' => self::getCheckboxOtherHook(),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_layout',
            ),
            array(
                'type' => 'checkbox',
                'name' => 'fullwidth_content_other_page',
                'label' => $this->l('Hook displayHome (content page) full width for other pages'),
                'class' => 'checkbox-group',
                'desc' => $this->l('The setting full width for content other pages (not Home page)'),
                'values' => array(
                    'query' => self::getCheckboxOtherPages(),
                    'id' => 'id',
                    'name' => 'name',
                ),
                'form_group_class' => 'tab_layout',
            ),
            array(
                'type' => 'checkbox',
                'name' => 'disable_cache_hook',
                'label' => $this->l('Disable cache Hooks'),
                'class' => 'checkbox-group',
                'desc' => $this->l('Some modules always update data, disable cache for those modules show correct info.'),
                'values' => array(
                    'query' => self::getCheckboxCacheHook(),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_layout',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Profile For Page'),
                'name' => 'page',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'index',
                            'name' => $this->l('Index'),
                        ),
                        array(
                            'id' => 'product_detail',
                            'name' => $this->l('Product Detail'),
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_layout hide',
            ),
        );
        $params = json_decode($this->object->params);
        $inputs = array_merge($inputs_general, $inputs_breadcrumb, $inputs_mobile_mode, $inputs_ajax_cart, $inputs_layout);

        if (Tools::isSubmit('updatedeotemplate_profiles')) {
            $pfile = new DeoTemplateProfilesModel();

            $id_positions = array('content' => $this->object->content,'header' => $this->object->header, 'footer' => $this->object->footer);
            $list_position = $pfile->getPositionsForProfile($id_positions);

            $data_position = array(
                'content' => '',
                'header' => '',
                'footer' => '',
            );
            if (!empty($list_position)){
                $tabs = array_merge($tabs, array('tab_customize' => $this->l('Customize')));
                foreach ($list_position as $key => $position) {
                    if ($position['position'] == 'content'){
                        $data_position['content'] = $position['position_key'];
                        $this->fields_value['key_content'] = $position['position_key'];
                    }elseif ($position['position'] == 'header'){
                        $data_position['header'] = $position['position_key'];
                        $this->fields_value['key_header'] = $position['position_key'];
                    }elseif ($position['position'] == 'footer'){
                        $data_position['footer'] = $position['position_key'];
                        $this->fields_value['key_footer'] = $position['position_key'];
                    }
                }
                // $file_customize_header = $this->position_customize_setting_folder.$key_header.'.json';
                // $file_customize_footer = $this->position_customize_setting_folder.$key_footer.'.json';

                $inputs_key_customize = array(
                    array(
                        'type' => 'hidden',
                        'name' => 'key_content',
                        'form_group_class' => 'tab_customize',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'key_header',
                        'form_group_class' => 'tab_customize',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'key_footer',
                        'form_group_class' => 'tab_customize',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Use customize'),
                        'name' => 'customize',
                        'is_bool' => false,
                        'values' => DeoSetting::returnYesNo(),
                        'form_group_class' => 'tab_customize',
                    ),
                );

                $inputs = array_merge($inputs, $inputs_key_customize);
                $inputs_customize = $this->fetchCustomize($id_positions, $data_position, $this->fields_value);
                $inputs = !empty($inputs_customize) ? array_merge($inputs, $inputs_customize) : $inputs;
                $this->fields_value['customize'] = isset($params->customize) ? $params->customize : '0';
            }else{
                $this->fields_value['customize'] = '0';
            }
        }else{
            $this->fields_value['customize'] = '0';
        }

        $inputs_header = array(
            array(
                'type' => 'tabConfig',
                'name' => 'title',
                'values' => $tabs,
                'default' => Tools::getValue('tab_open') ? Tools::getValue('tab_open') : 'tab_general',
                'save' => false,
            )
        );
        
        $inputs = array_merge($inputs_header, $inputs);

        $this->fields_form = array(
            'input' => $inputs,
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
                )
            )
        );

        $default_breadcrumb = array(
            'breadcrumb_image' => '0',
            'breadcrumb_image_fullwidth' => '0',
            'breadcrumb_category_image' => '0',
        );
        $params_breadcrumb = isset($params->breadcrumb) ? $params->breadcrumb : false;
        foreach ($default_breadcrumb as $key => $value) {
            if ($params_breadcrumb){
                $this->fields_value[$key] = isset($params_breadcrumb->{$key}) ? $params_breadcrumb->{$key} : $value;
            }else{
                $this->fields_value[$key] = $value;
            }
        }
        
        $default_ajax_cart = array(
            'enable_dropdown_defaultcart' => '1',
            'type_dropdown_defaultcart' => 'dropdown',
            'enable_dropdown_flycart' => '1',
            'type_dropdown_flycart' => 'slidebar_bottom',
            'enable_overlay_background_flycart' => '1',
            'show_popup_after_add_to_cart' => '0',
            'open_advance_cart_after_add_to_cart' => '1',
            'position_vertical_flycart' => 'bottom',
            'position_vertical_value_flycart' => '10',
            'position_horizontal_flycart' => 'left',
            'position_horizontal_value_flycart' => '10',
        );
        $params_ajax_cart = isset($params->ajax_cart) ? $params->ajax_cart : false;
        foreach ($default_ajax_cart as $key => $value) {
            if ($params_ajax_cart){
                $this->fields_value[$key] = isset($params_ajax_cart->{$key}) ? $params_ajax_cart->{$key} : $value;
            }else{
                $this->fields_value[$key] = $value;
            }
        }

        $default_mobile_mode = array(
            'header_mobile' => '0',
            'nav_mobile' => '0',
            'content_mobile' => '0',
            'footer_mobile' => '0',
        );
        $params_mobile_mode = isset($params->mobile_mode) ? $params->mobile_mode : false;
        foreach ($default_mobile_mode as $key => $value) {
            if ($default_mobile_mode){
                $this->fields_value[$key] = isset($params_mobile_mode->{$key}) ? $params_mobile_mode->{$key} : $value;
            }else{
                $this->fields_value[$key] = $value;
            }
        }

        $params_fullwidth_content_other_page = isset($params->fullwidth_content_other_page) ? $params->fullwidth_content_other_page : false;
        $fullwidth_content_other_page = self::getCheckboxOtherPages();
        foreach ($fullwidth_content_other_page as $value) {
            $this->fields_value['fullwidth_content_other_page_'.$value['id']] = (isset($params_fullwidth_content_other_page->{$value['id']})) ? $params_fullwidth_content_other_page->{$value['id']} : 0;
        }

        $values_index_hook = $this->getValueIndexHook();
        $values_other_hook = $this->getValueOtherHook();
        $values_disable_cache_hook = $this->getValueDisableCacheHook();
        foreach ($values_index_hook as $key => $value) {
            $this->fields_value['fullwidth_index_hook_'.$key] = $value;
        }
        foreach ($values_other_hook as $key => $value) {
            $this->fields_value['fullwidth_other_hook_'.$key] = $value;
        }
        foreach ($values_disable_cache_hook as $key => $value) {
            $this->fields_value['disable_cache_hook_'.$key] = $value;
        }
        // foreach ($groups as $group) {
        //     $this->fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], in_array($group['id_group'], explode(',', $this->object->group_box)));
        // }

        return parent::renderForm();
    }

    public function getCheckboxOtherPages()
    {
        $controllers_core = array();
        $controllers_modules = array();
        $arr_controllers_core = array();
        $arr_controllers_modules = array();

        $other_pages = DeoSetting::getPages();
        foreach ($other_pages as $page) {
            if ($page == 'index') continue;
            $arr_controllers_core[] = array(
                'id' => $page,
                'name' => $page,
                'val' => '1'
            );
        }



        if (DeoHelper::getConfig('CACHE_ADMIN_MODULE_EXCEPTION') === false) {
            # First Time : write to config
            $controllers_modules = Dispatcher::getModuleControllers('front');
            DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_ADMIN_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules)));
        } else {
            # Second Time : read from config
            $controllers_modules = json_decode(DeoHelper::correctDeCodeData(DeoHelper::getConfig('CACHE_ADMIN_MODULE_EXCEPTION')), true);
        }

        foreach ($controllers_modules as $module => $controllers) {
            foreach ($controllers as $controller) {
                $arr_controllers_modules[] = array(
                    'id' => "module-".$module."-".$controller,
                    'name' => "module-".$module."-".$controller,
                    'val' => '1'
                );
            }
        }


        return array_merge($arr_controllers_core, $arr_controllers_modules);       
    }


    public function fetchCustomize($id_positions, $data, &$fields_value)
    {
        $result = array();
        foreach ($data as $key => $type) {
            $uri_file = $this->position_customize_setting_folder.$key.$type.'.json';
            if (file_exists($uri_file)) {
                $default = $inputs = array();
                $settings = json_decode(Tools::file_get_contents($uri_file));
                if ($settings){
                    $name_group = '';
                    switch ($key) {
                        case 'content':
                            $name_group = $this->l('General');
                            break;
                        case 'header':
                            $name_group = $this->l('Header');
                            break;
                        case 'footer':
                            $name_group = $this->l('Footer');
                            break;
                    }

                    $inputs[] =  array(
                        'type' => 'html',
                        'name' => 'default_html',
                        'html_content' => '<div class="alert alert-info"><b>'.$name_group.'</b> <a href="javascript:void(0)" class="reset-customize pull-right" id="'.$key.$type.'">'.$this->l('Reset to default').'</a><p class="help-block">You do not want to use same settings customize color with other homepage builder please <a href="'. Context::getContext()->link->getAdminLink('AdminDeoPositions').'&updatedeotemplate_positions&id_deotemplate_positions='.$id_positions[$key].'">Change Position Key</a></p></div>',
                        'form_group_class' => 'tab_customize',
                    );

                    foreach ($settings as $input) {
                        $inputs[] = array(
                            'required' => isset($input->required) ? $input->required : false,
                            'name'     => $input->id,
                            'desc'     => isset($input->desc) ? $input->desc : '',
                            'type'     => ($input->type == 'color' || $input->type == 'background-color' || $input->type == 'border-color') ? 'color' : 'text',
                            'label'    => isset($input->label) ? $input->label : '',
                            'form_group_class' => 'tab_customize',
                        );

                        $fields_value[$input->id] = isset($input->value) ? $input->value : '';
                        $default[$input->id] = isset($input->default) ? $input->default : '';
                    }

                    $inputs[] = array(
                        'type' => 'hidden', 
                        'name' => $key.$type
                    );
                    $fields_value[$key.$type] = json_encode($default);

                    $result = array_merge($result, $inputs);
                }
            }
        }

        return $result;
    }


    /**
     * Read file css + js to form when add/edit
     */
    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);
        if ($obj->id && $obj->profile_key) {
            $file_value['css'] = Tools::file_get_contents($this->profile_css_folder.$obj->profile_key.'.css');
            $file_value['js'] = Tools::file_get_contents($this->profile_js_folder.$obj->profile_key.'.js');
        } else {
            $file_value['profile_key'] = 'profile'.DeoSetting::getRandomNumber();
        }
        return $file_value;
    }

    public function processAdd()
    {
        parent::validateRules();
        if (count($this->errors)) {
            return false;
        }
        if ($this->object = parent::processAdd()) {
            $this->saveCustomJsAndCss($this->object->profile_key, '');
        }
        $this->processParams();
        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoHome');
            $this->redirect_after .= '&id_deotemplate_profiles='.($this->object->id);
            $this->redirect();
        }
    }

    public function processUpdate()
    {
        parent::validateRules();
        if (count($this->errors)) {
            return false;
        }
        if ($this->object = parent::processUpdate()) {
            $this->saveCustomJsAndCss($this->object->profile_key, $this->object->profile_key);
        }

        $this->processParams();
        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoHome');
            $this->redirect_after .= '&id_deotemplate_profiles='.($this->object->id);
            $this->redirect();
        }else{
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminDeoProfiles');
            $this->redirect_after .= '&id_deotemplate_profiles='.($this->object->id).'&updatedeotemplate_profiles';
            $this->redirect_after .= Tools::getValue('tab_open') ? '&tab_open='.Tools::getValue('tab_open') : '';
            $this->redirect();
        }
    }

    /**
     * Get fullwidth hook, save to params
     */
    public function processParams()
    {
        $params = json_decode($this->object->params);
        if ($params === null) {
            $params = new stdClass();
        }

        $breadcrumb['breadcrumb_image'] = Tools::getValue('breadcrumb_image');
        $breadcrumb['breadcrumb_image_fullwidth'] = Tools::getValue('breadcrumb_image_fullwidth');
        $breadcrumb['breadcrumb_category_image'] = Tools::getValue('breadcrumb_category_image');
        $params->breadcrumb = $breadcrumb;

        # get post ajax cart
        $ajax_cart = array();
        $ajax_cart['enable_dropdown_defaultcart'] = Tools::getValue('enable_dropdown_defaultcart');
        $ajax_cart['type_dropdown_defaultcart'] = Tools::getValue('type_dropdown_defaultcart');
        $ajax_cart['enable_dropdown_flycart'] = Tools::getValue('enable_dropdown_flycart');
        $ajax_cart['type_dropdown_flycart'] = Tools::getValue('type_dropdown_flycart');
        $ajax_cart['enable_overlay_background_flycart'] = Tools::getValue('enable_overlay_background_flycart');
        $ajax_cart['show_popup_after_add_to_cart'] = Tools::getValue('show_popup_after_add_to_cart');
        $ajax_cart['open_advance_cart_after_add_to_cart'] = Tools::getValue('open_advance_cart_after_add_to_cart');
        $ajax_cart['position_vertical_flycart'] = Tools::getValue('position_vertical_flycart');
        $ajax_cart['position_vertical_value_flycart'] = Tools::getValue('position_vertical_value_flycart');
        $ajax_cart['position_horizontal_flycart'] = Tools::getValue('position_horizontal_flycart');
        $ajax_cart['position_horizontal_value_flycart'] = Tools::getValue('position_horizontal_value_flycart');
        $params->ajax_cart = $ajax_cart;

        # get post edit tool
        $params->customize = Tools::getValue('customize');

        # get post mobile
        $mobile_mode = array();
        $mobile_mode['header_mobile'] = Tools::getValue('header_mobile');
        $mobile_mode['nav_mobile'] = Tools::getValue('nav_mobile');
        $mobile_mode['content_mobile'] = Tools::getValue('content_mobile');
        $mobile_mode['footer_mobile'] = Tools::getValue('footer_mobile');
        $params->mobile_mode = $mobile_mode;

        # get post index hook
        $index_hook = DeoSetting::getIndexHook();
        $post_index_hooks = array();
        foreach ($index_hook as $key => $value) {
            // validate module
            $post_index_hooks[$value] = Tools::getValue('fullwidth_index_hook_'.$value) ? Tools::getValue('fullwidth_index_hook_'.$value) : DeoSetting::HOOK_BOXED;
            // validate module
            unset($key);
        }
        $params->fullwidth_index_hook = $post_index_hooks;

        # get post other hook
        $other_hook = DeoSetting::getOtherHook();
        $post_other_hooks = array();
        foreach ($other_hook as $key => $value) {
            // validate module
            $post_other_hooks[$value] = Tools::getValue('fullwidth_other_hook_'.$value) ? Tools::getValue('fullwidth_other_hook_'.$value) : DeoSetting::HOOK_BOXED;
            // validate module
            unset($key);
        }
        $params->fullwidth_other_hook = $post_other_hooks;

        # get post fullwidth content other hook
        $fullwidth_content_other_page = self::getCheckboxOtherPages();
        $post_fullwidth_content_other_page = array();
        foreach ($fullwidth_content_other_page as $value) {
            $post_fullwidth_content_other_page[$value['id']] = Tools::getValue('fullwidth_content_other_page_'.$value['id']) ? Tools::getValue('fullwidth_content_other_page_'.$value['id']) : 0;
        }
        $params->fullwidth_content_other_page = $post_fullwidth_content_other_page;


        
        
        # get post disable hook
        $cache_hooks = DeoSetting::getCacheHook();
        $post_disable_hooks = array();
        foreach ($cache_hooks as $key => $value) {
            // validate module
            $post_disable_hooks[$value] = Tools::getValue('disable_cache_hook_'.$value) ? Tools::getValue('disable_cache_hook_'.$value) : DeoSetting::HOOK_BOXED;
            // validate module
            unset($key);
        }
        $params->disable_cache_hook = $post_disable_hooks;
        

        # Save to params
        $this->object->params = json_encode($params);

        
        # Save group_box
        // if (Tools::getValue('groupBox')) {
        //     $this->object->group_box = implode(',', Tools::getValue('groupBox'));
        // } else {
        //     $this->object->group_box = '';
        // }
        
        $this->object->save();

        if (Tools::getValue('key_header')){
            $this->processCustomize('header', Tools::getValue('key_header'));
        }

        if (Tools::getValue('key_content')){
            $this->processCustomize('content', Tools::getValue('key_content'));
        }

        if (Tools::getValue('key_footer')){
            $this->processCustomize('footer', Tools::getValue('key_footer'));
        }
    }


    public function processCustomize($type, $key)
    {
        $uri_file = $this->position_customize_setting_folder.$type.$key.'.json';
        if (file_exists($this->position_customize_setting_folder.$type.$key.'.json')){
            $settings = json_decode(Tools::file_get_contents($uri_file));
            if (!count($settings)){
                return false;
            }

            $css = '';
            foreach ($settings as &$field) {
                $value = Tools::getValue($field->id);
                if ($value == '') {
                    unset($field->value);
                    continue;
                }
                $field->value = $value;

                $css_responsive = '';
                if (!empty($field->responsive)){
                    foreach ($field->responsive as $breakpoint) {
                        $css_responsive .= $breakpoint->media."{\r\t";
                            $css_responsive .= $breakpoint->selector."{\r\t\t";
                                if ($field->type == 'background-image') {
                                    $css_responsive .= $field->type.': url('.$field->value.')';
                                } elseif ($field->type == 'font-size') {
                                    $css_responsive .= $field->type.': '.$field->value;
                                } else if (Tools::strpos($field->type, 'color') !== false) {
                                    $css_responsive .= $field->type.': '.$field->value;
                                } else if ($field->type == 'color' || $field->type == 'background-color' || $field->type == 'border-color') {
                                    $css_responsive .= $field->type.': '.$field->value;
                                } else {
                                    $css_responsive .= $field->type.': '.$field->value;
                                }
                                $css_responsive .= ";\r";
                                if (isset($field->special) && $field->special){
                                    $css_responsive .= "\t".$field->special."\r";
                                }
                            $css_responsive .= "\t}\r\n";
                        $css_responsive .= "}\r\n";
                    }
                }

                $css .= (isset($field->media) && $field->media) ? $field->media."{\r" : '';
                    $css .= $field->selector."{\r\t";
                        if ($field->type == 'background-image') {
                            $css .= $field->type.': url('.$field->value.')';
                        } elseif ($field->type == 'font-size') {
                            $css .= $field->type.': '.$field->value;
                        } else if (Tools::strpos($field->type, 'color') !== false) {
                            $css .= $field->type.': '.$field->value;
                        } else if ($field->type == 'color' || $field->type == 'background-color' || $field->type == 'border-color') {
                            $css .= $field->type.': '.$field->value;
                        } else {
                            $css .= $field->type.': '.$field->value;
                        }
                        $css .= ";\r";
                        if (isset($field->special) && $field->special){
                            $css .= "\t".$field->special."\r";
                        }
                    $css .= "}\r\n";
                $css .= (isset($field->media) && $field->media) ? "}\r\n" : '';
                $css .= $css_responsive;
            }

            DeoSetting::writeFile($this->position_customize_css_folder, $type.$key.'.css', $css);
            
            $file_content = json_encode($settings, JSON_PRETTY_PRINT);
            if ($file_content){
                DeoSetting::writeFile($this->position_customize_setting_folder, $type.$key.'.json', $file_content);
            }
        }
    }


    public function saveCustomJsAndCss($key, $old_key = '')
    {
        if (Tools::getIsset('js')) {
            if ($old_key) {
                # DELETE OLD FILE
                Tools::deleteFile($this->profile_js_folder.$old_key.'.js');
            }
            DeoSetting::writeFile($this->profile_js_folder, $key.'.js', Tools::getValue('js'));
        }
        
        if (Tools::getIsset('css')) {
            if ($old_key) {
                # DELETE OLD FILE
                Tools::deleteFile($this->profile_css_folder.$old_key.'.css');
            }
            # FIX CUSTOMER CAN NOT TYPE "\"
            $temp = Tools::getAllValues();
            $css = $temp['css'];
            DeoSetting::writeFile($this->profile_css_folder, $key.'.css', $css);
        }
    }

    public static function getCheckboxIndexHook()
    {
        $ids = DeoSetting::getIndexHook();
        $names = DeoSetting::getIndexHook();
        return DeoHelper::getArrayOptions($ids, $names);
    }

    public static function getCheckboxOtherHook()
    {
        $ids = DeoSetting::getOtherHook();
        $names = DeoSetting::getOtherHook();
        return DeoHelper::getArrayOptions($ids, $names);
    }

    public static function getCheckboxCacheHook()
    {
        $ids = DeoSetting::getCacheHook();
        $names = DeoSetting::getCacheHook();
        return DeoHelper::getArrayOptions($ids, $names);
    }

    public static function getCheckboxFullwidthOtherPages()
    {

        return  array(
            array(
                'id' => 'manufacture',
                'val' => '0'
            )
        );
    }

    /**
     * Get fullwidth hook from database or default
     */
    public function getValueIndexHook()
    {
        $params = json_decode($this->object->params);
        return isset($params->fullwidth_index_hook) ? $params->fullwidth_index_hook : DeoSetting::getIndexHook(3);
    }

    /**
     * Get fullwidth hook from database or default
     */
    public function getValueOtherHook()
    {
        $params = json_decode($this->object->params);
        return isset($params->fullwidth_other_hook) ? $params->fullwidth_other_hook : DeoSetting::getOtherHook(3);
    }

    /**
     * Get fullwidth hook from database or default
     */
    public function getValueDisableCacheHook()
    {
        $params = json_decode($this->object->params);
        return isset($params->disable_cache_hook) ? $params->disable_cache_hook : DeoSetting::getCacheHook(3);
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
            }
        }
    }
}
