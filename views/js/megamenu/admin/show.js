/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

var imgId = null;
var selected_images = {};
var selected_rate_images = {};

$(document).ready(function(){
	initWidgetForm();
	$(document).on("change",".checkbox-group",function(e){
		id = $(this).attr('id');
		if($(this).is(':checked'))
			$('.'+id).show();
		else
			$('.'+id).hide();
	});
	$('.checkbox-group').trigger("change");
	$(document).on("change","#source",function(e){
		$('.group-select-change').hide();
		let val = $(this).val();
		$(".group-"+val).show(500);
		if (val != 'pproductids')
			$(".group-limit").show(500);
	});
	$(document).on("change","#ip_source",function(e){
		$("#ip_pcategories").closest(".form-group").hide();
		$("#ip_pproductids").closest(".form-group").hide();
		let val = $(this).val();
		$("#"+val).closest(".form-group").show(500);
	});
	$(document).on("click","#image_cate_tree input[type=checkbox]",function(e){
		if($(this).is(":checked")){
			//find parent category
			//all parent category must be not checked
			let check = checkParentNodes($(this));
			if(!check){          
			    $(this).prop("checked",false);
			    alert("All parent of this category must be not checked"); 
			}
		}else{
			$(".list-image-"+$(this).val()).remove();
			delete  selected_images[$(this).val()];
		}
	});
	// $(document).on("click",".list-image a",function(e){
	// 	let selText = $(this).text();
	// 	$(this).parents('.btn-group').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
	// 	$(this).parents('.btn-group').find('.dropdown-menu').hide();
	// 	if (selText != "none"){
	// 		cate_id = $(this).parents('.btn-group').find('.dropdown-toggle').closest("li").find("input[type=checkbox]").val();
	// 		selected_images[cate_id] = selText.trim();
	// 	}
	// 	return false;
	// });
	$(document).on("click",".dropdown-toggle",function(e){
		$(this).parents('.btn-group').find('.dropdown-menu').show();
		return false;
	});
	$(document).on("mouseleave",".list-image .dropdown-menu",function(e){
		$(".list-image .dropdown-menu").hide();
		return false;
	});
	// $(document).on("click",'[name="savedeowidget"].sub_categories',function(e){
	// 	$("#category_img").val(JSON.stringify(selected_images));

	// });
	// $(document).on("click",'[name="saveandstaydeowidget"].sub_categories',function(e){
	// 	$("#category_img").val(JSON.stringify(selected_images));
	// });
	$(document).on("change","#link_type",_updateLinkType);

	// add new link
	$(document).delegate('.add-new-link', 'click', function(e){ 
		// console.log('aaa');
		e.preventDefault();
		
		// var total_link = parseInt($("#total_link").val()) + 1;
		let total_link = getMaxIndex();
		let i=0;
		let new_link_tmp = '';
		$('.parent-tmp.hidden').each(function(){
			if (i == 0) {
				//$('.add-new-link').closest('.form-group').parent().append('<div class="link_group">');
				new_link_tmp += '<div class="link_group new">';
			}
			new_link_tmp += '<div class="form-group">'+$(this).html()+'</div>';
			// $('.add-new-link').closest('.form-group').parent().append('<div class="form-group new">'+$(this).html()+'</div>');
			i++;
			if (i == $('.parent-tmp.hidden').length) {
				// console.log('test');
				// $('.add-new-link').closest('.form-group').parent().append('</div>');
					new_link_tmp += "<div class='form-group'>";
						new_link_tmp += "<div class='col-lg-4'></div>";
						new_link_tmp += "<div class='col-lg-8'>";
							new_link_tmp += "<a href='javascript:void(0)' class='text-primary duplicate_link'>"+duplicate_button_text+"</a>";
							new_link_tmp += "<a href='javascript:void(0)' class='text-danger remove_link'>"+remove_button_text+"</a>";
						new_link_tmp += '</div>';
					new_link_tmp += '</div>';
				new_link_tmp += '</div>';
			}
				
		});
		$('.add-new-link').closest('.form-group').parent().append(new_link_tmp);
		$('.link_group.new').data('index',total_link);
		updateNewLink(total_link, true , 0);
		
	});
	
	// duplicate link - block link
	$('.duplicate_link').live('click',function(e){
		e.preventDefault();
		//var html_duplicate = $(this).closest('.link_group').html();
		let html_duplicate = $(this).closest('.link_group').clone().prop('class', 'link_group new');
		// console.log(html_duplicate);
		//html_duplicate.filter('.link_group').prop('class', 'link_group new');
		//var total_link = parseInt($("#total_link").val()) + 1;
		let total_link = getMaxIndex();
		$(this).closest('.link_group').after(html_duplicate);
		let current_index = $(this).closest('.link_group').data('index');
		$('.link_group.new').data('index',total_link);
		updateNewLink(total_link, false, current_index);
	});
	
	// remove link - block link
	$('.remove_link').live('click',function(e){
		e.preventDefault();
		if (confirm('Are you sure you want to delete?')) {
			//console.log($(this).find('.tmp'));
			$(this).closest('.link_group').find('.tmp').each(function(){
				// console.log($(this).attr('name'));
				let name_val = $(this).attr('name');
				
				if($(this).closest(".translatable-field").length)
				{
					name_val = name_val.substring(0, name_val.lastIndexOf('_'));
					updateField('remove',name_val,true);
				}
				else
				{
					updateField('remove',name_val,false);
				}
			});
			
			$(this).closest('.link_group').fadeOut(function(){
				$(this).remove();
				// $(".link_group:odd").css( "background-color", "#DAE4F0" );
				// $(".link_group:even").css( "background-color", "#FFFFFF" );
				let total_link = parseInt($("#total_link").val())-1;
				$("#total_link").val(total_link);
				
				$('#list_id_link').val('');
				$('.link_group').each(function(){
					$('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
				})
			});
			
		}	
	});
	
	// copy to other language - block link
	$('.copy_lang_value').live('click',function(e){
		e.preventDefault();
		// console.log('test');
		// console.log($(this).parent().find('.translatable-field:visible'));
		let value_copy = $(this).parent().find('.translatable-field:visible input').val();
		// console.log($(this).parent().find('.translatable-field:hidden'));
		$(this).parent().find('.translatable-field:hidden input').val(value_copy);
		if (typeof copy_lang_button_text_done !== 'undefined' && typeof copy_lang_button_text !== 'undefined'){
			$(this).text(copy_lang_button_text_done);
			let ele_obj = $(this);
			//copy_lang_button_text_done
			setTimeout(function(){ 
				ele_obj.text(copy_lang_button_text); 
			}, 2000);
		}
	});
	
	// update value of input select - block link
	$('.link_group select').live('change',function(){
		if($(this).val() != $(this).find('option[selected=selected]').val()){
			$(this).find('option[selected=selected]').removeAttr("selected");
			$(this).find('option[value='+$(this).val()+']').attr('selected','selected');
		}
	});

	$('.choose-img-extend').live('click',function(e){
	// $(document).on("click", ".choose-img-extend", function (e) {
		e.preventDefault();
		let link = $(this);
		// Store object image for hold the destination after select back
		imgId = $(link).data("for");
		$.ajax({
			url: $(link).attr("href"),
			beforeSend: function () {
				$("#deo_loading").show();
			},
			success: function (response) {
				$("#modal_select_image .modal-body").html(response);
				$("#modal_select_image .modal-body").css('min-height', $(window).height() * 0.8);
				$("#modal_select_image").modal('show');
				$(".image-manager").addClass('extend');
				$(".img-link").tooltip();
			},
			complete: function () {
				$("#deo_loading").hide();
			}
		});
		return false;
	});

	$('.selectImg.lang .reset-img').live('click',function(e){
	// $(document).on("click", ".selectImg.lang .reset-img", function (e) {
		e.preventDefault();

		$(this).closest('.translatable-field').find('.img-thumbnail').attr('src', '');
		$(this).closest('.translatable-field').find('.img-value').attr('value', '_JS_EMPTY_VALUE_');
		$(this).closest('.translatable-field').find('.img-thumbnail').hide();
		$('.calculate-rate-image').trigger('click');
		return false;
	});

	$('#modal_select_image').on('hidden.bs.modal', function () {
		$(".image-manager.extend").removeClass('extend');
	});
	
	$('.image-manager.extend .img-link').live('click',function(e){
	// $(document).on("click", ".image-manager.extend .img-link", function (e) {
		e.stopPropagation();
		let img = $(this).find("img");
		$("#s-image").removeClass("hidden");
		let name = $(img).attr("src");
		$(imgId).val($(img).attr("data-folder")+'/'+$(img).attr("data-name"));
		

		let div = $(imgId).closest("div");
		imgDest = $(div).find("img");
		
		let widget = $(img).attr("data-widget");
		if (imgDest.length > 0){
			$(imgDest).attr("src", $(img).attr("src"));
			$(imgDest).data("img", $(img).data("name"));
			$(imgDest).show();
			if ($(imgDest).attr("widget") === "DeoCategoryMenu"){
				$(imgDest).closest(".list-image").find(".remove-img").removeClass("hidden");
				$(imgDest).removeClass("hidden");
				$(imgDest).attr("src-url", $(img).attr("data-folder")+'/'+$(img).attr("data-name"));
				$(imgDest).data('img', $(img).attr("data-folder")+'/'+$(img).attr("data-name"));
				updateStatusCheck(imgDest);
			}else{
				
			}
		}else{
			$(div).prepend("<img src='" + $(img).attr("src") + "' class='img-thumbnail' data-img='" + $(img).attr("data-name") + "'/>");
		}

		
		$("#modal_select_image").modal('hide');
		setTimeout(function(){
			$('.calculate-rate-image').trigger('click');
		}, 1000);
		
		return false;
	});

	$('.calculate-rate-image').live('click',function(e){
	// $('.calculate-rate-image').click(function () {
		if ($(this).data('widget') === 'DeoCategoryMenu'){
			$('.virtual-image').empty();
			let array_rate_image = {};
			$(".list-image", $(".form-select-icon")).each(function() {
				let checkbox = $(this).closest("span").find("input[type='checkbox']").first();
				let img = $(this).find('img').first();
				if(img.attr("src-url") != '') {
					let rate_image = calculate_rate_image(img.prop('naturalWidth'),img.prop('naturalHeight'));
					rate_image = rate_image ? rate_image : 0;
					array_rate_image[$(checkbox).val()] = rate_image;
				}
			});
			$("#rate_image").val(JSON.stringify(array_rate_image));
		}
	});

	$('.remove-img').live('click',function(e){
	// $(document).on("click", ".remove-img", function (e) {
		e.stopPropagation();
		let img = $(this).closest(".list-image").find("img");
		$(img).attr("src-url", "");
		$(img).attr("src", "");
		$(img).addClass("hidden");

		updateStatusCheck(img);
		$('.calculate-rate-image').trigger('click');
	});
	$('.tree-folder-name input:checkbox').live('change',function(){
	// $(".tree-folder-name input:checkbox").change(function () {
		$(this).find("input:checkbox").removeAttr("checked");
	});
});


function initWidgetForm(){
	$(document).ready(function(){
		$(".image-choose").DeoImageSelector();
		toogle_link_viewall_category_image();

		$(".image-choose-icon").DeoImageSelector({
			name : 'icon_image',
			name_lazyload : 'icon_lazyload',
			name_rate_image : 'icon_rate_image',
			name_preview_image_link : 'icon_image_link',
			name_use_image_link : 'icon_use_image_link',
			class_calc_rate_image_group : '.group_calc_rate_image_icon',
			class_rate_lazyload_group : '.rate_lazyload_group_icon',
			class_select_image_link_group : '.select_image_link_group_icon',
		});
	})
	// initImageSelect();
	$("#pcategories").closest(".form-group").hide();
	$("#ptype").closest(".form-group").hide();
	$("#pproductids").closest(".form-group").hide();
	$("#pmanufacturers").closest(".form-group").hide();

	$("#categorybox").addClass('full_loaded');
	$('#collapse-all-categorybox').hide();

	$("#source option:selected").each(function() {
		$("#limit").closest(".form-group").hide();
		let val = $(this).val();
		$("#"+val).closest(".form-group").show(500);
		if( val != 'pproductids'){
			$("#limit").closest(".form-group").show(500);
		}
	});

	//for imageproduct widget
	$("#ip_pcategories").closest(".form-group").hide();
	$("#ip_pproductids").closest(".form-group").hide();
	$( "#ip_source option:selected" ).each(function() {
		let val = $(this).val();
		$("#"+val).closest(".form-group").show();
	});

	//for imageproduct widget
	$("#ip_pcategories").closest(".form-group").hide();
	$("#ip_pproductids").closest(".form-group").hide();
	$( "#ip_source option:selected" ).each(function() {
		let val = $(this).val();
		$("#"+val).closest(".form-group").show();
	});

	//done for imageproduct widget
	//for category_image widget
	//hide checkbox of root node
	$("input[type=checkbox]", "#categorybox").first().hide();
	let root_id = $("input[type=checkbox]", "#categorybox").first().val();
	Array.prototype.remove = function(v) { this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1); }
	if ($("#category_img").val()){
		selected_images = JSON.parse($("#category_img").val());
	}
	if ($("#rate_image").val()){
		selected_rate_images = JSON.parse($("#rate_image").val());
	}
	$("input[type=checkbox]", "#categorybox").click(function(){
		if ($(this).is(":checked")) {
			// find parent category
			// let parent_checked = checkParentNodes($(this));
			// if (parent_checked){
			// 	$(this).closest("ul").find("ul input[type=checkbox]").removeAttr("checked");
			// } else {
			// 	// all parent category must be not checked
			// 	$(this).prop("checked", false);
			// 	alert("All parent of this category must be not checked"); 
			// }
		} else {
			//$(".list-image-" + $(this).val()).remove();
			delete selected_images[$(this).val()];
		}
		$("#category_img").val(JSON.stringify(selected_images));
	});



	// show selected_image when loaded page
	$("input[type=checkbox]", $(".form-select-icon")).each(function(){
		let listImage;
		if($(this).val() != root_id){
			listImage = $(".list-image", "#list_image_wrapper").clone(1);
			let d = new Date();
			let n = "" + d.getTime() +  Math.random();
			n = n.replace(".", "");
			let span = $(this).closest("li").find("span");
			$(listImage).find("img").attr("id", "apci_" + n);
			$(listImage).find("a").data("for", "#apci_" + n);
			listImage.appendTo($(span).first());
		}
		for(let key in selected_images){
			if(key == $(this).val()){
				image_name = selected_images[key];
				if(listImage) {
					let path = $(listImage).find("img").attr("path");
					$(listImage).find("img").attr("src", path + image_name);
					$(listImage).find("img").attr("src-url", image_name);
					$(listImage).find("img").removeClass("hidden");
					$(listImage).find(".remove-img").removeClass("hidden");
				}
				//listImage.find(".dropdown-toggle").html(image_name+' <span class="caret"></span>');
				// Set status for checkbox
				//$(this).attr("checked", "checked");
				break;
			}
		}
		$("#category_img").val(JSON.stringify(selected_images));
		//$(this).closest("ul.tree").css("display", "none");
	});
	
	// update link type
	_updateLinkType(); 
	let array_id_lang = [];
	if (typeof list_id_lang !== "undefined") {
		array_id_lang = $.parseJSON(list_id_lang);
	}
	
	// hiden tmp form
	$('.tmp').each(function(){
		if ($(this).closest(".translatable-field").length) {
			// console.log($(this).closest(".form-group"));
			// console.log($(this).closest(".form-group").closest(".form-group"));
			if ($(this).hasClass('element')) {
				let id = $(this).attr('id');
				id = id.substring(0, id.lastIndexOf('_'));
				let index = id.substring(id.lastIndexOf('_')+1);
				
				$(this).closest(".form-group").parents(".form-group").addClass('element-tmp hidden element-'+index);
			}else{
				$(this).closest(".form-group").parents(".form-group").addClass('parent-tmp hidden');
			}
			
			if (!$(this).closest(".form-group").find('.copy_lang_value').length && typeof copy_lang_button_text !== 'undefined')
				$(this).closest(".form-group").append('<a href="javascript:void(0)" class="text-info copy_lang_value">'+copy_lang_button_text+'</a>');
		}else{
			if ($(this).hasClass('element')) {
				
				let id = $(this).attr('id');
				if(array_id_lang.length == 1 && $(this).hasClass('element-lang')){
					// console.log(array_id_lang.length);
					id = id.substring(0, id.lastIndexOf('_'));
				}
				
				let index = id.substring(id.lastIndexOf('_')+1);;
				// console.log(index);
				$(this).closest(".form-group").addClass('element-tmp hidden element-'+index);
				
			}else{
				$(this).closest(".form-group").addClass('parent-tmp hidden');
			}
		}
	});
	
	// display link group when edit block link
	if ($('#list_id_link').length && $('#list_id_link').val() != '') {
		let list_id_link = $('#list_id_link').val().split(',');
		let button_tmp = "<div class='form-group'>";
				button_tmp += "<div class='col-lg-4'></div>";
				button_tmp += "<div class='col-lg-8'>";
					button_tmp += "<a href='javascript:void(0)' class='text-primary duplicate_link'>"+duplicate_button_text+"</a>";
					button_tmp += "<a href='javascript:void(0)' class='text-danger remove_link'>"+remove_button_text+"</a>";
				button_tmp += '</div>';
			button_tmp += '</div>';
		button_tmp += '</div>';
		$.each(list_id_link, function( index, value ) {
			if (value != ''){
				//$("[id^=text_link_"+value+"]");
				// if($("[id^=text_link_"+value+"]").closest('.form-group').find('.translatable-field').length)
					// $("[id^=text_link_"+value+"]").closest('.form-group').parents('.element-tmp').before('<div class="link_group new">');
				// else
					// $("[id^=text_link_"+value+"]").closest('.element-tmp').before('<div class="link_group new">');
				
				// if($("[id^=controller_type_parameter_"+value+"]").closest('.form-group').find('.translatable-field').length)
					// $("[id^=controller_type_parameter_"+value+"]").closest('.form-group').parents('.element-tmp').after(button_tmp);
				// else
					// $("[id^=controller_type_parameter_"+value+"]").closest('.element-tmp').after(button_tmp);
				$('.element-'+value).wrapAll('<div class="link_group new">');
				$('.link_group.new').append(button_tmp);
				$('.link_group.new').data('index',value);
				$('.link_group.new .element-tmp').removeClass('element-tmp hidden');
				$('.link_group.new').removeClass('new');
				_updateLinkType(value);
				$("#link_type_"+value).on('change',function(){
					_updateLinkType(value);
				});
			}
		});
		
		// $(".link_group:odd").css("background-color", "#DAE4F0");
		// $(".link_group:even").css("background-color", "#FFFFFF");
	}
	
	// Check type of Carousel type - BEGIN
	$('.form-action').change(function(){
		elementName = $(this).attr('name');
		$('.'+elementName+'_sub').hide(300);
		$('.'+elementName+'-'+$(this).val()).show(500);
	});
	$('.form-action').trigger("change");
	// Check type of Carousel type - END

	$("#configuration_form").validate({
		rules : {
			owl_items : {
				min : 1,
			},
			owl_rows : {
				min : 1,
			},
		}
	});
	/*
	 * Owl carousel
	 */
	 // $(document).ready(function(){
	   
	 // });
	 
	$.validator.addMethod("owl_items_custom", function(value, element) {
	    pattern_en = /^\[\[[0-9]+, [0-9]+\](, [\[[0-9]+, [0-9]+\])*\]$/;  // [[320, 1], [360, 1]]
	    pattern_dis = /^0?$/
	    //console.clear();
	    //console.log (pattern.test(value));
	    return (pattern_en.test(value) || pattern_dis.test(value));
	    //return false;
	}, "Please enter correctly config follow under example.");
}

function checkParentNodes(obj){
	let flag = true;
	if(parent = obj.closest("ul").closest("li").find("input[type=checkbox]")){
		if(parent.val() != root_id){
			if($("input[value=" + parent.val() + "]","#image_cate_tree").is(":checked")){
				flag = false;
			}else{
				flag = checkParentNodes(parent);                  
			}
		}
	}
	return flag;
}

function getMaxIndex(){
	if($('.link_group').length == 0){
		return 1;
	}else{
		let list_index = [];
		$('.link_group').each(function(){
			list_index.push($(this).data('index'));
		})
		// console.log(list_index);
		return Math.max.apply(Math,list_index) + 1;
		// console.log(total_link);
	}
}

// update when add a new link
function updateNewLink(total_link, scroll_to_new_e, current_index){
	// console.log(id_language);
	let array_id_lang = $.parseJSON(list_id_lang);
	
	updateField('add','text_link_'+total_link,true);
	updateField('add','url_type_'+total_link,true);
	updateField('add','controller_type_parameter_'+total_link,true);
	
	// console.log($('.link_group.new .form-group .tmp').closest(".translatable-field").length);
	$('.link_group.new .form-group .tmp').each(function(){
		let e_obj = $(this);
		if ($(this).closest(".translatable-field").length){
			$.each(array_id_lang, function( index, value ) {
				switch (e_obj.attr('id')) {
					case 'text_link_'+current_index+'_'+value:
						e_obj.attr('id','text_link_'+total_link+'_'+value);
						e_obj.attr('name','text_link_'+total_link+'_'+value);
						
						break;
					case 'url_type_'+current_index+'_'+value:
						e_obj.attr('id','url_type_'+total_link+'_'+value);
						e_obj.attr('name','url_type_'+total_link+'_'+value);
						
						break;
					case 'controller_type_parameter_'+current_index+'_'+value:
						e_obj.attr('id','controller_type_parameter_'+total_link+'_'+value);
						e_obj.attr('name','controller_type_parameter_'+total_link+'_'+value);
						
						break;
				}
			});
		}else{
			if (array_id_lang.length == 1){
				switch (e_obj.attr('id')) {
					case 'text_link_'+current_index+'_'+id_lang:
						e_obj.attr('id','text_link_'+total_link+'_'+id_lang);
						e_obj.attr('name','text_link_'+total_link+'_'+id_lang);
						
						break;
					case 'url_type_'+current_index+'_'+id_lang:
						e_obj.attr('id','url_type_'+total_link+'_'+id_lang);
						e_obj.attr('name','url_type_'+total_link+'_'+id_lang);
						
						break;
					case 'controller_type_parameter_'+current_index+'_'+id_lang:
						e_obj.attr('id','controller_type_parameter_'+total_link+'_'+id_lang);
						e_obj.attr('name','controller_type_parameter_'+total_link+'_'+id_lang);
						
						break;
					default:
						let old_id = e_obj.attr('id');
						let old_name = e_obj.attr('name');
						old_id = old_id.substring(0, old_id.lastIndexOf('_'));
						old_name = old_name.substring(0, old_name.lastIndexOf('_'));
						
						e_obj.attr('id',old_id+'_'+total_link);
						e_obj.attr('name',old_name+'_'+total_link);
						updateField('add',old_name+'_'+total_link, false);
						if(old_id == 'product_type' || old_id == 'cms_type' || old_id == 'category_type' || old_id == 'manufacture_type' || old_id == 'supplier_type' || old_id == 'controller_type')
						{
							if (e_obj.is( "input" ))
							{
								e_obj.attr('class','link_type_group_'+total_link+' tmp');
							}
							
							if (e_obj.is( "select" ))
							{
								e_obj.attr('class','link_type_group_'+total_link+' tmp fixed-width-xl');
							}
						}
						break;
				}
			}else{							
				let old_id = e_obj.attr('id');
				let old_name = e_obj.attr('name');
				old_id = old_id.substring(0, old_id.lastIndexOf('_'));
				old_name = old_name.substring(0, old_name.lastIndexOf('_'));
				e_obj.attr('id',old_id+'_'+total_link);
				e_obj.attr('name',old_name+'_'+total_link);
				updateField('add',old_name+'_'+total_link, false);
				if (old_id == 'product_type' || old_id == 'cms_type' || old_id == 'category_type' || old_id == 'manufacture_type' || old_id == 'supplier_type' || old_id == 'controller_type'){
					if (e_obj.is( "input" )){
						e_obj.attr('class','link_type_group_'+total_link+' tmp');
					}
					
					if (e_obj.is( "select" )){
						e_obj.attr('class','link_type_group_'+total_link+' tmp fixed-width-xl');
					}
				}
			}
		}
	});
	
	_updateLinkType(total_link);
	$("#link_type_"+total_link).on('change',function(){
		_updateLinkType(total_link);
	});
	if (scroll_to_new_e == true){
		// $(".link_group:odd").css("background-color", "#DAE4F0");
		// $(".link_group:even").css("background-color", "#FFFFFF");
	}
	if (scroll_to_new_e == true){
		$('html, body').animate({
			scrollTop: $('.link_group.new').offset().top
		}, 500, function (){
			$('.link_group.new').removeClass('new');
		});

		// scroll for modal
		$('#form-widget').animate({ 
			scrollTop: $('.link_group.new').offset().top
		}, 500);
	}else{
		setTimeout(function(){ 
			$('.link_group.new').removeClass('new'); 
			// $(".link_group:odd").css("background-color", "#DAE4F0");
			// $(".link_group:even").css("background-color", "#FFFFFF");
		}, 500);
	}
	
	$("#total_link").val(total_link);
}

// update list field
function updateField(action, value, is_lang) {
	// console.log('test');
	if (action == 'add'){
		if (is_lang == true){
			$('#list_field_lang').val($('#list_field_lang').val()+value+',');
		}else{
			$('#list_field').val($('#list_field').val()+value+',');
		}
	}else{
		// console.log('test');
		if (is_lang == true) {
			let old_list_field_lang = $('#list_field_lang').val();
			let new_list_field_lang = old_list_field_lang.replace(value,'');
			$('#list_field_lang').val(new_list_field_lang);
		}else{
			let old_list_field = $('#list_field').val();
			let new_list_field = old_list_field.replace(value,'');
			$('#list_field').val(new_list_field);
		}
	}
	
	$('#list_id_link').val('');
	$('.link_group').each(function(){
		$('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
	})	
}

// update link type
function _updateLinkType(total_link) {
	let total_link_new = ""; 
	if (typeof total_link === "undefined" || total_link === null) { 
		total_link = "";
	}else{
		// let total_link_old = total_link;
		total_link_new = '_'+total_link;
	}
	$(".link_type_group"+total_link_new).parent().parent().hide();
	if ($("[id^=url_type_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
		$("[id^=url_type_"+total_link+"]").closest('.form-group').parent().parent().hide();
	else
		$("[id^=url_type_"+total_link+"]").closest('.form-group').hide();
	
	if ($("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
		$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').parent().parent().hide();
	else
		$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').hide();
	
	if ($("[id^=content_text_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
		$("[id^=content_text_"+total_link+"]").closest('.form-group').parent().parent().hide();
	else
		$("[id^=content_text_"+total_link+"]").closest('.form-group').hide();	
	// console.log(total_link);
	// console.log(total_link_new);
	// console.log($("#link_type"+total_link_new).val());
	if ( $("#link_type"+total_link_new).val() =='url' ) {
		if($("[id^=url_type_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
			$("[id^=url_type_"+total_link+"]").closest('.form-group').parent().parent().show();
		else
			$("[id^=url_type_"+total_link+"]").closest('.form-group').show();
	} else {
		$("#"+$("#link_type"+total_link_new).val()+"_type"+total_link_new).parent().parent().show();
		if ($("#link_type"+total_link_new).val() == 'controller') {
			// $("#"+$("#link_type").val()+"_type_parameter").parent().parent().show();
			if ($("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
				$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').parent().parent().show();
			else
				$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').show();
		}
	}
}

function updateStatusCheck(obj) {
	let checkbox = $(obj).closest("span").find("input[type='checkbox']").first();
	if($(obj).attr("src-url") != "") {
		selected_images[$(checkbox).val()] = $(obj).attr("src-url");
		selected_rate_images[$(checkbox).val()] = calculate_rate_image($(obj).prop('naturalWidth'),$(obj).prop('naturalHeight'));
		// Set status for checkbox
		// $(checkbox).attr("checked", "checked");
		$(obj).closest("span").find(".remove-img").removeClass("hidden");	
	} else {
		$(checkbox).removeAttr("checked");
		$(obj).closest("span").find(".remove-img").addClass("hidden");
		delete selected_images[$(checkbox).val()];
		delete selected_rate_images[$(checkbox).val()];
	}

	$("#category_img").val(JSON.stringify(selected_images));
	$("#rate_image").val(JSON.stringify(selected_rate_images));
	return false;

	console.log(selected_images);
}

function toogle_link_viewall_category_image(){
	toogle_switch($('.show_link_viewall input[type="radio"]'),$('.group_show_link_viewall'),400);
	$(document).on('change', '#carousel_type', function(){
		if($(this).val() == 'normal-image-category'){
			$('.show_link_viewall').removeClass('hide-config').show(400,function(){
				$('input[name="viewall"]:checked').trigger('change');
				$('.group_normal-image-category').removeClass('hide-config').show(400);
				
			});
		}else{
			$('.group_viewall').addClass('hide-config').hide(400);
			$('.group_normal-image-category').addClass('hide-config').hide(400);
		}
	});
	$('#carousel_type').trigger('change');
}