{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file='customer/page-sidebar.tpl'}

{block name='page_title_sidebar'}
  <h1 class="page-title">{l s='My wishlist' mod='deotemplate'}</h1>
{/block}

{block name='page_right_content'}
	<section id="main">
		<div id="mywishlist">
			<div class="new-wishlist">
				<label for="wishlist_name">{l s='Wishlist name' mod='deotemplate'}</label>
				<div class="input-group js-parent-focus">
					<input type="text" class="form-control js-child-focus" id="wishlist_name" placeholder="{l s='Enter name of new wishlist' mod='deotemplate'}">
					<span class="input-group-btn">
						<button type="submit" class="btn btn-outline deo-save-wishlist-btn">
							<span class="deo-icon-loading-button"></span>
							<span class="text">
								{l s='Save' mod='deotemplate'}
							</span>
						</button>
					</span>
				</div>
				<div class="form-group has-warning">
					<div class="form-control-feedback"></div>			 
				</div>
				<div class="form-group has-success">
					<div class="form-control-feedback"></div>			 
				</div>
				<div class="form-group has-danger">		 
					<div class="form-control-feedback"></div>		 
				</div>
				<script type="text/javascript">
					var deo_msg_empty_wishlist_email = "{l s='List email is not empty!' mod='deotemplate'}";
					var deo_msg_empty_wishlist_name = "{l s='Wishlist name is not empty!' mod='deotemplate'}";
				</script>
			</div>
			<div class="deo-table-list-wishlist">
				<table class="table table-bordered">
					<thead class="wishlist-table-head">
						<tr>
							<th>{l s='Name' mod='deotemplate'}</th>
							<th class="text-sp-center">{l s='Product' mod='deotemplate'}</th>
							<th class="text-sp-center">{l s='Viewed' mod='deotemplate'}</th>
							<th class="text-sp-center wishlist-datecreate-head">{l s='Created' mod='deotemplate'}</th>
							<th class="text-sp-center">{l s='Link View' mod='deotemplate'}</th>
							<th class="text-sp-center">{l s='Default' mod='deotemplate'}</th>
							<th class="text-sp-center">{l s='Delete' mod='deotemplate'}</th>
						</tr>
					</thead>
					<tbody>
						{if count($wishlists)}
							{foreach from=$wishlists item=wishlists_item name=for_wishlists}
								<tr>					 
									<td>
										<a href="javascript:void(0)" class="view-wishlist-product" data-name-wishlist="{$wishlists_item.name}" data-id-wishlist="{$wishlists_item.id_wishlist}">
											<i class="deo-custom-icons deo-icon-loading-inline"></i>
											{$wishlists_item.name}
											{* <span class="view-wishlist-product-loading view-wishlist-product-loading-{$wishlists_item.id_wishlist}"></span> *}
										</a>
									</td>
									<td align="center" class="wishlist-numberproduct wishlist-numberproduct-{$wishlists_item.id_wishlist}">{$wishlists_item.number_product|intval}</td>
									<td align="center">{$wishlists_item.counter|intval}</td>
									<td align="center" class="wishlist-datecreate">{$wishlists_item.date_add}</td>							
									<td align="center">
										<a class="view-wishlist" data-token="{$wishlists_item.token}" target="_blank" href="{$view_wishlist_url}{if $deo_is_rewrite_active}?{else}&{/if}token={$wishlists_item.token}" title="{l s='View' mod='deotemplate'}">{l s='View' mod='deotemplate'}</a>
									</td>
									<td align="center">
										<label class="form-check-label">
											<input class="default-wishlist form-check-input" data-id-wishlist="{$wishlists_item.id_wishlist}" type="checkbox" {if $wishlists_item.default == 1}checked="checked"{/if}>
										</label>
									</td>
									<td align="center">
										<a class="delete-wishlist" data-id-wishlist="{$wishlists_item.id_wishlist}" href="javascript:void(0)" title="{l s='Delete' mod='deotemplate'}"><i class="deo-custom-icons"></i></a>
									</td>
								</tr>
							{/foreach}
						{/if}
					</tbody>
				</table>
			</div>
			<div class="send-wishlist">
				<a class="deo-send-wishlist-button btn btn-outline" href="javascript:void(0)" title="{l s='Send this wishlist' mod='deotemplate'}">
					<span>{l s='Send this wishlist' mod='deotemplate'}</span>
				</a>
			</div>
			<section id="products">
				<div class="deo-wishlist-product products row"></div>
			</section>
			{* <ul class="footer_links">
				<li class="pull-xs-left"><a class="btn btn-outline" href="{$link->getPageLink('my-account', true)|escape:'html'}"><i class="material-icons">&#xE317;</i>{l s='Back to Your Account' mod='deotemplate'}</a></li>
				<li class="pull-xs-right"><a class="btn btn-outline" href="{$urls.base_url}"><i class="material-icons">&#xE88A;</i>{l s='Home' mod='deotemplate'}</a></li>
			</ul> *}
		</div>
		<div class="modal deo-modal deo-modal-wishlist fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h5 class="modal-title">{l s='Confirm' mod='deotemplate'}</h5>
					</div>
					<div class="modal-body">
						<p class="available">{l s='Do you want to delete this wishlist?' mod='deotemplate'}</p>
						<p class="not-available hide">{l s='You can not delete default wishlist!' mod='deotemplate'}</p>
					</div>
					<div class="modal-footer">			
						<button type="button" class="btn btn-outline" data-dismiss="modal">{l s='Cancel' mod='deotemplate'}</button>
						<button type="button" class="deo-modal-wishlist-btn btn btn-outline">						
							<span class="deo-icon-loading-button"></span>
							<span class="text">{l s='Yes' mod='deotemplate'}</span>
						</button>				
					</div>
				</div>
			</div>
		</div>
		<div class="modal deo-modal deo-modal-send-wishlist fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h5 class="modal-title text-xs-center"></h5>
					</div>
					<div class="modal-body">
						<div class="send_wishlist_form_content">
							<form class="form-send-wishlist" action="#" method="post">
								{for $foo=1 to 5}						
									<div class="form-group row">
									  <label class="col-form-label col-md-2 col-sm-3 text-sp-left" for="wishlist_email_{$foo}">{l s='Email' mod='deotemplate'} {$foo}</label>
										<div class="col-form-input col-md-10 col-sm-9">		
									  		<input class="form-control wishlist_email" id="wishlist_email_{$foo}" name="wishlist_email_{$foo}" type="email">
										</div>
									</div>
								{/for}
								<button class="btn btn-outline form-control-submit deo-fake-send-wishlist-button pull-xs-right" type="submit"></button>					  				
							</form>
						</div>
					</div>
					<div class="modal-footer">	
						<button type="button" class="btn btn-outline" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
						<button type="button" class="deo-modal-send-wishlist-btn btn btn-outline">						
							<span class="deo-icon-loading-button"></span>
							<span class="text">
								{l s='Send' mod='deotemplate'}
							</span>
						</button>				
					</div>
				</div>
			</div>
		</div>
	</section>
{/block}

