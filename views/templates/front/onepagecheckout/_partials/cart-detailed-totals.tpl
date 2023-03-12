{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{block name='cart_detailed_totals'}
	{if $shipping_block_wait_for_address|count}
		{assign var='waitForShippingCls' value=' wait-for-shipping'}
	{else}
		{assign var='waitForShippingCls' value=''}
	{/if}
	<div class="cart-detailed-totals">
		<div class="cart-detailed-subtotals">
			{foreach from=$cart.subtotals item="subtotal"}
				{if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
					<div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
						<span class="label{if 'products' === $subtotal.type} js-subtotal{/if}">
							{if 'products' == $subtotal.type}
								{$cart.summary_string}
							{else}
								{$subtotal.label}
							{/if}
						</span>
						<span class="value">
							{if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}
						</span>
						{if $subtotal.type === 'shipping'}
								<div><small class="value">{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small></div>
						{/if}
					</div>
				{/if}
			{/foreach}
			{if isset($cart.subtotals.products.amount)}
				{$subtotal_price=$cart.subtotals.products.amount}
			{/if}
			{if isset($cart.subtotals.shipping.amount)}
				{$shipping_price=$cart.subtotals.shipping.amount}
			{/if}
			{$shipping_free_price = Configuration::get('PS_SHIPPING_FREE_PRICE')}
			{if $subtotal_price < $shipping_free_price && $shipping_price > 0}
				<div class="cart-summary-free-shipping">
					<span class="value">
						{l s='Spend [1]%currency_sign%%shipping_free_need%[/1] more and get free shipping' sprintf=['[1]' => "<strong>",'%currency_sign%' => $currency.sign,'%shipping_free_need%' => str_replace('.', ',', strval($shipping_free_price - $subtotal_price)),'[/1]' => "</strong>"] mod='deotemplate'}
					</span>
				</div>
			{/if}
		</div>
		{block name='cart_summary_totals'}
			{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/cart-summary-totals.tpl'}
		{/block}
		{block name='cart_voucher'}
			{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/cart-voucher.tpl'}
		{/block}
	</div>
{/block}
