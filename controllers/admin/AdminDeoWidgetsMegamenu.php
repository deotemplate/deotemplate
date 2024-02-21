<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperMegamenu.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoWidgetBaseModel.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoWidgetModel.php');

class AdminDeoWidgetsMegamenuController extends ModuleAdminControllerCore
{
    public $widget;
    public $base_config_url;
    private $_imageField = array('htmlcontent', 'content', 'information');
    private $_langField = array('widget_title', 'text_link', 'htmlcontent', 'header', 'content', 'information');
    private $_theme_dir = '';
    
    public function __construct()
    {
        $this->widget = new DeoWidgetModel();
        $this->className = 'DeoWidgetModel';
        $this->bootstrap = true;
        $this->table = 'deomegamenu_widgets';
        $this->name = 'deotemplate';
        $this->ajax = (Tools::getValue('ajax') || Tools::isSubmit('ajax')) ? true : false;
        
        parent::__construct();
        
        $this->fields_list = array(
            'id_deomegamenu_widgets' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 50,
                'class' => 'fixed-width-xs'
            ),
            'key_widget' => array(
                'title' => $this->l('Widget Key'),
                'filter_key' => 'a!key_widget',
                'type' => 'text',
                'width' => 140,
            ),
            'name' => array(
                'title' => $this->l('Widget Name'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!name'
            ),
            'type' => array(
                'title' => $this->l('Widget Type'),
                'width' => 50,
                'type' => 'text',
                'filter_key' => 'a!type'
            ),
        );
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
            ),
        );
        $this->_where = ' AND id_shop='.(int)($this->context->shop->id);
        // $this->_theme_dir = Context::getContext()->shop->getTheme();
        $this->_theme_dir = Context::getContext()->shop->theme->getName();
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['group'] = array(
            'short' => 'GroupsMenu',
            'href' => $this->context->link->getAdminLink('AdminDeoMegamenu'),
            'target' => '_blank',
            'desc' => $this->l('Groups Menu'),
            'icon' => 'icon-sitemap'
        );
        $this->page_header_toolbar_btn['export_widgets'] = array(
            'short' => 'ExportWidgetsMenu',
            'href' => $this->context->link->getAdminLink('AdminDeoWidgetsMegamenu').'&exportwidgets=1',
            'desc' => $this->l('Export Widgets'),
            'icon' => 'icon-cloud-download'
        );
        $this->page_header_toolbar_btn['import_widgets'] = array(
            'short' => 'ImportWidgetsMenu',
            'href' => 'javascript:void(0)',
            'desc' => $this->l('Import Widgets'),
            'icon' => 'icon-cloud-upload'
        );
        $this->page_header_toolbar_btn['widgets'] = array(
            'short' => 'Widgets',
            'href' => $this->context->link->getAdminLink('AdminDeoWidgetsMegamenu'),
            'target' => '_blank',
            'desc' => $this->l('Widgets'),
            'icon' => 'icon-list-alt'
        );
        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $tpl = $this->createTemplate('modal_import_widgets.tpl');

        return parent::renderList().$tpl->fetch();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }
        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        return $this->showWidgetsSetting();
    }

    public function showWidgetsSetting()
    {
        $media_dir = DeoHelper::getMediaDir();
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/general.css');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/jquery-validation-1.9.0/jquery.validate.js');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/megamenu/admin/jquery-validation-1.9.0/screen.css');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/megamenu/admin/admin.css');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/megamenu/admin/show.js');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');

        // image select
        $bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
            $bo_theme = 'default';
        }
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        

        $tpl = $this->createTemplate('widget.tpl');
        $form = '';
        $widget_selected = '';
        $id = (int)Tools::getValue('id_deomegamenu_widgets');
        $key = (int)Tools::getValue('key');
        if (Tools::getValue('id_deomegamenu_widgets')) {
            $model = new DeoWidgetModel((int)Tools::getValue('id_deomegamenu_widgets'));
        } else {
            $model = $this->widget;
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

    public function postProcess()
    {
        if ((Tools::isSubmit('savedeowidget') || Tools::isSubmit('saveandstaydeowidget')) && Tools::isSubmit('widgets')) {
            parent::validateRules();
            if (count($this->errors)) {
                $this->display = 'edit';
                return false;
            }
            if (!Tools::getValue('widget_name')) {
                $this->errors[] = Tools::displayError('Widget Name Empty !');
            }
            if (!count($this->errors)) {
                if (Tools::getValue('id_deomegamenu_widgets')) {
                    $model = new DeoWidgetModel((int)Tools::getValue('id_deomegamenu_widgets'));
                } else {
                    $model = $this->widget;
                }
                $model->loadEngines();
                $model->id_shop = Context::getContext()->shop->id;
                // $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
                $languages = Language::getLanguages(false);

                $tmp = array();


                # GET POST - BEGIN
                //validate module
                $widget_type = Tools::getValue('widget_type');
                $validate_class = str_replace('_', '', $widget_type);
                $file_name = _PS_MODULE_DIR_.'deotemplate/classes/Megamenu/widget/'.$widget_type.'.php';
                require_once($file_name);
                $class_name = 'DeoWidget'.Tools::ucfirst($validate_class);
                $widget = new $class_name;
                $keys = array('adddeomegamenu_widgets', 'id_deomegamenu_widgets', 'widget_name', 'widget_type', 'accordion_type' , 'class', 'saveandstaydeowidget', 'icon_rate_image', 'icon_image_link', 'icon_image', 'icon_use_image_link', 'icon_lazyload');
                $post = DeoMegamenuHelper::getPostAdmin($keys, 0);
                $keys = array('widget_title', 'link_title');
                $post += DeoMegamenuHelper::getPostAdmin($keys, 1);
                if ($widget_type == 'links') {
                    $keys = array('list_id_link', 'list_field', 'list_field_lang');
                    $post += DeoMegamenuHelper::getPostAdmin($keys, 0);
                }
                if ($widget_type == 'links') {
                    $keys = array_filter(explode(",", Tools::getValue('list_field')));
                } else {
                    $keys = $widget->getConfigKey(0);
                }
                $post += DeoMegamenuHelper::getPostAdmin($keys, 0);
                if ($widget_type == 'links') {
                    $keys = array_filter(explode(",", Tools::getValue('list_field_lang')));
                } else {
                    $keys = $widget->getConfigKey(1);
                }
                
                $post += DeoMegamenuHelper::getPostAdmin($keys, 1);
                $keys = $widget->getConfigKey(2);
                $post += DeoMegamenuHelper::getPostAdmin($keys, 2);


                # GET POST - END

                // auto create folder if not exists
                if ($widget_type == 'image_gallery') {
                    if ($post['image_folder_path'] != '') {
                        $path = _PS_ROOT_DIR_.'/'.trim($post['image_folder_path']).'/';
            
                        $path = str_replace('//', '/', $path);
                        
                        if (!file_exists($path)) {
                            $success = @mkdir($path, 0775, true);
                            $chmod = @chmod($path, 0775);
                            if (($success || $chmod) && !file_exists($path.'index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
                                @copy(_PS_IMG_DIR_.'index.php', $path.'index.php');
                            }
                        }
                    }
                }
                
                foreach ($post as $key => $value) {
                    $tmp[$key] = str_replace(array('\'', '\"'), array("'", '"'), $value);
                    foreach ($this->_langField as $fVal) {
                        if (Tools::strpos($key, $fVal) !== false) {
                            foreach ($languages as $language) {
                                if (!Tools::getValue($fVal.'_'.$language['id_lang'])) {
                                    $tmp[$fVal.'_'.$language['id_lang']] = $value;
                                }
                            }
                        }
                    }
                }

                $data = array(
                    'id' => Tools::getValue('id_deomegamenu_widgets'),
                    'params' => call_user_func('base64'.'_encode', json_encode($tmp)),
                    'type' => Tools::getValue('widget_type'),
                    'name' => Tools::getValue('widget_name')
                );

                foreach ($data as $k => $v) {
                    $model->{$k} = $v;
                }

                if ($model->id) {
                    if (!$model->update()) {
                        $this->errors[] = Tools::displayError('Can not update new widget');
                    } else {
                        if ($this->ajax) {
                            $id_widget = Tools::getValue('id_widget');
                            $id_shop = $this->context->shop->id;
                            $model->setTheme(Context::getContext()->shop->theme->getName());
                            $model->langID = $this->context->language->id;
                            $model->loadWidgets($id_shop);
                            $model->loadEngines();

                            $data = $model->getWidetByKey($id_widget,$id_shop);
                            $content = $model->renderContent($id_widget);

                            // $widgets = $model->getWidgets($id_shop);
                            $content['data'] = array_merge($content['data'], array('backoffice' => true));
                            $this->context->smarty->assign($content['data']);
                            $this->context->smarty->assign('id_widget', $id_widget);
                            // $this->context->smarty->assign('widgets',$widgets);
                            $data['html']  = $this->context->smarty->fetch(_PS_MODULE_DIR_.'deotemplate/views/templates/hook/megamenu/widgets/widget_'.$content['type'].'.tpl');
    
                            die(json_encode($data));
                        }else{
                            if (Tools::isSubmit('saveandstaydeowidget')) {
                                $this->confirmations[] = $this->l('Update successful');
                                Tools::redirectAdmin(self::$currentIndex.'&id_deomegamenu_widgets='.$model->id.'&updatebtmegamenu_widgets&token='.$this->token.'&updatedeomegamenu_widgets=1&conf=4');
                            } else {
                                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
                            }
                        }
                    }
                } else {
                    $key_widget = time();
                    $model->key_widget = $key_widget;
                    if (!$model->add()) {
                        $this->errors[] = Tools::displayError('Can not add new widget');
                    } else {
                        if ($this->ajax) {
                            $id_widget = $key_widget;
                            $id_shop = $this->context->shop->id;
                            $model->setTheme(Context::getContext()->shop->theme->getName());
                            $model->langID = $this->context->language->id;
                            $model->loadWidgets($id_shop);
                            $model->loadEngines();

                            $data = $model->getWidetByKey($id_widget,$id_shop);
                            $content = $model->renderContent($id_widget);

                            // $widgets = $model->getWidgets($id_shop);
                            $content['data'] = array_merge($content['data'], array('backoffice' => true));
                            $this->context->smarty->assign($content['data']);
                            $this->context->smarty->assign('id_widget', $id_widget);
                            // $this->context->smarty->assign('widgets',$widgets);
                            $data['html']  = $this->context->smarty->fetch(_PS_MODULE_DIR_.'deotemplate/views/templates/hook/megamenu/widgets/widget_'.$content['type'].'.tpl');
    
                            die(json_encode($data));
                        }else{
                            if (Tools::isSubmit('saveandstaydeowidget')) {
                                $this->confirmations[] = $this->l('Update successful');
                                Tools::redirectAdmin(self::$currentIndex.'&id_deomegamenu_widgets='.$model->id.'&updatebtmegamenu_widgets&token='.$this->token.'&conf=4');
                            } else {
                                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
                            }
                        }
                    }
                }
            }
        }


        if (Tools::isSubmit('exportwidgets')) {
            $this->exportWidgets();
        }
        if (Tools::isSubmit('importwidgets')) {
            if ($this->importWidgets()) {
                $this->displayInformation($this->l('Import widgets is successful'));
                // Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminDeoWidgetsMegamenu').'&success=importwidgets');
            } else {
                $this->html .= $this->displayError($this->l('The file could not be import.'));
            }
        }

        if (Tools::isSubmit('load_form_widget')) {
            $tpl = $this->createTemplate('widget.tpl');
            $form = '';
            $widget_selected = '';
            $id = (int)Tools::getValue('id_deomegamenu_widgets');
            $key = (int)Tools::getValue('key');
            if (Tools::getValue('id_deomegamenu_widgets')) {
                $model = new DeoWidgetModel((int)Tools::getValue('id_deomegamenu_widgets'));
            } else {
                $model = $this->widget;
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

            die(json_encode($form));
        }
       
        parent::postProcess();
    }

    private function exportWidgets()
    {
        // export widgets process
        if (Tools::getValue('exportwidgets')) {
            $languages = Language::getLanguages();
            $model = new DeoWidgetModel();
            $widget_shop = $model->getWidgets();
            
            $widgets = array();
            if (count($widget_shop) > 0) {
                foreach ($widget_shop as $widget_shop_item) {
                    $params_widget = DeoMegamenuHelper::base64Decode($widget_shop_item['params']);
                    foreach ($languages as $lang) {
                        # module validation
                        if (Tools::strpos($params_widget, '_'.$lang['id_lang'].'"') !== false) {
                            $params_widget = str_replace('_'.$lang['id_lang'].'"', '_'.$lang['iso_code'].'"', $params_widget);
                        }
                    }
                    
                    $widget_shop_item['params'] = DeoMegamenuHelper::base64Encode($params_widget);
                    $widgets[] = $widget_shop_item;
                }
            }
            
            header('Content-Type: plain/text');
            header('Content-Disposition: Attachment; filename=export_widgets_'.time().'.txt');
            header('Pragma: no-cache');
            die(DeoMegamenuHelper::base64Encode(json_encode($widgets)));
        }
    }

    public function importWidgets()
    {
        $this->renderGroupConfig = true;
        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['import_widgets_file']['name'], '.'), 1));
        if (isset($_FILES['import_widgets_file']) && $type == 'txt' && isset($_FILES['import_widgets_file']['tmp_name']) && !empty($_FILES['import_widgets_file']['tmp_name'])) {
            $content = Tools::file_get_contents($_FILES['import_widgets_file']['tmp_name']);
            $content = json_decode(DeoMegamenuHelper::base64Decode($content), true);
            // $override_import_widgets = Tools::getValue('override_import_widgets');
            $override_import_widgets = true;
            $shop_id = $this->context->shop->id;
            if (count($content) > 0) {
                if (!$this->processImportWidgets($content, $override_import_widgets, $shop_id)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    
    public function processImportWidgets($list_widget, $override, $shop_id)
    {
        $languages = Language::getLanguages();
        
        if (!is_array($list_widget) || !isset($list_widget[0]['id_deomegamenu_widgets']) || $list_widget[0]['id_deomegamenu_widgets'] == '') {
            return false;
        }
        foreach ($list_widget as $widget) {
            $check_widget_exists = DeoWidgetModel::getWidetByKey($widget['key_widget'], $shop_id);
            if ($check_widget_exists['id'] != '' && $override) {
                $mod_widget = new DeoWidgetModel($check_widget_exists['id']);
            }
            if (($override && $check_widget_exists['id'] == '') || (!$override && $check_widget_exists['id'] == '')) {
                $mod_widget = new DeoWidgetModel();
            }
            $mod_widget->name = $widget['name'];
            $mod_widget->type = $widget['type'];
            $params_widget = DeoMegamenuHelper::base64Decode($widget['params']);
            
            foreach ($languages as $lang) {
                # module validation
                if (Tools::strpos($params_widget, '_'.$lang['iso_code'].'"') !== false) {
                    $params_widget = str_replace('_'.$lang['iso_code'].'"', '_'.$lang['id_lang'].'"', $params_widget);
                }
            }
            $mod_widget->params = DeoMegamenuHelper::base64Encode($params_widget);
            
            if ($check_widget_exists['id'] != '' && $override) {
                if (!$mod_widget->save()) {
                    return false;
                }
            }
            
            if (($override && $check_widget_exists['id'] == '') || (!$override && $check_widget_exists['id'] == '')) {
                $mod_widget->key_widget = $widget['key_widget'];
                $mod_widget->id_shop = $shop_id;
                $mod_widget->id = 0;
            
                if (!$mod_widget->add()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     * classes\controller\AdminController.php
     */
    public function getTabSlug()
    {
        if (empty($this->tabSlug)) {
            // GET RULE FOLLOW AdminDeoWidgetsMegamenu
            $result = Db::getInstance()->getRow('
                SELECT `id_tab`
                FROM `'._DB_PREFIX_.'tab`
                WHERE UCASE(`class_name`) = "'.'AdminDeoWidgetsMegamenu'.'"
            ');
            $profile_id = $result['id_tab'];
            $this->tabSlug = Access::findSlugByIdTab($profile_id);
        }

        return $this->tabSlug;
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        
        if (count($this->errors) <= 0) {
            $id = (int)Tools::getValue('id_deomegamenu_widgets');
            if ($id) {
                if (!$this->access('edit')) {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
