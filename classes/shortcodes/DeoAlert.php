<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoAlert extends DeoShortCodeBase
{
    public $name = 'DeoAlert';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Alert',
            'position' => 5,
            'desc' => $this->l('Alert Message box'),
            'image' => 'alert.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $types = array(
            array(
                'value' => 'alert-primary',
                'text' => $this->l('Alert Primary')
            ),
            array(
                'value' => 'alert-secondary',
                'text' => $this->l('Alert Secondary')
            ),
            array(
                'value' => 'alert-light',
                'text' => $this->l('Alert Light')
            ),
            array(
                'value' => 'alert-dark',
                'text' => $this->l('Alert Dark')
            ),
            array(
                'value' => 'alert-success',
                'text' => $this->l('Alert Success')
            ),
            array(
                'value' => 'alert-info',
                'text' => $this->l('Alert Info')
            ),
            array(
                'value' => 'alert-warning',
                'text' => $this->l('Alert Warning')
            ),
            array(
                'value' => 'alert-danger',
                'text' => $this->l('Alert Danger')
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

        $inputs_content = array(
            array(
                'type' => 'textarea',
                'lang' => true,
                'label' => $this->l('Content'),
                'name' => 'content_html',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'default' => '',
                'autoload_rte' => true,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Alert Type'),
                'name' => 'alert_type',
                'options' => array('query' => $types,
                    'id' => 'value',
                    'name' => 'text'),
                'default' => '1',
                'desc' => $this->l('Select a alert style')
            )
        );

        $inputs = array_merge($inputs_head, $inputs_content);

        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
}
