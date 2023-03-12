{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $product.add_to_cart_url}
	{assign var="text_btn" value={l s='Add to cart' mod='deotemplate'}}
	{assign var="text_title" value={l s='Add to cart' mod='deotemplate'}}
{else}
	{if $product.quantity == 0}
		{assign var="text_btn" value={l s='Out of stock' mod='deotemplate'}}
		{if count($product.main_variants) <= 1}
			{assign var="text_title" value={l s='Out of stock' mod='deotemplate'}}
		{else count($product.main_variants) > 1}
			{assign var="text_title" value={l s='This product available with different options. Please go to product page to select other combination.' mod='deotemplate'}}
		{/if}
	{else if isset($product.embedded_attributes.customization_required) && $product.embedded_attributes.customization_required}
		{assign var="text_btn" value={l s='Add to cart' mod='deotemplate'}}
		{assign var="text_title" value={l s='This product have customization required. Please go to product page to complete the customization field.' mod='deotemplate'}}
	{else}
		{assign var="text_btn" value={l s='Add to cart' mod='deotemplate'}}
		{assign var="text_title" value={l s='Add to cart' mod='deotemplate'}}
	{/if}
{/if}

<div class="button-container btn-cart-product-list">
	<form action="{$link_cart}" method="post">
		<input type="hidden" name="token" value="{$static_token}">
		{if !$product.add_to_cart_url}
			<input type="hidden" value="{$product.quantity}" class="quantity_product quantity_product_{$product.id_product}" name="quantity_product">
			<input type="hidden" value="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity>$product.minimal_quantity}{$product.product_attribute_minimal_quantity}{else}{$product.minimal_quantity}{/if}" class="minimal_quantity minimal_quantity_{$product.id_product}" name="minimal_quantity">
		{/if}
		<input type="hidden" value="{$product.id_product_attribute}" class="id_product_attribute id_product_attribute_{$product.id_product}" name="id_product_attribute">
		<input type="hidden" value="{$product.id_product}" class="id_product" name="id_product">
		<input type="hidden" name="id_customization" value="{if $product.id_customization}{$product.id_customization}{/if}" class="product_customization_id">
		{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > $product.minimal_quantity}
			{assign var="qty" value=$product.product_attribute_minimal_quantity}
		{else}
			{assign var="qty" value=$product.minimal_quantity}
		{/if}
		{if (($PS_ORDER_OUT_OF_STOCK && !$PS_STOCK_MANAGEMENT) || ($PS_ORDER_OUT_OF_STOCK && $PS_STOCK_MANAGEMENT)) && $qty == 0}
			{assign var="qty" value="1"}
		{/if}
		<input type="hidden" class="input-group form-control qty qty_product qty_product_{$product.id_product}" name="qty" value="{$qty}" data-min="{$qty}">
		<button class="btn btn-product add-to-cart deo-btn-cart deo-btn-cart_{$product.id_product}{if !$product.add_to_cart_url} disabled{/if}" type="submit" data-toggle="deo-tooltip" data-position="top" title="{$text_title}" data-id_product_attribute="{$product.id_product_attribute}" data-id_product="{$product.id_product}">
			<span class="content-btn-product">
				<i class="icon-btn-product icon-cart"></i>
				<i class="loading-btn-product"></i>
				<span class="name-btn-product">{$text_btn}</span>
			</span>
		</button>
	</form>
</div>