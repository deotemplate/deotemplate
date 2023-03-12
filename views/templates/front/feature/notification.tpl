{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="deo-notification" style="width: {$width_notification}; {$vertical_position_notification}:{$vertical_position_value_notification}; {$horizontal_position_notification}:{$horizontal_position_value_notification};">
</div>
<div class="deo-temp deo-temp-success">
	<div class="notification-wrapper">
		<div class="notification notification-success">
			<strong class="noti product-name"></strong>
			<span class="noti noti-update">{l s='The product has been updated in your shopping cart' mod='deotemplate'}</span>
			<span class="noti noti-delete">{l s='The product has been removed from your shopping cart' mod='deotemplate'}</span>
			<span class="noti noti-add"><strong class="noti-special"></strong> {l s='Product successfully added to your shopping cart' mod='deotemplate'}</span>
			<span class="notification-close">X</span>
		</div>
	</div>
</div>
<div class="deo-temp deo-temp-error">
	<div class="notification-wrapper">
		<div class="notification notification-error">
			<span class="noti noti-update">{l s='Error updating' mod='deotemplate'}</span>
			<span class="noti noti-delete">{l s='Error deleting' mod='deotemplate'}</span>
			<span class="noti noti-add">{l s='Error adding. Please go to product detail page and try again' mod='deotemplate'}</span>
			<span class="notification-close">X</span>
			
		</div>
	</div>
</div>
<div class="deo-temp deo-temp-warning">
	<div class="notification-wrapper">
		<div class="notification notification-warning">
			<span class="noti noti-min">{l s='The minimum purchase order quantity for the product is' mod='deotemplate'} <strong class="noti-special"></strong></span>
			<span class="noti noti-max">{l s='There are not enough products in stock' mod='deotemplate'}</span>
			<span class="noti noti-check">{l s='You must enter a quantity' mod='deotemplate'}</span>
			<span class="notification-close">X</span>
		</div>
	</div>
</div>