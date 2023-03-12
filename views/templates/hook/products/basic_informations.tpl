{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="basic-informations">
	{if isset($product.reference_to_display) && $product.reference_to_display}
		<div class="information reference">
			<label class="label">{l s='Reference' mod='deotemplate'}</label>
			<span class="value">{$product.reference_to_display}</span>
		</div>
	{/if}
	{if isset($product.manufacturer_name) && $product.manufacturer_name}
		<div class="information manufacturer">
			<label class="label">{l s='Manufacturer' mod='deotemplate'}</label>
			<a href="{$link->getManufacturerLink($product.id_manufacturer)}" class="value" title="{$product.manufacturer_name}">{$product.manufacturer_name}</a>
		</div>
	{/if}

	{if isset($product.features) && $product.features}
		{foreach from=$product.features item=feature}
			<div class="information feature">
				<label class="label">{$feature.name}</label>
				<span class="value">{$feature.value}</span>
			</div>
		{/foreach}
	{/if}
</div>