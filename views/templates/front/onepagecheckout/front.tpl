{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file=$layout}

{block name='content'}
	<div id="empty-cart-notice">
		<div class="card cart-container">
			<div class="card-block">
				<h1>{l s='Cart is empty' d='Shop.Notifications.Error'}</h1>
				<hr class="separator">
				<a class="btn btn-outline continue" href="{$urls.pages.index}">
					{l s='Continue shopping' d='Shop.Theme.Actions'}
				</a>
			</div>
		</div>
		{* {hook h='displayCrossSellingShoppingCart'} *}
	</div>
	<div id="deo-onepagecheckout-container">
		{include file="module:deotemplate/views/templates/front/onepagecheckout/{$plist_key}.tpl"}

		{* This element will be added by JS script as overlay on binary payment methods *}
		<div class="save-account-overlay hidden">
			<button type="button" class="btn btn-primary center-block" data-link-action="deo-save-account-overlay">
				<i class="loading-btn-product"></i>
				<span class="text-btn">
					{l s='Confirm & Show payment' mod='deotemplate'}
				</span>
			</button>
		</div>

		{* This element is artificaly created, as "parent" element for calling prepareConfirmation *}
		<div id="prepare_confirmation" class="hidden"></div>
		{* <div id="payment_forms_persistence"></div> *}
	</div>
	<div class="modal fade" id="deo-modal-terms">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="js-modal-content"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline" data-dismiss="modal">{l s='Close' d='Shop.Theme.Global'}</button>
				</div>
			</div>
		</div>
	</div>
{/block}
