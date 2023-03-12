{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if count($tags)}
	<div class="deo-blog-tags">
		<span>{l s='Tags:' mod='deotemplate'}</span>
		{foreach from=$tags item=tag name=tag}
			 <a href="{$tag.link}" class="tag" title="{$tag.tag}">{$tag.tag}</a>
		{/foreach}
	</div>
{/if}