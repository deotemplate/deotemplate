<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class DeoSpace extends DeoShortCodeBase
{
    public $name = 'DeoSpace';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Space',
            'position' => 5,
            'desc' => $this->l('Create a Space between elements'),
            'image' => 'space.png',
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
                'type' => 'select',
                'name' => 'size_space',
                'label' => $this->l('Size Space'),
                'default' => 'medium_space',
                'options' => array(
                    'query' => array(
                        array('id' => 'large_space', 'name' => $this->l('Large Space')),
                        array('id' => 'medium_space', 'name' => $this->l('Medium Space')),
                        array('id' => 'small_space', 'name' => $this->l('Small Space')),
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
