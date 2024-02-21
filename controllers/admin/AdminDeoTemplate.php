<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class AdminDeoTemplateController extends ModuleAdminControllerCore
{
    public static $shortcode_lang;
    public static $lang_id;
    public static $language;
    public $error_text = '';
    public $theme_name;

    public function __construct()
    {
        $url = 'index.php?controller=adminmodules&configure=deotemplate&token='.Tools::getAdminTokenLite('AdminModules')
                .'&tab_module=Home&module_name=deotemplate';
        Tools::redirectAdmin($url);
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';
        $this->theme_name = Context::getContext()->shop->theme_name;
        parent::__construct();
    }
}