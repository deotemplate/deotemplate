/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


$(document).ready(function(){
	let enable_dropdown_defaultcart = deo_variables_ajax_cart.enable_dropdown_defaultcart;
	let type_dropdown_defaultcart = deo_variables_ajax_cart.type_dropdown_defaultcart;
	let enable_dropdown_flycart = deo_variables_ajax_cart.enable_dropdown_flycart;
	let type_dropdown_flycart = deo_variables_ajax_cart.type_dropdown_flycart;
	let enable_overlay_background_flycart = deo_variables_ajax_cart.enable_overlay_background_flycart;
	let show_popup_after_add_to_cart = deo_variables_ajax_cart.show_popup_after_add_to_cart;
	let open_advance_cart_after_add_to_cart = deo_variables_ajax_cart.open_advance_cart_after_add_to_cart;
	let type_effect_flycart = deo_variables_ajax_cart.type_effect_flycart;
	let position_vertical_flycart = deo_variables_ajax_cart.position_vertical_flycart;
	let position_vertical_value_flycart = deo_variables_ajax_cart.position_vertical_value_flycart;
	let position_horizontal_flycart = deo_variables_ajax_cart.position_horizontal_flycart;
	let position_horizontal_value_flycart = deo_variables_ajax_cart.position_horizontal_value_flycart;
	let enable_update_quantity = deo_variables_ajax_cart.enable_update_quantity;
	let show_combination = deo_variables_ajax_cart.show_combination;
	let show_customization = deo_variables_ajax_cart.show_customization;
	let width_cart_item = deo_variables_ajax_cart.width_cart_item;
	let height_cart_item = deo_variables_ajax_cart.height_cart_item;
	let number_cartitem_display = deo_variables_ajax_cart.number_cartitem_display;

	let enable_notification = deo_variables_ajax_cart.enable_notification;
	// let horizontal_position_notification = deo_variables_ajax_cart.horizontal_position_notification;
	// let horizontal_position_value_notification = deo_variables_ajax_cart.horizontal_position_value_notification;
	// let vertical_position_notification = deo_variables_ajax_cart.vertical_position_notification;
	// let vertical_position_value_notification = deo_variables_ajax_cart.vertical_position_value_notification;
	// let width_notification_notification = deo_variables_ajax_cart.width_notification_notification;

	let check_data_outstock = false;
	let notification = {
		success : {
			update : deo_variables_ajax_cart.notification_update_success,
			delete : deo_variables_ajax_cart.notification_delete_success,
			add : deo_variables_ajax_cart.notification_add_success,
		},
		error : {
			update : deo_variables_ajax_cart.notification_update_error,
			delete : deo_variables_ajax_cart.notification_delete_error,
			add : deo_variables_ajax_cart.notification_add_error,
			min : deo_variables_ajax_cart.notification_min_error,
			max : deo_variables_ajax_cart.notification_max_error,
		},
		warning : {
			check : deo_variables_ajax_cart.notification_check_warning,
		}
	}

	createModalAndDropdown(0, 0);
	if (typeof show_popup_after_add_to_cart != 'undefined' && !show_popup_after_add_to_cart){
		$('#cart-block .cart-preview').removeClass('blockcart');
	}
	prestashop.on('updateProductList', function() {
		DeoTemplate.initDeoCartQty();	
	});	
		
	// refresh cart
	prestashop.on('updateCart', function (event) {
		let product_name = false;
		let id_product = event.resp.id_product;
		let id_product_attribute = event.resp.id_product_attribute;
		let btn_add_cart = $('.add-to-cart.active');

		if (typeof event.resp.hasError != 'undefined' && event.resp.hasError){
			DeoTemplate.messageError(event.resp.errors.join('<br>'));
			// remove loading btn cart
			if ($('.deo-btn-cart.active').length){
				$('.deo-btn-cart.active').removeClass('active loading reset');
			}
			return false;
		}else{
			// update quantity for all products
			if (typeof event.resp.success != 'undefined' && event.resp.success){
				let form = $('.add-to-cart[data-id_product="'+id_product+'"][data-id_product_attribute="'+id_product_attribute+'"]').closest('form');
				let quantity_product = form.find('[name="quantity_product"]');
				let qty = 0;
				if (parseInt(quantity_product.val()) - event.resp.quantity){
					qty = parseInt(quantity_product.val()) - event.resp.quantity;
				}

				quantity_product.val(parseInt(qty));
			}
		}

		// $.each(event.resp.cart.products, function(key, value){
		// 	if (id_product == value.id_product){
		// 		product_name = value.name;
		// 		return false;
		// 	}
		// });

		if (btn_add_cart.hasClass('deo-btn-cart')){
			product_name = btn_add_cart.closest('.product-miniature').find('.product-title').text();
		}else{
			if ($('.quickview .product-detail-name').length){
				product_name = $('.quickview').find('.product-detail-name').text();
			}else{
				product_name = $('.product-detail').find('.product-detail-name').text();
			}
		}

		// loading default cart
		if ($('#cart-block .cart-preview .deo-icon-cart-loading').length){
			$('#cart-block .cart-preview').addClass('loading');
		}
		let refresh_url = $('#cart-block .cart-preview').data('refresh-url');
		$('#cart-block .cart-preview').remove();

		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: refresh_url,
			async: true,
			cache: false,										
			success: function (resp){	

				$('#cart-block').append($(resp.preview).find('.cart-preview'));
				if (typeof show_popup_after_add_to_cart != 'undefined' && !show_popup_after_add_to_cart){
					$('#cart-block .cart-preview').removeClass('blockcart');
				}
				// $('#cart-block .cart-preview').replaceWith($(resp.preview).find('.cart-preview'));

				// refresh product page when change cart
				if (prestashop.page.page_name == 'product' || $('.product-add-to-cart .add-to-cart').length){
					prestashop.emit('updateProduct', {
						reason: ''
					});
				}

				// show notification if add cart success
				if (event.reason.linkAction == 'add-to-cart' && event.resp.success){
					// run fly cart
					if (typeof type_effect_flycart != 'undefined' && type_effect_flycart){
						if ($('.deo-btn-cart.active').length){
							// product list
							flyCartEffect($('.deo-btn-cart.active'));
						}else if (prestashop.page.page_name == 'product' || $('.product-add-to-cart .add-to-cart').length){
							// product page or quickview
							flyCartEffect($('.product-add-to-cart .add-to-cart'));
						}
					}
					
					if (typeof enable_notification != 'undefined' && enable_notification){
						// show notification				
						showDeoNotification('success', 'add', product_name);				
					}
				}
					
				// remove loading btn cart
				if ($('.deo-btn-cart.active').length){
					$('.deo-btn-cart.active').removeClass('active loading reset');
				}

				// loading fly cart
				if ($('.deo-cart-solo .deo-icon-cart-loading').length){
					$('.deo-cart-solo').addClass('loading');
				}	
				
				if ($('.cart-item.deleting').length){
					$('.cart-item.deleting').fadeOut(function(){
						$('.cart-item.deleting').remove();
						updateClassCartItem();
					})
					showDeoNotification('success','delete', product_name);
				}
				
				if ($('.cart-item.updating').length){		
					$('.cart-item.updating').removeClass('updating loading');
					showDeoNotification('success','update', product_name);
				}
				
				// remove do not close dropdown-dropup when delete at page cart
				$('.deo-content-cart-wrapper.dropdown').removeClass('disable-close');
				$('.deo-content-cart-wrapper.dropup').removeClass('disable-close');
				createModalAndDropdown(1, 0);
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	});
	
	// call event for fly cart slide bar
	activeEventFlyCartSlideBar();
	$(document).on('click', '.deo-cart-solo.enable-slidebar .icon-cart-sidebar-wrapper', function(){
		let fly_cart_icon = $(this).children('.icon-cart-sidebar');
		showSlideBarCart(fly_cart_icon);
		return false;
	});
	
	$(document).on('click', '.deo-cart-solo.enable-dropdown .icon-cart-sidebar-wrapper, .deo-cart-solo.enable-dropdown .icon-cart-sidebar', function(){		
		showDropDownCart($(this), 'flycart');
		return false;
	});
	
	// click out to close dropdown-dropup cart
	$(document).click(function (e) {
		e.stopPropagation();

		let content_dropdown = $(".deo-content-cart-wrapper.dropdown.show");
		//check if the clicked area is dropDown or not
		if (content_dropdown.length && content_dropdown.has(e.target).length === 0) {
			if (!content_dropdown.hasClass('disable-close')){
				content_dropdown.removeClass('show');
			}
		}

		let content_dropup = $(".deo-content-cart-wrapper.dropup.show");
		//check if the clicked area is dropDown or not
		if (content_dropup.length && content_dropup.has(e.target).length === 0) {
			if (!content_dropup.hasClass('disable-close')){
				content_dropup.removeClass('show');
			}
		}
	})
	
	getOffsetFlycartIcon();
	
	// resize update scroll bar of fly cart slide bar
	$(window).resize(function(){
		// active scroll bar
		$('.list-items').each(function(){
			// scroll bar for slidebar
			if ($(this).parents('.deo-sidebar-cart').length){
				checkFlyCartScrollBar($(this));
			}
			// scroll bar for dropup/dropdown		
			if ($(this).parents('.deo-cart-solo.type-fixed.enable-dropdown').length){
				checkFlyCartScrollBarDropDown($(this));
			}
		})
		getOffsetFlycartIcon();
	});

	$(document).on('change', '.deo-cart-quantity', function(e){
		let product_miniature = $(this).closest('.product-miniature');
		product_miniature.find('.qty_product').val($(this).val());
	});
	

	$('body').on("submit", '.btn-cart-product-list form', (function(e) {
	// $(document).on('submit', '.btn-cart-product-list form', function(e){
		e.preventDefault();
		return false;
	}));


	// event click button add cart
	$('body').on("click", '.deo-btn-cart', (function(e) {
	// $(document).on('click', '.deo-btn-cart', function(e){
		e.preventDefault();
		let $btn = $(this);
		if ($btn.hasClass('active') || $('.deo-btn-cart.active').length || $btn.hasClass('disabled')){
			return false;
		}
		$btn.addClass('active loading');

		let product_miniature = $btn.closest('.product-miniature');
		let id_product = product_miniature.find('.id_product').val();
		let id_product_attribute = product_miniature.find('.id_product_attribute').val();
		let id_customization = product_miniature.find('.id_customization').val();
		let qty_product = product_miniature.find('.qty_product').val();
		let min_qty = product_miniature.find('.minimal_quantity').val();
		let quantity_product = product_miniature.find('.quantity_product').val();
		
		if (Math.floor(qty_product) == qty_product && $.isNumeric(qty_product) && qty_product > 0){
			// return true;
		}else{
			// show notification
			showDeoNotification('error', 'check', false);
			$btn.removeClass('active loading');
			return false;
		}

		// check min quantity
		if (parseInt(qty_product) < parseInt(min_qty) && (ps_stock_management && !ps_order_out_of_stock)){
			showDeoNotification('error', 'min', min_qty);
			setTimeout(function(){
				$btn.removeClass('active loading reset');
			}, 200); 
			return false;
		}

		// check max quantity
		if (parseInt(qty_product) > parseInt(quantity_product) && (ps_stock_management && !ps_order_out_of_stock)){
			showDeoNotification('error', 'max', quantity_product);
			setTimeout(function(){
				$btn.removeClass('active loading reset');
			}, 200);
			return false;
		}
		
		let $form = $btn.closest('form');
		let data = $form.serialize() + '&add=1&action=update';
		let actionURL = $form.attr('action');

		$.post(actionURL, data, null, "json").then(function(e) {
			prestashop.emit("updateCart", {
				reason: {
					idProduct: id_product,
					idProductAttribute: id_product_attribute,
					idCustomization: id_customization,
					linkAction: "add-to-cart",
					cart: e.cart
				},
				resp: e
			})
		}).fail(function(e) {
			prestashop.emit("handleError", {
				eventType: "addProductToCart",
				resp: e
			})
		});

		// $form.submit();
		return true;
	}));

	// event for select combination
	$(document).on('click', '.deo-select-attr', function(e){
		e.preventDefault();
		let product_miniature = $(this).closest('.product-miniature');
		let id_product = product_miniature.data('id-product');
		let attr_txt = $(this).text();
		let id_product_attr = $(this).data('id-attr');
		let parent_e = $(this).parents('.product-miniature');
		
		if (!$(this).hasClass('selected')){
			$(this).siblings().removeClass('selected');
			$(this).addClass('selected');
			parent_e.find('.dropdownListAttrButton_'+id_product).text(attr_txt);

			let deo_more_product_image = product_miniature.find('.deo-more-product-img .list-thumbs');
			let deo_cart_quantity = product_miniature.find('.wrapper-deo-cart-quantity');
			let deo_product_atribute = product_miniature.find('.deo-attr-list-container');
			let deo_product_thumbnail = product_miniature.find('.product-thumbnail');
			let deo_stock = product_miniature.find('.deo-quantity-stock');
			product_miniature.find('.deo-btn-cart').addClass('active loading');
			
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax_cart,
				async: true,
				cache: false,
				data: {
					"action": "get-combination-data",
					"id_product": id_product,
					"id_product_attr": id_product_attr,
					"configures_ajax_cart": deo_variables_ajax_cart,
					"deo_more_product_image": deo_more_product_image.data(),
					"deo_cart_quantity": deo_cart_quantity.data(),
					"deo_product_atribute": deo_product_atribute.data(),
					"deo_product_thumbnail": deo_product_thumbnail.data(),
					"deo_stock": deo_stock.length,
					"token": deo_token
				},
				success: function (result){
					if(result != ''){						
						product_miniature.find('.deo-btn-cart').removeClass('active loading');
						product_miniature.find('.btn-cart-product-list').replaceWith(result.add_to_cart);
						product_miniature.find('.product-thumbnail').replaceWith(result.product_thumbnail);
						product_miniature.find('.product-price-and-shipping').replaceWith(result.product_price_and_shipping);
						product_miniature.find('.deo-quantity-stock').replaceWith(result.stock);
						product_miniature.find('.deo-more-product-img').replaceWith(result.more_image_product);
						product_miniature.find('.wrapper-deo-cart-quantity').replaceWith(result.cart_quantity);
						product_miniature.find('.deo-attr-list-container').replaceWith(result.atribute_list);
						DeoTemplate.initTooltip();
						DeoTemplate.initDeoCartQty();
						DeoTemplate.initMoreProductImg();
					}else{
						DeoTemplate.messageError(add_cart_error);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});

			parent_e.find('.dropdownListAttrButton_'+id_product).trigger('click');
		}
	});

	// event for select attribute
	$(document).on('click', '.deo-attr-item', function(e){
		e.preventDefault();
		let product_miniature = $(this).closest('.product-miniature');
		let id_product = product_miniature.data('id-product');
		let attr_txt = $(this).text();
		let id_product_attr = $(this).data('id-attr');
		let parent_e = $(this).parents('.product-miniature');
		
		if (!$(this).hasClass('selected')){
			$(this).siblings().removeClass('selected');
			$(this).addClass('selected');

			let deo_more_product_image = product_miniature.find('.deo-more-product-img .list-thumbs');
			let deo_cart_quantity = product_miniature.find('.wrapper-deo-cart-quantity');
			// let deo_product_combination = product_miniature.find('.deo-dropdown-select-attr');
			let deo_product_thumbnail = product_miniature.find('.product-thumbnail');
			let deo_product_atribute = product_miniature.find('.deo-attr-list-container');
			let deo_stock = product_miniature.find('.deo-quantity-stock');
			product_miniature.find('.deo-btn-cart').addClass('active loading');

			let group_attributes = {};
			deo_product_atribute.find('.deo-attr-list').each(function(){
				let group_id = $(this).data('group-id');
				group_attributes[group_id] = $(this).find('.deo-attr-item.selected').data('product-attribute');
			});
			
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax_cart,
				async: true,
				cache: false,
				data: {
					"action": "get-attribute-data",
					"id_product": id_product,
					"group": group_attributes,
					"id_product_attr": id_product_attr,
					"configures_ajax_cart": deo_variables_ajax_cart,
					"deo_more_product_image": deo_more_product_image.data(),
					"deo_cart_quantity": deo_cart_quantity.data(),
					"deo_product_thumbnail": deo_product_thumbnail.data(),
					"deo_stock": deo_stock.length,
					"token": deo_token
				},
				success: function (result){
					if(result != ''){						
						product_miniature.find('.deo-btn-cart').removeClass('active loading');
						product_miniature.find('.btn-cart-product-list').replaceWith(result.add_to_cart);
						product_miniature.find('.product-thumbnail').replaceWith(result.product_thumbnail);
						product_miniature.find('.product-price-and-shipping').replaceWith(result.product_price_and_shipping);
						product_miniature.find('.deo-quantity-stock').replaceWith(result.stock);
						product_miniature.find('.deo-more-product-img').replaceWith(result.more_image_product);
						product_miniature.find('.wrapper-deo-cart-quantity').replaceWith(result.cart_quantity);
						product_miniature.find('.deo-dropdown-select-attr').replaceWith(result.combination);
						DeoTemplate.initTooltip();
						DeoTemplate.initDeoCartQty();
						DeoTemplate.initMoreProductImg();
					}else{
						DeoTemplate.messageError(add_cart_error);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
	});



		// remove dropdown cart item
		$(document).on('click', '.remove-cart', function(e){
		// $('.remove-cart').click(function(){
			let id_product = $(this).data('id-product');
			let id_product_attribute = $(this).data('id-product-attribute');
			let id_customization = $(this).data('id-customization');
			 
			let parent_obj = $(this).parents('.cart-item');


			parent_obj.addClass('deleting loading');
			if ($('.remove-from-cart').length){
				// do not close dropdown-dropup when delete at page cart
				$('.deo-content-cart-wrapper.dropdown').addClass('disable-close');
				$('.deo-content-cart-wrapper.dropup').addClass('disable-close');
				$('.remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').trigger('click');
			}else{
				let link_url = $(this).data('link-url');
				let refresh_url = $('#cart-block .cart-preview').data('refresh-url');
				let product_name = $(this).closest('.cart-item').find('.product-name a').html();
				
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: link_url,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "update",					
					},					
					success: function (result){
						let obj = $.parseJSON(result);
						
						if (obj.success){	
							parent_obj.fadeOut(function(){
								parent_obj.remove();
								$('.remove-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').parents('.cart-item').remove();
								updateClassCartItem();						
								
							});

							// show notification
							showDeoNotification('success','delete', product_name);
							
							// refresh cart
							$.ajax({
								type: 'POST',
								headers: {"cache-control": "no-cache"},
								url: refresh_url,
								async: true,
								cache: false,										
								success: function (resp){

									$('#cart-block .cart-preview').replaceWith($(resp.preview).find('.cart-preview'));
									if (typeof show_popup_after_add_to_cart != 'undefined' && !show_popup_after_add_to_cart){
										$('#cart-block .cart-preview').removeClass('blockcart');
									}
									createModalAndDropdown(1, 1, 1);

									// refresh product page when change cart
									if (prestashop.page.page_name == 'product' || $('.product-add-to-cart .add-to-cart').length){
										prestashop.emit('updateProduct', {
											reason: ''
										});
									}
								},
								error: function (XMLHttpRequest, textStatus, errorThrown) {
									console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
								}
							});
							
						}else{
							// show notification
							showDeoNotification('error','delete', false);
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}
			
			return false;
		});
		
		$(document).on('focusout', '.input-product-qty', function(e){
		// $('.input-product-qty').focusout(function(){
			updateQuantityProduct($(this));
		})
		$(document).on('keyup', '.input-product-qty', function(e){
		// $('.input-product-qty').keyup(function(event){
			if (event.keyCode == 13) {
				updateQuantityProduct($(this));
			}
		});
		
		let timer;
		let flag = false;
		
		// change quantity product dropdown with button up-down
		$(document).on('touchstart click', '.btn-qty-down, .btn-qty-up', function(e){
		// $('.btn-qty-down, .btn-qty-up').on('touchstart click', function(){
			if(flag == true){
				flag = false;
				clearTimeout(timer);
			}
			flag = true;
			let action = 'up';
			let input_target = $(this).parents('.cart-item').find('.input-product-qty');
			let product_qty = $(this).parents('.cart-item').find('.qty-number');
			let input_quantity = parseInt(input_target.val());
			let quantity_update;
			if ($(this).hasClass('btn-qty-down')){
				action = 'down';
			}
			
			if (action == 'up'){
				quantity_update = input_quantity+1;
			}
			if (action == 'down'){
				quantity_update = input_quantity-1;
			}
			input_target.val(quantity_update);
			product_qty.html(quantity_update);

			timer = setTimeout(function() {
				flag = false;
				updateQuantityProduct(input_target);
			}, 800);
			
			return false;
		});




	// show dropdown cart
	function showDropDownCart($element, $type){
		let object_element = '';
		if ($type == 'defaultcart'){
			object_element = $element.siblings('.deo-content-cart-wrapper');
		}
		if ($type == 'flycart'){
			object_element = $element.parents('.deo-cart-solo').find('.deo-content-cart-wrapper');
		}
		
		if (!object_element.hasClass('show')){
			object_element.addClass('show');
		}else{
			object_element.removeClass('show');
		}
	}

	// show dropdown cart
	function showSlideBarCart($element){
		if ($('[data-toggle="deo-tooltip"].has-init-tooltip').length){
			$('[data-toggle="deo-tooltip"].has-init-tooltip').tooltip('hide');
		}
		if (!$('.deo-sidebar-cart.disable').length){
			if (!$element.hasClass('active-slidebarcart')){
				let type = $element.data('type');
				$element.addClass('active-slidebarcart');
				
				$('.deo-sidebar-cart.'+type).addClass('active');
				if ($('.deo-cart-mask').length){
					$('body').addClass('active-sidebar-cart');
				}
			}else{
				$('.deo-sidebar-cart .icon-cart-sidebar-wrapper').trigger('click');
			}
		}
	}

	// event for dropdown cart
	function activeDropdownEvent(){
		// active scroll bar
		$('.list-items').each(function(){
			let check_number_cartitem = 3;
			if (typeof number_cartitem_display != 'undefined'){
				check_number_cartitem = number_cartitem_display;
			}
			// scroll bar for dropdown
			if (!$(this).parents('.deo-sidebar-cart').length){			
				checkFlyCartScrollBarDropDown($(this));
			}
			
			// scroll bar for slidebar
			if ($(this).parents('.deo-sidebar-cart').length){
				checkFlyCartScrollBar($(this));
			}
		})
	}

	function updateQuantityProduct($element){
		let $this = $element;
		let product_quantity = $this.data('product-quantity');
		let min_quantity = $this.data('min-quantity');
		let max_quantity = $this.data('quantity-available');
		let input_quantity = $this.val();
		
		// validate input
		if(Math.floor(input_quantity) == input_quantity && $.isNumeric(input_quantity) && input_quantity > 0){
			// return true;
		}else{
			showDeoNotification('warning', 'check', false);
			$this.val(product_quantity);
			return;
		}
		
		// check min quantity
		if (parseInt(input_quantity) < parseInt(min_quantity) && (ps_stock_management && !ps_order_out_of_stock)){
			showDeoNotification('error', 'min', min_quantity);
			$this.val(product_quantity);
			return false;
		}

		// check max quantity
		if (parseInt(input_quantity) > parseInt(max_quantity) && (ps_stock_management && !ps_order_out_of_stock)){
			showDeoNotification('error', 'max', max_quantity);
			$this.val(max_quantity);
			return false;
		}
		
		// do not change
		let qty = parseInt(input_quantity) - parseInt(product_quantity);
		if (qty == 0){
		  return;
		}
		
		let id_product = $this.data('id-product');
		let id_product_attribute = $this.data('id-product-attribute');
		let id_customization = $this.data('id-customization');
		
		$this.removeData('check-outstock');
		
		let check_product_outstock = true;
		let parent_obj = $this.parents('.cart-item');
		parent_obj.addClass('updating loading');
		

		let check_outstock = function(){					
			if (typeof $element.data('check-outstock') != 'undefined'){							
				clearInterval(check_data_outstock);
				if (!$this.data('check-outstock')){
					showDeoNotification('error', 'max', false);
					$this.val(product_quantity);
					check_product_outstock = false;
					parent_obj.removeClass('updating loading');
				}

				if (!check_product_outstock){
					return false;
				}
				
				if ($('.js-cart-line-product-quantity').length){
					// page cart
					let e = $.Event("keyup");
					e.keyCode = 13; // # Some key code value
					$('.remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').parents('.cart-item').find('.js-cart-line-product-quantity').val(input_quantity).trigger(e);	
				}else{				
					let link_url = $this.data('update-url');
					let refresh_url = $('#cart-block .cart-preview').data('refresh-url');
					let op = '';
					if (qty > 0){
						op = 'up';
					}else{
						op = 'down';
					}
					
					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: link_url,
						async: true,
						cache: false,
						data: {
							"ajax": 1,
							"action": "update",	
							"qty": Math.abs(qty),
							"op": op,			
						},					
						success: function (result){
							let obj = $.parseJSON(result);
							parent_obj.removeClass('updating loading');
							
							if(obj.success){
								let product_name = false;
								$.each(obj.cart.products, function(key, value){
									if (id_product == value.id_product){
										product_name = value.name;
										return false;
									}
								});

								$('.input-product-qty[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').val(input_quantity).data('product-quantity', input_quantity);				
								// show notification
								showDeoNotification('success','update', product_name);
								
								// refresh cart
								$.ajax({
									type: 'POST',
									headers: {"cache-control": "no-cache"},
									url: refresh_url,
									async: true,
									cache: false,										
									success: function (resp){
										$('#cart-block .cart-preview').replaceWith($(resp.preview).find('.cart-preview'));
										if (typeof show_popup_after_add_to_cart != 'undefined' && !show_popup_after_add_to_cart){
											$('#cart-block .cart-preview').removeClass('blockcart');
										}
										createModalAndDropdown(1, 1, 1);

										// refresh product page when change cart
										if (prestashop.page.page_name == 'product' || $('.product-add-to-cart .add-to-cart').length){
											prestashop.emit('updateProduct', {
												reason: ''
											});
										}
										
									},
									error: function (XMLHttpRequest, textStatus, errorThrown) {
										console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
									}
								});
							}else{
								// show notification
								showDeoNotification('error','update', false);
							}
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
				}
			}
		};
		checkProductOutStock(id_product, id_product_attribute, id_customization, input_quantity, $this, false, true, check_outstock);
	}

	// action click default cart
	$(document).on('click', '.cart-preview', function(){
		if (enable_dropdown_defaultcart){
			if (type_dropdown_defaultcart == 'dropdown' || type_dropdown_defaultcart == 'dropup'){
				showDropDownCart($(this), 'defaultcart');
			}
			if (type_dropdown_defaultcart == 'slidebar_left' || type_dropdown_defaultcart == 'slidebar_right' || type_dropdown_defaultcart == 'slidebar_top' || type_dropdown_defaultcart == 'slidebar_bottom'){
				showSlideBarCart($(this));
			}
			return false;
		}
	});

	function createModalAndDropdown($only_init_html, $only_content_cart, $update_qty = false){
		// add loading
		if (typeof enable_dropdown_defaultcart != 'undefined'){
			$('#cart-block .cart-preview').data('type', type_dropdown_defaultcart);
			if (enable_dropdown_defaultcart){
				if ($('#cart-block .cart-preview').length){
					$('#cart-block .cart-preview').addClass('loading');
				}
			}
		}
		
		// loading fly cart 
		if ($('.deo-cart-solo .deo-icon-cart-loading').length){
			$('.deo-cart-solo').removeClass('loading');
		}

		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: deo_url_ajax_cart,
			async: true,
			cache: false,
			data: {
				"action": "render-modal",
				"only_init_html": $only_init_html,
				"only_content_cart": $only_content_cart,
				"configures_ajax_cart": deo_variables_ajax_cart,
				"token": deo_token,
			},
			success: function (result){
				if (result != ''){
					$('#cart-block .cart-preview').removeClass('loading');
					$('#cart-block .cart-preview').data('type', type_dropdown_defaultcart);
					if ($('.deo-cart-solo .deo-icon-cart-loading').length){
						setTimeout(function(){
							$('.deo-cart-solo').removeClass('loading');
						}, 2000); 
					}

					// create modal popup
					if (result.modal != ''){						
						$('body').append(result.modal);
					}
					// create notification
					if (result.notification != ''){						
						$('body').append(result.notification);								
					}

					// create fly cart
					if (result.flycart != ''){						
						$('body').append(result.flycart);								
					}

					if (result.contentcart != ''){
						if ($('.deo-sidebar-cart.disable').length){
							$('.deo-sidebar-cart').removeClass('disable');
						}
						if ($('.deo-content-cart-wrapper').length == 0){
							if (typeof type_dropdown_defaultcart != 'undefined' && (type_dropdown_defaultcart == 'dropdown' || type_dropdown_defaultcart == 'dropup')){
								$('#cart-block .cart-preview').after('<div class="deo-content-cart-wrapper defaultcart '+type_dropdown_defaultcart+'"></div>');
							}				
							
							// add dropdown to flycart
							if ($('.deo-cart-solo.enable-dropdown').length){
								$('.deo-cart-solo.enable-dropdown').append('<div class="deo-content-cart-wrapper flycart '+$('.deo-cart-solo.enable-dropdown').data('type')+'"></div>');
							}
							
							// add dropdown to flycart slide bar
							if ($('.deo-sidebar-cart').length){
								$('.deo-sidebar-cart').append('<div class="deo-content-cart-wrapper"></div>');
							}
						}else{
							$('.deo-content-cart-wrapper').addClass('update');
						}
						
						if ($('.deo-content-cart').length){
							if ($only_content_cart == 1){
								$('.deo-content-cart .cart-total').replaceWith(result.contentcart);
								let check_number_cartitem = 3;
								if (typeof number_cartitem_display != 'undefined'){
									check_number_cartitem = number_cartitem_display;
								}
								// turn off scroll bar
								$('.list-items').each(function(){
									if (!$(this).parents('.deo-sidebar-cart').length){
										checkFlyCartScrollBarDropDown($(this));
									}

									// scroll bar for slidebar
									if ($(this).parents('.deo-sidebar-cart').length && $(this).parents('.deo-sidebar-cart').find('.active-scrollbar')){
										checkFlyCartScrollBar($(this));
									}
								})						
							}else{
								$('.deo-content-cart').replaceWith(result.contentcart);
								activeDropdownEvent();
							}
						}else{
							$('.deo-content-cart-wrapper').append(result.contentcart);
							activeDropdownEvent();
						}
					}else{
						// clear cart
						if ($('.deo-content-cart-wrapper').length){
							$('.deo-content-cart-wrapper').remove();
						}
						$('.deo-sidebar-cart').addClass('disable');
						if ($('.deo-sidebar-cart.active').length){
							$('.deo-sidebar-cart.active').find('.icon-cart-sidebar-wrapper').trigger('click');
						}
					}
					
					// update cart total for fly cart
					if ($('.icon-cart-total').length){
						if ($('.cart-total').length){
							$('.icon-cart-total').text($('.cart-total').data('cart-total'));
						}else{
							$('.icon-cart-total').text("0");
						}	
					}else{
						$('.icon-cart-total').text($(result.contentcart).find('.cart-total').data('cart-total'));
					}

					if ($('.DeoCartClone').length){
						$('.DeoCartClone .deo-cart-solo,.DeoCartClone .deo-cart-solo .icon-cart-sidebar').data('type',type_dropdown_flycart);
						// ($formAtts.type_dropdown == 'dropup' || $formAtts.type_dropdown == 'dropdown') ? ' enable-dropdown' : ''
						// ($formAtts.type_dropdown == 'slidebar_top' || $formAtts.type_dropdown == 'slidebar_bottom' || $formAtts.type_dropdown == 'slidebar_right' || $formAtts.type_dropdown == 'slidebar_left') ? ' enable-slidebar' : ''
						if (type_dropdown_flycart == 'dropup' || type_dropdown_flycart == 'dropdown'){
							$('.DeoCartClone .deo-cart-solo').addClass('enable-dropdown');
						}
						if (type_dropdown_flycart == 'slidebar_top' || type_dropdown_flycart == 'slidebar_bottom' || type_dropdown_flycart == 'slidebar_right' || type_dropdown_flycart == 'slidebar_left'){
							$('.DeoCartClone .deo-cart-solo').addClass('enable-slidebar');
						}
					}

					// show advance_cart after add to cart
					if ($only_init_html && !$update_qty && typeof open_advance_cart_after_add_to_cart != 'undefined' && open_advance_cart_after_add_to_cart && !$update_qty){
						setTimeout(function() {
							$('.deo-cart-solo.enable-slidebar .icon-cart-sidebar-wrapper, .deo-cart-solo.enable-dropdown .icon-cart-sidebar').trigger('click');
						}, 100);
					}
				}else{
					DeoTemplate.messageWarning(add_cart_error);
				}		
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}

	// event for notification
	function activeEventNotification(){
		$(".deo-notification .notification").click(function(){
			$(this).removeClass('show').addClass("closed").parent().addClass('disable');
		})
	}

	function showDeoNotification($status, $action, $special_parameter){
		// if (!$('.deo-notification').hasClass('active')){
		// 	$('.deo-notification').addClass('active');
		// }
		// let clone_obj = '';
		
		// clone_obj = $('.deo-temp-'+$status+'>div').clone();
		// clone_obj.find('.noti-'+$action).addClass('active');
		// if ($special_parameter && $special_parameter != ''){
		// 	clone_obj.find('.noti-'+$action).find('.noti-special').text($special_parameter);
		// }
		// $('.deo-notification').append(clone_obj);
		// setTimeout(function(){
		// 	clone_obj.find('.notification').addClass('show');
		// }, 100);
		
		// activeEventNotification();
		// setTimeout(function() {
		// 	clone_obj.find('.notification').removeClass('show').addClass("closed").parent().addClass('disable');
		// }, 5000);

		let message = $('<span>').html(notification[$status][$action]);
		if ($special_parameter && $special_parameter != ''){
			message.find('.deo-special').html($special_parameter);
		}
		message = message.html();
		if ($status == "success"){
			DeoTemplate.messageSuccess(message);
		}else if ($status == "warning"){
			DeoTemplate.messageWarning(message);
		}else if ($status == "error"){
			DeoTemplate.messageError(message);
		}
	}

	// check product out stock
	function checkProductOutStock($id_product, $id_product_attribute, $id_customization, $quantity, $element, $check_product_in_cart, update_qty, callback){
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: deo_url_ajax_cart,
			async: true,
			cache: false,
			data: {
				"action": "check-product-outstock",
				"id_product": $id_product,
				"id_product_attribute": $id_product_attribute,
				"id_customization": $id_customization,
				"quantity": $quantity,
				"check_product_in_cart": $check_product_in_cart,
				"configures_ajax_cart": deo_variables_ajax_cart,
				"token": deo_token
			},
			success: function (result){
				if (result != ''){
					let result_check = (typeof result.success != 'undefined') ? result.success : false;
					$element.data('check-outstock', result_check);
					if (!update_qty){
						// clearInterval(check_data_outstock);
					}
					check_data_outstock = true;	
					if (typeof callback != 'undefined' && check_data_outstock){
						callback();
					}
				}else{
					DeoTemplate.messageError(add_cart_error);
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}

	// update class first last of cart item
	function updateClassCartItem(){
		$('.list-items').each(function(){
			$(this).find('.cart-item').first().addClass('first');
			$(this).find('.cart-item').last().addClass('last');
		})
	}

	// create fly cart effect
	function flyCartEffect($element){
		let product_img = '';
		if ($element.hasClass('deo-btn-cart')){
			let parent_e = $element.parents('.product-miniature');
			product_img = parent_e.find('.product-thumbnail').find("img").eq(0);
		}else{
			if ($('.quickview .product-cover').length){
				// quickview in produc page
				product_img = $('.quickview .product-cover').find("img").eq(0);
			}else{
				// product page
				product_img = $('.product-cover').find("img").eq(0);
			}	
		}
		if (typeof product_img != '' && product_img.length){		
			let obj_element = '';
			if ($('.deo-sidebar-cart.active .icon-cart-sidebar').length){
				obj_element = $('.deo-sidebar-cart.active .icon-cart-sidebar');
			}else if ($('.deo-cart-solo.solo .icon-cart-sidebar').length){
				obj_element = $('.deo-cart-solo.solo .icon-cart-sidebar');
			}else if ($('#cart-block .cart-preview').length){
				obj_element = $('#cart-block .cart-preview');
			}else if ($('.blockcart.cart-preview').length){
				obj_element = $('.blockcart.cart-preview');
			}
			$('body').addClass('enable-deo-cart-solo');
			let divider = 4;
			let flyerClone = product_img.clone();
			
			let clone_offset_top = product_img.offset().top;
			let clone_offset_left = product_img.offset().left;
			// update for body boxed
			if ($('body').offset().left != 0){
				clone_offset_left = clone_offset_left - $('body').offset().left;
			}
			flyerClone.css({position: 'absolute', top: clone_offset_top + "px", left: clone_offset_left + "px", opacity: 1, 'z-index': 1000, width: product_img.width(), height: product_img.height()});
			
			$('body').append(flyerClone);
			let gotoX = obj_element.offset().left + (obj_element.width() / 2) - (product_img.width()/divider)/2;
			// update for body boxed
			if ($('body').offset().left != 0){
				gotoX = gotoX - $('body').offset().left;
			}
			let gotoY = obj_element.offset().top + (obj_element.height() / 2) - (product_img.height()/divider)/2;
			 
			flyerClone.animate({
				opacity: 0.4,
				left: gotoX,
				top: gotoY,
				width: product_img.width()/divider,
				height: product_img.height()/divider
			}, 1000,
			function (){		
				flyerClone.fadeOut('fast', function () {
					flyerClone.remove();
					$('body').removeClass('enable-deo-cart-solo');
				});
				obj_element.fadeOut('fast', function () {
					obj_element.fadeIn('fast', function () {
					});
				});
			});
		}
	}

	// event for fly cart slidebar
	function activeEventFlyCartSlideBar(){
		$(document).on('click', '.deo-cart-mask, .deo-sidebar-cart .icon-cart-sidebar-wrapper, .deo-sidebar-cart .deo-cart-solo, .deo-sidebar-cart .close-sidebar-cart', function(){
			$('body').removeClass('active-sidebar-cart');
			$('.icon-cart-sidebar.active-slidebarcart').removeClass('active-slidebarcart');
			$('#cart-block .cart-preview.active-slidebarcart').removeClass('active-slidebarcart');	
			$('.deo-sidebar-cart.active').removeClass('active');
		});
		
		// press esc
		$(document).keyup(function(e) {
			if (e.keyCode == 27) { 
				$('.deo-sidebar-cart .icon-cart-sidebar-wrapper').trigger('click');
			}
		});
	}

	// update scroll bar for slidebar cart
	function checkFlyCartScrollBar($element){
		let object_parent = $element.parents('.deo-sidebar-cart');
		if (object_parent.hasClass('slidebar_top') || object_parent.hasClass('slidebar_bottom')){
			let width_cart_total = object_parent.find('.cart-total-wrapper').outerWidth();
			let window_width = $('body').outerWidth();		
			let element_width = '';

			if (typeof width_cart_item != 'undefined'){
				element_width = $element.find('.cart-item').length * width_cart_item;
			}else{
				element_width = $element.find('.cart-item').length * $element.find('.cart-item').outerWidth();
			}

			if ((element_width+width_cart_total) > window_width){
				let width_list_wrapper = window_width-width_cart_total;
				width_list_wrapper = ((window_width-width_cart_total) == 0) ? '100%' : width_list_wrapper;
				object_parent.addClass('active-scroll');
				object_parent.find('.list-cart-item-warpper').addClass('active-scrollbar');
				object_parent.find('.list-cart-item-warpper').width(width_list_wrapper);
				object_parent.find('.list-cart-item-warpper').mCustomScrollbar({
					theme:"dark",
					axis: "x",
					scrollInertia: 200,
					callbacks:{
						onInit:function(){
						  
						}
					},
					advanced:{
						autoExpandHorizontalScroll:true
					},
					keyboard:{
						enable:true,
					}
				});
				object_parent.find('.list-cart-item-warpper').mCustomScrollbar('update');
			}else{
				object_parent.removeClass('active-scroll');
				object_parent.find('.list-cart-item-warpper').removeClass("active-scrollbar").css({'width': 'auto'});
				object_parent.find('.list-cart-item-warpper').mCustomScrollbar("destroy");
			}
		}
		
		if (object_parent.hasClass('slidebar_left') || object_parent.hasClass('slidebar_right')){
			let height_bottom = ($('.cart-sidebar-heading').length) ? object_parent.find('.cart-sidebar-heading').outerHeight() + object_parent.find('.cart-total-wrapper').outerHeight() : object_parent.find('.cart-total-wrapper').outerHeight();
			let window_height = $(window).height();		
			let element_height = '';
			if (typeof height_cart_item != 'undefined'){
				element_height = $element.find('.cart-item').length * height_cart_item;
			}else{
				element_height = $element.find('.cart-item').length * $element.find('.cart-item').outerHeight();
			}
			if (element_height+height_bottom > window_height){		
				object_parent.addClass('active-scroll');
				$element.addClass('active-scrollbar');
				$element.css({'max-height': window_height-height_bottom});
				$element.mCustomScrollbar({
					theme:"dark",
					scrollInertia: 200,
					callbacks:{
						onInit:function(){
						  
						}
					},
					keyboard:{
						enable:true,
					}

				});
				$element.mCustomScrollbar('update');
			}else{		
				object_parent.removeClass('active-scroll');
				$element.removeClass("active-scrollbar").css({'max-height': 'none'});
				$element.mCustomScrollbar("destroy");
			}
		}
	}

	// update scroll bar for dropdown/dropup
	function checkFlyCartScrollBarDropDown($element){
		let object_parent = $element.parents('.deo-cart-solo.type-fixed.enable-dropdown');
		let type = object_parent.data('type');
		let height_bottom = ($('.cart-sidebar-heading').length) ? object_parent.find('.cart-sidebar-heading').outerHeight() + object_parent.find('.cart-total-wrapper').outerHeight() : object_parent.find('.cart-total-wrapper').outerHeight();
		
		let height_real = height_cart_item*$element.find('.cart-item').length;
		let height_icon = object_parent.find('.icon-cart-sidebar-wrapper').outerHeight();
		let window_height = $(window).height();
		let check_number_cartitem = 3;
		if (typeof number_cartitem_display != 'undefined'){
			check_number_cartitem = number_cartitem_display;
		}
		let height_default = height_cart_item*number_cartitem_display;
		
		if (object_parent.length > 0 && type == 'dropup' && height_real+height_bottom > object_parent.position().top && height_default+height_bottom > object_parent.position().top){
			$element.addClass('active-scrollbar').css({'max-height': object_parent.position().top-height_icon-height_bottom});
			$element.mCustomScrollbar({
				theme:"dark",
				scrollInertia: 200,
				callbacks:{
					onInit:function(){
					  
					}
				},
				keyboard:{
					enable:true,					
				}

			});
			$element.mCustomScrollbar('update');
		}else if (object_parent.length > 0 && type == 'dropdown' && height_real+height_bottom > $(window).height()-object_parent.position().top && height_default+height_bottom > $(window).height()-object_parent.position().top){
			$element.addClass('active-scrollbar').css({'max-height': $(window).height()-object_parent.position().top-height_icon-height_bottom});
			$element.mCustomScrollbar({
				theme:"dark",
				scrollInertia: 200,
				callbacks:{
					onInit:function(){
					  
					}
				},
				keyboard:{
					enable:true,					
				}

			});
			$element.mCustomScrollbar('update');
		}else if ($element.find('.cart-item').length > check_number_cartitem){
			if (typeof height_cart_item != 'undefined'){
				$element.addClass('active-scrollbar').css({'max-height': height_cart_item*number_cartitem_display});
			}else{
				$element.addClass('active-scrollbar').css({'max-height': $element.find('.cart-item').outerHeight()*check_number_cartitem});
			}
			
			$element.mCustomScrollbar({
				theme:"dark",
				scrollInertia: 200,
				callbacks:{
					onInit:function(){
					  
					}
				},
				keyboard:{
					enable:true,			
				}
			});
		}else{
			$element.removeClass("active-scrollbar").css({'max-height': 'none'});
			$element.mCustomScrollbar("destroy");
		}
	}

	// set class by position of fly cart icon
	function getOffsetFlycartIcon(){
		if ($('.deo-cart-solo.solo .icon-cart-sidebar').length){
			let offset_top = $('.deo-cart-solo.solo .icon-cart-sidebar').offset().top;
			let offset_left = $('.deo-cart-solo.solo .icon-cart-sidebar').offset().left;
			let window_width = $(window).width();
			
			if (offset_left <= window_width/2){
				$('.deo-cart-solo.solo').removeClass('offset-right').addClass('offset-left');
			}else{
				$('.deo-cart-solo.solo').removeClass('offset-left').addClass('offset-right');
			}
			
			// update rtl
			if (prestashop.language.is_rtl == 1){			
				if ($('.deo-cart-solo.solo').hasClass('offset-right')){
					$('.deo-cart-solo.solo').removeClass('offset-right').addClass('offset-left');
				}else if ($('.deo-cart-solo.solo').hasClass('offset-left')){
					$('.deo-cart-solo.solo').removeClass('offset-left').addClass('offset-right');
				}
			}
		}
	}

	// show modal popup cart
	function showModalPopupCart(modal){
		if ($('#blockcart-modal').length){
			$('#blockcart-modal').remove();
		}
		$('body').append(modal);
		$('#blockcart-modal').modal('show');	
	}
});