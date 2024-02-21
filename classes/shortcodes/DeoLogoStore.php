<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoLogoStore extends DeoShortCodeBase
{
    public $name = 'DeoLogoStore';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Logo Store',
            'position' => 5,
            'desc' => $this->l('Show default logo your store with other link or other image'),
            'image' => 'logo.png',
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
        $inputs_head = array(
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
                'type' => 'switch',
                'label' => $this->l('Link home page for navigator mobile'),
                'name' => 'link_mobile',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Yes: Not logo store image, only should active for mobile navigator'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Open link new tab'),
                'name' => 'is_open',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Use Other Image'),
                'name' => 'use_other_image',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image'),
                'name' => 'image',
                'default' => '',
                'lang' => true,
                'class' => 'hide',
                'desc' => $desc,
                'form_group_class' => 'group-image_logo image-choose',
            ),
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
        if (isset($assign['formAtts']['use_other_image']) && (int)$assign['formAtts']['use_other_image'] && isset($assign['formAtts']['image']) && $assign['formAtts']['image']){
            $assign['formAtts']['image'] = DeoHelper::getImgThemeUrl().trim($assign['formAtts']['image']);
        }else{
            unset($assign['formAtts']['image']);
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

        return $assign;
    }
}
