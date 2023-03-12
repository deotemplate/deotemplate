{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<!-- Block categories module -->
{if $tree}
	<div id="deo_categories_tree_menu" class="block deo-blog-category-tree hidden-sm-down">
		<div class="box-title">
			<p class="title_block h4"><span>{if isset($currentCategory)}{$currentCategory->title|escape:'html':'UTF-8'}{else}{l s='Blog Categories' mod='deotemplate'}{/if}</span></p>
		</div>
		<div class="block_content">
			{$tree nofilter}{* HTML form , no escape necessary *}
		</div>
	</div>
{/if}
<!-- /Block categories module -->
