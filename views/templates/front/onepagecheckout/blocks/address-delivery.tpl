{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="block-inner-address-delivery">
	{if $offer_second_address}
		<div class="sub-title-heading h3">{l s='Delivery Address' mod='deotemplate'}</div>
	{/if}
	<form class="address-fields" data-address-type="delivery">
		{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/customer-addresses-dropdown.tpl' addressType='delivery'}
		{block name="address_delivery_form_fields"}
			<section class="form-fields row">
				{block name='form_fields'}
					{foreach from=$formFieldsDelivery item="field"}
						{block name='form_field'}
							{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/checkout-form-fields.tpl' checkoutSection='delivery'}
						{/block}
					{/foreach}
				{/block}
			</section>
		{/block}
	</form>
	{if !$isInvoiceAddressPrimary}
		<div class="second-address" {if !$offer_second_address}style="display: none;"{/if}>
			<span class="custom-checkbox">
				<input type="checkbox" data-link-action="deo-bill-to-different-address" id="bill-to-different-address" {if $showBillToDifferentAddress} checked{/if}>
				<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
				<label for="bill-to-different-address">{l s='Bill to a different address' mod='deotemplate'}</label>
			</span>
		</div>
	{/if}
</div>
