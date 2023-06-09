{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{function name=deo_content}
	<ul>
		{foreach from=$formAtts.links item=item}
			{if $item.title && $item.link}
				<li><a href="{$item.link}" target="{$item.target_type}">{$item.title|escape:'html':'UTF-8'}</a></li>
			{/if}
		{/foreach}
	</ul>
{/function}

{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
	{if isset($formAtts.lib_error) && $formAtts.lib_error}
		<div class="alert alert-warning deo-widget-error">{$formAtts.lib_error}</div>
	{/if}
{else}
	{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
		<div class="block {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				<div class="box-title">
			{/if}
				{if isset($formAtts.title) && !empty($formAtts.title)}
					<h4 class="title_block">
						{$formAtts.title|escape:'html':'UTF-8'}
					</h4>
				{/if}
				{if isset($formAtts.sub_title) && $formAtts.sub_title}
					<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
				{/if}
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				</div>
			{/if}
			{if isset($formAtts.links) && $formAtts.links|@count > 0}
			   {deo_content}
			{/if}
		</div>
	{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen' || $formAtts.accordion_type == 'accordion_mobile_screen')}
		<div class="block block-toggler {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $formAtts.accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				<div class="box-title">
			{/if}
				{if isset($formAtts.title) && !empty($formAtts.title)}
					<div class="title clearfix">
						<h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
						<span class="navbar-toggler collapse-icons" data-target="#footer-link-{$formAtts.form_id|escape:'html':'UTF-8'}" data-toggle="collapse">
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
			{if isset($formAtts.links) && $formAtts.links|@count > 0}
				<div class="collapse" id="footer-link-{$formAtts.form_id|escape:'html':'UTF-8'}">
					{deo_content}
				</div>
			{/if}
		</div>
	{/if}
{/if}