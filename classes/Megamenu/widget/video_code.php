<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoWidgetVideocode extends DeoWidgetBaseModel
{
    public $name = 'video_code';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Video Code'), 'explain' => $this->l('Make Video widget via putting Youtube Code, Vimeo Code'));
    }

    public function renderForm($args, $data)
    {
        # validate module
        unset($args);
        $helper = $this->getFormHelper();

        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget Video Code.'),
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'video_code',
                    'cols' => 40,
                    'rows' => 10,
                    'value' => true,
                    'default' => '',
                    'autoload_rte' => false,
                    'desc' => $this->l('Copy  Video CODE  from youtube, vimeo and put here')
                ),
            ),
            'buttons' => array(
                array(
                    'title' => $this->l('Save And Stay'),
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'type' => 'submit',
                    'name' => 'saveandstaydeowidget'
                ),
                array(
                    'title' => $this->l('Save'),
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'type' => 'submit',
                    'name' => 'savedeowidget'
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
        # validate module
        unset($args);
        $t = array(
            'video_code' => '',
        );

        $setting = array_merge($t, $setting);
        $html = $setting['video_code'];

        $html = html_entity_decode(Tools::stripslashes($html), ENT_QUOTES, 'UTF-8');
//        $header = '';
//        $content = $html;

        $output = array('type' => 'video', 'data' => $setting);
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
                'video_code',
            );
        } elseif ($multi_lang == 1) {
            return array(
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
