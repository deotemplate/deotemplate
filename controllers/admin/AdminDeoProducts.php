<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */



require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProductsModel.php');

class AdminDeoProductsController extends ModuleAdminControllerCore
{
    private $theme_name = '';
    public $module_name = 'deotemplate';
    public $tpl_save = '';
    public $file_content = array();
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $product_lists_css_folder;
    public $module_path;
    // public $module_path_resource;
    public $str_search = array();
    public $str_relace = array();
    public $theme_dir;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'deotemplate_products';
        $this->className = 'DeoTemplateProductsModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'deotemplate_products_shop` ps ON (ps.`id_deotemplate_products` = a.`id_deotemplate_products`)';
        $this->_select .= ' ps.active as active, ';

        $this->order_by = 'id_deotemplate_products';
        $this->order_way = 'DESC';
        parent::__construct();
        $this->fields_list = array(
            'id_deotemplate_products' => array(
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
            'class' => array(
                'title' => $this->l('Class'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!class',
                'orderby' => false
            ),
            'demo' => array(
                'title' => $this->l('Is Demo'),
                'active' => 'status',
                'filter_key' => 'a!demo',
                'align' => 'text-center',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
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
        $this->product_lists_css_folder = DeoHelper::getThemeDir().DeoHelper::getCssDir().'products/';
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
        // $this->module_path_resource = $this->module_path.'views/';
        $this->str_search = array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APOCBRACKET_', '_APCCBRACKET_');
        $this->str_relace = array('&', '\"', '\'', '\t', '\r', '\n', '[', ']', '{', '}');

        if (!is_dir($this->product_lists_css_folder)) {
            @mkdir($this->product_lists_css_folder, 0755, true);
        }

        if (!(int) DeoHelper::getConfig('DEBUG_MODE')){
            unset($this->fields_list['demo']);
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
                    Media::addJsDef(array('record_id' => 'deotemplate_productsBox[]'));
                }
        }
    }
    
    /**
     * OVERRIDE CORE
     */
    public function processExport($text_delimiter = '"')
    {
        $multilang = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
            $multilang = $definition['multilang'];
        }

        $record_id = Tools::getValue('record_id');
        $file_name = 'product_list_all.xml';
        # VALIDATE MODULE
        unset($text_delimiter);
        
        if($record_id){
            $record_id_str = implode(", ", $record_id);
            $this->_where = ' AND a.'.$this->identifier.' IN ( '.pSQL($record_id_str).' )';
            $file_name = 'product_list.xml';
        }

        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        $data = $this->_list;
        
        $data_all = array();
        foreach (Language::getLanguages() as $key => $lang) {
            $this->getList($lang['id_lang'], null, null, 0, false);
            $data_all[$lang['iso_code']] = $this->_list;
        }
        
        $this->file_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->file_content .= '<data>' . "\n";
        $this->file_content .= '<product_list>' . "\n";
        
        if ($data) {
            foreach ($data as $product_detail) {
                $this->file_content .= '<record>' . "\n";
                foreach ($product_detail as $key => $value) {
                    if(isset($definition['fields'][$key]['lang']) && $definition['fields'][$key]['lang'])
                    {
                        # MULTI LANG
                        $this->file_content .= '    <'.$key.'>'. "\n";
                        foreach (Language::getLanguages() as $key_lang => $lang) {
                            $this->file_content .= '        <'.$lang['iso_code'].'>';
                            $this->file_content .= '<![CDATA['.$value.']]>';
                            $this->file_content .= '</'.$lang['iso_code'].'>' . "\n";
                        }
                        $this->file_content .= '    </'.$key.'>' . "\n";
                    }else{
                        # SINGLE LANG
                        $this->file_content .= '    <'.$key.'>';
                        $this->file_content .= '<![CDATA['.$value.']]>';
                        $this->file_content .= '</'.$key.'>' . "\n";
                    }
                }
                $this->file_content .= '</record>' . "\n";
            }
        }
        $this->file_content .= '</product_list>' . "\n";
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
        
        if (isset($files_content->product_list) && $files_content->product_list)
        {
            foreach ($files_content->product_list->children() as $product_details) {
                if ($override) {
                    
                }else{
                    $obj_model = new DeoTemplateProductsModel();
                    $obj_model->plist_key = 'plist'.DeoSetting::getRandomNumber();
                    $obj_model->name = $product_details->name->__toString();
                    $obj_model->class = $product_details->class->__toString();
                    $obj_model->params = $product_details->params->__toString();
                    $obj_model->demo = $product_details->type->__toString();
                    $obj_model->responsive = $product_details->type->__toString();
                    $obj_model->active = 0;

                    if ($obj_model->save()) {
                        $this->saveTplFile($obj_model->plist_key, $obj_model->params);
                    }
                }
            }
            $this->confirmations[] = $this->trans('Successful importing.', array(), 'Admin.Notifications.Success');
        }else{
            $this->errors[]        = $this->trans('Wrong file to import.', array(), 'Admin.Notifications.Error');
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
        $this->redirect_after .= '&id_deotemplate_products='.$object->id;
        $this->redirect();
    }

    public function postProcess()
    {
        parent::postProcess();
        
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::getIsset('duplicatedeotemplate_products')) {
            $id = Tools::getValue('id_deotemplate_products');
            $model = new DeoTemplateProductsModel($id);
            $duplicate_object = $model->duplicateObject();
            if(isset($model->params)){
                # FIX : insert code can not duplicate
                $duplicate_object->params = $model->params;
            }
            $duplicate_object->name = $this->l('Duplicate of').' '.$duplicate_object->name;
            $old_key = $duplicate_object->plist_key;
            $duplicate_object->plist_key = 'plist'.DeoSetting::getRandomNumber();
            $duplicate_object->update();
            if ($duplicate_object->addShop()) {
                // duplicate shortCode
                if ($file_content = Tools::file_get_contents(DeoHelper::getConfigDir('theme_products').$old_key.'.tpl')){
                    DeoSetting::writeFile(DeoHelper::getConfigDir('theme_products'), $duplicate_object->plist_key.'.tpl', $file_content);
                }

                // duplicate css
                if ($file_content = Tools::file_get_contents($this->product_lists_css_folder.$old_key.'.css')){
                    DeoSetting::writeFile($this->product_lists_css_folder, $duplicate_object->plist_key.'.css', $file_content);
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
            if (!is_dir($this->theme_dir.'modules/deotemplate/views/templates/hook/products/')) {
                if (!is_dir($this->theme_dir.'modules/deotemplate/views/templates/hook/products/')) {
                    @mkdir($this->theme_dir.'modules/deotemplate/views/templates/hook/products/', 0755, true);
                }
            }
            DeoSetting::writeFile($this->theme_dir.'modules/deotemplate/views/templates/hook/products/', $fileName.'.tpl', $filecontent);
        }

        if (Tools::isSubmit('generateall')) {
            $query = new DbQuery();
            $query->from('deotemplate_products', 'productprofile');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            foreach ($result as $value) {
                $obj_model = new DeoTemplateProductsModel();
                $obj_model->plist_key = $value['plist_key'];
                $obj_model->name = $value['name'];
                $obj_model->class = $value['class'];
                $obj_model->params = $value['params'];
                $obj_model->demo = $value['demo'];
                $obj_model->active = $value['active'];
                $obj_model->responsive = $value['active'];

                if ($obj_model->update()) {
                    $this->saveTplFile($obj_model->plist_key, $obj_model->params);
                }
            }
        }
    }

    public function convertObjectToTpl($object_form)
    {
        $tpl = '';
        
        foreach ($object_form as $object) {
            if (isset($object['form']['active']) && $object['form']['active'] == 0){
                continue;
            }

            if ($object['name'] == 'box') {
                $tpl .= DeoSetting::getProductFunctionalButtons($object['form']['css']);
                $tpl .= $this->convertObjectToTpl($object['element']);
                $tpl .= '</div>';
            } else if ($object['name'] == 'code') {
                $tpl .= $object['code'];
            } else if ($object['name'] == 'more_image') {
                $str = $this->returnFileContent($object['name']);
                $str = str_replace('cart_default', $object['form']['size'], $str); 
                $str = (isset($object['form']['type']) && $object['form']['type'] == 'vertical') ? preg_replace('/vertical="(.*?)"/', 'vertical="true"', $str) : preg_replace('/vertical="(.*?)"/', 'vertical="false"', $str);
                $str = (isset($object['form']['dots']) && $object['form']['dots']) ? preg_replace('/dots="(.*?)"/', 'dots="true"', $str) : preg_replace('/dots="(.*?)"/', 'dots="false"', $str);
                $str = (isset($object['form']['centermode']) && $object['form']['centermode']) ? preg_replace('/centermode="(.*?)"/', 'centermode="true"', $str) : preg_replace('/centermode="(.*?)"/', 'centermode="false"', $str);
                $str = (isset($object['form']['lazyload']) && $object['form']['lazyload'] && DeoHelper::getLazyload()) ? preg_replace('/lazyload="(.*?)"/', 'lazyload="true"', $str) : preg_replace('/lazyload="(.*?)"/', 'lazyload="false"', $str);
                $str = (isset($object['form']['mousewheel']) && $object['form']['mousewheel']) ? preg_replace('/mousewheel="(.*?)"/', 'mousewheel="true"', $str) : preg_replace('/mousewheel="(.*?)"/', 'mousewheel="false"', $str);
                $str = (isset($object['form']['fade']) && $object['form']['fade']) ? preg_replace('/fade="(.*?)"/', 'fade="true"', $str) : preg_replace('/fade="(.*?)"/', 'fade="false"', $str);
                $str = (isset($object['form']['slidestoshow']) && $object['form']['slidestoshow']) ? preg_replace('/slidestoshow="(.*?)"/', 'slidestoshow="'.$object['form']['slidestoshow'].'"', $str) : preg_replace('/slidestoshow="(.*?)"/', 'slidestoshow="false"', $str);
                $str = (isset($object['form']['responsive']) && $object['form']['responsive']) ? preg_replace('/responsive="(.*?)"/', 'responsive="'.$object['form']['responsive'].'"', $str) : preg_replace('/responsive="(.*?)"/', 'responsive="false"', $str);


                // build data for fake item loading
                $col_loading = '';
                if (isset($object['form']['responsive']) && $object['form']['responsive'] != '') {
                    if (isset($object['form']['responsive'])) {
                        $object['form']['responsive'] = str_replace($this->str_search, $this->str_relace, $object['form']['responsive']);
                    }
                    if (isset($object['form']['slick_custom'])) {
                        $str_relace = array('&', '\"', '\'', '', '', '', '[', ']', '+', '{', '}');
                        $object['form']['slick_custom'] = str_replace($this->str_search, $str_relace, $object['form']['slick_custom']);
                    }
                    if (isset($object['form']['responsive'])) {
                        $object['form']['responsive'] = json_decode($object['form']['responsive']);
                    }
                    $array_item_custom = $object['form']['responsive'];
                    foreach ($array_item_custom as $array_item_custom_val) {
                        $size_window = $array_item_custom_val[0];
                        $number_item = $array_item_custom_val[1];
                        if ($size_window <= 480) {
                            $col_loading .= ' loading-sp-'.$number_item;
                        }else if ($size_window <= 576) {
                            $col_loading .= ' loading-xs-'.$number_item;
                        }else if ($size_window <= 768) {
                            $col_loading .= ' loading-sm-'.$number_item;
                        }else if ($size_window <= 992) {
                            $col_loading .= ' loading-md-'.$number_item;
                        }else if ($size_window <= 1200) {
                            $col_loading .= ' loading-lg-'.$number_item;
                        }else if ($size_window <= 1500) {
                            $col_loading .= ' loading-xl-'.$number_item;
                        }else if ($size_window > 1500) {
                            $col_loading .= ' loading-xxl-'.$object['form']['slidestoshow'];
                        }
                    };
                    // $str = str_replace('thumbnail-image slick-slide', 'thumbnail-image slick-slide'.$col_loading, $str);
                }else{
                    $col_loading = ' loading-sp-'.$object['form']['slidestoshow'].' loading-xs-'.$object['form']['slidestoshow'].' loading-sm-'.$object['form']['slidestoshow'].' loading-md-'.$object['form']['slidestoshow'].' loading-lg-'.$object['form']['slidestoshow'].' loading-xl-'.$object['form']['slidestoshow'].' loading-xxl-'.$object['form']['slidestoshow'];
                    // $str = str_replace('thumbnail-image slick-slide', 'thumbnail-image slick-slide'.$col_loading, $str);
                }
                $str = preg_replace('/col_loading="(.*?)"/', 'col_loading="'.$col_loading.'"', $str);

                // $str = preg_replace('/data-type="(.*?)"/', 'data-type="'.$object['form']['type'].'"', $str);
                // $str = preg_replace('/data-image-type="(.*?)"/', 'data-image-type="'.$object['form']['size'].'"', $str);;
                // $str = ($object['form']['responsive']) ? preg_replace('/data-breakpoints="(.*?)"/', 'data-breakpoints="'.$object['form']['breakpoints'].'"', $str) : preg_replace('/data-breakpoints="(.*?)"/', '', $str);
                $tpl .= $str;
            } else if ($object['name'] == 'product_thumbnail') {
                $str = $this->returnFileContent($object['name']);
                $str = str_replace('home_default', $object['form']['size'], $str);
                $str = str_replace('all', $object['form']['labelflag'], $str);
                $str = preg_replace('/second_image="(.*?)"/', 'second_image="'.$object['form']['second_image'].'"', $str);
                $str = preg_replace('/deo_size="(.*?)"/', 'deo_size="'.$object['form']['size'].'"', $str);
                $str = preg_replace('/labelflag="(.*?)"/', 'labelflag="'.$object['form']['labelflag'].'"', $str);

                // if (isset($object['form']['labelflag'])){
                //     switch ($object['form']['labelflag']) {
                //         case "disable":
                //             $str = str_replace('{include file="module:deotemplate/views/templates/front/products/product_flags.tpl"}', '', $str);
                //             break;
                //         case "newdiscount":
                //             $content = $this->returnFileContent('product_flags_new_discount');
                //             $content .= $this->returnFileContent('label_new_discount');
                //             $str = str_replace('{include file="module:deotemplate/views/templates/front/products/product_flags.tpl"}', $content, $str);
                //             break;
                //         case "newsale":
                //             $content = $this->returnFileContent('product_flags_new_sale');
                //             $content .= $this->returnFileContent('label_new_sale');
                //             $str = str_replace('{include file="module:deotemplate/views/templates/front/products/product_flags.tpl"}', $content, $str);
                //             break;
                //     }
                // }else{
                //     $str = str_replace('{include file="module:deotemplate/views/templates/front/products/product_flags.tpl"}', '', $str);
                // }

                $tpl .= $str;
            } else if ($object['name'] == 'attribute') {
                $str = $this->returnFileContent($object['name']);
                $str = (isset($object['form']['show_color']) && $object['form']['show_color']) ? preg_replace('/show_color="(.*?)"/', 'show_color="true"', $str) : preg_replace('/show_color="(.*?)"/', 'show_color="false"', $str);
                $str = (isset($object['form']['show_name_attribute']) && $object['form']['show_name_attribute']) ? preg_replace('/show_name_attribute="(.*?)"/', 'show_name_attribute="true"', $str) : preg_replace('/show_name_attribute="(.*?)"/', 'show_name_attribute="false"', $str);
                $str = (isset($object['form']['show_value_text']) && $object['form']['show_value_text']) ? preg_replace('/show_value_text="(.*?)"/', 'show_value_text="true"', $str) : preg_replace('/show_value_text="(.*?)"/', 'show_value_text="false"', $str);
                $tpl .= $str;
            } else if ($object['name'] == 'reviews') {
                $str = $this->returnFileContent($object['name']);
                $str = (isset($object['form']['show_count']) && $object['form']['show_count']) ? preg_replace('/show_count="(.*?)"/', 'show_count="true"', $str) : preg_replace('/show_count="(.*?)"/', 'show_count="false"', $str);
                $str = (isset($object['form']['show_text_count']) && $object['form']['show_text_count']) ? preg_replace('/show_text_count="(.*?)"/', 'show_text_count="true"', $str) : preg_replace('/show_text_count="(.*?)"/', 'show_text_count="false"', $str);
                $str = (isset($object['form']['show_zero_review']) && $object['form']['show_zero_review']) ? preg_replace('/show_zero_review="(.*?)"/', 'show_zero_review="true"', $str) : preg_replace('/show_zero_review="(.*?)"/', 'show_zero_review="false"', $str);
                $tpl .= $str;
            } else if ($object['name'] == 'quantity') {
                $str = $this->returnFileContent($object['name']);
                $str = (isset($object['form']['show_label_quantity']) && $object['form']['show_label_quantity']) ? preg_replace('/show_label_quantity="(.*?)"/', 'show_label_quantity="true"', $str) : preg_replace('/show_label_quantity="(.*?)"/', 'show_label_quantity="false"', $str);
                $tpl .= $str;
            } else {
                if (!isset($this->file_content[$object['name']])) {
                    $this->returnFileContent($object['name']);
                }
                $tpl .= $this->file_content[$object['name']];
            }
        }
        return $tpl;
    }

    public function returnFileContent($pelement)
    {
        $tpl_dir = $this->theme_dir.'modules/deotemplate/views/templates/front/products/'.$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = _PS_MODULE_DIR_.'deotemplate/views/templates/front/products/'.$pelement.'.tpl';
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
                'action' => Context::getContext()->link->getAdminLink('AdminDeoShortcodeController'),
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

    public function getFileContent()
    {
        $pelement = Tools::getValue('pelement');
        $tpl_dir = $this->theme_dir.'modules/deotemplate/views/templates/hook/products/'.$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = _PS_MODULE_DIR_.'deotemplate/views/templates/hook/products/'.$pelement.'.tpl';
        }
        return array('fileName' => $pelement, 'filecontent' => Tools::file_get_contents($tpl_dir));
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        $this->helper->module = DeoTemplate::getInstance();
    }

    public function renderForm()
    {
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addJqueryUI('ui.droppable');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
        // $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/form.js');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/product.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/form.css');

        $source_file = Tools::scandir(DeoHelper::getConfigDir('module_products'), 'tpl');
        if (is_dir(DeoHelper::getConfigDir('theme_products'))) {
            $source_template_file = Tools::scandir(DeoHelper::getConfigDir('theme_products'), 'tpl');
            $source_file = array_merge($source_file, $source_template_file);
        }
        
        Media::addJsDef(array('deo_message_box' => $this->l('You can not drag group to group!')));
        Media::addJsDef(array('deo_message_delete' => $this->l('Do you want to delete it?')));

        // $icon_list = DeoSetting::getProductElementIcon();
        // foreach ($source_file as $value) {
        //     $fileName = basename($value, '.tpl');
        //     if ($fileName == 'index') {
        //         continue;
        //     }
        //     $elements[$fileName] = array(
        //         'name' => str_replace('_', ' ', $fileName),
        //         'icon' => (isset($icon_list[$fileName]) ? $icon_list[$fileName] : 'icon-sun'));
        // }

        $this->object->params = str_replace($this->str_search, $this->str_relace, $this->object->params);

        $elements = array();
        $config_dir = DeoHelper::getConfigDir('theme_products') . 'config.json';
        if (!file_exists($config_dir)) {
            $config_dir = DeoHelper::getConfigDir('module_products') . 'config.json';
        }
        $elements = json_decode(Tools::file_get_contents($config_dir), true);

        $element_by_name = array();
        foreach ($elements as $key_lv1 => $groups) {
            foreach ($groups as $key_lv2 => $group) {
                foreach ($group as $key_lv3 => $item) {
                    if (isset($item['file'])){
                        $element_by_name[$item['file']] = array('name' => $item['name'], 'icon' => $item['icon'],'file' => $item['file']);
                        if (isset($item['data-form'])){
                            $elements[$key_lv1][$key_lv2][$key_lv3]['dataForm'] = json_encode($item['data-form']);
                            $element_by_name[$item['file']]['data-form'] = $item['data-form'];
                        }
                    }
                }
            }
        }

        $labelflag = DeoSetting::getLabelAndFlag();
        $effecthover = DeoSetting::getEffectHover();
        Media::addJsDef(array('deo_labelflag' => $labelflag));
        Media::addJsDef(array('deo_effecthover' => $effecthover));

        $block_list = array(
            'image' => array('title' => 'Product image', 'class' => 'product-image'),
            'meta' => array('title' => 'Product meta', 'class' => 'product-meta'),
        );

        $params = array('image' => array(), 'meta' => array());
        if (isset($this->object->params) && $this->object->params) {
            $params = json_decode($this->object->params, true);
            foreach ($block_list as $key_block => $blocks) {
                foreach ($params[$key_block] as $key => $value) {
                    $params[$key_block][$key]['dataForm'] = (isset($value['form'])) ? json_encode($value['form']) : array();
                    if (isset($element_by_name[$value['name']])) {
                        $params[$key_block][$key]['config'] = $element_by_name[$value['name']];
                    }
                    if ($value['name'] == 'box'){
                        foreach ($value['element'] as $key_lv1 => $value_lv1) {
                            $params[$key_block][$key]['element'][$key_lv1]['dataForm'] = (isset($value_lv1['form'])) ? json_encode($value_lv1['form']) : array();
                            if (isset($element_by_name[$value_lv1['name']])) {
                                $params[$key_block][$key]['element'][$key_lv1]['config'] = $element_by_name[$value_lv1['name']];
                            }
                        }
                    }
                }
            }
        }

        $imageType = ImageType::getImagesTypes('products');
        $type_more_image =  array(
            'vertical' => $this->l('Vertical'),
            'horizontal' => $this->l('Horizontal'),
        );
        Media::addJsDef(array('deo_type_more_image' => $type_more_image));

        // print_r($params);
        
        $inputs = array(
            array(
                'type' => 'text',
                'label' => $this->l('Name'),
                'name' => 'name',
                'required' => true,
                'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Product List Key'),
                'name' => 'plist_key',
                'readonly' => 'readonly',
                'desc' => $this->l('Tpl File name'),
            ),
            array(
                'label' => $this->l('Class'),
                'type' => 'text',
                'name' => 'class',
                'width' => 140
            ),
            array(
                'type' => 'product_list_builder',
                'name' => 'product_list_builder',
                'effecthover' => $effecthover,
                'labelflag' => $labelflag,
                'element_by_name' => $element_by_name,
                'elements' => $elements,
                'params' => $params,
                'blockList' => $block_list,
                'imageType' => $imageType,
                'type_more_image' => $type_more_image,
            ),
            array(
                'type' => 'hidden',
                'name' => 'params'
            ),
        );

        if ((int) DeoHelper::getConfig('DEBUG_MODE')){
            $inputs_demo = array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Responsive for demo'),
                    'name' => 'responsive',
                    'desc' => $this->l("Example: [[1500, 3],[1200, 3],[992, 3],[768, 2], [576, 1],[480, 1]]. The format is [x,y] whereby x=browser width and y=number of slides displayed"),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Demo'),
                    'name' => 'demo',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '1',
                ),
            );
        }else{
            $inputs_demo = array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Responsive for demo'),
                    'name' => 'responsive',
                    'desc' => $this->l("Example: [[1500, 3],[1200, 3],[992, 2],[768, 2], [576, 1],[480, 1]]. The format is [x,y] whereby x=browser width and y=number of slides displayed"),
                    'form_group_class' => 'hidden',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Demo'),
                    'name' => 'demo',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '1',
                    'form_group_class' => 'hidden',
                ),
            );
        }

        $inputs = array_merge($inputs_demo, $inputs);
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Deo Product List Manage'),
                'icon' => 'icon-folder-close'
            ),
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
                    'icon' => 'process-icon-save')
            )
        );
        return parent::renderForm();
    }

    public function replaceSpecialStringToHtml($arr)
    {
        foreach ($arr as &$v) {
            if ($v['name'] == 'code') {
                // validate module
                $v['code'] = str_replace($this->str_search, $this->str_relace, $v['code']);
            } else {
                if ($v['name'] == 'box') {
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
            $file_value['plist_key'] = 'plist'.$num;
            $file_value['name'] = $file_value['plist_key'];
            $file_value['class'] = 'product-list-'.$num;
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
        Tools::deleteFile(DeoHelper::getConfigDir('theme_products').$object->plist_key.'.tpl');
        parent::processDelete();
    }

    public function saveTplFile($plist_key, $params = '')
    {
        $data_form = str_replace($this->str_search, $this->str_relace, $params);
        $data_form = json_decode($data_form, true);

        $arr_class = array();
        foreach ($data_form as $key_data_form => $value_data_form) {
            foreach ($value_data_form as $key => $value) {
                if ($value['name'] == 'more_image' && !in_array('more-image', $arr_class)){
                    $arr_class[] = 'more-image';
                    if ($value['form']['type'] == 'horizontal' && !in_array('more-image-horizontal', $arr_class)){
                        $arr_class[] = 'more-image-horizontal';
                    }
                }

                if ($value['name'] == 'product_thumbnail'){
                    if ($value['form']['labelflag'] == 'newdiscount' && !in_array('label-new-discount', $arr_class)){
                        $arr_class[] = 'label-new-discount';
                    }
                    if ($value['form']['effecthover'] != 'disable' && !in_array('bg-hover-product', $arr_class)){
                        $arr_class[] = 'bg-hover-product';
                        if ($value['form']['effecthover'] == 'bg-hover-white' && !in_array('bg-hover-white', $arr_class)){
                            $arr_class[] = 'bg-hover-white';
                        }
                        if ($value['form']['effecthover'] == 'bg-hover-black' && !in_array('bg-hover-black', $arr_class)){
                            $arr_class[] = 'bg-hover-black';
                        }
                        if ($value['form']['effecthover'] == 'bg-hover-skin' && !in_array('bg-hover-skin', $arr_class)){
                            $arr_class[] = 'bg-hover-skin';
                        }
                    }
                }

                if ($value['name'] == 'product_flags_new_discount' && !in_array('label-new-discount', $arr_class)){
                    $arr_class[] = 'label-new-discount';
                }
            }
        }
        
        $tpl_grid = '';
        $tpl_grid .= (count($arr_class) > 0) ? str_replace(" thumbnail-container-class", " ".implode(" ",$arr_class), DeoSetting::getProductContainer()) : str_replace(" thumbnail-container-class", "", DeoSetting::getProductContainer());
        $tpl_grid .= DeoSetting::getProductLeftBlock().$this->convertObjectToTpl($data_form['image']).DeoSetting::getProductLeftBlockEnd();
        $tpl_grid .= DeoSetting::getProductRightBlock().$this->convertObjectToTpl($data_form['meta']).DeoSetting::getProductRightBlockEnd();
        $tpl_grid .= DeoSetting::getProductContainerEnd();
        $folder = DeoHelper::getConfigDir('theme_products');
        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }
        $file = $plist_key.'.tpl';
        $tpl_grid = preg_replace('/\{\*[\s\S]*?\*\}/', '', $tpl_grid);
        $tpl_grid = str_replace(" mod='deotemplate'", '', $tpl_grid);

        DeoSetting::writeFile($folder, $file, DeoHelper::getLicenceTPL().$tpl_grid);
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
    
    public function displayDuplicateLink($token = null, $id = null, $name = null)
    {
        $controller = 'AdminDeoProducts';
        $token = Tools::getAdminTokenLite($controller);
        $html = '<a href="#" title="Duplicate" onclick="confirm_link(\'\', \'Duplicate Product List ID '.$id.'. If you wish to proceed, click &quot;Yes&quot;. If not, click &quot;No&quot;.\', \'Yes\', \'No\', \'index.php?controller='.$controller.'&amp;id_deotemplate_products='.$id.'&amp;duplicatedeotemplate_products&amp;token='.$token.'\', \'#\')">
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
            }elseif($this->can_import && Tools::getIsset('import' . $this->table)){
                if (!$this->access('edit')) {
                    $this->errors[] = $this->trans('You do not have permission to import data.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
