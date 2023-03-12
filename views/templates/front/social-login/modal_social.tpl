{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="modal deo-message-social-modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="content">
					<div class="deo-social-icon"></div>
					<div class="text-modal loading">
						<p class="h5">{l s='Please wait!' mod='deotemplate'}</p>
						<p>{l s='We are processing.' mod='deotemplate'}</p>
					</div>

					<div class="text-modal error-email">
						<p class="h5">{l s='Can not login without email!' mod='deotemplate'}</p>
						<p>{l s='Please check your account and give me the permission to use your email.' mod='deotemplate'}</p>
					</div>
					
					<div class="text-modal error-login">
						<p class="h5">{l s='Can not login!' mod='deotemplate'}</p>
						<p>{l s='Please contact us or try to login with another way.' mod='deotemplate'}</p>
					</div>
					
					<div class="text-modal success">
						<p class="h5">{l s='Successful!' mod='deotemplate'}</p>
						<p>{l s='Thanks for logging in' mod='deotemplate'}</p>
					</div>
				</div>
			</div> 
		</div>
	</div>
</div>