<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class AdminDeoDeveloperConfigureController extends ModuleAdminController
{
	public $submitSaveSetting = false;

    public function __construct()
    {
    	$this->bootstrap = true;
    	$this->name = 'deotemplate';
    	$this->context = Context::getContext();

        parent::__construct();
    }

    public function initContent()
    {
    	if (!$this->viewAccess()) {
            $this->errors[] = $this->l('You do not have permission to view this.');
            return;
        }

        $this->getLanguages();
        $this->initToolbar();
        // $this->initTabModuleList();
        $this->initPageHeaderToolbar();

        $this->content .= $this->renderForm();
        $this->content .= $this->renderKpis();
        $this->content .= $this->renderList();
        $this->content .= $this->renderOptions();

        // if we have to display the required fields form
        if ($this->required_database) {
            $this->content .= $this->displayRequiredFields();
        }

        $this->context->smarty->assign(array(
            'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
            'debug_mode' => (bool)_PS_MODE_DEV_,
            'content' => $this->content,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    public function initPageHeaderToolbar()
    {
    	parent::initPageHeaderToolbar();

        // Add btn save on toolbar
        $this->page_header_toolbar_btn['Save'] = array(
            'href' => 'javascript:void(0);',
            'desc' => $this->l('Save'),
            'js' => 'TopSave()',
            'icon' => 'process-icon-save',
        );
        Media::addJsDef(array('TopSave_Name' => 'submitAddconfiguration'));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/general.js');
    }

    public function postProcess()
    {
        if (count($this->errors) > 0) {
            return;
        }

        if (Tools::isSubmit('submitAddconfiguration')) {
            # SAVING CONFIGURATION
            $posts = Tools::getValue('INPUTS_FIELDS');
            DeoHelper::updateValue('INPUTS_FIELDS', $posts);
            $posts = json_decode($posts);

            foreach ($posts as $key => $post) {
                # validate module
                $value = Tools::getValue($key);
                if ($key == 'INPUTS_FIELDS'){
                    continue;
                }
                DeoHelper::updateValue($key, $value);
            }

            $this->confirmations[] = 'Your configurations have been saved successfully.';
        }
    }

	public function renderForm()
    {
        $this->context->controller->addJs(DeoHelper::getJsAdminDir().'admin/function.js');
        $list_all_hooks = $this->renderListAllHook(DeoSetting::getHook('all'));
        $list_mobile_hooks = (DeoHelper::getConfig('LIST_MOBILE_HOOK')) ?
                DeoHelper::getConfig('LIST_MOBILE_HOOK') : implode(',', DeoSetting::getHook('mobile'));
        $list_header_hooks = (DeoHelper::getConfig('LIST_HEADER_HOOK')) ?
                DeoHelper::getConfig('LIST_HEADER_HOOK') : implode(',', DeoSetting::getHook('header'));
        $list_content_hooks = (DeoHelper::getConfig('LIST_CONTENT_HOOK')) ?
                DeoHelper::getConfig('LIST_CONTENT_HOOK') : implode(',', DeoSetting::getHook('content'));
        $list_footer_hooks = (DeoHelper::getConfig('LIST_FOOTER_HOOK')) ?
                DeoHelper::getConfig('LIST_FOOTER_HOOK') : implode(',', DeoSetting::getHook('footer'));
        $list_product_hooks = (DeoHelper::getConfig('LIST_PRODUCT_HOOK')) ?
                DeoHelper::getConfig('LIST_PRODUCT_HOOK') : implode(',', DeoSetting::getHook('product'));

    	$tabs =  array(
            'tab_library' => $this->l('Library'),
            // 'tab_functions' => $this->l('Functions'),
            'tab_ajax_setting' => $this->l('Ajax Setting'),
            'tab_hook' => $this->l('Hook'),
        );

        $inputs_library = array(
        	array(
                'type' => 'switch',
                'label' => $this->l('Load Jquery Stellar Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_STELLAR'),
                'desc' => $this->l('This script is use for parallax. If you load it in other plugin please turn it off'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Owl Carousel Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_OWL_CAROUSEL'),
                'desc' => $this->l('This script is use for Owl Carousel. If you load it in other plugin please turn it off'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 1,
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Swiper Carousel Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_SWIPER'),
                'desc' => $this->l('This script is use for Swiper Carousel. If you load it in other plugin please turn it off'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Panr Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_PANR'),
                'desc' => $this->l('This script is use for parallax image when hover. If you load it in other plugin please turn it off'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Waypoints Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_WAYPOINTS'),
                'desc' => $this->l('This script is use for Animated. If you load it in other plugin please turn it off'),
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => 0,
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Instafeed Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_INSTAFEED'),
                'desc' => $this->l('This script is use for Instagram. If you load it in other plugin please turn it off'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Video HTML5 Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_HTML5_VIDEO'),
                'desc' => $this->l('This script is use for Video HTML5. If you load it in other plugin please turn it off'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Full Page Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_FULLPAGE'),
                'desc' => $this->l('This script is use for Full Page. If you load it in other plugin please turn it off'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Magic360 Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_IMAGE360'),
                'desc' => $this->l('This script is use for Image 360. If you load it in other plugin please turn it off'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Cookie Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_COOKIE'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Yes : Load library JS jquery.cooki-plugin.js'),
                'form_group_class' => 'tab_library',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Product Zoom Library'),
                'name' => DeoHelper::getConfigName('LOAD_LIBRARY_PRODUCT_ZOOM'),
                'desc' => $this->l('This script is use for Zoom Image product page. If you load it in other plugin please turn it off'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_library',
            ),
        );

        $inputs_functions = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Save Profile Submit'),
                'name' => DeoHelper::getConfigName('SAVE_PROFILE_SUBMIT'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('Yes: Use submit form when save profile and load page again.').'<br>'.$this->l('No: Use save AJAX to submnit when save profile not load page again.'),
                'form_group_class' => 'tab_functions',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Save Profile Multithrearing'),
                'name' => DeoHelper::getConfigName('SAVE_PROFILE_MULTITHREARING'),
                'default' => 0,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $this->l('When save AJAX enable to submit Multithrearing when save profile.'),
                'form_group_class' => 'tab_functions',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Save profile and postion id to cookie'),
                'name' => DeoHelper::getConfigName('SAVE_COOKIE_PROFILE'),
                'default' => 0,
                'desc' => $this->l('That is only for demo, please turn off it in live site'),
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_functions',
            ),
            // array(
            //     'type' => 'text',
            //     'label' => $this->l('Random Product'),
            //     'desc' => $this->l('Number of time create random product when using Prestashop_CACHED and showing product carousel has order by RANDOM'),
            //     'name' => DeoHelper::getConfigName('PRODUCT_MAX_RANDOM'),
            //     'default' => 2,
            //     'form_group_class' => 'tab_functions',
            // ),
        );

        $html_countdown = $this->l('Use this code below').'</br>';
        $html_countdown .= htmlspecialchars('<div class="deo-countdown" data-idproduct="{$product.id_product}"></div>');
        $html_second_image = $this->l('Use this code below').'</br>';
        $html_second_image .= $this->l('- Case 1 get second image of product').'</br>';
        $html_second_image .= htmlspecialchars('<span class="deo-second-img" data-idproduct="{$product.id_product}"></span>').'</br>';
        $html_second_image .= $this->l('- Case 2 get second image of product_attribute (if product not attribute get second image of product)').'</br>';
        $html_second_image .= htmlspecialchars('<span class="product-additional-attribute" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}"></span>');
        $html_more_images = $this->l('Use this code below').'</br>';
        $html_more_images .= htmlspecialchars('<div class="deo-more-product-img" data-idproduct="{$product.id_product}"></div>');
        $html_qty_category = $this->l('Use this code below').'</br>';
        $html_qty_category .= htmlspecialchars('<span data-id="{$node.id}" class="deo-qty-category badge pull-right" data-str="{l s=\' item(s)\' d=\'Shop.Theme.Catalog\'}"></span>');

        $inputs_ajax_setting = array(
        	array(
                'type' => 'switch',
                'label' => $this->l('Show Category Quantity'),
                'name' => DeoHelper::getConfigName('AJAX_CATEGORY_QTY'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_setting',
                'desc' => $html_qty_category,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Second Product Image'),
                'name' => DeoHelper::getConfigName('AJAX_SECOND_PRODUCT_IMAGE'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_setting',
                'desc' => $html_second_image,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Multiple Product Images'),
                'name' => DeoHelper::getConfigName('AJAX_MULTIPLE_PRODUCT_IMAGE'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'form_group_class' => 'tab_ajax_setting',
                'desc' => $html_more_images,
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Count Down Product'),
                'name' => DeoHelper::getConfigName('AJAX_COUNTDOWN'),
                'default' => 1,
                'values' => DeoSetting::returnYesNo(),
                'desc' => $html_countdown,
                'form_group_class' => 'tab_ajax_setting',
            ),
        );

        $inputs_hook = array(
            array(
                'type' => 'text',
                'label' => $this->l('Hooks in mobile'),
                'name' => DeoHelper::getConfigName('LIST_MOBILE_HOOK'),
                'class' => '',
                'default' => $list_mobile_hooks,
                'form_group_class' => 'tab_hook',
            ),
        	array(
                'type' => 'text',
                'label' => $this->l('Hooks in header'),
                'name' => DeoHelper::getConfigName('LIST_HEADER_HOOK'),
                'class' => '',
                'default' => $list_header_hooks,
                'form_group_class' => 'tab_hook',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Hooks in content'),
                'name' => DeoHelper::getConfigName('LIST_CONTENT_HOOK'),
                'class' => '',
                'default' => $list_content_hooks,
                'form_group_class' => 'tab_hook',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Hooks in footer'),
                'name' => DeoHelper::getConfigName('LIST_FOOTER_HOOK'),
                'class' => '',
                'default' => $list_footer_hooks,
                'form_group_class' => 'tab_hook',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Hooks in product-footer'),
                'name' => DeoHelper::getConfigName('LIST_PRODUCT_HOOK'),
                'class' => '',
                'default' => $list_product_hooks,
                'form_group_class' => 'tab_hook',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<input type="hidden" name="hook_header_old" id="hook_header_old"/>
                                    <input type="hidden" name="hook_content_old" id="hook_content_old"/>
                                    <input type="hidden" name="hook_footer_old" id="hook_footer_old"/>
                                    <input type="hidden" name="hook_product_old" id="hook_product_old"/>
                                    <input type="hidden" name="is_change" id="is_change" value=""/>
                                    <input type="hidden" id="message_confirm" value="'.$this->l('The hook is changing. Click OK will save new config hooks and will REMOVE ALL current data widget. Are you sure?').'"/>',
                'form_group_class' => 'tab_hook',
            ),
        );

        $inputs_field = array(
        	array(
                'type' => 'hidden',
                'name' => 'INPUTS_FIELDS',
            ),
		);

        $inputs_header = array(
            array(
                'type' => 'tabConfig',
                'name' => 'title',
                'values' => $tabs,
                'default' => Tools::getValue('tab_open') ? Tools::getValue('tab_open') : 'tab_library',
                'save' => false,
            )
        );

        $inputs = array_merge($inputs_header, $inputs_library, $inputs_ajax_setting, $inputs_hook, $inputs_field);

        $this->fields_form = array(
            'input' => $inputs,
            'submit' => array(
                'class' => 'btn btn-default pull-right '.get_class($this),
                'title' => $this->l('Save'),
            ),
        );
      
        $fields_value = $this->getConfigFieldsValues($this->fields_form);
        $this->fields_value = $fields_value;
        $this->fields_value['INPUTS_FIELDS'] = json_encode($fields_value);

        return parent::renderForm();
    }

    private function renderListAllHook($arr)
    {
        $html = '';
        if ($arr) {
            foreach ($arr as $item) {
                $html .= "<a href='javascript:;'>$item</a>";
            }
        }
        return $html;
    }

    /**
     * Assign value for each input of Data form
     */
    public function getConfigFieldsValues($obj)
    {
        $fields_value = array();
        foreach ($obj['input'] as $input) {
            if ($input['name'] == 'INPUTS_FIELDS' || $input['type'] == 'html') {
                continue;
            }

            if (Configuration::hasKey($input['name'])){
                $fields_value[$input['name']] = DeoHelper::get($input['name']);
            }else{
                if (isset($input['default'])){
                    $fields_value[$input['name']] = $input['default'];
                }
            }
        }

		return $fields_value;
    }
}
