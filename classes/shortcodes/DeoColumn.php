<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoColumn extends DeoShortCodeBase
{
    public $name = 'DeoColumn';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Column', 
            'position' => 2, 
            'desc' => $this->l('A column can have one or more widget'),
            'tag' => 'content structure',
        );
    }

    public function getAdditionConfig()
    {
        return array(
            array(
                'type' => '',
                'name' => 'xxl',
                'default' => '12'
            ),
            array(
                'type' => '',
                'name' => 'xl',
                'default' => '12'
            ),
            array(
                'type' => '',
                'name' => 'lg',
                'default' => '12'
            ),
            array(
                'type' => '',
                'name' => 'md',
                'default' => '12'
            ),
            array(
                'type' => '',
                'name' => 'sm',
                'default' => '12'
            ),
            array(
                'type' => '',
                'name' => 'xs',
                'default' => '12'
            ),
            array(
                'type' => '',
                'name' => 'sp',
                'default' => '12'
            )
        );
    }

    public function getConfigList()
    {
        $input = array(
            array(
                'type' => 'tabConfig',
                'name' => 'tabConfig',
                'values' => array(
                    'tab_general' => $this->l('General'),
                    'tab_styles' => $this->l('Responsive'),
                    // 'tab_animation' => $this->l('Animation'),
                    'tab_exceptions' => $this->l('Exceptions'))
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
                'type' => 'DeoColumnClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'values' => '',
                'default' => '',
                'form_group_class' => 'tab_general',
            ),
            array(
                'type' => 'column_width',
                'name' => 'width',
                'values' => '',
                'columnGrids' => DeoSetting::getColumnGrid(),
                'form_group_class' => 'tab_styles',
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
                'form_group_class' => 'tab_exceptions specific_type_sub specific_type-all',
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
        $this->helper->tpl_vars['exception_list'] = $this->displayModuleExceptionList();
    }
    
    public function displayModuleExceptionList()
    {
        $controllers = array();
        $controllers_modules = array();
        $controllers_modules['admin'] = array();
        $controllers_modules['front'] = array();
        
        if (Tools::getValue('reloadControllerException')) {
            $controllers = DeoSetting::getPages();
            $controllers_modules = array(
                // 'admin' => Dispatcher::getModuleControllers('admin'),
                'front' => Dispatcher::getModuleControllers('front'),
            );
            
            DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_FRONT_CONTROLLER_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers)));
            DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_FRONT_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['front'])));
            // DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_ADMIN_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['admin'])));
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
                $controllers_modules['front'] = Dispatcher::getModuleControllers('front');
                DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_FRONT_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['front'])));
            } else {
                # Second Time : read from config
                $controllers_modules['front'] = json_decode(DeoHelper::correctDeCodeData(DeoHelper::getConfig('CACHE_FRONT_MODULE_EXCEPTION')), true);
            }
            
            // if (DeoHelper::getConfig('CACHE_ADMIN_MODULE_EXCEPTION') === false) {
            //     # First Time : write to config
            //     $controllers_modules['admin'] = Dispatcher::getModuleControllers('admin');
            //     DeoHelper::updateValue(DeoHelper::getConfigName('CACHE_ADMIN_MODULE_EXCEPTION'), DeoHelper::correctEnCodeData(json_encode($controllers_modules['admin'])));
            // } else {
            //     # Second Time : read from config
            //     $controllers_modules['admin'] = json_decode(DeoHelper::correctDeCodeData(DeoHelper::getConfig('CACHE_ADMIN_MODULE_EXCEPTION')), true);
            // }
        }
        
        $controller = Tools::getValue('controller_pages');
        $arr_controllers = explode(',', $controller);
        $arr_controllers = array_map('trim', $arr_controllers);
        
        $modules_controllers_type = array('front' => $this->l('Front modules controller'));
        // $modules_controllers_type = array('front' => $this->l('Front modules controller'), 'admin' => $this->l('Admin modules controller'));
        Context::getContext()->smarty->assign(array(
            '_core_' => $this->l('________________________________________ Core pages ________________________________________'),
            'controller' => $controller,
            'arr_controllers' => $arr_controllers,
            'controllers' => $controllers,
            'modules_controllers_type' => $modules_controllers_type,
            'controllers_modules' => $controllers_modules,
        ));
        $content = Context::getContext()->smarty->fetch(DeoHelper::getShortcodeTemplatePath('DeoColumn.tpl'));
        return $content;
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        if (!isset($assign['formAtts']['animation']) || $assign['formAtts']['animation'] == 'none') {
            $assign['formAtts']['animation'] = 'none';
            $assign['formAtts']['animation_delay'] = '';
        } elseif ($assign['formAtts']['animation'] != 'none') {
            // validate module
            // add more config for animation
            if ((int)$assign['formAtts']['animation_delay'] >= 0) {
                $assign['formAtts']['animation_delay'] .= 's';
            } else {
                $assign['formAtts']['animation_delay'] = '1s';
            }
            if (isset($assign['formAtts']['animation_duration']) && (int)$assign['formAtts']['animation_duration'] >= 0) {
                $assign['formAtts']['animation_duration'] .= 's';
            } else {
                $assign['formAtts']['animation_duration'] = '1s';
            }
            if (isset($assign['formAtts']['animation_iteration_count']) && (int)$assign['formAtts']['animation_iteration_count'] > 0) {
                $assign['formAtts']['animation_iteration_count'] = (int)$assign['formAtts']['animation_iteration_count'];
            } else {
                $assign['formAtts']['animation_iteration_count'] = 1;
            }
        };
        $assign['formAtts']['class'] = str_replace('.', '-', $assign['formAtts']['class']);
        return $assign;
    }
}
