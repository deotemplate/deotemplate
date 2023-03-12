{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if !empty($product.specific_prices) && ($product.specific_prices.to|date_format:"%Y%m%d%H%m%S" > $product.specific_prices.from|date_format:"%Y%m%d%H%m%S")}
 	<div class="deo-countdown pro" data-text-year="{l s='years' mod='deotemplate'}" data-text-week="{l s='weeks' mod='deotemplate'}" data-text-day="{l s='days' mod='deotemplate'}" data-text-hour="{l s='hours' mod='deotemplate'}" data-text-min="{l s='mins' mod='deotemplate'}" data-text-sec="{l s='secs' mod='deotemplate'}" data-text-finish="{l s='Expired' mod='deotemplate'}" data-time-from="{$product.specific_prices.from}" data-time-to="{$product.specific_prices.to}"></div>
{/if}