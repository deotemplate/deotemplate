<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogCategory.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogComment.php');

class AdminDeoBlogDashboardController extends ModuleAdminControllerCore
{
    public $name = 'deotemplate';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'list';
        $this->addRowAction('list');
        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        
        $this->page_header_toolbar_title = $this->l('Dashboard');
        $this->page_header_toolbar_btn = array();

        $this->page_header_toolbar_btn['list-blogs'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogs'),
            'desc' => $this->l('Blogs'),
            'icon' => 'process-icon-widget icon-file-text',
            'target' => '_blank',
        );

        $this->page_header_toolbar_btn['list-categories'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogCategories'),
            'desc' => $this->l('Categories'),
            'icon' => 'process-icon-widget icon-folder-open',
            'target' => '_blank',
        );

        $this->page_header_toolbar_btn['view-blog-comment'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogComments'),
            'desc' => $this->l('Comments'),
            'icon' => 'process-icon-widget icon-comments-alt',
            'target' => '_blank',
        );

        return parent::initPageHeaderToolbar();
    }
    
    public function postProcess()
    {   
        // parent::postProcess();
        if (count($this->errors)) {
            return false;
        }

        if (Tools::isSubmit('saveConfiguration')) {
            $posts = Tools::getValue(DeoHelper::getConfigName('BLOG_DASHBOARD_FIELDS_VALUE'));
            DeoHelper::updateValue(DeoHelper::getConfigName('BLOG_DASHBOARD_FIELDS_VALUE'), $posts);
            $posts = json_decode($posts);

            foreach ($posts as $key => $post) {
                # validate module
                $value = Tools::getValue($key);
                if ($key == DeoHelper::getConfigName('BLOG_DASHBOARD_FIELDS_VALUE')){
                    continue;
                }
                DeoHelper::updateValue($key, $value);
            }
        }

        if (Tools::isSubmit('regenerateImage')) {
            require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
            DeoBlogImage::regenerateImage();
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        $this->context->controller->addJS(DeoHelper::getJsAdminDir().'admin/blog.js');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/general.js');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
    }

    public function renderList()
    {
        $link = $this->context->link;

        //$obj           = new DeoBlogCategory();
        //$menus         = $obj->getDropdown(null, $obj->id_parent);
        $form = '';

        // $obj = new DeoBlogCategory();
        // $tree = $obj->getTree();
        // $menus = $obj->getDropdown(null, $obj->id_parent);

        $deo_templates_blog = array();
        $templates = DeoBlogHelper::getTemplates();
        foreach ($templates as $key => $template) {
            $deo_templates_blog[] = $key;
        }
        Media::addJsDef(array('deo_templates_blog' => $deo_templates_blog));

        $tabs =  array(
            'tab_general' => $this->l('General'),
            'tab_blog_post' => $this->l('Blog Post'),
            'tab_sidebar_column' => $this->l('Sidebar Column'),
            'tab_image_size' => $this->l('Image Size'),
            'tab_template' => $this->l('Template'),
        );
        $inputs_header = array(
            'type' => 'tabConfig',
            'name' => 'title',
            'values' => $tabs,
            'default' => Tools::getValue('tab_open') ? Tools::getValue('tab_open') : 'tab_general',
            'save' => false,
        );

        // custom template
        $inputs_general = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Blog'),
                'name' => DeoHelper::getConfigName('ENABLE_BLOG'),
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Template Default'),
                'name' => DeoHelper::getConfigName('BLOG_DEFAULT_TEMPLATE'),
                'options' => array('query' => $templates,
                    'id' => 'template',
                    'name' => 'name'),
                'default' => 'default',
                'desc' => $this->l('Default template when category set template is "none"'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('URL Root Rewrite'),
                'name' => DeoHelper::getConfigName('BLOG_LINK_REWRITE'),
                'required' => true,
                'desc' => $this->l('Make Friendly URL for your homepage blog. Example: You configure is blog => http://yourdomain/blog.html'),
                'default' => 'blog',
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Use ID Friendly URL'),
                'name' => DeoHelper::getConfigName('BLOG_URL_USE_ID'),
                'class' => 'form-action',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_general',
            ),
        );
       
        $inputs_blog_post = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Show Short Description:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_DESCRIPTION'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Image:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_IMAGE'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Author:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_AUTHOR'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Category:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_CATEGORY'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Created Date:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_CREATED'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Views:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_VIEWS'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Counter:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_COUNT_COMMENT'),
                'required' => false,
                'class' => 't',
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_blog_post',
            ),
            // comment
            array(
                'type' => 'select',
                'label' => $this->l('Comment Type:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_COMMENT_ENGINE'),
                'class' => 'select-comment',
                'id' => 'BLOG_ITEM_COMMENT_ENGINE',
                'options' => array('query' => array(
                        array('id' => 'none', 'name' => $this->l('None')),
                        array('id' => 'local', 'name' => $this->l('Local')),
                        array('id' => 'facebook', 'name' => $this->l('Facebook')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'default' => 'local',
                'form_group_class' => 'tab_blog_post',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit Comment'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_LIMIT_COMMENTS'),
                'required' => false,
                'class' => 't',
                'default' => 10,
                'form_group_class' => 'comment-limit tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show List Comments:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_LIST_COMMENT'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Show/Hidden the list comment'),
                'form_group_class' => 'comment-local tab_blog_post',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Form Comment:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_SHOW_FORM_COMMENT'),
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'comment-local tab_blog_post',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Application ID Facebook:'),
                'name' => DeoHelper::getConfigName('BLOG_ITEM_FACEBOOK_APP_ID'),
                'required' => false,
                'class' => 't',
                'default' => '100858303516',
                'desc' => '<a target="_blank" href="http://developers.facebook.com/docs/reference/plugins/comments/">'.$this->l('Register A Comment Box, Then Get Application ID in Script Or Register Facebook Application ID to moderate comments').'</a>',
                'form_group_class' => 'comment-facebook tab_blog_post',
            ),
        );
        
        $inputs_sidebar_column = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Blog Categories Tree'),
                'name' => DeoHelper::getConfigName('BLOG_CATEORY_MENU'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
                'form_group_class' => 'tab_sidebar_column',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Popular Blog'),
                'name' => DeoHelper::getConfigName('BLOG_SHOW_POPULAR'),
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_sidebar_column',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit Popular Blog'),
                'name' => DeoHelper::getConfigName('BLOG_LIMIT_POPULAR'),
                'default' => 5,
                'form_group_class' => 'tab_sidebar_column',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Recent Blog'),
                'name' => DeoHelper::getConfigName('BLOG_SHOW_RECENT'),
                'is_bool' => true,
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_sidebar_column',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit Recent Blog'),
                'name' => DeoHelper::getConfigName('BLOG_LIMIT_RECENT'),
                'default' => 5,
                'form_group_class' => 'tab_sidebar_column',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show All Tags'),
                'name' => DeoHelper::getConfigName('BLOG_SHOW_ALL_TAGS'),
                'is_bool' => true,
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_sidebar_column',
            ),
        );
        
        $inputs_image_size = array(
            array(
                'type' => 'hidden',
                'name' => DeoHelper::getConfigName('BLOG_IMAGE_SIZE'),
                'class' => 'image-size',
                'default' => '',
                'form_group_class' => 'form-image-size tab_image_size',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '
                <table class="grid-table js-grid-table table temp-table temp-table-image loading not-emtpy">
                    <thead>
                        <tr>
                            <td>'.$this->l('Name').'</td>
                            <td>'.$this->l('Width').'</td>
                            <td>'.$this->l('Height').'</td>
                            <td>'.$this->l('Rate Image').'</td>
                            <td>'.$this->l('Action').'</td>
                        </tr>
                    </thead>
                    </tbody>
                        <tr class="empty-row">
                            <td colspan="4">
                                <span class="block-empty"><i class="icon-meh"></i> List empty</span>
                                <span class="block-loading"><i class="icon-loading"></i> Loading</span>
                            </td>
                        </tr>
                    </tbody>
                </table>',
                'form_group_class' => 'form-temp-table tab_image_size',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button type="button" class="add-new btn btn-default"><i class="process-icon-new"></i> '.$this->l('Add new').'</button> <a href="'.$link->getAdminLink('AdminDeoBlogDashboard').'&regenerateImage'.'" class="regenerate-image btn btn-primary"><i class="process-icon-refresh"></i> '.$this->l('Regenerate Image').'</a>',
                'form_group_class' => 'tab_image_size',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Name Image Size'),
                'name' => 'temp_name',
                'default' => 'normal',
                'desc' => 'Name image size must be from a-z and do not allow space.',
                'form_group_class' => 'form-temp-image hide tab_image_size',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => 'temp_width',
                'suffix' => 'px',
                'class' => 'fixed-width-lg',
                'default' => '300',
                'form_group_class' => 'form-temp-image hide tab_image_size',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => 'temp_height',
                'suffix' => 'px',
                'class' => 'fixed-width-lg',
                'default' => '300',
                'form_group_class' => 'form-temp-image hide tab_image_size',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button type="button" class="save btn btn-primary">'.$this->l('Save').'</button> <button type="button" class="cancel btn btn-warning">'.$this->l('Cancel').'</button>',
                'form_group_class' => 'form-temp-image hide tab_image_size',
            ),
        );

        $inputs_template = array(
            array(
                'type' => 'hidden',
                'name' => DeoHelper::getConfigName('BLOG_TEMPLATES'),
                'form_group_class' => 'form-template tab_template',
                'default' => '',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '
                <table class="grid-table js-grid-table table temp-table temp-table-template loading not-emtpy">
                    <thead>
                        <tr>
                            <td>'.$this->l('Name').'</td>
                            <td>'.$this->l('Item Per Category').'</td>
                            <td>'.$this->l('Item Per Page').'</td>
                            <td>'.$this->l('Responsive').'</td>
                            <td>'.$this->l('Blog Item').'</td>
                            <td>'.$this->l('Actions').'</td>
                        </tr>
                    </thead>
                    </tbody>
                        <tr class="empty-row">
                            <td colspan="4">
                                <span class="block-empty"><i class="icon-meh"></i> List empty</span>
                                <span class="block-loading"><i class="icon-loading"></i> Loading</span>
                            </td>
                        </tr>
                    </tbody>
                </table>',
                'form_group_class' => 'form-temp-table tab_template',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Settings blog item').'</div>',
                'form_group_class' => 'form-temp-template hide',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Template'),
                'name' => 'temp_name_template',
                'disabled' => true,
                'default' => '6',
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Item per category'),
                'name' => 'temp_item_per_category',
                'default' => '3',
                'desc' => $this->l('Maximum item per category at blog homepage.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Item per page'),
                'name' => 'temp_item_per_page',
                'default' => '6',
                'desc' => $this->l('Number item per page at blog category page.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Introduce Category'),
                'name' => 'temp_show_introduce_category',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Image'),
                'name' => 'temp_show_image',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Title'),
                'name' => 'temp_show_title',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Description'),
                'name' => 'temp_show_description',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Author'),
                'name' => 'temp_show_author',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Category'),
                'name' => 'temp_show_category',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Created Date'),
                'name' => 'temp_show_created_date',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Viewed'),
                'name' => 'temp_show_viewed',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Comment'),
                'name' => 'temp_show_comment',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Read More'),
                'name' => 'temp_show_read_more',
                'is_bool' => true,
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Setting responsive').'</div>',
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Large Devices (width screen >= 1500px)'),
                'name' => 'temp_col_xxl',
                'default' => '2',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many column number display in blog list.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Devices (width screen < 1500px)'),
                'name' => 'temp_col_xl',
                'default' => '2',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many column number display in blog list.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Large Devices (width screen < 1200px)'),
                'name' => 'temp_col_lg',
                'default' => '2',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many column number display in blog list.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Tablet Devices (width screen < 992px)'),
                'name' => 'temp_col_md',
                'default' => '2',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many column number display in blog list.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Tablet Devices (width screen < 768px)'),
                'name' => 'temp_col_sm',
                'default' => '2',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many column number display in blog list.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Mobile Devices (width screen < 567px)'),
                'name' => 'temp_col_xs',
                'default' => '1',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many column number display in blog list.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Mobile Devices (width screen < 480px)'),
                'name' => 'temp_col_sp',
                'default' => '1',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button type="button" class="save btn btn-primary">'.$this->l('Save').'</button> <button type="button" class="cancel btn btn-warning">'.$this->l('Cancel').'</button>',
                'form_group_class' => 'form-temp-template hide tab_template',
            ),
            
        );

        $inputs_hidden = array(
            array(
                'type' => 'hidden',
                'name' => 'tab_open',
            ),
            array(
                'type' => 'hidden',
                'name' => DeoHelper::getConfigName('BLOG_DASHBOARD_FIELDS_VALUE'),
            ),
        );
        
        $inputs = array_merge($inputs_general, $inputs_blog_post, $inputs_sidebar_column, $inputs_image_size, $inputs_template, $inputs_hidden);
        
        

        $this->fields_form[0]['form'] = array(
            'input' => $inputs,
            'submit' => array(
                'class' => 'btn btn-default pull-right '.get_class($this),
                'title' => $this->l('Save'),
            ),
        );

        $this->fields_value = $this->getConfigFieldsValues($this->fields_form);

        $fields_value = array();
        foreach ($this->fields_value as $key => $field) {
            if (Tools::strpos($key, 'temp') !== false){
                continue;
            }else{
                $fields_value[$key] = $field;
            }
        }

        $this->fields_value[DeoHelper::getConfigName('BLOG_DASHBOARD_FIELDS_VALUE')] = json_encode($fields_value);
        $this->fields_value['tab_open'] = $inputs_header['default'];

        $helper = new HelperForm($this);
        
        $this->setHelperDisplay($helper);
        $helper->fields_value = $this->fields_value;
        $helper->tpl_vars = $this->tpl_form_vars;
        !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';
        if ($this->tabAccess['view']) {
            $helper->tpl_vars['show_toolbar'] = false;
            $helper->tpl_vars['submit_action'] = 'saveConfiguration';
            if (Tools::getValue('back')) {
                $helper->tpl_vars['back'] = '';
            } else {
                $helper->tpl_vars['back'] = '';
            }
        }
        $form = $helper->generateForm($this->fields_form);

        $template = $this->createTemplate('panel.tpl');
        $comments = DeoBlogComment::getComments(null, 10, $this->context->language->id);
        $blogs = DeoBlog::getListBlogs(null, $this->context->language->id, 0, 10, 'views', 'DESC');

        $template->assign(array(
            'showed' => 1,
            'comment_link' => $link->getAdminLink('AdminDeoBlogComments'),
            'blog_link' => $link->getAdminLink('AdminDeoBlogs'),
            'blogs' => $blogs,
            'count_blogs' => DeoBlog::countBlogs(null, $this->context->language->id),
            'count_cats' => DeoBlogCategory::countCats(),
            'count_comments' => DeoBlogComment::countComments(),
            'latest_comments' => $comments,
            'tabs' => $inputs_header,
            'globalform' => $form,
        ));

        return $template->fetch();
    }

    /**
     * Assign value for each input of Data form
     */
    public function getConfigFieldsValues($obj)
    {
        $fields_value = array();
        foreach ($obj as $tab) {
            if (!empty($tab['form']['input'])){
                foreach ($tab['form']['input'] as $input) {
                    if ($input['name'] == DeoHelper::getConfigName('BLOG_DASHBOARD_FIELDS_VALUE') || $input['type'] == 'html') {
                        continue;
                    }

                    if (DeoHelper::hasKey($input['name'])){
                        $fields_value[$input['name']] = DeoHelper::get($input['name']);
                    }else{
                        if (isset($input['default'])){
                            $fields_value[$input['name']] = $input['default'];
                        }
                    }
                }
            }
        }

        return $fields_value;
    }

    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        
        if (count($this->errors) <= 0) {
            # EDIT
            if (!$this->access('edit')) {
                if (Tools::isSubmit('saveConfiguration') &&  Tools::getValue('saveConfiguration')) {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
