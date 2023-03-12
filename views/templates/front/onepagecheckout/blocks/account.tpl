{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


{if isset($customer) && $customer.is_logged && !$customer.is_guest}
	<div class="title-heading register-header h2">
		<span class="title">{l s='Personal Information' mod='deotemplate'}</span>
	</div>
	<div id="hook_displayPersonalInformationTop">{$hook_displayPersonalInformationTop nofilter}</div>
	{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/static-customer-info.tpl' s_customer=$customer}
	{assign parentTplName 'account'}
	{foreach from=$formFieldsAccount item="field"}
		{block name='form_field'}
			{include file='module:deotemplate/views/templates/front/onepagecheckout/_partials/checkout-form-fields.tpl' checkoutSection='account'}
		{/block}
	{/foreach}
{else}
	<div class="account-inner {if $use_tab}use_tab{elseif $type == 'login'}init-one init-with-login{elseif $type == 'register'}init-one init-with-register{elseif $type == 'both'}init-with-both{/if}">
		{if $use_tab}
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#deo-register-box" role="tab" aria-controls="deo-register-box" aria-selected="true">
						<div class="title-tab">{l s='Order without registration' mod='deotemplate'}</div>	
						<div class="sub-title-tab">{l s='I don\'t have my account' mod='deotemplate'}</div>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#deo-login-box" role="tab" aria-controls="deo-login-box" aria-selected="false">
						<div class="title-tab">{l s='Sign in' mod='deotemplate'}</div>	
						<div class="sub-title-tab">{l s='I already have my account' mod='deotemplate'}</div>
					</a>
				</li>
			</ul>
			<div class="tab-content" id="tab-content">
		{/if}
		<div id="deo-login-box" class="login-box{if $use_tab} tab-pane fade{elseif $type == 'login'} active{elseif $type != 'both'} prev{/if}">
			{if !$use_tab}
				<div class="title-heading account-header h2">
					<span class="title">{l s='Sign in' mod='deotemplate'}</span>
				</div>
			{/if}
			{include file='module:deotemplate/views/templates/front/onepagecheckout/blocks/login-form.tpl'}
			{hook h='displayCustomerLoginFormAfter'}
		</div>
		<div id="deo-register-box" class="register-box{if $use_tab} tab-pane fade in active{elseif $type == 'register' && !$use_tab} active{elseif $type != 'both'} next{/if}">
			{if !$use_tab}
				<div class="title-heading register-header h2">
					<span class="title">{l s='Create an account' mod='deotemplate'}</span>
				</div>
			{/if}
			{include file='module:deotemplate/views/templates/front/onepagecheckout/blocks/register-form.tpl'}
			{hook h='displayCustomerLoginFormAfter'}
		</div>
		{if $use_tab}
			</div>
		{/if}
	</div>
{/if}