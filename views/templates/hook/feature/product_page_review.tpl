{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="product_reviews_product_page">
	{if count($reviews)}
		{foreach from=$reviews item=review}
			{if $review.content}
				<div class="review" itemprop="review" itemscope itemtype="https://schema.org/Review">
					<div class="review-info">
						<div class="review_details">
							<p itemprop="name" class="title">
								<strong>{$review.title}</strong>
							</p>
							<p itemprop="reviewBody" class="reviewBody">{$review.content|escape:'html':'UTF-8'|nl2br nofilter}</p>
							<div class="review_button">
								<div class="deo_star_content star_content clearfix"  itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
									{if (int)$review.grade != (float)$review.grade}
										{$review.grade = (float)$review.grade}
									{else}
										{$review.grade = (int)$review.grade}
									{/if}
									{section name="foo" start=0 loop=5 step=1}
										{if $review.grade < $smarty.section.foo.index_next}
											{if ($smarty.section.foo.index_next - $review.grade) <= 0.5 && ($smarty.section.foo.index_next - $review.grade) > 0 && is_float($review.grade)}
												<div class="deo-star star_half"></div>
											{elseif ($smarty.section.foo.index_next - $review.grade) >= 0.75 && ($smarty.section.foo.index_next - $review.grade) < 1 && is_float($review.grade)}
												<div class="deo-star star_quarter"></div>
											{else}
												<div class="deo-star"></div>
											{/if}
										{else}
											<div class="deo-star star_on"></div>
										{/if}
									{/section}
									<meta itemprop="worstRating" content="0"/>
									<meta itemprop="ratingValue" content="{$review.grade|escape:'html':'UTF-8'}"/>
									<meta itemprop="bestRating" content="5"/>
								</div>

								<ul>
									{* {assign var="not_useful" value=$review.total_advice - $review.total_useful} *}
									{if $allow_usefull_button}
										<li class="deo-usefulness">
											<a href="javascript:void(0)" title="{l s='Useful' mod='deotemplate'}" class="usefulness_btn{if isset($review.customer_advice) && $review.customer_advice == 0} allow{/if}{if $customer.is_logged} logged{/if}" data-is-usefull="1" data-id-product-review="{$review.id_deofeature_product_review}">
												<i class="deo-custom-icons"></i> 
											</a>
											<span>{l s='Useful' mod='deotemplate'} <span class="sum_usefull">{$review.total_advice}</span></span>
										</li>
									{/if}
									{if $allow_report_button}
										<li class="deo-report">
											<a href="javascript:void(0)" title="{l s='Report' mod='deotemplate'}" class="report_btn{if isset($review.customer_report) && $review.customer_report == 0} allow{/if}{if $customer.is_logged} logged{/if}" data-id-product-review="{$review.id_deofeature_product_review}">
												<i class="deo-custom-icons"></i> 
											</a>
											<span>{l s='Report' mod='deotemplate'}</span>
										</li>
									{/if}
								</ul>
							</div>
						</div>
						<div class="review_author">
							<div class="review_author_infos">
								<p class="author" itemprop="author">{$review.customer_name|escape:'html':'UTF-8'}</p>
								<meta itemprop="datePublished" content="{$review.date_add|escape:'html':'UTF-8'|substr:0:10}" />
								<em>{dateFormat date=$review.date_add|escape:'html':'UTF-8' full=0}</em>
							</div>
						</div>
					</div>
				</div> <!-- .review -->
			{/if}
		{/foreach}
		{if (!$too_early && ($customer.is_logged || $allow_guests))}
			<div class="open-review-form-wrapper">
				<a class="open-review-form" href="javascript:void(0)" data-id-product="{$id_product_tab_content}" data-is-logged="{$customer.is_logged}" data-product-link="{$link_product_tab_content}">
					<i class="icon-write-review deo-custom-icons"></i>
					{l s='Write a review' mod='deotemplate'}
				</a>
			</div>
		{/if}
	{else}
		{if (!$too_early && ($customer.is_logged || $allow_guests))}
			<div class="open-review-form-wrapper">
				<a class="open-review-form" href="javascript:void(0)" data-id-product="{$id_product_tab_content}" data-is-logged="{$customer.is_logged}" data-product-link="{$link_product_tab_content}">
					<i class="icon-write-review deo-custom-icons"></i>
					{l s='Be the first to write your review!' mod='deotemplate'}
				</a>
			</div>
		{else}
			<p class="align_center">{l s='No customer reviews for the moment.' mod='deotemplate'}</p>
		{/if}
	{/if}
</div> 

