{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var="path_widget_base" value="`$path_widget_base`widget.tpl"}
{extends file=$path_widget_base}

{block name='widget-content'}
	{if manufacturers}
		<div class="manu-logo">
			{foreach from=$manufacturers item=manufacturer name=manufacturers}
				<a  href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)}" title="{$manufacturer.name}">
				<img src="{$manufacturer.image}" alt=""></a>
			{/foreach}
		</div>
	{else}
		<p class="alert alert-info">{l s='No image logo at this time.' mod='deotemplate'}</p>
	{/if}
{/block}