<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProductsModel.php');

class DeoProductCarousel extends DeoShortCodeBase
{
    public $name = 'DeoProductCarousel';

    public function getInfo()
    {
        return array(
            'label' => 'Product Carousel', 
            'position' => 3,
            'desc' => $this->l('You can select product from category'),
            'image' => 'product-carousel.png',
            'tag' => 'content slider',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getAdditionConfig()
    {
        return array(
            array(
                'type' => '',
                'name' => 'value_by_categories',
                'default' => '0'
            ),
            array(
                'type' => '',
                'name' => 'value_by_product_type',
                'default' => '0'
            ),
            array(
                'type' => '',
                'name' => 'value_by_manufacture',
                'default' => '0'
            ),
            array(
                'type' => '',
                'name' => 'value_by_supplier',
                'default' => '0'
            ),
            array(
                'type' => '',
                'name' => 'value_by_product_id',
                'default' => '0'
            ),
            array(
                'type' => '',
                'name' => 'value_by_tags',
                'default' => '0'
            )
        );
    }

    public function getConfigList()
    {
        $selected_categories = array();
        if (Tools::getIsset('categorybox')) {
            $category_box = Tools::getValue('categorybox');
            $selected_categories = explode(',', $category_box);
        }
        //get all manufacture
        $manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, true);
        $suppliers = Supplier::getSuppliers();
        $profile = new DeoTemplateProductsModel();
        $profile_list = $profile->getAllProductProfileByShop();
        $product_active = DeoTemplateProductsModel::getActive();
        $product_class = $product_active['class'];
        $data_class = array(array('plist_key' => 'default', 'class' => $product_class));
        foreach ($profile_list as $item) {
            $data_class[] = array('plist_key' => $item['plist_key'], 'class' => $item['class']);
        }
        $script = '<script>var productTemp = '.json_encode($data_class).';</script>';
        array_unshift($profile_list, array('plist_key' => 'default', 'name' => $this->l('Use Default')));
        $id_root_category = Context::getContext()->shop->getCategory();
        
        $inputs_head = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'default' => ''
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
            ),
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
        );
        
        //Owl Carousel
        $inputs_owlCarousel = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Items per Row').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'items',
                'label' => $this->l('Desktop Large'),
                'desc' => $this->l('Typing number of items. Default'),
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
                'default' => '5',
            ),
            array(
                'type' => 'text',
                'name' => 'itemsdesktop',
                'label' => $this->l('Desktop'),
                'desc' => $this->l('Typing number of items ( width screen < 1500 )'),
                'default' => '4',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemsdesktopsmall',
                'label' => $this->l('Desktop Small'),
                'desc' => $this->l('Typing number of items ( width screen < 1200 )'),
                'default' => '3',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemstablet',
                'label' => $this->l('Tablet'),
                'desc' => $this->l('Typing number of items ( width screen < 992 )'),
                'default' => '3',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemstabletsmall',
                'label' => $this->l('Tablet Small'),
                'desc' => $this->l('Typing number of items ( width screen < 768 )'),
                'default' => '2',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemsmobile',
                'label' => $this->l('Mobile'),
                'desc' => $this->l('Typing number of items ( width screen < 576 )'),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemssmallmobile',
                'label' => $this->l('Small Mobile'),
                'desc' => $this->l('Typing number of items ( width screen < 480 )'),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemscustom',
                'label' => $this->l('Items Custom'),
                'desc' => $this->l('(Advance User) Example: [[0, 1], [480, 2], [576, 3], [768, 4], [992, 5], [1200, 6]]. The format is [x,y] whereby x=browser width and y=number of slides displayed'),
                'default' => '',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itempercolumn',
                'label' => $this->l('Items per Column'),
                'desc' => $this->l('Number of item per one column. Same with number of line for one page'),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Effect').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Autoplay'),
                'name' => 'autoplay',
                'is_bool' => true,
                'desc' => $this->l('Yes - scroll per page. No - scroll per item. This affect next/prev buttons and mouse/touch dragging.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Stop on Hover'),
                'name' => 'stoponhover',
                'is_bool' => true,
                'desc' => $this->l('Stop autoplay on mouse hover'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Responsive'),
                'name' => 'responsive',
                'is_bool' => true,
                'desc' => $this->l('You can use Owl Carousel on desktop-only websites too! Just change that to "false" to disable resposive capabilities'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Navigation'),
                'name' => 'navigation',
                'is_bool' => true,
                'desc' => $this->l('Display "next" and "prev" buttons.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Auto Height'),
                'name' => 'autoheight',
                'is_bool' => true,
                'desc' => $this->l('Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Mouse Drag'),
                'name' => 'mousedrag',
                'is_bool' => true,
                'desc' => $this->l('On DeskTop - Turn off/on mouse events.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Touch Drag'),
                'name' => 'touchdrag',
                'is_bool' => true,
                'desc' => $this->l('On Mobile - Turn off/on touch events.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Scroll Mouse Wheel'),
                'name' => 'mousewheel',
                'is_bool' => true,
                'desc' => $this->l('On Mobile - Turn off/on scroll mouse wheel.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Icon When Loading'),
                'name' => 'showloading',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Lazy Load').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy Load'),
                'name' => 'lazyload',
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Delays loading of images. Images outside of viewport will not be loaded before user scrolls to them. Great for mobile devices to speed up page loadings'),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy Follow'),
                'name' => 'lazyfollow',
                'is_bool' => true,
                'desc' => $this->l('When pagination used, it skips loading the images from pages that got skipped. It only loads the images that get displayed in viewport. If set to false, all images get loaded when pagination used. It is a sub setting of the lazy load function.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel group_lazyload_owl',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Lazy Effect'),
                'name' => 'lazyeffect',
                'options' => array(
                    'query' => array(
                        array('id' => 'fade', 'name' => $this->l('fade')),
                        array('id' => 'false', 'name' => $this->l('No')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Default is fadeIn on 400ms speed. Use false to remove that effect.'),
                'default' => 'fade',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel group_lazyload_owl',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Pagination').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Pagination Enable'),
                'name' => 'pagination',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Show Pagination below owl-carousel.'),
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Pagination Numbers'),
                'name' => 'paginationnumbers',
                'is_bool' => true,
                'desc' => $this->l('Show numbers inside Pagination'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel group-pagination',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Scroll Per Page').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Scroll per Page'),
                'name' => 'scrollperpage',
                'is_bool' => true,
                'desc' => $this->l('Yes - scroll per Page. No - scroll per Item. This affect next/prev buttons and mouse/touch dragging.'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Scroll Page Speed'),
                'name' => 'paginationspeed',
                'desc' => $this->l('Time to next page. Ex 800 ( Milliseconds )'),
                'default' => '800',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel group-scroll-per-page',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Scroll Item Speed'),
                'name' => 'slidespeed',
                'desc' => $this->l('Time to next item. Ex 200 (Milliseconds)'),
                'default' => '200',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel group-scroll-per-page',
            ),
        );

        // boostrap carousel
        $inputs_bootstrapCarousel = array(
            array(
                'type' => 'text',
                'name' => 'nbitemsperpage',
                'label' => $this->l('Number of Items per Page'),
                'desc' => $this->l('How many product you want to display in a Page. Divisible by Item per Line (Desktop, Table, mobile)(default:12)'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
                'default' => '12',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space">'.$this->l('Items per Row').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_Desktop ( >= 1200 )'),
                'name' => 'nbitemsperline_desktop',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                        array('id' => '12', 'name' => $this->l('12 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 4'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_SmallDesktop ( >= 992 )'),
                'name' => 'nbitemsperline_smalldesktop',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                        array('id' => '12', 'name' => $this->l('12 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 3'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_Tablet ( >= 768 )'),
                'name' => 'nbitemsperline_tablet',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                        array('id' => '12', 'name' => $this->l('12 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 3'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_SmallDevices ( >= 576 )'),
                'name' => 'nbitemsperline_smalldevices',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 2'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_ExtraSmallDevices ( >= 480 )'),
                'name' => 'nbitemsperline_extrasmalldevices',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 1'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_Smartphone ( < 480 )'),
                'name' => 'nbitemsperline_smartphone',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 1'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'text',
                'name' => 'interval',
                'label' => $this->l('interval'),
                'desc' => $this->l('The amount of time to delay between automatically cycling an item. If false, carousel will not automatically cycle.'),
                'default' => '5000',
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
        );

        //Slick carousel
        $inputs_slickCarousel = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Vertical'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'name' => 'slick_vertical',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0'
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_autoplay',
                'label' => $this->l('Auto play'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'text',
                'name' => 'slick_autoplayspeed',
                'label' => $this->l('Speed auto play'),
                'desc' => $this->l('1000 milliseconds = 1 seconds'),
                'default' => '10000',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel group_slick_autoplay',
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_pauseonhover',
                'label' => $this->l('Pause on Hover'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_mousewheel',
                'label' => $this->l('Scroll Mouse Wheel'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_loopinfinite',
                'label' => $this->l('Loop Infinite'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_arrows',
                'label' => $this->l('Prev/Next Arrows'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_dot',
                'label' => $this->l('Show dot indicators'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_autoheight',
                'label' => $this->l('Auto Height'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_showloading',
                'label' => $this->l('Show Icon When Loading'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_fade',
                'label' => $this->l('Effect Fade'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l('Warning: Only work fine when show one slide'),
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_lazyload',
                'label' => $this->l('Lazyload'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '1',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'select',
                'name' => 'slick_lazyload_type',
                'label' => $this->l('Lazyload Effect'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel group_lazyload_slick',
                'default' => 'ondemand',
                'options' => array('query' => array(
                        array('id' => 'ondemand', 'name' => $this->l('ondemand')),
                        array('id' => 'progressive', 'name' => $this->l('progressive')),
                    ),
                    'id' => 'id',
                    'name' => 'name')
            ),
            array(
                'type' => 'html',
                'name' => 'calculate_rate_image',
                'html_content' => '<p class="help-block html">progressive: Loads the visible image as soon as the page is displayed and the other ones after everything else is loaded in the background.</p><p class="help-block html">on-demand: Loads the visible image as soon as the page is displayed and the other ones only when they are displayed.</p>',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel group_lazyload_slick description',
            ),
            array(
                'type' => 'switch',
                'name' => 'slick_centermode',
                'label' => $this->l('Center mode'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'default' => '0',
                'values' => DeoSetting::returnYesNo(),
            ),
            array(
                'type' => 'text',
                'name' => 'slick_row',
                'label' => $this->l('Num Row'),
                'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'slick_slidestoshow',
                'label' => $this->l('Slides To Show'),
                'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
                'default' => '5',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'slick_slidestoscroll',
                'label' => $this->l('Slides To Scroll'),
                'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'slick_items_custom',
                'label' => $this->l('Display responsive for other screen'),
                'desc' => $this->l('Example: [[1200, 5],[992, 4],[768, 3], [576, 2],[480, 1]]. The format is [x,y] whereby x=browser width and y=number of slides displayed'),
                'default' => '[[1200, 5],[992, 4],[768, 3], [576, 2],[480, 1]]',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
        );

        $inputs_content = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $script.'<div class="alert alert-info">'.$this->l('Step 1: Product Filter').'</div>',
            ),
            array(
                'type' => 'checkbox',
                'name' => 'value_by',
                'label' => $this->l('Select By'),
                'class' => 'checkbox-group',
                'desc' => $this->l('Select Product Condition'),
                'values' => array(
                    'query' => array(
                        array(
                            'id' => 'categories',
                            'name' => $this->l('Categories'),
                            'val' => '1'
                        ),
                        array(
                            'id' => 'product_type',
                            'name' => $this->l('Product Type'),
                            'val' => '1'
                        ),
                        array(
                            'id' => 'manufacture',
                            'name' => $this->l('Manufacture'),
                            'val' => '1'
                        ),
                        array(
                            'id' => 'supplier',
                            'name' => $this->l('Supplier'),
                            'val' => '1'
                        ),
                        array(
                            'id' => 'product_id',
                            'name' => $this->l('Product ID'),
                            'val' => '1'
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'categories',
                'label' => $this->l('Select Category'),
                'name' => 'categorybox',
                'desc' => $this->l('You can select one or more, if not select we will not search by category'),
                'tree' => array(
                    'root_category' => $id_root_category,
                    'use_search' => false,
                    'id' => 'categorybox',
                    'use_checkbox' => true,
                    'selected_categories' => $selected_categories,
                ),
                'form_group_class' => 'value_by_categories',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product of Category'),
                'name' => 'category_type',
                'options' => array(
                    'query' => array(
                        array('id' => 'all', 'name' => $this->l('Get All Product of Category')),
                        array('id' => 'default', 'name' => $this->l('Get Product if category is default category of product'))),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'value_by_categories',
                'default' => 'all'
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="sperator"></div>',
                'form_group_class' => 'value_by_categories',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product Type'),
                'name' => 'product_type',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'all',
                            'name' => $this->l('All Product'),
                        ),
                        array(
                            'id' => 'new_product',
                            'name' => $this->l('New Product'),
                        ),
                        array(
                            'id' => 'best_sellers',
                            'name' => $this->l('Best Sellers'),
                        ),
                        array(
                            'id' => 'price_drop',
                            'name' => $this->l('Price Drop'),
                        ),
                        array(
                            'id' => 'home_featured',
                            'name' => $this->l('Home Featured'),
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'value_by_product_type',
                'default' => 'all',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="sperator"></div>',
                'form_group_class' => 'value_by_product_type',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Manufacture'),
                'name' => 'manufacture[]',
                'multiple' => true,
                'options' => array(
                    'query' => $manufacturers,
                    'id' => 'id_manufacturer',
                    'name' => 'name'
                ),
                'default' => 'all',
                'desc' => $this->l('Press "Ctrl" and "Mouse Left Click" to choose many items'),
                'form_group_class' => 'value_by_manufacture',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="sperator"></div>',
                'form_group_class' => 'value_by_manufacture',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Supplier'),
                'name' => 'supplier[]',
                'multiple' => true,
                'options' => array(
                    'query' => $suppliers,
                    'id' => 'id_supplier',
                    'name' => 'name'
                ),
                'desc' => $this->l('Press "Ctrl" and "Mouse Left Click" to choose many items'),
                'form_group_class' => 'value_by_supplier',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="sperator"></div>',
                'form_group_class' => 'value_by_supplier',
            ),
            array(
                'type' => 'text',
                'name' => 'product_id',
                'label' => $this->l('Product ID'),
                'desc' => $this->l('Show product follow product id. Example: 1 or 1,2,3,4 '),
                'default' => '',
                'form_group_class' => 'value_by_product_id',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="sperator"></div>',
                'form_group_class' => 'value_by_product_id',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Step 2: Product Order And Limit').'</div>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Order Way'),
                'class' => 'form-action',
                'name' => 'order_way',
                'options' => array(
                    'query' => array(
                        array('id' => 'asc', 'name' => $this->l('Ascending')),
                        array('id' => 'desc', 'name' => $this->l('Decreasing')),
                        array('id' => 'random', 'name' => $this->l('Random'))),        // remove to increase speed
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'all'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Order By'),
                'name' => 'order_by',
                'options' => array(
                    'query' => array(
                            array(
                                'id' => 'id_product', 'name' => self::l('ID')),
                            array(
                                'id' => 'name', 'name' => self::l('Name')),
                            array(
                                'id' => 'reference', 'name' => self::l('Reference')),
                            array(
                                'id' => 'price', 'name' => self::l('Base price')),
                            array(
                                'id' => 'date_add', 'name' => self::l('Date add')),
                            array(
                                'id' => 'date_upd', 'name' => self::l('Date update')
                            ),
                            array(
                                'id' => 'quantity', 'name' => self::l('Sales (only for Best Sales)')
                            ),
                        ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'order_type_sub order_type-asc order_type-desc',
                'default' => 'all'
            ),
            array(
                'type' => 'text',
                'name' => 'nb_products',
                'label' => $this->l('Limit'),
                'default' => '10',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Step 3: Carousel Setting').'</div>'
            ),
            // array(
            //     'type' => 'select',
            //     'label' => $this->l('Carousel Type'),
            //     'class' => 'form-action',
            //     'name' => 'carousel_type',
            //     'options' => array(
            //         'query' => array(
            //             array('id' => 'slickcarousel', 'name' => $this->l('Slick Carousel')),
            //             array('id' => 'owlcarousel', 'name' => $this->l('Owl Carousel')),
            //             array('id' => 'boostrap', 'name' => $this->l('Bootstrap')),
            //         ),
            //         'id' => 'id',
            //         'name' => 'name'
            //     ),
            //     'default' => 'slickcarousel'
            // ),
        );

        $inputs_footer = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Step 4: Product Template').'</div>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product Template'),
                'name' => 'profile',
                'options' => array(
                    'query' => $profile_list,
                    'id' => 'plist_key',
                    'name' => 'name'
                ),
                'default' => 'all'
            )
        );

        // $inputs = array_merge($inputs_head, $inputs_content, $inputs_owlCarousel, $inputs_bootstrapCarousel, $inputs_slickCarousel, $inputs_footer);
        $inputs = array_merge($inputs_head, $inputs_content, $inputs_slickCarousel, $inputs_footer);

        return $inputs;
    }
    
    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
        
        // KEEP OLD DATA
        // if (Tools::getIsset('nbitemsperline') && Tools::getValue('nbitemsperline')) {
        //     $this->helper->tpl_vars['fields_value']['nbitemsperline_desktop'] = Tools::getValue('nbitemsperline');
        //     $this->helper->tpl_vars['fields_value']['nbitemsperline_smalldesktop'] = Tools::getValue('nbitemsperline');
        //     $this->helper->tpl_vars['fields_value']['nbitemsperline_tablet'] = Tools::getValue('nbitemsperline');
        // }
        
        // if (Tools::getIsset('nbitemsperlinetablet') && Tools::getValue('nbitemsperlinetablet')) {
        //     $this->helper->tpl_vars['fields_value']['nbitemsperline_smalldevices'] = Tools::getValue('nbitemsperlinetablet');
        // }
        
        // if (Tools::getIsset('nbitemsperlinemobile') && Tools::getValue('nbitemsperlinemobile')) {
        //     $this->helper->tpl_vars['fields_value']['nbitemsperline_extrasmalldevices'] = Tools::getValue('nbitemsperlinemobile');
        //     $this->helper->tpl_vars['fields_value']['nbitemsperline_smartphone'] = Tools::getValue('nbitemsperlinemobile');
        // }
    }

    public function prepareFontContent($assign, $module = null)
    {
        if (!DeoHelper::getLazyload()) {
            $assign['formAtts']['lazyload'] = 0;
            $assign['formAtts']['slick_lazyload'] = 0;
        }
        // if (isset($assign['formAtts']['carousel_type']) && $assign['formAtts']['carousel_type'] == 'owlcarousel') {
        //     if (!(int) DeoHelper::getConfig('LOAD_LIBRARY_OWL_CAROUSEL')) {
        //         $assign['formAtts']['lib_has_error'] = true;
        //         $assign['formAtts']['lib_error'] = 'Can not show Product Carousel. Please enable Owl Carousel library.';
        //         return $assign;
        //     }
        // }

        $assign['products'] = $module->getProductsFont($assign['formAtts']);
        // print_r($products);
    
        $assign['productClassWidget'] = $this->getProductClassByPListKey($assign['formAtts']['profile']);
        
        if (isset($assign['formAtts']['profile']) && $assign['formAtts']['profile'] != 'default' && file_exists(DeoHelper::getConfigDir('theme_products').$assign['formAtts']['profile'] . '.tpl')) {
            $assign['product_item_path'] = DeoHelper::getConfigDir('theme_products') . $assign['formAtts']['profile'].'.tpl';
        } else {
            // Default load file in theme
            $assign['product_item_path'] = 'catalog/_partials/miniatures/product.tpl';
        }

        // if ($assign['formAtts']['carousel_type'] == 'boostrap') {
        //     if (isset($assign['formAtts']['nbitemsperline']) && $assign['formAtts']['nbitemsperline']) {
        //         $assign['formAtts']['nbitemsperline_desktop'] = $assign['formAtts']['nbitemsperline'];
        //         $assign['formAtts']['nbitemsperline_smalldesktop'] = $assign['formAtts']['nbitemsperline'];
        //         $assign['formAtts']['nbitemsperline_tablet'] = $assign['formAtts']['nbitemsperline'];
        //     }
        //     if (isset($assign['formAtts']['nbitemsperlinetablet']) && $assign['formAtts']['nbitemsperlinetablet']) {
        //         $assign['formAtts']['nbitemsperline_smalldevices'] = $assign['formAtts']['nbitemsperlinetablet'];
        //     }
        //     if (isset($assign['formAtts']['nbitemsperlinemobile']) && $assign['formAtts']['nbitemsperlinemobile']) {
        //         $assign['formAtts']['nbitemsperline_extrasmalldevices'] = $assign['formAtts']['nbitemsperlinemobile'];
        //         $assign['formAtts']['nbitemsperline_smartphone'] = $assign['formAtts']['nbitemsperlinemobile'];
        //     }
            
        //     $assign['formAtts']['nbitemsperline_desktop'] = isset($assign['formAtts']['nbitemsperline_desktop']) && $assign['formAtts']['nbitemsperline_desktop']  ? (int)$assign['formAtts']['nbitemsperline_desktop'] : 4;
        //     $assign['formAtts']['nbitemsperline_smalldesktop'] = isset($assign['formAtts']['nbitemsperline_smalldesktop']) && $assign['formAtts']['nbitemsperline_smalldesktop'] ? (int)$assign['formAtts']['nbitemsperline_smalldesktop'] : 4;
        //     $assign['formAtts']['nbitemsperline_tablet'] = isset($assign['formAtts']['nbitemsperline_tablet']) && $assign['formAtts']['nbitemsperline_tablet'] ? (int)$assign['formAtts']['nbitemsperline_tablet'] : 3;
        //     $assign['formAtts']['nbitemsperline_smalldevices'] = isset($assign['formAtts']['nbitemsperline_smalldevices']) && $assign['formAtts']['nbitemsperline_smalldevices'] ? (int)$assign['formAtts']['nbitemsperline_smalldevices'] : 2;
        //     $assign['formAtts']['nbitemsperline_extrasmalldevices'] = isset($assign['formAtts']['nbitemsperline_extrasmalldevices']) && $assign['formAtts']['nbitemsperline_extrasmalldevices'] ? (int)$assign['formAtts']['nbitemsperline_extrasmalldevices'] : 1;
        //     $assign['formAtts']['nbitemsperline_smartphone'] = isset($assign['formAtts']['nbitemsperline_smartphone']) && $assign['formAtts']['nbitemsperline_smartphone'] ? (int)$assign['formAtts']['nbitemsperline_smartphone'] : 1;
            
        //     $assign['tabname'] = 'carousel-'.DeoSetting::getRandomNumber();
        //     $assign['itemsperpage'] = (int)$assign['formAtts']['nbitemsperpage'];
        //     $assign['nbItemsPerLine'] = (int)$assign['formAtts']['nbitemsperline_desktop'];
            
        //     $assign['scolumn'] = '';
            
        //     if ($assign['formAtts']['nbitemsperline_desktop'] == '5') {
        //         $assign['scolumn'] .= ' col-xl-2-4';
        //     } else {
        //         $assign['scolumn'] .= ' col-xl-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_desktop']));
        //     }
            
        //     if ($assign['formAtts']['nbitemsperline_smalldesktop'] == '5') {
        //         $assign['scolumn'] .= ' col-lg-2-4';
        //     } else {
        //         $assign['scolumn'] .= ' col-lg-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smalldesktop']));
        //     }
            
        //     if ($assign['formAtts']['nbitemsperline_tablet'] == '5') {
        //         $assign['scolumn'] .= ' col-md-2-4';
        //     } else {
        //         $assign['scolumn'] .= ' col-md-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_tablet']));
        //     }
            
        //     if ($assign['formAtts']['nbitemsperline_smalldevices'] == '5') {
        //         $assign['scolumn'] .= ' col-sm-2-4';
        //     } else {
        //         $assign['scolumn'] .= ' col-sm-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smalldevices']));
        //     }
            
        //     if ($assign['formAtts']['nbitemsperline_extrasmalldevices'] == '5') {
        //         $assign['scolumn'] .= ' col-xs-2-4';
        //     } else {
        //         $assign['scolumn'] .= ' col-xs-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_extrasmalldevices']));
        //     }
            
        //     if ($assign['formAtts']['nbitemsperline_smartphone'] == '5') {
        //         $assign['scolumn'] .= ' col-sp-2-4';
        //     } else {
        //         $assign['scolumn'] .= ' col-sp-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smartphone']));
        //     }
        // }
        
        // create data for owl carousel with item custom
        // if ($assign['formAtts']['carousel_type'] == 'owlcarousel') {
        //     if (isset($assign['formAtts']['itemscustom']) && $assign['formAtts']['itemscustom'] != '') {
        //         $array_item_custom = json_decode($assign['formAtts']['itemscustom']);
        //         $array_item_custom_tmp = array();
        //         foreach ($array_item_custom as $array_item_custom_val) {
        //             $size_window = $array_item_custom_val[0];
        //             $number_item = $array_item_custom_val[1];
        //             if (0 <= $size_window && $size_window < 480) {
        //                 $array_item_custom_tmp['sp'] = $number_item;
        //             } else if (480 <= $size_window && $size_window < 576) {
        //                 $array_item_custom_tmp['xs'] = $number_item;
        //             } else if (576 <= $size_window && $size_window < 768) {
        //                 $array_item_custom_tmp['sm'] = $number_item;
        //             } else if (768 <= $size_window && $size_window < 992) {
        //                 $array_item_custom_tmp['md'] = $number_item;
        //             } else if (992 <= $size_window && $size_window < 1200) {
        //                 $array_item_custom_tmp['lg'] = $number_item;
        //             } else if (1200 <= $size_window && $size_window < 1500) {
        //                 $array_item_custom_tmp['xl'] = $number_item;
        //             } else if ($size_window >= 1500) {
        //                 $array_item_custom_tmp['xxl'] = $number_item;
        //             }
        //         };
        //         $assign['formAtts']['array_fake_item'] = $array_item_custom_tmp;
        //     }else{
        //         // build data for fake item loading
        //         $array_fake_item = array();
        //         $array_fake_item['sp'] = $assign['formAtts']['itemssmallmobile'];
        //         $array_fake_item['xs'] = $assign['formAtts']['itemsmobile'];
        //         $array_fake_item['sm'] = $assign['formAtts']['itemstabletsmall'];
        //         $array_fake_item['md'] = $assign['formAtts']['itemstablet'];
        //         $array_fake_item['lg'] = $assign['formAtts']['itemsdesktopsmall'];
        //         $array_fake_item['xl'] = $assign['formAtts']['itemsdesktop'];
        //         $array_fake_item['xxl'] = $assign['formAtts']['items'];
        //         $assign['formAtts']['array_fake_item'] = $array_fake_item;
        //     }
        // }
            
        // if ($assign['formAtts']['carousel_type'] == 'slickcarousel') {
            if (isset($assign['formAtts']['slick_items_custom'])) {
                $assign['formAtts']['slick_items_custom'] = str_replace($this->str_search, $this->str_relace, $assign['formAtts']['slick_items_custom']);
            }
            if (isset($assign['formAtts']['slick_custom'])) {
                $str_relace = array('&', '\"', '\'', '', '', '', '[', ']', '+', '{', '}');
                $assign['formAtts']['slick_custom'] = str_replace($this->str_search, $str_relace, $assign['formAtts']['slick_custom']);
            }
            if (isset($assign['formAtts']['slick_items_custom'])) {
                $assign['formAtts']['slick_items_custom'] = json_decode($assign['formAtts']['slick_items_custom']);
            }
        
            // build data for fake item loading
            if (isset($assign['formAtts']['slick_items_custom']) && $assign['formAtts']['slick_items_custom'] != '') {
                $array_item_custom_tmp = array();
                $array_item_custom = $assign['formAtts']['slick_items_custom'];
                $array_item_custom_tmp['xl'] = $assign['formAtts']['slick_slidestoshow'];
                foreach ($array_item_custom as $array_item_custom_val) {
                    $size_window = $array_item_custom_val[0];
                    $number_item = $array_item_custom_val[1];
                    if ($size_window <= 480) {
                        $array_item_custom_tmp['sp'] = $number_item;
                    }else if ($size_window <= 576) {
                        $array_item_custom_tmp['xs'] = $number_item;
                    }else if ($size_window <= 768) {
                        $array_item_custom_tmp['sm'] = $number_item;
                    }else if ($size_window <= 992) {
                        $array_item_custom_tmp['md'] = $number_item;
                    }else if ($size_window <= 1200) {
                        $array_item_custom_tmp['lg'] = $number_item;
                    }else if ($size_window <= 1500) {
                        $array_item_custom_tmp['xl'] = $number_item;
                        $array_item_custom_tmp['xxl'] = $assign['formAtts']['slick_slidestoshow'];
                    }
                };
                $assign['formAtts']['array_fake_item'] = $array_item_custom_tmp;
            }
        // }

        return $assign;
    }
}
