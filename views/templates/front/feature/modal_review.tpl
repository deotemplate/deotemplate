{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="modal deo deo-modal-review fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title h2 text-xs-center">{l s='Write a review' mod='deotemplate'}</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					{if isset($product_modal_review) && $product_modal_review}
						<div class="product-info clearfix  col-xs-12 col-sm-6">
							<div class="product-image">
								<img class="img-fluid" src="{$productcomment_cover_image}" alt="{$product_modal_review->name|escape:'html':'UTF-8'}" />
							</div>
							<div class="product-meta">
								<h3 class="product_name">{$product_modal_review->name}</h3>
								<div class="product-desc">{$product_modal_review->description_short nofilter}</div>
							</div>
						</div>
					{/if}
					<div class="new_review_form_content col-xs-12 col-sm-6">					
						{if $criterions|@count > 0}
							<ul id="criterions_list">
								{foreach from=$criterions item='criterion'}
									<li>
										{if isset($criterion.name) && $criterion.name != ''}<label class="form-control-label">{$criterion.name|escape:'html':'UTF-8'}:</label>{/if}
										<div class="deo-grade-stars" data-grade="{$criterions|@count}" data-input="criterion[{$criterion.id_deofeature_product_review_criterion}]"></div>

										{* {if isset($criterion.name) && $criterion.name != ''}<label>{$criterion.name|escape:'html':'UTF-8'}:</label>{/if}
										<div class="star_content">
											<input class="deo-star not_uniform" type="radio" name="criterion[{$criterion.id_deofeature_product_review_criterion|round}]" value="1" />
											<input class="deo-star not_uniform" type="radio" name="criterion[{$criterion.id_deofeature_product_review_criterion|round}]" value="2" />
											<input class="deo-star not_uniform" type="radio" name="criterion[{$criterion.id_deofeature_product_review_criterion|round}]" value="3" />
											<input class="deo-star not_uniform" type="radio" name="criterion[{$criterion.id_deofeature_product_review_criterion|round}]" value="4" checked="checked" />
											<input class="deo-star not_uniform" type="radio" name="criterion[{$criterion.id_deofeature_product_review_criterion|round}]" value="5" />
										</div>
										<div class="clearfix"></div> *}
									</li>
								{/foreach}
							</ul>
						{/if}				
						<form class="form-new-review" action="#" method="post">
							<div class="form-group">
								<label class="form-control-label" for="new_review_title">{l s='Title' mod='deotemplate'} <sup class="required">*</sup></label>
								<input type="text" class="form-control" id="new_review_title" required="" name="new_review_title">	  
							</div>
							<div class="form-group">
								<label class="form-control-label" for="new_review_content">{l s='Comment' mod='deotemplate'} <sup class="required">*</sup></label>
								<textarea type="text" class="form-control" id="new_review_content" required="" name="new_review_content"></textarea>				  
							</div>
							{if $allow_guests == true && !$is_logged}
								<div class="form-group">
									<label class="form-control-label" for="new_review_customer_name">{l s='Your name' mod='deotemplate'} <sup class="required">*</sup></label>
									<input type="text" class="form-control" id="new_review_customer_name" required="" name="new_review_customer_name">					  
								</div>
							{/if}
							<div class="form-group">
								<label class="form-control-label"><sup>*</sup> {l s='Required fields' mod='deotemplate'}</label>
								<input id="id_deofeature_product_review" name="id_deofeature_product_review" type="hidden" value="{$product_modal_review->id}"/>
							</div>
							<button class="btn btn-outline form-control-submit deo-fake-button pull-xs-right" type="submit"></button>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{* <button type="button" class="btn btn-outline" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button> *}
				<button type="button" class="deo-modal-review-bt btn btn-outline">
					<span class="deo-icon-loading-button"></span>
					<span class="text">{l s='Submit' mod='deotemplate'}</span>
				</button>
			</div>
		</div>
	</div>
</div>