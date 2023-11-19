{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="form-group" style="display: none;">
	<div class="col-lg-12" id="file-images-thumbnails">
		{if isset($files) && $files|count > 0}
		{foreach $files as $file}
		{if isset($file.image) && $file.type == 'image'}
		<div class="img-thumbnail text-center">
			<p>{$file.image}</p>
			{if isset($file.size)}<p>{l s='File size'  mod='deotemplate'} {$file.size}kb</p>{/if}
			{if isset($file.delete_url)}
			<p>
				<a class="btn btn-default" href="{$file.delete_url}">
				<i class="icon-trash"></i> {l s='Delete' mod='deotemplate'}
				</a>
			</p>
			{/if}
		</div>
		{/if}
		{/foreach}
		{/if}
	</div>
</div>
{if isset($max_files) && $files|count >= $max_files}
	<div class="row">
		<div class="alert alert-warning">{l s='You have reached the limit (%s) of files to upload, please remove files to continue uploading' mod='deotemplate' sprintf=$max_files}</div>
	</div>
	<script type="text/javascript">
		$( document ).ready(function() {
			{if isset($files) && $files}
			$('#file-images-thumbnails').parent().show();
			{/if}
		});
	</script>
{else}
<div class="form-group row">
	<div class="col-lg-12">
		<input id="file" type="file" name="{$name}[]"{if isset($url)} data-url="{$url}"{/if}{if isset($multiple) && $multiple} multiple="multiple"{/if} class="hide"/>
		<button class="btn btn-info btn-sm" data-style="expand-right" data-size="s" type="button" id="file-add-button">
			<i class="icon-plus"></i> {if isset($multiple) && $multiple}{l s='Add files' mod='deotemplate'}{else}{l s='Add images' mod='deotemplate'}{/if}
		</button>
        <!-- order button -->
        <div class="btn-group">
			<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">{l s='Order by' mod='deotemplate'} <span class="caret"></span></button>
			<ul id="img_order" class="dropdown-menu">
			    <li class="active"><a href="javascript:void(0);" data-type="name">{l s='Name' mod='deotemplate'} <i class="icon-sort-by-alphabet"></i></a></li>
			    <li><a href="javascript:void(0);" data-type="name_desc">{l s='Name DESC' mod='deotemplate'} <i class="icon-sort-by-alphabet-alt"></i></a></li>
			    <li class="divider"></li>
			    <li><a href="javascript:void(0);" data-type="date">{l s='Date Modified' mod='deotemplate'} <i class="icon-sort-by-attributes"></i></a></li>
			    <li><a href="javascript:void(0);" data-type="date_desc">{l s='Date Modified DESC' mod='deotemplate'} <i class="icon-sort-by-attributes-alt"></i></a></li>
			</ul>
        </div>
        <!-- folder button -->
		<div class="btn-group">	
	    	{* {assign var=folders value=['icon','images']} *}
	        <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
	        	<i class="icon-folder-open-alt"></i> <span class="value">{$img_dir}</span> <span class="caret"></span>
	        </button>
	        <ul id="img_folder" class="dropdown-menu">
	        	{foreach from=$folders item=folder}
		        	<li {if $folder == $img_dir}class="active"{/if}>
						<a href="javascript:void(0);" data-folder="{$folder}"><i class="icon-folder-open-alt"></i> {$folder}</a>
		        	</li>
	        	{/foreach}
	        </ul>
	    </div>
	</div>
</div>
<script type="text/javascript">
	function humanizeSize(bytes){
		if (typeof bytes !== 'number') {
			return '';
		}

		if (bytes >= 1000000000) {
			return (bytes / 1000000000).toFixed(2) + ' GB';
		}

		if (bytes >= 1000000) {
			return (bytes / 1000000).toFixed(2) + ' MB';
		}

		return (bytes / 1000).toFixed(2) + ' KB';
	}

	function parseUrl(url) {
	    let a = document.createElement('a');
	    a.href = url;
	    return a;
	}
        
	$( document ).ready(function() {
		$("#img_order a").click(function(){
			let type = $(this).data("type");
			let folder = $('#img_folder li.active a').data('folder');
			reloadImageList(type, folder);
			$('#img_order li').removeClass('active');
			$('#img_order li a[data-type="'+ type +'"]').closest('li').addClass('active');

			// reset search
			$(".image-item").show();
			$('.clear-search-bt').addClass('hide');
			$(".search-image").val('').trigger('keyup');
		});

		$("#img_folder a").click(function(){
			let type = $("#img_order li.active a").data("type");
			let folder = $(this).data("folder");
			reloadImageList(type, folder);
			$('#img_folder li').removeClass('active');
			$('#img_folder li a[data-folder="'+ folder +'"]').closest('li').addClass('active');
			$('#img_folder').closest('.btn-group').find('button span.value').text(folder);

			// change folder at url
			let url = $('#file').data('url');
			url = parseUrl(url).search.substring(1).split('&');
			for (let i = 0; i < url.length; i++) {
				if(url[i].includes("imgDir")){
					$('#file').attr('data-url',$('#file').data('url').replace(url[i],'imgDir='+folder));
					break;
				}
			}

			//reset search
			$(".image-item").show();
			$('.clear-search-bt').addClass('hide');
			$(".search-image").val('').trigger('keyup');

		});

		{if isset($multiple) && isset($max_files)}
			let file_max_files = {$max_files - $files|count};
		{/if}

		{if isset($files) && $files}
		$('#file-images-thumbnails').parent().show();
		{/if}

		let file_upload_button = $('#file-upload-button');
		let file_total_files = 0;
		const validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/svg', 'image/webp'];
		
		$('#file-upload-button').off('click');

		$('#file').fileupload({
			dataType: 'json',
			autoUpload: false,
			singleFileUploads: true,
			{if isset($post_max_size)}maxFileSize: {$post_max_size},{/if}
			{if isset($drop_zone)}dropZone: {$drop_zone},{/if}
			start: function (e) {
				//Important as we bind it for every elements in add function
				$('#file-upload-button').unbind('click'); 
				file_upload_button.addClass('loading');
			},
			fail: function (e, data) {
				showErrorMessage(data.errorThrown.message)
				$('#wrapper-list-imgs').removeClass('loading');	
			},
            done: function (e, data) {
                if (data.result) {
                    $("#list-imgs").html(data.result);
                    $(".label-tooltip").tooltip();
                    $(".fancybox").fancybox();
                    $("#file").val("");
                    $("#file-files-list").html("");
                    $("#file-files-list").hide();
                    $("#file-upload-button").hide();
                    $("#file-errors").hide();

                    showSuccessMessage('Upload image successful');
                    $(data.context).find("button").remove();		
                    $('#countImage').html(parseInt($('#countImage').html())+1);	
                    $('#wrapper-list-imgs').removeClass('loading');		
                }
            },
            add: function (e, data) {
            	data.url = $(this).attr('data-url');
            },
		}).on('fileuploadalways', function (e, data) {
				file_total_files--;

				if (file_total_files == 0){
					file_upload_button.removeClass('loading');
					$('#file-upload-button').unbind('click');
					$('#file-files-list').hide();
				}
		}).on('fileuploadadd', function(e, data) {
			if (typeof file_max_files !== 'undefined') {
				if (file_total_files >= file_max_files) {
					e.preventDefault();
					let text = "{l s='You can upload a maximum of %s files'|sprintf:$max_files mod='deotemplate'}";
					showErrorMessage(text);
					return;
				}
			}
			let fileType = data.files[0]["type"];
			if (($.inArray(fileType, validImageTypes) == 0) || fileType == '') {
				let text = "{l s=' file type not valid (PNG, JPG, GIF). '|sprintf:$max_files mod='deotemplate'}"
				showErrorMessage(data.files[0].name+text);
				return;
			}

			function setPreviewImage(data,image) {
				if (data.files && data.files[0]) {
					let reader = new FileReader();
					reader.onload = function(e) {
						image.attr('src',e.target.result);
					}
					reader.readAsDataURL(data.files[0]);
				}
			}

			data.context = $('<div/>').addClass('alert alert-info clearfix item-upload').appendTo($('#file-files-list'));
			let file_name = $('<span/>').addClass('image-infor').append('<strong>'+data.files[0].name+'</strong> ('+humanizeSize(data.files[0].size)+')').appendTo(data.context);
			let imagePreview = $('<img/>').addClass('img-preview');
			setPreviewImage(data,imagePreview);
			data.context.prepend(imagePreview);

			let button_delete = $('<button/>').addClass('btn btn-danger btn-sm pull-right').prop('type', 'button').html('<i class="icon-trash"></i> <span class="text-icon">{l s='Remove file' mod='deotemplate'}</span>').appendTo(data.context);
			button_delete.on('click', function() {
				file_total_files--;
				data.files = null;
				
				let total_elements = $(this).parent().siblings('div.clearfix').length;
				$(this).parent().remove();

				if (total_elements == 0) {
					$('#file-upload-button').hide();
					$("#file-files-list").html("");
                    $("#file-files-list").hide();
				}
			});

			$('#file-files-list').show();
			$('#file-upload-button').show();
			$('#file-upload-button').bind('click', function () {
				if (data.files != null){
					data.submit();
					$('#wrapper-list-imgs').addClass('loading');
				}
			});

			file_total_files++;
		}).on('fileuploadprocessalways', function (e, data) {
			let index = data.index,	file = data.files[index];
			
			if (file.error) {
				showErrorMessage('<strong>'+file.name+'</strong> ('+humanizeSize(file.size)+') : '+file.error);
				$(data.context).find('button').trigger('click');
			}
		});

		$('#file-add-button').on('click', function() {
			file_total_files = 0;
			$('#file').trigger('click');
		});
	});
</script>
{/if}