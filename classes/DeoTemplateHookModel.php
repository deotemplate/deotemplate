<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoTemplateProfilesModel.php');

class DeoTemplateHookModel
{
    public $profile_data;
    public $profile_param;
    public $hook;

    public function create()
    {
        $this->profile_data = DeoTemplateProfilesModel::getActiveProfile('index');
        $this->profile_param = json_decode($this->profile_data['params'], true);
        $this->fullwidth_index_hook = $this->fullwidthIndexHook();
        $this->fullwidth_other_hook = $this->fullwidthOtherHook();
        return $this;
    }

    public function fullwidthIndexHook()
    {
        return isset($this->profile_param['fullwidth_index_hook']) ? $this->profile_param['fullwidth_index_hook'] : DeoSetting::getIndexHook(3);
    }

    public function fullwidthOtherHook()
    {
        return isset($this->profile_param['fullwidth_other_hook']) ? $this->profile_param['fullwidth_other_hook'] : DeoSetting::getOtherHook(3);
    }

    public function fullwidthHook($hook_name, $page)
    {
        if ($page == 'index') {
            // validate module
            return isset($this->fullwidth_index_hook[$hook_name]) ? $this->fullwidth_index_hook[$hook_name] : 0;
        } else {
            # other page
            return isset($this->fullwidth_other_hook[$hook_name]) ? $this->fullwidth_other_hook[$hook_name] : 0;
        }
    }
}
