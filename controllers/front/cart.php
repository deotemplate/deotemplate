<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

class DeoTemplateCartModuleFrontController extends ModuleFrontController
{
	/**
	* @see FrontController::initContent()
	*/

	public function initContent()
	{
		$this->ajax = true;
		parent::initContent();
	}

	public function displayAjax()
	{
		$context = Context::getContext();
		$configures_ajax_cart = Tools::getValue('configures_ajax_cart');

		if ((!$this->isTokenValid() || !Tools::getValue('action')) && Tools::getValue('action') != 'get-new-review') {
			// Ooops! Token is not valid!
			$result = '';
			die($result);
		}

		// render modal popup and dropdown cart
		if (Tools::getValue('action') == 'render-modal') {
			$modal = '';
			$notification = '';
			$contentcart = '';
			$flycart = '';

			if (Tools::getValue('only_init_html') == 0) {
				// $modal = $this->renderModal();
				// $notification = $this->renderNotification($configures_ajax_cart);
				$flycart = $this->renderFlyCart($configures_ajax_cart);
			}

			if ($configures_ajax_cart['enable_dropdown_defaultcart'] || $configures_ajax_cart['enable_dropdown_flycart']) {
				$only_content_cart = Tools::getValue('only_content_cart');
				$contentcart = $this->renderContentCart($only_content_cart, $configures_ajax_cart);
			}

			ob_end_clean();
			header('Content-Type: application/json');
			// Tools::redirect(false, false, null, 'Content-Type: application/json');
			$this->ajaxRender(json_encode(array(
				'contentcart' => $contentcart,
				'modal'   => $modal,
				'notification' => $notification,
				'flycart' => $flycart,
			)));
		}

		if (Tools::getValue('action') == 'get-combination-data') {
			$result = array();
			
			$id_product = Tools::getValue('id_product');
			$id_product_attr = Tools::getValue('id_product_attr');
			$deo_more_product_image = Tools::getValue('deo_more_product_image');
			$deo_cart_quantity = Tools::getValue('deo_cart_quantity');
			$deo_product_atribute = Tools::getValue('deo_product_atribute');
			$deo_product_thumbnail = Tools::getValue('deo_product_thumbnail');
			$deo_stock = Tools::getValue('deo_stock');

			include_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoFeatureProduct.php');
			$deo_feature_product = new DeoFeatureProduct();
			$product = $deo_feature_product->getTemplateVarProductExtend($id_product, $id_product_attr);

			$templateVars = array(
				'product' => $product,
				'RTL' => $context->language->is_rtl,
				'more_product_img' => (int) DeoHelper::getConfig('AJAX_MULTIPLE_PRODUCT_IMAGE'),
			);

			if (is_array($deo_product_atribute)){
				$result['atribute_list'] = $this->renderDeoProductAtribute($product, $deo_product_atribute, $deo_feature_product->getAtributeList($id_product));
			}

			if (is_array($deo_cart_quantity)){
				$result['cart_quantity'] = $this->renderDeoCartQuantity($product, $deo_cart_quantity);
			}

			if (is_array($deo_product_thumbnail)){
				$result['product_thumbnail'] = $this->renderProductThumbnail($product, $deo_product_thumbnail);
			}

			if (is_array($deo_more_product_image)){
				$result['more_image_product'] = $this->renderDeoMoreProductImage($product, $deo_more_product_image);
			}

			if ($deo_stock){
				$result['stock'] = $this->renderDeoStock($product);
			}

			$result['add_to_cart'] = $this->renderDeoCartButton($product);
			$result['product_price_and_shipping'] = $this->renderProductPriceAndShipping($product);

			ob_end_clean();
			header('Content-Type: application/json');
			// Tools::redirect(false, false, null, 'Content-Type: application/json');
			$this->ajaxRender(json_encode($result));
		}

		if (Tools::getValue('action') == 'get-attribute-data') {
			$result = array();
			
			$group = Tools::getValue('group');
			$id_product = Tools::getValue('id_product');
			$id_product_attr = Tools::getValue('id_product_attr');
			$deo_more_product_image = Tools::getValue('deo_more_product_image');
			$deo_cart_quantity = Tools::getValue('deo_cart_quantity');
			$deo_product_atribute = Tools::getValue('deo_product_atribute');
			$deo_product_thumbnail = Tools::getValue('deo_product_thumbnail');
			$deo_stock = Tools::getValue('deo_stock');

			$id_product_attribute = (int) Product::getIdProductAttributeByIdAttributes($id_product, $group, true);
			include_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoFeatureProduct.php');
			$deo_feature_product = new DeoFeatureProduct();
			$product = $deo_feature_product->getTemplateVarProductExtend($id_product, $id_product_attribute);

			$templateVars = array(
				'product' => $product,
				'RTL' => $context->language->is_rtl,
				'more_product_img' => (int) DeoHelper::getConfig('AJAX_MULTIPLE_PRODUCT_IMAGE'),
			);

			// if (is_array($deo_product_atribute)){
			// }

			if (is_array($deo_cart_quantity)){
				$result['cart_quantity'] = $this->renderDeoCartQuantity($product, $deo_cart_quantity);
			}

			if (is_array($deo_product_thumbnail)){
				$result['product_thumbnail'] = $this->renderProductThumbnail($product, $deo_product_thumbnail);
			}

			if (is_array($deo_more_product_image)){
				$result['more_image_product'] = $this->renderDeoMoreProductImage($product, $deo_more_product_image);
			}

			if ($deo_stock){
				$result['stock'] = $this->renderDeoStock($product);
			}

			$result['add_to_cart'] = $this->renderDeoCartButton($product);
			$result['combination'] = $this->renderDeoProductCombination($deo_feature_product->getCombinations($product));
			$result['product_price_and_shipping'] = $this->renderProductPriceAndShipping($product);

			ob_end_clean();
			header('Content-Type: application/json');
			// Tools::redirect(false, false, null, 'Content-Type: application/json');
			$this->ajaxRender(json_encode($result));
		}

		if (Tools::getValue('action') == 'check-product-outstock') {
			$id_product = Tools::getValue('id_product');
			$id_product_attribute = Tools::getValue('id_product_attribute');
			$id_customization = Tools::getValue('id_customization');
			$check_product_in_cart = Tools::getValue('check_product_in_cart');
			$quantity = Tools::getValue('quantity');
			$qty_to_check = $quantity;
			
			if ($check_product_in_cart == 'true') {
				$cart_products = $context->cart->getProducts();

				if (is_array($cart_products)) {
					foreach ($cart_products as $cart_product) {
						if ((!isset($id_product_attribute) || ($cart_product['id_product_attribute'] == $id_product_attribute && $cart_product['id_customization'] == $id_customization )) && isset($id_product) && $cart_product['id_product'] == $id_product) {
							$qty_to_check = $cart_product['cart_quantity'];
							$qty_to_check += $quantity;
							break;
						}
					}
				}
			}

			$product = new Product($id_product, true, $context->language->id);
			$return = true;
			// Check product quantity availability
			if ($id_product_attribute) {
				if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($id_product_attribute, $qty_to_check)) {
					$return = false;
				}
			} elseif (!$product->checkQty($qty_to_check)) {
				$return = false;
			}

			ob_end_clean();
			header('Content-Type: application/json');
			// Tools::redirect(false, false, null, 'Content-Type: application/json');
			$this->ajaxRender(json_encode(array(
				'success' => $return,
			)));
		}
	}

	// create fly cart
	public function renderFlyCart($configures)
	{
		$output = '';
		$check_create_slidebar = false;

		if ($configures['enable_dropdown_defaultcart'] && ($configures['type_dropdown_defaultcart'] == 'slidebar_left' || $configures['type_dropdown_defaultcart'] == 'slidebar_right' || $configures['type_dropdown_defaultcart'] == 'slidebar_top' || $configures['type_dropdown_defaultcart'] == 'slidebar_bottom')) {
			// reverse position with rtl
			$type_dropdown_defaultcart = $configures['type_dropdown_defaultcart'];
			if ($this->context->language->is_rtl) {
				$type_dropdown_defaultcart = ($type_dropdown_defaultcart == 'slidebar_left') ? 'slidebar_right' : 'slidebar_left';
			}
			$output .= $this->buildFlyCartSlideBar($type_dropdown_defaultcart, $configures);
			if ($configures['type_dropdown_defaultcart'] == $configures['type_dropdown_flycart']) {
				$check_create_slidebar = true;
			}
		}
		if ($configures['enable_dropdown_flycart']) {
			
			$type_fly_cart = $configures['type_dropdown_flycart'];
			if (!$check_create_slidebar && ($type_fly_cart == 'slidebar_left' || $type_fly_cart == 'slidebar_right' || $type_fly_cart == 'slidebar_top' || $type_fly_cart == 'slidebar_bottom')) {

				// reverse position with rtl
				if ($this->context->language->is_rtl){
					$type_fly_cart = ($type_fly_cart == 'slidebar_left') ? 'slidebar_right' : 'slidebar_left';
				}
				$output .= $this->buildFlyCartSlideBar($type_fly_cart, $configures);
			}
			$output .= $this->buildFlyCart($configures, $type_fly_cart);
		}

		return $output;
	}

	// build fly cart
	public function buildFlyCart($params_ajax_cart, $type_fly_cart)
	{
		$position_vertical_flycart = $params_ajax_cart['position_vertical_flycart'];
		$position_vertical_value_flycart = $params_ajax_cart['position_vertical_value_flycart'].'px';
		if ($this->context->language->is_rtl){
			if ($position_vertical_flycart == 'left'){
				$position_vertical_flycart = 'right';
			}elseif ($position_vertical_flycart == 'right'){
				$position_vertical_flycart = 'left';
			}
		}
		$position_horizontal_flycart = $params_ajax_cart['position_horizontal_flycart'];
		$position_horizontal_value_flycart = $params_ajax_cart['position_horizontal_value_flycart'].'px';

		$templateVars = array(
			'type_fly_cart' => $type_fly_cart,
			'position_vertical_flycart' => $position_vertical_flycart,
			'position_vertical_value_flycart' => $position_vertical_value_flycart,
			'position_horizontal_flycart' => $position_horizontal_flycart,
			'position_horizontal_value_flycart' => $position_horizontal_value_flycart,
		);
		$this->context->smarty->assign($templateVars);

		return $this->module->fetch('module:deotemplate/views/templates/front/feature/fly_cart.tpl');
	}

	// build fly cart
	public function buildFlyCartSlideBar($type, $configures)
	{
		$templateVars = array(
			'type' => $type,
			'enable_overlay_background' => (int) $configures['enable_overlay_background_flycart'],
		);
		$this->context->smarty->assign($templateVars);

		return $this->module->fetch('module:deotemplate/views/templates/front/feature/fly_cart_slide_bar.tpl');
	}

		// render notification
	public function renderNotification($configures)
	{   
		$horizontal_position_notification = $configures['horizontal_position_notification'];
		if ($this->context->language->is_rtl){
			$horizontal_position_notification = ($horizontal_position_notification == 'left') ? 'right' : 'left';
		}
		$horizontal_position_value_notification = $configures['horizontal_position_value_notification'].'px';

		$vertical_position_notification = $configures['vertical_position_notification'];
		$vertical_position_value_notification = $configures['vertical_position_value_notification'].'px';
		$width_notification = $configures['width_notification_notification'];

		$templateVars = array(
			'vertical_position_notification' => $vertical_position_notification,
			'vertical_position_value_notification' => $vertical_position_value_notification,
			'horizontal_position_notification' => $horizontal_position_notification,
			'horizontal_position_value_notification' => $horizontal_position_value_notification,
			'width_notification' => $width_notification,
		);
		$this->context->smarty->assign($templateVars);
		$output = $this->module->fetch('module:deotemplate/views/templates/front/feature/notification.tpl');

		return $output;
	}

	// render modal cart popup
	public function renderModal()
	{
		$output = $this->module->fetch('module:deotemplate/views/templates/front/feature/modal.tpl');

		return $output;
	}

	// render price
	public function renderDeoMoreProductImage($product, $deo_more_product_image)
	{
		$templateVars = array(
			'product' => $product,
		);

		$deo_more_product_image['responsive'] = ($deo_more_product_image['responsive']) ? json_encode($deo_more_product_image['responsive']) : false;
		$templateVars = array_merge($templateVars, $deo_more_product_image);
		$this->context->smarty->assign($templateVars);
		$output = $this->module->fetch('module:deotemplate/views/templates/hook/products/more_image.tpl');

		return $output;
	}

	// render stock
	public function renderDeoStock($product)
	{
		$templateVars = array(
			'product' => $product,
		);

		$this->context->smarty->assign($templateVars);
		$output = $this->module->fetch('module:deotemplate/views/templates/hook/products/stock.tpl');

		return $output;
	}

	

	// render price
	public function renderProductPriceAndShipping($product)
	{
		$templateVars = array(
			'product' => $product,
		);
		$this->context->smarty->assign($templateVars);

		$output = $this->module->fetch('module:deotemplate/views/templates/front/products/product_price_and_shipping.tpl');

		return $output;
	}

	// render product image
	public function renderProductThumbnail($product, $deo_product_thumbnail)
	{
		$templateVars = array(
			'product' => $product,
			'second_img' => (int) DeoHelper::getConfig('AJAX_SECOND_PRODUCT_IMAGE'),
		);
		$templateVars = array_merge($templateVars, $deo_product_thumbnail);
		$this->context->smarty->assign($templateVars);

		$output = $this->module->fetch('module:deotemplate/views/templates/hook/products/product_thumbnail.tpl');

		return $output;
	}

	// render cart button
	public function renderDeoCartButton($product)
	{
		$templateVars = array(
			'product' => $product,
			'static_token' => Tools::getToken(false),
			'link_cart' => $this->context->link->getPageLink('cart', Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')),
		);
		$this->context->smarty->assign($templateVars);

		$output = $this->module->fetch('module:deotemplate/views/templates/hook/products/add_to_cart.tpl');

		return $output;
	}

	// render cart quantity
	public function renderDeoCartQuantity($product, $deo_cart_quantity)
	{
		$templateVars = array(
			'product' => $product,
		);
		$templateVars = array_merge($templateVars, $deo_cart_quantity);
		$this->context->smarty->assign($templateVars);

		$output = $this->module->fetch('module:deotemplate/views/templates/hook/products/quantity.tpl');

		return $output;
	}

	// render attribute list
	public function renderDeoProductAtribute($product, $deo_product_atribute, $groups)
	{
		$templateVars = array(
			'product' => $product,
			'groups' => $groups,
		);
		$templateVars = array_merge($templateVars, $deo_product_atribute);
		$this->context->smarty->assign($templateVars);

		$output = $this->module->fetch('module:deotemplate/views/templates/hook/products/attribute.tpl');

		return $output;
	}

	// render combination
	public function renderDeoProductCombination($product)
	{
		$templateVars = array(
			'product' => $product,
		);
		$this->context->smarty->assign($templateVars);

		$output = $this->module->fetch('module:deotemplate/views/templates/hook/products/combination.tpl');

		return $output;
	}

	// render content cart
	public function renderContentCart($only_content_cart, $configures)
	{
		$cart = (new CartPresenter)->present($this->context->cart);

		// add new infomations for attribute
		foreach ($cart['products'] as $product) {
			include_once(_PS_MODULE_DIR_ . 'deotemplate/classes/Feature/DeoFeatureProduct.php');
			$deo_feature_product = new DeoFeatureProduct();
			$product['attribute_infomations'] = $deo_feature_product->getAtributeList($product['id_product'], $product['id_product_attribute']);
		}

		$drop_down_html = '';
		if ($cart['products_count'] > 0) {
			$templateVars = array(
				'only_content_cart' => $only_content_cart,
				'cart' => $cart,
				'cart_url' => $this->context->link->getPageLink('cart', null, $this->context->language->id, array('action' => 'show'), false, null, true),
				'order_url' => $this->context->link->getPageLink('order'),
				'enable_update_quantity' => $configures['enable_update_quantity'],
				'show_combination' => $configures['show_combination'],
				'show_customization' => $configures['show_customization'],
				// 'width_cart_item' => $configures['width_cart_item'],
				// 'height_cart_item' => $configures['height_cart_item'],
			);
			$this->context->smarty->assign($templateVars);
			$drop_down_html = $this->module->fetch('module:deotemplate/views/templates/front/feature/content_cart.tpl');
		}
		return $drop_down_html;
	}
}

