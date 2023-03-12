/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

$(document).ready(function() {
	$('.submit-tranlsate').click(function(){
		let data = new Array();
		let form = $(this).closest('form');
		form.find('.target').each(function(index) {
			let id = $(this).data('id');
			let target = $(this).val();

			let data_field;
			if (typeof $(this).data('id_translation') != 'undefined'){
				data_field = {
					id : id,
					target : target,
					id_translation : $(this).data('id_translation'),
				};
			}else{
				data_field = {
					id : id,
					target : target,
				};
			}
			data = data.concat(data_field);
		});

		$.ajax({
			headers: {"cache-control": "no-cache"},
			url: form.attr('action')+'&submit-tranlsate&domain='+form.find('input[name="domain"]').val(),
			async: true,
			cache: false,
			dataType: "Json",
			data: {
				data : data
			},
			type: 'POST',
			success: function(response){
				if (response.success){
					showSuccessMessage(response.msg);
				}
			}
		});
	});

	$('#page-header-desc-configuration-SaveAll').click(function(){
		$('.form-translate').find('.submit-tranlsate').trigger('click');
	});
});
