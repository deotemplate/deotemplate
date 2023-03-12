/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function() {
	$('select#id_deofeature_product_review_criterion_type').change(function() {
		// PS 1.6
		$('#categoryBox').closest('div.form-group').hide();
		$('#ids_product').closest('div.form-group').hide();
		// PS 1.5
		$('#categories-treeview').closest('div.margin-form').hide();
		$('#categories-treeview').closest('div.margin-form').prev().hide();
		$('#ids_product').closest('div.margin-form').hide();
		$('#ids_product').closest('div.margin-form').prev().hide();

		if (this.value == 2){
			$('#categoryBox').closest('div.form-group').show();
			// PS 1.5
			$('#categories-treeview').closest('div.margin-form').show();
			$('#categories-treeview').closest('div.margin-form').prev().show();
		}else if (this.value == 3){
			$('#ids_product').closest('div.form-group').show();
			// PS 1.5
			$('#ids_product').closest('div.margin-form').show();
			$('#ids_product').closest('div.margin-form').prev().show();
		}
	});

	$('select#id_deofeature_product_review_criterion_type').trigger("change");

	// show label new at BO
	if (typeof deo_url_review != 'undefined'){
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: deo_url_review,
			async: true,
			cache: false,
			data: {
				"action": "get-new-review",
			},
			success: function (result){
				if(result != ''){						
					let obj = $.parseJSON(result);
					if (obj.number_review > 0){
						$('#subtab-AdminDeoReviewManager').addClass('has-review');
						// $('#subtab-AdminDeoReviewManager').append('<span id="total_notif_number_wrapper" class="notifs_badge"><span id="total_notif_value">'+obj.number_review+'</span></span>');
						$('#subtab-AdminDeoReviewManager').children('.link').append('<span class="notifs_badge">New</span>');
					}
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				// alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
});