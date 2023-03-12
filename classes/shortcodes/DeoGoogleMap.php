<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoGoogleMap extends DeoShortCodeBase
{
    public $name = 'DeoGoogleMap';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Google Map',
            'position' => 5,
            'desc' => $this->l('Create a Google Map'),
            'image' => 'google-map.png',
            'tag' => 'content social',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        // Get all store of shop
        $base_model = new DeoTemplateModel();
        $data_list = $base_model->getAllStoreByShop();
        // Options for switch elements
        $zoom_option = array();
        for ($i = 1; $i <= 20; $i++) {
            $zoom_option[] = array('id' => $i, 'value' => $i);
        }
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
            array(
                'type' => 'select',
                'label' => $this->l('Zoom'),
                'name' => 'zoom',
                'default' => '11',
                'options' => array(
                    'query' => $zoom_option,
                    'id' => 'id',
                    'name' => 'value'
                )
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => 'width',
                'desc' => $this->l('Example: 100%, 100px'),
                'default' => '100%',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => 'height',
                'desc' => $this->l('Example: 100%, 100px'),
                'default' => '300px',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show List Stores'),
                'name' => 'is_display_store',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show select store'),
                'name' => 'show_list_store',
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('NO: select all stores'),
                'form_group_class' => 'show_select_store',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Uncheck is show all stores').'</div>',
                'form_group_class' => 'group_show_select_store',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('List stores'),
                'desc' => $this->l('Press "Ctrl" and "Mouse Left Click" to choose many items').'<br>'.$this->l('Uncheck to show all stores'),
                'name' => 'store[]',
                'multiple' => true,
                'options' => array(
                    'query' => $data_list,
                    'id' => 'id_store',
                    'name' => 'name'
                ),
                'default' => 'all',
                'form_group_class' => 'group_show_select_store',
            ),
        );
        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }

    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        // Get all store of shop
        $base_model = new DeoTemplateModel();
        $data_list = $base_model->getAllStoreByShop();
        $form_atts = $assign['formAtts'];
        $not_all = (isset($form_atts['show_list_store']) && $form_atts['show_list_store']);
        $store_ids = explode(',', (isset($form_atts['store']) && $form_atts['store']) ? $form_atts['store'] : '');
        $is_display_store = (isset($form_atts['is_display_store']) && $form_atts['is_display_store']) ? 1 : 0;
        $deotemplate = DeoTemplate::getInstance();

        $markers = array();
        if ($not_all) {
            foreach ($store_ids as $id) {
                foreach ($data_list as $store) {
                    if ($id == $store['id_store']) {
                        $markers[] = $store;
                        break;
                    }
                }
            }
        } else {
            $markers = $data_list;
        }

        foreach ($markers as &$marker) {
            $address = $deotemplate->processStoreAddress($marker);
            $marker['other'] = $deotemplate->renderStoreWorkingHours($marker);
            $marker['address'] = $address;
            $marker['has_store_picture'] = file_exists(_PS_STORE_IMG_DIR_.(int)$marker['id_store'].'.jpg');
        }
        
        $assign['marker_center'] = json_encode($deotemplate->getMarkerCenter($markers));
        $assign['marker_list'] = json_encode($markers);
        
        return $assign;
    }
}
