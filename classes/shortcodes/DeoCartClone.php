<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoCartClone extends DeoShortCodeBase
{
    public $name = 'DeoCartClone';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Cart Clone',
            'position' => 5,
            'desc' => $this->l('This widget clone icon for Advance Cart. It only work when you enable Advance Cart'),
            'image' => 'cart.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        // $type_dropdown = array(
        //     array(
        //         'id_type' => 'dropdown',
        //         'name_type' => $this->l('Dropdown'),
        //     ),
        //     array(
        //         'id_type' => 'dropup',
        //         'name_type' => $this->l('Dropup'),
        //     ),
        //     array(
        //         'id_type' => 'slidebar_left',
        //         'name_type' => $this->l('Slidebar Left'),
        //     ),
        //     array(
        //         'id_type' => 'slidebar_right',
        //         'name_type' => $this->l('Slidebar Right'),
        //     ),
        //     array(
        //         'id_type' => 'slidebar_top',
        //         'name_type' => $this->l('Slidebar Top'),
        //     ),
        //     array(
        //         'id_type' => 'slidebar_bottom',
        //         'name_type' => $this->l('Slidebar Bottom'),
        //     ),
        // );

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
            // array(
            //     'type' => 'select',
            //     'label' => $this->l('Type Popup Cart'),
            //     'name' => 'type_dropdown',
            //     'options' => array(
            //         'query' => $type_dropdown,
            //         'id' => 'id_type',
            //         'name' => 'name_type'
            //     ),
            // ),
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
