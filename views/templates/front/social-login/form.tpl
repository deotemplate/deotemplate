{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{assign var="class_form_login" value=""}
{assign var="class_form_register" value=""}
{assign var="class_form_reset_password" value=""}
{if $layout == 'both' || $layout == 'login'}
	{assign var="class_form_login" value="active"}
	{assign var="class_form_register" value="next"}
	{assign var="class_form_reset_password" value="next"}
{else if $layout == 'register'}
	{assign var="class_form_login" value="prev"}
	{assign var="class_form_register" value="active"}
	{assign var="class_form_reset_password" value="next"}
{/if}

<div class="deo-social-login-form-wrapper">
	<div class="proccess-login">
		<i class="login-proccess-icon"></i>
		<span class="login-text-process"></span>
		<a href="javascript:void(0)" class="cancel-form-social-login">{l s='Cancel' mod='deotemplate'}</a>
	</div>
	<div class="deo-social-login-form {($type) ? $type : ''}">
		<div class="deo-form deo-login-form {$class_form_login}">
			<h3 class="login-title">			
				{l s='Login' mod='deotemplate'}
			</h3>
			<form class="form-content" action="#" method="post">
				<div class="form-group form-group-input email">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="icon icon-email"></i>
						</div>
						<input type="email" class="form-control email-login" name="email-login" required="" placeholder="{l s='Email' mod='deotemplate'}">
					</div>
				</div>
				<div class="form-group form-group-input password">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="icon icon-password"></i>
						</div>
						<input type="password" class="form-control password-login" name="password-login" required="" placeholder="{l s='Password' mod='deotemplate'}">
					</div>
				</div>
				{if $check_cookie}
					<div class="form-group form-group-checkbox form-group-links">
						<label class="form-control-label form-checkbox custom-checkbox label-inherit">
							<input type="checkbox" class="keep-login" name="keep-login">
							<span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
							<span>{l s='Keep me signed in?' mod='deotemplate'}</span>
						</label>
						{* <a role="button" href="#" class="call-reset-action">{l s='Forgot Password' mod='deotemplate'} ?</a> *}
					</div>
				{/if}
				<div class="form-group form-buttons">
					<button type="submit" class="form-control-submit form-btn btn-login btn btn-outline">	
						<span class="deo-icon-success"></span>
						<span class="deo-icon-loading-button"></span>
						<span class="text">{l s='Sign In' mod='deotemplate'}</span>
					</button>
					<div class="form-actions">
						<div>{l s='Don\'t have an account?' mod='deotemplate'}</div>
						<a role="button" href="#" class="call-register-action">{l s='Create an account' mod='deotemplate'} </a>
						<span>{l s='Or' mod='deotemplate'}</span>
						<a role="button" href="#" class="call-reset-action">{l s='Reset password' mod='deotemplate'}</a>
					</div>
				</div>
			</form>
		</div>

		<div class="deo-form deo-register-form {$class_form_register}">
			<h3 class="register-title">
				{l s='Register' mod='deotemplate'}
			</h3>
			<form class="form-content" action="#" method="post">
				<div class="form-group form-group-input email">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="icon icon-email"></i>
						</div>
						<input type="email" class="form-control register-email" name="register-email" required="" placeholder="{l s='Email' mod='deotemplate'}">
					</div>
				</div>
				<div class="form-group form-group-input password field-password-policy">
					<div class="js-input-column">
						<div class="input-group js-parent-focus">
							<div class="input-group-addon">
								<i class="icon icon-password"></i>
							</div>
							<input type="password" class="form-control register-password js-child-focus js-visible-password" name="register-password" required placeholder="{l s='Password' mod='deotemplate'}" id="#field-register-password" 
								pattern=".{literal}{{/literal}5,{literal}}{/literal}"
								{if isset($configuration.password_policy.minimum_length)}data-minlength="{$configuration.password_policy.minimum_length}"{/if}
								{if isset($configuration.password_policy.maximum_length)}data-maxlength="{$configuration.password_policy.maximum_length}"{/if}
								{if isset($configuration.password_policy.minimum_score)}data-minscore="{$configuration.password_policy.minimum_score}"{/if}
							>
						</div>
					</div>
				</div>
				<div class="form-group form-group-input repeat-password field-password-policy">
					<div class="js-input-column">
						<div class="input-group js-parent-focus">
							<div class="input-group-addon">
								<i class="icon icon-password"></i>
							</div>
							<input type="password" class="form-control repeat-register-password js-child-focus js-visible-password" name="repeat-register-password" required placeholder="{l s='Repeat Password' mod='deotemplate'}" id="#field-repeat-register-password" 
								pattern=".{literal}{{/literal}5,{literal}}{/literal}"
								{if isset($configuration.password_policy.minimum_length)}data-minlength="{$configuration.password_policy.minimum_length}"{/if}
								{if isset($configuration.password_policy.maximum_length)}data-maxlength="{$configuration.password_policy.maximum_length}"{/if}
								{if isset($configuration.password_policy.minimum_score)}data-minscore="{$configuration.password_policy.minimum_score}"{/if}
							>
						</div>
					</div>
				</div>
				<div class="form-group form-group-input firstname">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="icon icon-firstname"></i>
						</div>
						<input type="text" class="form-control register-firstname" name="firstname"  placeholder="{l s='First Name' mod='deotemplate'}">
					</div>
				</div>
				<div class="form-group form-group-input lastname">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="icon icon-lastname"></i>
						</div>
						<input type="text" class="form-control register-lastname" name="lastname" required="" placeholder="{l s='Last Name' mod='deotemplate'}">
					</div>
				</div>
				{if $check_terms }
					<div class="form-group form-group-checkbox">
						<label class="form-control-label form-checkbox custom-checkbox label-inherit">
							<input type="checkbox" class="register-checkbox" name="register-checkbox">
							<span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
							<span>{l s='I agree with the' mod='deotemplate'} <a href="{$link_term}" class="link-term">{l s='terms and condition.' mod='deotemplate'}</a></span>	
						</label>
					</div>
				{/if}
				<div class="form-group form-buttons">				
					<button type="submit" name="submit" class="form-control-submit form-btn btn-register btn btn-outline">	
						<span class="deo-icon-success"></span>
						<span class="deo-icon-loading-button"></span>
						<span class="text">{l s='Sign Up' mod='deotemplate'}</span>
					</button>
					<div class="form-actions">
						<div>{l s='Already have an account?' mod='deotemplate'}</div>
						<a role="button" href="#" class="call-login-action">{l s='Log in instead' mod='deotemplate'}</a>
						<span>{l s='Or' mod='deotemplate'}</span>
						<a role="button" href="#" class="call-reset-action">{l s='Reset password' mod='deotemplate'}</a>
					</div>
				</div>
			</form>
		</div>

		<div class="deo-form deo-reset-password-form {$class_form_reset_password}">
			<h3 class="reset-password-title">{l s='Reset Password' mod='deotemplate'}</h3>
			<form class="form-content" action="#" method="post">
				<div class="form-group form-group-input reset-password input-group">
					<div class="input-group-addon">
						<i class="icon icon-email"></i>
					</div>
					<input type="email" class="form-control email-reset" name="email-reset" required="" placeholder="{l s='Email Address' mod='deotemplate'}">
				</div>
				<div class="form-group form-buttons">
					<button type="submit" class="form-control-submit form-btn btn-reset-password btn btn-outline">
						<span class="deo-icon-success"></span>
						<span class="deo-icon-loading-button"></span>
						<span class="text">{l s='Send' mod='deotemplate'}</span>
					</button>
					<div class="form-actions only-both-form">
						<div>{l s='Already have an account?' mod='deotemplate'}</div>
						<a role="button" href="#" class="call-login-action">{l s='Log in instead' mod='deotemplate'}</a>
						<span>{l s='Or' mod='deotemplate'}</span>
						<a role="button" href="#" class="call-login-action">{l s='create new account' mod='deotemplate'}</a>
					</div>
					<div class="form-actions">
						<div>{l s='Already have an account?' mod='deotemplate'}</div>
						<a role="button" href="#" class="call-login-action">{l s='Log in instead' mod='deotemplate'}</a>
						<span>{l s='Or' mod='deotemplate'}</span>
						<a role="button" href="#" class="call-register-action">{l s='create new account' mod='deotemplate'}</a>
					</div>
				</div>
			</form>
		</div>

		<div class="deo-form-only-both-form only-both-form">
			<div class="form-group">
				<a role="button" href="#" class="call-reset-action">{l s='Forgot password? Click here to reset password.' mod='deotemplate'}</a>
			</div>
		</div>
	</div>
	{$social nofilter}
</div>
