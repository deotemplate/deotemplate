{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="error-msg">{l s='Please select a payment method' mod='deotemplate'}</div>
<div class="title-heading payment-method-header h2">
	<span class="title">{l s='Payment method' d='Shop.Theme.Checkout'}</span>
</div>
{if $payment_block_wait_for_address|count}
	<div class="waiting-block">
		<p class="sub-heading-waiting-block">{l s='First, please enter your: ' mod='deotemplate'}</p> 
		<ul>
			{foreach $payment_block_wait_for_address as $field_name}
				<li>{$field_name}</li>
			{/foreach}
		</ul>
	</div>
{else}
	{block name='payment_options'}
		{hook h='displayPaymentTop'}

		{if $is_free}
			<p class="cart-payment-step-not-needed-info">{l s='No payment needed for this order' d='Shop.Theme.Checkout'}</p>
		{/if}
		{if isset($payment_data)}
			<div id="payment_data">
				{foreach from=$payment_data key="data_key" item="data_item"}
					<input type="hidden" id="payment_data_{$data_key}" value="{$data_item}">
				{/foreach}
			</div>
		{/if}
		<div class="payment-options {if $is_free}hidden-xs-up{/if}">
			{foreach from=$payment_options key="module_name" item="module_options"}
				{foreach from=$module_options item="option" name="multioptions"}
					<div id="{$option.id}-main-title" class="deo-main-payment" data-payment-module="{$module_name}">
						<div id="{$option.id}-container" class="payment-option clearfix">
							{* This is the way an option should be selected when Javascript is enabled *}
							<span class="custom-radio float-xs-left">
								<input
									class="ps-shown-by-js {if $option.binary} binary {/if}"
									id="{$option.id}"
									data-module-name="{if ''!=$option.module_name}{$option.module_name}{else}{$module_name}{/if}{if $smarty.foreach.multioptions.index>0}-{$smarty.foreach.multioptions.index}{/if}" 
									{if $selected_payment_option == $option.id || $is_free}checked {/if}
									name="payment-option"
									type="radio"
									required
								>
								<span></span>
							</span>

							<label for="{$option.id}">
								<span class="payment-name">{$option.call_to_action_text}</span>
								{if $option.logo}
									<img src="{$option.logo}">
								{/if}
							</label>
						</div>
						{if $option.additionalInformation}
							<div id="{$option.id}-additional-information" class="js-additional-information definition-list additional-information {$module_name}{if $option.id != $selected_payment_option} ps-hidden{/if}">
								{$option.additionalInformation nofilter}
							</div>
						{/if}
						<div id="pay-with-{$option.id}-form" class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}">
							{if $option.form}
								{$option.form nofilter}
							{else}
								<form class="payment-{$option.id}-form" method="POST" action="{$option.action nofilter}">
									{foreach from=$option.inputs item=input}
										<input type="{$input.type}" name="{$input.name}" value="{$input.value}">
									{/foreach}
									<button style="display:none" id="pay-with-{$option.id}" type="submit"></button>
								</form>
							{/if}
						</div>
					</div>
				{/foreach}
				{foreachelse}
				<p class="alert alert-danger">{l s='Unfortunately, there are no payment method available.' d='Shop.Theme.Checkout'}</p>
			{/foreach}
		</div>
		{hook h='displayPaymentByBinaries'}
		<div class="modal fade" id="modal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="js-modal-content"></div>
				</div>
			</div>
		</div>
	{/block}
{/if}
