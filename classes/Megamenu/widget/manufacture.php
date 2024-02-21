<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoWidgetManufacture extends DeoWidgetBaseModel
{
    public $name = 'Manufacture';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Manufacture Logos'), 'explain' => $this->l('Manufacture Logo'));
    }

    public function renderForm($args, $data)
    {
        # validate module
        unset($args);
        $helper = $this->getFormHelper();
        $imagesTypes = ImageType::getImagesTypes('manufacturers');

        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget Manufacture Logos.'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Limit'),
                    'name' => 'limit',
                    'default' => 10,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Image:'),
                    'desc' => $this->l('Select type image for manufacture.'),
                    'name' => 'image',
                    'default' => 'small'.'_default',
                    'options' => array(
                        'query' => $imagesTypes,
                        'id' => 'name',
                        'name' => 'name'
                    )
                )
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
            'html' => '',
        );
        $setting = array_merge($t, $setting);
        $plimit = ($setting['limit']) ? (int)($setting['limit']) : 10;
        $image_type = ($setting['image']) ? ($setting['image']) : 'small'.'_default';
        $data = Manufacturer::getManufacturers(true, Context::getContext()->language->id, true, 1, $plimit, false);
        foreach ($data as &$item) {
            // $id_images = (!file_exists(_PS_MANU_IMG_DIR_.'/'.$item['id_manufacturer'].'-'.$image_type.'.jpg')) ? Language::getIsoById(Context::getContext()->language->id).'-default' : $item['id_manufacturer'];
            // $item['image'] = _THEME_MANU_DIR_.$id_images.'-'.$image_type.'.jpg';
            $item['image'] = Context::getContext()->link->getManufacturerImageLink($item['id_manufacturer'], $image_type);
        }

        $setting['manufacturers'] = $data;
        $setting['link'] = Context::getContext()->link;
        $output = array('type' => 'manufacture', 'data' => $setting);

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
                'limit',
                'image',
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
