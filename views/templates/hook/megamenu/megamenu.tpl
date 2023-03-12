{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if $group_type && $group_type == 'horizontal'}
	<nav class="deo-horizontal-menu navbar navbar-default {($show_cavas == 1) ? 'enable-canvas' : 'disable-canvas'} {($group_class != '') ? $group_class : ''}" role="navigation" data-megamenu-id="{$megamenu_id}" data-type="horizontal" data-show-mobile-menu="{($show_cavas == 1) ? 1 : 0}">
		<div class="navbar-header">
			<button type="button" class="navbar-toggler hidden-lg-up" {if $show_cavas == 0}data-toggle="collapse"{/if} data-target=".megamenu-{$megamenu_id}">
				<span class="icon-bar line-top"></span>
				<span class="icon-bar line-middle"></span>
				<span class="icon-bar line-bottom"></span>
			</button>
			<span class="text-menu-toggler">{l s='Menu' mod='deotemplate'}</span>
		</div>
		<div class="megamenu-content collapse navbar-toggleable-md megamenu-{$megamenu_id}">
			<h4 class="horizontal-menu-title"><i class="deo-custom-icons"></i><span>{$group_title}</span></h4>
			{$megamenu|escape:'html':'UTF-8' nofilter}
		</div>
	</nav>
{else}
	<div class="deo-vertical-menu {($group_class != '') ? $group_class : ''}" data-megamenu-id="{$megamenu_id}" data-type="vertical">
		<h4 class="vertical-menu-button"><i class="deo-custom-icons"></i><span>{$group_title}</span></h4>
		<div class="box-content">
			{$megamenu|escape:'html':'UTF-8' nofilter}
		</div>
	</div>
{/if}
