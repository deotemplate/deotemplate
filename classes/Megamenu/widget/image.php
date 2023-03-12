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

class DeoWidgetImage extends DeoWidgetBaseModel
{
    public $name = 'image';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Image'), 'explain' => $this->l('Create image multiple Language'));
    }

    public function renderForm($args, $data)
    {
        #validate module
        unset($args);

        $path_image = DeoHelper::getImgThemeUrl();
        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
        $no_image = __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png';

        $helper = $this->getFormHelper();

        $desc = '';
        $desc .= '<span class="image-select-wrapper" data-path_image="'.$path_image.'">
                    <span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
                    <span class="btn-image">
                        <a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
                        <a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
                    </span>
                </span>';
        $new_field = array(
            'legend' => array(
                'title' => $this->l('Widget Image.'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use image link'),
                    'name' => 'use_image_link',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '0',
                    'class' => 'use_image_link',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Lazy load'),
                    'name' => 'lazyload',
                    'values' => DeoSetting::returnYesNo(),
                    'default' => '1',
                    'class' => 'lazyload',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Rate size image'),
                    'name' => 'rate_image',
                    'default' => '0',
                    'suffix' => '%',
                    'lang' => true,
                    'class' => 'rate_image',
                    'form_group_class' => 'rate_lazyload_group rate_value',
                ),
                array(
                    'type' => 'html',
                    'default' => '',
                    'name' => 'html_calc_rate_image',
                    'html_content' => '<a href="javascript:void(0)" class="calc-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div><div class="virtual-image-link"></div>',
                    'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
                    'form_group_class' => 'rate_lazyload_group group_calc_rate_image',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image link'),
                    'name' => 'image_link',
                    'default' => '',
                    'lang' => true,
                    'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
                    'form_group_class' => 'select_image_link_group',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image'),
                    'name' => 'image',
                    'default' => '',
                    'lang' => true,
                    'class' => 'hide',
                    'desc' => $desc,
                    'form_group_class' => 'image-choose',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Alt'),
                    'name' => 'alt',
                    'default' => '',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link to'),
                    'name' => 'link',
                    'default' => '',
                    'desc' => $this->l('Example: http://prestashop.com. Leave empty to hidden link.'),
                    'lang' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content HTML'),
                    'name' => 'htmlcontent',
                    'cols' => 40,
                    'rows' => 10,
                    'value' => true,
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => true,
                ),
            )
        );

        $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'],$new_field['input']);
        array_unshift($this->fields_form[0]['form'], $new_field['legend']);

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($data),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => $default_lang
        );

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        #validate module
        unset($args);
        $t = array(
            'html' => '',
        );
        $setting = array_merge($t, $setting);
        $languageID = Context::getContext()->language->id;
        $setting['html'] = isset($setting['htmlcontent_'.$languageID]) ? Tools::stripslashes($setting['htmlcontent_'.$languageID]) : '';
        $setting['link'] = isset($setting['link_'.$languageID]) ? Tools::stripslashes($setting['link_'.$languageID]) : '';
        if (isset($setting['use_image_link']) && $setting['use_image_link']){
            $setting['image'] = isset($setting['image_link_'.$languageID]) ? Tools::stripslashes($setting['image_link_'.$languageID]) : '';
        }else{
            if (isset($setting['image_'.$languageID]) && $setting['image_'.$languageID]){
                $image =  DeoHelper::getImgThemeUrl().Tools::stripslashes($setting['image_'.$languageID]);
                $setting['image'] = $image;
            }else{
                $setting['image'] = '';
            }
        }
        
        if (DeoHelper::getLazyload()){
            $setting['rate_image'] = isset($setting['rate_image_'.$languageID]) ? Tools::stripslashes($setting['rate_image_'.$languageID]) : '';
            if (isset($setting['rate_image']) && $setting['rate_image']){
                $setting['rate_image'] = $setting['rate_image'].'%';
            }
        }else{
            $setting['lazyload'] = 0;
        }

        $output = array('type' => 'image', 'data' => $setting);

        return $output;
    }

    /**
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     */
    public function getConfigKey($multi_lang = 0)
    {
        if ($multi_lang == 0) {
            return array(
                'alt',
                'lazyload',
                'use_image_link',
            );
        } elseif ($multi_lang == 1) {
            return array(
                'image',
                'link',
                'rate_image',
                'image_link',
                'htmlcontent',
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
