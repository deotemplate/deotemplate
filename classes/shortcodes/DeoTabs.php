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

class DeoTabs extends DeoShortCodeBase
{
    public $name = 'DeoTabs';
    
    public function getInfo()
    {
        $arr_config_sub = array();
        $config = $this->renderDefaultConfig();
        if (!Tools::getIsset('subTab')) {
            $languages = Language::getLanguages(false);
            $config_sub = $this->renderDefaultConfig(true, true);
            for ($i=1; $i <= 3; $i++) { 
                $config_sub['form_id'] = 'form_'.DeoSetting::getRandomNumber();
                $arr_config_sub[$i] = $config_sub;
                $arr_config_sub[$i]['id'] = 'tab_'.DeoSetting::getRandomNumber();
                $arr_config_sub[$i]['active_tab'] = ($i == 1) ? 1 : 0;
                foreach ($languages as $lang) {
                    $arr_config_sub[$i]['title_'.$lang['id_lang']] = 'Tab '.$i;
                }
            }
        }

        return array(
            'label' => 'Tabs', 
            'position' => 4,
            'desc' => $this->l('You can put widget in tab'),
            'image' => 'tab.png',
            'tag' => 'content',
            'config' => $config,
            'config_sub' => $arr_config_sub,
        );
    }

    public function getConfigList($sub_tab = false, $keep_name=false)
    {
        Context::getContext()->smarty->assign('path_image', DeoHelper::getImgThemeUrl());
        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
        if (Tools::getIsset('subTab') || $sub_tab) {
            $input = array(
                array(
                    'type' => 'text',
                    'name' => 'title',
                    'label' => $this->l('Title'),
                    'lang' => 'true',
                    'values' => '',
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'sub_title',
                    'label' => $this->l('Sub Title'),
                    'lang' => true,
                    'values' => '',
                    'autoload_rte' => false,
                    'default' => ''
                ),
                array(
                    'type' => 'text',
                    'name' => 'id',
                    'label' => $this->l('ID Tab'),
                    'values' => '',
                ),
                array(
                    'type' => 'DeoClass',
                    'name' => 'css_class',
                    'label' => $this->l('CSS Class'),
                    'values' => '',
                ),
                array(
                    'label' => $this->l('Image'),
                    'type' => 'selectImg',
                    'href' => $href,
                    'name' => 'image',
                    'lang' => false,
                    'show_image' => true,
                    'form_group_class' => 'select_image no_lang'
                )
            );
            if (!$keep_name){
                $this->name = 'DeoSubTabs';
            }
        } else {
            $input = array(
                array(
                    'type' => 'text',
                    'name' => 'title',
                    'label' => $this->l('Title'),
                    'lang' => 'true',
                    'default' => '',
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'sub_title',
                    'label' => $this->l('Sub Title'),
                    'lang' => true,
                    'values' => '',
                    'autoload_rte' => false,
                    'default' => ''
                ),
                array(
                    'type' => 'DeoClass',
                    'name' => 'class',
                    'label' => $this->l('CSS Class'),
                    'default' => '',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Type'),
                    'name' => 'tab_type',
                    'default' => 'tabs-top',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'tabs-top',
                                'name' => $this->l('Tabs Top'),
                            ),
                            array(
                                'id' => 'tabs-below',
                                'name' => $this->l('Tabs below'),
                            ),
                            array(
                                'id' => 'tabs-left',
                                'name' => $this->l('Tabs Left'),
                            ),
                            array(
                                'id' => 'tabs-right',
                                'name' => $this->l('Tabs Right'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'text',
                    'name' => 'active_tab',
                    'label' => $this->l('Active Tab'),
                    'default' => '1',
                    'desc' => $this->l('Input position(number) to show tab. If Blank, all tab default is inactive.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use Fade effect'),
                    'name' => 'fade_effect',
                    'is_bool' => true,
                    'default' => '1',
                    'desc' => $this->l('To make tabs fade in.'),
                    'values' => DeoSetting::returnYesNo(),
                )
            );
        }
        return $input;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
    
    /**
     * Overide in tabs module
     * @param type $atts
     * @param type $content
     * @param type $tag_name
     * @param type $is_gen_html
     * @return type
     */
    public function adminContent($atts, $content = null, $tag_name = null, $is_gen_html = null)
    {
        $this->preparaAdminContent($atts, $tag_name);
        if ($is_gen_html) {
            $assign = array();
            $assign['formAtts'] = $atts;
            $w_info = $this->getInfo();
            $w_info['name'] = $this->name;
            $assign['deoInfo'] = $w_info;
            if ($tag_name == 'DeoTab') {
                $assign['tabID'] = $atts['id'];
                $assign['isSubTab'] = 1;
                $w_info['name'] = 'DeoTab';
            } else {
                // preg_match_all('/DeoTab form_id="([^\"]+)" id\=\"([^\"]+)\" css_class\=\"([^\"]+){0,1}\" title\=\"([^\"]+)\"{0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE);
                $sub_tab_content = array();
                // $len = count($matches[0]);
                // for ($i = 0; $i < $len; $i++) {
                //     $title = $matches[4][$i][0];
                //     $title = str_replace($this->str_search, $this->str_relace_html, $title);
                //     $form_id = $matches[1][$i][0];
                //     $sub_tab_content[$form_id] = array(
                //         'form_id' => $form_id,
                //         'id' => $matches[2][$i][0],
                //         'css_class' => $matches[3][$i][0],
                //         'title' => $title,
                //     );
                // }
                // validate module
                // $pattern = '/DeoTab form_id="([^\"]+)" id\=\"([^\"]+)\" css_class\=\"([^\"]+){0,1}\" ';
                // $pattern .= 'override_folder\=\"([^\"]+){0,1}\" title\=\"([^\"]+)\"{0,1}/i';
                // preg_match_all($pattern, $content, $matches2, PREG_OFFSET_CAPTURE);
                $sub_tab_content2 = array();
                // $len2 = count($matches2[0]);
                // for ($i = 0; $i < $len2; $i++) {
                //     $title2 = $matches2[5][$i][0];
                //     $title2 = str_replace($this->str_search, $this->str_relace_html, $title2);
                //     $form_id2 = $matches2[1][$i][0];
                //     $sub_tab_content2[$form_id2] = array(
                //         'form_id' => $form_id2,
                //         'id' => $matches2[2][$i][0],
                //         'css_class' => $matches2[3][$i][0],
                //         'title' => $title2,
                //     );
                // }
                
                $pattern = '/DeoTab form_id="([^\"]+)" id=\"([^\"]+)\" css_class=\"([^\"]+){0,1}\" image=\"([^\"]+){0,1}\" override_folder=\"([^\"]+){0,1}\" title=\"([^\"]+){0,1}\" sub_title=\"([^\"]+){0,1}\"/i';
                preg_match_all($pattern, $content, $matches3, PREG_OFFSET_CAPTURE);
                $sub_tab_content3 = array();
                $len3 = count($matches3[0]);
                for ($i = 0; $i < $len3; $i++) {
                    $title3 = isset($matches3[6][$i][0]) ? $matches3[6][$i][0] : '';
                    $title3 = str_replace($this->str_search, $this->str_relace_html, $title3);
                    $sub_title = isset($matches3[7][$i][0]) ? $matches3[7][$i][0] : '';
                    $sub_title = str_replace($this->str_search, $this->str_relace_html, $sub_title);

                    $form_id3 = $matches3[1][$i][0];
                    $sub_tab_content3[$form_id3] = array(
                        'form_id' => $form_id3,
                        'id' => $matches3[2][$i][0],
                        'css_class' => $matches3[3][$i][0],
                        'title' => $title3,
                        'sub_title' => $sub_title,
                    );
                }

                $pattern = '/DeoTab form_id="([^\"]+)" id=\"([^\"]+)\" css_class\=\"([^\"]+){0,1}\" image=\"([^\"]+){0,1}\" override_folder=\"([^\"]+){0,1}\" active_tab=\"([^\"]+){0,1}\" title=\"([^\"]+){0,1}\" sub_title=\"([^\"]+){0,1}\"/i';
                preg_match_all($pattern, $content, $matches4, PREG_OFFSET_CAPTURE);
                $sub_tab_content4 = array();
                $len4 = count($matches4[0]);
                for ($i = 0; $i < $len4; $i++) {
                    $title4 = isset($matches4[7][$i][0]) ? $matches4[7][$i][0] : '';
                    $title4 = str_replace($this->str_search, $this->str_relace_html, $title4);
                    $sub_title = isset($matches4[8][$i][0]) ? $matches4[8][$i][0] : '';
                    $sub_title = str_replace($this->str_search, $this->str_relace_html, $sub_title);

                    $form_id4 = $matches4[1][$i][0];
                    $sub_tab_content4[$form_id4] = array(
                        'form_id' => $form_id4,
                        'id' => $matches4[2][$i][0],
                        'css_class' => $matches4[3][$i][0],
                        'title' => $title4,
                        'sub_title' => $sub_title,
                        'active_tab' => isset($matches4[7][$i][0]) ? $matches4[7][$i][0] : '',
                    );
                }

                $assign['subTabContent'] = array_merge($sub_tab_content, $sub_tab_content2, $sub_tab_content3, $sub_tab_content4);
            }
            $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
            $controller = new AdminDeoShortcodesController();
            return $controller->adminContent($assign, $this->name.'.tpl');
        } else {
            DeoShortCodesBuilder::doShortcode($content);
        }
    }

    /**
     * Overide in tabs module
     * @param type $atts
     * @param type $content
     * @param type $tag_name
     * @param type $is_gen_html
     * @return type
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
        $assign = array();

        if ($tag_name == 'DeoTabs') {
            if (isset($atts['active_tab']) && $atts['active_tab'] != '') {
                $tab_count = substr_count($content, '[DeoTab');
                $tab_active = (int)$atts['active_tab'];
                
                if (($tab_active <= $tab_count) && ($tab_active >= 1)) {
                    # ACTIVE TAB
                    $atts['active_tab'] = $tab_active - 1;
                } elseif ($tab_active > $tab_count) {
                    # ACTIVE LAST TAB
                    $atts['active_tab'] = $tab_count - 1;
                } else {
                    # ACTIVE FIRST TAB
                    $atts['active_tab'] = 0;
                }
            } else {
                # BLANK
                $atts['active_tab'] = -1;
            }

            // DeoTabs
            $assign['tab_name'] = 'DeoTabs';
            // preg_match_all('/DeoTab form_id="([^\"]+)" id\=\"([^\"]+)\" css_class\=\"([^\"]+){0,1}\" image\=\"([^\"]+)\" title\=\"([^\"]+)\"{0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE);
            $sub_tab_content = array();
            // $len = count($matches[0]);
            // for ($i = 0; $i < $len; $i++) {
            //     $title = $matches[4][$i][0];
            //     $title = str_replace($this->str_search, $this->str_relace_html, $title);
            //     $sub_tab_content[] = array(
            //         'form_id' => $matches[1][$i][0],
            //         'id' => $matches[2][$i][0],
            //         'css_class' => $matches[3][$i][0],
            //         'title' => $title,
            //     );

            //     if ($atts['active_tab'] == $i){
            //         $atts['id_active_tab'] = $matches3[2][$i][0];
            //     }
            // }
            // $pattern = '/DeoTab form_id="([^\"]+)" id\=\"([^\"]+)\" css_class\=\"([^\"]+){0,1}\" override_folder\=\"([^\"]+){0,1}\" title\=\"([^\"]+)\"{0,1}/i';
            // preg_match_all($pattern, $content, $matches2, PREG_OFFSET_CAPTURE);
            $sub_tab_content2 = array();
            // $len2 = count($matches2[0]);
            // for ($i = 0; $i < $len2; $i++) {
            //     $title2 = $matches2[5][$i][0];
            //     $title2 = str_replace($this->str_search, $this->str_relace_html, $title2);
            //     $form_id2 = $matches2[1][$i][0];
            //     $sub_tab_content2[$form_id2] = array(
            //         'form_id' => $form_id2,
            //         'id' => $matches2[2][$i][0],
            //         'css_class' => $matches2[3][$i][0],
            //         'title' => $title2,
            //     );

            //     if ($atts['active_tab'] == $i){
            //         $atts['id_active_tab'] = $matches3[2][$i][0];
            //     }
            // }
            
            $pattern = '/DeoTab form_id="([^\"]+)" id=\"([^\"]+)\" css_class=\"([^\"]+){0,1}\" image=\"([^\"]+){0,1}\" override_folder=\"([^\"]+){0,1}\" title=\"([^\"]+){0,1}\" sub_title=\"([^\"]+){0,1}\"/i';
            preg_match_all($pattern, $content, $matches3, PREG_OFFSET_CAPTURE);
            $sub_tab_content3 = array();
            $len3 = count($matches3[0]);
            for ($i = 0; $i < $len3; $i++) {
                $title3 = isset($matches3[6][$i][0]) ? $matches3[6][$i][0] : '';
                $title3 = str_replace($this->str_search, $this->str_relace_html, $title3);
                $sub_title = isset($matches3[7][$i][0]) ? $matches3[7][$i][0] : '';
                $sub_title = str_replace($this->str_search, $this->str_relace_html, $sub_title);

                $form_id3 = $matches3[1][$i][0];
                $sub_tab_content3[$form_id3] = array(
                    'form_id' => $form_id3,
                    'id' => $matches3[2][$i][0],
                    'css_class' => $matches3[3][$i][0],
                    'title' => $title3,
                    'sub_title' => $sub_title,
                );
            }

            $pattern = '/DeoTab form_id="([^\"]+)" id=\"([^\"]+)\" css_class\=\"([^\"]+){0,1}\" image=\"([^\"]+){0,1}\" override_folder=\"([^\"]+){0,1}\" active_tab=\"([^\"]+){0,1}\" title=\"([^\"]+){0,1}\" sub_title=\"([^\"]+){0,1}\"/i';
            preg_match_all($pattern, $content, $matches4, PREG_OFFSET_CAPTURE);
            $sub_tab_content4 = array();
            $len4 = count($matches4[0]);
            for ($i = 0; $i < $len4; $i++) {
                $title4 = isset($matches4[7][$i][0]) ? $matches4[7][$i][0] : '';
                $title4 = str_replace($this->str_search, $this->str_relace_html, $title4);
                $sub_title = isset($matches4[8][$i][0]) ? $matches4[8][$i][0] : '';
                $sub_title = str_replace($this->str_search, $this->str_relace_html, $sub_title);

                $form_id4 = $matches4[1][$i][0];
                $sub_tab_content4[$form_id4] = array(
                    'form_id' => $form_id4,
                    'id' => $matches4[2][$i][0],
                    'css_class' => $matches4[3][$i][0],
                    'title' => $title4,
                    'sub_title' => $sub_title,
                    'active_tab' => isset($matches4[6][$i][0]) ? $matches4[6][$i][0] : '',
                );
            }

            $assign['subTabContent'] = array_merge($sub_tab_content, $sub_tab_content2, $sub_tab_content3, $sub_tab_content4);
            $atts['id'] = 'tab_'.DeoSetting::getRandomNumber();
            $atts['class'] = ((isset($atts['class']) && $atts['class']) ? $atts['class'].' ' : '').(isset($atts['tab_type']) ? $atts['tab_type'] : '');
            $atts['class'] .= ' '.$this->name;
            
            $assign['formAtts'] = $atts;
            $assign['path'] = DeoHelper::getImgThemeUrl();
            $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
            $module = DeoTemplate::getInstance();
            return $module->fontContent($assign, $this->name.'.tpl');
        } else {
            // DeoTab
            $assign['tabID'] = $atts['id'];
            $assign['tab_name'] = 'DeoTab';
            $assign['isSubTab'] = 1;
            $assign['formAtts'] = $atts;
            $assign['path'] = DeoHelper::getImgThemeUrl();
            $assign['deo_html_content'] = DeoShortCodesBuilder::doShortcode($content);
            $module = DeoTemplate::getInstance();
            return $module->fontContent($assign, $this->name.'.tpl');
        }
    }

    /**
     * @Override
     * Fixed css_class is empty -> cant set to $deoHomeBuilder.process (json) in javascript
     */
    public function preparaAdminContent($atts, $tag_name = null)
    {
        if ($tag_name == null) {
            $tag_name = $this->name;
        }
        if (is_array($atts)) {
            if (!isset(DeoShortCodesBuilder::$shortcode_lang[$tag_name])) {
                $inputs = $this->getConfigList();
                $lang_field = array();
                foreach ($inputs as $input) {
                    if (isset($input['lang']) && $input['lang']) {
                        $lang_field[] = $input['name'];
                    }
                }
                DeoShortCodesBuilder::$shortcode_lang[$tag_name] = $lang_field;
            } else {
                $lang_field = DeoShortCodesBuilder::$shortcode_lang[$tag_name];
            }
            foreach ($atts as $key => $val) {
                if ($lang_field && in_array($key, $lang_field)) {
                    $key .= '_'.DeoShortCodesBuilder::$lang_id;
                }
                if (!isset(DeoShortCodesBuilder::$data_form[$atts['form_id']][$key])) {
                    DeoShortCodesBuilder::$data_form[$atts['form_id']][$key] = $val;
                }
            }
        }
    }
}
