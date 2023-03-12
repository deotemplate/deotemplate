<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoBlockCarousel extends DeoShortCodeBase
{
    public $name = 'DeoBlockCarousel';
    public $inputs_lang = array('temp_title','temp_sub_title', 'temp_image', 'temp_rate_image', 'temp_image_link', 'temp_link', 'temp_description');
    public $inputs = array('temp_use_image_link', 'temp_active', 'temp_class');


    public function getInfo()
    {
        return array(
            'label' => 'Block Carousel', 
            'position' => 6,
            'desc' => $this->l('Show block in Carousel'),
            'image' => 'slideshow.png',
            'tag' => 'content slider',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
        $ad = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $list_slider = '<button type="button" id="btn-add-level2" class="btn btn-default btn-add-level2">
                <i class="icon-plus-sign-alt"></i> '.$this->l('Add slider').'</button><hr/>';
        $list_slider_button = '<div id="frm-level2" class="row-level2 frm-level2">
                            <div class="form-group">
                                <div class="col-lg-12 ">
                                    <button type="button" class="btn btn-primary btn-save-level2"
                                    data-error="'.$this->l('Please enter the title and description').'">'.$this->l('Save').'</button>
                                    <button type="button" class="btn btn-default btn-reset-level2">'.$this->l('Reset').'</button>
                                    <button type="button" class="btn btn-default btn-cancel-level2">'.$this->l('Cancel').'</button>
                                </div>
                            </div>
                            <hr/>
                        </div>';
        $desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
                        <span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
                        <span class="btn-image">
                            <a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
                            <a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
                        </span>
                    </span>';
        $no_image = __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png';

        $inputs_head = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
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
        
        $inputs_temp = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Step 2: Add content for sliders').'</div>'
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $list_slider
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable'),
                'name' => 'temp_active',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'row-level2'
            ),
            array(
                'type' => 'DeoClass',
                'name' => 'temp_class',
                'label' => $this->l('CSS Class'),
                'default' => '',
                'form_group_class' => 'row-level2'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Use image link'),
                'name' => 'temp_use_image_link',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'class' => 'temp_use_image_link',
                'form_group_class' => 'row-level2'
            ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Lazy load'),
            //     'name' => 'temp_lazyload',
            //     'values' => DeoSetting::returnYesNo(),
            //     'default' => '1',
            //     'class' => 'temp_lazyload',
            //     'form_group_class' => 'row-level2'
            // ),
            array(
                'type' => 'text',
                'label' => $this->l('Rate size image'),
                'name' => 'temp_rate_image',
                'default' => '0',
                'suffix' => '%',
                'lang' => true,
                'class' => 'temp_rate_image',
                'form_group_class' => 'row-level2 rate_lazyload_group_temp rate_value_temp',
            ),
            array(
                'type' => 'html',
                'default' => '',
                'name' => 'temp_html_calc_rate_image',
                'html_content' => '<a href="javascript:void(0)" class="calc-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div><div class="virtual-image-link"></div>',
                'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
                'form_group_class' => 'row-level2 rate_lazyload_group_temp group_calc_rate_image_temp',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image Link'),
                'name' => 'temp_image_link',
                'default' => '',
                'lang' => true,
                'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
                'form_group_class' => 'row-level2 select_image_link_group_temp',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image'),
                'name' => 'temp_image',
                'default' => '',
                'lang' => true,
                'class' => 'hide',
                'desc' => $desc,
                'form_group_class' => 'row-level2 image-choose-temp lazyload_carousel',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_title',
                'label' => $this->l('Title'),
                'lang' => true,
                'default' => '',
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'row-level2 title-slide row2-title',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'default' => '',
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'row-level2 sub-title-slide',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_link',
                'label' => $this->l('Link'),
                'lang' => true,
                'default' => '',
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'row-level2 link-slide',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Description'),
                'name' => 'temp_description',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'default' => '',
                'autoload_rte' => true,
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'row-level2 description-slide',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $list_slider_button
            ),
            array(
                'type' => 'hidden',
                'name' => 'total_slider',
                'default' => ''
            ),
        );

        $inputs_content = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Open new tab'),
                'desc' => $this->l('Open new tab when click to link in slider'),
                'name' => 'is_open',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Step 1: Carousel Setting').'</div>'
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
        
        // $inputs = array_merge($inputs_head, $inputs_content, $inputs_owlCarousel, $inputs_bootstrapCarousel, $inputs_slickCarousel, $inputs_temp);
        $inputs = array_merge($inputs_head, $inputs_content, $inputs_slickCarousel, $inputs_temp);

        return $inputs;
    }

    public function addConfigList($values)
    {
        // Get value with keys special
        $config_val = array();
        $total = isset($values['total_slider']) ? $values['total_slider'] : '';
        $arr = explode('|', $total);
        
        $inputs_lang = $this->inputs_lang;
        $inputs = $this->inputs;


        $languages = Language::getLanguages(false);
        foreach ($arr as $i) {
            foreach ($inputs_lang as $config) {
                foreach ($languages as $lang) {
                    $config_val[$config][$i][$lang['id_lang']] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config.'_'.$i.'_'.$lang['id_lang'], ''));
                }
            }
            foreach ($inputs as $config) {
                $config_val[$config][$i] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config.'_'.$i, ''));
            }
        }

        Context::getContext()->smarty->assign(array(
            'lang' => $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT')),
            'default_lang' => $lang->id,
            'arr' => $arr,
            'languages' => $languages,
            'config_val' => $config_val,
            'path' => DeoHelper::getImgThemeUrl(),
            'inputs_lang' => $this->inputs_lang,
            'inputs' => $this->inputs,
        ));
        
        $list_slider = Context::getContext()->smarty->fetch(DeoHelper::getShortcodeTemplatePath('DeoBlockCarousel.tpl'));
        
        $input = array(
            'type' => 'html',
            'name' => 'default_html',
            'html_content' => $list_slider,
        );
        // Append new input type html
        $this->config_list[] = $input;
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
        // validate module
        unset($module);

        if (!DeoHelper::getLazyload()) {
            $assign['formAtts']['lazyload'] = 0;
            $assign['formAtts']['slick_lazyload'] = 0;
        }

        $total_slider = isset($assign['formAtts']['total_slider']) ? $assign['formAtts']['total_slider'] : '';
        $list = explode('|', $total_slider);
        $list_items = array();
        $lang = Language::getLanguage(Context::getContext()->language->id);
        $id_lang = $lang['id_lang'];
        
        $inputs_lang = $this->inputs_lang;
        $inputs = $this->inputs;
                      
        foreach ($list as $number) {
            if ($number && (isset($assign['formAtts']['temp_active_'.$number]) && $assign['formAtts']['temp_active_'.$number] == 1)) {
                $item = array();
                $item['id'] = $number;

                # MULTI-LANG
                foreach ($inputs_lang as $key) {
                    $name = $key.'_'.$number.'_'.$id_lang;
                    $new_name = str_replace("temp_", "", $key);
                    $item[$new_name] = isset($assign['formAtts'][$name]) ? $assign['formAtts'][$name] : '';
                }

                # SINGLE-LANG
                foreach ($inputs as $key) {
                    $name = $key.'_'.$number;
                    $new_name = str_replace("temp_", "", $key);
                    $item[$new_name] = isset($assign['formAtts'][$name]) ? $assign['formAtts'][$name] : '';
                }

                //rate image
                $item['rate_image'] = $item['rate_image'].'%';

                // Image
                if ($item['use_image_link']){
                    $item['image'] = $item['image_link'];
                }else if ($item['image']){
                    $item['image'] = DeoHelper::getImgThemeUrl().$item['image'];
                    unset($item['image_link']);
                }

                array_push($list_items, $item);
            }
        }
        $assign['formAtts']['slides'] = $list_items;

        $assign['formAtts']['is_open'] = isset($assign['formAtts']['is_open']) ? $assign['formAtts']['is_open'] : 0;
        $assign['carouselName'] = 'carousel-'.DeoSetting::getRandomNumber();
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
