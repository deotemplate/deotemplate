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

class DeoAccordions extends DeoShortCodeBase
{
    public $name = 'DeoAccordions';

    public function getInfo()
    {
        $arr_config_sub = array();
        $config = $this->renderDefaultConfig();
        if (!Tools::getIsset('subTab')) {
            $languages = Language::getLanguages(false);
            $config_sub = $this->renderDefaultConfig(true, true);
            $config['id'] = 'accordion_'.DeoSetting::getRandomNumber();
            for ($i=1; $i <= 2; $i++) { 
                $config_sub['form_id'] = 'form_'.DeoSetting::getRandomNumber();
                $arr_config_sub[$i] = $config_sub;
                $arr_config_sub[$i]['parent_id'] = $config['id'];
                $arr_config_sub[$i]['active_type'] = $config['active_type'];
                $arr_config_sub[$i]['active_accordion'] = ($i == 1) ? 1 : 0;
                $arr_config_sub[$i]['id'] = 'collapse_'.DeoSetting::getRandomNumber();
                foreach ($languages as $lang) {
                    $arr_config_sub[$i]['title_'.$lang['id_lang']] = 'Accordion '.$i;
                }
            }
        }

        return array(
            'label' => 'Accordions',
            'position' => 5, 
            'desc' => $this->l('You can put widget in accordions'),
            'image' => 'accordions.png',
            'tag' => 'content',
            'config' => $config,
            'config_sub' => $arr_config_sub,
        );
    }

    public function getConfigList($sub_tab = false, $keep_name=false)
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

        if (Tools::getIsset('subTab') || $sub_tab) {
            $inputs_content = array(
                array(
                    'type' => 'text',
                    'name' => 'id',
                    'label' => $this->l('ID Accordion'),
                    'values' => ''
                )
            );
            if (!$keep_name){
                $this->name = 'DeoSubAccordion';
            }
        } else {
            $inputs_content = array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Showing Type'),
                    'name' => 'active_type',
                    'class' => 'form-action',
                    'default' => 'hideall',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'set',
                                'name' => $this->l('Set active'),
                            ),
                            array(
                                'id' => 'showall',
                                'name' => $this->l('Show all'),
                            ),
                            array(
                                'id' => 'hideall',
                                'name' => $this->l('Hide all'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'text',
                    'name' => 'active_accordion',
                    'label' => $this->l('Active Accordion'),
                    'default' => '1',
                    'form_group_class' => 'active_type_sub active_type-set',
                ),
            );
        }

        $inputs = array_merge($inputs_head, $inputs_content);
        
        return $inputs;
    }

    /**
     * overide in tabs module
     */
    public function adminContent($atts, $content = null, $tag_name = null, $is_gen_html = null)
    {
        $this->preparaAdminContent($atts, $tag_name);
        if ($is_gen_html) {
            $assign = array();
            $w_info = $this->getInfo();
            $w_info['name'] = $this->name;
            if ($tag_name == 'DeoAccordion') {
                $assign['isSubTab'] = 1;
                $w_info['name'] = 'DeoAccordion';
            } else {
                preg_match_all('/DeoAccordions form_id="([^\"]+)" class="([^\"]+)" active_type="([^\"]+)"{0,1} active_accordion="([^\"]+)"{0,1} override_folder="([^\"]+)" id="([^\"]+)"{0,1} title="([^\"]+)"{0,1} sub_title=\"([^\"]+){0,1}\"/i', $content, $matches, PREG_OFFSET_CAPTURE);
                if (isset($matches['6']['0']['0'])) {
                    $atts['id'] = $matches['6']['0']['0'];
                    // $atts['title'] = $matches['7']['0']['0'];
                }
            }
            $assign['formAtts'] = $atts;
            $assign['deoInfo'] = $w_info;
            $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
            $controller = new AdminDeoShortcodesController();
            return $controller->adminContent($assign, $this->name.'.tpl');
        } else {
            DeoShortCodesBuilder::doShortcode($content);
        }
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
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
       
        
        foreach ($atts as $key => $val) {
            if (Tools::strpos($key, 'content') !== false || Tools::strpos($key, 'link') !== false || Tools::strpos($key, 'url') !== false || Tools::strpos($key, 'alt') !== false || Tools::strpos($key, 'tit') !== false || Tools::strpos($key, 'name') !== false || Tools::strpos($key, 'desc') !== false || Tools::strpos($key, 'itemscustom') !== false) {
                $atts[$key] = str_replace($this->str_search, $this->str_relace_html, $val);
            }
        }
        // validate module
        unset($is_gen_html);
        $assign = $w_info = array();
        $w_info['name'] = $this->name;
        if ($tag_name == 'DeoAccordion') {
            $assign['isSubTab'] = 1;
            $w_info['name'] = 'DeoAccordion';
            $assign['isWrapper'] = 0;
        } else {
			// check correct wrapper DeoAccordion
			$assign['isWrapper'] = 1;
            preg_match_all('/DeoAccordions form_id="([^\"]+)" class="([^\"]+)" active_type="([^\"]+)"{0,1} active_accordion="([^\"]+)"{0,1} override_folder="([^\"]+)" id="([^\"]+)"{0,1} title="([^\"]+)"{0,1} sub_title=\"([^\"]+){0,1}\"/i', $content, $matches, PREG_OFFSET_CAPTURE);
            if (isset($matches['6']['0']['0'])) {
                $atts['id'] = $matches['6']['0']['0'];
                // $atts['title'] = $matches['7']['0']['0'];
            }
            
            if (!isset($atts['active_accordion'])) {
                $active_tab = 0;
                // validate module
                unset($active_tab);
            } else {
                # SET ACTIVE
                if (isset($atts['title']) && !empty($atts['title'])) {
                    $atts['active_accordion'] = $atts['active_accordion'];
                }
            }
        }
        $content = DeoShortCodesBuilder::doShortcode($content);
        $atts['class'] = ((isset($atts['class']) && $atts['class']) ? $atts['class'].' ' : '');
        $atts['class'] .= ' '.$this->name;
        $assign['deo_html_content'] = $content;
        $assign['formAtts'] = $atts;
        $module = DeoTemplate::getInstance();
        return $module->fontContent($assign, $this->name.'.tpl');
    }
}
