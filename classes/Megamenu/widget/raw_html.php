<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoWidgetRawhtml extends DeoWidgetBaseModel
{
    public $name = 'raw_html';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Raw HTML'), 'explain' => $this->l('Put Raw HTML Code'));
    }

    public function renderForm($args, $data)
    {
        $helper = $this->getFormHelper();

        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget Raw HTML.'),
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'raw_html',
                    'cols' => 40,
                    'rows' => 10,
                    'value' => true,
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => false,
                    'desc' => $this->l('Enter HTML CODE in here')
                ),
            )
        );

        $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'],$new_field['input']);
        array_unshift($this->fields_form[0]['form'], $new_field['legend']);

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($data),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => $default_lang
        );
        unset($args);

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        $t = array(
            'raw_html' => '',
        );

        $setting = array_merge($t, $setting);
                
        if (isset($setting['raw_html']) && $setting['raw_html'] != '') {
            // keep backup
            $html = $setting['raw_html'];
            $html = html_entity_decode(Tools::stripslashes($html), ENT_QUOTES, 'UTF-8');
        } else {
            // change raw html to use multi lang
            $languageID = Context::getContext()->language->id;
            $setting['raw_html'] = isset($setting['raw_html_'.$languageID]) ? html_entity_decode(Tools::stripslashes($setting['raw_html_'.$languageID]), ENT_QUOTES, 'UTF-8') : '';
        }
        

        $output = array('type' => 'raw_html', 'data' => $setting);
        unset($args);
        return $output;
    }

    /**
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     */
    public function getConfigKey($multi_lang = 0)
    {
        // change raw html to use multi lang
        if ($multi_lang == 0) {
            return array(
            );
        } elseif ($multi_lang == 1) {
            return array(
                'raw_html',
            );
        } elseif ($multi_lang == 2) {
            return array();
        }
    }
}
