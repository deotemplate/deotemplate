<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoWidgetAlert extends DeoWidgetBaseModel
{
    public $name = 'alert';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Alert'), 'explain' => $this->l('Create a Alert Message Box Based on Bootstrap 3 typo'));
    }

    public function renderForm($args, $data)
    {
        # validate module
        unset($args);
        $helper = $this->getFormHelper();
        $types = array();
        $types[] = array(
            'value' => 'alert-success',
            'text' => $this->l('Alert Success')
        );

        $types[] = array(
            'value' => 'alert-info',
            'text' => $this->l('Alert Info')
        );
        $types[] = array(
            'value' => 'alert-warning',
            'text' => $this->l('Alert Warning')
        );
        $types[] = array(
            'value' => 'alert-danger',
            'text' => $this->l('Alert Danger')
        );

        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget Alert'),
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'htmlcontent',
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
                ),
            ),
        );

        $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'],$new_field['input']);
        array_unshift($this->fields_form[0]['form'], $new_field['legend']);

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($data),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => $default_lang
        );

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        # validate module
        unset($args);
        $t = array(
            'name' => '',
            'html' => '',
            'alert_type' => ''
        );
        $setting = array_merge($t, $setting);
//        $html = '';
        $languageID = Context::getContext()->language->id;

        $languageID = Context::getContext()->language->id;
        $setting['html'] = isset($setting['htmlcontent_'.$languageID]) ? html_entity_decode($setting['htmlcontent_'.$languageID], ENT_QUOTES, 'UTF-8') : '';

        $output = array('type' => 'alert', 'data' => $setting);

        return $output;
    }

    /**
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     */
    public function getConfigKey($multi_lang = 0)
    {
        if ($multi_lang == 0) {
            return array(
                'alert_type',
            );
        } elseif ($multi_lang == 1) {
            return array(
                'htmlcontent',
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
