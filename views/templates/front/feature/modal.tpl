{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="modal deo-modal deo-modal-cart fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title h6 text-xs-center deo-warning deo-alert">
					<i class="material-icons">info_outline</i>				
					{l s='You must enter a quantity' mod='deotemplate'}		
				</h4>
				<h4 class="modal-title h6 text-xs-center deo-info deo-alert">
					<i class="material-icons">info_outline</i>				
					{l s='The minimum purchase order quantity for the product is ' mod='deotemplate'}<strong class="alert-min-qty"></strong>
				</h4>	
				<h4 class="modal-title h6 text-xs-center deo-block deo-alert">				
					<i class="material-icons">block</i>				
					{l s='There are not enough products in stock' mod='deotemplate'}
				</h4>
			</div>
		</div>
	</div>
</div>