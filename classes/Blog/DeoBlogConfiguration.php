<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

class DeoBlogConfiguration
{
    public $image_size;
    public $configurations;
    public $configurations_template = array();

    public function __construct()
    {
        $fields = json_decode(DeoHelper::getConfig('BLOG_DASHBOARD_FIELDS_VALUE'));
        $fields = ($fields) ? $fields : array();
        foreach ($fields as $key => $field) {
            $this->configurations[$key] = Configuration::get($key);
        }

        $image_size_configures = json_decode(DeoHelper::getConfig('BLOG_IMAGE_SIZE'));
        $image_size_configures = ($image_size_configures) ? $image_size_configures : array();
        foreach ($image_size_configures as $key => $image) {
            $this->image_size[$key] = $image;
        }

        $template_configures = json_decode(DeoHelper::getConfig('BLOG_TEMPLATES'));
        $template_configures = ($template_configures) ? $template_configures : array();
        foreach ($template_configures as $key => $template) {
            $this->configurations_template[$key] = $template;
        }
    }

    public function get($name, $value = '')
    {
        $name = DeoHelper::getConfigName($name);
        if (isset($this->configurations[$name])) {
            return $this->configurations[$name];
        }

        return $value;
    }
}