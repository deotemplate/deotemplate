<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }
 
class DeoCountdown extends DeoShortCodeBase
{
    public $name = 'DeoCountdown';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Countdown',
            'position' => 3,
            'desc' => $this->l('Show time countdown'),
            'image' => 'countdown.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $html_content = "
            <style rel='stylesheet' type='text/css'>
                .ui-datepicker.ui-widget-content{
                        border: 1px solid #aaaaaa/*{borderColorContent}*/;
                        background: #ffffff/*{bgColorContent}*/;
                        color: #222222/*{fcContent}*/;
                }
                .ui-slider.ui-widget-content{
                        border: 1px solid #aaaaaa/*{borderColorContent}*/;
                }
            </style>


            <script>
                $('.datepicker').datetimepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd',
                    // Define a custom regional settings in order to use PrestaShop translation tools
                    currentText: 'Now',
                    closeText: 'Done',
                    ampm: false,
                    amNames: ['AM', 'A'],
                    pmNames: ['PM', 'P'],
                    timeFormat: 'hh:mm:ss tt',
                    timeSuffix: '',
                    timeOnlyTitle: 'Choose Time',
                    timeText: 'Time',
                    hourText: 'Hour',
                    minuteText: 'Minute'
                });
            </script>
        ";

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
                'label' => $this->l('Time From'),
                'name' => 'time_from',
                'class' => 'datepicker',
                'default' => ''
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Time To'),
                'name' => 'time_to',
                'default' => '',
                'class' => 'datepicker',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Open new tab'),
                'desc' => $this->l('Open new tab when click to link in slider'),
                'name' => 'new_tab',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
            ),
            array(
                'type' => 'text',
                'name' => 'link_label',
                'label' => $this->l('Link Label'),
                'lang' => 'true',
                'default' => ''
            ),
            array(
                'type' => 'text',
                'name' => 'link',
                'label' => $this->l('Link'),
                'lang' => 'true',
                'default' => '',
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'apfullslider-row link-slide',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $html_content,
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Widget Description'),
                'name' => 'description',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'default' => '',
                'autoload_rte' => true,
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'apfullslider-row description-slide',
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
    
        if (!(int) DeoHelper::getConfig('AJAX_COUNTDOWN')) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show Countdown Widget. Please enable AJAX Show Count Down Product.';
            return $assign;
        }

        return $assign;
    }
}
