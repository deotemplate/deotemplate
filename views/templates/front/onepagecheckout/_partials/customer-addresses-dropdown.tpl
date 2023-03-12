{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


{if ("invoice" == $addressType) && isset($addressesList.invoice)}
	{assign var='addressesCombobox' value=$addressesList.invoice}
{elseif ("delivery" == $addressType) && isset($addressesList.delivery)}
	{assign var='addressesCombobox' value=$addressesList.delivery}
{/if}
{*otherwise, addressCombobox won't be set and we won't continue*}

{if isset($addressesCombobox) && $addressesCombobox|@count > 0}
	{assign var='hideAddressesSelection' value=($addressesCombobox|@count == 1 &&
	(("invoice" == $addressType && $idAddressInvoice|array_key_exists:$addressesCombobox)
	|| ("delivery" == $addressType && $idAddressDelivery|array_key_exists:$addressesCombobox)))}
	<div class="customer-addresses{if $addressesCombobox|@count == 1} hidden-1{/if}">
		{if $hideAddressesSelection}
			<a href="javascript:void(0)" class="custom-link btn btn-outline" data-link-action="deo-add-new-address">{l s='Add a new address' mod='deotemplate'}</a>
		{/if}
		<div class="addresses-selection{if $hideAddressesSelection} hidden{/if}">
			<span class="saved-addresses-label label">{l s='Addresses:' mod='deotemplate'}</span>
			<select class="not-extra-field form-control-select deo-{$addressType}-addresses">
				<option value="-1">{l s='New address...' mod='deotemplate'}</option>
				{foreach $addressesCombobox as $address}
					<option value="{$address.id}"
						{if "invoice" == $addressType}
							{if $address.id == $idAddressInvoice && ($isInvoiceAddressPrimary || $idAddressInvoice != $idAddressDelivery )} selected{/if}
							{if $address.id == $lastOrderInvoiceAddressId && $address.id != $idAddressDelivery && (!$isInvoiceAddressPrimary && $idAddressInvoice == $idAddressDelivery )} selected{/if}
							{if $address.id == $idAddressDelivery && $idAddressInvoice != $idAddressDelivery} disabled{/if}
						{else}
							{if $address.id == $idAddressDelivery && (!$isInvoiceAddressPrimary || $idAddressInvoice != $idAddressDelivery )} selected{/if}
							{if $address.id == $lastOrderDeliveryAddressId && $address.id != $idAddressInvoice && ($isInvoiceAddressPrimary && $idAddressInvoice == $idAddressDelivery )} selected{/if}
							{if $address.id == $idAddressInvoice && $idAddressInvoice != $idAddressDelivery} disabled{/if}
						{/if}
					>{$address.alias}</option>
				{/foreach}
			</select>
		</div>
	</div>
{/if}
