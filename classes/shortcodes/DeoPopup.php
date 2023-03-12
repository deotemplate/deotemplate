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

class DeoPopup extends DeoShortCodeBase
{
    public $name = 'DeoPopup';

    public function getInfo()
    {
        return array(
            'label' => 'Popup',
            'position' => 5, 
            'desc' => $this->l('You can put widget in popup'),
            'image' => 'popup.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList($sub_tab = 0)
    {
        Context::getContext()->smarty->assign('path_image', DeoHelper::getImgThemeUrl());
        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
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
            </script>";

        $input = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
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
                'desc' => $this->l('Auto hide if leave it blank'),
                'default' => '',
            ),
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Button open Popup'),
                'name' => 'show_btn_open_popup',
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'desc' => $this->l('No: Popup auto open when page load').'<br>'.$this->l('Yes: Show button open Popup'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Simple Popup'),
                'name' => 'simple_popup',
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'desc' => $this->l('Show popup simple, size popup will not responsive so you should set it full width. You only can use only one popup each page, If simple popup you can use it unlimited'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Text button show Popup'),
                'name' => 'text_btn_popup',
                'lang' => true,
                'form_group_class' => 'group-button-popup',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Padding'),
                'name' => 'padding',
                'default' => '',
                'suffix' => 'px',
                'desc' => $this->l('Space inside fancyBox around content. Can be set as array - [top, right, bottom, left]. Ex: [15, 15, 15, 15]. Be plused with width and height popup. Default: 15'),
                'class' => 'fixed-width-xl',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Margin'),
                'name' => 'margin',
                'default' => '',
                'suffix' => 'px',
                'desc' => $this->l('Minimum space between viewport and fancyBox. Can be set as array - [top, right, bottom, left]. Ex: [20, 20, 20, 20]. Default: 20'),
                'class' => 'fixed-width-xl',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => 'width',
                'default' => '',
                'desc' => $this->l('Unit have to is px, rem, em, % ... Default: 500'),
                'class' => 'fixed-width-sm',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => 'height',
                'default' => '',
                'desc' => $this->l('Unit have to is px, rem, em, % ... Default: 500'),
                'class' => 'fixed-width-sm',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Effect Show Popup'),
                'name' => 'effect',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'fadescale',
                            'name' => $this->l('Fade and scale'),
                        ),
                        array(
                            'id' => 'fall',
                            'name' => $this->l('Fall'),
                        ),
                        array(
                            'id' => 'stickyup',
                            'name' => $this->l('Sticky Up'),
                        ),
                        array(
                            'id' => 'blur',
                            'name' => $this->l('Blur'),
                        ),
                        array(
                            'id' => 'drop',
                            'name' => $this->l('Drop'),
                        ),
                        array(
                            'id' => 'superscale',
                            'name' => $this->l('Super scale'),
                        ),
                        array(
                            'id' => 'slideright',
                            'name' => $this->l('Slide in from right'),
                        ),
                        array(
                            'id' => 'slidebottom',
                            'name' => $this->l('Slide in from bottom'),
                        ),
                        array(
                            'id' => 'newspaper',
                            'name' => $this->l('Newspaper twirl'),
                        ),

                        array(
                            'id' => 'sidefall',
                            'name' => $this->l('Fall from the side'),
                        ),

                        array(
                            'id' => 'horizontalflip',
                            'name' => $this->l('3D horizontal flip'),
                        ),
                        array(
                            'id' => 'verticalflip',
                            'name' => $this->l('3D vertical flip'),
                        ),
                        array(
                            'id' => 'sign',
                            'name' => $this->l('3D Sign'),
                        ),
                        array(
                            'id' => 'slit',
                            'name' => $this->l('3D Slit'),
                        ),
                        array(
                            'id' => 'rotatebottom',
                            'name' => $this->l('3D rotate bottom'),
                        ),
                        array(
                            'id' => 'rotateleft',
                            'name' => $this->l('3D rotate left'),
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Effect will show if value of Time Delay Show Popup > 1 seconds'),
                'form_group_class' => 'group-config-off_simple_popup',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show only Homepage'),
                'name' => 'show_homepage',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Close Button'),
                'name' => 'show_btn_close',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Hidden popup when close'),
                'name' => 'hide_popup_when_close',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Do not show popup again when close popup and refresh page'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Display text option "Do not show this message again"'),
                'name' => 'show_message_again',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'group-display-show_text_again',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Time Open Popup Again'),
                'name' => 'time_life',
                'default' => '7',
                'suffix' => 'day',
                'desc' => $this->l('Time close popup after click "Do not show this message again" or enable option Do not show popup again when close popup. Leave empty if you want to popup never show until customer clear cookie browser. Be careful It will be annoying for customer !!!'),
                'class' => 'fixed-width-sm',
                'form_group_class' => 'group-display-show_text_again',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Time Show Popup').'</div>',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Date From'),
                'name' => 'time_from',
                'class' => 'datepicker',
                'default' => '',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Date To'),
                'name' => 'time_to',
                'class' => 'datepicker',
                'default' => '',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $html_content,
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Time Delay Show Popup'),
                'name' => 'time_wait',
                'default' => '',
                'suffix' => 'seconds',
                'desc' => $this->l('Time to wait after page loads to display popup. Leave empty or set = 0 if you want to display popup as soon as page loads. Effect will show if value > 1 seconds.'),
                'class' => 'fixed-width-sm',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Time Show Popup Again'),
                'name' => 'time_show_again',
                'default' => '',
                'suffix' => 'seconds',
                'desc' => $this->l('Time to display automatically popup again from last time close popup. Leave empty if you want to remove this function. Be careful It will be annoying for customer !!!'),
                'class' => 'fixed-width-sm',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Time Delay Close Popup'),
                'name' => 'time_close',
                'default' => '',
                'suffix' => 'seconds',
                'desc' => $this->l('Time to wait close popup automatically. Leave empty or set = 0 if you want to always show pop-up until user closes pop-up'),
                'class' => 'fixed-width-sm',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Responsive').'</div>',
                'form_group_class' => 'group-config-off_simple_popup',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show on Desktop'),
                'name' => 'show_desktop',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'desc' => $this->l('Show popup on Desktop device (screen width > 992px)'),
                'form_group_class' => 'group-config-off_simple_popup', 
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show on Tablet'),
                'name' => 'show_tablet',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'desc' => $this->l('Show popup on Tablet device (992px > screen width > 576px)'),
                'form_group_class' => 'group-config-off_simple_popup',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show on Mobile'),
                'name' => 'show_mobile',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'desc' => $this->l('Show popup on Mobile device (screen width < 576px)'),
                'form_group_class' => 'group-config-off_simple_popup',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Position Popup').'</div>',
            ),
            array(
                'type' => 'select',
                'name' => 'position_popup',
                'label' => $this->l('Position Popup'),
                'default' => '0',
                'options' => array('query' => array(
                        array('id' => '0', 'name' => $this->l('Center')),
                        array('id' => '1', 'name' => $this->l('Let me choose')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'form_group_class' => 'group-config-off_simple_popup',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Position Top'),
                'name' => 'top',
                'default' => '0.5',
                'desc' => $this->l('Top space ratio for vertical centering. If set to 0.5, then vertical and bottom space will be equal. If 0 - fancyBox will be at the viewport top. If 1 - fancyBox will be at the viewport bottom'),
                'class' => 'fixed-width-sm',
                'form_group_class' => 'group-config-position group-config-off_simple_popup',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Position Left'),
                'name' => 'left',
                'default' => '0.5',
                'desc' => $this->l('Left space ratio for horizontal centering. If set to 0.5, then horizontal and bottom space will be equal. If 0 - fancyBox will be at the viewport left. If 1 - fancyBox will be at the viewport right'),
                'class' => 'fixed-width-sm',
                'form_group_class' => 'group-config-position group-config-off_simple_popup',
            ),
            array(
                'type' => 'select',
                'name' => 'position_popup_simple',
                'label' => $this->l('Position Popup'),
                'default' => '0',
                'options' => array('query' => array(
                        array('id' => '0', 'name' => $this->l('Center')),
                        array('id' => '1', 'name' => $this->l('Let me choose')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'form_group_class' => 'group-config-simple_popup',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Position Left'),
                'name' => 'left_simple',
                'default' => '0px',
                'desc' => $this->l('Unit is px, rem or %. Set value auto to remove this position'),
                'class' => 'fixed-width-md',
                'form_group_class' => 'group-config-simple_popup group-config-position-simple',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Position Right'),
                'name' => 'right_simple',
                'default' => 'auto',
                'desc' => $this->l('Unit is px, rem or %. Set value auto to remove this position'),
                'class' => 'fixed-width-md',
                'form_group_class' => 'group-config-simple_popup group-config-position-simple',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Position Top'),
                'name' => 'top_simple',
                'default' => 'auto',
                'desc' => $this->l('Unit is px, rem or %. Set value auto to remove this position'),
                'class' => 'fixed-width-md',
                'form_group_class' => 'group-config-simple_popup group-config-position-simple',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Position Bottom'),
                'name' => 'bottom_simple',
                'default' => '0px',
                'desc' => $this->l('Unit is px, rem or %. Set value auto to remove this position'),
                'class' => 'fixed-width-md',
                'form_group_class' => 'group-config-simple_popup group-config-position-simple',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Overlay').'</div>',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Overlay Popup'),
                'name' => 'overlay_popup',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Background Overlay Color'),
                'name' => 'bg_color_overlay_popup',
                'default' => '#000000',
                'form_group_class' => 'group-config-overlay',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Background').'</div>',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Background CSS'),
                'name' => 'background_css',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Background size'),
                'name' => 'bg_size',
                'default' => 'cover',
                'desc' => $this->l('Set CSS value for the background size. (Ex: contain, cover, 50% 100%, 100px 200px,..)'),
                'form_group_class' => 'group-config-background',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Background color'),
                'name' => 'bg_color',
                'default' => '',
                'form_group_class' => 'group-config-background',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script type="text/javascript" src="'.__PS_BASE_URI__.DeoHelper::getJsDir().'colorpicker/js/deo.jquery.colorpicker.js"></script>
                    ',
                'form_group_class' => 'hide',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Background Lazy load'),
                'name' => 'lazyload',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'group-config-background',
            ),
            array(
                'type' => 'selectImg',
                'label' => $this->l('Background image'),
                'href' => $href,
                'name' => 'bg_img',
                'lang' => true,
                'show_image' => true,
                'default' => '',
                'form_group_class' => 'group-config-background',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Background position'),
                'name' => 'bg_position',
                'default' => 'center',
                'desc' => $this->l('Set CSS value for the background image position. (Ex: center top, right bottom, 50% 50%, 100px 200px,..)'),
                'form_group_class' => 'group-config-background',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Background repeat'),
                'name' => 'bg_repeat',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'no-repeat',
                            'name' => $this->l('No repeat'),
                        ),
                        array(
                            'id' => 'repeat',
                            'name' => $this->l('Repeat All'),
                        ),
                        array(
                            'id' => 'repeat-x',
                            'name' => $this->l('repeat horizontally only'),
                        ),
                        array(
                            'id' => 'repeat-y',
                            'name' => $this->l('repeat vertically only'),
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'group-config-background',
            ),
        );
        
        return $input;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
        $this->helper->tpl_vars['link'] = Context::getContext()->link;
        $this->helper->tpl_vars['exception_list'] = $this->displayModuleExceptionList();
    }

    /**
     * overide in tabs module
     */
    public function adminContent($atts, $content = null, $tag_name = null, $is_gen_html = null)
    {   
        $this->preparaAdminContent($atts, $tag_name);
        if ($is_gen_html) {
            $assign = array();
            $assign['formAtts'] = $atts;
            $assign['deoInfo'] = $this->getInfo();
            $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
            $controller = new AdminDeoShortcodesController();
            return $controller->adminContent($assign, 'DeoPopup.tpl');
        } else {
            DeoShortCodesBuilder::doShortcode($content);
        }
    }

    /**
     * overide in tabs module
     */
    public function fontContent($atts, $content = null, $tag_name = null, $is_gen_html = null)
    {   
        $is_active = $this->isWidgetActive(array('formAtts' => $atts));
        if (!$is_active) {
            return '';
        }
        $this->context = Context::getContext();
        $uri = DeoHelper::getJsDir().'jquery.fancybox-transitions.js';
        $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));

        $uri = DeoHelper::getCssDir().'jquery.fancybox-transitions.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));

        foreach ($atts as $key => $val) {
            if (Tools::strpos($key, 'content') !== false || Tools::strpos($key, 'link') !== false || Tools::strpos($key, 'url') !== false || Tools::strpos($key, 'alt') !== false || Tools::strpos($key, 'tit') !== false || Tools::strpos($key, 'name') !== false || Tools::strpos($key, 'desc') !== false || Tools::strpos($key, 'itemscustom') !== false) {
                $atts[$key] = str_replace($this->str_search, $this->str_relace_html, $val);
            }
        }

        // validate module
        unset($is_gen_html);
        $form_atts = $atts;
        $form_atts['class'] = ((isset($form_atts['class']) && $form_atts['class']) ? $form_atts['class'].' ' : '');
        $form_atts['class'] .= ' '.$this->name;
        $form_atts['bg_data'] = '';

        $form_atts['active'] = 1;
        if (isset($form_atts['time_from']) && $form_atts['time_from'] && isset($form_atts['time_to']) && $form_atts['time_to']) {
            $now = time();
            $from = strtotime($form_atts['time_from']);
            $end = strtotime($form_atts['time_to']);

            if (($from <= $now) && ($now < $end)) {
                $form_atts['time_to'] = str_replace('-', '/', $form_atts['time_to']);
                $form_atts['time_from'] = str_replace('-', '/', $form_atts['time_from']);
            }else {
                $form_atts['active'] = 0;
            }
        }
       
        if (isset($form_atts['bg_color']) && $form_atts['bg_color']) {
            $form_atts['bg_data'] .= 'background-color:'.$form_atts['bg_color'].';';
        }

        if (isset($form_atts['bg_img']) && $form_atts['bg_img']) {
            $form_atts['bg_img'] = _THEME_IMG_DIR_.'modules/'.$this->module_name.'/'.$form_atts['bg_img'];
            if (isset($form_atts['lazyload']) && $form_atts['lazyload']) {
                
            }else{
                $form_atts['bg_data'] .= 'background-image:url('.$form_atts['bg_img'].');';
            }
        }
        if (isset($form_atts['bg_repeat']) && $form_atts['bg_repeat']) {
            $form_atts['bg_data'] .= 'background-repeat:'.$form_atts['bg_repeat'].';';
        }
        if (isset($form_atts['bg_position']) && $form_atts['bg_position']) {
            $form_atts['bg_data'] .= 'background-position:'.$form_atts['bg_position'].';';
        }
        if (isset($form_atts['bg_size']) && $form_atts['bg_size']) {
            $form_atts['bg_data'] .= 'background-size:'.$form_atts['bg_size'].';';
        }

        $str_relace = array('&', '\"', '\'', '', '', '', '[', ']', '+', '{', '}');
        if (isset($form_atts['margin']) && $form_atts['margin']) {
            $form_atts['margin'] = str_replace($this->str_search, $str_relace, $form_atts['margin']);
        }
        if (isset($form_atts['padding']) && $form_atts['padding']) {
            $form_atts['padding'] = str_replace($this->str_search, $str_relace, $form_atts['padding']);
        }

        if (isset($form_atts['time_wait']) && $form_atts['time_wait']) {
            $form_atts['time_wait'] = $form_atts['time_wait']*1000;
        }
        if (isset($form_atts['time_show_again']) && $form_atts['time_show_again']) {
            $form_atts['time_show_again'] = $form_atts['time_show_again']*1000;
        }
        if (isset($form_atts['time_close']) && $form_atts['time_close']) {
            $form_atts['time_close'] = $form_atts['time_close']*1000;
        }

        $assign = array();
        $assign['formAtts'] = $form_atts;
        $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
        $module = DeoTemplate::getInstance();
        return $module->fontContent($assign, $this->name.'.tpl');
    }


    public function hex2rgba($color, $opacity = false) {
 
        $default = 'rgb(0,0,0)';
     
        //Return default if no color provided
        if(empty($color))
              return $default; 
     
        //Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
            $color = Tools::substr( $color, 1 );
        }
 
        //Check if color has 6 or 3 characters and get values
        if (Tools::strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( Tools::strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }
 
        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);
 
        //Check if opacity is set(rgba or rgb)
        if($opacity){
            if(abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",",$rgb).')';
        }
 
        //Return rgb(a) color string
        return $output;
    }
}
