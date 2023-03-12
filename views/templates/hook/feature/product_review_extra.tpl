{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if ($nbReviews_product_extra == 0 && $too_early_extra == false && ($customer.is_logged || $allow_guests_extra)) || ($nbReviews_product_extra != 0)}
	<div id="deo_product_reviews_block_extra" class="no-print" {if $nbReviews_product_extra != 0}itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"{/if}>
		{if $nbReviews_product_extra != 0}
			<div class="reviews_note clearfix">
				<span>{l s='Rating' mod='deotemplate'}&nbsp;</span>
				<div class="deo_star_content star_content clearfix">
					{if (int)$ratings_extra.avg != (float)$ratings_extra.avg}
						{$ratings_extra.avg = (float)$ratings_extra.avg}
					{else}
						{$ratings_extra.avg = (int)$ratings_extra.avg}
					{/if}
					{section name="foo" start=0 loop=5 step=1}
						{if $ratings_extra.avg < $smarty.section.foo.index_next}
							{if ($smarty.section.foo.index_next - $ratings_extra.avg) <= 0.5 && ($smarty.section.foo.index_next - $ratings_extra.avg) > 0 && is_float($ratings_extra.avg)}
								<div class="deo-star star_half"></div>
							{elseif ($smarty.section.foo.index_next - $ratings_extra.avg) >= 0.75 && ($smarty.section.foo.index_next - $ratings_extra.avg) < 1 && is_float($ratings_extra.avg)}
								<div class="deo-star star_quarter"></div>
							{else}
								<div class="deo-star"></div>
							{/if}
						{else}
							<div class="deo-star star_on"></div>
						{/if}
					{/section}
					<meta itemprop="worstRating" content="0"/>
					<meta itemprop="ratingValue" content="{$ratings_extra.avg|escape:'html':'UTF-8'}"/>
					<meta itemprop="bestRating" content="5"/>
				</div>
			</div>
		{/if}

		<ul class="reviews_advices">
			{if $nbReviews_product_extra != 0}
				<li>
					<a href="javascript:void(0)" class="read-review">					
						<i class="material-icons">&#xE0B9;</i>
						{l s='Read reviews' mod='deotemplate'} (<span itemprop="reviewCount">{$nbReviews_product_extra}</span>)
					</a>
				</li>
			{/if}
			{if ($too_early_extra == false && ($customer.is_logged || $allow_guests_extra))}
				<li class="{if $nbReviews_product_extra != 0}last{/if}">
					<a class="open-review-form" href="javascript:void(0)" data-id-product="{$id_deofeature_product_review_extra}" data-is-logged="{$customer.is_logged}" data-product-link="{$link_product_review_extra}">
						<i class="material-icons">&#xE150;</i>
						{l s='Write a review' mod='deotemplate'}
					</a>
				</li>
			{/if}
		</ul>
	</div>
{/if}

