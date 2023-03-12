/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function(){
	let productcompare_add =  deo_variables_compare.productcompare_add; 
	let productcompare_viewlistcompare =  deo_variables_compare.productcompare_viewlistcompare;
	let productcompare_remove =  deo_variables_compare.productcompare_remove;
	let productcompare_add_error =  deo_variables_compare.productcompare_add_error;
	let productcompare_remove_error =  deo_variables_compare.productcompare_remove_error;
	let comparator_max_item =  deo_variables_compare.comparator_max_item;
	let compared_products =  deo_variables_compare.compared_products;
	let productcompare_max_item =  deo_variables_compare.productcompare_max_item;
	let buttoncompare_title_add =  deo_variables_compare.buttoncompare_title_add;
	let buttoncompare_title_remove =  deo_variables_compare.buttoncompare_title_remove;

	DeoCompareButtonAction();
	prestashop.on('updateProductList', function() {
		DeoCompareButtonAction();
	});
	// recall button action if need when change attribute at product page
	prestashop.on('updatedProduct', function() {  
		DeoCompareButtonAction();
	});
	prestashop.on('clickQuickView', function() {		
		check_active_compare = setInterval(function(){
			if ($('.quickview.modal').length){			
				$('.quickview.modal').on('shown.bs.modal', function (e) {
					DeoCompareButtonAction();
				})
				clearInterval(check_active_compare);
			}
		}, 300);
	});
	
	function DeoCompareButtonAction(){
		$('.deo-compare-button').click(function(){
			if (!$('.deo-compare-button.active').length){
				let total_product_compare = compared_products.length;
				let id_product = $(this).data('id-product');
				let content_product_compare_mess_remove = productcompare_remove+'. <a href="'+deo_url_compare+'" target="_blank" class="deo-special"><strong>'+productcompare_viewlistcompare+'.</strong></a>';
				let content_product_compare_mess_add = productcompare_add+'. <a href="'+deo_url_compare+'" target="_blank" class="deo-special"><strong>'+productcompare_viewlistcompare+'.</strong></a>';
				let content_product_compare_mess_max = productcompare_max_item+'. <a href="'+deo_url_compare+'" target="_blank" class="deo-special"><strong>'+productcompare_viewlistcompare+'.</strong></a>';
				
				$(this).addClass('active loading');
				let object_e = $(this);
				if ($(this).hasClass('added') || $(this).hasClass('delete')){
					// remove product form list product compare and add product to list product compare
					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: deo_url_compare,
						async: true,
						cache: false,
						data: {
							"ajax": 1,
							"action": "remove",
							"id_product": id_product,
							"token": deo_token
						},
						success: function (result){
							if (result == 1){
								// update number product on icon compare
								if ($('.deo-btn-compare .deo-total-compare').length){
									let old_num_compare = parseInt($('.deo-btn-compare .deo-total-compare').data('compare-total'));
									let new_num_compare = old_num_compare-1;
									$('.deo-btn-compare .deo-total-compare').data('compare-total',new_num_compare);
									$('.deo-btn-compare .deo-total-compare').text(new_num_compare);
								}
														
								compared_products.splice($.inArray(parseInt(id_product), compared_products), 1);
								if (object_e.hasClass('delete')){
									// remove from page product compare
									if ($('.deo-product-compare-item').length == 1){								
										window.location.replace(deo_url_compare);
									}else{
										$('td.product-'+id_product).fadeOut(function(){
											$(this).remove();
										});
									}
								}else{
									// remove from page product list
									DeoTemplate.messageSuccess(content_product_compare_mess_remove);
									$('.deo-compare-button[data-id-product='+id_product+']').removeClass('added');
									$('.deo-compare-button[data-id-product='+id_product+']').attr('title',buttoncompare_title_add);
								}
							}else{
								DeoTemplate.messageError(productcompare_remove_error);
							}
							object_e.removeClass('active loading');
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
				}else{
					if (total_product_compare < comparator_max_item){
						// add product to list product compare
						$.ajax({
							type: 'POST',
							headers: {"cache-control": "no-cache"},
							url: deo_url_compare,
							async: true,
							cache: false,
							data: {
								"ajax": 1,
								"action": "add",
								"id_product": id_product,
								"token": deo_token,
							},
							success: function (result){
								if (result == 1){
									DeoTemplate.messageSuccess(content_product_compare_mess_add);
									// update number product on icon compare
									if ($('.deo-btn-compare .deo-total-compare').length){								
										let old_num_compare = parseInt($('.deo-btn-compare .deo-total-compare').data('compare-total'));
										let new_num_compare = old_num_compare+1;
										$('.deo-btn-compare .deo-total-compare').data('compare-total',new_num_compare);
										$('.deo-btn-compare .deo-total-compare').text(new_num_compare);
									}
									
									compared_products.push(id_product);
									$('.deo-compare-button[data-id-product='+id_product+']').addClass('added');
									$('.deo-compare-button[data-id-product='+id_product+']').attr('title',buttoncompare_title_remove);
								}else{
									DeoTemplate.messageError(productcompare_add_error);
								}
								object_e.removeClass('active loading');			
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
							}
						});
						
					}else{
						// list product compare limited
						DeoTemplate.messageWarning(content_product_compare_mess_max);
						object_e.removeClass('active loading');
					}
				}
			}
			return false;
		})
	}
});


