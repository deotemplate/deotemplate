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

class DeoTwitter extends DeoShortCodeBase
{
    public $name = 'DeoTwitter';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Twitter',
            'position' => 6,
            'desc' => $this->l('You can config for display Twitter box'),
            'image' => 'twitter.png',
            'tag' => 'social',
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
                'label'   => $this->l('Accordion Type'),
                'name'       => 'accordion_type',
                'options' => array(
                    'query' => $accordion_type,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'full',
                'hint'    => $this->l('Select a Accordion Type'),
            ),
            // array(
            //     'type' => 'text',
            //     'label' => $this->l('Twitter Widget ID'),
            //     'name' => 'twidget_id',
            //     'default' => '578806287158251521',
            //     'desc' => $this->l('Please go to the page'). ' https://publish.twitter.com '. $this->l('then create a widget, and get data-widget-id to input in this param.'),
            // ),
            array(
                'type' => 'text',
                'label' => $this->l('Username'),
                'name' => 'username',
                'desc' => $this->l('Example: ').'with url twitter https://twitter.com/prestashop =>'.$this->l('username is prestashop'),
                'default' => 'prestashop',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script type="text/javascript" src="'.__PS_BASE_URI__.DeoHelper::getJsDir().'colorpicker/js/deo.jquery.colorpicker.js"></script>',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Border Color'),
                'name' => 'border_color',
                'default' => '#e2e9ec',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Link Color'),
                'name' => 'link_color',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Text Color'),
                'name' => 'text_color',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Name Color'),
                'name' => 'name_color',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Nick name Color'),
                'name' => 'mail_color',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => 'width',
                'default' => '270',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => 'height',
                'default' => '300',
                'desc' => $this->l('Active when show Scroll Scrollbar is NO'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show background'),
                'name' => 'show_backgroud',
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Replies'),
                'name' => 'show_replies',
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Header'),
                'name' => 'show_header',
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Footer'),
                'name' => 'show_footer',
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Border'),
                'name' => 'show_border',
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Scrollbar'),
                'name' => 'show_scrollbar',
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit'),
                'name' => 'limit',
                'default' => 3,
                'form_group_class' => 'limit-twitter',
            ),
        );
        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
}
