{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{extends file="helpers/form/form.tpl"}
{block name="label"}
	{if $input.type == 'DeoColumnClass' || $input.type == 'DeoRowClass'}
	
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
{block name="field"}
	{if $input.type == 'blockLink'}
		<script type="text/javascript">
			function getMaxIndex(){
				if ($('.link_group').length == 0){
					return 1;
				}else{
					var list_index = [];
					$('.link_group').each(function(){
						list_index.push($(this).data('index'));
					})
					return Math.max.apply(Math,list_index) + 1;
				}
			}

			function updateNewLink(total_link, scroll_to_new_e, current_index, allow_add_fieldname){
				var array_id_lang = $.parseJSON(list_id_lang);
				if (allow_add_fieldname){
					$('.form-group.link_group.new .form-action').trigger("change"); // FIX show_hide input follow select_box
					hideOtherLanguage(id_language); // FIX when add new link, only show input in current_lang

					updateField('add','link_title_'+total_link,true);
					updateField('add','link_url_'+total_link,true);

					updateField('add','target_type_'+total_link, false);
					updateField('add','link_type_'+total_link, false);
					updateField('add','cmspage_id_'+total_link, false);
					updateField('add','category_id_'+total_link, false);
					updateField('add','product_id_'+total_link, false);
					updateField('add','manufacture_id_'+total_link, false);
					updateField('add','page_id_'+total_link, false);
					updateField('add','page_param_'+total_link, false);
					updateField('add','supplier_id_'+total_link, false);
				}

				$('.link_group.new .form-group .tmp').each(function(){
					// RENAME INPUT
					var e_obj = $(this);
					if ($(this).closest(".translatable-field").length){
						// MULTI_LANG
						$.each(array_id_lang, function( index, value ) {
							if (current_index == 0){
								// ADD NEW
								switch(e_obj.attr('id')){
									case 'link_title_'+value:
										e_obj.attr('id','link_title_'+total_link+'_'+value);
										e_obj.attr('name','link_title_'+total_link+'_'+value);
										break;
									case 'link_url_'+value:
										e_obj.attr('id','link_url_'+total_link+'_'+value);
										e_obj.attr('name','link_url_'+total_link+'_'+value);
										break;
								}
							}
						});
					}else{
						// ONE_LANG
						switch (e_obj.attr('id')){
							case 'link_title_'+id_language:
								e_obj.attr('id','link_title_'+total_link+'_'+id_language);
								e_obj.attr('name','link_title_'+total_link+'_'+id_language);
								break;
							case 'link_url_'+id_language:
								e_obj.attr('id','link_url_'+total_link+'_'+id_language);
								e_obj.attr('name','link_url_'+total_link+'_'+id_language);
								break;
							default:
								var old_id = e_obj.attr('id');
								var old_name = e_obj.attr('name');
								e_obj.attr('id',old_id+'_'+total_link);
								e_obj.attr('name',old_name+'_'+total_link);
								break;
						}
					}
				});
				$("#total_link").val(total_link);
			}

			function updateField(action, value, is_lang){
				if (action == 'add'){
					if (is_lang == true){
						$('#list_field_lang').val($('#list_field_lang').val()+value+',');
					}else{
						$('#list_field').val($('#list_field').val()+value+',');
					}
				}else{
					// REMOVE
					if (is_lang == true){
						var old_list_field_lang = $('#list_field_lang').val();
						var new_list_field_lang = old_list_field_lang.replace(value+',','');
						$('#list_field_lang').val(new_list_field_lang);
					}else{
						var old_list_field = $('#list_field').val();
						var new_list_field = old_list_field.replace(value+',','');
						$('#list_field').val(new_list_field);
					}
				}

				// UPDATE INDEX FORM 2,3,4,5,
				$('#list_id_link').val('');
				$('.link_group').each(function(){
					$('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
				})	
			}

			$(document).off("click", ".add-new-link");
			$(document).on("click", ".add-new-link", function(e) {
				e.preventDefault();
				addLinkForm();
			});

			/**
			 * ACTION FOR BUTTON ADD NEW
			 * param : index for edit ajax_widget
			 */
			function addLinkForm( index ){
				var maxIndex = getMaxIndex();
				var allow_add_fieldname = true;
				if (index){
					maxIndex = index;
					allow_add_fieldname = false;
				}

				var new_link_html = '';
				new_link_html += '<div class="form-group link_group new">';

				$('.parent-tmp').each(function(){
					new_link_html += $(this).prop('outerHTML');
					new_link_html = new_link_html.replace('parent-tmp hidden','');
					new_link_html = new_link_html.replace('parent-tmp','');
					new_link_html = new_link_html.replace('display: none;','');
				});

				new_link_html += "<div class='form-group'>";
								new_link_html += "<div class='col-lg-4'></div>";
								new_link_html += "<div class='col-lg-8'>";
									new_link_html += "<button class='fr btn btn-danger remove_link pull-left'>"+remove_button_text+"</button>";
								new_link_html += '</div>';
							new_link_html += '</div>';
						new_link_html += '</div>';

				$(new_link_html).insertBefore('.form-group.frm-add-new-link').data('index', maxIndex);

				updateNewLink(maxIndex, true , 0, allow_add_fieldname);
				$('.link_group.new').removeClass('new');
			}

			$(document).off("click", ".remove_link");
			$(document).on("click", ".remove_link", function(e) {
				e.preventDefault();

				$(this).closest('.link_group').find('.tmp').each(function(){
					// REMOVE FORM list_field, list_field_lang
					var name_val = $(this).attr('name');
					if($(this).closest(".translatable-field").length){
						name_val = name_val.substring(0, name_val.lastIndexOf('_'));
						updateField('remove',name_val,true);
					}else{
						updateField('remove',name_val,false);
					}
				});

				$(this).closest('.link_group').fadeOut(function(){
					// REMOVE FORM
					$(this).remove();
					var total_link = parseInt($("#total_link").val())-1;
					$("#total_link").val(total_link);

					$('#list_id_link').val('');
					$('.link_group').each(function(){
						$('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
					})
				});
			});

			$(document).off("change", ".form-group.link_group .form-action");
			$(".form-action").each(function(e) {
				$(this).attr('data-name', $(this).attr('name') );
			});
			$(document).on("change", ".form-group.link_group .form-action", function(e) {
				var elementName = $(this).attr('data-name');
				$('.' + elementName + '_sub', $(this).closest('.form-group.link_group')).hide(400);
				$('.' + elementName + '-' + $(this).val(), $(this).closest('.form-group.link_group')).show(400);
			});

			/**
			 * AJAX FOR EDIT BLOCKLINK WIDGET
			 */
			function editWidgetLink(){
				if ($('#list_id_link').length && $('#list_id_link').val() != ''){
					var list_id_link = $('#list_id_link').val().split(',');
					$.each(list_id_link, function( index, value ) {
						if (value != ''){
							// GENERATE FORM
							addLinkForm(value);
						}
					});

					$.each(listData, function( index, value ) {
						// FILL DATA INTO FORM
						$('#'+index).val(value);
						$('#'+index).val(value).prop('selected', true);;
					});

					setTimeout(function(){
						// SHOW_HIDE INPUT FOLLOW SELECT_BOX
						$('.form-group.link_group .form-action').trigger("change");
					}, 500);
				}
			}
			editWidgetLink();
		</script>
	{elseif $input.type == 'tabConfig'}
		<div class="row">
			{assign var=tabList value=$input.values}
			<ul class="nav nav-tabs admin-tabs" role="tablist">
			{foreach $tabList as $key => $value name="tabList"}
				<li role="presentation" class="{if $smarty.foreach.tabList.first}active{/if}"><a href="#{$key|escape:'html':'UTF-8'}" class="deo-tab-config" role="tab" data-toggle="tab">{$value|escape:'html':'UTF-8'}</a></li>
			{/foreach}
			</ul>
		</div>
	{elseif $input.type == 'selectImg'}
		<div class="col-lg-8">
			{if isset($input.lang) AND $input.lang}
				<div class="selectImg lang">
					{foreach from=$languages item=language}
						{if $languages|count > 1}
							<div class="translatable-field row lang-{$language.id_lang|escape:'html':'UTF-8'}" data-lang="{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
						{/if}
							<div class="col-lg-6">
								{if isset($input.show_image) && $input.show_image != false}
									{if isset($fields_value[$input.name][$language.id_lang]) && $fields_value[$input.name][$language.id_lang]}
										<div class="image-wrapper">
											<img src="{$path_image|escape:'html':'UTF-8'}{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" class="img-thumbnail" title="{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" alt="{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" data-img="">
										</div>
									{/if}
								{/if}
								<div style="margin-top: 10px; font-size: 13px;">
									<a class="choose-img choose-img-extend {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" data-fancybox-type="iframe" href="{$input.href|escape:'html':'UTF-8'}" data-for="#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}">{l s='Select image' mod='deotemplate'}</a>
									-
									<a class="reset-img {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" data-fancybox-type="iframe" href="{$input.href|escape:'html':'UTF-8'}" data-for="#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}">{l s='Remove image' mod='deotemplate'}</a>
								</div>
								<input id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" type="text" name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" class="hide img-value{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}" value="{if isset($fields_value[$input.name][$language.id_lang]) && ($fields_value[$input.name][$language.id_lang])}{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}{/if}"/>
							</div>
								
						{if isset($input.lang) AND $input.lang }
							{if $languages|count > 1}
								<div class="col-lg-6">
									<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
										{$language.iso_code|escape:'html':'UTF-8'}
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach from=$languages item=lang}
										<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
										{/foreach}
									</ul>
								</div>
							{/if}
						{/if}
						
						{if $languages|count > 1}
							</div>
						{/if}
						<script type="text/javascript">
							$(document).ready(function(){
								$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}-selectbutton').click(function(e){
									$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}').trigger('click');
								});
								$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}').change(function(e){
									var val = $(this).val();
									var file = val.split(/[\\/]/);
									$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}-name').val(file[file.length-1]);
								});
							});
						</script>
					{/foreach}
				</div>
			{else}
				<div class="selectImg">
					<div class="col-lg-6">
						{if isset($input.show_image) && $input.show_image != false}
							{if isset($fields_value[$input.name]) && $fields_value[$input.name]}
							<div class="image-wrapper">
								<img src="{$path_image|escape:'html':'UTF-8'}{$fields_value[$input.name]|escape:'html':'UTF-8'}" class="img-thumbnail" title="{$fields_value[$input.name]|escape:'html':'UTF-8'}" alt="{$fields_value[$input.name]|escape:'html':'UTF-8'}" data-img="">
							</div>
							{/if}
						{/if}
						<div></div>
						<a class="choose-img choose-img-extend {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" data-fancybox-type="iframe" href="{$input.href|escape:'html':'UTF-8'}" data-for="#{$input.name|escape:'html':'UTF-8'}">{l s='Select image' mod='deotemplate'}</a> - 
						<a onclick="resetDeoImage();" href="javascript:void(0)">{l s='Remove image' mod='deotemplate'}</a>
						<input id="{$input.name|escape:'html':'UTF-8'}" type="text" name="{$input.name|escape:'html':'UTF-8'}" class="hide input-s-image" value="{if isset($fields_value[$input.name]) && ($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'} {/if}"/>
						<script type="text/javascript">
							function resetDeoImage(){
								// Reset img and hidden input
								$(".img-thumbnail").hide();
								$(".img-thumbnail").attr('src','');
								$(".input-s-image").val('');
							}
						</script>            
					</div>

						<script type="text/javascript">
						$(document).ready(function(){
							$('#{$input.name|escape:'html':'UTF-8'}-selectbutton').click(function(e){
								$('#{$input.name|escape:'html':'UTF-8'}').trigger('click');
							});
							$('#{$input.name|escape:'html':'UTF-8'}').change(function(e){
								var val = $(this).val();
								var file = val.split(/[\\/]/);
								$('#{$input.name|escape:'html':'UTF-8'}-name').val(file[file.length-1]);
							});
						});
					</script>
				</div>
			{/if}
		</div>
	{elseif $input.type == 'img_cat'}
		{assign var=tree value=$input.tree}
		{assign var=imageList value=$input.imageList}
		{assign var=selected_images value=$input.selected_images}
		<div class="form-group form-select-icon">
			<label class="control-label col-lg-4 " for="categories"> {l s='Categories' mod='deotemplate'} </label>
			<div class="col-lg-8">
				{$tree}{* HTML form , no escape necessary *}
				{if isset($input.desc) && $input.desc}
					<p class="help-block">{$input.desc}</p>
				{/if}
			</div>
			<input type="hidden" name="category_img" id="category_img" value='{$selected_images|escape:'html':'UTF-8'}'/>
			<div id="list_image_wrapper" style="display:none">
				<div class="list-image">
					<img id="apci" src="" class="hidden" path="{$input.path_image|escape:'html':'UTF-8'}" widget="DeoCategoryImage" src-url=""/>
					<a data-for="#apci" href="{$input.href_image|escape:'html':'UTF-8'}" class="categoryimage field-link choose-img choose-img-extend">[{l s='Select image' mod='deotemplate'}]</a>
					<a class="categoryimage field-link remove-img hidden" href="javascript:void(0)">[{l s='Remove image' mod='deotemplate'}]</a>
				  </div>
			</div>
			<script type="text/javascript">
				full_loaded = true;
				intiForDeoCategoryImage();
			</script>
			
		</div>
	{* {elseif $input.type == 'categories'}
		<script type="text/javascript">
			var full_loaded = undefined;
		</script> *}
	{elseif $input.type == 'bg_img'}
		<div class="col-lg-8 ">
			<input type="text" name="bg_img" id="bg_img" value="{$fields_value['bg_img']|escape:'html':'UTF-8'}" class="hidden">
				{if isset($fields_value['bg_img']) && $fields_value['bg_img']}
					<img id="s-image"{if !$fields_value['bg_img']} class="hidden"{/if} src="{$path_image|escape:'html':'UTF-8'}{$fields_value['bg_img']|escape:'html':'UTF-8'}"/>
				{else}
					<img id="s-image"{if !$fields_value['bg_img']} class="hidden"{/if} src=""/>
				{/if}
			<div>
				<a class="choose-img choose-img-extend" data-fancybox-type="iframe" href="{$link->getAdminLink('AdminDeoImages')|escape:'html':'UTF-8'}&ajax=1&action=manageimage&imgDir=images" data-for="#bg_img">{l s='Select image' mod='deotemplate'}</a> -
				<a class="reset-img" href="javascript:void(0)" onclick="resetBgImage();">{l s='Reset' mod='deotemplate'}</a>
			</div>
			<p class="help-block">{l s='Please put image link or select image' mod='deotemplate'}</p>
		</div>
		<script type="text/javascript">
			function resetBgImage(){
				// Reset img and hidden input
				$("#s-image").addClass('hiden');
				$("#s-image").attr('src','');
				$("#bg_img").val('');
			}
		</script> 
	{elseif $input.type == 'DeoExceptions'}
		<div class="well">
			<p>
				{l s='Please specify the files for which you do not want it to be displayed.' mod='deotemplate'}<br/>
				{l s='Please input each filename, separated by a comma (",").' mod='deotemplate'}<br />
				{l s='You can also click the filename in the list below, and even make a multiple selection by keeping the Ctrl key pressed while clicking, or choose a whole range of filename by keeping the Shift key pressed while clicking.' mod='deotemplate'}<br/>
			</p>
			{$exception_list}{* HTML form , no escape necessary *}
		</div>
	{elseif $input.type == 'DeoColumnClass' || $input.type == 'DeoRowClass' || $input.type == 'DeoClass'}
		{function name=col_class}
			<ul class="deo-col-class row">
				<li class="{if $input.type == 'DeoClass'}col-lg-6{else}col-lg-4{/if} col-md-6 col-sm-6 col-xs-12">
					<label class="choose-class"><input class="select-class no-save" name="hidden-xxl" type="checkbox" value="hidden-xxl"> {l s='Hidden Large Desktop Devices (screen ≥ 1500px)' mod='deotemplate'}</label>
				</li>
				<li class="{if $input.type == 'DeoClass'}col-lg-6{else}col-lg-4{/if} col-md-6 col-sm-6 col-xs-12">
					<label class="choose-class"><input class="select-class no-save" name="hidden-xl" type="checkbox" value="hidden-xl"> {l s='Hidden Desktop Devices (1500px > screen ≥ 1200px)' mod='deotemplate'}</label>
				</li>
				<li class="{if $input.type == 'DeoClass'}col-lg-6{else}col-lg-4{/if} col-md-6 col-sm-6 col-xs-12">
					<label class="choose-class"><input class="select-class no-save" name="hidden-lg" type="checkbox" value="hidden-lg"> {l s='Hidden Small Desktop Devices (1200px > screen ≥ 992px)' mod='deotemplate'}</label>
				</li>
				<li class="{if $input.type == 'DeoClass'}col-lg-6{else}col-lg-4{/if} col-md-6 col-sm-6 col-xs-12">
					<label class="choose-class"><input class="select-class no-save" name="hidden-md" type="checkbox" value="hidden-md"> {l s='Hidden Tablets Devices (992px > screen ≥ 768px)' mod='deotemplate'}</label>
				</li>
				<li class="{if $input.type == 'DeoClass'}col-lg-6{else}col-lg-4{/if} col-md-6 col-sm-6 col-xs-12">
					<label class="choose-class"><input class="select-class no-save" name="hidden-sm" type="checkbox" value="hidden-sm"> {l s='Hidden Small Tablets Devices (768px > screen ≥ 576px)' mod='deotemplate'}</label>
				</li>
				<li class="{if $input.type == 'DeoClass'}col-lg-6{else}col-lg-4{/if} col-md-6 col-sm-6 col-xs-12">
					<label class="choose-class"><input class="select-class no-save" name="hidden-xs" type="checkbox" value="hidden-xs"> {l s='Hidden Mobile Devices (576px > screen ≥ 480px)' mod='deotemplate'}</label>
				</li>
				<li class="{if $input.type == 'DeoClass'}col-lg-6{else}col-lg-4{/if} col-md-6 col-sm-6 col-xs-12">
					<label class="choose-class"><input class="select-class no-save" name="hidden-sp" type="checkbox" value="hidden-sp"> {l s='Hidden Small Mobile Devices (screen < 480px)' mod='deotemplate'}</label>
				</li>
			</ul>
		{/function}
		{if $input.type == 'DeoClass'}
			<div class="col-lg-8">
				<div class="row">
					<div class="col-lg-10">
						<input type="text" class="element_class" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" name="{$input.name}">
						<div class="help-block">
						</div>
						<div class="desc-bottom well">
							{col_class}
						</div>
					</div>
				</div>
			</div>
		{else}
			<div class="">
				<div class="well">
					<div class="row">
						{if $input.type == 'DeoRowClass'} 
							<label class="choose-class col-lg-12"><input type="checkbox" name="row" class="select-class" value="row"> {l s='Use class row' mod='deotemplate'}</label>
						{/if}
						<label class="control-label col-lg-2">{$input.label}</label>
						<div class="col-lg-10"><input type="text" class="element_class" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" name="{$input.name}"></div>
					</div><br/>
					<div class="desc-bottom">
						{col_class}
					</div>
				</div>
			</div>
		{/if}
	{elseif $input.type == 'bg_select'}
		{$image_uploader}{* HTML form , no escape necessary *}
	{elseif $input.type == 'column_width'}
		<div class="panel panel-default">
			<div class="panel-body">
				<p>{l s='Responsive: You can config width for each Devices' mod='deotemplate'}</p>
			</div>
			<table class="table">
				<thead><tr>
					  <th>{l s='Devices' mod='deotemplate'}</th>
					  <th>{l s='Width' mod='deotemplate'}</th>
				</tr></thead>
				<tbody>
					{foreach $input.columnGrids as $gridKey=>$gridValue}
					<tr>
						<td>
							<span class="col-{$gridKey|escape:'html':'UTF-8'}"></span>
							{$gridValue|escape:'html':'UTF-8'}
						</td>
						<td>
							<div class="btn-group">
								<button type="button" class="btn btn-default deo-btn-width dropdown-toggle" tabindex="-1" data-toggle="dropdown">
									<span class="width-val deo-w-{$fields_value[$gridKey]|replace:'.':'-'|escape:'html':'UTF-8'}">{$fields_value[$gridKey]|escape:'html':'UTF-8'}/12 - ({(($fields_value[$gridKey]|replace:'-':'.'/12)*100)|string_format:"%.2f"}%)</span><span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$widthList item=itemWidth}
										<li>
											<a class="width-select" href="javascript:void(0);" tabindex="-1">
												<span data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" class="width-val deo-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</span>
											</a>
										</li>
									{/foreach}
								</ul>
								<input type='hidden' class="col-val" name='{$gridKey|escape:'html':'UTF-8'}' value="{$fields_value[$gridKey]|escape:'html':'UTF-8'}"/>
							</div>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	{elseif $input.type == 'reloadControler'}
		<div class="col-lg-4"></div>
		<div class="col-lg-8">
			{l s='If website have new a Controller/Modules, click ' mod='deotemplate'} <a class="reload-controller-exception" href="javascript:void(0);">{l s='Reload' mod='deotemplate'}</a>
		</div>
			<script>
				$(document).off('click', '.reload-controller-exception').on('click', '.reload-controller-exception', function(){
					$($globalthis.currentElement).data('form').reloadControllerException = '1';
					var idFormDeoRow = $($globalthis.currentElement).data('form').form_id;
					$('.'+idFormDeoRow+' .btn-edit').first().click();
					$($globalthis.currentElement).data('form').reloadControllerException = '0';
				});
			</script>
	{elseif $input.type == 'img_cat_menu'}
		{assign var=tree value=$input.tree}
		{assign var=imageList value=$input.imageList}
		{assign var=selected_images value=$input.selected_images}
		<div class="form-group form-select-icon">
			<label class="control-label col-lg-4 " for="categories"> {l s='Categories' mod='deotemplate'} </label>
			<div class="col-lg-8">
				{$tree}{* HTML form , no escape necessary *}
				{if isset($input.desc) && $input.desc}
					<p class="help-block">{$input.desc}</p>
				{/if}
			</div>
			<input type="hidden" name="category_img" id="category_img" value='{$selected_images|escape:'html':'UTF-8'}'/>
			<div id="list_image_wrapper" style="display:none">
				<div class="list-image">
					<img id="apci" src="" class="hidden" path="{$input.path_image|escape:'html':'UTF-8'}" widget="DeoCategoryMenu" src-url=""/>
					<a data-for="#apci" href="{$input.href_image|escape:'html':'UTF-8'}" class="categoryimage field-link choose-img choose-img-extend">[{l s='Select image' mod='deotemplate'}]</a>
					<a class="categoryimage field-link remove-img hidden" href="javascript:void(0)">[{l s='Remove image' mod='deotemplate'}]</a>
				  </div>
			</div>
			<script type="text/javascript">
				var full_loaded = true;
				// intiForDeoCategoryMenu();
			</script>
			
		</div>
	{elseif $input.type == 'color'}
		<div class="col-lg-8">
			<div class="input-group colorpicker-element fixed-width-xxl">
				<input type="text" class="color-picker form-control" name="{$input.name}" value="{if isset($fields_value[$input.name]) && ($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'} {/if}">
				<span class="input-group-addon"><i></i></span>
			</div>
		</div>
	{elseif $input.type == 'tags'}
		<div class="col-lg-8">
			{if isset($input.lang) AND $input.lang}
				{if $languages|count > 1}
					<div class="form-group">
				{/if}
				{foreach $languages as $language}
					{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
					{if $languages|count > 1}
						<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
							<div class="col-lg-8">
					{/if}
							{literal}
								<script type="text/javascript">
									$(document).ready(function () {
										var input_id = '{/literal}{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}{literal}';
										$('#'+input_id).tagify({
											originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
										});
									});
								</script>
							{/literal}
							{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
								<div class="input-group{if isset($input.class)} {$input.class}{/if}">
							{/if}
								{if isset($input.maxchar) && $input.maxchar}
									<span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
										<span class="text-count-down">{$input.maxchar|intval}</span>
									</span>
								{/if}
								{if isset($input.prefix)}
									<span class="input-group-addon">{$input.prefix}</span>
								{/if}
								<input type="text"
									id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
									name="{$input.name}_{$language.id_lang}"
									class="{if isset($input.class)}{$input.class}{/if} tagify"
									value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
									onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
									{if isset($input.size)} size="{$input.size}"{/if}
									{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
									{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
									{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
									{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
									{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
									{if isset($input.required) && $input.required} required="required" {/if}
									{if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if}/>
								{if isset($input.suffix)}
									<span class="input-group-addon">{$input.suffix}</span>
								{/if}
							{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
								</div>
							{/if}
					{if $languages|count > 1}
							</div>
							<div class="col-lg-2">
								<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
									{$language.iso_code}
									<i class="icon-caret-down"></i>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$languages item=language}
									<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
									{/foreach}
								</ul>
							</div>
						</div>
					{/if}
				{/foreach}

				{if isset($input.maxchar) && $input.maxchar}
					<script type="text/javascript">
						$(document).ready(function(){
							{foreach from=$languages item=language}
								countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
							{/foreach}
						});
					</script>
				{/if}

				{if $languages|count > 1}
					</div>
				{/if}
			{else}
				{literal}
				<script type="text/javascript">
					$(document).ready(function () {
						var input_id = '{/literal}{if isset($input.id)}{$input.id}{else}{$input.name}{/if}{literal}';
						$('#'+input_id).tagify({
							originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
						});
					});
				</script>
				{/literal}
				
				{assign var='value_text' value=$fields_value[$input.name]}
				{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
					<div class="input-group{if isset($input.class)} {$input.class}{/if}">
				{/if}
					{if isset($input.maxchar) && $input.maxchar}
						<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
					{/if}
					{if isset($input.prefix)}
						<span class="input-group-addon">
						  {$input.prefix}
						</span>
					{/if}
					<input type="text"
						name="{$input.name}"
						id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
						value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
						class="{if isset($input.class)}{$input.class}{/if} tagify"
						{if isset($input.size)} size="{$input.size}"{/if}
						{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
						{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
						{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
						{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
						{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
						{if isset($input.required) && $input.required } required="required" {/if}
						{if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}/>
					{if isset($input.suffix)}
						<span class="input-group-addon">{$input.suffix}</span>
					{/if}

				{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
					</div>
				{/if}
				{if isset($input.maxchar) && $input.maxchar}
					<script type="text/javascript">
						$(document).ready(function(){
							countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
						});
					</script>
				{/if}
			{/if}
		</div> 
	{else}
		{$smarty.block.parent}
	{/if}
{/block}