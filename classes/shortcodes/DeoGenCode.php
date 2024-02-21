<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoGenCode extends DeoShortCodeBase
{
    public $name = 'DeoGenCode';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Generate Code',
            'position' => 8,
            'desc' => $this->l('Generate Code for tpl file. This function for web developer'),
            'image' => 'code.png',
            'tag' => 'code',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $inputs = array(
            array(
                'type' => 'hidden',
                'value' => 'abcd',
                'name' => 'id_gencode',
                'default' => uniqid('id_gencode_').'_'.time(),
            ),
           
            array(
                'type' => 'textarea',
                'name' => 'content_html',
                'class' => 'deo_html_raw raw-'.time(),
                'rows' => '10',
                'label' => $this->l('Code'),
                'values' => '',
                'default' => '',
                'desc' => $this->l('Typing code for file tpl.'),
            ),
        );
        return $inputs;
    }

    public function prepareFontContent($assign, $module = null)
    {
        $this->profile_data = $module->getProfileData();
        if ($this->profile_data === NULL){
            $this->profile_data = DeoTemplateProfilesModel::getActiveProfile('index');;
        }
        $this->generateFile($assign, $module);

        $file_name = $assign['formAtts']['id_gencode'].'.tpl';

        $profile_folder = $this->profile_data['profile_key'];
        $file_url = DeoHelper::getConfigDir('theme_profiles').$profile_folder.'/'.$file_name;
        // check file tồn tại
        if (file_exists($file_url)) {
            $assign['formAtts']['tpl_file'] = $file_url;
        } else {
            $title = $assign['formAtts']['title'];
            $assign['formAtts']['error_file'] = '1';
            $assign['formAtts']['error_message'] = "ERROR!!! Generate Code '$title'. Physical file does not exist ".Context::getContext()->shop->theme_name.'/'.$profile_folder.'/'.$file_name;
        }
        return $assign;
    }

    /**
     * Create code file in profile folder
     */
    public function generateFile($assign, $module = null)
    {
        $folder_profiles = DeoHelper::getConfigDir('theme_profiles');
        if (!is_dir($folder_profiles)) {
            @mkdir($folder_profiles, 0755, true);
        }

        $file = $assign['formAtts']['id_gencode'].'.tpl';
        $folder = $folder_profiles.$this->profile_data['profile_key'];
        $value = isset($assign['formAtts']['content_html']) ? $assign['formAtts']['content_html'] : '';

        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }
        
        if ($file_content = DeoHelper::getLicenceTPL().$value){
            DeoSetting::writeFile($folder, $file, $file_content);
        }
    }
}
