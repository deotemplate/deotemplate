{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if !empty($product.specific_prices) && ($product.specific_prices.to|date_format:"%Y%m%d%H%m%S" > $product.specific_prices.from|date_format:"%Y%m%d%H%m%S")}
	<div class="countdown-product-page simple-countdown">
		<h4 class="title-countdown">{l s='Hungry up' d='Shop.Theme.Global'}</h4>
	 	<div class="deo-countdown pro" data-text-year="{l s='years' d='Shop.Theme.Global'}" data-text-week="{l s='weeks' d='Shop.Theme.Global'}" data-text-day="{l s='days' d='Shop.Theme.Global'}" data-text-hour="{l s='hours' d='Shop.Theme.Global'}" data-text-min="{l s='mins' d='Shop.Theme.Global'}" data-text-sec="{l s='secs' d='Shop.Theme.Global'}" data-text-finish="{l s='Expired' d='Shop.Theme.Global'}" data-time-from="{$product.specific_prices.from}" data-time-to="{$product.specific_prices.to}"></div>
	</div>
{/if}