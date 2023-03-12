{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}



{block name='register_form'}
	<form id="register-form" class="account-fields">
		{block name="account_form_fields"}
			<section class="form-fields row">
				{block name='form_fields'}
					{assign parentTplName 'account'}
					{foreach from=$formFieldsAccount item="field"}
						{block name='form_field'}
							{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/checkout-form-fields.tpl' registerForm=true checkoutSection='account'}
						{/block}
					{/foreach}
				{/block}
				{$hook_create_account_form nofilter}
			</section>
		{/block}
		{if !$use_tab && ($type == 'login' || $type == 'register')}
			<div class="offer-account offer-register">
				<span class="offer-text">
					{l s='Already have an account?' mod='deotemplate'}
				</span>
				<a href="javascript:void(0)" class="change-login">
					{l s='Sign in!' mod='deotemplate'}
				</a>
			</div>
		{/if}
	</form>
{/block}