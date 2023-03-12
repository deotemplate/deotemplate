{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}
	<div {if isset($formAtts.form_id) && $formAtts.form_id} id="{$formAtts.form_id nofilter}"{/if}
		{if isset($formAtts.class)} class="block {$formAtts.class} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}"{/if}>
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<h4 class="title_block">{$formAtts.title nofilter}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
			<p class="block_content">
		{if isset($formAtts.tags) &&  $formAtts.tags}
			{foreach from=$formAtts.tags item=tag name=myLoop}
				<a href="{$link_deo->getPageLink('search', true, NULL, "tag={$tag.name|urlencode}")}" title="{l s='More about' mod='deotemplate'} {$tag.name}" class="{$tag.class} {if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{else}item{/if}">{$tag.name}</a>
			{/foreach}
		{else}
			{l s='No tags have been specified yet.' mod='deotemplate'}
		{/if}
			</p>
	</div>
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen' || $formAtts.accordion_type == 'accordion_mobile_screen')}
	<div {if isset($formAtts.form_id) && $formAtts.form_id} id="{$formAtts.form_id nofilter}"{/if}
		class="{if isset($formAtts.class)}block block-toggler {$formAtts.class}{/if} {if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $formAtts.accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if}{if isset($formAtts.sub_title) && $formAtts.sub_title} has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<div class="title clearfix">
					<h4 class="title_block">
						{$formAtts.title nofilter}
					</h4>
					<span class="navbar-toggler collapse-icons" data-target="#DeoProductTag_{$formAtts.form_id}" data-toggle="collapse">
						<i class="add"></i>
						<i class="remove"></i>
					</span>
				</div>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}

		<div class="block_content">
			{if isset($formAtts.tags) &&  $formAtts.tags}
				<ul class="collapse" id="DeoProductTag_{$formAtts.form_id}">
				{foreach from=$formAtts.tags item=tag name=myLoop}
					<li><a href="{$link_deo->getPageLink('search', true, NULL, "tag={$tag.name|urlencode}")}" title="{l s='More about' mod='deotemplate'} {$tag.name}" class="{$tag.class} {if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{else}item{/if}">{$tag.name}</a></li>
				{/foreach}
				</ul>
			{else}
				{l s='No tags have been specified yet.' mod='deotemplate'}
			{/if}
		</div>
	</div>
{/if}