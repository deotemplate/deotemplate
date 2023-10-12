<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */


class DeoWidgetCategory extends DeoWidgetBaseModel
{
	public $widget_name = 'category';
	public $for_module = 'all';
	public $level = 0;
	protected $deo_count = 0;
	protected $id_deo_count = array();
	protected $categories = array();
	protected $id_categories = array();

	public function getWidgetInfo()
	{
		return array('label' => $this->l('Category'), 'explain' => 'Show categories');
	}

	public function renderForm($args, $data)
	{
		# validate module
		unset($args);
		$helper = $this->getFormHelper();
		$root = Category::getRootCategory();
        $selected_cat = array();
        $selected_cates = '';
        $selected_images = '';
        // $image_list = $this->getImages();
        $categorybox = array();
        $image_list = array();
        if ($data && isset($data['params'])) {
	        if (isset($data['params']['categorybox']) && $data['params']['categorybox'] != '') {
                $categorybox = $data['params']['categorybox'];
            }
            if (isset($data['params']['category_img']) && $data['params']['category_img'] != '') {
                $selected_images = $data['params']['category_img'];
            }
            if (isset($data['params']['selected_cates']) && $data['params']['selected_cates'] != '') {
                $selected_cates = $data['params']['selected_cates'];
            }
        }
        $tree = new HelperTreeCategories('categorybox', 'All Categories');
        // fix tree category with ps version 1.6.1 or newer
        if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
            $tree->setRootCategory($root->id)->setUseCheckBox(true)->setFullTree(true)->setSelectedCategories($categorybox)->setInputName('categorybox');
        } else {
            $tree->setRootCategory($root->id)->setUseCheckBox(true)->setSelectedCategories($categorybox)->setInputName('categorybox');
        }

		$path_image = DeoHelper::getImgThemeUrl();
        Context::getContext()->smarty->assign('path_image', $path_image);
        
        $href = Context::getContext()->link->getAdminLink('AdminDeoImages').'&ajax=1&action=manageimage&imgDir=icon';

        $tree_html = $tree->render();
		// $orderby = array(
		// 	array(
		// 		'order' => 'position',
		// 		'name' => $this->l('Position')
		// 	),
		// 	array(
		// 		'order' => 'depth',
		// 		'name' => $this->l('Depth')
		// 	),
		// 	array(
		// 		'order' => 'name',
		// 		'name' => $this->l('Name')
		// 	)
		// );

		$new_field = array(
			'legend' => array(
				'title' => $this->l('Widget Category.'),
			),
			'input' => array(
				array(
					'type' => 'switch',
					'label' => $this->l('Lazy load'),
					'name' => 'lazyload',
					'values' => DeoSetting::returnYesNo(),
					'default' => '1',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Rate size image'),
					'name' => 'rate_image',
					'default' => '',
					'suffix' => '%',
					'class' => 'rate-image',
					'form_group_class' => 'rate_lazyload rate_value',
				),
				array(
					'type' => 'html',
					'name' => 'calculate_rate_image',
					'html_content' => '<a href="javascript:void(0)" class="calculate-rate-image" data-widget="DeoCategoryMenu">'.$this->l('Calculate rate image when use lazy load').'</a><div class="virtual-image"></div>',
					'desc' => $this->l('Rate size image = (width/height)*100. Unit must be %'),
					'form_group_class' => 'rate_lazyload btn_calculate_rate_image ',
				),
				array(
					'type' => 'img_cat_menu',
					'name' => 'img_cat',
					'imageList' => $image_list,
					'selected_images' => $selected_images,
					'selected_cates' => $selected_cates,
					'tree' => $tree_html,
					'href_image' => $href,
					'path_image' => $path_image,
					'default' => '',
					'desc' => $this->l('Note: Choose only one category and depth > 2 to show childrens of category and button show more will show link to parent of category.'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Depth'),
					'name' => 'cate_depth',
					'desc' => $this->l('Level depth from category slected').'<br>'.$this->l('Depth have to > 0'),
					'default' => '1',
				),
				// array(
				// 	'type' => 'select',
				// 	'label' => $this->l('Order By:'),
				// 	'name' => 'orderby',
				// 	'default' => 'position',
				// 	'options' => array(
				// 		'query' => $orderby,
				// 		'id' => 'order',
				// 		'name' => 'name'
				// 	)
				// ),
				array(
					'type' => 'switch',
					'label' => $this->l('Disable HTML tree structure'),
					'name' => 'disable_html_tree_structure',
					'values' => DeoSetting::returnYesNo(),
					'default' => '0',
					'label' => $this->l('Destroy tree structure, categories have same level'),
					'form_group_class' => 'group_normal-image-category',
				), 
				array(
					'type' => 'text',
					'label' => $this->l('Limit'),
					'name' => 'limit',
					'default' => '5',
				),
				array(
	                'type' => 'switch',
	                'label' => $this->l('Quantity'),
	                'name' => 'quantity',
	                'values' => DeoSetting::returnYesNo(),
	                'default' => '0',
	                'desc' => $this->l('Show quantity number product in category'),
	            ),
	            array(
	                'type' => 'switch',
	                'label' => $this->l('Description'),
	                'name' => 'description',
	                'values' => DeoSetting::returnYesNo(),
	                'default' => '0',
	                'desc' => $this->l('Show description category'),
	            ),
	            array(
	                'type' => 'switch',
	                'label' => $this->l('Show Button View All'),
	                'name' => 'viewall',
	                'values' => DeoSetting::returnYesNo(),
	                'default' => '0',
	                'desc' => $this->l('Show button View All if total number item > limit').'<br>'.$this->l('If depth > 1 and choose only one category => button view all will show link to parent category'),
	                'form_group_class' => 'show_link_viewall group_viewall group_normal-image-category',
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Text Button View All'),
	                'name' => 'text_link_viewall',
	                'lang' => true,
	                'default' => '',
	                'desc' => $this->l('Leave empty will be show default text'),
	                'form_group_class' => 'group_show_link_viewall group_viewall',
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Link Button View All'),
	                'name' => 'link_viewall',
	                'lang' => true,
	                'default' => '',
	                'desc' => $this->l('Leave empty to enable load more categories at current page (not link to other page).'),
	                'form_group_class' => 'group_show_link_viewall group_viewall',
	            ),
				array(
					'type' => 'hidden',
					'name' => 'id_root',
					'default' => '2',
				),
				array(
					'type' => 'hidden',
					'name' => 'id_lang',
					'default' => '1',
				),
			)
		);

		$this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'],$new_field['input']);
		array_unshift($this->fields_form[0]['form'], $new_field['legend']);

		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$data_form = $this->getConfigFieldsValues($data);
		$data_form['id_root'] = $root->id;
		$data_form['id_lang'] = Context::getContext()->employee->id_lang;
		$helper->tpl_vars = array(
			'fields_value' => $data_form,
			'languages' => Context::getContext()->controller->getLanguages(),
			'id_language' => $default_lang,
		);

		return $helper->generateForm($this->fields_form);
	}


	public function renderContent($args, $setting)
	{
		// validate module
		unset($args);
		if (!DeoHelper::getLazyload()) {
			$setting['lazyload'] = 0;
		}

		$images = array();
		if (isset($setting['category_img']) && $setting['category_img']) {
			$selected_images = $setting['category_img'];
			$images = json_decode($selected_images, true);
		}
		$rate_images = null;
		if (isset($setting['rate_image']) && $setting['rate_image']) {
			$rate_images = $setting['rate_image'];
			$rate_images = json_decode($rate_images, true);
		}

		$sql_filter = '';
		// $sql_sort = '';
		// if (isset($setting['orderby']) && $setting['orderby']) {
		// 	if ($setting['orderby'] == 'depth') {
		// 		$sql_sort = ' ORDER BY c.`level_depth` ASC';
		// 	}
		// 	if ($setting['orderby'] == 'position') {
		// 		$sql_sort = ' ORDER BY c.`level_depth` ASC, category_shop.`position` ASC';
		// 	}
		// 	if ($setting['orderby'] == 'name') {
		// 		$sql_sort = ' ORDER BY c.`level_depth` ASC, cl.`name` ASC';
		// 	}
		// }
		$catids = (isset($setting['categorybox']) && $setting['categorybox']) ? ($setting['categorybox']) : array();
		$result = array();
		$limit = (isset($setting['limit']) && $setting['limit']) ? ((int)$setting['limit']) : 0;
		$limit++;
		
		$carousel_type = false;
		$this->level = (int) $setting['cate_depth'];

		foreach ($catids as $cate_id) {
			if ($cate_id && Validate::isInt($cate_id)) {
				$result_cate = $this->getNestedCategories($cate_id, 1, $images, $limit, $this->deo_count, $carousel_type, $rate_images);
				if ($result_cate) {
					foreach ($result_cate as $item => &$cate) {
						// if(isset($cate['id_category']) && !empty($rate_images)){
						//     $result_cate[$item]['rate_image'] = $rate_images[$cate['id_category']].'%';
						// }
						if (!in_array($cate['id_category'], $this->id_categories)){
							$this->id_categories[] = $cate['id_category'];
							if ($carousel_type){
								$this->categories[] = $cate;
							}else{
								$this->categories[] = array($cate['id_category'] => $cate);
							}
						}
					}
					$result[] = $result_cate;
				}
			}
		}

		$setting['link'] = Context::getContext()->link;
		$setting['categories'] = (isset($setting['disable_html_tree_structure']) && $setting['disable_html_tree_structure']) ? array_reverse($this->categories) : $result;
		$setting['total'] = $this->deo_count;
		$this->deo_count = 0;
		$this->id_deo_count = array();
		$this->categories = array();
		$this->id_categories = array();

		// validate module
		unset($sql_filter);
		// unset($sql_sort);

		$output = array('type' => 'category', 'data' => $setting);
		return $output;
	}

	public function getImages($image_folder)
	{
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
		$url = Tools::htmlentitiesutf8($protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);

		$path = _PS_ROOT_DIR_.'/'.$image_folder.'/';
		$path = str_replace('//', '/', $path);
		$oimages = array();
		if (is_dir($path)) {
			$images = glob($path.'*.*');
			$exts = array('jpg', 'gif', 'png');
			foreach ($images as $key => $image) {
				# validate module
				unset($key);
				$ext = Tools::substr($image, Tools::strlen($image) - 3, Tools::strlen($image));
				if (in_array(Tools::strtolower($ext), $exts)) {
					$i = str_replace('\\', '/', $image_folder.'/'.basename($image));
					$i = str_replace('//', '/', $i);
					$aimage = array();
					$aimage['path'] = $url.$i;
					$aimage['name'] = basename($image);
					$oimages[] = $aimage;
				}
			}
		}
		return $oimages;
	}


	public function getNestedCategories($parent, $level, $images, $limit, $count, $carousel_type, $rate_images = null)
	{
		$buff = array();
		if ($level <= $this->level) {
			$lang = Context::getContext()->language->id;
			$current = array();
			$child = array();
			//$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
			$image_path = DeoHelper::getImgThemeUrl();

			$sql = 'SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c'.Shop::addSqlAssociation('category', 'c').'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
				WHERE c.id_parent='.(int)$parent.' AND `id_lang` = '.(int)$lang.'
				AND c.`active` = 1
				ORDER BY c.`level_depth` ASC, category_shop.`position` ASC';
			$result = Db::getInstance()->executeS($sql);
			$current_category = Db::getInstance()->executeS('SELECT c.*, cl.*
								FROM `'._DB_PREFIX_.'category` c'.Shop::addSqlAssociation('category', 'c').'
										LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
										WHERE c.id_category='.(int)$parent.' AND `id_lang` = '.(int)$lang.'
										AND c.`active` = 1');
			if ($current_category) {
				if (array_key_exists($current_category[0]['id_category'], $images)) {
					$current_category[0]['image'] = $image_path.$images[$current_category[0]['id_category']];
				}
				if(isset($rate_images[$current_category[0]['id_category']])){
					$current_category[0]['rate_image'] = $rate_images[$current_category[0]['id_category']].'%';
				}
				if (!in_array($parent, $this->id_deo_count)){
					$current_category[0]['deo_count'] = $this->deo_count;
					$this->id_deo_count[] = $parent;
					$this->deo_count = $this->deo_count + 1;
				}
				if ($result) {
					foreach ($result as &$row) {
						if ($row && isset($row['id_category'])) {
							$child = $this->getNestedCategories($row['id_category'], $level + 1, $images, $limit, $this->deo_count, $carousel_type, $rate_images);
							if ($child) {
								foreach ($child as &$item) {
									if (array_key_exists($item['id_category'], $images)) {
										$item['image'] = $image_path.$images[$item['id_category']];
									}
									if (!in_array($item['id_category'], $this->id_categories)){
										if ($carousel_type){
											// $this->categories[] = $item;
										}else{
											$this->categories[] = array($item['id_category'] => $item);
										}
										$this->id_categories[] = $item['id_category'];
									}
									if (!in_array($item['id_category'], $this->id_deo_count)){
										$item['deo_count'] = $this->deo_count;
										$this->id_deo_count[] = $item['id_category'];
										$this->deo_count = $this->deo_count + 1;
									}
									$current[$row['id_category']] = $item;
								}
							}
							$buff[$row['id_parent']] = $current_category[0];
							if ($current) {
								$buff[$row['id_parent']]['children'] = &$current;
							}
						}
					}
				} else {
					// validate module
					if (!in_array($current_category[0]['id_category'], $this->id_categories)){
						$this->id_categories[] = $current_category[0]['id_category'];
						if ($carousel_type){
							$this->categories[] = $current_category[0];
						}else{
							$this->categories[] = array($current_category[0]['id_category'] => $current_category[0]);
						}
					}
					if (!in_array($current_category[0]['id_category'], $this->id_deo_count)){
						$current_category[0]['deo_count'] = $this->deo_count;
						$this->id_deo_count[] = $current_category[0]['id_category'];
						$this->deo_count = $this->deo_count + 1;
					}
					$buff[$parent] = $current_category[0];
				}
			}
		}

		return $buff;
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
				'lazyload',
				'rate_image',
				'category_img',
				'categorybox',
				'img_cat',
				'disable_html_tree_structure',
				'cate_depth',
				// 'orderby',
				'limit',
				'quantity',
				'description',
				'viewall',
				'id_root',
				'id_lang',
			);
		} elseif ($multi_lang == 1) {
			return array(
				'text_link_viewall',
				'link_viewall',
			);
		} elseif ($multi_lang == 2) {
			return array(
			);
		}
	}
}
