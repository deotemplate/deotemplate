<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoOriginalHtml extends DeoShortCodeBase
{
    public $name = 'DeoOriginalHtml';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Original Html', 
            'position' => 3, 
            'desc' => $this->l('You can put original html'),
            'image' => 'html-code.png',
            'tag' => 'content code',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
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
                'type' => 'textarea',
                'name' => 'content_html',
                'class' => 'deo_html_raw raw-'.time(),
                'rows' => '50',
                'lang' => true,
                'label' => $this->l('Html'),
                'values' => '',
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
