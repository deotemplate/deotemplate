{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<!-- Block languages module -->
<div class="popup-over deo_customer_actions dropdown js-dropdown{if isset($formAtts.languageselector) && $formAtts.languageselector && isset($formAtts.currencyselector) && $formAtts.currencyselector && isset($formAtts.customersignin) && $formAtts.customersignin} show-all{/if} {(isset($formAtts.class)) ? $formAtts.class : ''}">
	<a href="javascript:void(0)" data-toggle="dropdown" class="popup-title" title="{l s='Setting' mod='deotemplate'}">
		<i class="icon icon-settings"></i>
		<span class="name-simple">{l s='Setting' mod='deotemplate'}</span>
		<span class="relationship">
			{if isset($formAtts.languageselector) && $formAtts.languageselector}
				<span class="language">{$current_language.iso_code}</span>
			{/if}
			{if isset($formAtts.currencyselector) && $formAtts.currencyselector}
				<span class="currency">{$current_currency.iso_code}</span>
			{/if}
		</span>
		<i class="icon-arrow-down deo-custom-icons"></i>
	</a>
	<div class="popup-content dropdown-menu">
		{if isset($formAtts.customersignin) && $formAtts.customersignin}
			<div class="customer-block">
				<ul class="user-info">
					{if $logged}
						<li class="account-name">
							<a
								class="account dropdown-item" 
								href="{$urls.pages.my_account}"
								title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}"
								rel="nofollow"
							>
								<span>{l s='Hello' mod='deotemplate'} {$customerName}</span>
							</a>
						</li>
					{else}
						<li class="sign-up">
							{if (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE')}
								<a
									class="signup deo-social-login dropdown-item"
									href="javascript:void(0)" 
									data-enable-sociallogin="{if DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_ENABLE') || DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_ENABLE') || DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_ENABLE')}1{else}0{/if}" 
									data-type="popup" 
									data-layout="register"
									title="{l s='Register' mod='deotemplate'}"
									rel="nofollow"
								>
									<span>{l s='Register' mod='deotemplate'}</span>
								</a>
							{else}
								<a
									class="signup dropdown-item"
									href="{$urls.pages.register}"
									title="{l s='Register' mod='deotemplate'}"
									rel="nofollow"
								>
									<span>{l s='Register' mod='deotemplate'}</span>
								</a>
							{/if}
						</li>
					{/if}
					{if (int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST')}
						<li class="wishlist-popup">
							<a
								class="deo-btn-wishlist dropdown-item"
								href="{url entity='module' name='deotemplate' controller='mywishlist'}"
								title="{l s='Wishlist' mod='deotemplate'}"
								rel="nofollow"
							>
								<span>{l s='Wishlist' mod='deotemplate'}<span class="deo-total-wishlist deo-total">0</span></span>
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
								<span>{l s='Compare' mod='deotemplate'}<span class="deo-total-compare deo-total">0</span></span>
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
					{if $logged}
						<li class="my-account">
							<a
								class="myacount dropdown-item"
								href="{$urls.pages.my_account}"
								title="{l s='My account' mod='deotemplate'}"
								rel="nofollow"
							>
								<span>{l s='My account' mod='deotemplate'}</span>
							</a>
						</li>
						<li class="sign-out-popup">
							<a
								class="logout dropdown-item"
								href="{$urls.actions.logout}"
								rel="nofollow"
							>
								<span>{l s='Sign out' mod='deotemplate'}</span>
							</a>
						</li>
					{else}
						<li class="sign-in-popup">
							{if (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE')}
								<a
									href="javascript:void(0)"
									class="signin deo-social-login dropdown-item"
									data-enable-sociallogin="{if DeoHelper::getConfig('SOCIAL_LOGIN_FACEBOOK_ENABLE') || DeoHelper::getConfig('SOCIAL_LOGIN_GOOGLE_ENABLE') || DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_ENABLE')}1{else}0{/if}" 
									data-type="popup" 
									data-layout="login"
									title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}"
									rel="nofollow"
								>
									<span>{l s='Sign in' d='Shop.Theme.Actions'}</span>
								</a>
							{else}
								<a
									class="signin dropdown-item"
									href="{$urls.pages.authentication}?back={$urls.current_url|urlencode}"
									title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}"
									rel="nofollow"
								>
									<span>{l s='Sign in' d='Shop.Theme.Actions'}</span>
								</a>
							{/if}
						</li>
					{/if}
				</ul>
			</div>
		{/if}
		<div class="language-currency-block">
			{if isset($formAtts.languageselector) && $formAtts.languageselector && count($languages)}
				<div class="language-selector">
					<p>{l s='Language:' mod='deotemplate'}</p>
					<ul class="link">
						{foreach from=$languages item=language}
							<li {if $language.id_lang == $current_language.id_lang} class="current" {/if}>
								<a href="{url entity='language' id=$language.id_lang}">
									<img src="{$img_lang_url}{$language.id_lang}.jpg" alt="{$language.iso_code}" title="{$language.name}"/>
								</a>
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
			{if isset($formAtts.currencyselector) && $formAtts.currencyselector && count($currencies)}
				<div class="currency-selector">
					<p>{l s='Currency:' mod='deotemplate'}</p>
					<ul class="link">
						{foreach from=$currencies item=currency}
							<li {if $currency.current} class="current" {/if}>
								<a title="{$currency.name}" rel="nofollow" href="{$currency.url}">{if $current_currency.iso_code !== $current_currency.sign}{$currency.sign}{/if} {$currency.iso_code}</a>
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
		</div>
	</div>
</div>

<!-- /Block languages module -->
