<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoImage extends DeoShortCodeBase
{
    public $name = 'DeoImage';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Image',
            'position' => 5,
            'desc' => $this->l('Single Image'),
            'image' => 'image-single.png',
            'tag' => 'image',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        Context::getContext()->smarty->assign('path_image', DeoHelper::getImgThemeUrl());
        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
        $desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
                        <span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
                        <span class="btn-image">
                            <a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
                            <a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
                        </span>
                    </span>';
        $no_image = __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png';
        
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
            array(
                'type'       => 'select',
                'label'   => $this->l('Use Accordion'),
                'name'       => 'accordion_type',
                'desc'   => 'If you use accordion title not empty.',
                'class' => 'fixed-width-xxl',
                'options' => array(
                    'query' => $accordion_type,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'full',
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
                'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
                'form_group_class' => 'select_image_link_group',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image'),
                'name' => 'image',
                'default' => '',
                'lang' => true,
                'class' => 'hide',
                'desc' => $desc,
                'form_group_class' => 'image-choose',
            ),
            array(
                'type' => 'text',
                'name' => 'alt',
                'label' => $this->l('Alt'),
                'lang' => true,
                'default' => ''
            ),
            array(
                'type' => 'text',
                'name' => 'url',
                'label' => $this->l('Link to'),
                'lang' => true,
                'desc' => 'Example: http://prestashop.com',
                'default' => ''
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Open link new tab'),
                'name' => 'is_open',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
            ),
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
            )
        );

        if ((int) DeoHelper::getConfig('ANIMATION')) {
            $inputs = array_merge($inputs_head, $inputs_animation, $inputs_content);
        }else{
            $inputs = array_merge($inputs_head, $inputs_content);
        }

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
        }else{
            if (isset($assign['formAtts']['rate_image'])) {
                $assign['formAtts']['rate_image'] = $assign['formAtts']['rate_image'].'%';
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

        if (isset($assign['formAtts']['use_image_link']) && $assign['formAtts']['use_image_link']) {
            $assign['formAtts']['image'] = $assign['formAtts']['image_link'];
        }else{
            if (isset($assign['formAtts']['image']) && $assign['formAtts']['image']) {
                $assign['formAtts']['image'] = DeoHelper::getImgThemeUrl().trim($assign['formAtts']['image']);
            }
        }


        return $assign;
    }
}
