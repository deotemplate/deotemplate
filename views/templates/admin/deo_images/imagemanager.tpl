{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($url_param) && $url_param}
    
{else}
    {* DEFAULT VALUE *}
    {assign var=url_param value=''}
{/if}

{if isset($reloadBack) && $reloadBack==1}
	{foreach $images as $image}
		<div style="background:url('{$image.link|escape:'html':'UTF-8'}') no-repeat center center;" class="pull-left" data-image="{$image.link|escape:'html':'UTF-8'}" data-val="../../../../assets/img/patterns/{$image.name|escape:'html':'UTF-8'}">

		</div>
	{/foreach}
{else}
	{if !(isset($reloadSliderImage) && $reloadSliderImage==1)}
		<div class="bootstrap image-manager">
			<div class="tab" >
				<div class="header-block">
					<div class="button-action">
						{$image_uploader}{* HTML form , no escape necessary *} 
					</div>
					<div class="search-image-group">
						<a href="javascript:void(0);" class="clear-search-bt text-danger hide"><i class="icon-remove"></i> {l s='Clear' mod='deotemplate'}</a>
						<input type="text" placeholder="{l s='Search image' mod='deotemplate'}" class="form-control search-image" value="" onsubmit="return false;">
					</div>
				</div>
				<button class="ladda-button btn btn-warning btn-sm" data-style="expand-right" type="button" id="file-upload-button" style="display:none;">
					<i class="icon-cloud-upload"></i> <span class="ladda-label">{if isset($multiple) && $multiple}{l s='Upload images' mod='deotemplate'}{else}{l s='Upload file' mod='deotemplate'}{/if}</span>
				</button>
				<div id="file-files-list" style="display:none"></div>
				<div class="infor-images">
					<div class="file_upload_label">
						{l s='Allow format:' mod='deotemplate'} JPG, GIF, PNG. ({l s='Max size:' mod='deotemplate'} {$max_image_size|string_format:"%.2f"|escape:'html':'UTF-8'} {l s='MB.' mod='deotemplate'})
					</div>
					<div class="total">{l s='Total:' mod='deotemplate'} <span id="countImage" class="badge">{$countImages|escape:'html':'UTF-8'}</span></div>
				</div>
			</div>
			<div id="wrapper-list-imgs">
				<ul id="list-imgs" class="clearfix">
	{/if}
					{foreach from=$images item=image name=myLoop}
						<li class="image-item" data-image-name="{$image.name|escape:'html':'UTF-8'}">
							<div class="image-background">
								<div class="img-row">
									{assign var=random_number value=1000|mt_rand:9999}
									<a class="label-tooltip img-link" data-widget="{if isset($widget) && $widget}{$widget}{/if}" data-toggle="tooltip" href="{$image.link|escape:'html':'UTF-8'}" title="{$image.name|escape:'html':'UTF-8'}">
										<img class="select-img" data-widget="{if isset($widget) && $widget}{$widget}{/if}" data-name="{$image.name|escape:'html':'UTF-8'}" data-folder="{$img_dir|escape:'html':'UTF-8'}" title="" alt="" src="{$image.link|escape:'html':'UTF-8'}?t={$random_number}"/>
									</a>
								 </div>
								<div class="name-image">
									{$image.name|rtrim|escape:'html':'UTF-8'}
								</div>
								<div class="button-image">
									<a class="fancybox view-image text-info btn label-tooltip" data-toggle="tooltip" href="{$image.link|escape:'html':'UTF-8'}" title="{l s='View' mod='deotemplate'}">
										<i class="icon-eye-open"></i>
									</a>
									<a href="{$link->getAdminLink('AdminDeoImages')|escape:'html':'UTF-8'}&ajax=1&action=deleteimage&imgDir={$img_dir|escape:'html':'UTF-8'}&imgName={$image.name|rtrim|escape:'html':'UTF-8'}" class="text-danger delete-image btn label-tooltip" title="{l s='Delete' mod='deotemplate'}" onclick="if (confirm(text_confirm_delete_image)) {
											return deleteImage($(this));
										} else {
											return false;
										}
										;">
										<i class="icon-trash"></i>
									</a>
								</div>
							</div>
						</li>
					{/foreach}
	{if !(isset($reloadSliderImage) && $reloadSliderImage==1)}
				</ul>
				<div class="spinner">
	                <div class="item-1"></div>
	                <div class="item-2"></div>
	                <div class="item-3"></div>
	            </div>
			</div>

			<script type="text/javascript">
				var text_confirm_delete_image = "{l s='Delete Selected Image?' mod='deotemplate'}";
				var imgManUrl = "{$imgManUrl}";
				var img_dir = "{$img_dir}";
				var upbutton = "{l s='Upload an image' mod='deotemplate'}";

				{literal}
					$(document).ready(function() {
						$('.label-tooltip').tooltip();
						$(window).keydown(function(event){
							if(event.keyCode == 13) {
								event.preventDefault();
								return false;
							}
						});
						$('.fancybox').fancybox();	

						// search image by name
						$(".search-image").keyup(function(e){		
							let filter = $(this).val();
							if (e.which !== 0 && filter !== ''){
								$('.clear-search-bt').removeClass('hide');
								$(".image-item").each(function(){		
									if ($(this).data('image-name').search(new RegExp(filter, "i")) < 0) {
										$(this).hide();
									} else {
										$(this).show();
									}
								});
							}else{
								$(".image-item").show();
								$('.clear-search-bt').addClass('hide');
							}
						});
						
						// clear search image by name
						$('.clear-search-bt').click(function(){
							$(this).addClass('hide');
							$(".search-image").val('').trigger('keyup');
						});
							
					});

					function deleteImage(element){
						$('#wrapper-list-imgs').addClass('loading');
						$.ajax({
							type: 'GET',
							url: element.attr("href"),
							data: '',
							dataType: 'json',
							cache: false, // @todo see a way to use cache and to add a timestamps parameter to refresh cache each 10 minutes for example
							success: function(data) {
								$("#list-imgs").html(data);
								$("#countImage").text($("#list-imgs li").length);
								$('.label-tooltip').tooltip();
								$('.fancybox').fancybox();

								$(".image-item").show();
								$('.clear-search-bt').addClass('hide');
								$(".search-image").val('').trigger('keyup');
								$('#wrapper-list-imgs').removeClass('loading');
								showSuccessMessage('Deleted image successful');
							}
						});

						return false;
					}

					function getUrlVars(){
						let vars = [], hash;
						let hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
						for(let i = 0; i < hashes.length; i++){
							hash = hashes[i].split('=');
							vars.push(hash[0]);
							vars[hash[0]] = hash[1];
						}
						return vars;
					}
					{/literal}
					function reloadImageList(sortBy, imgDir){
						if(!sortBy) sortBy = "date_add";
						if(!imgDir) sortBy = "images";
						$('#wrapper-list-imgs').addClass('loading');
						$.ajax({
							type: 'GET',
							url: imgManUrl + '&ajax=1&action=reloadSliderImage&imgDir='+imgDir+'&sortBy='+sortBy+'{$url_param}',
							data: '',
							dataType: 'json',
							cache: false, // @todo see a way to use cache and to add a timestamps parameter to refresh cache each 10 minutes for example
							success: function(data){
								$("#list-imgs").html(data);
								$('.label-tooltip').tooltip();
								$('.fancybox').fancybox();
								$('#wrapper-list-imgs').removeClass('loading');
							}
						});
					}
				</script>
			</div>
		</div>
	{/if}
{/if}
