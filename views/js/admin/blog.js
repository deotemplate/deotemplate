/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

(function($) {
	$.fn.DeoMegaMenuList = function(opts) {
		// default configuration
		let config = $.extend({}, {
			action:null, 
			addnew : null,
			confirm_del:'Are you sure delete this?'
		}, opts);

		function checkInputHanlder(){
			let _updateMenuType = function(){
				$(".menu-type-group").parent().parent().hide();
				$("[for^=content_text_]").parent().hide();

				if( $("#menu_type").val() =='html' ){
					$("[for^=content_text_]").parent().show();
				}else {
					$("#"+$("#menu_type").val()+"_type").parent().parent().show();
				}
			};
			_updateMenuType(); 
			$("#menu_type").change(  _updateMenuType );

			let _updateSubmenuType = function(){
				if( $("#type_submenu").val() =='html' ){
					$("[for^=submenu_content_text_]").parent().show();
				}else{
					$("[for^=submenu_content_text_]").parent().hide();
				}
			};
			_updateSubmenuType();
			$("#type_submenu").change(  _updateSubmenuType );

		}

		function manageTreeMenu(){
			if ($('ol').hasClass("sortable")){
				$('ol.sortable').nestedSortable({
					forcePlaceholderSize: true,
					handle: 'div',
					helper:	'clone',
					items: 'li',
					opacity: .6,
					placeholder: 'placeholder',
					revert: 250,
					tabSize: 25,
					tolerance: 'pointer',
					toleranceElement: '> div',
					maxLevels: 4,

					isTree: true,
					expandOnHover: 700,
					startCollapsed: true,
					
					stop: function(){ 							
						let serialized = $(this).nestedSortable('serialize');
					
						$.ajax({
							type: 'POST',
							url: config.action+"&doupdatepos=1&rand="+Math.random(),
							data : serialized+'&updatePosition=1' 
						}).done( function (msg) {
							 showSuccessMessage(msg);
						} );
					}
				});
				
				// $('#serialize').click(function(){
					// let serialized = $('ol.sortable').nestedSortable('serialize');
				 	// let text = $(this).val();
				 	// let $this  = $(this);
				 	// $(this).val( $(this).data('loading-text') );
					// $.ajax({
						// type: 'POST',
						// url: config.action+"&doupdatepos=1&rand="+Math.random(),
						// data : serialized+'&updatePosition=1' 
					// }).done( function () {
						 // $this.val( text );
					// } );
				// });
				
				$('#addcategory').click(function(){
					location.href=config.addnew;
				});
			}	
		}

		/**
		 * initialize every element
		 */
		this.each(function() {  
			$(".quickedit",this).click( function(){  
				location.href=config.action+"&id_deoblog_category="+$(this).attr('rel').replace("id_","");
			});

			$(".quickdel",this).click( function(){  
				if( confirm(config.confirm_del) ){
					location.href=config.action+"&dodel=1&id_deoblog_category="+$(this).attr('rel').replace("id_","");
				}
			});
		
			$(".delete_many_menus",this).click( function(){
			    if (confirm('Delete selected items?')){
			        let list_menu = '';
			        $('.quickselect:checkbox:checked').each(function () {
			            list_menu += $(this).val() + ",";

			        });

			        if (list_menu != ''){
			            location.href=config.action+"&delete_many_menu=1&list="+list_menu;
			        }
			    }
			});

			manageTreeMenu();
		});

		return this;
	};
})(jQuery);


jQuery(document).ready(function(){
 	$(".deo-modal").fancybox({
	 	'type':'iframe',
	 	'width':980,
	 	'height':500,
	 	afterLoad:function(   ){
	 		if( $('body',$('.fancybox-iframe').contents()).find("#main").length  ){  
		 		$('body',$('.fancybox-iframe').contents()).find("#header").hide();
		 		$('body',$('.fancybox-iframe').contents()).find("#footer").hide();
	 		}else { 
	 			 
	 		}
	 	}
	});
 	
 	$("#widgetds a.btn").fancybox( {'type':'iframe'} );

 	$(".deo-modal-action, #widgets a.btn").fancybox({
	 	'type':'iframe',
	 	'width':950,
	 	'height':500,
	 	afterLoad:function(   ){
	 		if( $('body',$('.fancybox-iframe').contents()).find("#main").length  ){  
		 		$('body',$('.fancybox-iframe').contents()).find("#header").hide();
		 		$('body',$('.fancybox-iframe').contents()).find("#footer").hide();
	 		}else { 
	 			 
	 		}
	 	},
 		afterClose: function (event, ui) {  
		//	location.reload();
		},	
	});
});


/*
 * SHOW HIDE - URL include ID
 */
$(document).ready(function(){
	$('.form-action').change(function(){
		let elementName = $(this).attr('name');
		$('.'+elementName+'_sub').hide(300);
		$('.'+elementName+'-'+$(this).val()).show(500);
	});
	$('.form-action').trigger("change");

	let selectComment = $('.select-comment');
	let duration = 300;
	let local_comment = $('.comment-local');
	let comment_limit = $('.comment-limit');
	let facebook_comment = $('.comment-facebook');

	selectComment.change(function(){
		if ($(this).val() == 'local'){
			local_comment.show(duration);
			comment_limit.show(duration);
			facebook_comment.hide(duration);
		}else if ($(this).val() == 'facebook'){
			local_comment.hide(duration);
			comment_limit.show(duration);
			facebook_comment.show(duration);
		}else if ($(this).val() == 'none'){
			local_comment.hide(duration);
			comment_limit.hide(duration);
			facebook_comment.hide(duration);
		}
	});
	selectComment.trigger('change');
});

/*
 * IMAGE SIZE
 */
$(document).ready(function(){
	if ($('.admindeoblogdashboard').length == 0){
		return;
	}

	let name_input = $('#temp_name');
	let width_input = $('#temp_width');
	let height_input = $('#temp_height');
	// let category_input = $('input[name="temp_category"]');
	// let blog_input = $('input[name="temp_blog"]');
	// let blog_thumb_input = $('input[name="temp_blog_thumb"]');
	let image_size = $('.form-image-size > input');
	let data_image_size = (image_size.val() != '') ? JSON.parse(image_size.val()) : new Object();

	let name_template_input = $('#temp_name_template');
	let item_per_page_input = $('#temp_item_per_page');
	let item_per_category_input = $('#temp_item_per_category');
	let show_image_input = $('input[name="temp_show_image"]');
	let show_title_input = $('input[name="temp_show_title"]');
	let show_description_input = $('input[name="temp_show_description"]');
	let show_author_input = $('input[name="temp_show_author"]');
	let show_category_input = $('input[name="temp_show_category"]');
	let show_created_date_input = $('input[name="temp_show_created_date"]');
	let show_viewed_input = $('input[name="temp_show_viewed"]');
	let show_comment_input = $('input[name="temp_show_comment"]');
	let show_read_more_input = $('input[name="temp_show_read_more"]');
	let show_introduce_category_input = $('input[name="temp_show_introduce_category"]');
	let col_xxl_input = $('#temp_col_xxl');
	let col_xl_input = $('#temp_col_xl');
	let col_lg_input = $('#temp_col_lg');
	let col_md_input = $('#temp_col_md');
	let col_sm_input = $('#temp_col_sm');
	let col_xs_input = $('#temp_col_xs');
	let col_sp_input = $('#temp_col_sp');
	let tempates = $('.form-template > input');
	let data_tempates = (tempates.val() != '') ? JSON.parse(tempates.val()) : new Object();

	if (Object.keys(data_image_size).length){
		$('.temp-table-image').addClass('not-emtpy');
		$.each(data_image_size, function( key, val ){
			let data = {
				name : key,
				width : val.width,
				height : val.height,
				rate : val.rate,
				// category : val.category,
				// blog : val.blog,
				// blog_thumb : val.blog_thumb,
			};
			
			$('.temp-table-image').append(createRowImage(data));
		});
	}else{
		$('.temp-table-image').removeClass('not-emtpy');
	}

	if (Object.keys(deo_templates_blog).length){
		$('.temp-table-template').addClass('not-emtpy');
		$.each(deo_templates_blog, function( key, val ){
			let data = new Object(); 
			if (typeof data_tempates[val] != 'undefined'){
				data = data_tempates[val];
			}else{
				data = {
					name : val,
					show_image : 0,
					show_title : 0,
					show_description : 0,
					show_category : 0,
					show_created_date : 0,
					show_author : 0,
					show_comment : 0,
					show_viewed : 0,
					show_introduce_category : 0,
					item_per_page : 6,
					item_per_category : 3,
					col : {
						xxl : 2,
						xl : 2,
						lg : 2,
						md : 2,
						sm : 2,
						xs : 1,
						sp : 1,
					},
				};
				data_tempates[val] = data;
			}
			$('.temp-table-template').append(createRowTemplate(data));
		});

		if (tempates.val() == '' && Object.keys(deo_templates_blog).length > 0){
			tempates.val(JSON.stringify(data_tempates));
		}
	}else{
		$('.temp-table-template').removeClass('not-emtpy');
	}

	setTimeout(function(){
		$('.temp-table-image').removeClass('loading');
		$('.temp-table-template').removeClass('loading');
	}, 1000);

	$('.add-new').click(function(){
		$('.form-temp-image .cancel').trigger('click');
		$('.form-temp-image').removeClass('hide');
	});

	$('.form-temp-image .save').click(function(){
		let name = convertName(name_input.val().trim());
		let width = width_input.val().trim();
		let height = height_input.val().trim();
		let rate = Math.round(((height/width)*100)*10)/10;
		// let category = getValueRadio(category_input);
		// let blog = getValueRadio(blog_input);
		// let blog_thumb = getValueRadio(blog_thumb_input);

		let data = {
			name : name,
			width : width,
			height : height,
			rate : rate,
			// category : category,
			// blog : blog,
			// blog_thumb : blog_thumb,
		};

		if (validateDataImage(data)){
			if ($('.temp-table-image').hasClass('edit')){
				// remove old data
				delete data_image_size[$('.temp-table-image tr.active').attr('id')];

				let row = $('.temp-table-image tr.active');
				row.attr('id', name);
				row.find('.name').html(name);
				row.find('.width').html(width);
				row.find('.height').html(height);
				row.find('.rate').html(rate);
				
				// if (category){
				// 	row.find('.category').addClass('checked');
				// }else{
				// 	row.find('.category').removeClass('checked');
				// }

				// if (blog){
				// 	row.find('.blog').addClass('checked');
				// }else{
				// 	row.find('.blog').removeClass('checked');
				// }
				
				// if (blog_thumb){
				// 	row.find('.blog_thumb').addClass('checked');
				// }else{
				// 	row.find('.blog_thumb').removeClass('checked');
				// }
			}else{
				$('.temp-table-image').addClass('not-emtpy');
				$('.temp-table-image').append(createRowImage(data));
			}

			// add new data
			data_image_size[name] = {
				width : width,
				height : height,
				rate : rate,
				// category : category,
				// blog : blog,
				// blog_thumb : blog_thumb,
			}

			$('.form-temp-image .cancel').trigger('click');
			image_size.val(JSON.stringify(data_image_size));
		}else{
			return false;
		}
	});

	$('.form-temp-image .cancel').click(function(){
		name_input.val('');
		width_input.val(300);
		height_input.val(300);
		// setValueRadio(category_input,1);
		// setValueRadio(blog_input,1);
		// setValueRadio(blog_thumb_input,1);

		$('.form-temp-image').addClass('hide');
		$('.temp-table-image').removeClass('edit');
		$('.temp-table-image tr').removeClass('active');
	});

	$(document).on('click', '.temp-table-image .edit', function(e){
		e.stopPropagation();

		$('.temp-table-image tbody tr').removeClass('active');
		let row = $(this).parents('tr');
		row.addClass('active');

		let name = row.find('.name').html();
		let width = row.find('.width').html();
		let height = row.find('.height').html();
		// let blog = (row.find('.blog').hasClass('checked')) ? 1 : 0;
		// let category = (row.find('.category').hasClass('checked')) ? 1 : 0;
		// let blog_thumb = (row.find('.blog_thumb').hasClass('checked')) ? 1 : 0;


		data = {
			name : name,
			width : width,
			height : height,
			// blog : blog,
			// category : category,
			// blog_thumb : blog_thumb,
		};

		name_input.val(name);
		width_input.val(width);
		height_input.val(height);
		// setValueRadio(blog_input,blog);
		// setValueRadio(category_input, blog_thumb);
		// setValueRadio(blog_thumb_input, blog_thumb);

		if (!$('.temp-table-image').hasClass('edit')){
			$('.form-temp-image').removeClass('hide');
		}
		$('.temp-table-image').addClass('edit');
	});

	$(document).on('click', '.temp-table-image .remove', function(){
		let confirm_message = confirm("Do you want to delete this row?");
		if (confirm_message == true) {
			let row = $(this).closest('tr');
			row.remove();

			showSuccessMessage('Delete Success');
			delete data_image_size[row.attr('id')];
			image_size.val(JSON.stringify(data_image_size));

			if ($('.temp-table-image tbody tr:not(.empty-row)').length == 0){
				$('.temp-table-image').removeClass('not-emtpy');
			}

			$('.form-temp-image .cancel').trigger('click');
		}		
	});

	$('.form-temp-template .save').click(function(){
		let name = name_template_input.val();
		let item_per_page = item_per_page_input.val();
		let item_per_category = item_per_category_input.val();
		let col_xxl = col_xxl_input.val();
		let col_xl = col_xl_input.val();
		let col_lg = col_lg_input.val();
		let col_md = col_md_input.val();
		let col_sm = col_sm_input.val();
		let col_xs = col_xs_input.val();
		let col_sp = col_sp_input.val();
		let show_image = getValueRadio(show_image_input);
		let show_title = getValueRadio(show_title_input);
		let show_description = getValueRadio(show_description_input);
		let show_author = getValueRadio(show_author_input);
		let show_category = getValueRadio(show_category_input);
		let show_created_date = getValueRadio(show_created_date_input);
		let show_viewed = getValueRadio(show_viewed_input);
		let show_comment = getValueRadio(show_comment_input);
		let show_read_more = getValueRadio(show_read_more_input);
		let show_introduce_category = getValueRadio(show_introduce_category_input);


		let data = {
			name : name,
			item_per_page : item_per_page,
			item_per_category : item_per_category,
			show_image : show_image,
			show_title : show_title,
			show_description : show_description,
			show_author : show_author,
			show_category : show_category,
			show_created_date : show_created_date,
			show_viewed : show_viewed,
			show_comment : show_comment,
			show_read_more : show_read_more,
			show_introduce_category : show_introduce_category,
			col : {
				xxl : col_xxl,
				xl : col_xl,
				lg : col_lg,
				md : col_md,
				sm : col_sm,
				xs : col_xs,
				sp : col_sp,
			},
		};

		if (validateDataTemplate(data)){
			if ($('.temp-table-template').hasClass('edit')){
				let row = $('.temp-table-template tr.active');
				row.find('.item_per_page').html(item_per_page);
				row.find('.item_per_category').html(item_per_category);
				row.find('.col_xxl').html(col_xxl);
				row.find('.col_xl').html(col_xl);
				row.find('.col_lg').html(col_lg);
				row.find('.col_md').html(col_md);
				row.find('.col_sm').html(col_sm);
				row.find('.col_xs').html(col_xs);
				row.find('.col_sp').html(col_sp);

				if (show_image){
					row.find('.show_image').addClass('checked');
				}else{
					row.find('.show_image').removeClass('checked');
				}
				
				if (show_title){
					row.find('.show_title').addClass('checked');
				}else{
					row.find('.show_title').removeClass('checked');
				}

				if (show_description){
					row.find('.show_description').addClass('checked');
				}else{
					row.find('.show_description').removeClass('checked');
				}

				if (show_author){
					row.find('.show_author').addClass('checked');
				}else{
					row.find('.show_author').removeClass('checked');
				}

				if (show_category){
					row.find('.show_category').addClass('checked');
				}else{
					row.find('.show_category').removeClass('checked');
				}

				if (show_created_date){
					row.find('.show_created_date').addClass('checked');
				}else{
					row.find('.show_created_date').removeClass('checked');
				}

				if (show_viewed){
					row.find('.show_viewed').addClass('checked');
				}else{
					row.find('.show_viewed').removeClass('checked');
				}

				if (show_comment){
					row.find('.show_comment').addClass('checked');
				}else{
					row.find('.show_comment').removeClass('checked');
				}

				if (show_read_more){
					row.find('.show_read_more').addClass('checked');
				}else{
					row.find('.show_read_more').removeClass('checked');
				}

				if (show_introduce_category){
					row.find('.show_introduce_category').addClass('checked');
				}else{
					row.find('.show_introduce_category').removeClass('checked');
				}
			}else{
				$('.temp-table-template').addClass('not-emtpy');
				$('.temp-table-template').append(createRowTemplate(data));
			}

			let col_class = '';
			$.each(data.col, function(key, value) {
				let col = (value == 5) ? '2-4' : 12/value;
				col_class += ' col-'+key+'-'+col;
			});

			// add new data
			data_tempates[name] = {
				name : name,
				item_per_page : item_per_page,
				item_per_category : item_per_category,
				show_image : show_image,
				show_title : show_title,
				show_description : show_description,
				show_author : show_author,
				show_category : show_category,
				show_created_date : show_created_date,
				show_viewed : show_viewed,
				show_comment : show_comment,
				show_read_more : show_read_more,
				show_introduce_category : show_introduce_category,
				col_class : $.trim(col_class),
				col : {
					xxl : col_xxl,
					xl : col_xl,
					lg : col_lg,
					md : col_md,
					sm : col_sm,
					xs : col_xs,
					sp : col_sp,
				},
			}

			tempates.val(JSON.stringify(data_tempates));
			$('.form-temp-template .cancel').trigger('click');
		}else{
			return false;
		}
	});

	$('.form-temp-template .cancel').click(function(){
		name_template_input.val('');
		item_per_page_input.val('');
		item_per_category_input.val('');
		col_xxl_input.val(2);
		col_xl_input.val(2);
		col_lg_input.val(2);
		col_md_input.val(2);
		col_sm_input.val(2);
		col_xs_input.val(1);
		col_sp_input.val(1);
		setValueRadio(show_image_input,1);
		setValueRadio(show_title_input,1);
		setValueRadio(show_description_input,1);
		setValueRadio(show_author_input,1);
		setValueRadio(show_category_input,1);
		setValueRadio(show_created_date_input,1);
		setValueRadio(show_viewed_input,1);
		setValueRadio(show_comment_input,1);
		setValueRadio(show_read_more_input,1);
		setValueRadio(show_introduce_category_input,1);

		$('.form-temp-template').addClass('hide');
		$('.temp-table-template').removeClass('edit');
		$('.temp-table-template tr').removeClass('active');
	});

	$(document).on('click', '.temp-table-template .edit', function(e){
		e.stopPropagation();

		$('.temp-table-template > tbody > tr').removeClass('active');
		let row = $(this).parents('tr');
		row.addClass('active');

		let name = row.find('.name').html();
		let item_per_page = row.find('.item_per_page').html();
		let item_per_category = row.find('.item_per_category').html();
		let col_xxl = row.find('.col_xxl').html();
		let col_xl = row.find('.col_xl').html();
		let col_lg = row.find('.col_lg').html();
		let col_md = row.find('.col_md').html();
		let col_sm = row.find('.col_sm').html();
		let col_xs = row.find('.col_xs').html();
		let col_sp = row.find('.col_sp').html();
		let show_image = (row.find('.show_image').hasClass('checked')) ? 1 : 0;
		let show_title = (row.find('.show_title').hasClass('checked')) ? 1 : 0;
		let show_description = (row.find('.show_description').hasClass('checked')) ? 1 : 0;
		let show_author = (row.find('.show_author').hasClass('checked')) ? 1 : 0;
		let show_category = (row.find('.show_category').hasClass('checked')) ? 1 : 0;
		let show_created_date = (row.find('.show_created_date').hasClass('checked')) ? 1 : 0;
		let show_viewed = (row.find('.show_viewed').hasClass('checked')) ? 1 : 0;
		let show_comment = (row.find('.show_comment').hasClass('checked')) ? 1 : 0;
		let show_read_more = (row.find('.show_read_more').hasClass('checked')) ? 1 : 0;
		let show_introduce_category = (row.find('.show_introduce_category').hasClass('checked')) ? 1 : 0;

		data = {
			name : name,
			item_per_page : item_per_page,
			item_per_category : item_per_category,
			show_image : show_image,
			show_title : show_title,
			show_description : show_description,
			show_author : show_author,
			show_category : show_category,
			show_created_date : show_created_date,
			show_viewed : show_viewed,
			show_comment : show_comment,
			show_read_more : show_read_more,
			show_introduce_category : show_introduce_category,
			col : {
				xxl : col_xxl,
				xl : col_xl,
				lg : col_lg,
				md : col_md,
				sm : col_sm,
				xs : col_xs,
				sp : col_sp,
			},
		};

		name_template_input.val(name);
		item_per_page_input.val(item_per_page);
		item_per_category_input.val(item_per_category);
		col_xxl_input.val(col_xxl);
		col_xl_input.val(col_xl);
		col_lg_input.val(col_lg);
		col_md_input.val(col_md);
		col_sm_input.val(col_sm);
		col_xs_input.val(col_xs);
		col_sp_input.val(col_sp);
		setValueRadio(show_image_input, show_image);
		setValueRadio(show_title_input, show_title);
		setValueRadio(show_description_input, show_description);
		setValueRadio(show_author_input, show_author);
		setValueRadio(show_category_input, show_category);
		setValueRadio(show_created_date_input, show_created_date);
		setValueRadio(show_viewed_input, show_viewed);
		setValueRadio(show_comment_input, show_comment);
		setValueRadio(show_read_more_input, show_read_more);
		setValueRadio(show_introduce_category_input, show_introduce_category);

		if (!$('.temp-table-template').hasClass('edit')){
			$('.form-temp-template').removeClass('hide');
		}
		$('.temp-table-template').addClass('edit');
	});

	function setValueRadio(input, val){
		input.each(function(index) {
			if ($(this).val() == val){
				$(this).prop('checked',true);
			}else{
				$(this).prop('checked',false);
			}
		});
	}

	function getValueRadio(input){
		let result = 0;
		input.each(function(index) {
			if ($(this).is(':checked') && $(this).val() == 1){
				result = 1;
			}
		});

		return result;
	}

	function validateDataImage(data){
		name_input.val(data.name);
		if (data.name == ''){
			showErrorMessage('Name image size is required');
			return false;
		} 
		if (data.width == ''){
			showErrorMessage('Width is required');
			return false;
		}
		if (data.height == ''){
			showErrorMessage('Height is required');
			return false;
		}

		if (isNaN(data.width)){
			showErrorMessage('Width must be number');
			return false;
		}
		if (isNaN(data.height)){
			showErrorMessage('Height must be number');
			return false;
		}

		let exist = false;
		if (!$('.temp-table-image').hasClass('edit')){
			$.each(data_image_size, function( key, val ){
				if (key == data.name){
					showErrorMessage('Image size has exist');
					exist = true;
					return false;
				}
			});
		}

		if (exist){
			return false;
		}

		return true;
	}

	function createRowImage(data){
		// let category_class = (data.category) ? ' checked' : '';
		let blog_class = (data.blog) ? ' checked' : '';
		let blog_thumb_class = (data.blog_thumb) ? ' checked' : '';

		let html = '';

		html += '<tr id="'+data.name+'">';
			html += '<td class="name">'+data.name+'</td>';
			html += '<td class="width">'+data.width+'</td>';
			html += '<td class="height">'+data.height+'</td>';
			html += '<td class="rate">'+data.rate+'</td>';
			// html += '<td class="category'+category_class+'"></td>';
			// html += '<td class="blog'+blog_class+'"></td>';
			// html += '<td class="blog_thumb'+blog_thumb_class+'"></td>';
			html += '<td class="actions"><a href="javascript:void(0);" class="edit text-info"><i class="icon-cog"></i></a><a href="javascript:void(0);" class="remove text-danger"><i class="icon-trash"></i></a></td>';
		html += '</tr>';

		return html;
	}

	function convertName(name) {
	    return name.toString()               // Convert to string
			.normalize('NFD')               // Change diacritics
			.replace(/[\u0300-\u036f]/g,'') // Remove illegal characters
			.replace(/\s+/g,'_')            // Change whitespace to dashes
			.toLowerCase()                  // Change to lowercase
			.replace(/&/g,'-and-')          // Replace ampersand
			.replace(/[^a-z0-9\_]/g,'')     // Remove anything that is not a letter, number or dash
			.replace(/_+/g,'_')             // Change duplicate dashes
			.replace(/^_*/,'')              // Remove starting dashes
			.replace(/_*$/,'');             // Remove trailing dashes
	}

	function validateDataTemplate(data){
		if (isNaN(data.item_per_page)){
			showErrorMessage('Item per page must be number');
			return false;
		}

		return true;
	}

	function createRowTemplate(data){
		let show_image_class = (data.show_image) ? ' checked' : '';
		let show_title_class = (data.show_title) ? ' checked' : '';
		let show_description_class = (data.show_description) ? ' checked' : '';
		let show_author_class = (data.show_author) ? ' checked' : '';
		let show_category_class = (data.show_category) ? ' checked' : '';
		let show_created_date_class = (data.show_created_date) ? ' checked' : '';
		let show_viewed_class = (data.show_viewed) ? ' checked' : '';
		let show_comment_class = (data.show_comment) ? ' checked' : '';
		let show_read_more_class = (data.show_read_more) ? ' checked' : '';
		let show_introduce_category_class = (data.show_introduce_category) ? ' checked' : '';

		let html = '';

		html += '<tr id="'+data.name+'">';
			html += '<td class="name">'+data.name+'</td>';
			html += '<td class="item_per_category">'+data.item_per_category+'</td>';
			html += '<td class="item_per_page">'+data.item_per_page+'</td>';
			html += '<td class="responsive configures-inner">';
				html += '<table class="table-inner">';
					html += '<tr>';
						html += '<td class="title-name">Desktop Large</td>';
						html += '<td class="title-value col_xxl">'+data.col.xxl+'</td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Desktop</td>';
						html += '<td class="title-value col_xl">'+data.col.xl+'</td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Large</td>';
						html += '<td class="title-value col_lg">'+data.col.lg+'</td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Tablet</td>';
						html += '<td class="title-value col_md">'+data.col.md+'</td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Small Tablet</td>';
						html += '<td class="title-value col_sm">'+data.col.sm+'</td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Mobile</td>';
						html += '<td class="title-value col_xs">'+data.col.xs+'</td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Small Mobile</td>';
						html += '<td class="title-value col_sp">'+data.col.sp+'</td>';
					html += '</tr>';
				html += '</table>';
			html += '</td>';
			html += '<td class="blog-item configures-inner">';
				html += '<table class="table-inner">';
					html += '<tr>';
						html += '<td class="title-name">Show Introduce Category</td>';
						html += '<td class="title-value show_introduce_category'+show_introduce_category_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Image</td>';
						html += '<td class="title-value show_image'+show_image_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Title</td>';
						html += '<td class="title-value show_title'+show_title_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Description</td>';
						html += '<td class="title-value show_description'+show_description_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Author</td>';
						html += '<td class="title-value show_author'+show_author_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Category</td>';
						html += '<td class="title-value show_category'+show_category_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Created Date</td>';
						html += '<td class="title-value show_created_date'+show_created_date_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Viewed</td>';
						html += '<td class="title-value show_viewed'+show_viewed_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Comment</td>';
						html += '<td class="title-value show_comment'+show_comment_class+'"></td>';
					html += '</tr>';
					html += '<tr>';
						html += '<td class="title-name">Show Read More</td>';
						html += '<td class="title-value show_read_more'+show_read_more_class+'"></td>';
					html += '</tr>';
				html += '</table>';
			html += '</td>';
			html += '<td class="actions"><a href="javascript:void(0);" class="edit text-info"><i class="icon-edit"></i></a></td>';
		html += '</tr>';

		return html;
	}
});