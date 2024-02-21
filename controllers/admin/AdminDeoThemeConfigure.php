<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoFrameworkHelper.php');
require_once(_PS_MODULE_DIR_.'deotemplate/libs/DeoDataSample.php');

/**
 * NOT extends ModuleAdminControllerCore, because override tpl : ROOT/modules/deotemplate/views/templates/admin/deo_theme_configuration/helpers/form/form.tpl
 */
class AdminDeoThemeConfigureController extends ModuleAdminController
{
    protected $max_image_size = null;
    public $module_name = 'deotemplate';
    public $img_path;
    public $folder_name;
    public $module_path;
    public $tpl_path;
    public $theme_dir;


    /**
     * @var Array $overrideHooks
     */
    protected $themeName;
    
    /**
     * @var Array $overrideHooks
     */
    protected $themePath = '';
    
    /**
     * save config
     */
    public $submitSaveSetting = false;


    public function __construct()
    {
        parent::__construct();
        $this->theme_dir = DeoHelper::getThemeDir();
        $this->folder_name = Tools::getIsset('imgDir') ? Tools::getValue('imgDir') : 'images';
        $this->bootstrap = true;
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->themeName = DeoHelper::getThemeName();
        $this->img_path = DeoHelper::getImgThemeDir($this->folder_name);
        $this->img_url = DeoHelper::getImgThemeUrl($this->folder_name);
        $this->context = Context::getContext();
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
        $this->tpl_path = _PS_ROOT_DIR_.'/modules/'.$this->module_name.'/views/templates/admin';
        $this->module_path = __PS_BASE_URI__.'modules/deotemplate/';
        // $this->module_path_resource = $this->module_path.'views/';
        $this->themePath = _PS_ALL_THEMES_DIR_.$this->themeName.'/';
    }
    
    public function initPageHeaderToolbar()
    {
        Media::addJsDef(array(
            'deo_controller'  => 'AdminDeoThemeConfigureController',
        ));
        
        parent::initPageHeaderToolbar();

        // Add btn save on toolbar
        $this->page_header_toolbar_btn['Save'] = array(
            'href' => 'javascript:void(0);',
            'desc' => $this->l('Save'),
            'js' => 'TopSave()',
            'icon' => 'process-icon-save',
        );
        Media::addJsDef(array('TopSave_Name' => 'submitAddconfiguration'));
    }
    
    /**
     * OVERRIDE ROOT\classes\controller\AdminController.php
     * Assign smarty variables for all default views, list and form, then call other init functions
     */
    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = $this->l('You do not have permission to view this.');
            return;
        }

        $this->getLanguages();
        $this->initToolbar();
        // $this->initTabModuleList();
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

    public function renderForm()
    {
        $this->context->controller->addJqueryPlugin('colorpicker');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/general.js');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/bootstrap-colorpicker.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/bootstrap-colorpicker.css');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
        $this->context->controller->addCss(DeoHelper::getCssAdminDir().'admin/style_AdminDeoThemeConfigure.css', 'all');

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
        $type_position = array(
            array(
                'id_type' => 'fixed',
                'name_type' => $this->l('Fixed'),
            ),
            array(
                'id_type' => 'absolute',
                'name_type' => $this->l('Absolute'),
            ),
        );
        $type_unit = array(
            array(
                'id_type' => 'percent',
                'name_type' => $this->l('Percent'),
            ),
            array(
                'id_type' => 'pixel',
                'name_type' => $this->l('Pixel'),
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
        
        $skins_available = DeoFrameworkHelper::getSkins($this->themeName);
        // $directions = DeoFrameworkHelper::getLayoutDirections($this->themeName);
        $skins_default = array('name' => $this->l('Default'), 'id' => 'default');
        $skins_custom = array('name' => $this->l('Color Custom'), 'id' => 'custom-skin');
        
        $this->lang = true;
        $skins = array();
        $skins[] = $skins_default;
        $skins[] = $skins_custom;
        $skins = array_merge_recursive($skins, $skins_available);
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        
        $sample = new DeoDataSample();
        $moduleList = $sample->getModuleList();

        $tabs =  array(
            'tab_general' => $this->l('General'),
            'tab_category_page' => $this->l('Category Page'),
            'tab_product_list' => $this->l('Product List'),
            'tab_ajax_cart' => $this->l('Ajax Cart'),
            'tab_infinite_scroll' => $this->l('Infinite Scroll'),
            'tab_review' => $this->l('Review'),
            'tab_compare' => $this->l('Compare'),
            'tab_wishlist' => $this->l('Wishlist'),
            'tab_social_login' => $this->l('Social Login'),
            'tab_google_map' => $this->l('Goole Map'),
            'tab_child_theme' => $this->l('Child Theme'),
            'tab_data_sample' => $this->l('Data Sample'),
        );

        $inputs_general = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy load'),
                'name' => DeoHelper::getConfigName('LAZYLOAD'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Allow use lazy load image on your site.'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy load Intersection Observer'),
                'name' => DeoHelper::getConfigName('LAZY_INTERSECTION_OBSERVER'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Allow use lazy load Intersection Observer on your site.'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Stickey Header'),
                'name' => DeoHelper::getConfigName('STICKEY_MENU'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Allow use stickey header.'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Delete account'),
                'name' => DeoHelper::getConfigName('DELETE_ACCOUNT_LINK'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Show link allow customer delete themseff account at my account page.'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Back to Top'),
                'name' => DeoHelper::getConfigName('BACKTOP'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Show Scroll To Top button.'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Name Primary Font Custom'),
                'name' => DeoHelper::getConfigName('PRIMARY_CUSTOM_FONT'),
                'class' => 'fixed-width-xxl',
                'desc' => $this->l('Use fonts provided by Google. Example: Open San, Lato. Get font name here ').'<a href="https://fonts.google.com/">'.$this->l('Google Font').'</a>',
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Name Second Font Custom'),
                'name' => DeoHelper::getConfigName('SECOND_CUSTOM_FONT'),
                'class' => 'fixed-width-xxl',
                'desc' => $this->l('Use fonts provided by Google. Example: Open San, Lato. Get font name here ').'<a href="https://fonts.google.com/">'.$this->l('Google Font').'</a>',
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Color Theme'),
                'name' => DeoHelper::getConfigName('DEFAULT_SKIN'),
                'class' => 'select_skin',
                'default' => 'default',
                'options' => array(
                    'query' => $skins,
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Choose Skin Color for your site.'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Primary Color Custom'),
                'name' => DeoHelper::getConfigName('PRIMARY_CUSTOM_COLOR_SKIN'),
                'desc' => $this->l('Override default Primary Color'),
                'form_group_class' => 'tab_general group_show_select_skin',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Second Color Custom'),
                'name' => DeoHelper::getConfigName('SECOND_CUSTOM_COLOR_SKIN'),
                'desc' => $this->l('Override default Second Color'),
                'form_group_class' => 'tab_general group_show_select_skin',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script type="text/javascript" src="'.__PS_BASE_URI__.DeoHelper::getJsDir().'colorpicker/js/deo.jquery.colorpicker.js"></script>',
                'form_group_class' => 'hide',
            ),

            array(
                'type' => 'text',
                'label' => $this->l('Quality'),
                'name' => DeoHelper::getConfigName('QUALITY_IMAGE_COMPRESS'),
                'default' => 80,
                'suffix' => '%',
                'desc' => $this->l('Be Careful. If the quality is low your image will be blurred!'),
                'class' => 'fixed-width-xl',
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'html',
                'label' => $this->l('Compress Images Themes'),
                'name' => 'default_html',
                'html_content' => '<button class="button btn btn-success" name="submitCompressImage" id="submitCompressImage" type="submit">'.$this->l('Compress Images').'</button>',
                'desc' => $this->l('Only compress images uploaded by our template'),
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'html',
                'label' => $this->l('Import Sample Data'),
                'name' => 'default_html',
                'html_content' => '<button class="button btn btn-warning" name="submitImportSampleData" id="submitImportSampleData" type="submit">'.$this->l('Import Sample Data').'</button>',
                'desc' => $this->l('Replace data sample data to current settings with our template. Becareful: Current settings with our template will be lost and restore to default'),
                'form_group_class' => 'tab_general',
            ),
        );

        $inputs_category_page = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Display Sub Category'),
                'name' => DeoHelper::getConfigName('SUBCATEGORY'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Allow display list of sub category in category page.'),
                'form_group_class' => 'tab_category_page',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Display Banner Category'),
                'name' => DeoHelper::getConfigName('BANNER_CATEGORY_PAGE'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Allow display banner in category page.'),
                'form_group_class' => 'tab_category_page',
            ),
        );

        $inputs_product_list_setting = array(
            array(
                'type' => 'select',
                'label' => $this->l('Products Listing Mode'),
                'name' => DeoHelper::getConfigName('GRID_MODE'),
                'default' => 'grid',
                'options' => array('query' => array(
                        array('id' => 'grid', 'name' => $this->l('Grid Mode')),
                        array('id' => 'list', 'name' => $this->l('List Mode')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('Display Products In List Mode Or Grid Mode In Product List....'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Default Module On Desktop'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_MODULE'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in default module of prestashop.'),
                'form_group_class' => 'tab_product_list',
            ),
            // START: layout fullwidth
            array(
                'type' => 'html',
                'name' => 'default_html',
                'form_group_class' => 'tab_product_list',
                'html_content' => '<div class="alert alert-info">'.$this->l('Layout fullwidth (layout-full-width)').'</div>'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Large Devices (width screen >= 1500px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Devices (width screen < 1500px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Large Devices (width screen < 1200px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Tablet Devices (width screen < 992px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Tablet Devices (width screen < 768px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Mobile Devices (width screen < 567px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Mobile Devices (width screen < 480px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            // END: layout fullwidth
            // START: layout sidebar
            array(
                'type' => 'html',
                'name' => 'default_html',
                'form_group_class' => 'tab_product_list',
                'html_content' => '<div class="alert alert-info">'.$this->l('Layout Sidebar (layout-left-column or layout-right-column sidebar)').'</div>'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Large Devices (width screen >= 1500px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP_SIDEBAR'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Devices (width screen < 1500px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP_SIDEBAR'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Large Devices (width screen < 1200px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP_SIDEBAR'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Tablet Devices (width screen < 992px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET_SIDEBAR'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Tablet Devices (width screen < 768px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET_SIDEBAR'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Mobile Devices (width screen < 567px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE_SIDEBAR'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Mobile Devices (width screen < 480px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE_SIDEBAR'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            // END: layout sidebar
            // START: layout both sidebar
            array(
                'type' => 'html',
                'name' => 'default_html',
                'form_group_class' => 'tab_product_list',
                'html_content' => '<div class="alert alert-info">'.$this->l('Layout Both Columns (layout-both-columns)').'</div>'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Large Devices (width screen >= 1500px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP_BOTH'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Desktop Devices (width screen < 1500px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP_BOTH'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Large Devices (width screen < 1200px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP_BOTH'),
                'default' => ' ',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Tablet Devices (width screen < 992px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET_BOTH'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Tablet Devices (width screen < 768px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET_BOTH'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Mobile Devices (width screen < 567px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE_BOTH'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Small Mobile Devices (width screen < 480px)'),
                'name' => DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE_BOTH'),
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns'))
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many number products inline display in grid mode of product list.'),
                'form_group_class' => 'tab_product_list',
            ),
        );

        $inputs_data_sample = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Panel Tool'),
                'name' => DeoHelper::getConfigName('PANELTOOL'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_data_sample',
                'desc' => $this->l('Display PanelTool appearing on right of website.'),
            ),
            array(
                'type' => 'modules_block',
                'label' => $this->l('Module List:'),
                'name' => 'moduleList',
                'values' => $moduleList,
                'default' => '',
                'form_group_class' => 'tab_data_sample',
                'save' => false,
                'folder_data_struct' => str_replace('\\', '/', _PS_MODULE_DIR_.'deotemplate/install'),
            ),
        );

        $inputs_child_theme = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button type="submit" name="submitCopyImageChildTheme" id="submitCopyImageChildTheme" class="btn btn-success">'.$this->l('Copy Image').'</button>',
                'desc' => $this->l('Copy image sample from parent template. Use when you want to use our image on child theme. Be careful it will replace file in child theme if have same name.'),
                'form_group_class' => 'tab_child_theme',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button type="submit" name="submitCorrectChildTheme" id="submitCorrectChildTheme" class="btn btn-info">'.$this->l('Correct Theme').'</button>',
                'desc' => $this->l('Copy file necessary from parent template. Use when you just create child theme or parent theme just update. Be careful it will replace file in child theme if have same name.'),
                'form_group_class' => 'tab_child_theme',
            ),
        );

        $inputs_infinite_scroll = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL'),
                'default' => 0,
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            // PAGES SUPPORT
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll in Category Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_CATEGORY'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll Product in Search Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_SEARCH'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('You need install module ps_searchbar'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll Product in Deo Advanced Search Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_DEOTEMPLATE-ADVANCEDSEARCH'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('You need use our widget "Deo Advanced Search"'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll Product in Best Sales Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_BEST-SALES'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('You need install module ps_bestsellers'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll Product in New Products Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_NEW-PRODUCTS'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('You need install module ps_newproducts'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll Price Product in Price Drop (On Sale) Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_PRICES-DROP'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('You need install module ps_specials'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll Product in Manufature Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_MANUFACTURER'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('You need install module ps_brandlist'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Infinite Scroll Product in Supplier Page'),
                'name' => DeoHelper::getConfigName('ENABLE_INFINITE_SCROLL_SUPPLIER'),
                'default' => 1,
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('You need install module ps_supplierlist'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            // END PAGES SUPPORT
            array(
                'type' => 'text',
                'label' => $this->l('Product List CSS Selector'),
                'name' => DeoHelper::getConfigName('INFINITE_SCROLL_PRODUCT_LIST_CSS_SELECTOR'),
                'class' => 'fixed-width-xxl',
                'default' => '#js-product-list .products',
                'desc' => $this->l('If you don\'t know CSS please keep default or contact us'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Product Item CSS Selector'),
                'name' => DeoHelper::getConfigName('INFINITE_SCROLL_ITEM_SELECTOR'),
                'default' => '.ajax_block_product',
                'class' => 'fixed-width-xxl',
                'desc' => $this->l('If you don\'t know CSS please keep default or contact us'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Pagination CSS Selector'),
                'name' => DeoHelper::getConfigName('INFINITE_SCROLL_PAGINATION_SELECTOR'),
                'default' => '.pagination',
                'class' => 'fixed-width-xxl',
                'desc' => $this->l('If you don\'t know CSS please keep default or contact us'),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Hidden Message when reached the bottom end of the page (end page)'),
                'name' => DeoHelper::getConfigName('INFINITE_SCROLL_HIDE_MESSAGE_WHEN_END_PAGE'),
                'default' => 0,
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Display Button "Load more products" in Bottom'),
                'name' => DeoHelper::getConfigName('INFINITE_SCROLL_DISPLAY_LOAD_MORE_PRODUCT'),
                'default' => 0,
                'is_bool' => false,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_infinite_scroll show_load_more_product',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number Page Will Display Button "Load more products"'),
                'name' => DeoHelper::getConfigName('INFINITE_SCROLL_NUMBER_PAGE_SHOW_LOAD_MORE_PRODUCT'),
                'class' => 'fixed-width-xxl',
                'default' => '2',
                'form_group_class' => 'tab_infinite_scroll group_show_load_more_product',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Frequency Display Button "Load more products"'),
                'name' => DeoHelper::getConfigName('INFINITE_SCROLL_FREQUENCY_SHOW_LOAD_MORE_PRODUCT'),
                'default' => '0',
                'class' => 'fixed-width-xxl',
                'desc' => $this->l('Set 0 to disable this feature'),
                'form_group_class' => 'tab_infinite_scroll group_show_load_more_product',
            ),
            array(
                'type'         => 'textarea',
                'label'        => $this->l('Javascript code callback script after ajax Infinite Scroll'),
                'name'         => DeoHelper::getConfigName('INFINITE_SCROLL_JS_SCRIPT_AFTER'),
                'desc'         => $this->l('Javascript code executed after ajax Infinite Scroll is displayed'),
                'default'      => '',
                'autoload_rte' => false,
                'class'        => 'tinymce-on-demand',
                'rows'         => '20',
                'form_group_class' => 'tab_infinite_scroll',
            ),
            array(
                'type'         => 'textarea',
                'label'        => $this->l('Javascript code callback script on product processing'),
                'name'         => DeoHelper::getConfigName('INFINITE_SCROLL_JS_SCRIPT_PROCESS_PRODUCTS'),
                'desc'         => $this->l('Javascript executed on the result products'),
                'default'      => '',
                'autoload_rte' => false,
                'class'        => 'tinymce-on-demand',
                'rows'         => '20',
                'form_group_class' => 'tab_infinite_scroll',
            ),
        );

        $inputs_ajax_cart = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Ajax cart'),
                'name' => DeoHelper::getConfigName('ENABLE_AJAX_CART'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Configurations after Add to Cart').'</div>',
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Effect Fly Image After Add To Cart'),
                'name' => DeoHelper::getConfigName('TYPE_EFFECT_FLYCART'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show Popup after Add to Cart'),
            //     'name' => DeoHelper::getConfigName('SHOW_POPUP'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Enable Overlay Background After Add to Cart'),
            //     'name' => DeoHelper::getConfigName('ENABLE_OVERLAY_BACKGROUND'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Configurations for Popup Cart').'</div>',
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Update Quantity'),
                'name' => DeoHelper::getConfigName('ENABLE_UPDATE_QUANTITY'),
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Product Combination'),
                'name' => DeoHelper::getConfigName('SHOW_COMBINATION'),
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Product Customization'),
                'name' => DeoHelper::getConfigName('SHOW_CUSTOMIZATION'),
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            // array(
            //     'type' => 'text',
            //     'label' => $this->l('Width Of Cart Item'),
            //     'name' => DeoHelper::getConfigName('WIDTH_CART_ITEM'),
            //     'default' => '270',
            //     'suffix' => 'px',
            //     'class' => 'fixed-width-lg',
            //     'desc' => $this->l('Unit have to is pixcel (px). Example: 270px'),
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            // array(
            //     'type' => 'text',
            //     'label' => $this->l('Height of Cart Item'),
            //     'name' => DeoHelper::getConfigName('HEIGHT_CART_ITEM'),
            //     'default' => '145',
            //     'suffix' => 'px',
            //     'class' => 'fixed-width-lg',
            //     'desc' => $this->l('Unit have to is pixcel (px). Example: 145px'),
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            array(
                'type' => 'text',
                'label' => $this->l('Maximun Number Cart Item To Show Scroll (dropup or dropdown)'),
                'name' => DeoHelper::getConfigName('NUMBER_CART_ITEM_DISPLAY'),
                'default' => '3',
                'class' => 'fixed-width-lg',
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Notification after Add to Cart').'</div>',
                'form_group_class' => 'tab_ajax_cart',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Notification'),
                'name' => DeoHelper::getConfigName('ENABLE_NOTIFICATION'),
                'desc' => $this->l('Show notification when cart make a change'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_cart',
            ),
            // array(
            //     'type' => 'text',
            //     'label' => $this->l('Width Notification'),
            //     'name' => DeoHelper::getConfigName('WIDTH_NOTIFICATION'),
            //     'default' => '300px',
            //     'class' => 'fixed-width-lg',
            //     'desc' => $this->l('Unit have to is rem, em, px or %. Example: 300px or 100%'),
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            // array(
            //     'type' => 'select',
            //     'label' => $this->l('Position Vertical Notification'),
            //     'name' => DeoHelper::getConfigName('POSITION_VERTICAL_NOTIFICATION'),
            //     'options' => array(
            //         'query' => $type_vertical,
            //         'id' => 'id_type',
            //         'name' => 'name_type'
            //     ),
            //     'default' => 'top',
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            // array(
            //     'type' => 'text',
            //     'label' => $this->l('Value Position Vertical Notification'),
            //     'name' => DeoHelper::getConfigName('POSITION_VERTICAL_VALUE_NOTIFICATION'),
            //     'default' => '10',
            //     'suffix' => 'px',
            //     'class' => 'fixed-width-lg',
            //     'desc' => $this->l('Unit have to is pixcel (px). Example: 10px'),
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            // array(
            //     'type' => 'select',
            //     'label' => $this->l('Position Horizontal Notification'),
            //     'name' => DeoHelper::getConfigName('POSITION_HORIZONTAL_NOTIFICATION'),
            //     'options' => array(
            //         'query' => $type_horizontal,
            //         'id' => 'id_type',
            //         'name' => 'name_type'
            //     ),
            //     'default' => 'right',
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
            // array(
            //     'type' => 'text',
            //     'label' => $this->l('Value Position Horizontal Notification'),
            //     'name' => DeoHelper::getConfigName('POSITION_HORIZONTAL_VALUE_NOTIFICATION'),
            //     'default' => '10',
            //     'suffix' => 'px',
            //     'class' => 'fixed-width-lg',
            //     'desc' => $this->l('Unit have to is pixcel (px). Example: 10px'),
            //     'form_group_class' => 'tab_ajax_cart',
            // ),
        );

        $inputs_review = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable product reviews'),
                'name' => DeoHelper::getConfigName('ENABLE_PRODUCT_REVIEWS'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_review',
            ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show product reviews at list product'),
            //     'name' => DeoHelper::getConfigName('SHOW_PRODUCT_REVIEWS_LIST_PRODUCT'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_review',
            // ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show number product reviews at list product'),
            //     'name' => DeoHelper::getConfigName('SHOW_NUMBER_PRODUCT_REVIEWS_LIST_PRODUCT'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_review',
            // ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show zero product reviews at list product'),
            //     'name' => DeoHelper::getConfigName('SHOW_ZERO_PRODUCT_REVIEWS_LIST_PRODUCT'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_review',
            // ),
            array(
                'type' => 'switch',
                'label' => $this->l('All reviews must be validated by an employee'),
                'name' => DeoHelper::getConfigName('PRODUCT_REVIEWS_MODERATE'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_review',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Allow usefull button'),
                'name' => DeoHelper::getConfigName('PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_review',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Allow report button'),
                'name' => DeoHelper::getConfigName('PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_review',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Allow guest reviews'),
                'name' => DeoHelper::getConfigName('PRODUCT_REVIEWS_ALLOW_GUESTS'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Minimum time between 2 reviews from the same user'),
                'name' => DeoHelper::getConfigName('PRODUCT_REVIEWS_MINIMAL_TIME'),
                'default' => '30',
                'class' => 'fixed-width-lg',
                'suffix' => $this->l('second(s)'),
                'form_group_class' => 'tab_review',
            ),
        );

        $inputs_compare = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable product compare'),
                'name' => DeoHelper::getConfigName('ENABLE_PRODUCT_COMPARE'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_compare',
            ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show product compare at list product'),
            //     'name' => DeoHelper::getConfigName('SHOW_PRODUCT_COMPARE_LIST_PRODUCT'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_compare',
            // ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show product compare at product page'),
            //     'name' => DeoHelper::getConfigName('SHOW_PRODUCT_COMPARE_PRODUCT_PAGE'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_compare',
            // ),
            array(
                'type' => 'text',
                'label' => $this->l('Number product comparison '),
                'name' => DeoHelper::getConfigName('COMPARATOR_MAX_ITEM'),
                'default' => '3',
                'class' => 'fixed-width-lg',
                'form_group_class' => 'tab_compare',
            ),
        );

        $inputs_wishlist = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable product wishlist'),
                'name' => DeoHelper::getConfigName('ENABLE_PRODUCT_WISHLIST'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_wishlist',
            ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show product wishlist at list product'),
            //     'name' => DeoHelper::getConfigName('SHOW_PRODUCT_WISHLIST_LIST_PRODUCT'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_wishlist',
            // ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show product wishlist at product page'),
            //     'name' => DeoHelper::getConfigName('SHOW_PRODUCT_WISHLIST_PRODUCT_PAGE'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'form_group_class' => 'tab_wishlist',
            // ),
        );

        $inputs_social_login = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Social Login'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Social Login At Login Page'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_AT_LOGIN_PAGE'),
                'desc' => $this->l('Show Social Login At The Bottom Of Login Form'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'form_group_class' => 'tab_social_login',
                'html_content' => '<div class="alert alert-info">'.$this->l('Configurations key API for social login').'</div>'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Facebook Login'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_FACEBOOK_ENABLE'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Facebook App ID'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_FACEBOOK_APPID'),
                'default' => '',
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Google Login'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_GOOGLE_ENABLE'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Google App Client ID'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_GOOGLE_CLIENTID'),
                'default' => '',
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Twitter Login'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_TWITTER_ENABLE'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Twitter App Consumer Key (API Key)'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_TWITTER_APIKEY'),
                'default' => '',
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Twitter App Consumer Secret (API Secret)'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_TWITTER_APISECRET'),
                'default' => '',
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'form_group_class' => 'tab_social_login',
                'html_content' => '<div class="alert alert-info">'.$this->l('Configurations for quick login form').'</div>'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Redirect To My Account page'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE_REDIRECT'),
                'desc' => $this->l('After login or create new account success'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Checkbox Accept Terms and Condition'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE_CHECK_TERMS'),
                'desc' => $this->l('Check accept terms and condition for GDPR rule.'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Link terms and condition'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_LINK_TERMS'),
                'options' => array('query' => CMS::listCms(Context::getContext()->language->id, false, true),
                    'id' => 'id_cms',
                    'name' => 'meta_title'),
                'default' => '',
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Check Login Cookie'),
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE_CHECK_COOKIE'),
                'desc' => $this->l('Check browser cookie for the login session when the customer come back'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_social_login',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Lifetime Of Login Cookie'),
                'class' => 'fixed-width-lg',
                'default' => '28800',
                'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_LIFETIME_COOKIE'),
                'desc' => $this->l('Only for enable check login cookie. Default: 28800 minutes = 20 days. 1440 minutes = 1 days. Set 0 to keep login session until the customer logout'),
                'suffix' => $this->l('minutes'),
                'form_group_class' => 'tab_social_login',
            ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Enable Tab Navigation Style'),
            //     'name' => DeoHelper::getConfigName('SOCIAL_LOGIN_ENABLE_TAB_NAVIGATION'),
            //     'is_bool' => true,
            //     'values' => DeoSetting::returnYesNo(),
            //     'desc' => $this->l('Only display with one form layout'),
            //     'form_group_class' => 'tab_social_login',
            // ),
        );
        
        // Options for switch elements
        $zoom_option = array();
        for ($i = 1; $i <= 20; $i++) {
            $zoom_option[] = array('id' => $i, 'value' => $i);
        }

        // Get all store of shop
        $base_model = new DeoTemplateModel();
        $data_list_store = $base_model->getAllStoreByShop();
        $inputs_google_map = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Google Map'),
                'name' => DeoHelper::getConfigName('ENABLE_GOOGLE_MAP'),
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Disable it when you dose not use it to increase speed your site'),
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Google Key'),
                'name' => DeoHelper::getConfigName('API_KEY_GOOGLE_MAP'),
                'desc' => $this->l('Example: AIzaSyCWJmaoDNR_l3GVkP6uRnMzsGG5iuuU_AM'),
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Configurations google map for Contact Page').'</div>',
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Google Map on Contact Page'),
                'name' => DeoHelper::getConfigName('ENABLE_GOOGLE_MAP_CONTACT_PAGE'),
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => DeoHelper::getConfigName('WIDTH_GOOGLE_MAP_CONTACT_PAGE'),
                'desc' => $this->l('Example: 100%, 100px'),
                'default' => '100%',
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => DeoHelper::getConfigName('HEIGHT_GOOGLE_MAP_CONTACT_PAGE'),
                'desc' => $this->l('Example: 100%, 100px'),
                'default' => '300px',
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Zoom'),
                'name' => DeoHelper::getConfigName('ZOOM_GOOGLE_MAP_CONTACT_PAGE'),
                'default' => '11',
                'options' => array(
                    'query' => $zoom_option,
                    'id' => 'id',
                    'name' => 'value'
                ),
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show list stores on Contact Page'),
                'name' => DeoHelper::getConfigName('ENABLE_STORE_ON_MAP_CONTACT_PAGE'),
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_google_map',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show select store on Contact Page'),
                'name' => DeoHelper::getConfigName('SHOW_SELECT_STORE_ON_MAP_CONTACT_PAGE'),
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('NO: select all stores'),
                'form_group_class' => 'tab_google_map show_select_store',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Uncheck to show all stores').'</div>',
                'form_group_class' => 'tab_google_map group_show_select_store',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('List stores'),
                'desc' => $this->l('Press key Shift to select multiple store').'<br>'.$this->l('Uncheck to show all stores'),
                'name' => DeoHelper::getConfigName('LIST_STORE_CONTACT_PAGE').'[]',
                'multiple' => true,
                'options' => array(
                    'query' => $data_list_store,
                    'id' => 'id_store',
                    'name' => 'name'
                ),
                'default' => 'all',
                'form_group_class' => 'tab_google_map group_show_select_store',
            ),
        );

        $inputs_rtl = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button class="button btn btn-danger" name="submit_rtl_prestashop" id="submit_rtl_prestashop" type="submit">1. Generate RTL stylesheet</button>',
                'form_group_class' => 'tab_rtl',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button class="button btn btn-success" name="submit_rtl" id="submit_rtl" type="submit">2. Use class RTL of theme</button>',
                'form_group_class' => 'tab_rtl',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('1. Generate RTL stylesheet :  create file *_rtl.css in current theme like Prestashop default.').'</br>'.$this->l('2. Use class RTL of theme and delete all *_rtl.css file in current theme.').'</div>',
                'form_group_class' => 'tab_rtl',
            ),
        );

        if (!(int) DeoHelper::getConfig('DEBUG_MODE')){
            $inputs_rtl = array();
            $inputs_data_sample = array();
            unset($tabs['tab_data_sample']);
        }

        if (version_compare(Configuration::get('PS_VERSION_DB'), '1.7.3.0', '>=') && (int) DeoHelper::getConfig('DEBUG_MODE')) {
            $tabs = array_merge($tabs, array('tab_rtl' => $this->l('Righ to Left')));
            $inputs = array_merge($inputs_general, $inputs_category_page, $inputs_product_list_setting, $inputs_data_sample, $inputs_ajax_cart, $inputs_infinite_scroll, $inputs_review, $inputs_compare, $inputs_wishlist, $inputs_social_login, $inputs_google_map, $inputs_child_theme, $inputs_rtl);
        }else{
            $inputs = array_merge($inputs_general, $inputs_category_page, $inputs_product_list_setting, $inputs_data_sample, $inputs_ajax_cart, $inputs_infinite_scroll, $inputs_review, $inputs_compare, $inputs_wishlist, $inputs_social_login, $inputs_google_map, $inputs_child_theme);
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

        $fields_form = array(
            'input' => $inputs,
            'submit' => array(
                'class' => 'btn btn-default pull-right '.get_class($this),
                'title' => $this->l('Save'),
            ),
        );

        // $theme_customizations = DeoFrameworkHelper::getLayoutSettingByTheme($this->themeName);
        // if (isset($theme_customizations['layout'])) {
        //     foreach ($theme_customizations['layout'] as $key => $value) {
        //         $o = array(
        //             'label' => $this->module->getTranslator()->trans((isset($value['title']) ? $value['title'] : $key)),
        //             'name' => DeoHelper::getConfigName(trim($key)),
        //             'default' => $value['default'],
        //             'type' => 'select',
        //             'options' => array(
        //                 'query' => $value['option'],
        //                 'id' => 'id',
        //                 'name' => 'name'
        //             ),
        //             'desc' => isset($value['desc']) ? $this->module->getTranslator()->trans($value['desc']) : null,
        //             'form_group_class' => 'tab_general',
        //         );
        //         array_push($fields_form['input'], $o);
        //     }
        // }

        $this->fields_form = $fields_form;
        $this->tpl_form_vars['backup_dir'] = $sample->backup_dir;
        
        if ($this->submitSaveSetting && Tools::isSubmit('submitAddconfiguration')) {
            # SAVING CONFIGURATION
            $this->saveThemeConfigs();
            $this->confirmations[] = 'Your configurations have been saved successfully.';
        }
        return parent::renderForm();
    }
    
    public function postProcess()
    {
        if (count($this->errors) > 0) {
            return;
        }
        $dataSample = new DeoDataSample();
        if (Tools::isSubmit('submitRestore')) {
            $dataSample->restoreBackUpFile();
            $this->confirmations[] = 'Restore from PHP file is successful.';
        } else if (Tools::isSubmit('submitSample')) {
            $dataSample->processSample();
            $folder = str_replace('\\', '/', _PS_ALL_THEMES_DIR_.DeoHelper::getThemeName().'/samples/');
            $this->confirmations[] = 'Export Sample Data is successful. <br/>' . $folder;
        } else if (Tools::isSubmit('submitImport')) {
            $dataSample->processImport();
            $this->confirmations[] = 'Restore Sample Data is successful.';
        } else if (Tools::isSubmit('submitExportDBStruct')) {
            $dataSample->exportDBStruct();
            $dataSample->exportThemeSql();
            $folder = str_replace('\\', '/', _PS_MODULE_DIR_.'deotemplate/install');
            $this->confirmations[] = 'Export Data Struct is successful. <br/>' . $folder;
        } else if (Tools::isSubmit('submitImportDataHosting')) {
            $dataSample->importDataHosting();
            Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token'));
        } else if (Tools::isSubmit('submitUpdateModule')) {
            DeoHelper::processCorrectModule();
            $this->confirmations[] = 'Update and Correct Module is successful.';
        } else if (Tools::isSubmit('submitCompressImage')) {
            DeoHelper::processCompressImages();
            $this->confirmations[] = 'Compress images successful.';
        } else if (Tools::isSubmit('submitImportSampleData')) {
            $dataSample->processImport('deotemplate');
            Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token'));
        } else if (Tools::isSubmit('submit_rtl_prestashop')) {
            $this->generateRTL();
            $this->confirmations[] = DeoHelper::getThemeName() . ' theme generate RTL stylesheet';
        } else if (Tools::isSubmit('submit_rtl')) {
            $this->removeRTL();
            $this->confirmations[] = DeoHelper::getThemeName() . ' theme use class RTL of theme';
        } else if (Tools::isSubmit('submitCopyImageChildTheme')) {
            DeoHelper::processCopyImageChildTheme();
            $this->confirmations[] = 'Copy image for child theme is successful.';
        } else if (Tools::isSubmit('submitCorrectChildTheme')) {
            DeoHelper::processCorrectChildTheme();
            $this->confirmations[] = 'Correct child theme is successful.';
        } else if (Tools::isSubmit('submitAddconfiguration')) {
            $this->saveThemeConfigsBefore();
            $this->submitSaveSetting = true; 
        } else if (Tools::isSubmit('debug')) {
            DeoHelper::processDebugMode(Tools::getValue('debug'));
            Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token'));
        }
    }
    
    public function generateRTL()
    {
        $theme_name = DeoHelper::getThemeName();
        Language::getRtlStylesheetProcessor()
                ->setProcessFOThemes(array($theme_name))
                ->setRegenerate(true)
                ->process();
    }
    
    public function removeRTL()
    {
        $directory = _PS_ALL_THEMES_DIR_.Context::getContext()->shop->theme_name;
        $allFiles = Tools::scandir($directory, 'css', '', true);

        foreach ($allFiles as $key => $file) {
            if (Tools::substr(rtrim($file, '.css'), -4) !== '_rtl') {
                unset($allFiles[$key]);
            }
        }
        # REMOVE FILE _rtl
        foreach ($allFiles as $key => $file) {
            unlink($directory . DIRECTORY_SEPARATOR . $file);
        }
    }
    
    public function saveThemeConfigsBefore()
    {
        //$helper = DeoFrameworkHelper::getInstance();

        // SET COOKIE AGAIN
        $theme_cookie_name = DeoHelper::getConfigName('PANEL_CONFIG');
        $arrayConfig = array('LAZYLOAD','DEFAULT_SKIN','STICKEY_MENU','PRIMARY_CUSTOM_COLOR_SKIN','SECOND_CUSTOM_COLOR_SKIN','PRIMARY_CUSTOM_FONT','SECOND_CUSTOM_FONT');
        # Remove value in cookie
        foreach ($arrayConfig as $value) {
            if (DeoHelper::getConfig('PANELTOOL')){
                setcookie($theme_cookie_name.'_'.$value, Tools::getValue(DeoHelper::getConfigName($value)), time() + (86400 * 30), '/');
            }else{
                unset($_COOKIE[$theme_cookie_name.'_'.$value]);
                setcookie($theme_cookie_name.'_'.$value, '', 0, '/');  
            }
        }
    }
    
    /**
     * alias from DeoHelper::getConfigName()
     */
    public function getConfigName($name)
    {
        return DeoHelper::getConfigName($name);
    }
    
    /**
     * Update Theme Configurations
     */
    public function saveThemeConfigs()
    {
        $languages = Language::getLanguages(false);
        $content_setting = '';
        
        foreach ($this->fields_form['input'] as $input) {
            if (isset($input['lang']) && $input['lang']) {
                $data = array();
                foreach ($languages as $lang) {
                    $value = Tools::getValue(trim($input['name']).'_'.$lang['id_lang']);
                    $data[$lang['id_lang']] = $value ? $value : $input['default'];
                }
                DeoHelper::updateValue(trim($input['name']), $data);
            } else {
                if (isset($input['save']) && $input['save']) {
                    // NOT SAVE
                } else {
                    if (Tools::strpos($input['name'], '[]')){
                        $input_name_conf = Tools::str_replace_once('[]', '', $input['name']);
                        $value = (!empty(Tools::getValue(trim($input_name_conf)))) ? Tools::getValue(trim($input_name_conf)) : array();
                        $value = json_encode($value);
                        DeoHelper::updateValue(trim($input_name_conf), $value);
                    }else{
                        $value = Tools::getValue(trim($input['name']), DeoHelper::get($input['name']));
                        DeoHelper::updateValue(trim($input['name']), $value);
                    }
                }

                if (trim($input['name']) == DeoHelper::getConfigName('GRID_MODE')) {
                    if (trim($value) == '') {
                        $value = 'grid';
                    }
                    $content_setting .= '{assign var="GRID_MODE" value="'.$value.'" scope="global"}'."\n";
                }elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_MODULE')) {
                    $value = (trim($value) == '') ? '4' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_MODULE" value="'.$value.'" scope="global"}'."\n";
                }
                // START: layout fullwidth
                elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP')) {
                    $value = (trim($value) == '') ? '4' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_LARGE_DESKTOP" value="'.$value.'" scope="global"}'."\n";
                }elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP')) {
                    $value = (trim($value) == '') ? '4' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_DESKTOP" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP')) {
                    $value = (trim($value) == '') ? '3' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_DESKTOP" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET')) {
                    $value = (trim($value) == '') ? '3' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_TABLET" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_TABLET" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_MOBILE" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE')) {
                    $value = (trim($value) == '') ? '1' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_MOBILE" value="'.$value.'" scope="global"}'."\n";
                }
                // End: layout fullwidth
                // START: layout sidebar
                elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP_SIDEBAR')) {
                    $value = (trim($value) == '') ? '3' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_LARGE_DESKTOP_SIDEBAR" value="'.$value.'" scope="global"}'."\n";
                }elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP_SIDEBAR')) {
                    $value = (trim($value) == '') ? '3' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_DESKTOP_SIDEBAR" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP_SIDEBAR')) {
                    $value = (trim($value) == '') ? '3' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_DESKTOP_SIDEBAR" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET_SIDEBAR')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_TABLET_SIDEBAR" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET_SIDEBAR')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_TABLET_SIDEBAR" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE_SIDEBAR')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_MOBILE_SIDEBAR" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE_SIDEBAR')) {
                    $value = (trim($value) == '') ? '1' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_MOBILE_SIDEBAR" value="'.$value.'" scope="global"}'."\n";
                }
                // END: layout sidebar
                // START: layout both
                elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_LARGE_DESKTOP_BOTH')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_LARGE_DESKTOP_BOTH" value="'.$value.'" scope="global"}'."\n";
                }elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_DESKTOP_BOTH')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_DESKTOP_BOTH" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_DESKTOP_BOTH')) {
                    $value = (trim($value) == '') ? '3' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_DESKTOP_BOTH" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_TABLET_BOTH')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_TABLET_BOTH" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_TABLET_BOTH')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_TABLET_BOTH" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_MOBILE_BOTH')) {
                    $value = (trim($value) == '') ? '2' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_MOBILE_BOTH" value="'.$value.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == DeoHelper::getConfigName('NUMBER_PRODUCT_SMALL_MOBILE_BOTH')) {
                    $value = (trim($value) == '') ? '1' : $value;
                    $content_setting .= '{assign var="NUMBER_PRODUCT_SMALL_MOBILE_BOTH" value="'.$value.'" scope="global"}'."\n";
                }
                // End: layout both
                elseif (trim($input['name']) == DeoHelper::getConfigName('ENABLE_RESPONSIVE')) {
                    # validate module
                    $content_setting .= '{assign var="ENABLE_RESPONSIVE" value="'.$value.'" scope="global"}'."\n";
                }
            }
        }
        
        // write cate for layout
        $folder = $this->themePath.'templates/layouts/';
        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }
        DeoFrameworkHelper::writeToCache($this->themePath.'templates/layouts/', 'setting', DeoHelper::getLicenceTPL()."\n\n".$content_setting, 'tpl');

        // write cache for skin
        $primary_color = Tools::getValue(DeoHelper::getConfigName('PRIMARY_CUSTOM_COLOR_SKIN'));
        $second_color = Tools::getValue(DeoHelper::getConfigName('SECOND_CUSTOM_COLOR_SKIN'));
        if (Tools::getValue(DeoHelper::getConfigName('DEFAULT_SKIN')) == 'custom-skin' && $second_color && $primary_color){
            $rgb_primary_color = preg_match('/\((.*?)\)/i', DeoHelper::convertHexToRgb($primary_color), $match_primary_color);
            $rgb_second_color = preg_match('/\((.*?)\)/i', DeoHelper::convertHexToRgb($second_color), $match_second_color);
            $uri_skin_original = DeoHelper::getCssDir().'skins/skin-color.css';
            $file_skin = Tools::file_get_contents(DeoHelper::getThemeDir().'/'.$uri_skin_original);
            $file_skin = str_replace(["#1bbc9b", "#169a7f", "27, 188, 155", "22, 154, 127"], [$primary_color, $second_color, $match_primary_color[1], $match_second_color[1]], $file_skin);
            if ($file_content = $file_skin){
                DeoSetting::writeFile(DeoHelper::getThemeDir().DeoHelper::getCssDir().'skins', 'skin-custom.css', $file_content);
            }
        }


        // write cate for font
        $primary_font = Tools::getValue(DeoHelper::getConfigName('PRIMARY_CUSTOM_FONT'));
        $second_font = Tools::getValue(DeoHelper::getConfigName('SECOND_CUSTOM_FONT'));
        if ($primary_font && $second_font){
            $uri_font_original = DeoHelper::getCssDir().'skins/skin-font.css';
            $file_font = Tools::file_get_contents(DeoHelper::getThemeDir().'/'.$uri_font_original);
            $file_font = str_replace(["font-family-base", "font-family-heading"], [$primary_font, $second_font], $file_font);
            if ($file_content = $file_font){
                DeoSetting::writeFile(DeoHelper::getThemeDir().DeoHelper::getCssDir().'skins', 'font-custom.css', $file_content);
            }
        }
    }
    
    public function getFieldsValue($obj)
    {
        unset($obj);
        $languages = Language::getLanguages(false);
        foreach ($this->fields_form as $f) {
            foreach ($f['form']['input'] as $input) {

                if (isset($input['lang'])) {
                    foreach ($languages as $lang) {
                        if (Tools::getIsset($input['name'])){
                            $val = Tools::getValue($input['name']);
                        }else if (DeoHelper::hasKey($input['name'])){
                            $val = DeoHelper::get($input['name'], $lang['id_lang']);
                        }
                        $input['default'] = isset($input['default']) ? $input['default'] : '';
                        $this->fields_values[$input['name']][$lang['id_lang']] = isset($val) ? $val : $input['default'];
                    }
                } else {
                    if (Tools::strpos($input['name'], '[]')){
                        $input_name_conf = Tools::str_replace_once('[]', '', $input['name']);
                        if (Tools::getIsset($input_name_conf)){
                            $val = Tools::getValue($input_name_conf);
                        }else if (DeoHelper::hasKey($input_name_conf)){
                            $val = DeoHelper::get($input_name_conf);
                        }
                    }else{
                        if (Tools::getIsset($input['name'])){
                            $val = Tools::getValue($input['name']);
                        }else if (DeoHelper::hasKey($input['name'])){
                            $val = DeoHelper::get($input['name']);
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
}
