/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

var instantSearchQueries = [];
$(document).ready(function(){
	/* TODO Ids aa blocksearch_type need to be removed*/
	$(".deo-advanced-search-query").each(function(index) {
		var advanced_search_query = $(this);
		var ajaxsearch = advanced_search_query.data('ajax-search');
		if (typeof ajaxsearch != 'undefined' && ajaxsearch){
			var parent_form = advanced_search_query.closest('.deo-search-advanced-top-box');
			var width_ac_results = 	advanced_search_query.outerWidth();
			var token = parent_form.find('.token').val();
			var number_product_display = advanced_search_query.data('number-product-display');
			var advanced_search_url = parent_form.attr('action');
			var show_image = advanced_search_query.data('show-image');
			var show_price = advanced_search_query.data('show-price');
			var show_stock = advanced_search_query.data('show-stock');
			var text_not_found = advanced_search_query.data('text-not-found');
			var select_list_cate = parent_form.find('.select-list-cate');
			var select_cate_id = parent_form.find('.deo-advanced-search-cate-id');
			var list_cate =  parent_form.find('.list-cate');
			var keydown = jQuery.Event("keydown");
			keydown.keyCode = 40;

			advanced_search_query.autocomplete(
				advanced_search_url,
				{
					minChars: 3,
					max: number_product_display,
					width: (width_ac_results > 0 ? width_ac_results : 500),
					selectFirst: false,
					scroll: false,
					dataType: "json",
					formatItem: function(data, i, max, value, term) {
						return value;
					},
					parse: function(data) {
					
						var result = data.products;
						var mytab = new Array();
						if (result.length > 0){
							for (var i = 0; i < result.length; i++){
								// update config show product img and product price
								var html_result = '';
								if (typeof show_image != 'undefined' && show_image){
									html_result += '<div class="result-img"><img class="img-fluid" align = "center" src=' + result[i].cover.bySize.small_default.url + '></div>';
								}
								html_result += '<div class="result-content"><div class="result-title">' + result[i].name +'</div>';
								if (typeof show_price != 'undefined' && show_price){
									html_result += '<div class="result-price';
									if (typeof result[i].has_discount != "undefined" && result[i].has_discount){
										html_result += ' has_discount">';
										html_result += ' <span class="regular-price">'+ result[i].regular_price +'</span>';
										if (typeof result[i].discount_type  != "undefined" && result[i].discount_type == "percentage"){
											html_result += '<span class="discount-percentage">'+ result[i].discount_percentage +'</span>';
										}else if(typeof result[i].discount_type  != "undefined" && result[i].discount_type == "amount"){
											html_result += '<span class="discount-amount discount-product">'+ result[i].discount_amount_to_display +'</span>';
										}
									}else{
										html_result += '">';
									}
									html_result += '<span class="price">'+ result[i].price +'</span></div>';
									if (typeof result[i].deo_label != 'undefined' && result[i].deo_label && show_stock){
										let class_stock = (result[i].quantity_all_versions > 0) ? 'in-stock' : 'out-of-stock';
										html_result += '<div class="result-stock '+ class_stock +'"><span>'+ result[i].deo_label +'</span></div>';
									}
								}
								html_result += '</div>';
								
								mytab[mytab.length] = { data: result[i], value: html_result };
							}
						}else{
							mytab[0] = {data: { url: window.location.href}, value: '<span class="no-result">'+text_not_found+'</span>'};
						}
								
						return mytab;
					},
					extraParams: {
						ajax_search: 1,
						id_lang: prestashop.language.id,
						token: token,
					}
				}
			)
			.result(function(event, data, formatted) {
				advanced_search_query.val(data.name);
				document.location.href = data.url;
			});
			
			// update when width of input has been change
			advanced_search_query.click(function(){
				width_ac_results = $(this).outerWidth();		
				// update option js libary option when resize
				$(this).setOptions({
					width: width_ac_results
				});	
			});


			// update position of result when resize
			$(window).resize(function(){
				updatePositionOfResult(advanced_search_query);
			});

			list_cate.find('.cate-item').click(function(){
				var cate_id = $(this).data('cate-id');
				var cate_name = $(this).data('cate-name');

				list_cate.find('.cate-item').removeClass('active');
				select_cate_id.val(cate_id);
				select_list_cate.find('span').text(cate_name);
				$(this).addClass('active');
				select_list_cate.trigger('click');
				advanced_search_query.focus().trigger(keydown);
			});
			
			// show result when click to input search
			advanced_search_query.click(function(){
				if ($(this).val() != ''){
					$(this).trigger(keydown);
				}
			});
		}
	});
	
	// toggle open popup search
	$('.deo-search-advanced .popup-title').click(function(e) {
    	e.stopPropagation();
    	
    	let popup_over = $(this).closest('.deo-search-advanced');
    	let popup_content = popup_over.find('.popup-content');

    	popup_content.toggle(300, function(){
    		if (popup_over.hasClass('open')){
    			popup_over.removeClass('open');
    		}else{
    			popup_over.addClass('open');
    		}
    	});
    });
});

function updatePositionOfResult(advanced_search_query){
	var ac_results = advanced_search_query.closest('.deo-search-advanced-top-box').find('.ac_results');
	if (ac_results.length) {
		width_ac_results = advanced_search_query.outerWidth();

		// update option js libary option when resize
		advanced_search_query.setOptions({
			width: width_ac_results
		});	
		
		ac_results.width(width_ac_results);
	}
}

function tryToCloseInstantSearch(){
	if ($('#old_center_column').length > 0){
		$('#center_column').remove();
		$('#old_center_column').attr('id', 'center_column');
		$('#center_column').show();
		return false;
	}
}

function stopInstantSearchQueries(){
	for(i=0;i<instantSearchQueries.length;i++)
		instantSearchQueries[i].abort();
	instantSearchQueries = new Array();
}
