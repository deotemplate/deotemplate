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

class DeoSetting
{

	public static function getHookHome()
	{
		return array(
			'displayDeoHeaderMobile',
			'displayDeoNavMobile',
			'displayDeoContentMobile',
			'displayDeoFooterMobile',
			'displayTop',
			'displayNavFullWidth',
			'displayLeftColumn',
			'displayHome',
			'displayRightColumn',
			'displayFooter'
		);
	}
	const HOOK_BOXED = 0;
	const HOOK_FULWIDTH_INDEXPAGE = 1;
	const HOOK_FULWIDTH_OTHERPAGE = 1;
	const ROW_BOXED = 0;
	const ROW_FULWIDTH_INDEXPAGE = 1;
	const HOOK_DISABLE_CACHE = 1;
	const HOOK_ENABLE_CACHE = 0;
	/**
	 * hook for fullwidth and boxed
	 */
	public static function getIndexHook($type = 1)
	{
		if ($type == 1) {
			# get name hook
			return array(
				'displayDeoHeaderMobile',
				'displayDeoNavMobile',
				'displayDeoContentMobile',
				'displayDeoFooterMobile',
				'displayBanner',
				'displayNav1',
				'displayNav2',
				'displayTop',
				'displayNavFullWidth',
				'displayHome',
				'displayFooterBefore',
				'displayFooter',
				'displayFooterAfter',
			);
		} else if ($type == 2) {
			# get name hook
			return array(
				'displayDeoHeaderMobile' => 'displayDeoHeaderMobile',
				'displayDeoNavMobile' => 'displayDeoNavMobile',
				'displayDeoContentMobile' => 'displayDeoContentMobile',
				'displayDeoFooterMobile' => 'displayDeoFooterMobile',
				'displayBanner' => 'displayBanner',
				'displayNav1' => 'displayNav1',
				'displayNav2' => 'displayNav2',
				'displayTop' => 'displayTop',
				'displayNavFullWidth' => 'displayNavFullWidth',
				'displayHome' => 'displayHome',
				'displayFooterBefore' => 'displayFooterBefore',
				'displayFooter' => 'displayFooter',
				'displayFooterAfter' => 'displayFooterAfter',
			);
		} else if ($type == 3) {
			# get default fullwidth or boxed for each hook
			return array(
				'displayDeoHeaderMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayDeoNavMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayDeoContentMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayDeoFooterMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayBanner' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayNav1' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayNav2' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayTop' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayNavFullWidth' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayHome' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayFooterBefore' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayFooter' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayFooterAfter' => self::HOOK_FULWIDTH_INDEXPAGE,
			);
		}
	}

	/**
	 * hook for fullwidth and boxed
	 */
	public static function getOtherHook($type = 1)
	{
		if ($type == 1) {
			# get name hook
			return array(
				'displayDeoHeaderMobile',
				'displayDeoNavMobile',
				'displayDeoContentMobile',
				'displayDeoFooterMobile',
				'displayBanner',
				'displayNav1',
				'displayNav2',
				'displayTop',
				'displayNavFullWidth',
				// 'displayHome',
				'displayFooterBefore',
				'displayFooter',
				'displayFooterAfter',
			);
		} else if ($type == 2) {
			# get name hook
			return array(
				'displayDeoHeaderMobile' => 'displayDeoHeaderMobile',
				'displayDeoNavMobile' => 'displayDeoNavMobile',
				'displayDeoContentMobile' => 'displayDeoContentMobile',
				'displayDeoFooterMobile' => 'displayDeoFooterMobile',
				'displayBanner' => 'displayBanner',
				'displayNav1' => 'displayNav1',
				'displayNav2' => 'displayNav2',
				'displayTop' => 'displayTop',
				'displayNavFullWidth' => 'displayNavFullWidth',
				// 'displayHome' => 'displayHome',
				'displayFooterBefore' => 'displayFooterBefore',
				'displayFooter' => 'displayFooter',
				'displayFooterAfter' => 'displayFooterAfter',
			);
		} else if ($type == 3) {
			# get default value
			return array(
				'displayDeoHeaderMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayDeoNavMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayDeoContentMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayDeoFooterMobile' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayBanner' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayNav1' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayNav2' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayTop' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayNavFullWidth' => self::HOOK_FULWIDTH_INDEXPAGE,
				// 'displayHome' => self::HOOK_BOXED,
				'displayFooterBefore' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayFooter' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayFooterAfter' => self::HOOK_FULWIDTH_INDEXPAGE,
			);
		}
	}

	public static function getCacheHook($type = 1)
	{
		if ($type == 1) {
			# get name hook
			return array(
				'displayDeoHeaderMobile',
				'displayDeoNavMobile',
				'displayDeoContentMobile',
				'displayDeoFooterMobile',
				'displayBanner',
				'displayNav1',
				'displayNav2',
				'displayTop',
				'displayNavFullWidth',
				'displayHome',
				'displayFooterBefore',
				'displayFooter',
				'displayFooterAfter',
			);
		} else if ($type == 2) {
			# get name hook
			return array(
				'displayDeoHeaderMobile' => 'displayDeoHeaderMobile',
				'displayDeoNavMobile' => 'displayDeoNavMobile',
				'displayDeoContentMobile' => 'displayDeoContentMobile',
				'displayDeoFooterMobile' => 'displayDeoFooterMobile',
				'displayTop' => 'displayTop',
				'displayNavFullWidth' => 'displayNavFullWidth',
				'displayHome' => 'displayHome',
				'displayFooter' => 'displayFooter',
			);
		} else if ($type == 3) {
			# get default value
			return array(
				'displayDeoHeaderMobile' => self::HOOK_DISABLE_CACHE,
				'displayDeoNavMobile' => self::HOOK_DISABLE_CACHE,
				'displayDeoContentMobile' => self::HOOK_DISABLE_CACHE,
				'displayDeoFooterMobile' => self::HOOK_DISABLE_CACHE,
				'displayBanner' => self::HOOK_DISABLE_CACHE,
				'displayNav1' => self::HOOK_DISABLE_CACHE,
				'displayNav2' => self::HOOK_DISABLE_CACHE,
				'displayTop' => self::HOOK_DISABLE_CACHE,
				'displayNavFullWidth' => self::HOOK_FULWIDTH_INDEXPAGE,
				'displayHome' => self::HOOK_ENABLE_CACHE,
				'displayFooter' => self::HOOK_ENABLE_CACHE,
			);
		}
	}

	public static function getPositionsName()
	{
		return array('mobile', 'header', 'content', 'footer', 'product');
	}

	/**
	 * Get list hooks by type
	 * @param type $type: string in {all, header, footer, content, product}
	 * @return array
	 */
	public static function getHook($type = 'all')
	{
		$list_hook = array();
		$hook_mobile_default = array(
			'displayDeoHeaderMobile',
			'displayDeoNavMobile',
			'displayDeoContentMobile',
			'displayDeoFooterMobile',
		);
		if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
			$hook_header_default = array(
				'displayBanner',
				'displayNav1',
				'displayNav2',
				'displayTop',
				'displayNavFullWidth',
			);
		} else {
			$hook_header_default = array(
				'displayNav1',
				'displayNav2',
				'displayTop',
				'displayNavFullWidth',
			);
		}
		$hook_content_default = array(
			'displayLeftColumn',
			'displayHome',
			'displayRightColumn',
		);
		$hook_footer_default = array(
			'displayFooterBefore',
			'displayFooter',
			'displayFooterAfter',
		);
		$hook_product_default = array(
			'displayLeftColumnProduct',
			'displayRightColumnProduct',
			'displayReassurance',
			'displayProductButtons',
			'displayFooterProduct',
		);
		if ($type == 'all') {
			$list_hook = array_merge($hook_mobile_default, $hook_header_default, $hook_content_default, $hook_footer_default, $hook_product_default);
		} else if ($type == 'mobile') {
			$list_hook = $hook_mobile_default;
		} else if ($type == 'header') {
			$list_hook = $hook_header_default;
		} else if ($type == 'content') {
			$list_hook = $hook_content_default;
		} else if ($type == 'footer') {
			$list_hook = $hook_footer_default;
		} else if ($type == 'product') {
			$list_hook = $hook_product_default;
		}
		return $list_hook;
	}

	public static function getProductContainer()
	{
		$html = '<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="product">' . "\n";
		$html .= '	<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">' . "\n";
		$html .= '		<div class="thumbnail-container thumbnail-container-class">' . "\n";
		return $html;
	}
	public static function getProductContainerEnd()
	{
		$html = '		</div>' . "\n";
		$html .= '	</article>' . "\n";
		$html .= '</div>' . "\n";
		return $html;
	}

	public static function getProductFunctionalButtons($css = "")
	{
		if ($css){
			return '<div class="'.$css.'">';
		}else{
			return '<div class="box-button clearfix">';
		}
	}

	public static function getProductLeftBlock()
	{
		return '    <div class="product-image">';
	}

	public static function getProductRightBlock()
	{
		return '    <div class="product-meta">';
	}

	public static function getProductLeftBlockEnd()
	{
		return "</div>\n";
	}

	public static function getProductRightBlockEnd()
	{
		return "</div>\n";
	}

	public static function writeFile($folder, $file, $value)
	{
		if (!is_dir($folder)) {
			@mkdir($folder, 0755, true);
		}
		$file = $folder.'/'.$file;
		$handle = fopen($file, 'w+');
		fwrite($handle, ($value));
		fclose($handle);
	}

	public static function getRandomNumber()
	{
		return rand() + time();
	}

	public static function returnYesNo()
	{
		return array(
			array(
				'id' => 'active_on',
				'value' => 1,
				'label' => self::l('Enabled')
			),
			array(
				'id' => 'active_off',
				'value' => 0,
				'label' => self::l('Disabled')
		));
	}

	public static function returnTrueFalse()
	{
		return array(array(
				'id' => 'active_on',
				'value' => 'true',
				'label' => self::l('Enabled')
			),
			array(
				'id' => 'active_off',
				'value' => 'false',
				'label' => self::l('Disabled')
		));
	}

	public static function getOrderByBlog()
	{
		return array(
			array(
				'id' => 'id_deoblog_category', 'name' => self::l('Category')),
			array(
				'id' => 'id_deoblog', 'name' => self::l('ID')),
			array(
				'id' => 'meta_title', 'name' => self::l('Title')),
			array(
				'id' => 'date_add', 'name' => self::l('Date add')),
			array(
				'id' => 'date_upd', 'name' => self::l('Date update')),
		);
	}

	public static function getOrderByManu()
	{
		return array(
			array(
				'id' => 'id_manufacturer', 'name' => self::l('ID')),
			array(
				'id' => 'name', 'name' => self::l('Name')),
			array(
				'id' => 'date_add', 'name' => self::l('Date add')),
			array(
				'id' => 'date_upd', 'name' => self::l('Date update')),
		);
	}

	public static function getOrderBy()
	{
		return array(
			// array(
			// 	'id' => 'position', 'name' => self::l('Position')),    // remove to increase speed
			array(
				'id' => 'id_product', 'name' => self::l('ID')),
			array(
				'id' => 'name', 'name' => self::l('Name')),
			array(
				'id' => 'reference', 'name' => self::l('Reference')),
			array(
				'id' => 'price', 'name' => self::l('Base price')),
			array(
				'id' => 'date_add', 'name' => self::l('Date add')),
			array(
				'id' => 'date_upd', 'name' => self::l('Date update')),
		);
	}

	public static function getColumnGrid()
	{
		return array(
			'xxl' => self::l('Large Desktop Devices (width screen ≥ 1500px)'),
			'xl'  => self::l('Desktop Devices (width screen < 1500px)'),
			'lg'  => self::l('Small Desktop Devices (width screen < 1200px)'),
			'md'  => self::l('Tablets Devices (width screen < 992px)'),
			'sm'  => self::l('Small Tablets Devices (width screen < 768px)'),
			'xs'  => self::l('Mobile Devices (width screen < 576px)'),
			'sp'  => self::l('Small Mobile Devices(width screen < 480px)'),
		);
	}

	public static function l($text)
	{
		return $text;
	}

	public static function getAnimations()
	{
		return array(
			'none' => array(
				'name' => self::l('Turn off'),
				'query' => array(
					array(
						'id' => 'none',
						'name' => self::l('None'),
					)
				)
			),
			'attention_seekers' => array(
				'name' => self::l('Attention Seekers'),
				'query' => array(
					array(
						'id' => 'bounce',
						'name' => self::l('bounce'),
					),
					array(
						'id' => 'flash',
						'name' => self::l('flash'),
					), array(
						'id' => 'pulse',
						'name' => self::l('pulse'),
					), array(
						'id' => 'rubberBand',
						'name' => self::l('rubberBand'),
					), array(
						'id' => 'shake',
						'name' => self::l('shake'),
					), array(
						'id' => 'swing',
						'name' => self::l('swing'),
					), array(
						'id' => 'tada',
						'name' => self::l('tada'),
					), array(
						'id' => 'wobble',
						'name' => self::l('wobble'),
					)
				)
			),
			'Bouncing_Entrances' => array(
				'name' => self::l('Bouncing Entrances'),
				'query' => array(
					array(
						'id' => 'bounceIn',
						'name' => self::l('bounceIn'),
					),
					array(
						'id' => 'bounceInDown',
						'name' => self::l('bounceInDown'),
					),
					array(
						'id' => 'bounceInLeft',
						'name' => self::l('bounceInLeft'),
					),
					array(
						'id' => 'bounceInRight',
						'name' => self::l('bounceInRight'),
					),
					array(
						'id' => 'bounceInUp',
						'name' => self::l('bounceInUp'),
					)
				),
			),
			'Bouncing_Exits' => array(
				'name' => self::l('Bouncing Exits'),
				'query' => array(
					array(
						'id' => 'bounceOut',
						'name' => self::l('bounceOut'),
					),
					array(
						'id' => 'bounceOutDown',
						'name' => self::l('bounceOutDown'),
					),
					array(
						'id' => 'bounceOutLeft',
						'name' => self::l('bounceOutLeft'),
					),
					array(
						'id' => 'bounceOutRight',
						'name' => self::l('bounceOutRight'),
					),
					array(
						'id' => 'bounceOutUp',
						'name' => self::l('bounceOutUp'),
					)
				),
			),
			'Fading_Entrances' => array(
				'name' => self::l('Fading Entrances'),
				'query' => array(
					array(
						'id' => 'fadeIn',
						'name' => self::l('fadeIn'),
					),
					array(
						'id' => 'fadeInDown',
						'name' => self::l('fadeInDown'),
					),
					array(
						'id' => 'fadeInDownBig',
						'name' => self::l('fadeInDownBig'),
					),
					array(
						'id' => 'fadeInLeft',
						'name' => self::l('fadeInLeft'),
					),
					array(
						'id' => 'fadeInLeftBig',
						'name' => self::l('fadeInLeftBig'),
					),
					array(
						'id' => 'fadeInRight',
						'name' => self::l('fadeInRight'),
					),
					array(
						'id' => 'fadeInRightBig',
						'name' => self::l('fadeInRightBig'),
					),
					array(
						'id' => 'fadeInRight',
						'name' => self::l('fadeInRight'),
					),
					array(
						'id' => 'fadeInRightBig',
						'name' => self::l('fadeInRightBig'),
					),
					array(
						'id' => 'fadeInUp',
						'name' => self::l('fadeInUp'),
					),
					array(
						'id' => 'fadeInUpBig',
						'name' => self::l('fadeInUpBig'),
					),
				),
			),
			'Fading_Exits' => array(
				'name' => self::l('Fading Exits'),
				'query' => array(
					array(
						'id' => 'fadeOut',
						'name' => self::l('fadeOut'),
					),
					array(
						'id' => 'fadeOutDown',
						'name' => self::l('fadeOutDown'),
					),
					array(
						'id' => 'fadeOutDownBig',
						'name' => self::l('fadeOutDownBig'),
					),
					array(
						'id' => 'fadeOutLeft',
						'name' => self::l('fadeOutLeft'),
					),
					array(
						'id' => 'fadeOutRight',
						'name' => self::l('fadeOutRight'),
					),
					array(
						'id' => 'fadeOutRightBig',
						'name' => self::l('fadeOutRightBig'),
					),
					array(
						'id' => 'fadeOutUp',
						'name' => self::l('fadeOutUp'),
					),
					array(
						'id' => 'fadeOutUpBig',
						'name' => self::l('fadeOutUpBig'),
					)
				),
			),
			'Flippers' => array(
				'name' => self::l('Flippers'),
				'query' => array(
					array(
						'id' => 'flip',
						'name' => self::l('flip'),
					),
					array(
						'id' => 'flipInX',
						'name' => self::l('flipInX'),
					),
					array(
						'id' => 'flipInY',
						'name' => self::l('flipInY'),
					),
					array(
						'id' => 'flipOutX',
						'name' => self::l('flipOutX'),
					),
					array(
						'id' => 'flipOutY',
						'name' => self::l('flipOutY'),
					)
				),
			),
			'Lightspeed' => array(
				'name' => self::l('Lightspeed'),
				'query' => array(
					array(
						'id' => 'lightSpeedIn',
						'name' => self::l('lightSpeedIn'),
					),
					array(
						'id' => 'lightSpeedOut',
						'name' => self::l('lightSpeedOut'),
					)
				),
			),
			'Rotating_Entrances' => array(
				'name' => self::l('Rotating Entrances'),
				'query' => array(
					array(
						'id' => 'rotateIn',
						'name' => self::l('rotateIn'),
					),
					array(
						'id' => 'rotateInDownLeft',
						'name' => self::l('rotateInDownLeft'),
					),
					array(
						'id' => 'rotateInDownRight',
						'name' => self::l('rotateInDownRight'),
					),
					array(
						'id' => 'rotateInUpLeft',
						'name' => self::l('rotateInUpLeft'),
					),
					array(
						'id' => 'rotateInUpRight',
						'name' => self::l('rotateInUpRight'),
					)
				),
			),
			'Rotating_Exits' => array(
				'name' => self::l('Rotating Exits'),
				'query' => array(
					array(
						'id' => 'rotateOut',
						'name' => self::l('rotateOut'),
					),
					array(
						'id' => 'rotateOutDownLeft',
						'name' => self::l('rotateOutDownLeft'),
					),
					array(
						'id' => 'rotateOutDownRight',
						'name' => self::l('rotateOutDownRight'),
					),
					array(
						'id' => 'rotateOutUpLeft',
						'name' => self::l('rotateOutUpLeft'),
					),
					array(
						'id' => 'rotateOutUpRight',
						'name' => self::l('rotateOutUpRight'),
					)
				),
			),
			'Specials' => array(
				'name' => self::l('Specials'),
				'query' => array(
					array(
						'id' => 'hinge',
						'name' => self::l('hinge'),
					),
					array(
						'id' => 'rollIn',
						'name' => self::l('rollIn'),
					),
					array(
						'id' => 'rollOut',
						'name' => self::l('rollOut'),
					)
				),
			),
			'Zoom Entrances' => array(
				'name' => self::l('Zoom Entrances'),
				'query' => array(
					array(
						'id' => 'zoomIn',
						'name' => self::l('zoomIn'),
					),
					array(
						'id' => 'zoomInDown',
						'name' => self::l('zoomInDown'),
					),
					array(
						'id' => 'zoomInLeft',
						'name' => self::l('zoomInLeft'),
					),
					array(
						'id' => 'zoomInRight',
						'name' => self::l('zoomInRight'),
					),
					array(
						'id' => 'zoomInUp',
						'name' => self::l('zoomInUp'),
					)
				),
			),
			'Zoom_Exits' => array(
				'name' => self::l('Zoom Exits'),
				'query' => array(
					array(
						'id' => 'zoomOut',
						'name' => self::l('zoomOut'),
					),
					array(
						'id' => 'zoomOutDown',
						'name' => self::l('zoomOutDown'),
					),
					array(
						'id' => 'zoomOutLeft',
						'name' => self::l('zoomOutLeft'),
					),
					array(
						'id' => 'zoomOutRight',
						'name' => self::l('zoomOutRight'),
					),
					array(
						'id' => 'zoomOutUp',
						'name' => self::l('zoomOutUp'),
					)
				),
			)
		);
	}
	
	// build list animation for group and column
	public static function getAnimationsColumnGroup()
	{
		return array(
			'none' => array(
				'name' => self::l('Turn off'),
				'query' => array(
					array(
						'id' => 'none',
						'name' => self::l('None'),
					)
				)
			),
			'Fading_Entrances' => array(
				'name' => self::l('Fading Entrances'),
				'query' => array(
					array(
						'id' => 'fadeIn',
						'name' => self::l('fadeIn'),
					),
					array(
						'id' => 'fadeInDown',
						'name' => self::l('fadeInDown'),
					),
					array(
						'id' => 'fadeInDownBig',
						'name' => self::l('fadeInDownBig'),
					),
					array(
						'id' => 'fadeInLeft',
						'name' => self::l('fadeInLeft'),
					),
					array(
						'id' => 'fadeInLeftBig',
						'name' => self::l('fadeInLeftBig'),
					),
					array(
						'id' => 'fadeInRight',
						'name' => self::l('fadeInRight'),
					),
					array(
						'id' => 'fadeInRightBig',
						'name' => self::l('fadeInRightBig'),
					),
					array(
						'id' => 'fadeInUp',
						'name' => self::l('fadeInUp'),
					),
					array(
						'id' => 'fadeInUpBig',
						'name' => self::l('fadeInUpBig'),
					),
				),
			),
			'Bouncing_Entrances' => array(
				'name' => self::l('Bouncing Entrances'),
				'query' => array(
					array(
						'id' => 'bounceIn',
						'name' => self::l('bounceIn'),
					),
					array(
						'id' => 'bounceInDown',
						'name' => self::l('bounceInDown'),
					),
					array(
						'id' => 'bounceInLeft',
						'name' => self::l('bounceInLeft'),
					),
					array(
						'id' => 'bounceInRight',
						'name' => self::l('bounceInRight'),
					),
					array(
						'id' => 'bounceInUp',
						'name' => self::l('bounceInUp'),
					)
				),
			),
			'Zoom Entrances' => array(
				'name' => self::l('Zoom Entrances'),
				'query' => array(
					array(
						'id' => 'zoomIn',
						'name' => self::l('zoomIn'),
					),
					array(
						'id' => 'zoomInDown',
						'name' => self::l('zoomInDown'),
					),
					array(
						'id' => 'zoomInLeft',
						'name' => self::l('zoomInLeft'),
					),
					array(
						'id' => 'zoomInRight',
						'name' => self::l('zoomInRight'),
					),
					array(
						'id' => 'zoomInUp',
						'name' => self::l('zoomInUp'),
					)
				),
			),
			'attention_seekers' => array(
				'name' => self::l('Attention Seekers'),
				'query' => array(
					array(
						'id' => 'bounce',
						'name' => self::l('bounce'),
					),
					array(
						'id' => 'flash',
						'name' => self::l('flash'),
					),
					array(
						'id' => 'pulse',
						'name' => self::l('pulse'),
					),
					array(
						'id' => 'rubberBand',
						'name' => self::l('rubberBand'),
					),
					array(
						'id' => 'shake',
						'name' => self::l('shake'),
					),
					array(
						'id' => 'swing',
						'name' => self::l('swing'),
					),
					array(
						'id' => 'tada',
						'name' => self::l('tada'),
					),
					array(
						'id' => 'wobble',
						'name' => self::l('wobble'),
					)
				)
			),
			'Flippers' => array(
				'name' => self::l('Flippers'),
				'query' => array(
					array(
						'id' => 'flip',
						'name' => self::l('flip'),
					),
					array(
						'id' => 'flipInX',
						'name' => self::l('flipInX'),
					),
					array(
						'id' => 'flipInY',
						'name' => self::l('flipInY'),
					),
					array(
						'id' => 'flipOutX',
						'name' => self::l('flipOutX'),
					),
					array(
						'id' => 'flipOutY',
						'name' => self::l('flipOutY'),
					)
				),
			),
			'Lightspeed' => array(
				'name' => self::l('Lightspeed'),
				'query' => array(
					array(
						'id' => 'lightSpeedIn',
						'name' => self::l('lightSpeedIn'),
					),
					array(
						'id' => 'lightSpeedOut',
						'name' => self::l('lightSpeedOut'),
					)
				),
			),
			'Rotating_Entrances' => array(
				'name' => self::l('Rotating Entrances'),
				'query' => array(
					array(
						'id' => 'rotateIn',
						'name' => self::l('rotateIn'),
					),
					array(
						'id' => 'rotateInDownLeft',
						'name' => self::l('rotateInDownLeft'),
					),
					array(
						'id' => 'rotateInDownRight',
						'name' => self::l('rotateInDownRight'),
					),
					array(
						'id' => 'rotateInUpLeft',
						'name' => self::l('rotateInUpLeft'),
					),
					array(
						'id' => 'rotateInUpRight',
						'name' => self::l('rotateInUpRight'),
					)
				),
			),
			'Specials' => array(
				'name' => self::l('Specials'),
				'query' => array(
					array(
						'id' => 'hinge',
						'name' => self::l('hinge'),
					),
					array(
						'id' => 'rollIn',
						'name' => self::l('rollIn'),
					),
					array(
						'id' => 'rollOut',
						'name' => self::l('rollOut'),
					)
				),
			),
			'Bouncing_Exits' => array(
				'name' => self::l('Bouncing Exits'),
				'query' => array(
					array(
						'id' => 'bounceOut',
						'name' => self::l('bounceOut'),
					),
					array(
						'id' => 'bounceOutDown',
						'name' => self::l('bounceOutDown'),
					),
					array(
						'id' => 'bounceOutLeft',
						'name' => self::l('bounceOutLeft'),
					),
					array(
						'id' => 'bounceOutRight',
						'name' => self::l('bounceOutRight'),
					),
					array(
						'id' => 'bounceOutUp',
						'name' => self::l('bounceOutUp'),
					)
				),
			),
			'Fading_Exits' => array(
				'name' => self::l('Fading Exits'),
				'query' => array(
					array(
						'id' => 'fadeOut',
						'name' => self::l('fadeOut'),
					),
					array(
						'id' => 'fadeOutDown',
						'name' => self::l('fadeOutDown'),
					),
					array(
						'id' => 'fadeOutDownBig',
						'name' => self::l('fadeOutDownBig'),
					),
					array(
						'id' => 'fadeOutLeft',
						'name' => self::l('fadeOutLeft'),
					),
					array(
						'id' => 'fadeOutRight',
						'name' => self::l('fadeOutRight'),
					),
					array(
						'id' => 'fadeOutRightBig',
						'name' => self::l('fadeOutRightBig'),
					),
					array(
						'id' => 'fadeOutUp',
						'name' => self::l('fadeOutUp'),
					),
					array(
						'id' => 'fadeOutUpBig',
						'name' => self::l('fadeOutUpBig'),
					)
				),
			),
			'Rotating_Exits' => array(
				'name' => self::l('Rotating Exits'),
				'query' => array(
					array(
						'id' => 'rotateOut',
						'name' => self::l('rotateOut'),
					),
					array(
						'id' => 'rotateOutDownLeft',
						'name' => self::l('rotateOutDownLeft'),
					),
					array(
						'id' => 'rotateOutDownRight',
						'name' => self::l('rotateOutDownRight'),
					),
					array(
						'id' => 'rotateOutUpLeft',
						'name' => self::l('rotateOutUpLeft'),
					),
					array(
						'id' => 'rotateOutUpRight',
						'name' => self::l('rotateOutUpRight'),
					)
				),
			),
			'Zoom_Exits' => array(
				'name' => self::l('Zoom Exits'),
				'query' => array(
					array(
						'id' => 'zoomOut',
						'name' => self::l('zoomOut'),
					),
					array(
						'id' => 'zoomOutDown',
						'name' => self::l('zoomOutDown'),
					),
					array(
						'id' => 'zoomOutLeft',
						'name' => self::l('zoomOutLeft'),
					),
					array(
						'id' => 'zoomOutRight',
						'name' => self::l('zoomOutRight'),
					),
					array(
						'id' => 'zoomOutUp',
						'name' => self::l('zoomOutUp'),
					)
				),
			)
		);
	}

	public static function requireShortCode($short_code, $theme_dir = '')
	{
		if (file_exists($theme_dir.'modules/deotemplate/classes/shortcodes/'.$short_code)) {
			return $theme_dir.'modules/deotemplate/classes/shortcodes/'.$short_code;
		}
		if (file_exists(_PS_MODULE_DIR_.'deotemplate/classes/shortcodes/'.$short_code)) {
			return _PS_MODULE_DIR_.'deotemplate/classes/shortcodes/'.$short_code;
		}
		return false;
	}

	public static function getControllerId($controller, $ids)
	{
		switch ($controller) {
			case 'product':
				$current_id = Tools::getValue('id_product');
				if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
					return $current_id;
				}
				break;
			case 'category':
				$current_id = Tools::getValue('id_category');
				if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
					return $current_id;
				}
				break;
			case 'cms':
				$current_id = Tools::getValue('id_cms');
				if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
					return $current_id;
				}
				break;
			default:
				return false;
		}
	}

	public static function getAllowOverrideHook()
	{
		return array('rightcolumn', 'leftcolumn', 'home', 'top', 'footer');
	}

	public static function returnWidthList()
	{
		return array('12', '11', '10', '9.6', '9', '8', '7.2', '7', '6', '5', '4.8', '4', '3', '2.4', '2', '1');
	}

	public static function returnWidthColumn()
    {
        return array(
            '12' => '100.00%', 
            '11' => '91.67%', 
            '10' => '83.33%', 
            '9.6' => '80%', 
            '9' => '75%', 
            '8' => '66.67%', 
            '7.2' => '60.00%', 
            '7' => '58.33%', 
            '6' => '50.00%', 
            '5' => '41.67%', 
            '4.8' => '40.00%', 
            '4' => '33.33%', 
            '3' => '25.00%', 
            '2.4' => '20.00%', 
            '2' => '16.6%', 
            '1' => '8.33%'
        );
    }

    public static function returnWidthColumnCreate()
    {
        return array(
            '1' => '8.33%',
            '2' => '16.6%', 
            '3' => '25.00%', 
            '4' => '33.33%', 
            '5' => '41.67%', 
            '6' => '50.00%'
        );
    }

	public static function getDefaultNameImage($type = 'small')
	{
		$sep = '_';
		$arr = array('small' => 'small'.$sep.'default', 'thickbox' => 'thickbox'.$sep.'default');
		return $arr[$type];
	}

	
	public static function getOverrideHook()
	{
		$list_hook = array(
			'displayBanner',
			'displayNav1',
			'displayNav2',
			'displayTop',
			'displayNavFullWidth',
			'displayHome',
			'displayLeftColumn',
			'displayRightColumn',
			'displayFooterBefore',
			'displayFooter',
			'displayFooterAfter',
			'displayFooterProduct',
			'displayRightColumnProduct',
			'displayLeftColumnProduct',
		);

		return $list_hook;
	}

	public static function getCssFilesAvailable()
	{
		$elements = array(
			'image-category',
			'service',
			'fake-product',
			'fake-number-static',
			'testimonials',
			'banner',
			'open-time',
			'about-us',
			'history',
			'process',
			'portrait',
			'header-contact',
			'text-header',
			'text-slide',
			'header-phone',
			'text-shop-now',
			'footer-contact'
		);

		$widgets_modules = array(
			'DeoBlockLink',
			'DeoTabs',
			'DeoProductTabs',
			'DeoManufacturersCarousel',
			'DeoCategoryImage',
			'DeoCustomerActions',
			'DeoCountdown',
			'DeoSocialLogin',
			'DeoPopup',
			'DeoGoogleMap',
			'DeoGallery',
			'DeoProductList',
			'DeoBlogs',
			'DeoInstagram',
			'DeoVideo',
			'DeoImageHotspot',
			'DeoAlert',
			'DeoAccordions',
			'DeoMegamenu',
			'DeoMegamenuTabs',
			'DeoAdvancedSearch',
			'DeoSlideshow',
			// modules
			'ps_searchbar',
			'ps_currencyselector',
			'ps_customersignin',
			'ps_languageselector',
			'ps_shoppingcart',
			'ps_emailsubscription',
			'ps_socialfollow'
		);

		$exceptions = array(
			'box-fake-number-static' => 'fake-number-static',
		);

		return array('elements' => $elements, 'widgets_modules' => $widgets_modules, 'exceptions' => $exceptions);
	}

	public static function getLabelAndFlag()
	{
		return array(
			'all' => self::l('Show all Label'),
			'disable' => self::l('Disable all Label'),
			'newsale' => self::l('Label and Flag New & Sale'),
			'newdiscount' => self::l('Label and Flag New & Discount'),
		);
	}

	public static function getEffectHover()
	{
		return array(
			'disable' => self::l('Disable'),
			'bg-hover-white' => self::l('Background Color White'),
			'bg-hover-black' => self::l('Background Color Black'),
			'bg-hover-skin' => self::l('Background Color Skin'),
		);
	}

	public static function getThumbnailPosition()
	{
		return array(
			'left' => self::l('Left'),
			'right' => self::l('Right'),
			'bottom' => self::l('Bottom'),
			'none' => self::l('Hidden'),
		);
	}

	public static function getProductMoreInfo()
	{
		return array(
			'show_all' => self::l('Show All'),
			'tab' => self::l('Tab'),
			'accordions' => self::l('Accordions'),
		);
	}
}
