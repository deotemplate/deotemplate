{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $product && isset($product.js)}
	<div class="deal-clock-wrapper{if $product.js == 'unlimited'} unlimited{/if}">
		<ul class="deal-clock" data-text-day="{l s='days' mod='deotemplate'}" data-text-hour="{l s='hours' mod='deotemplate'}" data-text-min="{l s='min' mod='deotemplate'}" data-text-sec="{l s='sec' mod='deotemplate'}" data-text-finish="{l s='Expired' mod='deotemplate'}" {if $product.js != 'unlimited'}data-target-date="{$product.js.month}/{$product.js.day}/{$product.js.year} {$product.js.hour}:{$product.js.minute}:{$product.js.seconds}"{/if}></ul>
	</div>
{/if}