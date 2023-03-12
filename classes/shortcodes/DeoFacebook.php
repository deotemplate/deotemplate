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

class DeoFacebook extends DeoShortCodeBase
{
    public $name = 'DeoFacebook';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Facebook',
            'position' => 5,
            'desc' => $this->l('You can config Facebook Like box'),
            'image' => 'facebook.png',
            'tag' => 'social',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
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
                'label' => $this->l('Page URL'),
                'name' => 'page_url',
                'class' => 'deo_facebook',
                'default' => 'https://www.facebook.com/facebook',
                'desc' => $this->l('Example:').' https://www.facebook.com/facebook',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Hide cover'),
                'name' => 'hide_cover',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Hide cover photo in the header'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Facepile'),
                'name' => 'show_facepile',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Show profile photos when friends like this'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Hide cta'),
                'name' => 'hide_cta',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Hide the custom call to action button (if available)'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Small header'),
                'name' => 'small_header',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Use the small header instead'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Adapt container width'),
                'name' => 'adapt_container_width',
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'desc' => $this->l('Try to fit inside the container width'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy'),
                'name' => 'lazy',
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Yes: means use the browser\'s lazy-loading mechanism by setting the loading="lazy" iframe attribute. The effect is that the browser does not render the plugin if it\'s not close to the viewport and might never be seen.'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => 'width',
                'default' => '270',
                'desc' => $this->l('The pixel width of the plugin. Min is 180 & Max is 500'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => 'height',
                'default' => '300',
                'desc' => $this->l('The pixel height of the plugin. Min is 70'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Tabs'),
                'name' => 'tabs',
                'default' => 'timeline',
                'desc' => $this->l('Tabs to render i.e. timeline, events, messages. Use a comma-separated list to add multiple tabs, i.e. timeline, events.'),
            ),
        );
        $inputs = array_merge($inputs_head, $inputs_content);

        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
}
