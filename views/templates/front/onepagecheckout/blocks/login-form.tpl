{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{block name='login_form'}
	<form id="login-form" action="{block name='login_form_actionurl'}{$action}{/block}" method="post">
		<section class="form-fields row">
			{block name='login_form_fields'}
				{foreach from=$formFieldsLogin item="field"}
					{block name='form_field'}
						{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/checkout-form-fields.tpl' checkoutSection='login'}
					{/block}
				{/foreach}
			{/block}
		</section>

		<div class="forgot-password">
			<a href="{$urls.pages.password}" rel="nofollow">
				{l s='Forgot your password?' d='Shop.Theme.Customeraccount'}
			</a>
		</div>

		{block name='login_form_footer'}
			<footer class="form-footer clearfix">
				<input type="hidden" name="submitLogin" value="1">
				{block name='form_buttons'}
					<button class="btn btn-primary" data-link-action="deo-sign-in" type="button" class="form-control-submit">
						{l s='Sign in' d='Shop.Theme.Actions'}
					</button>
				{/block}
				<div class="hidden successful-login">{l s='Login successful, please wait...' mod='deotemplate'}</div>
			</footer>

		{/block}
		<div class="alert alert-danger" id="errors-login-form" style="display: none"></div>
	</form>
	{if !$use_tab && ($type == 'login' || $type == 'register')}
		<div class="offer-account offer-login">
			<span class="offer-text">
				{l s='Don\'t have an account?' mod='deotemplate'}
			</span>
			<a href="javascript:void(0)" class="change-register">
				{l s='Create an account!' mod='deotemplate'}
			</a>
		</div>
	{/if}	
{/block}