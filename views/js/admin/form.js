/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

function initFullSlider(type) {
	let total = parseInt($("#total_slider").val());
	$(".apfullslider-row").addClass("hide");
	$(".apfullslider-row input, .apfullslider-row textarea").removeAttr("name");
}
function updateListIdFullSlider() {
	let listId = "";
	let sep = "";
	$("#list-slider li").each(function() {
		listId += sep + $(this).attr("id");
		sep = "|";
	});
	$("#total_slider").val(listId);
}
$(document).ready(function() {
	$("#modal_form").draggable({
		handle: ".modal-header"
	});
	miniLeftMenu();
	// $('.addnew-group').popover({
	// 	html: true,
	// 	content: function () {
	// 		return $('#addnew-group-form').html();
	// 	}
	// });

	// NOT WORK FOR AJAX
	$('.form-action').change(function(){
		let elementName = $(this).attr('name');
		$('.'+elementName+'_sub').hide();
		$('.'+elementName+'-'+$(this).val()).show();
	});
	$('.form-action').trigger("change");

	$('.checkbox-group').change(function(){
		id = $(this).attr('id');
		if($(this).is(':checked'))
			$('.'+id).show();
		else
			$('.'+id).hide();
	});
	$('.checkbox-group').trigger("change");


	$(document).on("click", ".hook-top", function() {
		$(".hook-content", $(this).parent()).each(function(){
			$(this).toggle('clip');
			let groupTop = $(".open-group i", $(this).parent());
			if($(groupTop).attr('class').indexOf('up') >-1){
				$(groupTop).attr('class',$(groupTop).attr('class').replace('up', 'down'));
			}else{
				$(groupTop).attr('class',$(groupTop).attr('class').replace('down', 'up'));
			}
		});
	});

	$(document).on("click", ".class-container", function() {
		let input = $(this).closest('.form-group').find('input[name="container"]');
		let str = input.val();
		let regex = /\bcontainer\b(?!-)/g; 

		if ($(this).hasClass('add')){
			if (!regex.test(str)){
				str += " container";
			}
		}else{
			var rex = regex.test(str); 
			str = str.replace(regex, "");
		}

		input.val($.trim(str));
	});
	
	// fix can't click tab 1 when create new widget tab
	// $('.DeoTabs .nav-tabs a:first').tab('show');
	// $('.DeoTabs:not(#default_DeoTabs)').each(function(){
		// console.log($(this).data('form'));
		// console.log($(this).attr('form'));
		// let data_form = $(this).data();
		// console.log(data_form.type);
		// console.log(data_form.form);
		// console.log(data_form['form'].active_tab);
		// $(this).find('.nav-tabs a:first').tab('show');
	// }) 
	
	$(".DeoAccordions").each(function(){
	   // $('.panel-collapse:first' , $(this)).collapse('show'); 
	});
	$('.btn-form-toggle').click(function (e) {
		e.preventDefault();
		if ($('.displayLeftColumn').hasClass('col-md-3')) {
			$('i', $(this)).attr('class', 'icon-resize-small');
			$(".hook-content").hide();
			$(".open-group i").attr('class', $(".open-group i").attr('class').replace('down', 'up'));
			$('.displayLeftColumn').removeClass('col-md-3').addClass('col-md-12');
			$('.displayRightColumn').removeClass('col-md-3').addClass('col-md-12');
			$('.home-content-wrapper').removeClass('col-md-6').addClass('col-md-12');
		} else {
			$('i', $(this)).attr('class', 'icon-resize-full');
			$(".hook-content").show();
			$(".open-group i").attr('class', $(".open-group i").attr('class').replace('up', 'down'));
			$('.displayLeftColumn').removeClass('col-md-12').addClass('col-md-3');
			$('.displayRightColumn').removeClass('col-md-12').addClass('col-md-3');
			$('.home-content-wrapper').removeClass('col-md-12').addClass('col-md-6');
		}
	});
	
	$(document).on("click", ".position-cover .show-sidebar", function (e) {
		let left_sidebar = right_sidebar = false;

		if ($(this).is(':checked')){
			$($(this).data('value')).removeClass('hidden');
		}else{
			$($(this).data('value')).addClass('hidden');
		}

		if ($('.left-sidebar').is(':checked')){
			left_sidebar = true;
		}
		if ($('.right-sidebar').is(':checked')){
			right_sidebar = true;
		}

		// console.log(left_sidebar, right_sidebar);
		if (left_sidebar && right_sidebar){
			$('.displayHome').removeClass('col-md-12').removeClass('col-md-9').addClass('col-md-6');
		}else if (left_sidebar || right_sidebar){
			$('.displayHome').removeClass('col-md-12').removeClass('col-md-6').addClass('col-md-9');
		}else{
			$('.displayHome').removeClass('col-md-6').removeClass('col-md-9').addClass('col-md-12');
		}
	});
	$('.position-cover .show-sidebar').trigger('change');

	//only for product generate
	$( ".product-container .content" ).sortable({
		revert: false
	});
	$('.element-list .plist-element').draggable({
		connectToSortable: ".product-container .content",
		revert: "true",
		helper: "clone",
		stop: function() {
			$( ".product-container .content" ).sortable({
				revert: false
			});
		}
	});
	$(document).on("click", "#list-slider .btn-delete-slider", function() {
		if(confirm($("#form_content").data("delete"))) {
			$(this).closest("li").remove();
			$("#frm-slider").removeAttr("edit");
			updateListIdFullSlider();
		}
	});
	$(document).on("click", "#list-slider .btn-delete-fullslider", function() {
		if(confirm($("#form_content").data("delete"))) {
			$(this).closest("li").remove();
			$("#frm-slider").removeAttr("edit");
			updateListIdFullSlider();
		}
	});
	$(document).on("click", "#btn-add-slider", function() {
		$("#frm-slider, .apfullslider-row, #frm-block-slider").removeClass("hide");
		$(".btn-reset-slider, .btn-reset-fullslider").trigger("click");
		$("#frm-slider, #frm-block-slider").removeAttr("edit");
		$(".image-choose-temp").DeoImageSelector({
			name : 'temp_image',
			name_lazyload : 'temp_lazyload',
			name_rate_image : 'temp_rate_image',
			name_preview_image_link : 'temp_image_link',
			name_use_image_link : 'temp_use_image_link',
			class_calc_rate_image_group : '.group_calc_rate_image_temp',
			class_rate_lazyload_group : '.rate_lazyload_group_temp',
			class_select_image_link_group : '.select_image_link_group_temp',
		});
	});
	$(document).on("click", ".btn-cancel-slider, .btn-cancel-fullslider", function() {
		$("#frm-slider, .apfullslider-row, #frm-block-slider").addClass("hide");
	});
	// $(document).on("click", ".btn-reset-slider", function() {
	// 	$("#frm-slider, #frm-block-slider").removeAttr("edit");
	// 	$("#s-open").removeAttr("checked");
	// 	$("#s-image").attr("src", "");
	// 	$("#s-image").hide();
	// 	$("#frm-slider input, #frm-slider textarea, #frm-block-slider input, #frm-block-slider textarea").val("");
	// 	$("#s-tit").focus();
	// });
	$(document).on("click", ".btn-reset-fullslider, .btn-reset-slider", function() {
		$("#frm-slider, #frm-block-slider").removeAttr("edit");
		$(".apfullslider-row img").attr("src", "").hide();
		$(".apfullslider-row input, .apfullslider-row textarea").val("");
	});
	$(document).on("click", ".btn-edit-slider", function() {
		let li = $(this).closest("li");
		let idRow = $(li).attr("id");
		let lengthLang = Object.keys($globalthis.languages).length;
		$("#frm-slider, .apfullslider-row").removeClass("hide");
		$("#frm-slider").attr("edit", $(li).attr("id"));

		if(lengthLang > 1) {
			$(".select-img .translatable-field").each(function() {
				currentLang = $(this).data("lang");
				let tempId = idRow + "_" + currentLang;
				let img = $(li).find("#img_" + tempId).val();
				let title = $(li).find("#tit_" + tempId).val();
				let link= $(li).find("#link_" + tempId).val();
				let descript = $(li).find("#descript_" + tempId).val();
				$("#temp_title_" + currentLang).val(title);
				$("#temp_image_" + currentLang).val(img);
				// Check only diplay image for language
				if(img) {
					if($(".select-img .lang-" + currentLang).find("img").length == 0) {
						$(".select-img .lang-" + currentLang + " div:first-child").prepend("<img src='" + img + "' class='img-thumbnail'/>");
					} else {
						$(".select-img .lang-" + currentLang).find("img").attr("src", img);
						$(".select-img .lang-" + currentLang).find("img").css("display", "block");
					}
				}
				$("#temp_link_" + currentLang).val(link);
				$(".description-slide .lang-"  + currentLang + " textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
			});
		} else {
			currentLang = default_language;
			let tempId = idRow + "_" + currentLang;
			let img = $(li).find("#img_" + tempId).val();
			let title = $(li).find("#tit_" + tempId).val();
			let link= $(li).find("#link_" + tempId).val();
			let descript = $(li).find("#descript_" + tempId).val();
			$("#temp_title_" + currentLang).val(title);
			$("#temp_image_" + currentLang).val(img);
			// Check only diplay image for language
			if(img) {
				if($(".select-img img").length == 0) {
					$(".select-img div:first-child").prepend("<img src='" + img + "' class='img-thumbnail'/>");
				} else if(img) {
					$(".select-img img").attr("src", img);
				}
			}
			$("#temp_link_" + currentLang).val(link);
			$(".description-slide textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
		}
	});
	$(document).on("click", ".btn-edit-fullslider", function() {
		let li = $(this).closest("li");
		let idRow = $(li).attr("id");
		let lengthLang = Object.keys($globalthis.languages).length;
		$("#frm-slider, .apfullslider-row").removeClass("hide");
		$("#frm-slider").attr("edit", $(li).attr("id"));

		if(lengthLang > 1) {
			$(".select-img .translatable-field").each(function() {
				let currentLang = $(this).data("lang");
				let tempId = idRow + "_" + currentLang;
				let img = $(li).find("#img_" + tempId).val();
				let imgLink = imgModuleLink+img;
				let rate_image = $(li).find("#rate_image_" + tempId).val();
				let title = $(li).find("#tit_" + tempId).val();
				let sub_title = $(li).find("#sub_tit_" + tempId).val();
				let link= $(li).find("#link_" + tempId).val();
				let descript = $(li).find("#descript_" + tempId).val();
				$("#temp_title_" + currentLang).val(title);
				$("#temp_sub_title_" + currentLang).val(sub_title);
				$("#temp_image_" + currentLang).val(img);
				$("#temp_rate_image_" + currentLang).val(rate_image);
				// Check only diplay image for language
				if(img) {
					if($(".select-img .lang-" + currentLang).find("img").length == 0) {
						$(".select-img .lang-" + currentLang + " div").first().prepend("<img src='" + imgLink + "' class='img-thumbnail'/>");
					} else if(img) {
						$(".select-img .lang-" + currentLang).find("img").attr("src", imgLink);
						$(".select-img .lang-" + currentLang).find("img").css("display", "block");
					}
				}else{
					// NOT EXIST IMAGE
					$(".select-img .lang-" + currentLang).find("img").css("display", "none");
				}
				$("#temp_link_" + currentLang).val(link);
				$(".description-slide .lang-"  + currentLang + " textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
			});
		} else {
			let currentLang = default_language;
			let tempId = idRow + "_" + currentLang;
			let img = $(li).find("#img_" + tempId).val();
			let imgLink = imgModuleLink+img;
			let rate_image = $(li).find("#rate_image_" + tempId).val();
			let title = $(li).find("#tit_" + tempId).val();
			let sub_title = $(li).find("#sub_tit_" + tempId).val();
			let link= $(li).find("#link_" + tempId).val();
			let descript = $(li).find("#descript_" + tempId).val();
			$("#temp_title_" + currentLang).val(title);
			$("#temp_sub_title_" + currentLang).val(sub_title);
			$("#temp_image_" + currentLang).val(img);
			$("#temp_rate_image_" + currentLang).val(rate_image);
			// Check only diplay image for language
			if(img) {
				if($(".select-img img").length == 0) {
					$(".select-img .selectImg div").first().prepend("<img src='" + imgLink + "' class='img-thumbnail'/>");
				} else if(img) {
					$(".select-img img").attr("src", imgLink);
					$(".select-img img").css("display", "block");
				}
			}else{
				// NOT EXIST IMAGE
				$(".select-img").find("img").css("display", "none");
			}
			$("#temp_link_" + currentLang).val(link);
			$(".description-slide textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
		}
		$(".image-choose-temp").DeoImageSelector({
			name : 'temp_image',
			name_lazyload : 'temp_lazyload',
			name_rate_image : 'temp_rate_image',
			name_preview_image_link : 'temp_image_link',
			name_use_image_link : 'temp_use_image_link',
			class_calc_rate_image_group : '.group_calc_rate_image_temp',
			class_rate_lazyload_group : '.rate_lazyload_group_temp',
			class_select_image_link_group : '.select_image_link_group_temp',
		});

		scrollToModal($('#modal_form'),$('#btn-add-slider'));
	});
	$(document).on("click", ".btn-save-slider", function() {
		// Validate
		// Get current language code selected
		let currentLang = default_language;
		let lengthLang = Object.keys($globalthis.languages).length;
		let temp_title = lengthLang > 1 ? ".title-slide .lang-" + default_language + " input" : ".title-slide input";
		let title = $.trim($(temp_title).val());
		let temp_sub_title = lengthLang > 1 ? ".sub-title-slide .lang-" + default_language + " input" : ".sub-title-slide input";
		let sub_title = $.trim($(temp_sub_title).val());
		let temp_image = lengthLang > 1 ? ".select-img .lang-" + default_language + " img" : ".select-img img";
		let image = $.trim($(temp_image).attr("src"));
		let imageName = $.trim($(temp_image).data("img"));
		let temp_rate_image = lengthLang > 1 ? ".rate_value .lang-" + default_language + " input" : ".rate-image input";
		let rate_image = $.trim($(temp_rate_image).val());
		let temp_link = lengthLang > 1 ? ".link-slide .lang-" + default_language + " input" : ".link-slide input";
		let link = $.trim($(temp_link).val());
		let temp_description = lengthLang > 1 ? ".description-slide .lang-" + default_language + " textarea" : ".description-slide textarea";
		let description = $.trim($(temp_description).val());
		let countLimit = 0;
		if(!image) {
			countLimit++;
		}
		if(!rate_image) {
			countLimit++;
		}
		if(!title) {
			countLimit++;
		}
		if(!sub_title) {
			countLimit++;
		}
		if(!description) {
			countLimit++;
		}
		// Require enter value for one in of [image, title, sub_title, description, rate_image]
		if(countLimit == 5) {
			alert($(this).data("error"));
			return;
		}
		
		let idForm = "#frm-slider";
		let idRow = (typeof $(idForm).attr("edit") != "undefined") ? $(idForm).attr("edit") : "";
		if(!idRow) {
			let html = $("#temp-list li:first").html();
			idRow = 1;
			let arr = $("#total_slider").val().split("|");
			arr.sort(function (a, b) { return a - b; });
			for(let i = 0; i < arr.length; i++) {
				if(idRow != arr[i]) {
					break;
				}
				idRow++;
			}
			if(lengthLang > 1) {
				// console.log(idRow);
				// Duplicate for new slider and build name and id by language
				$(".select-img .translatable-field").each(function() {
					currentLang = $(this).data("lang");
					let tempId = idRow + "_" + currentLang + "'";
					html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + "/>";
					html += "<input type='hidden' name='sub_tit_" + tempId + " id='sub_tit_" + tempId + "/>";
					html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + "/>";
					html += "<input type='hidden' name='rate_image_" + tempId + " id='rate_image_" + tempId + "/>";
					html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + "/>";
					html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + "/>";
				});
			} else {
				let tempId = idRow + "_" + currentLang + "'";
				html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + " value='" + title + "'/>";
				html += "<input type='hidden' name='sub_tit_" + tempId + " id='sub_tit_" + tempId + " value='" + sub_title + "'/>";
				html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + " value='" + imageName + "'/>";
				html += "<input type='hidden' name='rate_image_" + tempId + " id='rate_image_" + tempId + " value='" + rate_image + "'/>";
				html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + " value='" + link + "'/>";
				html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + " value='" + description + "'/>";
			}
			$("#list-slider").prepend("<li id='" + idRow + "'>" + html + "</li>");
		}
		// Update labels for diplay interface
		let label = (title ? '<div class="col-lg-5">'+ title +'</div>' : "");
		label += (image ? '<img src="' + image + '">': "");
		$("#" + idRow + " div:first").html(label);
				
		if(lengthLang > 1) {
			// Update value for other language by default language and save to dum hidden fields
			$(".select-img .translatable-field").each(function() {
				currentLang = $(this).data("lang");
				let titleOther = $.trim($(".title-slide .lang-" + currentLang + " input").val());
				let subtitleOther = $.trim($(".sub-title-slide .lang-" + currentLang + " input").val());
				let imageOther = $.trim($(".select-img #temp_image_" + currentLang).val());
				let rateimageOther = $.trim($(".rate_value .lang-" + currentLang + " input").val());
				let linkOther = $.trim($(".link-slide .lang-" + currentLang + " input").val());
				let descriptionOther = $.trim($(".description-slide .lang-" + currentLang + " textarea").val());
				if(currentLang != default_language) {
					if(!titleOther) {
						titleOther = title;
						$(".title-slide .lang-" + currentLang + " input").val(title);
					}
					if(!subtitleOther) {
						subtitleOther = sub_title;
						$(".sub-title-slide .lang-" + currentLang + " input").val(sub_title);
					}
					if(!imageOther) {
						imageOther = imageName;
						$(".select-img .lang-" + currentLang + " input").val(imageName);
					}
					if(!rateimageOther) {
						rateimageOther = rate_image;
						$(".rate_value .lang-" + currentLang + " input").val(rate_image);
					}
					if(!linkOther) {
						linkOther = link;
						$(".link-slide .lang-" + currentLang + " input").val(link);
					}
					if(!descriptionOther) {
						descriptionOther = description;
						$(".description-slide .lang-" + currentLang + " textarea").val(description);
					}
				}
				let tempId = idRow + "_" + currentLang;
								
				$("#tit_" + tempId).val(titleOther);
				$("#sub_tit_" + tempId).val(subtitleOther);
				$("#img_" + tempId).val(imageOther);
				$("#rate_image_" + tempId).val(rateimageOther);
				$("#link_" + tempId).val(linkOther);
				$("#descript_" + tempId).val(descriptionOther);
			});
		} else {
			let tempId = idRow + "_" + currentLang;
			// FIX CUSTOMER HAVE ONE LANGUAGE
			let imageName = $("#temp_image_" + default_language).val();
			$("#tit_" + tempId).val(title);
			$("#sub_tit_" + tempId).val(sub_title);
			$("#img_" + tempId).val(imageName);
			$("#rate_image_" + tempId).val(rate_image);
			$("#link_" + tempId).val(link);
			$("#descript_" + tempId).val(description);
		}
		$(idForm).attr("edit", idRow);
		updateListIdFullSlider();
		$(idForm).addClass("hide");
		$(".apfullslider-row").addClass("hide");
	});
	/**
	* Validate and gender data for fullsilder and fill data for all language from current language selected in form
	*/
	$(document).on("click", ".btn-save-fullslider", function() {
		// Validate
		// Get current language code selected
		let currentLang = default_language;
		let lengthLang = Object.keys($globalthis.languages).length;
		let temId = lengthLang > 1 ? ".title-slide .lang-" + default_language + " input" : ".title-slide input";
		let title = $.trim($(temId).val());
		temId = lengthLang > 1 ? ".select-img .lang-" + default_language + " img" : ".select-img img";
		let image = $.trim($(temId).attr("src"));
		let imageName = $.trim($(temId).data("img"));
		temId = lengthLang > 1 ? ".link-slide .lang-" + default_language + " input" : ".link-slide input";
		let link = $.trim($(temId).val());
		temId = lengthLang > 1 ? ".description-slide .lang-" + default_language + " textarea" : ".description-slide textarea";
		let description = $.trim($(temId).val());
		let countLimit = 0;
		if(!image) {
			countLimit++;
		}
		if(!title) {
			countLimit++;
		}
		if(!description) {
			countLimit++;
		}
		// Require enter value for one in of [image, title, description]
		if(countLimit == 3) {
			alert($(this).data("error"));
			return;
		}
		
		let idForm = "#frm-slider";
		let idRow = (typeof $(idForm).attr("edit") != "undefined") ? $(idForm).attr("edit") : "";
		if(!idRow) {
			let html = $("#temp-list li:first").html();
			idRow = 1;
			let arr = $("#total_slider").val().split("|");
			arr.sort();
			for(let i = 0; i < arr.length; i++) {
				if(idRow != arr[i]) {
					break;
				}
				idRow++;
			}
			if(lengthLang > 1) {
				//console.log(idRow);
				// Duplicate for new slider and build name and id by language
				$(".select-img .translatable-field").each(function() {
					currentLang = $(this).data("lang");
					let tempId = idRow + "_" + currentLang + "'";
					html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + "/>";
					html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + "/>";
					html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + "/>";
					html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + "/>";
				});
			} else {
				let tempId = idRow + "_" + currentLang + "'";
				html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + " value='" + title + "'/>";
				html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + " value='" + image + "'/>";
				html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + " value='" + link + "'/>";
				html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + " value='" + description + "'/>";
			}
			$("#list-slider").prepend("<li id='" + idRow + "'>" + html + "</li>");
		}
		// Update labels for diplay interface
		let label = (title ? '<div class="col-lg-5">'+ title +'</div>' : "");
		label += (image ? '<img src="' + image + '">': "");
		$("#" + idRow + " div:first").html(label);

		if(lengthLang > 1) {
			// Update value for other language by default language and save to dum hidden fields
			$(".select-img .translatable-field").each(function() {
				currentLang = $(this).data("lang");
				let titleOther = $.trim($(".title-slide .lang-" + currentLang + " input").val());
				let imageOther = $.trim($(".select-img #temp_image_" + currentLang).val());
				let linkOther = $.trim($(".link-slide .lang-" + currentLang + " input").val());
				let descriptionOther = $.trim($(".description-slide .lang-" + currentLang + " textarea").val());
				if(currentLang != default_language) {
					if(!titleOther) {
						titleOther = title;
						$(".title-slide .lang-" + currentLang + " input").val(title);
					}
					if(!imageOther) {
						imageOther = imageName;
						$(".select-img .lang-" + currentLang + " input").val(imageOther);
					}
					if(!linkOther) {
						linkOther = link;
						$(".link-slide .lang-" + currentLang + " input").val(link);
					}
					if(!descriptionOther) {
						descriptionOther = description;
						$(".description-slide .lang-" + currentLang + " textarea").val(description);
					}
				}
				let tempId = idRow + "_" + currentLang;
				$("#tit_" + tempId).val(titleOther);
				$("#img_" + tempId).val(imageOther);
				$("#link_" + tempId).val(linkOther);
				$("#descript_" + tempId).val(descriptionOther);
			});
		} else {
			let tempId = idRow + "_" + currentLang;
			// FIX CUSTOMER HAVE ONE LANGUAGE
			let imageName = $("#temp_image_" + default_language).val();
			$("#tit_" + tempId).val(title);
			$("#img_" + tempId).val(imageName);
			$("#link_" + tempId).val(link);
			$("#descript_" + tempId).val(description);
		}
		$(idForm).attr("edit", idRow);
		updateListIdFullSlider();
		$(idForm).addClass("hide");
		$(".apfullslider-row").addClass("hide");
	});
	$(document).on("click", ".latest-blog-category input[type='checkbox']", function() {
		ckb = $(this).is(':checked');
		if(ckb) {
			$(this).closest("li").find('input').attr("checked", "checked");
		} else {
			$(this).closest("li").find('input').removeAttr("checked");
		}
	});
	$(document).on("click", ".list-font-awesome li", function() {
		$(".list-font-awesome li").removeClass("selected");
		$("#font_name").val($(this).find("i").data("default"));
		$(".preview-widget i").attr("class", $(this).find("i").attr("class"));
		$(this).addClass("selected");
		renderDefaultPreviewFontwesome();
	});
	$(document).on("change", "#font_type, #font_size, #is_spin", function() {
		renderDefaultPreviewFontwesome();
	});
});
function renderDefaultPreviewFontwesome() {
	let cls = "icon " + $("#font_name").val() + " " + $("#font_type").val()
			+ " " + $("#font_size").val()
			+ " " + $("#is_spin").val();
	$(".preview-widget i").attr("class", cls);
}
/**
* Start block for module DeoCategoryImage
*/
/**
 * Update status check a current category, this function is called from event in file home.js in this module
 * @param {type} obj: install of image just selected
 * @returns {Boolean}
 */
let selected_images = {};
let selected_rate_images = {};
function resetSelectedImage() {
	if(typeof selected_images != "undefined") {
		selected_images = {};
	}
	if(typeof selected_rate_images != "undefined") {
		selected_rate_images = {};
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
}
function intiForDeoCategoryImage() {
	selected_rate_images = {};
	$("#categorybox").addClass('full_loaded');  // Not load AJAX Tree Category again, For action expandAll in tree.js library
	$('#collapse-all-categorybox').hide();
	
	$("#pcategories").closest(".form-group").hide();
	$("#ptype").closest(".form-group").hide();
	$("#pproductids").closest(".form-group").hide();
	$("#pmanufacturers").closest(".form-group").hide();
	$("#source option:selected").each(function() {
		let val = $(this).val();
		$("#"+val).closest(".form-group").show();
	});
	$("#source").change(function(){
		$("#pcategories").closest(".form-group").hide();
		$("#ptype").closest(".form-group").hide();
		$("#pproductids").closest(".form-group").hide();
		$("#pmanufacturers").closest(".form-group").hide();
		let val = $(this).val();
		$("#"+val).closest(".form-group").show(500);
	});
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
	
	// Show selected_image when loaded page
	refreshListIcon();
	function refreshListIcon() {
		$("input[type=checkbox]", $(".form-select-icon")).each(function() {
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
	}

	function checkParentNodes(obj) {
		let flag = true;
		if(parent = obj.closest("ul").closest("li").find("input[type=checkbox]")){
			if(parent.val() != root_id){
				if($("input[value=" + parent.val() + "]","#categorybox").is(":checked")){
					flag = false;
				} else {
					flag = checkParentNodes(parent);
				}
			}
		}
		return flag;
	}
}
function replaceSpecialString(str){
	return str.replace(/\t/g, "_APTAB_").replace(/\r/g, "_APNEWLINE_").replace(/\n/g, "_APENTER_").replace(/"/g, "_APQUOT_").replace(/'/g, "_APAPOST_");
}
/*
* End block for module DeoCategoryImage
*/



function hideFormLevel2(){
	$(".row-level2").addClass("hide");
	$('.image-hotspot .image-wrapper .dot,.image-hotspot .preview-image-link .dot').remove();
	$("#list-slider .list-item.active").removeClass('active');
}

function showFormLevel2(){
	$(".btn-reset-level2").trigger("click");
	$(".row-level2").removeClass("hide");
	// get name
	$(".row-level2 input, .row-level2 textarea, .row-level2 select").each(function(){
		$(this).attr("name", $(this).attr('data-name'));
	});
}

$(document).on("click", ".btn-add-level2", function() {
	showFormLevel2();
	$('#list-slider > li').removeClass('active');
	$(".image-choose-temp").DeoImageSelector({
		name : 'temp_image',
		name_lazyload : 'temp_lazyload',
		name_rate_image : 'temp_rate_image',
		name_preview_image_link : 'temp_image_link',
		name_use_image_link : 'temp_use_image_link',
		class_calc_rate_image_group : '.group_calc_rate_image_temp',
		class_rate_lazyload_group : '.rate_lazyload_group_temp',
		class_select_image_link_group : '.select_image_link_group_temp',
	});

});

$(document).on("click", ".btn-cancel-level2", function() {
	hideFormLevel2();
	$('#list-slider > li').removeClass('active');
	$(".btn-reset-level2").trigger("click");
	$('.frm-level2').removeAttr("edit" );
});

$(document).on("click", ".btn-reset-level2", function() {
	scrollToModal($('#modal_form'),$('.image-hotspot:visible'));
	$('.row-level2 input:not([type="radio"]), .row-level2 textarea').val('');
	if ($('.row-level2 input.tagify').length){
		$('.row-level2 input.tagify').each(function(){
			$(this).data('tagify').removeAllTags();
			$(this).trigger('change');
		});
	}
	if ($(".row-level2 textarea.autoload_rte").length){
		$(".row-level2 textarea.autoload_rte").each(function(){
			tinymce.get($(this).attr('id')).setContent('');
		});
	}

	// $(".row-level2 img").each(function(){
	// 	if (!$(this).parent().hasClass('mColorPickerTrigger')){
	// 		if ($(this).parent().hasClass('virtual-image') || $(this).parent().hasClass('virtual-image-link')){
	// 			$(this).remove();
	// 		}
	// 	}
	// });
	$('.row-level2 .image-wrapper img').attr('src','').addClass('hide');
	$('.row-level2 .virtual-image-link img,.row-level2 .virtual-image img').remove();
	$('.row-level2 .preview-image-link img.img-preview').attr('src','').addClass('hide');
	$('.row-level2 .preview-image-link img.no-image').attr('src',deo_url_no_image).removeClass('hide');
	$('.temp_rate_image').val('0');

	$('.image-hotspot .image-wrapper .dot,.image-hotspot .preview-image-link .dot').remove();
	$('.image-hotspot .image-wrapper,.image-hotspot .preview-image-link').append('<span class="dot" style="left: 50%;top: 50%;"></span>');

	$('input[name="temp_left"],input[name="temp_top"]').val('50');
	$('input[name="temp_width"]').val('300px');

	$('input[name="temp_active"][value="1"]').attr('checked','checked').trigger('change');
	$('input[name="temp_lazyload"][value="1"]').attr('checked','checked').trigger('change');
	$('input[name="temp_use_image_link"][value="0"]').attr('checked','checked').trigger('change');


	$('#temp_location').val('top');
	$('#temp_trigger').val('mouseover');
	$('#temp_profile').val('default');
	$('#temp_type').val('product');
	$('#temp_effect_first_text').val('bounce');
	$('#temp_effect_second_text').val('bounce');
	$('#temp_effect_third_text').val('bounce');
	$('#temp_effect_link_btn').val('bounce');
	$('#temp_align_text').val('center-text-slide');
	$('.temp_delay').val('1000');

	$("select[name^='temp_']").trigger('change');

	$('.mColorPickerInput.mColorPicker').each(function(){
		let val = $(this).val();
		$(this).css('background-color', val);
	});
});

$(document).on("keyup", "#temp_left, #temp_top", function() {
	let value = $(this).val();
	let myRe = /^([0-9]{0,2})((\.|,){1}\d+){0,1}$/;
	if (myRe.test(value)){
		showSuccessMessage('Value position is valid!');
		if ($('.image-hotspot .image-wrapper .dot,.image-hotspot .preview-image-link .dot').length){
			let position = ($(this).attr('id') == "temp_left") ? 'left' : 'top';
			$('.image-hotspot .image-wrapper .dot,.image-hotspot .preview-image-link .dot').css(position,value+'%');
		}
	}else{
		showErrorMessage('The value position is not valid. Value must be a number from 0 to 99!');
	}
});



$(document).on("click", ".btn-save-level2", function() {
	let currentLang = default_language;
	let lengthLang = Object.keys($globalthis.languages).length;

	let title;
	if ($('.row2-title').find('.translatable-field').length){
		let temId = lengthLang > 1 ? ".row2-title .lang-" + default_language + " input" : ".row2-title input";
		title = $.trim($(temId).val());
	}else{
		title = $.trim($('#temp_title').val());
	}

	if ($('.row2-hotspot-type').length && $('.row2-hotspot-type .temp_type').val() == 'product'){
		title = 'ID: '+$('.temp_product').val();
	}
	
	let countLimit = 0;     // error
	if (!title){
		countLimit++;
	}
	
	// Require enter value for one in of [title]
	if(countLimit > 1) {
		alert($(this).data("error"));
		return;
	}
	
	let html_clone = $("#temp-list li:first").clone(1);
	let idRow = (typeof $('.frm-level2').attr("edit") != "undefined") ? $('.frm-level2').attr("edit") : "";
	let action = (typeof $('.frm-level2').attr("edit") != "undefined") ? 'edit' : 'add';

	if (action == 'add') {
		idRow = 1;
		let arr = $("#total_slider").val().split("|");
		// sort numbers
		arr.sort(function(a, b) {
			return a - b;
		});
		for(let i = 0; i < arr.length; i++) {
			if(idRow != arr[i]) {
				break;
			}
			idRow++;
		}
	}

	let list_inputs = $('#list-slider').data('inputs');
	let list_inputs_lang = $('#list-slider').data('inputs_lang');

	// $.each(array_inputs_lang, function(index, input){
	// 	if (deo_language.length == 1){
	// 		input = input+'_'+deo_language[0].id_lang;
	// 		list_inputs_lang.push(input);
	// 	}
	// });

	$.each(list_inputs, function(index, input){
		let value;
		let name_input = input+'_'+idRow;
		let temp_input = $('.row-level2 [name="'+input+'"]');

		if (temp_input.hasClass('autoload_rte')){
			value = tinymce.get(input).getContent();
		}else{
			if (temp_input.attr("type") == 'radio'){
				temp_input = $('.row-level2 [name="'+input+'"]:checked');
			}

			if (temp_input.hasClass("tagify")){
				value = temp_input.tagify().val();
			}else{
				value = temp_input.val();
			}
		}

		let input_hidden = $('<input type="hidden" id="' + name_input + '" data-name="'+input+'" name="' + name_input + '"/>');
		input_hidden.val(value);
		html_clone.append(input_hidden);
	});

	$.each(list_inputs_lang, function(index, input){
		$.each(deo_language, function(key, lang){
			let value;
			let id_lang = lang.id_lang;
			let name_input = input+'_'+idRow+'_'+id_lang;
			let temp_input = $('.row-level2 [name="'+input+'_'+id_lang+'"]');

			if (temp_input.hasClass('autoload_rte')){
				value = tinymce.get(input+'_'+id_lang).getContent();
			}else{
				if (temp_input.attr("type") == 'radio'){
					temp_input = $('.row-level2 [name="'+input+'"]:checked');
				}

				if (temp_input.hasClass("tagify")){
					value = temp_input.tagify().val();
				}else{
					value = temp_input.val();
				}
			}

			let input_hidden = $('<input type="hidden" id="' + name_input + '" data-name="'+input+'" data-lang="'+id_lang+'" name="' + name_input + '"/>');
			input_hidden.val(value);
			html_clone.append(input_hidden);
		});
	});

 
	if (action =='add'){
		$("#list-slider").prepend($("<li id='" + idRow + "'></li>").append(html_clone.children()));
	}else if(action =='edit'){
		$("#list-slider #" + idRow).empty().append(html_clone.children());
	}

	// Update image preview
	if (($('#temp_type').val() == 'product' && $('#temp_type').length) && typeof $('#temp_type') != "undefined"){
		$("#list-slider #" + idRow + " .img-preview img").remove();
	}else{
		let image_element, src_image = '';
		if ($('input[name="temp_use_image_link"]:checked').val() == 1) {
			image_element = $('.row-level2 .preview-image-link .img-preview').first();
		}else{
			image_element = $('.row-level2 .image-select-wrapper img').first();
		}

		if (deo_language.length == 1){
			src_image = image_element.attr('src');
		}else{
			image_element.each(function(){
				if (($(this).closest('.translatable-field').css('display') == 'block')){
					src_image = $(this).attr('src');
					return false;
				}
			});
		}

		if (src_image == '' || src_image == '#'){
			$("#list-slider #" + idRow + " .img-preview img").remove();
		}else{
			$("#list-slider #" + idRow + " .img-preview").html('<img src="' + src_image + '" class="img-thumbnail">');
		}
	}

	// Update title preview
	if (title){
		$("#list-slider #" + idRow + " .label-preview").html(title);
	}

	if ($('input[name="temp_active"]').length){
		if ($('input[name="temp_active"]:checked').val() == 1) {
			$("#list-slider #" + idRow).removeClass('disable');
		}else{
			$("#list-slider #" + idRow).addClass('disable');
		}
	}

	$('.frm-level2').removeAttr("edit");
	updateListIdFullSlider();

	hideFormLevel2();
	
});

$(document).on("click", ".btn-duplicate-level2", function() {
	let list_item = $(this).closest('.list-item ').clone();
	let inputs = list_item.find('input');

	idRow = 1;
	let arr = $("#total_slider").val().split("|");
	arr.sort();
	for (let i = 0; i < arr.length; i++) {
		if (idRow != arr[i]) {
			break;
		}
		idRow++;
	}

	inputs.each(function(){
		if (typeof $(this).data('lang') != "undefined"){
			$(this).attr({
				id : $(this).data('name')+'_'+idRow+'_'+$(this).data('lang'),
				name : $(this).data('name')+'_'+idRow+'_'+$(this).data('lang'),
			});
		}else{
			$(this).attr({
				id : $(this).data('name')+'_'+idRow,
				name : $(this).data('name')+'_'+idRow,
			});
		}
	});

	list_item.attr('id',idRow);
	$("#list-slider").prepend(list_item);

	updateListIdFullSlider();
});

$(document).on("click", "#list-slider .btn-delete-level2", function() {
	if (confirm($("#form_content").data("delete"))) {
		$(this).closest("li").remove();
		$("#frm-slider").removeAttr("edit");
		updateListIdFullSlider();
	}
});


$(document).on("click", ".btn-edit-level2", function() {
	showFormLevel2();
	$('#list-slider > li').removeClass('active');
	let li = $(this).closest("li");
	li.addClass('active');
	let idRow = $(li).attr("id");
	$(".frm-level2").attr("edit", $(li).attr("id"));

	$("#list-slider #" + idRow + " input").each(function(){
		let value = $(this).val();
		let id_lang = $(this).data('lang');
		let name_input = $(this).data('name');

		if (typeof id_lang != 'undefined'){
			name_input = name_input+'_'+id_lang;
		}

		let input = $(".row-level2 [name='" + name_input + "']");
		input.each(function(){
			// temp_image
			if ($(this).closest(".image-choose-temp").length){
				let img = $(this).parent().find('.image-select-wrapper img');
				if (value){
					let imgLink =  imgModuleLink+value;
					img.attr('src',imgLink).removeClass('hide');
					if (typeof id_lang != 'undefined'){
						$('.row-level2 .virtual-image').append('<img src="'+imgLink+'" data-lang="'+id_lang+'"/>');
					}else{
						$('.row-level2 .virtual-image').append('<img src="'+imgLink+'"/>');
					}
				}else{
					img.attr('src','').addClass('hide');
				}
			}

			// temp_image_link
			if ($(this).closest(".select_image_link_group_temp").length){
				let img_preview = $(this).parent().parent().find('.preview-image-link img.img-preview');
				let img_no_image = $(this).parent().parent().find('.preview-image-link img.no-image');
				if (value){
					img_preview.attr('src',value).removeClass('hide');
					img_no_image.attr('src',deo_url_no_image).addClass('hide');
					if (typeof id_lang != 'undefined'){
						$('.row-level2 .virtual-image-link').append('<img src="'+value+'" data-lang="'+id_lang+'"/>');
					}else{
						$('.row-level2 .virtual-image-link').append('<img src="'+value+'"/>');
					}
				}else{
					img_preview.attr('src','').addClass('hide');
					img_no_image.attr('src',deo_url_no_image).removeClass('hide');
				}
			}

			if ($(this).attr("type") == 'radio'){
				$(this).each(function(){
					if ($(this).val() == value) {
						$(this).trigger('change');
						$(this).prop('checked',true);
					}else{
						$(this).prop('checked',false);
					}
				});
			}else{
				$(this).val(value);
				if ($(this).hasClass('autoload_rte')){
					tinymce.get($(this).attr('id')).setContent(value);
				}

				$(this).trigger('change');

				if ($(this).hasClass("tagify")){
					$(this).data('tagify').removeAllTags();
					$(this).data('tagify').addTags(value);
				}
			}
		});
	}); 


	$('.image-hotspot .image-wrapper .dot,.image-hotspot .preview-image-link .dot').remove();
	$('.image-hotspot .image-wrapper,.image-hotspot .preview-image-link').append('<span class="dot" style="top: '+ $('input[name="temp_top"]').val() +'%;left: '+ $('input[name="temp_left"]').val() +'%;"></span>');
	
	$('.mColorPickerInput.mColorPicker').each(function(){
		let val = $(this).val();
		$(this).css('background-color', val);
	});

	$(".image-choose-temp").DeoImageSelector({
		name : 'temp_image',
		name_lazyload : 'temp_lazyload',
		name_rate_image : 'temp_rate_image',
		name_preview_image_link : 'temp_image_link',
		name_use_image_link : 'temp_use_image_link',
		class_calc_rate_image_group : '.group_calc_rate_image_temp',
		class_rate_lazyload_group : '.rate_lazyload_group_temp',
		class_select_image_link_group : '.select_image_link_group_temp',
	});

	scrollToModal($('#modal_form'),$('#config-hotspot'));
});

$(document).on('click','.scroll-to-image-hotspot', function() {
	scrollToModal($('#modal_form'),$('.image-hotspot:visible'));
});

// image-hotspot
$(document).on('change', '#temp_type', function() {
	if ($(this).val() == 'product'){
		$('.group-config-product').removeClass('hide-config-level-2');
		$('.group-config-text-image').addClass('hide-config-level-2');
	}else{
		$('.group-config-product').addClass('hide-config-level-2');
		$('.group-config-text-image').removeClass('hide-config-level-2');
	}
});

$(document).on("click",'.image-hotspot .image-wrapper .img-thumbnail,.image-hotspot .preview-image-link .img-thumbnail', function(e) {
	let result = {};
	let offset = $(this).offset();
	let imageHeight = $(this).outerHeight();
	let imageWidth = $(this).outerWidth();

	let relativeX = Math.round((((e.pageX - offset.left)/imageWidth)*100)*1000)/1000;
	let relativeY = Math.round((((e.pageY - offset.top)/imageHeight)*100)*1000)/1000;

	$('input[name="temp_left"]').val(relativeX);
	$('input[name="temp_top"]').val(relativeY);

	$(this).parent().find('.dot').css({
		left : relativeX +'%',
		top  : relativeY +'%',
	});
});
