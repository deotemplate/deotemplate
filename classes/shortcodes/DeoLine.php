<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoLine extends DeoShortCodeBase
{
    public $name = 'DeoLine';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Line Space',
            'position' => 5,
            'desc' => $this->l('Create a Line between elements'),
            'image' => 'line.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $inputs_head = array(
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
        );

        $inputs_content = array(
            array(
                'type' => 'color',
                'label' => $this->l('Border Color'),
                'name' => 'border_color',
                'default' => '#000000',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Border Width'),
                'name' => 'border_width',
                'default' => '1',
                'suffix' => 'px',
                'class' => 'fixed-width-xl',
            ),
            array(
                'type' => 'select',
                'name' => 'border_style',
                'label' => $this->l('Border Style'),
                'default' => 'solid',
                'options' => array(
                    'query' => array(
                        array('id' => 'solid', 'name' => $this->l('solid')),
                        array('id' => 'dotted', 'name' => $this->l('dotted')),
                        array('id' => 'dashed', 'name' => $this->l('dashed')),
                        array('id' => 'double', 'name' => $this->l('double')),
                        array('id' => 'inset', 'name' => $this->l('inset')),
                        array('id' => 'outset', 'name' => $this->l('outset')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
        );

        $inputs = array_merge($inputs_head, $inputs_content);

        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
}
