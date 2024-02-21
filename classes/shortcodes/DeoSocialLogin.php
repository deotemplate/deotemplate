<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoSocialLogin extends DeoShortCodeBase
{
    public $name = 'DeoSocialLogin';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Social Login', 
            'position' => 3, 
            'desc' => $this->l('Show form social login'),
            'image' => 'social-login.png',
            'tag' => 'social',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        // create list type
        $list_type = array(
            'popup' => $this->l('Popup'),
            'slidebar_left' => $this->l('Slidebar Left'),
            'slidebar_right' => $this->l('Slidebar Right'),
            'slidebar_top' => $this->l('Slidebar Top'),
            'slidebar_bottom' => $this->l('Slidebar Bottom'),
            'dropdown' => $this->l('Drop Down'),
            'dropup' => $this->l('Drop Up'),
            'html' => $this->l('HTML'),
        );

        // create list layout
        $list_layout = array(
            'login' => $this->l('Only Login Form'),
            'register' => $this->l('Only Register Form'),
            'both' => $this->l('Both login and register form'),
        );

        $select_list_type = array();
        foreach ($list_type as $key => $value) {
            $select_list_type[] = array('id' => $key, 'name' => $value);
        }

        $select_list_layout = array();
        foreach ($list_layout as $key => $value) {
            $select_list_layout[] = array('id' => $key, 'name' => $value);
        }

        $inputs = array(
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Show Layout Form'),
                'name' => 'quicklogin_type',
                'options' => array(
                    'query' => $select_list_type,
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l(' Display Form'),
                'name' => 'quicklogin_layout',
                'options' => array(
                    'query' => $select_list_layout,
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Display Text'),
                'name' => 'quicklogin_display',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'login',
                            'name' => $this->l('Login'),
                        ),
                        array(
                            'id' => 'register',
                            'name' => $this->l('Register'),
                        ),
                        array(
                            'id' => 'both',
                            'name' => $this->l('Login And Register'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Social Login'),
                'name' => 'quicklogin_sociallogin',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
            ),
        );
        
        return $inputs;
    }

    // buid for deotemplate
    // public function processHookCallBack($type, $layout, $enable_sociallogin)
    // {
    //     return $this->_processHook('deotemplate', $type, $layout, $enable_sociallogin);
    // }

    // // render for FO
    // public function _processHook($hookName, $type = '', $layout = '', $enable_sociallogin = '')
    // {
        
    // }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
        $this->helper->tpl_vars['link'] = Context::getContext()->link;
        $this->helper->tpl_vars['exception_list'] = $this->displayModuleExceptionList();
    }

    public function prepareFontContent($assign, $module = null)
    {

        $this->context = Context::getContext();
        if ((int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE')) {
            $array_assign = array();
            if ($this->context->customer->isLogged()) {
                $link = $this->context->link;
                $isLogged = true;
                $assign['formAtts']['customerName'] = $this->context->customer->firstname . ' ' . $this->context->customer->lastname;
                $assign['formAtts']['logout_url'] = $link->getPageLink('index', true, null, 'mylogout');
                $assign['formAtts']['my_account_url'] = $link->getPageLink('my-account', true);
            } else {
                $module = new DeoTemplate();

                // reverse in rtl
                if ($this->context->language->is_rtl && !$module->is_gen_rtl) {
                    if ($assign['formAtts']['quicklogin_type'] == 'slidebar_left') {
                        $assign['formAtts']['quicklogin_type'] = 'slidebar_right';
                    } else if ($assign['formAtts']['quicklogin_type'] == 'slidebar_right') {
                        $assign['formAtts']['quicklogin_type'] = 'slidebar_left';
                    }
                }

                if ($assign['formAtts']['quicklogin_type'] == 'html' || $assign['formAtts']['quicklogin_type'] == 'dropdown' || $assign['formAtts']['quicklogin_type'] == 'dropup') {

                    $assign['formAtts']['html_form'] = $module->buildQuickLoginForm($assign['formAtts']['quicklogin_layout'], $assign['formAtts']['quicklogin_type'], $assign['formAtts']['quicklogin_sociallogin']);
                }
                $isLogged = false;
            }

            $assign['formAtts']['isLogged'] = $isLogged;
        }
       
        return $assign;
    }
}
