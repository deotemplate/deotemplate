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

class DeoHtml extends DeoShortCodeBase
{
    public $name = 'DeoHtml';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Html', 
            'position' => 3, 
            'desc' => $this->l('You can put html'),
            'image' => 'html.png',
            'tag' => 'content code',
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
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'lang' => 'true',
                'default' => '',
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'autoload_rte' => false,
                'values' => '',
                'class' => 'sub_title',
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
                'label'   => $this->l('Accordion Type'),
                'name'       => 'accordion_type',
                'class' => 'fixed-width-xxl',
                'options' => array(
                    'query' => $accordion_type,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'full',
                'hint'    => $this->l('Select a Accordion Type'),
            ),
            array(
                'type' => 'textarea',
                'name' => 'content_html',
                'class' => 'ap_html',
                'rows' => '50',
                'lang' => true,
                'label' => $this->l('Html'),
                'values' => '',
                'autoload_rte' => true,
                'default' => "<div>\n</div>"
            ),
        );
        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
}
