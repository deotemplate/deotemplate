<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoVideo extends DeoShortCodeBase
{
    public $name = 'DeoVideo';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Video',
            'position' => 5,
            'desc' => $this->l('Embed video box'),
            'image' => 'video.png',
            'tag' => 'social',
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
                'form_group_class' => 'deoimage_animation',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div id="animationSandbox">Prestashop.com</div>',
                'form_group_class' => 'deoimage_animation animate_sub',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Delay'),
                'name' => 'animation_delay',
                'default' => '0.5',
                'suffix' => 's',
                'class' => 'fixed-width-xs',
                'form_group_class' => 'deoimage_animation animate_sub',
            ),
        );

        $inputs_content = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Video').'</div>',
            ), 
            array(
                'type' => 'switch',
                'label' => $this->l('Use popup video'),
                'name' => 'popup_video',
                'desc' => $this->l('Play video on popup when click to image'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
            ),
            array(
                'type' => 'select',
                'name' => 'video_type',
                'label' => $this->l('Video Type'),
                'default' => 'normal',
                'options' => array('query' => array(
                        array('id' => 'youtube', 'name' => $this->l('Youtube')),
                        array('id' => 'normal', 'name' => $this->l('Normal')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'class' => 'form-action',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Source Youtube Video'),
                'name' => 'content_html',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'default' => '',
                'desc' => $this->l('Example embed video: ').htmlspecialchars('"<iframe width="1280" height="720" src="https://www.youtube.com/embed/Elim33GXrTw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>"'),
                'autoload_rte' => false,
                'form_group_class' => 'video_type_sub video_type-youtube',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Source Link Video'),
                'name' => 'link_video',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'default' => '',
                'desc' => $this->l('Link access video that you upload on your hosting. Support video formats *.mp4, *.webm or *.ogg.'),
                'autoload_rte' => false,
                'form_group_class' => 'video_type_sub video_type-normal',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => 'width',
                'desc' => $this->l('Example: 100%, 100px, auto'),
                'default' => '100%',
                'form_group_class' => 'video_type_sub video_type-normal',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => 'height',
                'desc' => $this->l('Example: 100%, 100px, auto'),
                'default' => '300px',
                'form_group_class' => 'video_type_sub video_type-normal',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Controls Video'),
                'name' => 'controls',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'video_type_sub video_type-normal',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Mute Video'),
                'name' => 'mute',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('You have to enable "Mute Video" if you want enable "Auto Play Video"'),
                'form_group_class' => 'video_type_sub video_type-normal',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Auto Play Video'),
                'name' => 'autoplay',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'desc' => $this->l('"Mute Video" always is enable if you want use "Auto Play Video"'),
                'form_group_class' => 'video_type_sub video_type-normal',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Repeat Video'),
                'name' => 'loop',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'video_type_sub video_type-normal',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Fake Content'),
                'name' => 'fake_content',
                'desc' => $this->l('Show fake content when load site. If enable video will be hidden and video only show when click to fake content'),
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'show_fake_content',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Image and HTML').'</div>',
                'form_group_class' => 'group_show_fake_content',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Use image link'),
                'name' => 'use_image_link',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'class' => 'use_image_link',
                'form_group_class' => 'group_show_fake_content',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy load'),
                'name' => 'lazyload',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'class' => 'lazyload',
                'form_group_class' => 'group_show_fake_content',
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
                'form_group_class' => 'group_show_fake_content',
            ),
            array(
                'type' => 'html',
                'default' => '',
                'name' => 'html_calc_rate_image',
                'html_content' => '<a href="javascript:void(0)" class="calc-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div><div class="virtual-image-link"></div>',
                'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
                'form_group_class' => 'rate_lazyload_group group_calc_rate_image group_show_fake_content',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image link'),
                'name' => 'image_link',
                'default' => '',
                'lang' => true,
                'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
                'form_group_class' => 'select_image_link_group group_show_fake_content',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image'),
                'name' => 'image',
                'default' => '',
                'lang' => true,
                'class' => 'hide',
                'desc' => $desc,
                'form_group_class' => 'image-choose group_show_fake_content',
            ),
            array(
                'type' => 'text',
                'name' => 'alt',
                'lang' => true,
                'label' => $this->l('Alt'),
                'default' => '',
                'form_group_class' => 'group_show_fake_content',
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
                'form_group_class' => 'group_show_fake_content',
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
