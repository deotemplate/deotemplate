{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if (isset($nbReviews) && $nbReviews > 0) || (isset($show_zero_review) && $show_zero_review)}
	<div class="deo-product-list-reviews{if isset($show_count) && $show_count} show_count{/if}{if isset($show_text_count) && $show_text_count} show_text_count{/if}{if isset($show_zero_review) && $show_zero_review} show_zero_review{/if}" {if (isset($nbReviews) && $nbReviews > 0)}itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"{/if}>
		<div class="deo-product-list-reviews-wraper">
			{if (int)$averageTotal != (float)$averageTotal}
				{$averageTotal = (float)$averageTotal}
			{else}
				{$averageTotal = (int)$averageTotal}
			{/if}
			<div class="deo_star_content star_content clearfix">
				{section name="foo" start=0 loop=5 step=1}
					{if $averageTotal < $smarty.section.foo.index_next}
						{if ($smarty.section.foo.index_next - $averageTotal) <= 0.5 && ($smarty.section.foo.index_next - $averageTotal) > 0 && is_float($averageTotal)}
							<div class="deo-star star_half"></div>
						{elseif ($smarty.section.foo.index_next - $averageTotal) >= 0.75 && ($smarty.section.foo.index_next - $averageTotal) < 1 && is_float($averageTotal)}
							<div class="deo-star star_quarter"></div>
						{else}
							<div class="deo-star"></div>
						{/if}
					{else}
						<div class="deo-star star_on"></div>
					{/if}
				{/section}
				<meta itemprop="worstRating" content="0"/>
				<meta itemprop="ratingValue" content="{$averageTotal|escape:'html':'UTF-8'}"/>
				<meta itemprop="bestRating" content="5"/>
			</div>

			{if isset($show_count) && $show_count}
				<span class="nb-revews">
					<span class="number-count" itemprop="reviewCount">{$nbReviews}</span>
					{if isset($show_text_count) && $show_text_count}
						<span class="text-count">{l s='Review(s)' mod='deotemplate'}</span>
					{/if}
				</span>
			{/if}
		</div>
	</div>
{/if}
