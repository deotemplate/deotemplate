{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div{if isset($formAtts.id) && $formAtts.id} id="{$formAtts.id|escape:'html':'UTF-8'}"{/if}
	class="{(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} {(isset($formAtts.animation) && $formAtts.animation != 'none'|escape:'html':'UTF-8') ? 'has-animation' : ''}"
	{if isset($formAtts.animation) && $formAtts.animation != 'none'} data-animation="{$formAtts.animation|escape:'html':'UTF-8'}"
	{if isset($formAtts.animation_delay) && $formAtts.animation_delay != ''} data-animation-delay="{$formAtts.animation_delay|escape:'html':'UTF-8'}" {/if}
	{if isset($formAtts.animation_duration) && $formAtts.animation_duration != ''} data-animation-duration="{$formAtts.animation_duration|escape:'html':'UTF-8'}" {/if}
	{if isset($formAtts.animation_iteration_count) && $formAtts.animation_iteration_count != ''} data-animation-iteration-count="{$formAtts.animation_iteration_count|escape:'html':'UTF-8'}" {/if}
	{if isset($formAtts.animation_infinite) && $formAtts.animation_infinite != ''} data-animation-infinite="{$formAtts.animation_infinite|escape:'html':'UTF-8'}" {/if}
	{/if}
	>
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		<div class="box-title box-title-deo-column">
	{/if}
		{if isset($formAtts.title) && $formAtts.title}
			<h4 class="title_block title-deo-column">{$formAtts.title}</h4>
		{/if}
		{if isset($formAtts.sub_title) && $formAtts.sub_title}
			<div class="sub-title-widget sub-title-deo-column">{$formAtts.sub_title nofilter}</div>
		{/if}
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		</div>
	{/if}
	{if isset($formAtts.content_html)}
		{$formAtts.content_html|escape:'html':'UTF-8' nofilter}
	{else}
		{$deo_html_content nofilter}
	{/if}
</div>