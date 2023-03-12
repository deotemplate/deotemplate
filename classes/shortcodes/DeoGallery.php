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

class DeoGallery extends DeoShortCodeBase
{
	public $name = 'DeoGallery';
	public $inputs_lang = array('temp_title','temp_sub_title', 'temp_image', 'temp_rate_image', 'temp_image_link', 'temp_link', 'temp_description','temp_tags');
	public $inputs = array('temp_use_image_link', 'temp_active', 'temp_class');


	public function getInfo()
	{
		return array(
			'label' => 'Image Gallery', 
			'position' => 6,
			'desc' => $this->l('Show Image in Gallery with filter'),
			'image' => 'gallery.png',
			'tag' => 'content image',
			'config' => $this->renderDefaultConfig(),
		);
	}

	public function getConfigList()
	{
		$href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=images';
		$ad = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
		$list_slider = '<button type="button" id="btn-add-level2" class="btn btn-default btn-add-level2">
				<i class="icon-plus-sign-alt"></i> '.$this->l('Add slider').'</button><hr/>';
		$list_slider_button = '<div id="frm-level2" class="row-level2 frm-level2">
							<div class="form-group">
								<div class="col-lg-12 ">
									<button type="button" class="btn btn-primary btn-save-level2"
									data-error="'.$this->l('Please enter the title and description').'">'.$this->l('Save').'</button>
									<button type="button" class="btn btn-default btn-reset-level2">'.$this->l('Reset').'</button>
									<button type="button" class="btn btn-default btn-cancel-level2">'.$this->l('Cancel').'</button>
								</div>
							</div>
							<hr/>
						</div>';
		$desc = '<span class="image-select-wrapper" data-path_image="'.DeoHelper::getImgThemeUrl().'">
						<span class="image-wrapper"><img src="#" class="img-thumbnail hide"></span>
						<span class="btn-image">
							<a href="'.$href.'" class="choose-img" data-fancybox-type="iframe">'.$this->l('Select image').'</a> - 
							<a href="javascript:void(0)" class="reset-img">'.$this->l('Remove image').'</a>
						</span>
					</span>';
		$no_image = __PS_BASE_URI__.'modules/deotemplate/views/img/no-image.png';

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

		
		$inputs_temp = array(
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => '<div class="alert alert-info">'.$this->l('Add images for Gallery').'</div>'
			),
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => $list_slider
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Enable'),
				'name' => 'temp_active',
				'values' => DeoSetting::returnYesNo(),
				'default' => '1',
				'form_group_class' => 'row-level2'
			),
			array(
				'type' => 'tags',
				'label' => $this->l('Tags'),
				'name' => 'temp_tags',
				'lang' => true,
				'default' => '',
				'class' => 'no-save-input-tags',
				'desc' => $this->l('Data hidden use to filter').'<br>'.$this->l('To add "tags" click in the field, write something, and then press "Enter". Tag values not use commas ","'),
				'form_group_class' => 'row-level2'
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Use image link'),
				'name' => 'temp_use_image_link',
				'values' => DeoSetting::returnYesNo(),
				'default' => '0',
				'class' => 'temp_use_image_link',
				'form_group_class' => 'row-level2'
			),
			array(
				'type' => 'text',
				'name' => 'temp_class',
				'label' => $this->l('Class Width Column'),
				'default' => '',
				'desc' => $this->l('You can use "col" system of Bootstrap 4 to set width for each image. If you set empty, width column will be replaced with Class Width Column Smallest'),
				'form_group_class' => 'row-level2',
			),
			// array(
			//     'type' => 'switch',
			//     'label' => $this->l('Lazy load'),
			//     'name' => 'temp_lazyload',
			//     'values' => DeoSetting::returnYesNo(),
			//     'default' => '1',
			//     'class' => 'temp_lazyload',
			//     'form_group_class' => 'row-level2'
			// ),
			array(
				'type' => 'text',
				'label' => $this->l('Rate size image'),
				'name' => 'temp_rate_image',
				'default' => '0',
				'suffix' => '%',
				'lang' => true,
				'class' => 'temp_rate_image',
				'form_group_class' => 'row-level2 rate_lazyload_group_temp rate_value_temp',
			),
			array(
				'type' => 'html',
				'default' => '',
				'name' => 'temp_html_calc_rate_image',
				'html_content' => '<a href="javascript:void(0)" class="calc-rate-image" data-widget="'.$this->name.'">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div><div class="virtual-image-link"></div>',
				'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
				'form_group_class' => 'row-level2 rate_lazyload_group_temp group_calc_rate_image_temp',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Image Link'),
				'name' => 'temp_image_link',
				'default' => '',
				'lang' => true,
				'desc' => '<span>Example: https://www.prestashop.com/sites/all/themes/prestashop/images/logo_ps_second.svg</span><span class="preview-image-link"><img src="#" class="img-thumbnail img-preview hide"/><img src="'.$no_image.'" class="img-thumbnail no-image hide"/></span>',
				'form_group_class' => 'row-level2 select_image_link_group_temp',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Image'),
				'name' => 'temp_image',
				'default' => '',
				'lang' => true,
				'class' => 'hide',
				'desc' => $desc,
				'form_group_class' => 'row-level2 image-choose-temp lazyload_carousel',
			),
			array(
				'type' => 'textarea',
				'label' => $this->l('Description'),
				'name' => 'temp_description',
				'cols' => 40,
				'rows' => 10,
				'value' => true,
				'lang' => true,
				'default' => '',
				'autoload_rte' => true,
				'class' => 'item-add-slide ignore-lang',
				'form_group_class' => 'row-level2 description-slide',
			),
			array(
				'type' => 'html',
				'name' => 'default_html',
				'html_content' => $list_slider_button
			),
			array(
				'type' => 'hidden',
				'name' => 'total_slider',
				'default' => ''
			),
		);

		$inputs_content = array(
			array(
				'type' => 'switch',
				'label' => $this->l('Lazy load'),
				'name' => 'lazyload',
				'values' => DeoSetting::returnYesNo(),
				'default' => '0',
				'class' => 'lazyload',
				'desc' => $this->l('Becareful with this option because some case it work not exactly. You should disable it.'),
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Display Tags'),
				'name' => 'display_tags',
				'values' => DeoSetting::returnYesNo(),
				'default' => '0',
			),
			array(
				'type' => 'text',
				'name' => 'class_col_width',
				'label' => $this->l('Class Width Column Smallest'),
				'default' => '',
				'desc' => $this->l('You can use "col" system of Bootstrap 4 to set width for each image. Default with 100% if you not set class col'),
			),
			array(
				'type' => 'tags',
				'label' => $this->l('Tags Filter'),
				'name' => 'tags',
				'lang' => true,
				'default' => '',
				'class' => 'no-save-input-tags',
				'desc' => $this->l('Tags use to filter').'<br>'.$this->l('To add "tags" click in the field, write something, and then press "Enter". Tag values not use commas ","'),
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Show Count'),
				'name' => 'show_count',
				'values' => DeoSetting::returnYesNo(),
				'default' => '0',
				'desc' => $this->l('Display count on filter'),
			),
		);
		

		$inputs = array_merge($inputs_head, $inputs_content, $inputs_temp);

		return $inputs;
	}

	public function addConfigList($values)
	{
		// Get value with keys special
		$config_val = array();
		$total = isset($values['total_slider']) ? $values['total_slider'] : '';
		$arr = explode('|', $total);
		
		$inputs_lang = $this->inputs_lang;
		$inputs = $this->inputs;


		$languages = Language::getLanguages(false);
		foreach ($arr as $i) {
			foreach ($inputs_lang as $config) {
				foreach ($languages as $lang) {
					$config_val[$config][$i][$lang['id_lang']] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config.'_'.$i.'_'.$lang['id_lang'], ''));
				}
			}
			foreach ($inputs as $config) {
				$config_val[$config][$i] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config.'_'.$i, ''));
			}
		}

		Context::getContext()->smarty->assign(array(
			'lang' => $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT')),
			'default_lang' => $lang->id,
			'arr' => $arr,
			'languages' => $languages,
			'config_val' => $config_val,
			'path' => DeoHelper::getImgThemeUrl(),
			'inputs_lang' => $this->inputs_lang,
			'inputs' => $this->inputs,
		));
		
		$list_slider = Context::getContext()->smarty->fetch(DeoHelper::getShortcodeTemplatePath('DeoGallery.tpl'));
		
		$input = array(
			'type' => 'html',
			'name' => 'default_html',
			'html_content' => $list_slider,
		);
		// Append new input type html
		$this->config_list[] = $input;
	}

	public function endRenderForm()
	{
		$this->helper->module = new $this->module_name();
		
		// KEEP OLD DATA
		if (Tools::getIsset('nbitemsperline') && Tools::getValue('nbitemsperline')) {
			$this->helper->tpl_vars['fields_value']['nbitemsperline_desktop'] = Tools::getValue('nbitemsperline');
			$this->helper->tpl_vars['fields_value']['nbitemsperline_smalldesktop'] = Tools::getValue('nbitemsperline');
			$this->helper->tpl_vars['fields_value']['nbitemsperline_tablet'] = Tools::getValue('nbitemsperline');
		}
		
		if (Tools::getIsset('nbitemsperlinetablet') && Tools::getValue('nbitemsperlinetablet')) {
			$this->helper->tpl_vars['fields_value']['nbitemsperline_smalldevices'] = Tools::getValue('nbitemsperlinetablet');
		}
		
		if (Tools::getIsset('nbitemsperlinemobile') && Tools::getValue('nbitemsperlinemobile')) {
			$this->helper->tpl_vars['fields_value']['nbitemsperline_extrasmalldevices'] = Tools::getValue('nbitemsperlinemobile');
			$this->helper->tpl_vars['fields_value']['nbitemsperline_smartphone'] = Tools::getValue('nbitemsperlinemobile');
		}
	}
	
	public function prepareFontContent($assign, $module = null)
	{
		// validate module
		unset($module);

		if (!DeoHelper::getLazyload()) {
			$assign['formAtts']['lazyload'] = 0;
		}
		if (Tools::strpos($assign['formAtts']['class_col_width'], 'col-') === false){
			$assign['formAtts']['class_col_width'] = 'col-sp-12';
		}

		$total_slider = isset($assign['formAtts']['total_slider']) ? $assign['formAtts']['total_slider'] : '';
		$list = explode('|', $total_slider);
		$list_items = array();
		$lang = Language::getLanguage(Context::getContext()->language->id);
		$id_lang = $lang['id_lang'];
		
		$inputs_lang = $this->inputs_lang;
		$inputs = $this->inputs;
					  
		foreach ($list as $number) {
			if ($number && (isset($assign['formAtts']['temp_active_'.$number]) && $assign['formAtts']['temp_active_'.$number] == 1)) {
				$item = array();
				$item['id'] = $number;

				# MULTI-LANG
				foreach ($inputs_lang as $key) {
					$name = $key.'_'.$number.'_'.$id_lang;
					$new_name = str_replace("temp_", "", $key);
					$item[$new_name] = isset($assign['formAtts'][$name]) ? $assign['formAtts'][$name] : '';
				}

				# SINGLE-LANG
				foreach ($inputs as $key) {
					$name = $key.'_'.$number;
					$new_name = str_replace("temp_", "", $key);
					$item[$new_name] = isset($assign['formAtts'][$name]) ? $assign['formAtts'][$name] : '';
				}

				//rate image
				$item['rate_image'] = $item['rate_image'].'%';

				// Image
				if ($item['use_image_link']){
					$item['image'] = $item['image_link'];
				}else if ($item['image']){
					$item['image'] = DeoHelper::getImgThemeUrl().$item['image'];
					unset($item['image_link']);
				}

				$item['tags'] = explode(',', $item['tags']);

				
				if ($item['class']){
					if (Tools::strpos($item['class'], 'col-') === false){
						$item['class'] = $item['class'].' col-sp-12';
					}
				}else{
					$item['class'] = $assign['formAtts']['class_col_width'];
				}

				array_push($list_items, $item);
			}
		}

		$assign['formAtts']['tags'] = explode(',', $assign['formAtts']['tags']);
		$assign['formAtts']['sliders'] = $list_items;

		return $assign;
	}
}
