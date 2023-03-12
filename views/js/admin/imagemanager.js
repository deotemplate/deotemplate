/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

(function($, window, document) {
	let ImageSelector = {
		init : function (options, el) {
			let base = this;

			base.imgId = null;
			base.$elem = $(el);


			base.options = $.extend({}, $.fn.DeoImageSelector.options, options);
			if (deo_language.length == 1){
				base.options.name = base.options.name+'_'+deo_language[0].id_lang;
				base.options.name_rate_image = base.options.name_rate_image+'_'+deo_language[0].id_lang;
				base.options.name_preview_image_link = base.options.name_preview_image_link+'_'+deo_language[0].id_lang;
			}

			base.use_image_link_radio = base.$elem.closest('.form-wrapper').find('input[name="'+base.options.name_use_image_link+'"]');
			base.use_link = (base.use_image_link_radio.val() == 1 && base.use_image_link_radio.attr('checked') == 'checked') ? true : false;
			base.multiple_lang = (base.$elem.find('.translatable-field').length) ? true : false; 
			base.path_image = base.$elem.find('.image-select-wrapper').data('path_image');

			if (base.multiple_lang){
				// select image
				let has_init_image = false;
				let original_image = base.$elem.find('.image-select-wrapper');
				let virtual_wrappper = base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.virtual-image');
				virtual_wrappper.empty();
				base.$elem.find('.translatable-field').each(function(key){
					let input = $(this).find('input');
					let id_lang = input.attr('id').replace(base.options.name+'_','');

					// has init
					if ($(this).find('.image-select-wrapper').length){
						has_init_image = true;
						if (input.val()){
							let img = $(this).find('.image-wrapper img');
							img.removeClass('hide');
							img.attr('src',base.path_image+input.val());

							let virtual_image = $('<img src="#" class="img-thumbnail"/>');
							virtual_image.attr('data-lang',id_lang);
							virtual_image.attr('src',base.path_image+input.val());
							
							virtual_wrappper.append(virtual_image);
						}
					}else{
						let clone_original_image = original_image.clone(1);
						let img = clone_original_image.find('.image-wrapper img');
						input.attr('data-lang',id_lang);

						if (input.val()){
							img.removeClass('hide');
							img.attr('src',base.path_image+input.val());
							img.attr('data-lang',id_lang);

							let virtual_image = img.clone(1).attr('data-lang',id_lang); 
							virtual_wrappper.append(virtual_image);
						}

						clone_original_image.find('.choose-img').data('for','#'+input.attr('id'));
						clone_original_image.insertBefore(input);
					}
				});
				if (!has_init_image){
					original_image.addClass('hide');
				}

				// image link
				if (base.$elem.closest('.form-wrapper').children(base.options.class_select_image_link_group).length){
					let has_init_image_link = false;
					let original_image_link = base.$elem.closest('.form-wrapper').children(base.options.class_select_image_link_group).find('.preview-image-link');
					let virtual_wrappper = base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.virtual-image-link');
					virtual_wrappper.empty();
					base.$elem.closest('.form-wrapper').children(base.options.class_select_image_link_group).find('.translatable-field').each(function(key){
						let input = $(this).find('input');
						let id_lang = input.attr('id').replace(base.options.name_preview_image_link+'_','');

						// has init
						if ($(this).find('.preview-image-link').length){
							has_init_image_link = true;
							if (input.val()){
								let img = $(this).find('.img-preview');
								img.removeClass('hide');
								img.attr('src',input.val());

								let virtual_image = $('<img src="#" class="img-thumbnail"/>');
								virtual_image.attr('data-lang',id_lang);
								virtual_image.attr('src',input.val());
								virtual_wrappper.append(virtual_image);
							}
						}else{
							let clone_preview_image_link = original_image_link.clone(1);
							let img = clone_preview_image_link.find('.img-preview');
							input.attr('data-lang',id_lang);
							
							if (input.val()){
								img.removeClass('hide');
								img.attr('src',input.val());
								img.attr('data-lang',id_lang);

								let virtual_image = img.clone(1).attr('data-lang',id_lang); 
								virtual_image.removeClass('img-preview');
								virtual_wrappper.append(virtual_image);
							}

							clone_preview_image_link.insertAfter(input);
						}

					});
					if (!has_init_image){
						original_image_link.addClass('hide');
					}
				}
			}else{
				// select image
				let input = base.$elem.find('input');
				let img = base.$elem.find('.image-select-wrapper img');

				if (input.val()){
					img.removeClass('hide');
					img.attr('src',base.path_image+input.val());
				}
				
				base.$elem.find('.choose-img').data('for','#'+input.attr('id'));

				if (base.$elem.closest('.form-wrapper').children(base.options.class_select_image_link_group).length){
					let input = base.$elem.closest('.form-wrapper').children(base.options.class_select_image_link_group).find('input');
					let img = base.$elem.closest('.form-wrapper').children(base.options.class_select_image_link_group).find('.preview-image-link .img-preview');

					// image link
					if (input.val()){
						img.removeClass('hide');
						img.attr('src',input.val());
					}
				}
			}

			if (base.$elem.hasClass('lazyload_carousel')){
				
	        }else{
	            base.toogle_switch($('input[name="'+base.options.name_lazyload+'"]'), $(base.options.class_rate_lazyload_group));
	        }

			base.$elem.data('init', base.options);

			base.toogle_switch($('input[name="'+base.options.name_use_image_link+'"]'), $(base.options.class_select_image_link_group), base.$elem);
			base.choose_image();
			base.select_image();
			base.reset_image();
			base.rate_image();
			// base.image_hotspot();
			base.load_image_link();
			base.toogle_use_image_link();
			base.action_modal_select_image();
		},

		toogle_switch : function(element, group_show, group_hide = false, duration = 400){
			if (!element.length || !group_show.length) return false;
			element.change(function(){
				if ($(this).val() == 1){
					group_show.show(duration);
					group_show.removeClass('hide-config');
					if (group_hide){
						group_hide.hide(duration);
					}
				}else{ 
					group_show.hide(duration);
					if (group_hide){
						group_hide.show(duration);
						group_hide.removeClass('hide-config');
					}
				}
			});
			$(element.selector+':checked').trigger('change');
		},

		toogle_use_image_link : function(){
			let base = this;

			base.$elem.closest('.form-wrapper').find('input[name="'+base.options.name_use_image_link+'"]').change(function() {

				if ($('input[name="temp_top"]').is(":visible") && $('input[name="temp_left"]').is(":visible")){
					$('.image-hotspot .image-wrapper .dot,.image-hotspot .preview-image-link .dot').remove();
					$('.image-hotspot .image-wrapper,.image-hotspot .preview-image-link').append('<span class="dot" style="top: '+ $('input[name="temp_top"]').val() +'%;left: '+ $('input[name="temp_left"]').val() +'%;"></span>');
				}
				
				if ($(this).val() == 1){
					base.use_link = true;
				}else{
					base.use_link = false;
				}
				
				setTimeout(function(){
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.calc-rate-image').trigger('click');
				}, 400);
			});
		},

		calculate_rate_image : function(width,height){
			return Math.round(((height/width)*100)*10)/10;
		},

		select_image : function(){
			let base = this;
			
			$(document).on('click', '.image-manager .img-link', function(e) {
				e.preventDefault();

				if (!base.$elem.hasClass('choosing')){
					return false;
				}

				let img = $(this).children('img');
				let name_img_select = img.data('folder')+'/'+img.data('name');
				$(base.imgId).val(name_img_select);

				let img_show;
				if (base.multiple_lang) {
					img_show = $(base.imgId).closest(".translatable-field").find(".image-wrapper img");
					img_show.attr('src',base.path_image+name_img_select);
					img_show.removeClass('hide');

					let id_lang = $(base.imgId).closest(".translatable-field").find('input').data('lang');
					let virtual_image = img_show.clone(1).attr('data-lang',id_lang);
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.virtual-image').find('img[data-lang="'+id_lang+'"]').remove();
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.virtual-image').append(virtual_image);
				}else{
					img_show = base.$elem.find(".image-select-wrapper img");
					img_show.attr('src',base.path_image+name_img_select);
					img_show.removeClass('hide');
				}

				img_show.on('load', function () {
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.calc-rate-image').trigger('click');
				});

				$("#modal_select_image").modal('hide');
			});
		},

		reset_image : function(){
			let base = this;

			base.$elem.find('.reset-img').click(function (e) {
				e.stopPropagation();

				if (base.$elem.hasClass('choosing')){
					return false;
				}

				let img, input;
				if (base.multiple_lang) {
					img = $(this).closest(".translatable-field").find(".image-wrapper img");
					input = $(this).closest(".translatable-field").find("input");
					let id_lang = input.data('lang');
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.virtual-image').find('img[data-lang="'+id_lang+'"]').remove();
					$('#'+base.options.name_rate_image+'_'+id_lang).val(0);
				}else{
					img = base.$elem.find(".image-select-wrapper img");
					input = base.$elem.find("input");
					$('#'+base.options.name_rate_image).val(0);
				}
				img.attr("src", "");
				img.addClass('hide');
				input.val('');
			});
		},

		choose_image : function(){
			let base = this;

			base.$elem.find('.choose-img').click(function (e) {
				e.preventDefault();

				base.$elem.addClass('choosing');
				let modal = $("#modal_select_image");
				let url = $(this).attr('href');
				base.imgId = $(this).data('for');
				$.ajax({
					url: url,
					beforeSend: function () {
						$("#deo_loading").show();
					},
					success: function (response) {
						modal.find(".modal-body").html(response);
						modal.find(".modal-body").css('min-height', $(window).height() * 0.8);
						modal.modal('show');
					},
					complete: function () {
						$("#deo_loading").hide();
					}
				});
			});
		},

		rate_image : function(){
			let base = this;

			base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.calc-rate-image').click(function (){
				if (base.multiple_lang) {
					let rate_image = 0;
					let img = (base.use_link) ? $(this).closest(base.options.class_calc_rate_image_group).find('.virtual-image-link img') : $(this).closest(base.options.class_calc_rate_image_group).find('.virtual-image img');
					if (img.length){
						img.each(function() {
							rate_image = 0;
							let id_lang = $(this).data('lang');
							if (!$(this).hasClass('image-error')){
								rate_image = base.calculate_rate_image($(this).prop('naturalWidth'),$(this).prop('naturalHeight'));
							}
							$('#'+base.options.name_rate_image+'_'+id_lang).val(rate_image);
						});
					}else{
						$('.'+base.options.name_rate_image).val(rate_image);
					}
				}else{
					let image = (base.use_link) ? base.$elem.closest('.form-wrapper').find('.preview-image-link .img-preview') : base.$elem.find('.image-select-wrapper img');
					if (!image.hasClass('image-error') && (image.attr('src') != '' || image.attr('src') != '#')){
						rate_image = base.calculate_rate_image(image.prop('naturalWidth'),image.prop('naturalHeight'));
					}
					$('#'+base.options.name_rate_image).val(rate_image);
				}
			});
		},

		load_image_link : function(){
			let base = this;

			base.$elem.closest('.form-wrapper').children(base.options.class_select_image_link_group).find('input').keyup(function(event) {
				let src = $(this).val();
				if (src == ''){
					return false;
				}

				let img, virtual_image;
				if (base.multiple_lang) {
					img = $(this).closest('.translatable-field').find('.preview-image-link .img-preview');
					let id_lang = $(this).data('lang');
					img.attr('src',src);

					virtual_image = img.clone(1).attr('data-lang',id_lang);
					virtual_image.removeClass('img-preview');
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.virtual-image-link').find('img[data-lang="'+id_lang+'"]').remove();
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.virtual-image-link').append(virtual_image);
				}else{
					img = $(this).closest(base.options.class_select_image_link_group).find('.preview-image-link .img-preview');
					img.attr('src',src);
				}

				let no_image = $(base.options.class_select_image_link_group).find('.no-image').first();
				img.on('load', function(){
					$(this).removeClass('hide');
					no_image.addClass('hide');
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.calc-rate-image').trigger('click');
					if (base.multiple_lang) {
						virtual_image.removeClass('image-error');
					}
				});

				img.on('error', function(){
					$(this).addClass('hide');
					no_image.removeClass('hide');
					base.$elem.closest('.form-wrapper').children(base.options.class_calc_rate_image_group).find('.calc-rate-image').trigger('click');
					if (base.multiple_lang) {
						virtual_image.addClass('image-error');
					}
				});
			});
		},

		action_modal_select_image : function(){
			let base = this;

			$("#modal_select_image").on('hidden.bs.modal', function () {
				base.$elem.removeClass('choosing');
			});
		}
	};

	$.fn.DeoImageSelector = function (options) {
		return this.each(function () {
			if ($(this).data("image-init") === true) {
				return false;
			}

			$(this).data("image-init", true);
			let image_selector = Object.create(ImageSelector);
			image_selector.init(options, this);
			$.data(this, "ImageSelector", image_selector);
		});
	};

	$.fn.DeoImageSelector.options = {
		name : 'image',
		name_lazyload : 'lazyload',
		name_rate_image : 'rate_image',
		name_preview_image_link : 'image_link',
		name_use_image_link : 'use_image_link',
		class_calc_rate_image_group : '.group_calc_rate_image',
		class_rate_lazyload_group : '.rate_lazyload_group',
		class_select_image_link_group : '.select_image_link_group',
	};
}(jQuery, window, document));