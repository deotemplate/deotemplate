{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


<div class="error-msg">{l s='Please select a shipping method' mod='deotemplate'}</div>
{block name='shipping_options'}
	<div class="title-heading shipping-method-header h2">
		<span class="title">{l s='Shipping Method' d='Shop.Theme.Checkout'}</span>
	</div>
	{if $shipping_block_wait_for_address|count}
		<div class="waiting-block">
			<p class="sub-heading-waiting-block">{l s='First, please enter your: ' mod='deotemplate'}</p> 
			<ul>
				{foreach $payment_block_wait_for_address as $field_name}
					<li>{$field_name}</li>
				{/foreach}
			</ul>
		</div>
	{else}
		{if isset($shippingAddressNotice) && $shippingAddressNotice|count}
			<div class="shipping-address-notice">
				{l s='Shipping Address' d='Shop.Theme.Checkout'}: <span class="country-name">{$shippingAddressNotice|join:', '}</span>
			</div>
		{/if}
		<div id="hook-display-before-carrier">
			{$hookDisplayBeforeCarrier nofilter}
		</div>
		<div class="delivery-options-list">
			{if $delivery_options|count}
				<form id="js-delivery" class="clearfix" data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectDeliveryOption']}" method="post">
					{block name='delivery_options'}
						<div class="delivery-options">
							{foreach from=$delivery_options item=carrier key=carrier_id}
								<div class="delivery-option-wrapper">
									<div class="delivery-option-row delivery-option{if isset($carrier.external_module_name) && "" != $carrier.external_module_name} {$carrier.external_module_name}{/if}{if (isset($customerSelectedDeliveryOption) && $carrier_id == $customerSelectedDeliveryOption)} user-selected{/if}">
										<div class="shipping-radio">
											<span class="custom-radio float-xs-left">
												<input type="radio" name="delivery_option[{$id_address}]" id="delivery_option_{$carrier.id}" value="{$carrier_id}"{if $delivery_option == $carrier_id && (isset($customerSelectedDeliveryOption) && $carrier_id == $customerSelectedDeliveryOption)} checked{/if}>
												<span></span>
											</span>
										</div>
										<label for="delivery_option_{$carrier.id}" class="delivery-option-label delivery-option-2">
											<div class="delivery-option-detail">
												{if $carrier.logo}
													<div class="delivery-option-logo">
														<img src="{$carrier.logo}" alt="{$carrier.name}"/>
													</div>
												{/if}
												<div class="delivery-option-name {if $carrier.logo}has-logo{else}no-logo{/if}">
													<span class="carrier-name">{$carrier.name}</span>
												</div>
											</div>
											<div class="delivery-option-delay">
												<span class="carrier-delay">{$carrier.delay}</span>
											</div>
											<div class="delivery-option-price">
												<span class="carrier-price">{$carrier.price}</span>
											</div>
										</label>
									</div>
									<div class="carrier-extra-content{if "1" === $carrier.is_module} {$carrier.external_module_name}{/if}"{if $delivery_option != $carrier_id} style="display:none;"{/if}>{$carrier.extraContent nofilter}</div>
									<div class="clearfix"></div>
								</div>
							{/foreach}
						</div>
					{/block}
					<div class="order-options">
						{if $recyclablePackAllowed}
							<div class="form-group form-group-input">
								<label class="custom-checkbox label-inherit">
									<input type="checkbox" id="input_recyclable" name="recyclable" value="1" {if $recyclable} checked {/if}>
									<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
									<span for="input_recyclable">{l s='I would like to receive my order in recycled packaging.' d='Shop.Theme.Checkout'}</span>
								</label>
							</div>
						{/if}

						{if $gift.allowed}
							<div class="form-group form-group-input">
								<label class="custom-checkbox label-inherit">
									<input class="js-gift-checkbox" id="input_gift" name="gift" type="checkbox" value="1" {if $gift.isGift}checked="checked"{/if}>
									<span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
									<span for="input_gift">{$gift.label}</span>
								</label>
							</div>
							<div id="gift" class="collapse{if $gift.isGift} in show{/if}">
								<label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' d='Shop.Theme.Checkout'}</label>
								<textarea rows="2" id="gift_message" class="form-control" name="gift_message">{$gift.message}</textarea>
							</div>
						{/if}
					</div>
				</form>
			{else}
				<p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' d='Shop.Theme.Checkout'}</p>
			{/if}
		</div>
		<div id="hook-display-after-carrier">
			{$hookDisplayAfterCarrier nofilter}
		</div>
		<div id="extra_carrier"></div>
	{/if}
{/block}
