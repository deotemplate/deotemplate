<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoCategoryImage extends DeoShortCodeBase
{
    public $name = 'DeoCategoryImage';
    public $for_module = 'manage';
    public $module_name = 'deotemplate';
    public $level = 0;
    protected $deo_count = 0;
    protected $id_deo_count = array();
    protected $categories = array();
    protected $id_categories = array();

    public function getInfo()
    {
        return array(
            'label' => 'Categories Image',
            'position' => 5,
            'desc' => $this->l('Choosing images for categories'),
            'image' => 'image-multiple.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $root = Category::getRootCategory();
        $selected_cat = array();
        $selected_cates = '';
        $selected_images = '';
        // $image_list = $this->getImages();
        $image_list = array();
        if (Tools::getIsset('categorybox')) {
            $category_box = Tools::getValue('categorybox');
            $selected_cat = explode(',', $category_box);
        }

        if (Tools::getIsset('category_img')) {
            $selected_images = str_replace($this->str_search, $this->str_relace_html, Tools::getValue('category_img'));
        }
        if (Tools::getIsset('selected_cates')) {
            $selected_cates = Tools::getValue('selected_cates');
        }
        $tree = new HelperTreeCategories('categorybox', 'All Categories');
        // fix tree category with ps version 1.6.1 or newer
        if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
            $tree->setRootCategory($root->id)->setUseCheckBox(true)->setFullTree(true)->setSelectedCategories($selected_cat)->setInputName('categorybox');
        } else {
            $tree->setRootCategory($root->id)->setUseCheckBox(true)->setSelectedCategories($selected_cat)->setInputName('categorybox');
        }
        // $orderby = array(
        //     array(
        //         'order' => 'position',
        //         'name' => $this->l('Position')
        //     ),
        //     array(
        //         'order' => 'depth',
        //         'name' => $this->l('Depth')
        //     ),
        //     array(
        //         'order' => 'name',
        //         'name' => $this->l('Name')
        //     )
        // );
        
        $path_image = DeoHelper::getImgThemeUrl();
        Context::getContext()->smarty->assign('path_image', $path_image);
        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=icon';
        $tree_html = $tree->render();

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
                'type' => 'switch',
                'label' => $this->l('Lazy load'),
                'name' => 'lazyload',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Rate size image'),
                'name' => 'rate_image',
                'default' => '',
                'suffix' => '%',
                'class' => 'rate-image',
                'form_group_class' => 'rate_lazyload rate_value',
            ),
            array(
                'type' => 'html',
                'name' => 'calculate_rate_image',
                'html_content' => '<a href="javascript:void(0)" class="calculate-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div>',
                'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
                'form_group_class' => 'rate_lazyload btn_calculate_rate_image ',
            ),
            array(
                'type' => 'img_cat',
                'name' => 'img_cat',
                'imageList' => $image_list,
                'selected_images' => $selected_images,
                'selected_cates' => $selected_cates,
                'lang' => true,
                'tree' => $tree_html,
                'href_image' => $href,
                'path_image' => $path_image,
                'default' => '',
                'desc' => $this->l('Note: Choose only one category and depth > 2 to show childrens of category and button show more will show link to parent of category.'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Depth'),
                'name' => 'cate_depth',
                'default' => '1',
                'desc' => $this->l('Level depth from category slected').'<br>'.$this->l('Depth have to > 0'),
                'form_group_class' => 'group_normal-image-category',
            ),
            // array(
            //     'type' => 'select',
            //     'label' => $this->l('Order By:'),
            //     'name' => 'orderby',
            //     'default' => 'position',
            //     'options' => array(
            //         'query' => $orderby,
            //         'id' => 'order',
            //         'name' => 'name'
            //     )
            // ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit'),
                'name' => 'limit',
                'default' => '5',
                'form_group_class' => 'group_normal-image-category',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Disable HTML tree structure'),
                'name' => 'disable_html_tree_structure',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'label' => $this->l('Destroy tree structure, categories have same level'),
                'form_group_class' => 'group_normal-image-category',
            ), 
            array(
                'type' => 'switch',
                'label' => $this->l('Quantity'),
                'name' => 'quantity',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'desc' => $this->l('Show quantity number product in category'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Description'),
                'name' => 'description',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Show description category'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Button View All'),
                'name' => 'viewall',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'desc' => $this->l('Show button View All if total number item > limit').'<br>'.$this->l('If depth > 1 and choose only one category => button view all will show link to parent category'),
                'form_group_class' => 'show_link_viewall group_viewall group_normal-image-category',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Text Button View All'),
                'name' => 'text_link_viewall',
                'lang' => true,
                'desc' => $this->l('Leave empty will be show default text'),
                'form_group_class' => 'group_show_link_viewall group_viewall',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Link Button View All'),
                'name' => 'link_viewall',
                'lang' => true,
                'desc' => $this->l('Leave empty to enable load more categories at current page (not link to other page).'),
                'form_group_class' => 'group_show_link_viewall group_viewall',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="color:red">'.$this->l('Template Type').'</div>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Use Carousel'),
                'class' => 'form-action',
                'name' => 'carousel_type',
                'options' => array(
                    'query' => array(
                        array('id' => 'normal-image-category', 'name' => $this->l('None')),
                        array('id' => 'slickcarousel', 'name' => $this->l('Slick Carousel')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'none'
            ),
            array(
                'type' => 'hidden',
                'name' => 'id_root',
                'default' => $root->id,
            ),
            array(
                'type' => 'hidden',
                'name' => 'id_lang',
                'default' => Context::getContext()->language->id,
            )
        );

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
        $form_atts = $assign['formAtts'];

        if (!DeoHelper::getLazyload()) {
            $assign['formAtts']['lazyload'] = 0;
            $assign['formAtts']['slick_lazyload'] = 0;
        }
        $images = array();
        if (isset($form_atts['category_img']) && $form_atts['category_img']) {
            $selected_images = str_replace($this->str_search, $this->str_relace_html, $form_atts['category_img']);
            $images = json_decode($selected_images, true);
        }
        $rate_images = null;
        if (isset($form_atts['rate_image']) && $form_atts['rate_image']) {
            $rate_images = str_replace($this->str_search, $this->str_relace_html, $form_atts['rate_image']);
            $rate_images = json_decode($rate_images, true);
        }

        $sql_filter = '';
        // $sql_sort = '';
        // if (isset($form_atts['orderby']) && $form_atts['orderby']) {
        //     if ($form_atts['orderby'] == 'depth') {
        //         $sql_sort = ' ORDER BY c.`level_depth` ASC';
        //     }
        //     if ($form_atts['orderby'] == 'position') {
        //         $sql_sort = ' ORDER BY c.`level_depth` ASC, category_shop.`position` ASC';
        //     }
        //     if ($form_atts['orderby'] == 'name') {
        //         $sql_sort = ' ORDER BY c.`level_depth` ASC, cl.`name` ASC';
        //     }
        // }
        $catids = (isset($form_atts['categorybox']) && $form_atts['categorybox']) ? ($form_atts['categorybox']) : '';
        $catids = explode(',', $catids);
        $result = array();
        $limit = (isset($form_atts['limit']) && $form_atts['limit']) ? ((int)$form_atts['limit']) : 0;
        $limit++;
        
        $carousel_type = false;
        if (isset($form_atts['carousel_type']) && $form_atts['carousel_type'] == 'slickcarousel'){
            $carousel_type = true;
            $form_atts['cate_depth'] = 1;
        }
        $this->level = (int) $form_atts['cate_depth'];

        foreach ($catids as $cate_id) {
            if ($cate_id && Validate::isInt($cate_id)) {
                $result_cate = $this->getNestedCategories($cate_id, 1, $images, $limit, $this->deo_count, $carousel_type, $rate_images);
                if ($result_cate) {
                    foreach ($result_cate as $item => &$cate) {
                        // if(isset($cate['id_category']) && !empty($rate_images)){
                        //     $result_cate[$item]['rate_image'] = $rate_images[$cate['id_category']].'%';
                        // }
                        if (!in_array($cate['id_category'], $this->id_categories)){
                            $this->id_categories[] = $cate['id_category'];
                            if ($carousel_type){
                                $this->categories[] = $cate;
                            }else{
                                $this->categories[] = array($cate['id_category'] => $cate);
                            }
                        }
                    }
                    $result[] = $result_cate;
                }
            }
        }



        $assign['categories'] = array();
        if ((isset($form_atts['carousel_type']) && $form_atts['carousel_type'] == 'slickcarousel') || (isset($form_atts['carousel_type']) && $form_atts['carousel_type'] != 'slickcarousel' && isset($form_atts['disable_html_tree_structure']) && $form_atts['disable_html_tree_structure'])){
            $assign['categories'] = $this->categories; 
        }else{
            $assign['categories'] = $result;
        }
        // $assign['categories'] = ((isset($form_atts['disable_html_tree_structure']) && $form_atts['disable_html_tree_structure'] && isset($form_atts['viewall']) && !$form_atts['viewall']) || (isset($form_atts['carousel_type']) && $form_atts['carousel_type'] == 'slickcarousel')) ? array_reverse($this->categories) : $result;


        $assign['total'] = $this->deo_count;
        $this->deo_count = 0;
        $this->id_deo_count = array();
        $this->categories = array();
        $this->id_categories = array();

        if (isset($form_atts['carousel_type']) && $form_atts['carousel_type'] == 'slickcarousel'){
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
        }
        
        // validate module
        unset($sql_filter);
        // unset($sql_sort);
        return $assign;
    }

    public function getImages()
    {
        $oimages = array();
        
        $img_theme_dir = DeoHelper::getImgThemeDir();
        $icon_theme_dir = DeoHelper::getImgThemeDir('icon');
        $icon_theme_url = DeoHelper::getImgThemeUrl();
        
        if (!file_exists($img_theme_dir)) {
            @mkdir($img_theme_dir, 0755, true);
        }
        if (!file_exists($icon_theme_dir)) {
            @mkdir($icon_theme_dir, 0755, true);
        }
        
        if (is_dir($img_theme_dir) && is_dir($icon_theme_dir)) {
            $images = glob($icon_theme_dir.'*.*');
            $exts = array('jpg', 'gif', 'png');
            foreach ($images as $key => $image) {
                $ext = Tools::substr($image, Tools::strlen($image) - 3, Tools::strlen($image));
                if (in_array(Tools::strtolower($ext), $exts)) {
                    $aimage = array();
                    $aimage['path'] = $icon_theme_url.basename($image);
                    $aimage['name'] = basename($image);
                    $oimages[] = $aimage;
                }
                // validate module
                unset($key);
            }
        }
        return $oimages;
    }

    public function getNestedCategories($parent, $level, $images, $limit, $count, $carousel_type, $rate_images = null)
    {
        $buff = array();
        if ($level <= $this->level) {
            $lang = Context::getContext()->language->id;
            $current = array();
            $child = array();
            //$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
            $image_path = DeoHelper::getImgThemeUrl();

            $sql = 'SELECT c.*, cl.*
                FROM `'._DB_PREFIX_.'category` c'.Shop::addSqlAssociation('category', 'c').'
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
                WHERE c.id_parent='.(int)$parent.' AND `id_lang` = '.(int)$lang.'
                AND c.`active` = 1
                ORDER BY c.`level_depth` ASC, category_shop.`position` ASC';
            $result = Db::getInstance()->executeS($sql);
            $current_category = Db::getInstance()->executeS('SELECT c.*, cl.*
                                FROM `'._DB_PREFIX_.'category` c'.Shop::addSqlAssociation('category', 'c').'
                                        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
                                        WHERE c.id_category='.(int)$parent.' AND `id_lang` = '.(int)$lang.'
                                        AND c.`active` = 1');
            if ($current_category) {
                if (array_key_exists($current_category[0]['id_category'], $images)) {
                    $current_category[0]['image'] = $image_path.$images[$current_category[0]['id_category']];
                }
                if(isset($rate_images[$current_category[0]['id_category']])){
                    $current_category[0]['rate_image'] = $rate_images[$current_category[0]['id_category']].'%';
                }
                if (!in_array($parent, $this->id_deo_count)){
                    $current_category[0]['deo_count'] = $this->deo_count;
                    $this->id_deo_count[] = $parent;
                    $this->deo_count = $this->deo_count + 1;
                }
                if ($result) {
                    foreach ($result as &$row) {
                        if ($row && isset($row['id_category'])) {
                            $child = $this->getNestedCategories($row['id_category'], $level + 1, $images, $limit, $this->deo_count, $carousel_type, $rate_images);
                            if ($child) {
                                foreach ($child as &$item) {
                                    if (array_key_exists($item['id_category'], $images)) {
                                        $item['image'] = $image_path.$images[$item['id_category']];
                                    }
                                    if (!in_array($item['id_category'], $this->id_categories)){
                                        if ($carousel_type){
                                            // $this->categories[] = $item;
                                        }else{
                                            $this->categories[] = array($item['id_category'] => $item);
                                        }
                                        $this->id_categories[] = $item['id_category'];
                                    }
                                    if (!in_array($item['id_category'], $this->id_deo_count)){
                                        $item['deo_count'] = $this->deo_count;
                                        $this->id_deo_count[] = $item['id_category'];
                                        $this->deo_count = $this->deo_count + 1;
                                    }
                                    $current[$row['id_category']] = $item;
                                }
                            }
                            $buff[$row['id_parent']] = $current_category[0];
                            if ($current) {
                                $buff[$row['id_parent']]['children'] = &$current;
                            }
                        }
                    }
                } else {
                    // validate module
                    if (!in_array($current_category[0]['id_category'], $this->id_categories)){
                        $this->id_categories[] = $current_category[0]['id_category'];
                        if ($carousel_type){
                            $this->categories[] = $current_category[0];
                        }else{
                            $this->categories[] = array($current_category[0]['id_category'] => $current_category[0]);
                        }
                    }
                    if (!in_array($current_category[0]['id_category'], $this->id_deo_count)){
                        $current_category[0]['deo_count'] = $this->deo_count;
                        $this->id_deo_count[] = $current_category[0]['id_category'];
                        $this->deo_count = $this->deo_count + 1;
                    }
                    $buff[$parent] = $current_category[0];
                }
            }
        }
        return $buff;
    }
}
