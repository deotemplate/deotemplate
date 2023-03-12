{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div{if isset($formAtts.id) && $formAtts.id} id="{$formAtts.id|escape:'html':'UTF-8' nofilter}"{/if}
	{if isset($formAtts.class)} class="block {$formAtts.class|escape:'html':'UTF-8'} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}"{/if}>
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		<div class="box-title">
	{/if}
		{if isset($formAtts.title) && $formAtts.title}
			<h4 class="title_block">{$formAtts.title}</h4>
		{/if}
		{if isset($formAtts.sub_title) && $formAtts.sub_title}
			<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
		{/if}
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		</div>
	{/if}
	{if isset($formAtts.content_html)}
		{$formAtts.content_html nofilter}{* HTML form , no escape necessary *}
	{else}
		{$deo_html_content nofilter}{* HTML form , no escape necessary *}
	{/if}
</div>