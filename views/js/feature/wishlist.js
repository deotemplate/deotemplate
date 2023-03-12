/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function(){
	let wishlist_url = deo_variables_wishlist.wishlist_url;
	let wishlist_add = deo_variables_wishlist.wishlist_add;
	let wishlist_view_wishlist = deo_variables_wishlist.wishlist_view_wishlist;
	let wishlist_remove = deo_variables_wishlist.wishlist_remove;
	let buttonwishlist_title_add = deo_variables_wishlist.buttonwishlist_title_add;
	let buttonwishlist_title_remove = deo_variables_wishlist.buttonwishlist_title_remove;
	let wishlist_loggin_required = deo_variables_wishlist.wishlist_loggin_required;
	let isLogged = deo_variables_wishlist.isLogged;
	let wishlist_quantity_required = deo_variables_wishlist.wishlist_quantity_required;

	DeoListWishlistProductModalAction();
	DeoWishlistButtonAction();
	prestashop.on('updateProductList', function() {
		DeoWishlistButtonAction();
	});
	// recall button action if need when change attribute at product page
	prestashop.on('updatedProduct', function() {  
		DeoWishlistButtonAction();
	});
	prestashop.on('clickQuickView', function() {		
		check_active_wishlist = setInterval(function(){
			if($('.quickview.modal').length){			
				$('.quickview.modal').on('shown.bs.modal', function (e) {
					DeoWishlistButtonAction();
				})
				clearInterval(check_active_wishlist);
			}
		}, 300);
	});
	
	activeEventModalWishlist();
	DeoListWishlistAction();
	$('.deo-save-wishlist-btn').click(function(){
		if (!$(this).hasClass('active')){
			$(this).addClass('active');
			let name_wishlist = $.trim($('#wishlist_name').val());
			if (!name_wishlist){
				DeoTemplate.messageWarning(deo_msg_empty_wishlist_name);
				$(this).removeClass('active');
			}else{
				let object_e = $(this);
				$(this).addClass('deo-loading-button');
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_wishlist,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "add-wishlist",
						"name_wishlist": name_wishlist,
						"token": deo_token
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						if (object_result.errors.length){
							DeoTemplate.messageError(object_result.errors);
						}else{
							$('#wishlist_name').val('');
							DeoTemplate.messageSuccess(object_result.result.message);
							
							$('.deo-table-list-wishlist table tbody').prepend(object_result.result.wishlist);
							$('html, body').animate({
								scrollTop: $('.deo-table-list-wishlist table tr.new').offset().top
							}, 500, function (){
								$('.deo-table-list-wishlist table tr.new').removeClass('new');
							});
							DeoListWishlistAction();
							// reload list product if a wishlist current view
							// $('.deo-table-list-wishlist tr.show .view-wishlist-product').trigger('click');
						}
						object_e.removeClass('active deo-loading-button');
						$('.deo-wishlist-product').hide();
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}
		}
		return false;
	});

	function DeoWishlistButtonAction(){
		if ($('.deo-wishlist-button').hasClass('show-list')){
			// add/remove wishlist with list wishlist
			$('.wishlist-item').click(function(){
				if ($(this).siblings('.added').length == 0){		
					let id_product = $(this).data('id-product');
					let id_wishlist = $(this).data('id-wishlist');
					let id_product_attribute = $(this).data('id-product-attribute');
					let content_wishlist_mess_remove = wishlist_remove+'. <a href="'+deo_url_ajax_wishlist+'" target="_blank" class="deo-special"><strong>'+wishlist_view_wishlist+'.</strong></a>';
					let content_wishlist_mess_add = wishlist_add+'. <a href="'+deo_url_ajax_wishlist+'" target="_blank" class="deo-special"><strong>'+wishlist_view_wishlist+'.</strong></a>';			
					let btn_wishlist = $('.deo-wishlist-button[data-id-product='+id_product+']');

					if (!isLogged){
						// display quicklogin if enable
						DeoTemplate.messageWarning(wishlist_loggin_required);
						
						return true;
					}
					
					let object_e = $(this);
					let parents_e = object_e.parents('.deo-wishlist-button-dropdown');
					parents_e.find('.deo-wishlist-button').addClass('active');
					if ($(this).hasClass('added') || $(this).hasClass('delete')){
						// remove product form wishlist				
						$.ajax({
							type: 'POST',
							headers: {"cache-control": "no-cache"},
							url: deo_url_ajax_wishlist,
							async: true,
							cache: false,
							data: {
								"ajax": 1,
								"action": "remove",
								"id_product": id_product,
								"id_wishlist": id_wishlist,
								"id_product_attribute": id_product_attribute,
								"quantity": 1,
								"token": deo_token
							},
							beforeSend: function (){
								btn_wishlist.addClass('loading');
							},
							success: function (result){
								let object_result = $.parseJSON(result);
								if (object_result.errors.length){
									DeoTemplate.messageError(object_result.errors);
								}else{
									// update number product on icon wishlist after remove from wishlist								
									if ($('.deo-btn-wishlist .deo-total-wishlist').length){								
										let old_num_wishlist = parseInt($('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total'));
										let new_num_wishlist = old_num_wishlist-1;
										$('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total',new_num_wishlist);
										$('.deo-btn-wishlist .deo-total-wishlist').text(new_num_wishlist);
									}
									
									if (object_e.hasClass('delete')){
										// remove from page wishlist
										$('td.product-'+id_product).fadeOut(function(){
											$(this).remove();
										});
									}else{
										// remove from page product list
										DeoTemplate.messageSuccess(content_wishlist_mess_remove);
										
										$('.wishlist-item[data-id-wishlist='+id_wishlist+'][data-id-product='+id_product+']').removeClass('added');
										$('.wishlist-item[data-id-wishlist='+id_wishlist+'][data-id-product='+id_product+']').attr('title',buttonwishlist_title_add);
										if (!$('.wishlist-item[data-id-product='+id_product+']').hasClass('added')){
											btn_wishlist.removeClass('added');
										}
										
										parents_e.find('.deo-wishlist-button').removeClass('active');
									}
								}
								btn_wishlist.removeClass('loading');
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
							}
						});
					}else{
						// add product to list product wishlist
						$.ajax({
							type: 'POST',
							headers: {"cache-control": "no-cache"},
							url: deo_url_ajax_wishlist,
							async: true,
							cache: false,
							data: {
								"ajax": 1,
								"action": "add",
								"id_product": id_product,
								"id_wishlist": id_wishlist,
								"id_product_attribute": id_product_attribute,
								"quantity": 1,
								"token": deo_token
							},
							beforeSend: function (){
								btn_wishlist.addClass('loading');
							},
							success: function (result){
								// console.log(result);
								let object_result = $.parseJSON(result);
								if (object_result.errors.length){
									DeoTemplate.messageError(object_result.errors);
								}else{
									DeoTemplate.messageSuccess(content_wishlist_mess_add);
									// update number product on icon wishlist after add from wishlist								
									if ($('.deo-btn-wishlist .deo-total-wishlist').length){								
										let old_num_wishlist = parseInt($('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total'));
										let new_num_wishlist = old_num_wishlist+1;
										$('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total',new_num_wishlist);
										$('.deo-btn-wishlist .deo-total-wishlist').text(new_num_wishlist);
									}
									
									// console.log(object_result.result.id_wishlist);
						
									$('.wishlist-item[data-id-wishlist='+id_wishlist+'][data-id-product='+id_product+']').addClass('added');
									$('.wishlist-item[data-id-wishlist='+id_wishlist+'][data-id-product='+id_product+']').attr('title',buttonwishlist_title_remove);
									if (!btn_wishlist.hasClass('added')){
										btn_wishlist.addClass('added');
									}
									
									parents_e.find('.deo-wishlist-button').removeClass('active');
								}	
								btn_wishlist.removeClass('loading');									
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
							}
						});										
					}
				}
			});
		}else{
			$('.deo-wishlist-button').click(function(){
				if (!$(this).hasClass('active').length){			
					let id_product = $(this).data('id-product');
					let id_wishlist = $(this).data('id-wishlist');
					let id_product_attribute = $(this).data('id-product-attribute');
					let content_wishlist_mess_remove = wishlist_remove+'. <a href="'+deo_url_ajax_wishlist+'" target="_blank" class="deo-special"><strong>'+wishlist_view_wishlist+'.</strong></a>';
					let content_wishlist_mess_add = wishlist_add+'. <a href="'+deo_url_ajax_wishlist+'" target="_blank" class="deo-special"><strong>'+wishlist_view_wishlist+'.</strong></a>';			
					
					$(this).addClass('active');
					
					if (!isLogged){
						// display quicklogin
						DeoTemplate.messageWarning(wishlist_loggin_required);
					
						return true;
					}
					
					let object_e = $(this);
					let btn_wishlist = $('.deo-wishlist-button[data-id-product='+id_product+']');
					if ($(this).hasClass('added') || $(this).hasClass('delete')){
						// remove product form wishlist				
						$.ajax({
							type: 'POST',
							headers: {"cache-control": "no-cache"},
							url: deo_url_ajax_wishlist,
							async: true,
							cache: false,
							data: {
								"ajax": 1,
								"action": "remove",
								"id_product": id_product,
								"id_wishlist": id_wishlist,
								"id_product_attribute": id_product_attribute,
								"quantity": 1,
								"token": deo_token
							},
							beforeSend: function (){
								btn_wishlist.addClass('loading');
							},
							success: function (result){
								let object_result = $.parseJSON(result);
								if (object_result.errors.length){
									DeoTemplate.messageError(object_result.errors);
								}else{
									// update number product on icon wishlist after remove from wishlist								
									if ($('.deo-btn-wishlist .deo-total-wishlist').length){								
										let old_num_wishlist = parseInt($('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total'));
										let new_num_wishlist = old_num_wishlist-1;
										$('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total',new_num_wishlist);
										$('.deo-btn-wishlist .deo-total-wishlist').text(new_num_wishlist);
									}
									
									if (object_e.hasClass('delete')){
										// remove from page wishlist
										$('td.product-'+id_product).fadeOut(function(){
											$(this).remove();
										});
									}else{
										// remove from page product list
										DeoTemplate.messageSuccess(content_wishlist_mess_remove);
										btn_wishlist.removeClass('added');
										btn_wishlist.attr('title',buttonwishlist_title_add);
									}
								}
								btn_wishlist.removeClass('loading');
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
							}
						});
					}else{
						// add product to list product wishlist
						$.ajax({
							type: 'POST',
							headers: {"cache-control": "no-cache"},
							url: deo_url_ajax_wishlist,
							async: true,
							cache: false,
							data: {
								"ajax": 1,
								"action": "add",
								"id_product": id_product,
								"id_wishlist": id_wishlist,
								"id_product_attribute": id_product_attribute,
								"quantity": 1,
								"token": deo_token
							},
							beforeSend: function (){
								btn_wishlist.addClass('loading');
							},
							success: function (result){
								let object_result = $.parseJSON(result);
								if (object_result.errors.length){
									DeoTemplate.messageError(object_result.errors);
								}else{
									DeoTemplate.messageSuccess(content_wishlist_mess_add);
									// update number product on icon wishlist after add from wishlist								
									if ($('.deo-btn-wishlist .deo-total-wishlist').length){								
										let old_num_wishlist = parseInt($('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total'));
										let new_num_wishlist = old_num_wishlist+1;
										$('.deo-btn-wishlist .deo-total-wishlist').data('wishlist-total',new_num_wishlist);
										$('.deo-btn-wishlist .deo-total-wishlist').text(new_num_wishlist);
									}
									
									// update id wishlist if the first add of user
									if (id_wishlist == ''){
										$('.deo-wishlist-button').data('id-wishlist', object_result.result.id_wishlist);
									}
									
									btn_wishlist.addClass('added');
									btn_wishlist.attr('title',buttonwishlist_title_remove);
								}
								btn_wishlist.removeClass('loading');									
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
							}
						});										
					}
				};
			});
		}
	}

	function DeoListWishlistAction(){
		// click delete wishlist
		$('.delete-wishlist').click(function(){
			if (!$(this).hasClass('active')){
				$(this).addClass('active');
				$(this).parents('tr').addClass('active');
				$('.deo-modal-wishlist .modal-title').html($('.deo-table-list-wishlist tr.active .view-wishlist-product').data('name-wishlist'));
				if ($('.deo-table-list-wishlist tr.active .default-wishlist').is(":checked")){
					$('.deo-modal-wishlist .available').hide();
					$('.deo-modal-wishlist .not-available').show();
					$('.deo-modal-wishlist').removeClass('enable-action').modal('show');
				}else{
					$('.deo-modal-wishlist .available').show();
					$('.deo-modal-wishlist .not-available').hide();
					$('.deo-modal-wishlist').addClass('enable-action').modal('show');
				}
			}
			
			return false;
		});
		
		// confirm delete wishlist
		$('.deo-modal-wishlist-btn').click(function(){
			if (!$(this).hasClass('active')){
				$(this).addClass('active deo-loading-button');
				let object_e = $(this);
				let id_wishlist = $('.delete-wishlist.active').data('id-wishlist');
				
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_wishlist,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "delete-wishlist",
						"id_wishlist": id_wishlist,	
						"token": deo_token	
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						if (object_result.errors.length){
							DeoTemplate.messageError(object_result.errors);
						}else{				
							let object_delete = $('.deo-table-list-wishlist tr.active');
							$('.deo-modal-wishlist').modal('hide');
							object_delete.fadeOut(function(){
								if ($(this).hasClass('show')){
									$('.deo-wishlist-product').fadeOut().html('');
								}else{
									// reload list product if a wishlist current view
									$('.deo-table-list-wishlist tr.show .view-wishlist-product').trigger('click');
								}
								$(this).remove();
							});
							DeoTemplate.messageSuccess(object_result.result);
						}
						object_e.removeClass('active deo-loading-button');
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}
		});
		
		// change default wishlist
		$('.default-wishlist').click(function(){
			if ($(this).is(":checked")){
				if (!$('.default-wishlist.active').length){
					$(this).addClass('active');
					let object_e = $(this);
					let id_wishlist = $('.default-wishlist.active').data('id-wishlist');
					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: deo_url_ajax_wishlist,
						async: true,
						cache: false,
						data: {
							"ajax": 1,
							"action": "default-wishlist",
							"id_wishlist": id_wishlist,	
							"token": deo_token	
						},
						success: function (result){
							let object_result = $.parseJSON(result);
							if (object_result.errors.length){
								DeoTemplate.messageError(object_result.errors);
							}else{				
								$('.default-wishlist:checked').removeAttr('checked');
								object_e.prop('checked', true);
							}
							
							object_e.removeClass('active');			
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
				}
			}

			return false;
		});
		
		// show list product of wishlist
		$('.view-wishlist-product').click(function(){
			if (!$('.view-wishlist-product.active').length){
				$(this).addClass('active deo-loading-button');
				$('.deo-table-list-wishlist tr.show').removeClass('show');
				$(this).parents('tr').addClass('show');
				let object_e = $(this);
				let id_wishlist = $(this).data('id-wishlist');		
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_wishlist,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "show-wishlist-product",
						"id_wishlist": id_wishlist,
						"token": deo_token
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						if (object_result.errors.length){
							DeoTemplate.messageError(object_result.errors);
						}else{				
							$('.deo-wishlist-product').hide();
							$('.deo-wishlist-product').html(object_result.result.html).fadeIn();
							if (object_result.result.show_send_wishlist){
								$('.send-wishlist').fadeIn();					
								DeoListWishlistProductAction();
							}else{
								$('.send-wishlist').hide();
							}
							refeshWishlist(id_wishlist);
							DeoTemplate.initDeoCartQty();
						}
						
						object_e.removeClass('active deo-loading-button');		
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}
			return false;
		})
	}

	function DeoListWishlistProductModalAction(){
		$('.deo-modal-send-wishlist').on('hidden.bs.modal', function(e) { 
			$('.form-send-wishlist')[0].reset();
			$('.wishlist_email').removeClass('has-error');
			let object_parent_e = $('.wishlist_email').parents('.col-form-input');
			object_parent_e.removeClass('has-icon-input email-not-valid sending-wishlist send-wishlist-error send-wishlist-success');
		});

		// send wishlist
		$('.deo-send-wishlist-button').click(function(){
			let name_wishlist = $('.deo-table-list-wishlist tr.show .view-wishlist-product').data('name-wishlist');
			$('.deo-modal-send-wishlist .modal-title').html(name_wishlist);
			console.log('aaa');
			$('.deo-modal-send-wishlist').modal('show');
			
			return false;
		});

		$('.deo-modal-send-wishlist-btn').click(function(){
			$('.form-send-wishlist').submit();
		});
		
		// valiate email
		$('.wishlist_email').keyup(function(){
			if ($(this).val() !== '' && !$(this).parents('.col-form-input').hasClass('has-success') && !$(this).parents('.form-group').hasClass('has-warning')){
				let object_parent_e = $(this).parents('.col-form-input');
				if (validateEmail($(this).val())){
					$(this).removeClass('has-error');
					object_parent_e.removeClass('has-icon-input email-not-valid');
				}else{
					$(this).addClass('has-error');
					object_parent_e.addClass('has-icon-input email-not-valid');
				}
			}
		});

		// submit send wishlist
		$('.form-send-wishlist').submit(function(e){
			e.preventDefault();
			let list_email = [];

			$('.wishlist_email').each(function(){
				if (!$(this).hasClass('has-error') && $(this).val() !== ''){
					list_email.push($(this));
				}
			});

			if (list_email.length){
				if (!$('.deo-modal-send-wishlist-btn').hasClass('deo-loading-button')){
					$('.deo-modal-send-wishlist-btn').addClass('deo-loading-button');
				}
				$.each(list_email, function(){
					let email = $(this).val();
					let object_parent_e = $(this).parents('.col-form-input');
					let id_wishlist = $('.deo-table-list-wishlist tr.show .view-wishlist-product').data('id-wishlist');

					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: deo_url_ajax_wishlist,
						async: true,
						cache: false,
						data: {
							"ajax": 1,
							"action": "send-wishlist",
							"id_wishlist": id_wishlist,
							"email": email,
							"token": deo_token
						},
						beforeSend: function () {
							object_parent_e.removeClass('send-wishlist-error send-wishlist-success').addClass('has-icon-input sending-wishlist');
						},
						success: function (result){
							object_parent_e.removeClass('sending-wishlist');
							let object_result = $.parseJSON(result);
							if (object_result.errors.length){
								object_parent_e.addClass('send-wishlist-error');
							}else{
								object_parent_e.addClass('send-wishlist-success');
							}													
						},
						complete: function () {
							
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
				});
				if ($('.deo-modal-send-wishlist-btn').hasClass('deo-loading-button')){
					$('.deo-modal-send-wishlist-btn').removeClass('deo-loading-button');
				}
			}else{
				DeoTemplate.messageWarning(deo_msg_empty_wishlist_email);
			}
		});
	}

	function DeoListWishlistProductAction(){
		// delete product of wishlist
		$('.deo-delete-wishlist-product .btn').click(function(){
			let confirm_message = confirm("Are you want to delete?");
			if (confirm_message == false) {
				return false;
			}

			if (!$(this).hasClass('active')){
				$(this).addClass('active');
				let object_e = $(this);
				let object_parent_e = object_e.parents('.deo-wishlist-product');
				let id_wishlist_product = $(this).data('id-wishlist-product');
				let id_wishlist = $(this).data('id-wishlist');
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_wishlist,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "delete-wishlist-product",
						"id_wishlist": id_wishlist,	
						"id_wishlist_product": id_wishlist_product,	
						"token": deo_token	
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						if (object_result.errors.length){
							DeoTemplate.messageError(object_result.errors);
						}else{			
							DeoTemplate.messageSuccess(object_result.result);	
							object_e.parents('.deo-wishlist-product-item').fadeOut(function(){
								$(this).remove();
								if (!object_parent_e.find('.deo-wishlist-product-item').length){							
									$('.send-wishlist').hide();
									$('.deo-table-list-wishlist tr.show .view-wishlist-product').trigger('click');
								}
							});
							refeshWishlist(id_wishlist);
						}
						
						object_e.removeClass('active');
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}
			return false;
		})
		

		$('.deo-wishlist-product-save-button').click(function(){
			if (!$(this).hasClass('active')){
				$(this).addClass('active deo-loading-button');
				let object_e = $(this);
				let id_wishlist_product = $(this).data('id-wishlist-product');
				let id_wishlist = $(this).data('id-wishlist');
				let quantity = $('.wishlist-product-quantity-'+id_wishlist_product).val();		
				let priority = $('.wishlist-product-priority-'+id_wishlist_product).val();		
				
				if (Math.floor(quantity) == quantity && $.isNumeric(quantity) && quantity > 0){
					$('.wishlist-product-quantity-'+id_wishlist_product).parents('.form-group').removeClass('has-error');
					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: deo_url_ajax_wishlist,
						async: true,
						cache: false,
						data: {
							"ajax": 1,
							"action": "update-wishlist-product",
							"id_wishlist": id_wishlist,	
							"id_wishlist_product": id_wishlist_product,
							"quantity": quantity,	
							"priority": priority,
							"token": deo_token
						},
						success: function (result){
							let object_result = $.parseJSON(result);
							if (object_result.errors.length){
								DeoTemplate.messageError(object_result.errors);
							}else{
								$('.deo-wishlist-product-item-'+id_wishlist_product).hide();
								$('.deo-wishlist-product-item-'+id_wishlist_product).fadeIn();
								refeshWishlist(id_wishlist);
							}
							
							object_e.removeClass('active deo-loading-button');
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
				}else{
					$('.wishlist-product-quantity-'+id_wishlist_product).parents('.form-group').addClass('has-error');			
					DeoTemplate.messageError(wishlist_quantity_required);
					object_e.removeClass('active');
				}
			}
			return false;
		})
		
		$('.move-wishlist-item').click(function(){
			if (!$(this).hasClass('active')){
				$(this).addClass('active');
				let object_e = $(this);
				let object_parent_e = object_e.parents('.deo-wishlist-product');
				let id_wishlist_product = $(this).data('id-wishlist-product');
				let id_product = $(this).data('id-product');
				let id_product_attribute = $(this).data('id-product-attribute');
				let id_old_wishlist = $('.deo-table-list-wishlist tr.show .view-wishlist-product').data('id-wishlist');
				let id_new_wishlist = $(this).data('id-wishlist');
				let priority = $('.wishlist-product-priority-'+id_wishlist_product).val();
				let quantity = $('.wishlist-product-quantity-'+id_wishlist_product).val();
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_wishlist,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "move-wishlist-product",
						"id_new_wishlist": id_new_wishlist,	
						"id_wishlist_product": id_wishlist_product,
						"id_old_wishlist": id_old_wishlist,	
						"id_product" : id_product,
						"id_product_attribute": id_product_attribute,
						"quantity": quantity,
						"priority": priority,
						"token": deo_token
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						if (object_result.errors.length){
							DeoTemplate.messageError(object_result.errors);
						}else{
							DeoTemplate.messageSuccess(object_result.result);
							object_e.parents('.deo-wishlist-product-item').fadeOut(function(){
								$(this).remove();
								if (!object_parent_e.find('.deo-wishlist-product-item').length)
								{							
									$('.send-wishlist').hide();
									$('.deo-table-list-wishlist tr.show .view-wishlist-product').trigger('click');
								}
							});
							refeshWishlist(id_new_wishlist);
							refeshWishlist(id_old_wishlist);
						}
						
						object_e.removeClass('active');
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});		
			}
			return false;
		})
	}

	function activeEventModalWishlist(){
		$('.deo-modal-wishlist').on('hide.bs.modal', function (e) {
			resetButtonAction();
		});
		
		$('.deo-modal-wishlist').on('hidden.bs.modal', function (e) {
			$('body').css('padding-right', '');
		});

		$('.deo-modal-wishlist').on('show.bs.modal', function (e) {
			if ($('.quickview.modal').length){			
				$('.quickview.modal').modal('hide');		
			}
		});
	}

	// reset button add wishlist after click
	function resetButtonAction(){
		if ($('.deo-wishlist-button.active').length){
			$('.deo-wishlist-button.active').removeClass('active');
		}
		
		if ($('.wishlist-item.added').length){
			$('.wishlist-item.added').removeClass('added');
		}

		$('.default-wishlist.active').removeClass('active');
		$('.delete-wishlist.active').removeClass('active');
		
		$('.deo-table-list-wishlist tr.active').removeClass('active');
	}

	function validateEmail(email) {
	  // let regex = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	  // return regex.test(email);
		let reg = /^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i;
		return reg.test(email);
	}

	// update quantity of wishlist
	function refeshWishlist(id_wishlist){
		let parent = $('.view-wishlist-product-loading-'+id_wishlist).closest('.view-wishlist-product');
		parent.addClass('deo-loading-button');
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: deo_url_ajax_wishlist,
			async: true,
			cache: false,
			data: {
				"ajax": 1,
				"action": "get-wishlist-info",
				"id_wishlist": id_wishlist,
				"token": deo_token
			},
			success: function (result){
				let object_result = $.parseJSON(result);
				if (object_result.errors.length){
					DeoTemplate.messageError(object_result.errors);
				}else{				
					$('.wishlist-numberproduct-'+id_wishlist).html(object_result.result.number_product);
				}
				parent.removeClass('deo-loading-button');
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
});
