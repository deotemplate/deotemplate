{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="block-inner-address-invoice">
	{if $offer_second_address}
		<div class="sub-title-heading h3">{l s='Billing address' mod='deotemplate'}</div>
	{/if}
	{if $show_i_am_business}
		<div class="business-customer">
			<span class="custom-checkbox">
				<input id="i_am_business" type="checkbox" data-link-action="deo-i-am-business" {if !$hideBusinessFields}checked="checked"{/if} disabled="disabled">
				<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
				<label for="i_am_business">{l s='I am a business customer' mod='deotemplate'}</label>
			</span>
		</div>
	{/if}
	{if $show_i_am_private}
		<div class="private-customer">
			<span class="custom-checkbox">
				<input id="i_am_private" type="checkbox" data-link-action="deo-i-am-private" {if !$hidePrivateFields}checked="checked"{/if} disabled="disabled">
				<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
				<label for="i_am_private">{l s='I am a private customer' mod='deotemplate'}</label>
			</span>
		</div>
	{/if}

	<form class="address-fields{if $show_i_am_business} show_i_am_business{if $hideBusinessFields} hideBusinessFields{/if}{/if}{if $show_i_am_private} show_i_am_private{if $hidePrivateFields} hidePrivateFields{/if}{/if}" data-address-type="invoice">
		{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/customer-addresses-dropdown.tpl' addressType='invoice'}
		{block name="address_invoice_form_fields"}
			<section class="form-fields row">
				{block name='form_fields'}
					{if $show_i_am_business}
						<div class="business-fields-container"><div class="business-fields-separator"></div></div>
					{/if}
					{if $show_i_am_private}
						<div class="private-fields-container"><div class="private-fields-separator"></div></div>
					{/if}
					{foreach from=$formFieldsInvoice item="field"}
						{block name='form_field'}
							{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/checkout-form-fields.tpl' checkoutSection='invoice'}
						{/block}
					{/foreach}
				{/block}
			</section>
		{/block}
	</form>
	{if $isInvoiceAddressPrimary}
		<div class="second-address" {if !$offer_second_address}style="display: none;"{/if}>
			<span class="custom-checkbox">
				<input type="checkbox" data-link-action="deo-ship-to-different-address" id="ship-to-different-address" {if $showShipToDifferentAddress} checked{/if}>
				<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
				<label for="ship-to-different-address">{l s='Ship to a address delivery' mod='deotemplate'}</label>
			</span>
		</div>
	{/if}
</div>
