{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


<div id="delivery_block">
	<label for="delivery_message">{l s='If you would like to add a comment about your order, please write it in the field below.' d='Shop.Theme.Checkout'}</label>
	<textarea rows="5" id="delivery_message" class="form-control" name="delivery_message">{$delivery_message|replace:'&#039;':'\''|replace:'&quot;':'"'}</textarea>
</div>
