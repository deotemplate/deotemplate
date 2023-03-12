{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


{if isset($js_custom_vars) && $js_custom_vars|@count}
	<script type="text/javascript">
		{foreach from=$js_custom_vars key=var_name item=var_value}
		if ('object' === typeof {$var_name}{literal}) {
			{/literal}
				jQuery.extend({$var_name}, {$var_value|json_encode nofilter});
			{literal}
		} {/literal} else if ('undefined' !== typeof {$var_name}{literal}) {
			{/literal}
			{$var_name} = {$var_value|json_encode nofilter};
			{literal}
		} else {
			{/literal}
				var {$var_name} = {$var_value|json_encode nofilter};
			{literal}
		}
		{/literal}
		{/foreach}
	</script>
{/if}

<section id="main">
	<div class="title-heading shopping-cart-header h2">
		<span class="title">{l s='Order Summary' d='Shop.Theme.Checkout'}</span>
		<span class="show-details-wrapper">
			<a href="#" data-toggle="collapse" data-target="#deo-cart-summary-product-list" class="show-basket">
				<span class="open-cart">{l s='Show items' mod='deotemplate'}</span>
				<span class="close-cart">{l s='Hide items' mod='deotemplate'}</span> 
			</a>
		</span>
		{if $cartQuantityError}
			<div class="error-msg visible">{$cartQuantityError}</div>
		{/if}
	</div>

	<div class="cart-grid">
		<div class="cart-container">
			{block name='cart_overview'}
				<div class="collapsed in" id="deo-cart-summary-product-list">
					{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/cart-detailed.tpl' cart=$cart}
				</div>
			{/block}
		</div>
		{block name='cart_summary'}
			<div class="cart-summary">
				{block name='cart_totals'}
					{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/cart-detailed-totals.tpl' cart=$cart}
				{/block}
				{block name='hook_shopping_cart'}
					{hook h='displayShoppingCart'}
				{/block}
			</div>
		{/block}
		{block name='hook_shopping_cart_footer'}
			{hook h='displayShoppingCartFooter'}
		{/block}
	</div>
</section>
