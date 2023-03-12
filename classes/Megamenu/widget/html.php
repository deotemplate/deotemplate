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

class DeoWidgetHtml extends DeoWidgetBaseModel
{
    public $name = 'html';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('HTML'), 'explain' => $this->l('Create HTML With multiple Language'));
    }

    public function renderForm($args, $data)
    {
        #validate module
        unset($args);
        $helper = $this->getFormHelper();

        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget HTML.'),
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

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        #validate module
        unset($args);
        $t = array(
            'html' => '',
        );
        $setting = array_merge($t, $setting);
        $languageID = Context::getContext()->language->id;
        $setting['html'] = isset($setting['htmlcontent_'.$languageID]) ? Tools::stripslashes($setting['htmlcontent_'.$languageID]) : '';
        
        $output = array('type' => 'html', 'data' => $setting);

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
