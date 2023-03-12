{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<tr class="comparison_header">
	<td class="feature-name td_empty">
		<strong>{l s='Reviews' mod='deotemplate'}</strong>
	</td>
	{foreach from=$list_ids_product item=id_product}
		<td class="comparison_infos product-{$id_product}" align="center">
			{if isset($product_reviews[$id_product]) && $product_reviews[$id_product]}		
				<div class="dropup deo-compare-review-dropdown">
					<a href="javascript:void(0)" class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						{l s='View reviews' mod='deotemplate'}					
					</a>
					<div class="dropdown-menu">						
						{foreach from=$product_reviews[$id_product] item=review}	
							<div class="dropdown-item well well-sm">
								<strong>{$review.customer_name|escape:'html':'UTF-8'} </strong>
								<small class="date"> {dateFormat date=$review.date_add|escape:'html':'UTF-8' full=0}</small>
								<div class="review_title">{$review.title|escape:'html':'UTF-8'|nl2br}</div>
								<div class="review_content">{$review.content|escape:'html':'UTF-8'|nl2br}</div>
							</div>
						{/foreach}
					</div>
				</div>
			{else}
				-
			{/if}
		</td>	
	{/foreach}
</tr>


{foreach from=$grades item=grade key=grade_id}
	<tr>
		<td class="feature-name">
			<strong>{$grade}</strong>
		</td>
		{foreach from=$list_ids_product item=id_product}
			{assign var='tab_grade' value=$product_grades[$grade_id]}
			<td class="comparison_infos ajax_block_product product-{$id_product}" align="center">
				{if isset($tab_grade[$id_product]) && $tab_grade[$id_product]}
					<div class="product-rating">
						{if (int)$tab_grade[$id_product] != (float)$tab_grade[$id_product]}
							{$tab_grade[$id_product] = (float)$tab_grade[$id_product]}
						{else}
							{$tab_grade[$id_product] = (int)$tab_grade[$id_product]}
						{/if}
						<div class="deo_star_content star_content clearfix">
							{section name="foo" start=0 loop=5 step=1}
								{if $tab_grade[$id_product] < $smarty.section.foo.index_next}
									{if ($smarty.section.foo.index_next - $tab_grade[$id_product]) <= 0.5 && ($smarty.section.foo.index_next - $tab_grade[$id_product]) > 0 && is_float($tab_grade[$id_product])}
										<div class="deo-star star_half"></div>
									{elseif ($smarty.section.foo.index_next - $tab_grade[$id_product]) >= 0.75 && ($smarty.section.foo.index_next - $tab_grade[$id_product]) < 1 && is_float($tab_grade[$id_product])}
										<div class="deo-star star_quarter"></div>
									{else}
										<div class="deo-star"></div>
									{/if}
								{else}
									<div class="deo-star star_on"></div>
								{/if}
							{/section}
						</div>

						{* {section loop=6 step=1 start=1 name=average}
							<input class="auto-submit-star not_uniform" disabled="disabled" type="radio" name="{$grade_id}_{$id_product}_{$smarty.section.average.index}" {if isset($tab_grade[$id_product]) && $tab_grade[$id_product]|round != 0 && $smarty.section.average.index == $tab_grade[$id_product]|round}checked="checked"{/if} />
						{/section} *}
					</div>
				{else}
					-
				{/if}
			</td>
		{/foreach}
	</tr>				
{/foreach}

<tr>
	<td class="feature-name">
		<strong>{l s='Average' mod='deotemplate'}</strong>
	</td>
	{foreach from=$list_ids_product item=id_product}
		<td class="comparison_infos product-{$id_product}" align="center" >
			{if isset($list_product_average[$id_product]) && $list_product_average[$id_product]}
				<div class="product-rating" {$list_product_average[$id_product]}>
					{if (int)$list_product_average[$id_product] != (float)$list_product_average[$id_product]}
						{$list_product_average[$id_product] = (float)$list_product_average[$id_product]}
					{else}
						{$list_product_average[$id_product] = (int)$list_product_average[$id_product]}
					{/if}
					<div class="deo_star_content star_content clearfix">
						{section name="foo" start=0 loop=5 step=1}
							{if $list_product_average[$id_product] < $smarty.section.foo.index_next}
								{if ($smarty.section.foo.index_next - $list_product_average[$id_product]) <= 0.5 && ($smarty.section.foo.index_next - $list_product_average[$id_product]) > 0 && is_float($list_product_average[$id_product])}
									<div class="deo-star star_half"></div>
								{elseif ($smarty.section.foo.index_next - $list_product_average[$id_product]) >= 0.75 && ($smarty.section.foo.index_next - $list_product_average[$id_product]) < 1 && is_float($list_product_average[$id_product])}
									<div class="deo-star star_quarter"></div>
								{else}
									<div class="deo-star"></div>
								{/if}
							{else}
								<div class="deo-star star_on"></div>
							{/if}
						{/section}
					</div>
					{* {section loop=6 step=1 start=1 name=average}
						<input class="auto-submit-star not_uniform" disabled="disabled" type="radio" name="average_{$id_product}" {if $list_product_average[$id_product]|round != 0 && $smarty.section.average.index == $list_product_average[$id_product]|round}checked="checked"{/if} />
					{/section} *}
				</div>
			{else}
				-
			{/if}
		</td>	
	{/foreach}
</tr>

