{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="btn-compare-product-list">
	<a class="deo-compare-button btn-product btn{if $added} added{/if}" href="javascript:void(0)" data-id-product="{$id_product}" title="{if $added}{l s='Remove from Compare' mod='deotemplate'}{else}{l s='Add to Compare' mod='deotemplate'}{/if}" data-toggle="deo-tooltip" data-position="top">
		<span class="content-btn-product">
			<i class="loading-btn-product"></i>
			<i class="icon-btn-product icon-compare"></i>
			<span class="name-btn-product">{l s='Compare' mod='deotemplate'}</span>
		</span>
	</a>
</div>