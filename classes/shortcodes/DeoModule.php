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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class DeoModule extends DeoShortCodeBase
{

    public $name = 'DeoModule';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Module',
            'position' => 5,
            'desc' => $this->l('Custom moule'),
            'icon_class' => 'icon-copy',
            'tag' => 'module',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        if (Tools::getIsset('edit')) {
            $name_module = Tools::getValue('name_module');
        } else {
            $name_module = Tools::getValue('type_shortcode');
        }
        if (!$name_module) {
            return array();
        }
        
        # GET HOOK
        if (($module_instance = Module::getInstanceByName($name_module))) {
            if ($module_instance instanceof WidgetInterface) {
                # module has method function renderWidget()
                $hooks = DeoSetting::getOverrideHook();
                $arr = array(
                    array(
                    'id' => '',
                    'name' => $this->l('--------- Select a Hook ---------')
                    )
                );
                foreach ($hooks as $hook) {
                    $arr[] = array(
                        'id' => $hook,
                        'name' => $hook,
                    );
                }
            }
        }

        $modules_disable_cache = array('ps_customersignin','ps_shoppingcart','ps_languageselector');

        $inputs_class = array(
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('Class'),
                'default' => '',
            ),
        );

        $inputs_accordion = array(
            array(
                'type' => 'select',
                'label' => $this->l('Toogle'),
                'name' => 'accordion',
                'class' => ' fixed-width-xxl',
                'default' => 'disable_accordion',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'disable_accordion',
                            'name' => $this->l('None'),
                        ),
                        array(
                            'id' => 'accordion_small_screen',
                            'name' => $this->l('Accordion at tablet (screen <= 768px)'),
                        ),
                        array(
                            'id' => 'accordion_mobile_screen',
                            'name' => $this->l('Accordion at mobile (screen <= 576px)'),
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),            
        );
        
        $inputs_content = array(
            // array(
            //     'type' => 'html',
            //     'name' => 'default_html',
            //     'html_content' => '<div class="alert alert-info">Module name: <b>"'.$name_module
            //     .'"</b><input type="hidden" id="select-hook-error" value="'.$this->l('Please select a hook').'"/>
            //                                                                     <input type="hidden" id="name-module" name="name_module" value="'.$name_module.'"/>
            //                                                             </div>',
            // ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<input type="hidden" id="name-module" name="name_module" value="'.$name_module.'"/>',
                'form_group_class' => 'hide',
            ),
            array(
                'type' => 'select',
                'id' => 'select-hook',
                'label' => $this->l('Select hook of module (*)'),
                'name' => 'hook',
                'class' => ' fixed-width-xxl',
                'options' => array('query' => $arr,
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Disable Cache Smarty'),
                'desc' => $this->l('Use for module have the change content when client. Only work when enable Cache Smarty'),
                'name' => 'disable_cache',
                'values' => DeoSetting::returnYesNo(),
                'default' => (in_array($name_module, $modules_disable_cache)) ? '1' : '0',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Remove display'),
                'desc' => $this->l('This module will remove in this hook'),
                'name' => 'is_display',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-danger">'.$this->l('Please consider using this function.
                                    This function is only for advance user,
                                    It will load other module and display in column of Deotemplate.
                                    With some module have ID in wrapper DIV, your site will have Javascript Conflicts.
                                    We will not support this error.').'</div>',
            )
        );

        $inputs_additional = array();

        if (in_array($name_module, DeoHelper::getModulesAccordion())){
            $inputs_additional = array_merge($inputs_accordion, $inputs_additional);
        }

        if (in_array($name_module, DeoHelper::getModulesClass())){
            $inputs_additional = array_merge($inputs_class, $inputs_additional);
        }
        
        $inputs = array_merge($inputs_additional, $inputs_content);

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
        $form_attr = $assign['formAtts'];
        $context = Context::getContext();
        if (isset($form_attr['hook']) && isset($form_attr['name_module']) && Module::isEnabled($form_attr['name_module'])) {
            $content = $this->execModuleHook($form_attr['hook'], array(), $form_attr['name_module'], false, $context->shop->id);
            if (in_array($form_attr['name_module'], DeoHelper::getModulesAccordion()) && isset($form_attr['accordion'])){
                $content = str_replace('deo_accordion_class', $form_attr['accordion'], $content);
            } 
            if (in_array($form_attr['name_module'], DeoHelper::getModulesClass())){
                $class = (isset($form_attr['class'])) ? $form_attr['class'] : '';
                $content = str_replace('deo_class', $class, $content);
            }

            $assign['deo_html_content'] = $content;
        }

        return $assign;
    }

    public static function execModuleHook($hook_name = null, $hook_args = array(), $module_name = null, $use_push = false, $id_shop = null)
    {
        static $disable_non_native_modules = null;
        if ($disable_non_native_modules === null) {
            $disable_non_native_modules = (bool)Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        }
        // Check arguments validity
        if (!Validate::isModuleName($module_name) || !Validate::isHookName($hook_name)) {
            return '';
        }
        //throw new PrestaShopException('Invalid module name or hook name');
        // If no modules associated to hook_name or recompatible hook name, we stop the function
        if (!Hook::getHookModuleExecList($hook_name)) {
            return '';
        }
        // Check if hook exists
        if (!$id_hook = Hook::getIdByName($hook_name)) {
            return false;
        }
        // Store list of executed hooks on this page
        Hook::$executed_hooks[$id_hook] = $hook_name;
        $context = Context::getContext();
        if (!isset($hook_args['cookie']) || !$hook_args['cookie']) {
            $hook_args['cookie'] = $context->cookie;
        }
        if (!isset($hook_args['cart']) || !$hook_args['cart']) {
            $hook_args['cart'] = $context->cart;
        }

        // Look on modules list
        $altern = 0;
        $output = '';
        if ($disable_non_native_modules && !isset(Hook::$native_module)) {
            Hook::$native_module = Module::getNativeModuleList();
        }
        $different_shop = false;
        if ($id_shop !== null && Validate::isUnsignedId($id_shop) && $id_shop != $context->shop->getContextShopID()) {
            $old_context = $context->shop->getContext();
            $old_shop = clone $context->shop;
            $shop = new Shop((int)$id_shop);
            if (Validate::isLoadedObject($shop)) {
                $context->shop = $shop;
                $context->shop->setContext(Shop::CONTEXT_SHOP, $shop->id);
                $different_shop = true;
            }
        }
        // Check errors
        if ((bool)$disable_non_native_modules && Hook::$native_module && count(Hook::$native_module) && !in_array($module_name, self::$native_module)) {
            return;
        }
        if (!($module_instance = Module::getInstanceByName($module_name))) {
            return;
        }
        
        // if ($use_push && !$module_instance->allow_push) {
        //     return;
        // }

        // Check which / if method is callable
        $hook_callable = is_callable(array($module_instance, 'hook'.$hook_name));
        if ($hook_callable ) {
            $hook_args['altern'] = ++$altern;
            if ($use_push && isset($module_instance->push_filename) && file_exists($module_instance->push_filename)) {
                Tools::waitUntilFileIsModified($module_instance->push_filename, $module_instance->push_time_limit);
            }
            $display = $module_instance->{'hook'.$hook_name}($hook_args);
            $output .= $display;
        } elseif (Hook::isDisplayHookName($hook_name)) {
            if ($module_instance instanceof WidgetInterface) {
                $display = Hook::coreRenderWidget($module_instance, $hook_name, $hook_args);
                
                $output .= $display;
            }
        }
            
        if ($different_shop) {
            $context->shop = $old_shop;
            $context->shop->setContext($old_context, $shop->id);
        }
        return $output; // Return html string
    }
}
