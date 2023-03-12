{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
 
{if $formAtts.isLogged}
	<div class="DeoSocialLogin deo-social-login-builder logged{if $formAtts.quicklogin_type == 'dropdown'} deo-login-popup dropdown{elseif $formAtts.quicklogin_type == 'dropup'} deo-register-popup dropup{/if}{if isset($formAtts.class)} {$formAtts.class}{/if} js-dropdown popup-over">
		<a class="popup-title deo-social-login" href="javascript:void(0)" data-toggle="dropdown">
			<i class="icon deo-custom-icons"></i>
		    <span class="name-simple">{l s='Account' mod='deotemplate'}</span>
		    <i class="icon-arrow-down deo-custom-icons"></i>
		</a>
		<ul class="dropdown-menu popup-content social-login-selector user-info">
			<li class="account-name">
				<a class="account dropdown-item" href="{$formAtts.my_account_url}" title="{l s='View My Account' mod='deotemplate'}" rel="nofollow">
					<span>{l s='Hello' mod='deotemplate'} {$formAtts.customerName}</span>
				</a>
			</li>
			{if (int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST')}
				<li class="wishlist-popup">
					<a
						class="deo-btn-wishlist dropdown-item"
						href="{url entity='module' name='deotemplate' controller='mywishlist'}"
						title="{l s='Wishlist' mod='deotemplate'}"
						rel="nofollow"
					>
						<span>{l s='Wishlist' mod='deotemplate'} <span class="deo-total-wishlist deo-total"></span></span>
					</a>
				</li>
			{/if}
			{if (int) DeoHelper::getConfig('ENABLE_PRODUCT_COMPARE')}
				<li class="compare-popup">
					<a
						class="deo-btn-compare dropdown-item"
						href="{url entity='module' name='deotemplate' controller='compare'}"
						title="{l s='Compare' mod='deotemplate'}"
						rel="nofollow"
					>
						<span>{l s='Compare' mod='deotemplate'} <span class="deo-total-compare deo-total"></span></span>
					</a>
				</li>
			{/if}
			<li class="check-out">
				<a
					class="checkout dropdown-item"
					href="{url entity='cart' params=['action' => show]}"
					title="{l s='Checkout' d='Shop.Theme.Customeraccount'}"
					rel="nofollow"
				>
					<span>{l s='Checkout' d='Shop.Theme.Actions'}</span>
				</a>
			</li>
			<li class="sign-out-popup">
				<a class="logout dropdown-item" href="{$formAtts.logout_url}" rel="nofollow">    
					<span>{l s='Sign out' d='Shop.Theme.Actions'}</span>
				</a>
			</li>
		</ul>
	</div>
{else}
	{if $formAtts.quicklogin_type == 'html'}
		<div class="DeoSocialLogin deo-social-login-builder html {if $formAtts.quicklogin_layout == 'both'}both{else}only-one{/if}{if isset($formAtts.class)} {$formAtts.class}{/if}">
			{$formAtts.html_form nofilter}
		</div>
	{else}
		{if $formAtts.quicklogin_display == 'login' || $formAtts.quicklogin_display == 'both'}
			<div class="DeoSocialLogin deo-social-login-builder deo-login-popup {$formAtts.quicklogin_type} {if $formAtts.quicklogin_layout == 'both'}both{else}only-one{/if}{if isset($formAtts.class)} {$formAtts.class}{/if}{if in_array($formAtts.quicklogin_type, ['dropdown','dropup'])} js-dropdown{/if}{if $formAtts.quicklogin_type == 'dropdown'} dropdown{elseif $formAtts.quicklogin_type == 'dropup'} dropup{/if}{if $formAtts.quicklogin_display == 'both'} both-title{/if} popup-over">
				<a href="javascript:void(0)" 
					class="deo-social-login{if in_array($formAtts.quicklogin_type, ['dropdown','dropup'])} dropdown-toggle{/if} popup-title" 
					title="{l s='Login' mod='deotemplate'}" rel="nofollow" 
					data-enable-sociallogin="{if isset($formAtts.quicklogin_sociallogin)}{$formAtts.quicklogin_sociallogin}{/if}" 
					data-type="{$formAtts.quicklogin_type}" 
					data-layout="login"
					{if $formAtts.quicklogin_type == 'dropdown' || $formAtts.quicklogin_type == 'dropup'} 
						data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
					{/if}
				>
					<i class="icon deo-custom-icons"></i>
					<span class="name-simple">{l s='Login' mod='deotemplate'}</span>
					<i class="icon-arrow-down deo-custom-icons"></i>
				</a>
				{if $formAtts.quicklogin_type == 'dropdown' || $formAtts.quicklogin_type == 'dropup'}
					<div class="dropdown-menu popup-content">
						{$formAtts.html_form nofilter}
					</div>
				{/if}
			</div>
		{/if}
		{if $formAtts.quicklogin_display == 'register' || $formAtts.quicklogin_display == 'both'}
			<div class="DeoSocialLogin deo-social-login-builder deo-register-popup {$formAtts.quicklogin_type} {if $formAtts.quicklogin_layout == 'both'}both{else}only-one{/if}{if isset($formAtts.class)} {$formAtts.class}{/if}{if in_array($formAtts.quicklogin_type, ['dropdown','dropup'])} js-dropdown{/if}{if $formAtts.quicklogin_type == 'dropdown'} dropdown{elseif $formAtts.quicklogin_type == 'dropup'} dropup{/if}{if $formAtts.quicklogin_display == 'both'} both-title{/if} popup-over">
				<a href="javascript:void(0)" 
					class="deo-social-login{if in_array($formAtts.quicklogin_type, ['dropdown','dropup'])} dropdown-toggle{/if} popup-title" 
					title="{l s='Register' mod='deotemplate'}" rel="nofollow" 
					data-enable-sociallogin="{if isset($formAtts.quicklogin_sociallogin)}{$formAtts.quicklogin_sociallogin}{/if}" 
					data-type="{$formAtts.quicklogin_type}" 
					data-layout="register"
					{if $formAtts.quicklogin_type == 'dropdown' || $formAtts.quicklogin_type == 'dropup'} 
						data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
					{/if}
				>
					<i class="icon deo-custom-icons"></i>
					<span class="name-simple">{l s='Register' mod='deotemplate'}</span>
					<i class="icon-arrow-down deo-custom-icons"></i>
				</a>
				{if $formAtts.quicklogin_type == 'dropdown' || $formAtts.quicklogin_type == 'dropup'}
					<div class="dropdown-menu popup-content">
						{$formAtts.html_form nofilter}
					</div>
				{/if}
			</div>
		{/if}
	{/if}
{/if}

