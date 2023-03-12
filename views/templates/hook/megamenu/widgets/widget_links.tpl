{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var="path_widget_base" value="`$path_widget_base`widget.tpl"}
{extends file=$path_widget_base}

{block name='widget-content'}
	{if isset($links)}
		<ul class="nav-links">
			{foreach $links as $key => $ac}  
				<li ><a href="{$ac.link}" >{$ac.text}</a></li>
			{/foreach}
		</ul>
    {/if}
{/block}