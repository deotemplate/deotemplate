{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="deoonepagecheckout-cart-summary" class="opc-checkout-element opc-element deo_class">
	<div class="block-inner">
		{*cart-summary block loaded via Ajax, display dummy container only*}
		<section id="main">
			<div class="cart-grid">
				<div class="cart-container">
					<div class="title-heading shopping-cart-header h2">
						<span class="title">{l s='Order Summary' d='Shop.Theme.Checkout'}</span>
					</div>
				</div>
			</div>
		</section>
		{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/blocks-loader.tpl'}
		<div class="cart-summary"></div>
	</div>
</div>
