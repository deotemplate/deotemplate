{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($formAtts.has_error) && $formAtts.has_error}
	<div class="alert alert-warning deo-widget-error">{$formAtts.msg_error}</div>
{else}
	<div id="{if isset($group.tab_style) && $group.tab_style}group-megamenu-tab-{$group.randkey}{else}memgamenu-{$formAtts.form_id}{/if}" class="{(isset($formAtts.class)) ? $formAtts.class : ''}{if isset($group.tab_style) && $group.tab_style} use-tab-style{if $megamenu_group_tab_active == $group.randkey} active{/if}{/if}">
		{if isset($content_megamenu)}
			{$content_megamenu nofilter}{* HTML form , no escape necessary *}
		{/if}
	</div>
{/if}