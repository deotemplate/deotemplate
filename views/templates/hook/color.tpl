{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $product}
	{if $colors}
		{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction}
			{if $product.specific_prices.reduction_type == 'percentage'}
				{$product.specific_prices.reduction*100}
			{else}
				{($product.specific_prices.reduction/$product.price_without_reduction)*100}
			{/if}
			{foreach from=$colors item=color key=k}	
				{if $k >= $sale }
					<div>
						{$color nofilter}{* HTML form , no escape necessary *}
					</div>
					{break}
				{/if}
			{/foreach}
		{/if}
	{/if}		
{/if}