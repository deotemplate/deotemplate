<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateHookModel.php');

class DeoRow extends DeoShortCodeBase
{
    public $name = 'DeoRow';
    public $for_module = 'manage';
    public $show_upload = '1';
    public $atribute = array('el_class' => '');

    public function getInfo()
    {
        return array(
            'label' => 'Row', 
            'position' => 1,
            'desc' => $this->l('Each row can have one or more Column'),
            'tag' => 'content structure',
        );
    }

    public function getConfigList()
    {   
        Context::getContext()->smarty->assign('path_image', DeoHelper::getImgThemeUrl());

        // $tabs =  array(
        //     'tab_general' => $this->l('General'),
        //     'tab_style' => $this->l('Style'),
        //     'tab_background' => $this->l('Background'),
        //     // 'tab_animation' => $this->l('Animation'),
        //     'tab_exceptions' => $this->l('Exceptions'))
        // );

        // $inputs_head = array(
        //     array(
        //         'type' => 'tabConfig',
        //         'name' => 'tabConfig',
        //         'values' => $tabs,
        //     ),
        //     array(
        //         'type' => 'text',
        //         'name' => 'title',
        //         'label' => $this->l('Title'),
        //         'desc' => $this->l('Auto hide if leave it blank'),
        //         'lang' => 'true',
        //         'form_group_class' => 'tab_general',
        //         'default' => ''
        //     ),
        //     array(
        //         'type' => 'textarea',
        //         'name' => 'sub_title',
        //         'label' => $this->l('Sub Title'),
        //         'lang' => true,
        //         'values' => '',
        //         'autoload_rte' => false,
        //         'form_group_class' => 'tab_general',
        //         'default' => ''
        //     ),
        //     array(
        //         'type' => 'text',
        //         'name' => 'id',
        //         'label' => $this->l('ID'),
        //         'form_group_class' => 'tab_general',
        //         'desc' => $this->l('Use for css and javascript'),
        //         'default' => ''
        //     ),
        // );

        $input = array(
            array(
                'type' => 'tabConfig',
                'name' => 'tabConfig',
                'default' => Tools::getValue('tab_open') ? Tools::getValue('tab_open') : 'tab_general',
                'values' => array(
                    'tab_general' => $this->l('General'),
                    'tab_style' => $this->l('Style'),
                    'tab_background' => $this->l('Background'),
                    // 'tab_animation' => $this->l('Animation'),
                    'tab_exceptions' => $this->l('Exceptions')
                ),
                'save' => false,
            ),
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'form_group_class' => 'tab_general',
                'default' => ''
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'form_group_class' => 'tab_general',
                'default' => ''
            ),
            array(
                'type' => 'text',
                'name' => 'id',
                'label' => $this->l('ID'),
                'form_group_class' => 'tab_general',
                'desc' => $this->l('Use for css and javascript'),
                'default' => ''
            ),
            array(
                'type' => 'text',
                'name' => 'container',
                'label' => $this->l('Class container'),
                'form_group_class' => ($this->getHookLayout()) ? 'tab_general' : 'tab_general hide-config',
                'desc' => $this->getDescriptionContainerInput(),
                // 'default' => ($this->getHookLayout()) ? 'container' : '',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'form_group_class' => 'tab_general',
                'html_content' => $this->getInfomationsRow(),
            ),
            array(
                'type' => 'DeoRowClass',
                'name' => 'class',
                'label' => 'CSS Class',
                'form_group_class' => 'tab_general',
                'default' => 'row'
            ),
            // array(
            //     'type' => 'text',
            //     'name' => 'min_height',
            //     'label' => $this->l('Minimum height'),
            //     'desc' => $this->l('You can use pixels : 10px or percents : 10%.'),
            //     'default' => '',
            //     'form_group_class' => 'tab_style',
            // ),
            array(
                'type' => 'text',
                'label' => $this->l('Margin Top'),
                'name' => 'margin_top',
                'desc' => $this->l('You can use pixels :10px or percents : 10%.'),
                'default' => '',
                'form_group_class' => 'tab_style',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Margin Bottom'),
                'name' => 'margin_bottom',
                'desc' => $this->l('You can use pixels :10px or percents : 10%.'),
                'default' => '',
                'form_group_class' => 'tab_style',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Padding Top'),
                'name' => 'padding_top',
                'desc' => $this->l('You can use pixels :10px or percents : 10%.'),
                'default' => '',
                'form_group_class' => 'tab_style',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Padding Bottom'),
                'name' => 'padding_bottom',
                'desc' => $this->l('You can use pixels : 10px or percents : 10%.'),
                'default' => '',
                'form_group_class' => 'tab_style',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Background Config'),
                'name' => 'bg_config',
                'class' => 'form-action',
                'default' => 'none',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'fullwidth',
                            'name' => $this->l('Full width'),
                        ),
                        array(
                            'id' => 'boxed',
                            'name' => $this->l('Boxed'),
                        ),
                        array(
                            'id' => 'none',
                            'name' => $this->l('None'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_background',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Background color'),
                'name' => 'bg_color',
                'default' => '',
                'form_group_class' => 'tab_background group-config-background',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Background Type'),
                'name' => 'bg_config_type',
                'class' => 'form-action',
                'default' => 'image',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'image',
                            'name' => $this->l('Image'),
                        ),
                        array(
                            'id' => 'video_link',
                            'name' => $this->l('Video Link'),
                        ),
                        array(
                            'id' => 'video_youtube',
                            'name' => $this->l('Video Youtube'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_background group-config-background',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Background Video Youtube').'</div>',
                'form_group_class' => 'tab_background group-config-background group-config-background-video-youtube',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Source Youtube Video'),
                'name' => 'bg_video_youtube',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'default' => '',
                'desc' => $this->l('Example embed video: ').htmlspecialchars('"<iframe width="1280" height="720" src="https://www.youtube.com/embed/Elim33GXrTw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>"'),
                'autoload_rte' => false,
                'form_group_class' => 'tab_background group-config-background group-config-background-video-youtube',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Background Video Link').'</div>',
                'form_group_class' => 'tab_background group-config-background group-config-background-video-link',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Source Link Video'),
                'name' => 'bg_video_link',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'default' => '',
                'desc' => $this->l('Link access video that you upload on your hosting. Support video formats *.mp4, *.webm or *.ogg.'),
                'autoload_rte' => false,
                'form_group_class' => 'tab_background group-config-background group-config-background-video-link',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Background Image').'</div>',
                'form_group_class' => 'tab_background group-config-background group-config-background-image',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Background Image Type'),
                'name' => 'bg_type',
                'class' => 'form-action',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'normal',
                            'name' => $this->l('Normal'),
                        ),
                        array(
                            'id' => 'fixed',
                            'name' => $this->l('Fixed'),
                        ),
                        array(
                            'id' => 'parallax',
                            'name' => $this->l('Parallax'),
                        ),
                        // array(
                        //    'id' => 'video_youtube',
                        //    'name' => $this->l('Video Youtube'),
                        // ),
                        //                        array(
                        //    'id' => 'video_vimeo',
                        //    'name' => $this->l('Vimeo video'),
                        // ),
                        //                        array(
                        //    'id' => 'video_html5',
                        //    'name' => $this->l('HTML5'),
                        // )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_background group-config-background group-config-background-image',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Background size'),
                'name' => 'bg_size',
                'desc' => $this->l('Set CSS value for the background size. (Ex: contain, cover, 50% 100%, 100px 200px,..)'),
                'form_group_class' => 'tab_background group-config-background group-config-background-image',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Background position'),
                'name' => 'bg_position',
                'desc' => $this->l('Set CSS value for the background image position. (Ex: center top, right bottom, 50% 50%, 100px 200px,..)'),
                'form_group_class' => 'tab_background bg_type-normal bg_type-parallax bg_type-fixed group-config-background  group-config-background-image',
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
                            'name' => $this->l('Repeat all'),
                        ),
                        array(
                            'id' => 'repeat-x',
                            'name' => $this->l('Repeat horizontally'),
                        ),
                        array(
                            'id' => 'repeat-y',
                            'name' => $this->l('Repeat vertically'),
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_background group-config-background group-config-background-image',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script type="text/javascript" src="'.__PS_BASE_URI__.DeoHelper::getJsDir().'colorpicker/js/deo.jquery.colorpicker.js"></script>',
                'form_group_class' => 'tab_background hide',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Background Lazy load'),
                'name' => 'lazyload',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'tab_background group-config-background group-config-background-image',
            ),
            array(
                'type' => 'bg_img',
                'label' => $this->l('Background image'),
                'name' => 'bg_img',
                'img_link' => _THEME_IMG_DIR_.'modules/'.$this->module_name.'/images/',
                'default' => '',
                'form_group_class' => 'tab_background group-config-background group-config-background-image',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Specific Controller'),
                'name' => 'specific_type',
                'class' => 'form-action',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'all',
                            'name' => $this->l('Show on all Page Controller'),
                        ),
                        array(
                            'id' => 'index',
                            'name' => $this->l('Show on only Index'),
                        ),
                        array(
                            'id' => 'category',
                            'name' => $this->l('Show on only Category'),
                        ),
                        array(
                            'id' => 'product',
                            'name' => $this->l('Show on only Product'),
                        ),
                        array(
                            'id' => 'cms',
                            'name' => $this->l('Show on only CMS'),
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'form_group_class' => 'tab_exceptions',
                'default' => 'all'
            ),
            array(
                'type' => 'reloadControler',
                'name' => 'reloadControler',
                'default' => '',
                'form_group_class' => 'tab_exceptions  specific_type_sub specific_type-all',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Controller ID'),
                'name' => 'controller_id',
                'desc' => $this->l('Example: 1,2,3'),
                'default' => '',
                'form_group_class' => 'tab_exceptions specific_type_sub specific_type-category specific_type-product specific_type-cms',
            ),
            array(
                'type' => 'DeoExceptions',
                'name' => 'controller_pages',
                'form_group_class' => 'tab_exceptions specific_type_sub specific_type-all',
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
    
    public function displayModuleExceptionList()
    {
        $controllers = array();
        $controllers_modules = array();
        $controllers_modules['admin'] = array();
        $controllers_modules['front'] = array();
        
        if (Tools::getValue('reloadControllerException')) {
            $controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
            $controllers_modules = array(
                'admin' => Dispatcher::getModuleControllers('admin'),
                'front' => Dispatcher::getModuleControllers('front'),
            );
            
            DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_FRONT_CONTROLLER_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers)));
            DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_FRONT_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['admin'])));
            DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_ADMIN_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['front'])));
        } else {
            if (DeoHelper::getConfig('CACHE_FRONT_CONTROLLER_EXCEPTION') === false) {
                # First Time : write to config
                $controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
                DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_FRONT_CONTROLLER_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers)));
            } else {
                # Second Time : read from config
                $controllers = json_decode(DeoHelper::correctDeCodeData(DeoHelper::getConfig('CACHE_FRONT_CONTROLLER_EXCEPTION')), true);
            }
            
            if (DeoHelper::getConfig('CACHE_FRONT_MODULE_EXCEPTION') === false) {
                # First Time : write to config
                $controllers_modules['admin'] = Dispatcher::getModuleControllers('admin');
                DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_FRONT_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['admin'])));
            } else {
                # Second Time : read from config
                $controllers_modules['admin'] = json_decode(DeoHelper::correctDeCodeData(DeoHelper::getConfig('CACHE_FRONT_MODULE_EXCEPTION')), true);
            }
            
            if (DeoHelper::getConfig('CACHE_ADMIN_MODULE_EXCEPTION') === false) {
                # First Time : write to config
                $controllers_modules['front'] = Dispatcher::getModuleControllers('front');
                DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_ADMIN_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['front'])));
            } else {
                # Second Time : read from config
                $controllers_modules['front'] = json_decode(DeoHelper::correctDeCodeData(DeoHelper::getConfig('CACHE_ADMIN_MODULE_EXCEPTION')), true);
            }
        }
        
        $controller = Tools::getValue('controller_pages');
        $arr_controllers = explode(',', $controller);
        $arr_controllers = array_map('trim', $arr_controllers);

        $modules_controllers_type = array('front' => $this->l('Front modules controller'), 'admin' => $this->l('Admin modules controller'));
        Context::getContext()->smarty->assign(array(
            '_core_' => $this->l('________________________________________ CORE ________________________________________'),
            'controller' => $controller,
            'arr_controllers' => $arr_controllers,
            'controllers' => $controllers,
            'modules_controllers_type' => $modules_controllers_type,
            'controllers_modules' => $controllers_modules,
        ));
        $content = Context::getContext()->smarty->fetch(DeoHelper::getShortcodeTemplatePath('DeoRow.tpl'));
        return $content;
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        $form_atts = $assign['formAtts'];

        //process back-ground
        $form_atts['bg_class'] = '';
        $form_atts['bg_data'] = '';
        $form_atts['parallax'] = '';
        $form_atts['bg_video'] = '';

        if (!DeoHelper::getLazyload()) {
            $form_atts['lazyload'] = 0;
        }

        //1. set class
        if (isset($form_atts['bg_config']) && $form_atts['bg_config'] != 'none') {
            if (isset($form_atts['bg_color']) && $form_atts['bg_color']) {
                $form_atts['bg_data'] .= 'background-color:'.$form_atts['bg_color'].';';
            }

            if (isset($form_atts['bg_video_youtube']) && $form_atts['bg_video_youtube']) {
                $form_atts['bg_video_youtube'] = str_replace($this->str_search, $this->str_relace, $form_atts['bg_video_youtube']);
            }

            $form_atts['bg_class'] = 'has-bg';
            if ($form_atts['bg_config'] == 'boxed') {
                $form_atts['bg_class'] .= ' bg-boxed';
            } else {
                if (isset($form_atts['container']) && $form_atts['container']) {
                    $form_atts['bg_class'] .= ' bg-fullwidth-container';
                } else {
                    $form_atts['id'] = (isset($form_atts['id']) && $form_atts['id'] != '') ? $form_atts['id'] : $form_atts['form_id'];
                    $form_atts['bg_class'] .= ' bg-fullwidth';
                }
            }
           
            if (isset($form_atts['bg_img']) && $form_atts['bg_img']) {
                $form_atts['bg_img'] = _THEME_IMG_DIR_.'modules/'.$this->module_name.'/'.$form_atts['bg_img'];
                if (isset($form_atts['lazyload']) && $form_atts['lazyload']) {
                    
                }else{
                    $form_atts['bg_data'] .= 'background-image:url('.$form_atts['bg_img'].');';
                }
                if (isset($form_atts['bg_repeat'])) {
                    $form_atts['bg_data'] .= 'background-repeat:'.$form_atts['bg_repeat'].';';
                }
            }else{
                $form_atts['lazyload'] = 0; 
            }

            if (isset($form_atts['bg_type']) && $form_atts['bg_type'] == 'fixed') {
                $form_atts['bg_data'] .= 'background-attachment:fixed;';
            }
            if (isset($form_atts['bg_position']) && $form_atts['bg_position']) {
                $form_atts['bg_data'] .= 'background-position:'.$form_atts['bg_position'].';';
            }
            if (isset($form_atts['bg_size']) && $form_atts['bg_size']) {
                $form_atts['bg_data'] .= 'background-size:'.$form_atts['bg_size'].';';
            }

            //config for background style - stela - stela
            if (isset($form_atts['bg_type']) && $form_atts['bg_type'] == 'parallax' && isset($form_atts['bg_img']) && $form_atts['bg_img']) {
                $form_atts['bg_data'] .= 'background-attachment:fixed;';
                $form_atts['bg_class'] .= ' bg-parallax';
                $form_atts['id'] = (isset($form_atts['id']) && $form_atts['id'] != '') ? $form_atts['id'] : $form_atts['form_id'];

                $form_atts['parallax'] = 'data-stellar-background-ratio=0.1';
            }
        }else{
            $form_atts['lazyload'] = 0;
        }

        if (isset($form_atts['bg_config_type']) && $form_atts['bg_config_type'] != 'image'){
            $form_atts['bg_img'] = '';
        }else{
            if (isset($form_atts['bg_img']) && isset($form_atts['img_link'])) {
                if ($form_atts['bg_img'] == '' && $form_atts['img_link'] != '') {
                    // validate module
                    $form_atts['bg_img'] = $form_atts['img_link'];
                }
            }
        }

        if (!isset($form_atts['animation']) || $form_atts['animation'] == 'none') {
            $form_atts['animation'] = 'none';
            $form_atts['animation_delay'] = '';
        } elseif ($form_atts['animation'] != 'none') {
            // validate module
            // add more config for animation
            if ((int)$form_atts['animation_delay'] >= 0) {
                $form_atts['animation_delay'] .= 's';
            } else {
                $form_atts['animation_delay'] = '1s';
            }
            
            if (isset($form_atts['animation_duration']) && (int)$form_atts['animation_duration'] >= 0) {
                $form_atts['animation_duration'] .= 's';
            } else {
                $form_atts['animation_duration'] = '1s';
            }
            
            if (isset($form_atts['animation_iteration_count']) && (int)$form_atts['animation_iteration_count'] > 0) {
                $form_atts['animation_iteration_count'] = (int)$form_atts['animation_iteration_count'];
            } else {
                $form_atts['animation_iteration_count'] = 1;
            }
        };

        # set style
        $assign['formAtts'] = $form_atts;
        $assign['formAtts']['bg_style'] = $form_atts['bg_data'];
        $assign['formAtts']['css_style'] = $this->showCSSStyle($assign);
        $this->checkFullwidth($assign);

        return $assign;
    }

    public function checkFullwidth(&$assign)
    {
        $page_name = DeoHelper::getPageName();
        $hook_name = DeoShortCodesBuilder::$hook_name;

        $hook_model = new DeoTemplateHookModel();
        $hook_model->create();
        if ($page_name == 'index') {
            $hooks = $hook_model->fullwidth_index_hook;
        } else {
            $hooks = $hook_model->fullwidth_other_hook;
        }

        $assign['formAtts']['has_container'] = '0';
        # remove container class - BEGIN
        if (isset($assign['formAtts']['container']) && $assign['formAtts']['container']) {
            $str_search = array('/\bcontainer\b(?!-)/');
            $str_replace = array('');
            $str_subject = $assign['formAtts']['container'];

            $assign['formAtts']['container'] = preg_replace($str_search, $str_replace, $str_subject);
            $assign['formAtts']['has_container'] = '1';
        }else{
            $assign['formAtts']['container'] = '';
        }
        # remove container class - END
    }

    public function getHookLayout($page = 'index')
    {
        $hook_name = Tools::getValue('hook_name');

        $hook_model = new DeoTemplateHookModel();
        $hook_model->create();

        return $hook_model->fullwidthHook($hook_name, $page);
    }

    /**
     * Live
     * not follow in database
     */
    public function getRowLayOut($hook_layout)
    {
        $row_layout = DeoSetting::ROW_BOXED;
        if ($hook_layout == DeoSetting::HOOK_FULWIDTH_INDEXPAGE) {
            $row_container = Tools::getValue('container');
            if (!preg_match('/\bcontainer\b(?!-)/', $row_container)) {
                // validate module
                $row_layout = DeoSetting::ROW_FULWIDTH_INDEXPAGE;
            }
        }

        return $row_layout;
    }

    public function getDescriptionContainerInput()
    {
        $desc = Context::getContext()->smarty->fetch(DeoHelper::getShortcodeTemplatePath('DeoRowContainerClassDesciption.tpl'));

        return $desc;
    }

    public function getInfomationsRow()
    {
        $hook_layout = $this->getHookLayout();
        $row_layout = $this->getRowLayOut($hook_layout);

        $id_profile = Tools::getValue('id_deotemplate_profiles');
        $url_profile_edit = Context::getContext()->link->getAdminLink('AdminDeoProfiles').
                '&id_deotemplate_profiles='.$id_profile.'&tab_open=tab_layout&updatedeotemplate_profiles';

        $hook_name = Tools::getValue('hook_name');

        $hook_layout_other = $this->getHookLayout('other');
        $row_layout_other = $this->getRowLayOut($hook_layout_other);

        Context::getContext()->smarty->assign(array(
            'row_layout' => $row_layout,
            'hook_layout' => $hook_layout,

            'row_layout_other' => $row_layout_other,
            'hook_layout_other' => $hook_layout_other,

            'hook_name' => $hook_name,

            'url_profile_edit' => $url_profile_edit,
        ));
        
        $desc = Context::getContext()->smarty->fetch(DeoHelper::getShortcodeTemplatePath('DeoRowInformations.tpl'));

        return $desc;
    }

    public function showCSSStyle($assign)
    {
        $form_atts = $assign['formAtts'];
        $style = '';
        // if (isset($form_atts['bg_config']) && $form_atts['bg_config'] == 'boxed' && isset($form_atts['bg_data']) && $form_atts['bg_data']) {
        //     $style .= $form_atts['bg_data'];
        // }
        // if (isset($form_atts['min_height']) && $form_atts['min_height']) {
        //     $style .= 'min-height: '.$form_atts['min_height'].';';
        // }
        if (isset($form_atts['margin_top']) && $form_atts['margin_top']) {
            $style .= 'margin-top: '.$form_atts['margin_top'].';';
        }
        if (isset($form_atts['margin_bottom']) && $form_atts['margin_bottom']) {
            $style .= 'margin-bottom: '.$form_atts['margin_bottom'].';';
        }
        if (isset($form_atts['padding_top']) && $form_atts['padding_top']) {
            $style .= 'padding-top: '.$form_atts['padding_top'].';';
        }
        if (isset($form_atts['padding_bottom']) && $form_atts['padding_bottom']) {
            $style .= 'padding-bottom: '.$form_atts['padding_bottom'].';';
        }
        return $style;
    }
    
    public function getPageName()
    {
        // Are we in a payment module
        $module_name = '';
        if (Validate::isModuleName(Tools::getValue('module'))) {
            $module_name = Tools::getValue('module');
        }

        if (!empty($this->page_name)) {
            $page_name = $this->page_name;
        } elseif (!empty($this->php_self)) {
            $page_name = $this->php_self;
        } elseif (Tools::getValue('fc') == 'module' && $module_name != '' && (Module::getInstanceByName($module_name) instanceof PaymentModule)) {
            $page_name = 'module-payment-submit';
        } elseif (preg_match('#^'.preg_quote(Context::getContext()->shop->physical_uri, '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m)) {
            // @retrocompatibility Are we in a module ?
            $page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
        } else {
            $page_name = Dispatcher::getInstance()->getController();
            $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
        }

        return $page_name;
    }
}
