<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogCategory.php');

class AdminDeoBlogsController extends ModuleAdminController
{
    public $name = 'deotemplate';
    protected $max_image_size;
    protected $position_identifier = 'id_deoblog';

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'deoblog';
        $this->identifier = 'id_deoblog';
        $this->className = 'DeoBlog';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
        $this->fields_list = array(
            'id_deoblog' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'meta_title' => array('title' => $this->l('Title'), 'filter_key' => 'b!meta_title'),
            'author_name' => array('title' => $this->l('Author'), 'filter_key' => 'a!author_name'),
            'title' => array('title' => $this->l('Category'), 'filter_key' => 'cl!title'),
            'active' => array('title' => $this->l('Disable'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => true),
            'date_add' => array('title' => $this->l('Date Create'), 'type' => 'datetime', 'filter_key' => 'a!date_add'),
            'date_upd' => array('title' => $this->l('Date Update'), 'type' => 'datetime', 'filter_key' => 'a!date_upd')
            
        );
        $this->max_image_size = Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->_select .= ' cl.title ';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'deoblog_category c ON a.id_deoblog_category = c.id_deoblog_category
                                  LEFT JOIN '._DB_PREFIX_.'deoblog_category_lang cl ON cl.id_deoblog_category=c.id_deoblog_category AND cl.id_lang=b.id_lang
                ';
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'deoblog_shop` sh ON (sh.`id_deoblog` = b.`id_deoblog` AND sh.id_shop = '.(int)Context::getContext()->shop->id.') ';
        }
        $this->_where = '';
        $this->_group = ' GROUP BY (a.id_deoblog) ';
        $this->_orderBy = 'a.id_deoblog';
        $this->_orderWay = 'DESC';
    }

    public function initPageHeaderToolbar()
    {
        $link = $this->context->link;
        if (Tools::getValue('id_deoblog')) {
            $helper = DeoBlogHelper::getInstance();
            $blog_obj = new DeoBlog(Tools::getValue('id_deoblog'), $this->context->language->id);
            $this->page_header_toolbar_btn['view-blog-preview'] = array(
                'href' => $helper->getBlogLink(get_object_vars($blog_obj)),
                'desc' => $this->l('Preview'),
                'icon' => 'process-icon-preview deoblog-comment-link-icon',
                'target' => '_blank',
            );
            
            $this->page_header_toolbar_btn['view-blog-comment'] = array(
                'href' => $link->getAdminLink('AdminDeoBlogComments').'&id_deoblog='.Tools::getValue('id_deoblog'),
                'desc' => $this->l('View Comments'),
                'icon' => 'process-icon-comment icon-comments-alt deoblog-comment-link-icon',
                'target' => '_blank',
            );
        }

        $this->page_header_toolbar_btn['list-blogs'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogs'),
            'desc' => $this->l('List Blogs'),
            'icon' => 'process-icon-widget icon-file-text',
        );

        $this->page_header_toolbar_btn['create-new-blog'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogs').'&adddeoblog',
            'desc' => $this->l('Create New'),
            'icon' => 'process-icon-new',
        );
        
        return parent::initPageHeaderToolbar();
    }
    
    public function renderForm()
    {   
        $bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
            $bo_theme = 'default';
        }
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/imagemanager.css');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/imagemanager.js');

        if (!$this->loadObject(true)) {
            if (Validate::isLoadedObject($this->object)) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $obj = new DeoBlogCategory();
        $id_root = $obj->getRoot();
        $id_root = ($id_root) ? $id_root : 1;
        $obj->getTree($id_root);
        $menus = $obj->getDropdown(null, $obj->id_parent);
        // array_shift($menus);
        
        $id_shop = (int)Context::getContext()->shop->id;
        $url = _PS_BASE_URL_;
        if (Tools::usingSecureMode()) {
            # validate module
            $url = _PS_BASE_URL_SSL_;
        }
        
        $default_author_name = '';
        if (isset($this->context->employee->firstname) && isset($this->context->employee->lastname)) {
            $default_author_name = $this->context->employee->firstname.' '.$this->context->employee->lastname;
        }
        
        if ($this->object->id == '') {
            $this->object->author_name = $default_author_name;
        }

        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=blog';
        $desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
                    <span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
                    <span class="btn-image">
                        <a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
                        <a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
                    </span>
                </span>';
        $no_image = __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png';

        $this->multiple_fieldsets = true;
        
        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Blog Form'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name blog'),
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    'required' => true,
                    'class' => 'copyMeta2friendlyURL',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category'),
                    'name' => 'id_deoblog_category',
                    'options' => array('query' => $menus,
                        'id' => 'id',
                        'name' => 'title'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is Active'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => DeoSetting::returnYesNo(),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Author'),
                    'name' => 'author_name',
                    'desc' => $this->l('Author only display on Front End')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use image link'),
                    'name' => 'use_image_link',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '0',
                    'class' => 'use_image_link',
                ),
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
                    'label' => $this->l('Short Description'),
                    'name' => 'description',
                    'autoload_rte' => true,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 30,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Blog Content'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                ),
                array(
                    'type' => 'date_deoblog',
                    'label' => $this->l('Date Create'),
                    'name' => 'date_add',
                    'default' => date('Y-m-d'),
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
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('SEO META'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                // custom template
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'desc' => $this->l('Only letters and the minus (-) character are allowed')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
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

        Media::addJsDef(array(
            'deoblog_del_img_txt' => $this->l('Delete'),
            'deoblog_del_img_mess' => $this->l('Are you sure delete this?'),
            'PS_ALLOW_ACCENTED_CHARS_URL' => (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
        ));

        return parent::renderForm();
    }

    public function renderList()
    {
        $this->toolbar_title = $this->l('Blogs Management');
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new')
        );

        return parent::renderList();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('viewblog') && ($id_deoblog = (int)Tools::getValue('id_deoblog')) && ($blog = new DeoBlog($id_deoblog, $this->context->language->id)) && Validate::isLoadedObject($blog)) {
            $this->redirect_after = $this->getPreviewUrl($blog);
        }
                
        if ((Tools::isSubmit('save'.$this->name) && Tools::isSubmit('active')) || Tools::isSubmit('submitAdddeoblogAndPreview') || Tools::isSubmit('saveandstay')) {
            parent::validateRules();

            if (count($this->errors)) {
                $this->display = 'edit';
                return false;
            }
            
            $id_shop = (int)Context::getContext()->shop->id;
            if (!$id_deoblog = (int)Tools::getValue('id_deoblog')) {
                # ADD
                $blog = new DeoBlog();
                $this->copyFromPost($blog, $this->table);

                $blog->id_employee = $this->context->employee->id;

                if (!$blog->add(false)) {
                    $this->errors[] = $this->l('An error occurred while creating blog.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    # validate module
                    $this->updateAssoShop($blog->id);
                    if ($blog->image != '') {
                        require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
                        DeoBlogImage::generateImageBlog($blog->id, $blog->image, $blog->image_link, $blog->use_image_link, $id_shop);
                    }
                }
            } else {
                # EDIT
                $blog = new DeoBlog($id_deoblog);
                $this->copyFromPost($blog, $this->table);
                

                if (!$blog->update()) {
                    $this->errors[] = $this->l('An error occurred while update blog.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    # validate module
                    $this->updateAssoShop($blog->id);
                    if ($blog->image != '') {
                        require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
                        DeoBlogImage::generateImageBlog($blog->id, $blog->image, $blog->image_link, $blog->use_image_link, $id_shop);
                    }
                }
            }

            if (Tools::isSubmit('submitAddblogAndPreview')) {
                # validate module
                $this->redirect_after = $this->previewUrl($blog);
            } elseif (Tools::isSubmit('saveandstay')) {
                # validate module
                Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$blog->id.'&conf=4&update'.$this->table.'&token='.Tools::getValue('token'));
            } else {
                # validate module
                Tools::redirectAdmin(self::$currentIndex.'&id_deoblog_category='.$blog->id_deoblog_category.'&conf=4&token='.Tools::getValue('token'));
            }
        } else {
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

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'jquery.tagify.min.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'tagify.css');
        $media_dir = DeoHelper::getMediaDir();
        if (file_exists(_PS_THEME_DIR_.'js/modules/deotemplate/assets/admin/blog.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/deotemplate/assets/admin/blog.js');
        } else {
            $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/blog.js');
        }
        
        if (file_exists(_PS_THEME_DIR_.'css/modules/deotemplate/assets/admin/blog.css')) {
            $this->context->controller->addCss(__PS_BASE_URI__.'modules/deotemplate/assets/admin/blog.css');
        } else {
            $this->context->controller->addCss(__PS_BASE_URI__.$media_dir.'css/admin/blog.css');
        }
    }

    public function ajaxProcessUpdateblogPositions()
    {
        if ($this->tabAccess['edit'] === '1') {
            $id_deoblog = (int)Tools::getValue('id_deoblog');
            $id_category = (int)Tools::getValue('id_deoblog_category');
            $way = (int)Tools::getValue('way');
            $positions = Tools::getValue('blog');
            if (is_array($positions)) {
                foreach ($positions as $key => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && $pos[2] == $id_deoblog)) {
                        $position = $key;
                        break;
                    }
                }
            }
            $blog = new blog($id_deoblog);
            if (Validate::isLoadedObject($blog)) {
                if (isset($position) && $blog->updatePosition($way, $position)) {
                    die(true);
                } else {
                    die('{"hasError" : true, "errors" : "Can not update blog position"}');
                }
            } else {
                die('{"hasError" : true, "errors" : "This blog can not be loaded"}');
            }
        }
    }

    public function ajaxProcessUpdateblogCategoriesPositions()
    {
        if ($this->tabAccess['edit'] === '1') {
            $id_deoblog_category_to_move = (int)Tools::getValue('id_deoblog_category_to_move');
            $id_deoblog_category_parent = (int)Tools::getValue('id_deoblog_category_parent');
            $way = (int)Tools::getValue('way');
            $positions = Tools::getValue('blog_category');
            if (is_array($positions)) {
                foreach ($positions as $key => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_deoblog_category_parent && $pos[2] == $id_deoblog_category_to_move)) {
                        $position = $key;
                        break;
                    }
                }
            }
            $blog_category = new blogCategory($id_deoblog_category_to_move);
            if (Validate::isLoadedObject($blog_category)) {
                if (isset($position) && $blog_category->updatePosition($way, $position)) {
                    die(true);
                } else {
                    die('{"hasError" : true, "errors" : "Can not update blog categories position"}');
                }
            } else {
                die('{"hasError" : true, "errors" : "This blog category can not be loaded"}');
            }
        }
    }

    public function ajaxProcessPublishblog()
    {
        if ($this->tabAccess['edit'] === '1') {
            if ($id_deoblog = (int)Tools::getValue('id_deoblog')) {
                $bo_blog_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminblogContent&id_deoblog='.(int)$id_deoblog.'&updateblog&token='.$this->token;

                if (Tools::getValue('redirect')) {
                    die($bo_blog_url);
                }

                $blog = new blog((int)(Tools::getValue('id_deoblog')));
                if (!Validate::isLoadedObject($blog)) {
                    die('error: invalid id');
                }

                $blog->active = 1;
                if ($blog->save()) {
                    die($bo_blog_url);
                } else {
                    die('error: saving');
                }
            } else {
                die('error: parameters');
            }
        }
    }
}
