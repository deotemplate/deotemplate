{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="deoonepagecheckout-payment" class="opc-checkout-element primary-block opc-element deo_class">
	<div class="block-inner">
		<section id="checkout-payment-step" class="js-current-step">
			<div class="dynamic-content">
				{*payment block loaded via Ajax, display dummy container only*}
				<div class="title-heading shipping-method-header">
					<span class="title">{l s='Payment method' d='Shop.Theme.Checkout'}</span>
				</div>
				{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/blocks-loader.tpl'}
			</div>
		</section>
		<div class="static-content"></div>
		<div class="popup-payment-content">
			<div class="popup-header">
				<div class="popup-close-icon"></div>
				<div class="popup-shop-info">
					<div class="popup-shop-logo"><img src="{$shop.logo}"></div>
					<div class="popup-shop-name">{$shop.name}</div>
				</div>
			</div>
			<div class="popup-body">
				<div class="popup-payment-form"></div>
				<div class="popup-payment-button">
					{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/payment-confirmation-button.tpl'}
				</div>
			</div>
		</div>
	</div>
</div>