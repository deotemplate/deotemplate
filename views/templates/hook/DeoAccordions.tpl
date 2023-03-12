{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($isWrapper) && $isWrapper}
	<div id="{$formAtts.form_id}" class="deo-accordion panel-group {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<h4 class="title_block">{$formAtts.title|rtrim}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		{$deo_html_content nofilter}{* HTML form , no escape necessary *}
	</div>
{else}
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#{$formAtts.parent_id}" aria-controls="{$formAtts.id}" aria-expanded="{if isset($formAtts.active_accordion) && isset($formAtts.active_type) && (($formAtts.active_type == 'set' && $formAtts.active_accordion) || $formAtts.active_type == 'showall')}true{else}false{/if}" class="{if isset($formAtts.active_accordion) && isset($formAtts.active_type) && (($formAtts.active_type == 'set' && $formAtts.active_accordion) || $formAtts.active_type == 'showall')}{else}collapsed{/if}" href="#{$formAtts.id}">{$formAtts.title}</a>
			</h4>
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		</div>
		<div id="{$formAtts.id}" class="panel-collapse collapse{if isset($formAtts.active_accordion) && isset($formAtts.active_type) && (($formAtts.active_type == 'set' && $formAtts.active_accordion) || $formAtts.active_type == 'showall')} in{/if}" role="tabpanel" aria-expanded="{if isset($formAtts.active_accordion) && isset($formAtts.active_type) && (($formAtts.active_type == 'set' && $formAtts.active_accordion) || $formAtts.active_type == 'showall')}true{else}false{/if}">
			<div class="panel-body">
				{$deo_html_content nofilter}{* HTML form , no escape necessary *}
			</div>
		</div>
	</div> 
{/if}