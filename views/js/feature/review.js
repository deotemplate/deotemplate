/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function(){
    let useful_txt =  deo_variables_review.useful_txt; 
    let report_txt =  deo_variables_review.report_txt; 
    let login_required_txt =  deo_variables_review.login_required_txt; 
	let cancel_rating_txt =  deo_variables_review.cancel_rating_txt; 
	let disable_review_form_txt =  deo_variables_review.disable_review_form_txt; 
	let review_error =  deo_variables_review.review_error;

	// activeEventModalReview();
	activeStar();

	if ($('.open-review-form').length){
		let id_product = $('.open-review-form').data('id-product');		
		let is_logged = $('.open-review-form').data('is-logged');
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: deo_url_ajax_review,
			async: true,
			cache: false,
			data: {
				"action": "render-modal-review",
				"id_product": id_product,				
				"is_logged": is_logged,
				"token": deo_token
			},
			success: function (result){
				if (result != ''){						
					$('body').append(result);
					activeEventModalReview();
					activeStar();
					$('.open-review-form').addClass('loaded-modal-review');
				}else{
					alert(review_error);
				}
							
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				// alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
		
		$('.open-review-form').click(function(){
			if ($('#criterions_list').length){	
				$('.deo-modal-review').modal('show');
			}else{
				if ($('.deo-modal-review .modal-body .disable-form-review').length){
					$('.deo-modal-review').modal('show');
				}else{
					$('.deo-modal-review-bt').remove();
					$('.deo-modal-review .modal-header').remove();
					$('.deo-modal-review .modal-body').empty();
					$('.deo-modal-review .modal-body').append('<div class="form-group disable-form-review has-danger text-center"><label class="form-control-label">'+disable_review_form_txt+'</label></div>');
					$('.deo-modal-review').modal('show');
				}
			}
			return false;
		});
	}
	
	$('.read-review').click(function(){
		if ($('.deo-product-show-review-title').length){
			if ($('.deo-product-show-review-title').hasClass('deofeature-accordion')){
				if ($('.deo-product-show-review-title').hasClass('collapsed')){
					$('.deo-product-show-review-title').trigger('click');
				}
				let timer = setInterval(function() {
					if ($('#collapse_product_review').hasClass('collapse in') || $('#collapsereviews').hasClass('collapse in')) {
						//run some other function 
						$('html, body').animate({
							scrollTop: $('.deo-product-show-review-title').offset().top
						}, 500);					   
						clearInterval(timer);
					}
				}, 200);
			}else{
				$('.deo-product-show-review-title').trigger('click');
				$('html, body').animate({
					scrollTop: $('.deo-product-show-review-title').offset().top
				}, 500);
			}
		}
		return false;
	});
	
	$('.usefulness_btn').click(function(){
		let btn = $(this);
		if (!btn.hasClass('logged')){
			DeoTemplate.messageWarning(login_required_txt);
			return false;
		}
		if (btn.hasClass('allow')){
			let id_deofeature_product_review = btn.data('id-product-review');
			let is_usefull = btn.data('is-usefull');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax_review,
				async: true,
				cache: false,
				data: {
					"action": "add-review-usefull",
					"id_deofeature_product_review": id_deofeature_product_review,				
					"is_usefull": is_usefull,
					"token": deo_token
				},
				success: function (result){
					if (result != ''){
						let count = btn.parent().find('.sum_usefull');
						count.html(parseInt(count.html()) + 1);
						btn.removeClass('allow');
						DeoTemplate.messageSuccess(useful_txt);
					}else{
						DeoTemplate.messageError(review_error);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
	});
	
	$('.report_btn').click(function(){
		let btn = $(this);
		if (!btn.hasClass('logged')){
			DeoTemplate.messageWarning(login_required_txt);
			return false;
		}
		if (btn.hasClass('allow')){
			let id_deofeature_product_review = btn.data('id-product-review');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax_review,
				async: true,
				cache: false,
				data: {
					"action": "add-review-report",
					"id_deofeature_product_review": id_deofeature_product_review,
					"token": deo_token
				},
				success: function (result){
					if (result != ''){
						btn.removeClass('allow');
						DeoTemplate.messageSuccess(report_txt);
					}else{
						DeoTemplate.messageError(review_error);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
		return false;
	});

	function activeStar(){
		// add txt cancel rating to translate
		// $('.deo-modal-review input.deo-star').rating({cancel: cancel_rating_txt});
		// $('.deo-modal-review .auto-submit-star').rating({cancel: cancel_rating_txt});

		$('.deo-grade-stars').rating();
	}

	function activeEventModalReview(){
		$('.form-new-review').submit(function(){
			if ($('.new_review_form_content .form-group.deo-has-error').length || $('.deo-fake-button').hasClass('validate-ok')){
				return false;
			}
		});
		$('.deo-modal-review').on('show.bs.modal', function (e) {
			$('.deo-modal-review-bt').click(function(){
				if (!$(this).hasClass('active')){
					$(this).addClass('active deo-loading-button');
					
					$('.new_review_form_content input, .new_review_form_content textarea').each(function(){
						
						if ($(this).val() == ''){
							$(this).parent('.form-group').addClass('deo-has-error');
							$(this).attr("required", "");
						}else{
							$(this).parent('.form-group').removeClass('deo-has-error');
							$(this).removeAttr('required');
						}
					});
					
					if ($('.new_review_form_content .form-group.deo-has-error').length){
						$(this).removeClass('active deo-loading-button');
					}else{
						$('.deo-fake-button').addClass('validate-ok');
						$.ajax({
							type: 'POST',
							headers: {"cache-control": "no-cache"},
							url: deo_url_ajax_review + '?action=add-new-review&token='+deo_token,
							async: true,
							cache: false,
							data: $(".new_review_form_content input, .new_review_form_content textarea").serialize(),
							success: function (result){
								if (result != ''){
									let object_result = $.parseJSON(result);

									$('.deo-modal-review-bt').fadeOut('slow', function(){
										$(this).remove();
									});
									
									$('.deo-modal-review .modal-body>.row').fadeOut('slow', function(){
										$(this).remove();
										if (object_result.result){
											$('.deo-modal-review .modal-body').append('<div class="form-group has-success"><label class="form-control-label">'+object_result.sucess_mess+'</label></div>');
										}else{
											$.each(object_result.errors, function(key, val){
												$('.deo-modal-review .modal-body').append('<div class="form-group has-danger text-center"><label class="form-control-label">'+val+'</label></div>');
											});
										}
									});
								}else{
									DeoTemplate.messageError(review_error);
								}
								
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
								window.location.replace($('.open-review-form').data('product-link'));
							}
						});
					}
					$('.deo-fake-button').trigger('click');
				}
			});
		});
		
		$('.deo-modal-review').on('hide.bs.modal', function (e) {
			if (!$('.deo-modal-review-bt').length && !$('.deo-modal-review .modal-body .disable-form-review').length){
				location.reload();
			}
		});
	}
});


