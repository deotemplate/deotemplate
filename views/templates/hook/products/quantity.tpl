{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="wrapper-deo-cart-quantity{if isset($show_label_quantity) && $show_label_quantity} show_label{/if}" data-show_label_quantity="{$show_label_quantity}">
	{if isset($show_label_quantity) && $show_label_quantity}
		<label class="label-name">{l s='Quantity:' mod='deotemplate'}</label>
	{/if}
	{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > $product.minimal_quantity}
		{assign var="qty" value=$product.product_attribute_minimal_quantity}
	{else}
		{assign var="qty" value=$product.minimal_quantity}
	{/if}
	{if (($PS_ORDER_OUT_OF_STOCK && !$PS_STOCK_MANAGEMENT) || ($PS_ORDER_OUT_OF_STOCK && $PS_STOCK_MANAGEMENT)) && $qty == 0}
		{assign var="qty" value="1"}
	{/if}
	<input type="number" name="deo_cart_quantity" class="input-group form-control deo-cart-quantity" data-id-product="{$product.id_product}" value="{$qty}" data-min="{$qty}" data-max="{if (($PS_ORDER_OUT_OF_STOCK && !$PS_STOCK_MANAGEMENT) || ($PS_ORDER_OUT_OF_STOCK && $PS_STOCK_MANAGEMENT))}999999999{else}{$product.quantity}{/if}">
</div>
	
