{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $field.type == 'hidden'}

	{block name='form_field_item_hidden'}
		<input type="hidden" class="orig-field" name="{$field.name}" value="{$field.value|default}">
	{/block}

{else}

	{assign var="passwordShallBeVisible" value=false}
	{if $field.type === 'password' && isset($parentTplName) && $parentTplName === 'account'}
		{assign var=show_create_account_checkbox value=$ps_config.PS_GUEST_CHECKOUT_ENABLED && $create_account_checkbox && (!$customer.is_logged || $customer.is_guest)}
		{if $show_create_account_checkbox}
			{assign var="passwordShallBeVisible" value=(isset($opc_form_checkboxes['create-account']) && 'true' == $opc_form_checkboxes['create-account'])}
			<div id="create_account" class="form-group checkbox col-sp-12">
				<label class="custom-checkbox label-inherit">
					<input type="checkbox" name="create-account" class="orig-field" data-link-action="deo-create-account"
						{if $passwordShallBeVisible}
							checked="checked"
						{else}
							{$field.visible=false}{*hide password field, when $show_create_account_checkbox=YES && checkboxes['create-account']=NO*}
						{/if}
					>
					<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
					<span>{l s='Create an account with password and save time on your next order (optional)' mod='deotemplate'}</span>
				</label>
			</div>
		{/if}
	{/if}
	{capture name="form_group_classes"}
		form-group
		form-group-input 
		{$field.name}
		{if $field.required} mark-required{/if}
		{if isset($checkoutSection) && $checkoutSection == 'invoice' && in_array($field.name, $businessFieldsList)}business-field{/if}
		{if isset($checkoutSection) && $checkoutSection == 'invoice' && in_array($field.name, $privateFieldsList)}private-field{/if}
		{if isset($checkoutSection) && $checkoutSection == 'invoice' && in_array($field.name, $businessDisabledFieldsList)}business-disabled-field{/if}
		{$field.type}
		{if (false == $field.visible) && !($field.type === 'password' && $passwordShallBeVisible)} hidden{/if}
		{if !empty($field.errors)} has-error{/if}
		{if $field.type === 'select' && empty($field.availableValues)} hidden{/if}
		{if $field.type === 'password' && empty($field.availableValues)} js-input-column{/if}
	{/capture}
	{if $field.type === 'password' && empty($field.availableValues) && isset($registerForm) && $registerForm}
		<div class="field-password-policy">	
	{/if}	
		<div class="{$smarty.capture.form_group_classes|strip|trim} {if isset($checkoutSection) && $checkoutSection == 'login'}col-sp-12{else}col-sp-{$field.width}{/if}{if ($field.type === 'password')} js-input-column{/if}">

			{if !in_array($field.type, ['radio-buttons', 'checkbox'])}
				<span class="form-control-label">
					{$field.label}
				</span>
			{/if}

			{if $field.type === 'select'}

				{block name='form_field_item_select'}
					<select class="form-control orig-field form-control-select{if $field.live} live{/if}" name="{$field.name}" {if $field.required}required{/if}>
						<option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
						{foreach from=$field.availableValues key="value" item="label"}
							<option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
						{/foreach}
					</select>
				{/block}

			{elseif $field.type === 'countrySelect'}

				{block name='form_field_item_country'}
					<select
						class="form-control orig-field form-control-select js-country{if $field.live} live{/if}"
						name="{$field.name}"
						{if $field.required}required{/if}
					>
						<option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
						{foreach from=$field.availableValues key="option_value" item="label"}
							{if is_array($label)}
								{assign var="label_label" value=$label.label}
								{assign var="option_data" value=$label.option_data}
							{else}
								{assign var="label_label" value=$label}
								{assign var="option_data" value=""}
							{/if}
							<option {$option_data} value="{$option_value}" {if $option_value eq $field.value} selected {/if}>{$label_label}</option>
						{/foreach}
					</select>
				{/block}

			{elseif $field.type === 'radio-buttons'}

				{block name='form_field_item_radio'}
					<label class="form-control-label">
						{$field.label}
					</label>
					<div class="available-values {$field.name}">
						{foreach from=$field.availableValues item="label" key="value"}
							<label class="radio-inline label-inherit" for="field-{$field.name}-{$value}">
								<span class="custom-radio">
									<input
										id="field-{$field.name}-{$value}"
										name="{$field.name}"
										type="radio"
										value="{$value}"
										class="orig-field"
										{if $field.required}required{/if}
										{if $value eq $field.value} checked {/if}
									>
									<span></span>
								</span>
								<span>{$label}</span>	
							</label>
						{/foreach}
					</div>
				{/block}

			{elseif $field.type === 'checkbox'}

				{block name='form_field_item_checkbox'}
					<label class="custom-checkbox label-inherit">
						<input class="orig-field" name="{$field.name}" type="checkbox" value="1" {if $field.value}checked="checked"{/if} {if $field.required}required{/if}>
						<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
						{*Although validator is complaining, very same syntax with 'nofilter' is used also in ./themes/classic/templates/_partials/form-fields.tpl; this is to allow HTML in checkbox label*}
						<span class="js-terms">{$field.label nofilter}</span>
					</label>
				{/block}

			{elseif $field.type === 'date'}

				{block name='form_field_item_date'}
					<input name="{$field.name}" class="form-control orig-field" type="date" value="{$field.value|default}" placeholder="{if isset($field.availableValues.placeholder)}{$field.availableValues.placeholder}{/if}">
				{/block}

			{elseif $field.type === 'birthday'}

				{block name='form_field_item_birthday'}
					<div class="js-parent-focus">
						{$field.label}
						{html_select_date
						field_order=DMY
						time={$field.value|default}
						field_array={$field.name}
						prefix=false
						reverse_years=true
						field_separator='<br>'
						day_extra='class="form-control orig-field form-control-select"'
						month_extra='class="form-control orig-field form-control-select"'
						year_extra='class="form-control orig-field form-control-select"'
						day_empty={l s='-- day --' d='Shop.Forms.Labels'}
						month_empty={l s='-- month --' d='Shop.Forms.Labels'}
						year_empty={l s='-- year --' d='Shop.Forms.Labels'}
						start_year={'Y'|date}-100 end_year={'Y'|date}
						}
					</div>
				{/block}

			{elseif $field.type === 'password'}

				{block name='form_field_item_password'}
					<span class="input-group js-parent-focus">
						<input
							class="form-control orig-field js-child-focus js-visible-password"
							name="{$field.name}"
							type="password"
							{if isset($configuration.password_policy.minimum_length)}data-minlength="{$configuration.password_policy.minimum_length}"{/if}
							{if isset($configuration.password_policy.maximum_length)}data-maxlength="{$configuration.password_policy.maximum_length}"{/if}
							{if isset($configuration.password_policy.minimum_score)}data-minscore="{$configuration.password_policy.minimum_score}"{/if}
							value=""
							pattern=".{literal}{{/literal}5,{literal}}{/literal}"
							{if $field.required}required{/if}
							placeholder=" " 
						>
						<span class="input-group-btn">
							<button type="button"
								data-action="show-password"
								data-text-show="{l s='Show' d='Shop.Theme.Actions'}"
								data-text-hide="{l s='Hide' d='Shop.Theme.Actions'}" 
								data-link-action="toggle-password-visibility" 
								class="btn icon-remove-red-eye"> 
							</button>
						</span>
					</span>
				{/block}

			{else} 

				{* standard text inputs *}
				{if $field.name === 'birthday' && isset($field.availableValues.placeholder)}
					{assign var='placeholder' value="{$field.availableValues.placeholder}" }
				{else}
					{assign 'placeholder' ' '}
				{/if}

				{* Remove call prefix from phone number - for display purposes, if prefix is shown separately *}
				{* Part 1 *}
				{if $show_call_prefix && ($field.name === 'phone' || $field.name === 'phone_mobile')}
					{assign 'callPrefix' '+'|cat:$field.custom_data['call_prefix']}
					{$field.value = $field.value|replace:{$callPrefix}:''}
				{/if}
				{* Part 2, displayed after input field, due to 'modern' theme, which uses placeholder shown CSS selectors *}
				{if $show_call_prefix && ($field.name === 'phone' || $field.name === 'phone_mobile')}
					<span class="input-group">
						<span class="input-group-btn">
							<button type="button" class="country-call-prefix input-group-text btn">{$callPrefix}</button>
						</span>
				{/if}
				{block name='form_field_item_other'}
					<input
						class="form-control orig-field{if $field.live} live{/if}"
						name="{$field.name}"
						type="{$field.type}"
						value="{$field.value|default}"
						placeholder="{$placeholder}"
						{if $field.autoCompleteAttribute}autocomplete="{$field.autoCompleteAttribute}"{/if}
						{if $field.maxLength}maxlength="{$field.maxLength}"{/if}
						{if $field.required}required{/if}
					>
				{/block}
				{if $show_call_prefix && ($field.name === 'phone' || $field.name === 'phone_mobile')}
					</span>
				{/if}
			{/if}

			{block name='form_field_comment'}
				{if (!$field.required && !in_array($field.type, ['radio-buttons', 'checkbox'])) || isset($field.availableValues.comment)}
					<div class="form-control-comment">
						{if !$field.required}
							<span class="optional">
								{l s='Optional' d='Shop.Forms.Labels'}
							</span>
						{/if}
						{if isset($field.availableValues.comment)}
							<span class="comment">
								{$field.availableValues.comment}
							</span>
						{/if}
					</div>
				{/if}
			{/block}
		</div>
	{if $field.type === 'password' && empty($field.availableValues) && isset($registerForm) && $registerForm}
		</div>	
	{/if}	
{/if}
