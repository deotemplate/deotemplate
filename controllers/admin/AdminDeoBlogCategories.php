<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogCategory.php');

class AdminDeoBlogCategoriesController extends ModuleAdminController
{
    public $name = 'deotemplate';
    protected $fields_form = array();
    private $_html = '';
    
    public function __construct()
    {
        $this->bootstrap = true;
        $this->id_deoblog_category = true;
        $this->table = 'deoblog_category';

        $this->className = 'DeoBlogCategory';
        $this->lang = true;
        $this->fields_options = array();
        
        parent::__construct();
        $this->identifier = 'id_deoblog_category';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));

        $this->fields_list = array(
            'id_deoblog_category' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'title' => array('title' => $this->l('Name'), 'filter_key' => 'cl!title'),
            'active' => array('title' => $this->l('Displayed'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => true),
        );
        $this->max_image_size = Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->_select .= ' cl.title ';
        $this->_join .= 'LEFT JOIN '._DB_PREFIX_.'deoblog_category_lang cl ON cl.id_deoblog_category=a.id_deoblog_category AND cl.id_lang=b.id_lang
                ';
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'deoblog_category_shop` sh ON (sh.`id_deoblog_category` = b.`id_deoblog_category` AND sh.id_shop = '.(int)Context::getContext()->shop->id.') ';
        }
        $this->_where = ' && a.`is_root` != 1';
        $this->_group = ' GROUP BY (a.id_deoblog_category) ';
        $this->_orderBy = 'a.id_deoblog_category';
        $this->_orderWay = 'DESC';
    }

    /**
     * Build List linked Icons Toolbar
     */
    public function initPageHeaderToolbar()
    {
        // update new direction for media
        $media_dir = DeoHelper::getMediaDir();
        $this->context->controller->addCss(__PS_BASE_URI__.'js/jquery/ui/themes/base/jquery.ui.tabs.css');
        if (file_exists(DeoHelper::getThemeDir().'css/modules/deotemplate/assets/admin/blog.css')) {
            $this->context->controller->addCss(__PS_BASE_URI__.'modules/deotemplate/assets/admin/blog.css');
        } else {
            $this->context->controller->addCss(__PS_BASE_URI__.$media_dir.'css/admin/blog.css');
        }

        if (Tools::getValue('id_deoblog_category')) {
            $helper = DeoBlogHelper::getInstance();
            $category_obj = new DeoBlogCategory(Tools::getValue('id_deoblog_category'), $this->context->language->id);
            $this->page_header_toolbar_btn['view-blog-preview'] = array(
                'href' => $helper->getBlogCatLink(get_object_vars($category_obj)),
                'desc' => $this->l('Preview'),
                'icon' => 'process-icon-preview icon-preview deoblog-comment-link-icon icon-3x',
                'target' => '_blank',
            );

        }

        $this->page_header_toolbar_btn['lists-blog-category'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogCategories'),
            'desc' => $this->l('List Categories'),
            'icon' => 'process-icon-widget icon-list-alt',
        );

        $this->page_header_toolbar_btn['create-blog-category'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogCategories').'&adddeoblog_category',
            'desc' => $this->l('Create New'),
            'icon' => 'process-icon-new',
        );

        
        
        return parent::initPageHeaderToolbar();
    }

    /**
     *
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        // $this->addJqueryPlugin('tagify');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.sortable.min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/jquery.cookie-plugin.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.tabs.min.js');

        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'jquery.tagify.min.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'tagify.css');

        $media_dir = DeoHelper::getMediaDir();
        if (file_exists(DeoHelper::getThemeDir().'js/modules/deotemplate/assets/admin/jquery.nestable.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/deotemplate/assets/admin/jquery.nestable.js');
        } else {
            $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/jquery.nestable.js');
        }

        if (file_exists(DeoHelper::getThemeDir().'js/modules/deotemplate/assets/admin/blog.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/deotemplate/assets/admin/blog.js');
        } else {
            $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/blog.js');
        }

        Media::addJsDef(array(
            'PS_ALLOW_ACCENTED_CHARS_URL' => (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
        ));
    }

    /**
     * get live Edit URL
     */
    public function getLiveEditUrl($live_edit_params)
    {
        $url = $this->context->shop->getBaseURL().Dispatcher::getInstance()->createUrl('index', (int)$this->context->language->id, $live_edit_params);
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $url = str_replace('index.php', '', $url);
        }
        return $url;
    }


    public function postProcess()
    {
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::getValue('doupdatepos') && Tools::isSubmit('updatePosition')) {
            $list = Tools::getValue('list');
            $root = 1;
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
                foreach ($menus as $id_deoblog_category) {
                    $sql = 'UPDATE `'._DB_PREFIX_.'deoblog_category` SET `position` = '.(int)$i.', id_parent = '.(int)$id_parent.'
                            WHERE `id_deoblog_category` = '.(int)$id_deoblog_category;
                    $res &= Db::getInstance()->execute($sql);
                    $i++;
                }
            }
            die($this->l('Update Positions Done'));
        }else if (Tools::getValue('dodel')) {
            /* delete megamenu item */
            $obj = new DeoBlogCategory((int)Tools::getValue('id_deoblog_category'));
            $res = $obj->delete();
            Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getValue('token'));
        }else if (Tools::getValue('delete_many_menu')) {
            $list = array_filter(explode( ',', trim(Tools::getValue('list'), ',')));
            if(is_array($list) && $list)
            {
                foreach ($list as $key => $id) {
                    $obj = new DeoBlogCategory((int)$id);
                    if($obj->id)
                    {
                        $obj->delete();
                    }
                }
            }
            Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getValue('token'));
        }else if ((Tools::isSubmit('save'.$this->name) && Tools::isSubmit('active')) || Tools::isSubmit('saveandstay')) {
            parent::validateRules();
            
            if (count($this->errors)) {
                $this->display = 'edit';
                return false;
            }
            if ($id_deoblog_category = Tools::getValue('id_deoblog_category')) {
                # validate module
                $category = new DeoBlogCategory((int)$id_deoblog_category);
            } else {
                # validate module
                $category = new DeoBlogCategory();
                $category->randkey = DeoBlogHelper::genKey();
            }
            $this->copyFromPost($category, $this->table);
            $id_shop = (int)Context::getContext()->shop->id;
            
            $category->id_shop = $id_shop;
            if ($category->validateFields(true) && $category->validateFieldsLang(true)) {
                $category->save();
                if ($category->image != '') {
                    require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
                    DeoBlogImage::generateImageCategory($category->id, $category->image, $category->image_link, $category->use_image_link, $id_shop);
                }

                if (Tools::isSubmit('submitAddblogAndPreview')) {
                    # validate module
                    $this->redirect_after = $this->previewUrl($category);
                } elseif (Tools::isSubmit('saveandstay')) {
                    # validate module
                    Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$category->id.'&conf=4&update'.$this->table.'&token='.Tools::getValue('token'));
                } else {
                    # validate module
                    Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token'));
                }
                
                // Tools::redirectAdmin(AdminController::$currentIndex.'&savedeoblog&token='.Tools::getValue('token').'&id_deoblog_category='.$category->id);
            } else {
                // validate module
                $this->_html .= $this->displayWarning($this->l('An error occurred while attempting to save.'));
            }
        }else{
            parent::postProcess(true);
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

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            if (Validate::isLoadedObject($this->object)) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
            $bo_theme = 'default';
        }
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');  

        $obj = $this->object;
        $categories = $obj->getDropdown(null, $obj->id_parent);

        $id_root = $obj->getRoot();
        $id_root = ($id_root) ? $id_root : 1;

        # FIX : PARENT IS NOT THIS CATEGORY
        if (Tools::getValue('id_deoblog_category')){
            $id_deoblog_category = (int)(Tools::getValue('id_deoblog_category'));
            foreach ($categories as $key => $category) {
                if ($category['id'] == $id_deoblog_category) {
                    unset($categories[$key]);
                }
            }
        }

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $templates = DeoBlogHelper::getTemplates();
        $templates = array_merge(array(array('template' => '','name' => $this->l('Choose template'))), $templates);

        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=blog-category';
        $desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
                    <span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
                    <span class="btn-image">
                        <a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
                        <a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
                    </span>
                </span>';
        $no_image = __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png';

        $this->multiple_fieldsets = true;
        $this->fields_value['id_parent'] = $id_root;
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Category Form.'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Category ID'),
                    'name' => 'id_deoblog_category',
                    'default' => 0,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Template'),
                    'name' => 'template',
                    'options' => array('query' => $templates,
                        'id' => 'template',
                        'name' => 'name'),
                    'default' => '',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'default' => '',
                    'name' => 'title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    'required' => true,
                    'class' => 'copyMeta2friendlyURL',
                ),
                // array(
                //     'type' => 'select',
                //     'label' => $this->l('Parent ID'),
                //     'name' => 'id_parent',
                //     'options' => array(
                //         'query' => $categories,
                //         'id' => 'id',
                //         'name' => 'title'
                //     ),
                //     'default' => 'url',
                // ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Parent ID'),
                    'default' => $id_root,
                    'name' => 'id_parent',
                    'id' => 'name', 
                    'required' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is Active'),
                    'name' => 'active',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '1',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('CSS Class'),
                    'name' => 'class_css',
                    'display_image' => true,
                    'default' => ''
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use image link'),
                    'name' => 'use_image_link',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '0',
                    'class' => 'use_image_link',
                ),
                // array(
                //     'type' => 'switch',
                //     'label' => $this->l('Lazy load'),
                //     'name' => 'lazyload',
                //     'values' => DeoSetting::returnYesNo(),
                //     'default' => '1',
                //     'class' => 'lazyload',
                // ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Rate size image'),
                    'name' => 'rate_image',
                    'default' => '0',
                    'suffix' => '%',
                    'class' => 'rate_image',
                    'form_group_class' => 'rate_lazyload_group rate_value',
                ),
                array(
                    'type' => 'html',
                    'default' => '',
                    'name' => 'html_calc_rate_image',
                    'html_content' => '<a href="javascript:void(0)" class="calc-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div><div class="virtual-image-link"></div>',
                    'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
                    'form_group_class' => 'rate_lazyload_group group_calc_rate_image',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image link'),
                    'name' => 'image_link',
                    'default' => '',
                    'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
                    'form_group_class' => 'select_image_link_group',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image'),
                    'name' => 'image',
                    'default' => '',
                    'class' => 'hide',
                    'desc' => $desc,
                    'form_group_class' => 'image-choose',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content',
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => true
                ),
                array(
                    'type' => 'script_image',
                    'name' => 'script_image',
                    'default' => '',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'save'.$this->name,
                'class' => 'btn btn-default pull-right'
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'saveandstay',
                    'type' => 'submit',
                    'title' => $this->l('Save and stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save-and-stay'
                )
            )
        );

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('SEO META'),
            ),
            'input' => array(
                // custom template
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'default' => '',
                    'desc' => $this->l('Only letters and the minus (-) character are allowed')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                    'default' => ''
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'default' => '',
                    'desc' => $this->l('To add "tags" click in the field, write something, and then press "Enter."').$this->l('Tag values not use commas ","')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'save'.$this->name,
                'class' => 'btn btn-default pull-right'
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'saveandstay',
                    'type' => 'submit',
                    'title' => $this->l('Save and stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save-and-stay'
                )
            )
        ); 

        return parent::renderForm();
    }

    public function renderList()
    {   
        $this->toolbar_title = $this->l('Blog Categories Management');
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new')
        );

        $this->initToolbar();
        if (!$this->loadObject(true)) {
            return;
        }

        // $obj = $this->object;
        // $tree = $obj->getTree();
        // $categories = $obj->getDropdown(null, $obj->id_parent);

        # FIX : PARENT IS NOT THIS CATEGORY
        // $id_deoblog_category = (int) (Tools::getValue('id_deoblog_category'));
        // foreach ($categories as $key => $category) {
        //     if ($category['id'] == $id_deoblog_category) {
        //         unset($categories[$key]);
        //     }
        // }

        // $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        
        // Media::addJsDef(array(
        //     'deoblog_del_img_txt' => $this->l('Delete'),
        //     'deoblog_del_img_mess' => $this->l('Are you sure delete this?'),
        //     'action' => AdminController::$currentIndex.'&save'.$this->name.'&token='.Tools::getValue('token'),
        //     'addnew' => AdminController::$currentIndex.'&token='.Tools::getValue('token'),
        // ));

        // $this->context->smarty->assign(array(
        //     'tree' => $tree,
        // ));

        // $content = '';
        // $content .= parent::renderList();
        // $content .= Context::getContext()->smarty->fetch($this->getTemplatePath().'deo_blog_categories/category.tpl');

        return parent::renderList();
    }

    /**
     * Asign value for each input of Data form
     */
    public function getConfigFieldsValues($obj)
    {
        $languages = Language::getLanguages(false);
        $fields_values = array();
        
        $id_shop = (int)Context::getContext()->shop->id;
        $url = _PS_BASE_URL_;
        if (Tools::usingSecureMode()) {
            # validate module
            $url = _PS_BASE_URL_SSL_;
        }
        foreach ($this->fields_form as $k => $f) {
            foreach ($f['form']['input'] as $j => $input) {
                if (isset($obj->{trim($input['name'])})) {
                    if (isset($obj->{trim($input['name'])})) {
                        $data = $obj->{trim($input['name'])};
                    } else {
                        $data = $input['default'];
                    }
                                       
                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            # validate module
                            $fields_values[$input['name']][$lang['id_lang']] = isset($data[$lang['id_lang']]) ? $data[$lang['id_lang']] : $input['default'];
                        }
                    } else {
                        # validate module
                        $fields_values[$input['name']] = $data;
                    }
                } else {
                    // if ($input['name'] == 'image_link' && $data) {
                    // if ($input['name'] == 'image_link' && $obj->image != '') {
                    //     //$thumb = __PS_BASE_URI__.'modules/'.$this->name.'/views/img/c/'.$data;
                    //     $thumb = $url._THEME_DIR_.'assets/img/modules/deotemplate/blog/'.$id_shop.'/c/'.$obj->image;
                    //     $this->fields_form[$k]['form']['input'][$j]['thumb'] = $thumb;
                    // }
                    
                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            $v = Tools::getValue('title', DeoHelper::get($input['name'], $lang['id_lang']));
                            $fields_values[$input['name']][$lang['id_lang']] = $v ? $v : $input['default'];
                        }
                    } else {
                        $v = Tools::getValue($input['name'], DeoHelper::get($input['name']));
                        $fields_values[$input['name']] = $v ? $v : $input['default'];
                    }
                }
            }
        }
        return $fields_values;
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        
        if (count($this->errors) <= 0) {
            if ($this->id_object) {
                # EDIT
                if (!$this->access('edit')) {
                    if (Tools::isSubmit('save'.$this->name) && Tools::getValue('save'.$this->name)) {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                    }
                }
                
                if (!$this->access('delete')) {
                    if (Tools::getValue('dodel')) {
                        $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
                    }
                }
            } else {
                # ADD
                if (!$this->access('add')) {
                    if (Tools::isSubmit('save'.$this->name) && Tools::getValue('save'.$this->name)) {
                        $this->errors[] = $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error');
                    }
                }
            }
        }
    }
}
