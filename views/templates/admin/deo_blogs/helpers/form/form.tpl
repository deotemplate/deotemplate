{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file="helpers/form/form.tpl"}
{block name="input"}
	{if $input.type == 'date_deoblog'}
		<div class="row">
			<div class="input-group col-lg-4">
				<input
					id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
					type="text"
					data-hex="true"
					{if isset($input.class)} class="{$input.class}"
					{else}class="datepicker"{/if}
					name="{$input.name}"
					value="{if isset($input.default) && $fields_value[$input.name] == ''}{$input.default}{else}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" />
				<span class="input-group-addon">
					<i class="icon-calendar-empty"></i>
				</span>
			</div>
		</div>
	{elseif $input.type == 'script_image'}
		<div id="modal_select_image" class="modal fade form-setting" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
		                <span class="sr-only">{l s='Close' mod='deotemplate'}</span></button>
		                <h4 class="modal-title2">{l s='Image manager' mod='deotemplate'}</h4>
		            </div>
		            <div class="modal-body"></div>
		                <div class="modal-footer">
		                <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
		            </div>
		        </div>
		    </div>
		</div>
		<div id="deo_loading" class="deo-loading" style="display: none;">
		    <div class="spinner">
		        <div class="item-1"></div>
		        <div class="item-2"></div>
		        <div class="item-3"></div>
		    </div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".image-choose").DeoImageSelector();
			});
		</script>
	{elseif $input.type == 'tags'}
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
	{else}
		{$smarty.block.parent}
	{/if}
{/block}