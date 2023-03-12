{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var="path_widget_base" value="`$path_widget_base`widget.tpl"}
{extends file=$path_widget_base}

{block name='widget-content'}
	<p class="alert alert-info">{l s='Products will display normally on Front End' mod='deotemplate'}</p>
{/block}