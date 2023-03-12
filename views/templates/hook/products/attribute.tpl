{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
		
{if isset($groups)}
	<div class="deo-attr-list-container{if $show_value_text} show-value-text{/if}{if !$show_name_attribute} hide-name-attribute{/if}{if $show_color} show-color{/if}{* {if array_sum($group.attributes_quantity) <= 0} out_stock_all{/if} *}" data-show_value_text="{$show_value_text}" data-show_name_attribute="{$show_name_attribute}" data-show_color="{$show_color}">
		{foreach from=$groups key=id_attribute_group item=group}
			{if $group.attributes|@count}
				<div class="deo-attr-list{if $group.group_type == 'color'} list-color{/if}" data-group-id="{$id_attribute_group}">
					{if ($show_name_attribute)}
						<label class="group-name">{$group.name}</label>
					{/if}
					<span class="group-values">
						{if ($group.group_type == 'color' && $show_color)}
							{foreach from=$group.attributes key=id_attribute item=group_attribute name=posts}
								{if !$PS_DISPLAY_UNAVAILABLE_ATTR && $group.attributes_quantity[$id_attribute] <= 0}
									{* continue next loop *}
								{else}
									<a href="javascript:void(0)" class="deo-attr-item{if $group.attributes_quantity[$id_attribute] <= 0} out-stock{/if}{if isset($product.attributes[$id_attribute_group]['id_attribute']) && $product.attributes[$id_attribute_group]['id_attribute'] == $id_attribute} selected{/if}" title="{$group.name}: {$group_attribute}" style="{if $group.colors[$id_attribute]['type']}background-image: url('{$group.colors[$id_attribute]['value']}');{else}background-color:{$group.colors[$id_attribute]['value']};{/if}" data-toggle="deo-tooltip" data-position="top" data-product-attribute="{$id_attribute}" data-total-quantity="{$group.attributes_quantity[$id_attribute]}">
										{if $show_value_text && !$smarty.foreach.posts.last},{/if}
									</a>
								{/if}
							{/foreach}
						{else}
							{foreach from=$group.attributes key=id_attribute item=group_attribute name=posts}
								{if !$PS_DISPLAY_UNAVAILABLE_ATTR && $group.attributes_quantity[$id_attribute] <= 0}
									{* continue next loop *}
								{else}
									<a href="javascript:void(0)" class="deo-attr-item{if $group.attributes_quantity[$id_attribute] <= 0} out-stock{/if}{if isset($product.attributes[$id_attribute_group]['id_attribute']) && $product.attributes[$id_attribute_group]['id_attribute'] == $id_attribute} selected{/if}" title="{$group.name}: {$group_attribute}" data-toggle="deo-tooltip" data-position="top" data-product-attribute="{$id_attribute}" data-total-quantity="{$group.attributes_quantity[$id_attribute]}">
										{$group_attribute}{if $show_value_text && !$smarty.foreach.posts.last},{/if}
									</a>
								{/if}		
							{/foreach}
						{/if}
					</span>
				</div>
			{/if}
		{/foreach}
	</div>
{/if}