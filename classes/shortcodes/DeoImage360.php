<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoImage360 extends DeoShortCodeBase
{
    public $name = 'DeoImage360';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Image 360°', 
            'position' => 20, 
            'desc' => $this->l('Adds image rotate 360°'),
            'image' => 'image-360.png',
            'tag' => 'image',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
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
                'type' => 'text',
                'name' => 'image',
                'label' => $this->l('URL First Image'),
                'desc' => $this->l('Example: https://magictoolbox.sirv.com/demo/magic360/armani-bag/armani-bag-small-01-01.jpg'),
            ),
            array(
                'label' => $this->l('File Name Image'),
                'type' => 'text',
                'name' => 'filename',
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'apfullslider-row select-img',
                'desc' => $this->l('All image have to put same folder on your website directory and your images will be have format name: filename-{row}-{col}.jpg.').'</br>'
                    .'<b>filename</b>:'.$this->l(' all images have to same start name.').'</br>'
                    .'<b>{col}</b>:'.$this->l(' to denote the image column number.').'</br>'
                    .'<b>{row}</b>:'.$this->l(' if your spin has multiple rows (for up/down spins).').'</br>'
                    .$this->l('This configuration is also the first image to be displayed.').'</br>'
                    .$this->l('Example: https://magictoolbox.sirv.com/demo/magic360/armani-bag/armani-bag-small-01-01.jpg').'   =>   <b>armani-bag-small-{row}-{col}.jpg</b>',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Use Large Image'),
                'name' => 'use_large_image',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
            ),
            array(
                'type' => 'text',
                'name' => 'large_filename',
                'label' => $this->l('File Name Large Image'),
                'desc' => $this->l('All image have to put same folder on your website directory and your images will be have format name: filename-{row}-{col}.jpg.').'</br>'
                    .'<b>filename</b>:'.$this->l(' all images have to same start name.').'</br>'
                    .'<b>{col}</b>:'.$this->l(' to denote the image column number.').'</br>'
                    .'<b>{row}</b>:'.$this->l(' if your spin has multiple rows (for up/down spins).').'</br>'
                    .$this->l('This configuration is also the first image to be displayed.').'</br>'
                    .$this->l('Example: https://magictoolbox.sirv.com/demo/magic360/armani-bag/armani-bag-large-01-01.jpg').'   =>   <b>armani-bag-large-{row}-{col}.jpg</b>',
                'form_group_class' => 'use-large-image',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Fullscreen'),
                'name' => 'fullscreen',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'desc' => $this->l('Should enable if you have specified large images.'),
                'form_group_class' => 'use-large-image',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Use Multiple Rows'),
                'name' => 'multiple_row',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
            ),
            array(
                'type' => 'text',
                'name' => 'columns',
                'label' => $this->l('Number of images per column'),
                'desc' => $this->l('Definde variable').' <b>{col}</b>',
                'default' => 12,
                'form_group_class' => 'use-multiple-row',
            ),
            array(
                'type' => 'text',
                'name' => 'rows',
                'label' => $this->l('Number of images per row'),
                'desc' => $this->l('Definde variable').' <b>{row}</b>',
                'default' => 4,
                'form_group_class' => 'use-multiple-row',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Magnifier'),
                'name' => 'magnify',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'desc' => $this->l('Zoom image when click. Should enable if you have specified large images.'),
            ),
            array(
                'type' => 'text',
                'name' => 'magnifier_width',
                'label' => $this->trans("Size Magnifier"),
                'default' => '80%',
                'desc' => $this->l('Size magnifier compare to size image. Unit is %, px, rem...'),
                'form_group_class' => 'use-magnify',
            ),
            array(
                'type' => 'select',
                'label' => $this->trans("Shape Magnifier"),
                'name' => 'magnifier_shape',
                'options' => array(
                   'query' => array(
                       array('id' => 'inner', 'name' => $this->l('Inner')),
                       array('id' => 'circle', 'name' => $this->l('Circle')),
                       array('id' => 'square', 'name' => $this->l('Square')),
                   ),
                   'id' => 'id',
                   'name' => 'name'
                ),
                'desc' => $this->l('Shape of magnifying glass.'),
                'form_group_class' => 'use-magnify',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Spin'),
                'name' => 'spin',
                'options' => array(
                    'query' => array(
                        array('id' => 'drag', 'name' => $this->l('Drag')),
                        array('id' => 'hover', 'name' => $this->l('Hover')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'drag',
                'desc' => $this->l('Method for spinning the image'),
            ),
            array(
                'type' => 'text',
                'name' => 'speed',
                'label' => $this->l('Speed'),
                'default' => '50',
                'desc' => $this->l('The speed of rotation while dragging. Example: 50'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Initialization'),
                'name' => 'initialize_on',
                'options' => array(
                    'query' => array(
                        array('id' => 'load', 'name' => $this->l('load')),
                        array('id' => 'hover', 'name' => $this->l('hover')),
                        array('id' => 'click', 'name' => $this->l('click')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'load',
                'desc' => $this->l('The images in your spin will download automatically when page load, click or hover'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin'),
                'name' => 'autospin',
                'options' => array(
                    'query' => array(
                        array('id' => 'off', 'name' => $this->l('Disable')),
                        array('id' => 'once', 'name' => $this->l('Once')),
                        array('id' => 'twice', 'name' => $this->l('Twice')),
                        array('id' => 'infinite', 'name' => $this->l('Infinite')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'off',
            ),
            array(
                'type' => 'text',
                'name' => 'start_column',
                'label' => $this->l('Number Column Start'),
                'default' => 'auto',
                'desc' => $this->l('The autospin will automatically start with the column. Default: auto'),
                'form_group_class' => 'use-autospin',
            ),
            array(
                'type' => 'text',
                'name' => 'start_row',
                'label' => $this->l('Number Row Start'),
                'desc' => $this->l('The autospin will automatically start with the row. Default: auto'),
                'default' => 'auto',
                'form_group_class' => 'use-autospin',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin start'),
                'name' => 'autospin_start',
                'options' => array(
                    'query' => array(
                        array('id' => 'load', 'name' => $this->l('Spin on page load')),
                        array('id' => 'hover', 'name' => $this->l('Spin on page hover')),
                        array('id' => 'click', 'name' => $this->l('Spin on page click')),
                        array('id' => 'load,hover', 'name' => $this->l('Spin on page load and again on hover')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'load',
                'desc' => $this->l('Start automatic spin on page load, click or hover'),
                'form_group_class' => 'use-autospin',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin stops'),
                'name' => 'autospin_stop',
                'options' => array(
                    'query' => array(
                        array('id' => 'never', 'name' => $this->l('Never')),
                        array('id' => 'hover', 'name' => $this->l('Spin on page hover')),
                        array('id' => 'click', 'name' => $this->l('Spin on page click')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'never',
                'desc' => $this->l('Start automatic spin on page load, click or hover'),
                'form_group_class' => 'use-autospin',
            ),
            array(
                'type' => 'text',
                'name' => 'autospin_speed',
                'label' => $this->l('Speed Autospin'),
                'default' => '2000',
                'desc' => $this->l('Speed when auto spin in milisecond. Example: 2000ms = 2s'),
                'form_group_class' => 'use-autospin',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin direction'),
                'name' => 'autospin_direction',
                'options' => array(
                    'query' => array(
                        array('id' => 'clockwise', 'name' => $this->l('Clockwise')),
                        array('id' => 'anticlockwise', 'name' => $this->l('Anticlockwise')),
                        array('id' => 'alternate-clockwise', 'name' => $this->l('Alternate Clockwise')),
                        array('id' => 'alternate-anticlockwise', 'name' => $this->l('Alternate Anticlockwise')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'clockwise',
                'desc' => $this->l('Method for spinning the image'),
                'form_group_class' => 'use-autospin',
            ),
        );
        $inputs = array_merge($inputs_head, $inputs_content);

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

        return $assign;
    }
}
