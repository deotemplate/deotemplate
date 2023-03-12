<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoInstagram extends DeoShortCodeBase
{
    public $name = 'DeoInstagram';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Instagram',
            'position' => 6,
            'desc' => $this->l('You can config Instagram box'),
            'image' => 'instagram.png',
            'tag' => 'social',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $accordion_type = array(
            array(
                'value' => 'full',
                'text' => $this->l('Normal')
            ),
            array(
                'value' => 'accordion',
                'text' => $this->l('Accordion')
            ),
            array(
                'value' => 'accordion_small_screen',
                'text' => $this->l('Accordion at tablet (screen <= 768px)')
            ),
            array(
                'value' => 'accordion_mobile_screen',
                'text' => $this->l('Accordion at mobile (screen <= 576px)')
            ),
        );
        $sort = array(
            array(
                'id' => 'none',
                'label' => $this->l('None')
            ),
            array(
                'id' => 'most-recent',
                'label' => $this->l('Newest to oldest'),
            ),
            array(
                'id' => 'least-recent',
                'label' => $this->l('Oldest to newest')
            ),
            array(
                'id' => 'most-liked',
                'label' => $this->l('Highest # of likes to lowest')
            ),
            array(
                'id' => 'least-liked',
                'label' => $this->l('Lowest # likes to highest')
            ),
            array(
                'id' => 'most-commented',
                'label' => $this->l('Highest # of comments to lowest')
            ),
            array(
                'id' => 'least-commented',
                'label' => $this->l('Lowest # of comments to highest')
            ),
            array(
                'id' => 'random',
                'label' => $this->l('Random')
            ),
        );
        $resolution = array(
            array(
                'id' => 'thumbnail',
                'label' => $this->l('thumbnail - 150x150')
            ),
            array(
                'id' => 'low_resolution',
                'label' => $this->l('low_resolution - 306x306'),
            ),
            array(
                'id' => 'standard_resolution',
                'label' => $this->l('standard_resolution - 612x612')
            )
        );

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
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
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
                'type'       => 'select',
                'label'   => $this->l('Accordion Type'),
                'name'       => 'accordion_type',
                'options' => array(
                    'query' => $accordion_type,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'full',
                'hint'    => $this->l('Select a Accordion Type'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Client ID'),
                'name' => 'client_id',
                'desc' => $this->l('Your API client id from Instagram. Required. Example: ').'3844455992253712',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Access Token'),
                'name' => 'access_token',
                'default' => '',
                'desc' => $this->l('A valid oAuth token. Example: ').'IGQVJYNWhPLUpJZA0owS3cwVk5FNkFqZAUFLaEhFcDBCeDcxZA0JoZAnlyWjFvT0JLQTg5UlhFek5CQXB1SzY2QWVfc3hCM0VDWHAydWdUUm51R29yWFBWb1hpWF9fclJLaE5CeHRzY1V5VHVxd2h5UE90aAZDZD',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('User ID'),
                'name' => 'user_id',
                'default' => '29852611652',
                'desc' => $this->l('User ID of Instagram Account. Example: ').'29852611652',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Sort By'),
                'name' => 'sort_by',
                'options' => array(
                    'query' => $sort,
                    'id' => 'id',
                    'name' => 'label'
                ),
                'desc' => $this->l('Sort the images in a set order. Available options are'),
                'default' => 'none',
            ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show Comment'),
            //     'name' => 'show_comment',
            //     'is_bool' => true,
            //     'desc' => $this->l('Show number comment'),
            //     'values' => DeoSetting::returnYesNo(),
            //     'default' => '1',
            // ),
            // array(
            //     'type' => 'switch',
            //     'label' => $this->l('Show Like'),
            //     'name' => 'show_like',
            //     'is_bool' => true,
            //     'desc' => $this->l('Show number like'),
            //     'values' => DeoSetting::returnYesNo(),
            //     'default' => '1',
            // ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Icon'),
                'name' => 'show_icon',
                'is_bool' => true,
                'desc' => $this->l('Show Icon Instagram'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Title'),
                'name' => 'show_title',
                'is_bool' => true,
                'desc' => $this->l('Show Title Instagram'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit'),
                'desc' => $this->l('Number of Images want to get. Max is 20 images, this is rule of Instagram.'),
                'name' => 'limit',
                'default' => '20',
            ),
            // array(
            //     'type' => 'select',
            //     'label' => $this->l('Resolution'),
            //     'name' => 'resolution',
            //     'options' => array(
            //         'query' => $resolution,
            //         'id' => 'id',
            //         'name' => 'label'
            //     ),
            //     'desc' => $this->l('Size of the images to show.'),
            //     'default' => 'thumbnail',
            // ),
            array(
                'type' => 'text',
                'label' => $this->l('Profile Link'),
                'desc' => $this->l('Create link in footer link to profile'),
                'name' => 'profile_link',
                'default' => '',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="color:red">'.$this->l('Template Type').'</div>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('View Type'),
                'class' => 'form-action',
                'name' => 'carousel_type',
                'options' => array(
                    'query' => array(
                        array('id' => 'slickcarousel', 'name' => $this->l('Carousel')),
                        array('id' => 'list', 'name' => $this->l('Normal')),
                        // array('id' => 'owlcarousel', 'name' => $this->l('Owl Carousel')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'slickcarousel'
            ),
        );

        // $inputs = array_merge($inputs_head, $inputs_content, $inputs_owlCarousel, $inputs_slickCarousel);
        $inputs = array_merge($inputs_head, $inputs_content, $inputs_slickCarousel);

        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }

    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);

        if (!DeoHelper::getLazyload()) {
            $assign['formAtts']['lazyload'] = 0;
            $assign['formAtts']['slick_lazyload'] = 0;
        }

        if (!(int) DeoHelper::getConfig('LOAD_LIBRARY_INSTAFEED')) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show Instagram. Please enable Instafeed library.';
            return $assign;
        // } elseif (isset($assign['formAtts']['carousel_type']) && $assign['formAtts']['carousel_type'] == 'owlcarousel') {
        //     if (!(int) DeoHelper::getConfigName('LOAD_LIBRARY_OWL_CAROUSEL')) {
        //         $assign['formAtts']['lib_has_error'] = true;
        //         $assign['formAtts']['lib_error'] = 'Can not show Instagram. Please enable Owl Carousel library.';
        //         return $assign;
        //     }
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
          
        } elseif (isset($assign['formAtts']['carousel_type']) && $assign['formAtts']['carousel_type'] == 'slickcarousel') {
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


            // set variable auto expired access_token
            if (!(Tools::strpos(_PS_BASE_URL_, 'localhost') == false) && (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true)){
                $assign['formAtts']['refresh_api_token'] = true;
            }else{
                $assign['formAtts']['refresh_api_token'] = false;
            }
        }
        
        return $assign;
    }
}
