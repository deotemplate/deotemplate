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

class DeoImageHotspot extends DeoShortCodeBase
{
    public $name = 'DeoImageHotspot';
    public $for_module = 'manage';

    public $inputs_lang = array('temp_title', 'temp_image', 'temp_rate_image', 'temp_image_link', 'temp_link', 'temp_description');
    public $inputs = array('temp_active', 'temp_lazyload','temp_use_image_link', 'temp_top', 'temp_left', 'temp_hpcolor', 'temp_location','temp_trigger', 'temp_width', 'temp_backcolor', 'temp_class','temp_type', 'temp_product','temp_profile');

    public function getInfo()
    {
        return array(
            'label' => 'Image Hotspot',
            'position' => 5,
            'desc' => $this->l('Display tooltip in your image when user hover over points'),
            'image' => 'image-multiple.png',
            'tag' => 'image',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        Context::getContext()->smarty->assign('path_image', DeoHelper::getImgThemeUrl());

        $ad = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $list_slider = '<button type="button" id="btn-add-hotpot" class="btn btn-default btn-add-level2">
        <i class="icon-plus-sign-alt"></i> '.$this->l('Add Hotspot').'</button><hr/>';
        $list_slider_button = '<div id="frm-level2" class="row-level2 frm-level2">
                            <div class="form-group">
                                <div class="col-lg-12 ">
                                    <button type="button" class="btn btn-primary btn-save-level2"
                                    data-error="'.$this->l('Please enter the title').'">'.$this->l('Save').'</button>
                                    <button type="button" class="btn btn-default btn-reset-level2">'.$this->l('Reset').'</button>
                                    <button type="button" class="btn btn-default btn-cancel-level2">'.$this->l('Cancel').'</button>
                                </div>
                            </div>
                            <hr/>
                        </div>';
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

        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
        $desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
                        <span class="image-wrapper full-image"><img src="#" class="img-thumbnail hide"></span>
                        <span class="btn-image">
                            <a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
                            <a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
                        </span>
                    </span>';
        $desc_temp = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
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

        $inputs_animation =  array(
            array(
                'type' => 'select',
                'label' => $this->l('Animations'),
                'name' => 'animation',
                'class' => 'animation-select',
                'options' => array(
                    'optiongroup' => array(
                        'label' => 'name',
                        'query' => DeoSetting::getAnimations(),
                    ),
                    'options' => array(
                        'id' => 'id',
                        'name' => 'name',
                        'query' => 'query',
                    ),
                ),
                'form_group_class' => 'apimage_animation',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div id="animationSandbox">Prestashop.com</div>',
                'form_group_class' => 'apimage_animation animate_sub',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Delay'),
                'name' => 'animation_delay',
                'default' => '0.5',
                'suffix' => 's',
                'class' => 'fixed-width-xs',
                'form_group_class' => 'apimage_animation animate_sub',
            ),
        );

        $inputs_content = array(
            array(
                'type' => 'textarea',
                'label' => $this->l('Description'),
                'name' => 'description',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'default' => '',
                'autoload_rte' => true,
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
                'type' => 'switch',
                'label' => $this->l('Lazy load'),
                'name' => 'lazyload',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'class' => 'lazyload',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Rate size image'),
                'name' => 'rate_image',
                'default' => '0',
                'suffix' => '%',
                'lang' => true,
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
                'lang' => true,
                'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link full-image"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
                'form_group_class' => 'select_image_link_group image-hotspot',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image'),
                'name' => 'image',
                'default' => '',
                'lang' => true,
                'class' => 'hide',
                'desc' => $desc,
                'form_group_class' => 'image-choose image-hotspot',
            ),
            array(
                'type' => 'text',
                'name' => 'alt',
                'lang' => true,
                'label' => $this->l('Alt'),
                'default' => ''
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $list_slider
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div id="config-hotspot" class="space" style="font-size:13px">'.$this->l('Configuration dot for Hotspot').'</div>',
                'form_group_class' => 'row-level2',
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
                'type' => 'text',
                'name' => 'temp_top',
                'label' => $this->l('Position Vertical'),
                'suffix' => '%',
                'class' => 'fixed-width-xl input-level2 temp_top',
                'default' => '50',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_left',
                'label' => $this->l('Position Horizontal'),
                'suffix' => '%',
                'class' => 'fixed-width-xl input-level2 temp_left',
                'default' => '50',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="help-block">'.$this->l('Too hard to set position for hotspot. ').'<a href="javascript:void(0)" class="scroll-to-image-hotspot">'.$this->l('Click here').'</a> to set position on image</div>',
                'form_group_class' => 'row-level2 scroll-description',
            ),
            array(
                'type' => 'color',
                'name' => 'temp_hpcolor',
                'label' => $this->l('Hotpot Color'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_hpcolor',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Configuration position and event for Hotspot').'</div>',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Location'),
                'name' => 'temp_location',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'top',
                            'name' => $this->l('top'),
                        ),
                        array(
                            'id' => 'right',
                            'name' => $this->l('right'),
                        ),
                        array(
                            'id' => 'bottom',
                            'name' => $this->l('bottom'),
                        ),
                        array(
                            'id' => 'left',
                            'name' => $this->l('left'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'top',
                'class' => 'input-level2 temp_location',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Mouse event'),
                'name' => 'temp_trigger',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'mouseover',
                            'name' => $this->l('Mouse Over'),
                        ),
                        array(
                            'id' => 'click',
                            'name' => $this->l('Click'),
                        ),
                        array(
                            'id' => 'popup',
                            'name' => $this->l('Popup'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'hoverable',
                'class' => 'input-level2 temp_trigger',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'DeoClass',
                'name' => 'temp_class',
                'label' => $this->l('CSS Class Hotspot'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_class',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_width',
                'label' => $this->l('Width'),
                'lang' => false,
                'default' => '150px',
                'desc' => 'Unit have to px, rem, em... Example: 150px',
                'class' => 'fixed-width-xxl input-level2 temp_width',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'color',
                'name' => 'temp_backcolor',
                'label' => $this->l('Backgroud Color'),
                'lang' => false,
                'class' => 'input-level2 temp_backcolor',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script type="text/javascript" src="'.__PS_BASE_URI__.DeoHelper::getJsDir().'colorpicker/js/deo.jquery.colorpicker.js"></script>',
                'form_group_class' => 'hide',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Configuration content for Hotspot').'</div>',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Hotspot Type'),
                'name' => 'temp_type',
                'options' => array(
                    'query' => array(
                        array('id' => 'product', 'name' => $this->l('Product')),
                        array('id' => 'textandimage', 'name' => $this->l('Text and Image')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'product',
                'class' => 'input-level2 temp_type',
                'form_group_class' => 'row-level2 row2-hotspot-type',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_title',
                'label' => $this->l('Title Hotspot'),
                'lang' => 'true',
                'default' => '',
                'class' => 'input-level2 temp_title js-multilang',
                'form_group_class' => 'row-level2 row2-title group-config-text-image',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_link',
                'label' => $this->l('Link Hotspot'),
                'lang' => 'true',
                'default' => '',
                'class' => 'input-level2 temp_link js-multilang',
                'form_group_class' => 'row-level2 row2-link group-config-text-image',
            ),
            // image for hotspot
            array(
                'type' => 'switch',
                'label' => $this->l('Use image link Hotspot'),
                'name' => 'temp_use_image_link',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'class' => 'temp_use_image_link',
                'form_group_class' => 'row-level2 group-config-text-image'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy load Hotspot'),
                'name' => 'temp_lazyload',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'class' => 'temp_lazyload',
                'form_group_class' => 'row-level2 group-config-text-image'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Rate size image Hotspot'),
                'name' => 'temp_rate_image',
                'default' => '0',
                'suffix' => '%',
                'lang' => true,
                'class' => 'temp_rate_image',
                'form_group_class' => 'row-level2 rate_lazyload_group_temp rate_value_temp group-config-text-image',
            ),
            array(
                'type' => 'html',
                'default' => '',
                'name' => 'temp_html_calc_rate_image',
                'html_content' => '<a href="javascript:void(0)" class="calc-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div><div class="virtual-image-link"></div>',
                'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
                'form_group_class' => 'row-level2 rate_lazyload_group_temp group_calc_rate_image_temp group-config-text-image',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image Link Hotspot'),
                'name' => 'temp_image_link',
                'default' => '',
                'lang' => true,
                'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
                'form_group_class' => 'row-level2 select_image_link_group_temp group-config-text-image',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image Hotspot'),
                'name' => 'temp_image',
                'default' => '',
                'lang' => true,
                'class' => 'hide',
                'desc' => $desc_temp,
                'form_group_class' => 'row-level2 image-choose-temp group-config-text-image',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Description Hotspot'),
                'name' => 'temp_description',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'default' => '',
                'autoload_rte' => true,
                'class' => 'input-level2 temp_description js-multilang',
                'form_group_class' => 'row-level2 group-config-text-image',
            ),
            array(
                'type' => 'text',
                'lang' => false,
                'name' => 'temp_product',
                'label' => $this->l('ID Product'),
                'desc' => $this->l('Find ID at Catalog/Product'),
                'class' => 'input-level2 temp_product',
                'form_group_class' => 'row-level2 group-config-product',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product Template'),
                'name' => 'temp_profile',
                'options' => array(
                    'query' => $profile_list,
                    'id' => 'plist_key',
                    'name' => 'name'
                ),
                'default' => 'all',
                'form_group_class' => 'row-level2 group-config-product',
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

        if ((int) DeoHelper::getConfig('ANIMATION')) {
            $inputs = array_merge($inputs_head, $inputs_animation, $inputs_content);
        }else{
            $inputs = array_merge($inputs_head, $inputs_content);
        }

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
        
        $list_slider = Context::getContext()->smarty->fetch(DeoHelper::getShortcodeTemplatePath('DeoImageHotspot.tpl'));
        
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
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        $assign['path'] = DeoHelper::getImgThemeUrl();

        if (!DeoHelper::getLazyload()) {
            $assign['formAtts']['lazyload'] = 0;
        }else{
            if (isset($assign['formAtts']['rate_image'])) {
                $assign['formAtts']['rate_image'] = $assign['formAtts']['rate_image'].'%';
            }
        }

        if (isset($assign['formAtts']['use_image_link']) && $assign['formAtts']['use_image_link']) {
            $assign['formAtts']['image'] = $assign['formAtts']['image_link'];
        }else{
            if (isset($assign['formAtts']['image'])) {
                $assign['formAtts']['image'] = DeoHelper::getImgThemeUrl().trim($assign['formAtts']['image']);
            }
        }

        if ((int) DeoHelper::getConfig('ANIMATION')) {
            if (!isset($assign['formAtts']['animation']) || $assign['formAtts']['animation'] == 'none') {
                $assign['formAtts']['animation'] = 'none';
                $assign['formAtts']['animation_delay'] = '';
            } elseif ($assign['formAtts']['animation'] != 'none' && (int)$assign['formAtts']['animation_delay'] > 0) {
                // validate module
                $assign['formAtts']['animation_delay'] .= 's';
            } elseif ($assign['formAtts']['animation'] != 'none' && (int)$assign['formAtts']['animation_delay'] <= 0) {
                // Default delay
                $assign['formAtts']['animation_delay'] = '1s';
            }
        }else{
            $assign['formAtts']['animation'] = 0;
            $assign['formAtts']['animation_delay'] = 0;
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

                // position
                $item['top']    = $item['top'].'%';
                $item['left']   = $item['left'].'%';

                if ($item['type'] == 'product'){
                    // product
                    $module = new DeoTemplate();
                    $params['value_by_product_id'] = 1;
                    $params['product_id'] = $item['product'];
                    $products = $module->getProductsFont($params);

                    $item['product'] = (count($products) > 0) ? $products[0] : '';

                    // product class
                    $profile = $item['profile'];
                    $item['productClassWidget'] = DeoShortCodeBase::getProductClassByPListKey($profile);
                    
                    unset(
                        $item['rate_image'], 
                        $item['lazyload'], 
                        $item['link'], 
                        $item['image'], 
                        $item['image_link'], 
                        $item['title'], 
                        $item['description']
                    );

                }else{
                    //lazyload
                    if (!DeoHelper::getLazyload()){
                        $item['lazyload'] = 0;
                    }

                    //rate image
                    $item['rate_image'] = $item['rate_image'].'%';

                    // Image
                    if ($item['use_image_link']){
                        $item['image'] = $item['image_link'];
                    }else if($item['image']){
                        $item['image'] = DeoHelper::getImgThemeUrl().$item['image'];
                        unset($item['image_link']);
                    }

                    unset($item['product'], $item['profile']);
                }

                array_push($list_items, $item);
            }
        }

        $assign['formAtts']['items'] = $list_items;
       
        return $assign;
    }
}
