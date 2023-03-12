{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{* {if isset($login_page) && $login_page} *}
	<div class="deo-social-line-spacing"><span>{l s='Or' mod='deotemplate'}</span></div>
{* {/if} *}
<div class="deo-social-login-links clearfix">
	<h3 class="deo-social-login-title">
		{l s='Connect with Social Networks' mod='deotemplate'}
	</h3>
	{if $fb_enable && $fb_app_id != ''}
		{* <div class="fb-login-button" data-max-rows="1" data-size="large" data-button-type="login_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false" scope="public_profile,email" onlogin="checkLoginState();"></div> *}
		<button class="btn social-login-btn facebook">
			<i class="deo-custom-icons"></i>
			<span>{l s='Facebook' mod='deotemplate'}</span>
		</button>
	{/if}

	{if $google_enable && $google_client_id != ''}
		{* <div class="g-signin2" data-scope="profile email" data-longtitle="true" data-theme="dark" data-onsuccess="googleSignIn" data-onfailure="googleFail"></div> *}
		<button class="btn social-login-btn google">
			<i class="deo-custom-icons"></i>
			<span>{l s='Google' mod='deotemplate'}</span>
		</button>
	{/if}

	{if $twitter_enable && $twitter_api_key != '' && $twitter_api_secret !== ''}
		<button class="btn social-login-btn twitter">
			<i class="deo-custom-icons"></i>
			<span>{l s='Twitter' mod='deotemplate'}</span>
		</button>
	{/if}
</div>

