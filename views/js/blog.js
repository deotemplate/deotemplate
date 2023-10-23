/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready( function(){
	let src =  $('#comment-form img.comment-capcha-image').attr('src');
	// $('#comment-form img.comment-capcha-image').trigger("click");
	$('#comment-form img.comment-capcha-image').click(function(){
		console.log('ssss');
		let srcn = src.replace('captchaimage','rand='+Math.random()+"&captchaimage");
		$('#comment-form img.comment-capcha-image').attr('src', srcn);
	})
	$("#comment-form").submit( function() {
		let action = $(this).attr('action');
		let data = $(this).serialize();
		
	 	$.ajax( {
			url:action,
			data: data+"&submitcomment="+Math.random(),
			type:'POST',
			dataType: 'json',
			beforeSend: function(){ 
				$('.btn-submit-comment-wrapper').addClass('deo-loading-button');
			},
			success: function(data){ 
				if( !data.error ){
					DeoTemplate.messageSuccess(data.message);
					$('input[type=text], textarea', '#comment-form').each( function(){
						$(this).val('');
					});
					let srcn = src.replace('captchaimage','rand='+Math.random()+"&captchaimage");
					$('#comment-form img.comment-capcha-image').attr('src', srcn);
				}else {
					DeoTemplate.messageWarning(data.message);
				}
				$('.btn-submit-comment-wrapper').removeClass('deo-loading-button');
			}
		} );  
		return false;
	} );
	
	$('.top-pagination-content a.disabled').click(function(){
		return false;
	});
	
	// update link in language block
	let current_lang = prestashop.language.iso_code;
	if (typeof array_list_rewrite != 'undefined'){	
		let current_list_rewrite = array_list_rewrite[current_lang];
		let current_blog_rewrite = array_blog_rewrite[current_lang];
		let current_category_rewrite = array_category_rewrite[current_lang];
		let current_config_blog_rewrite = array_config_blog_rewrite[current_lang];
		let current_config_category_rewrite = array_config_category_rewrite[current_lang];
		
		$.each(array_list_rewrite, function(iso_code, list_rewrite){
			if (iso_code != current_lang){
				let url_search = prestashop.urls.base_url + iso_code;			

				// update for widget Customer Actions and default
				let parent_o = $('.language-selector-wrapper');
				if ($('.deo_customer_actions').length){
					parent_o = $('.deo_customer_actions .language-selector');
				}
				
				parent_o.find('li a').each(function(){
					
					let lang_href = $(this).attr('href');
					
					if(lang_href.indexOf(url_search) > -1){
						let url_change;
						if ($('body#module-deotemplate-bloghomepage').length){
							url_change = lang_href.replace('/'+current_list_rewrite+'.html', '/'+list_rewrite+'.html');
						}else{
							url_change = lang_href.replace('/'+current_list_rewrite+'/', '/'+list_rewrite+'/');
						}
						
						if ($('body#module-deotemplate-blog').length){
							if (config_url_use_id == 0){
								url_change = url_change.replace('/'+current_config_blog_rewrite+'/', '/'+array_config_blog_rewrite[iso_code]+'/');
							}
							url_change = url_change.replace('/'+current_blog_rewrite, '/'+array_blog_rewrite[iso_code]);
						}
						
						if ($('body#module-deotemplate-blogcategory').length){
							if (config_url_use_id == 0){
								url_change = url_change.replace('/'+current_config_category_rewrite+'/', '/'+array_config_category_rewrite[iso_code]+'/');
							}
							url_change = url_change.replace('/'+current_category_rewrite, '/'+array_category_rewrite[iso_code]);
						}
						console.log(lang_href, url_change);
						$(this).attr('href', url_change);					
					}
				});
			}		
		});
	}
});