{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<hr class="separator">
<div class="cart-summary-totals">
	{block name='cart_summary_total'}
		{if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
			<div class="cart-summary-line">
				<span class="label">{$cart.totals.total.label}&nbsp;{$cart.labels.tax_short}</span>
				<span class="value">{$cart.totals.total.value}</span>
			</div>
			<div class="cart-summary-line cart-total">
				<span class="label">{$cart.totals.total_including_tax.label}</span>
				<span class="value">{$cart.totals.total_including_tax.value}</span>
			</div>
		{else}
			<div class="cart-summary-line cart-total">
				<span class="label">{$cart.totals.total.label}&nbsp;{if $configuration.display_taxes_label && $configuration.taxes_enabled}{$cart.labels.tax_short}{/if}</span>
				<span class="value">{$cart.totals.total.value}</span>
			</div>
		{/if}
	{/block}

	{block name='cart_summary_tax'}
		{if $cart.subtotals.tax}
			<div class="cart-summary-line cart-subtotals">
				<span class="label sub">{l s='%label%:' sprintf=['%label%' => $cart.subtotals.tax.label] d='Shop.Theme.Global'}</span>
				<span class="value sub">{$cart.subtotals.tax.value}</span>
			</div>
		{/if}
	{/block}

	{if $waitForShippingCls}
		<div class="cart-summary-line please-select-shipping">
			<span class="label">{l s='Please select a shipping method' mod='deotemplate'}</span>
		</div>
	{/if}
</div>