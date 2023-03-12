{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if !isset($deoAjax)}
	<div class="block {$formAtts.class|escape:'html':'UTF-8'} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		<input type="hidden" name="data_form" class="data_form" value="{$data_form nofilter}"/>
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && !empty($formAtts.title)}
				<h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
{/if}
{if isset($products) && $products}
	{if !isset($deoAjax)}
		<!-- Products list -->
		<ul {if isset($id) && $id}id="{$id|intval}"{/if} class="product_list grid row{if isset($class) && $class} {$class|escape:'html':'UTF-8'}{/if} {if isset($productClassWidget)}{$productClassWidget|escape:'html':'UTF-8'}{/if}">
	{/if}
		{foreach from=$products item=product name=products}
			<li class="ajax_block_product{if isset($formAtts.use_animation) && $formAtts.use_animation} has-animation{/if} product_block 
				{if $scolumn == 5} col-lg-2-4 {else} col-lg-{12/$scolumn|intval}{/if} col-md-4 col-sm-4 col-xs-6 col-sp-12 {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if}"{if isset($formAtts.use_animation) && $formAtts.use_animation} data-animation="fadeInUp" data-animation-delay="{$smarty.foreach.products.iteration*100}ms" data-animation-duration="2s" data-animation-iteration-count="1"{/if}>
				{if isset($product_item_path)}
					{include file="$product_item_path"}
				{/if}
			</li>
		{/foreach}
	{if !isset($deoAjax)}
		</ul>
		<!-- End Products list -->
		{if isset($formAtts.use_showmore) && $formAtts.use_showmore}
			<div class="box-show-more open">
				<a href="javascript:void(0)" class="btn btn-default btn-show-more" data-use-animation="{if isset($formAtts.use_animation) && $formAtts.use_animation}1{else}0{/if}" data-page="{$p|intval}" data-loading-text="{l s='Loading...' mod='deotemplate'}">
					<span>{l s='Show more' mod='deotemplate'}</span>
				</a>
			</div>
		{/if}
	</div>
	{/if}
{else}
	</div>
{/if}
