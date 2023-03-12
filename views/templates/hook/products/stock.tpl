{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $product.quantity > 0}
	<div class="deo-quantity-stock">
		<span class="number-count">{$product.quantity}</span>
		<span class="">{if $product.quantity == 1}{l s='item' mod='deotemplate'}{else}{l s='items' mod='deotemplate'}{/if}</span>
	</div>
{/if}
