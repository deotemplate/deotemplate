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

class DeoWidgetTabHTML extends DeoWidgetBaseModel
{
    public $name = 'tabhtml';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Widget HTML Tab'), 'explain' => $this->l('Create HTML Tab'));
    }

    public function renderForm($args, $data)
    {
        # validate module
        unset($args);
        $helper = $this->getFormHelper();

        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget HTML Tab'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Vertical Tab'),
                    'name' => 'vertical',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '1',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Number of HTML Tab'),
                    'name' => 'nbtabhtml',
                    'default' => 5,
                    'desc' => $this->l('Enter a number greater 0')
                )
            )
        );

        if (!isset($data['params']['nbtabhtml']) || !$data['params']['nbtabhtml']) {
            $nbtabhtml = 5;
        } else {
            $nbtabhtml = $data['params']['nbtabhtml'];
        }
        for ($i = 1; $i <= $nbtabhtml; $i++) {
            $tmpArray = array(
                'type' => 'text',
                'label' => $this->l('Title ').$i,
                'name' => 'title_'.$i,
                'default' => 'Title Sample '.$i,
                'lang' => true
            );
            $new_field['input'][] = $tmpArray;
            $tmpArray = array(
                'type' => 'textarea',
                'label' => $this->l('Content ').$i,
                'name' => 'content_'.$i,
                'default' => 'Content Sample '.$i,
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'autoload_rte' => true,
                'desc' => $this->l('Enter Content ').$i
            );
            $new_field['input'][] = $tmpArray;
        }
        // array_merge($new_field['input'],$tmpArray);
        //$this->fields_form[1]['form']['input'][] = $tmpArray;
        
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
        $content = '';

        $tabs = array();
        $languageID = Context::getContext()->language->id;

        for ($i = 1; $i <= $setting['nbtabhtml']; $i++) {
            $title = isset($setting['title_'.$i.'_'.$languageID]) ? Tools::stripslashes($setting['title_'.$i.'_'.$languageID]) : '';

            if (!empty($title)) {
                $content = isset($setting['content_'.$i.'_'.$languageID]) ? Tools::stripslashes($setting['content_'.$i.'_'.$languageID]) : '';
                $tabs[] = array('title' => trim($title), 'content' => trim($content));
            }
        }
        $setting['tabhtmls'] = $tabs;
        $setting['id'] = rand() + count($tabs);

        $output = array('type' => 'tabhtml', 'data' => $setting);
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
                'nbtabhtml',
                'vertical',
            );
        } elseif ($multi_lang == 1) {
            $number_html = Tools::getValue('nbtabhtml');
            $array = array();
            for ($i = 1; $i <= $number_html; $i++) {
                $array[] = 'title_'.$i;
                $array[] = 'content_'.$i;
            }
            return $array;
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
