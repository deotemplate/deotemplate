{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var=_expand_id value=10|mt_rand:100000}
<div class="deo-widget{if isset($class) && $class} {$class}{/if}{if isset($icon_image) && $icon_image} has-icon-image{/if}" data-id_widget="{$id_widget}">
    <div class="widget-{$name}">
        {if isset($accordion_type) && ($accordion_type == 'accordion' || $accordion_type == 'accordion_small_screen' || $accordion_type == 'accordion_mobile_screen')}
            <div class="block block-toggler w_image{if $accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if}">
                {if isset($widget_heading) && !empty($widget_heading)}
                    <div class="title clearfix">
                        <div class="menu-title">
                        	{block name='widget-title'}
                                {if isset($icon_image) && $icon_image}
                                    {if isset($icon_lazyload) && $icon_lazyload && !$backoffice}
                                        <span class="lazyload-wrapper" style="padding-bottom: {$icon_rate_image};">
                                            <span class="lazyload-icon"></span>
                                        </span>
                                        <img data-src="{$icon_image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload"/>
                                    {else}
                                        <img src="{$icon_image}" class="img-fluid"/>
                                    {/if}    
                                {/if}
                                {if isset($link_title) && !empty($link_title)}
                                    <a href="{$link_title}" class="link-title-menu">
                                {/if}
                                    <span class="name-heading">{$widget_heading}</span>
                                {if isset($link_title) && !empty($link_title)}
                                    </a>
                                {/if}
                            {/block}
		                </div>
                        <span class="navbar-toggler collapse-icons" data-target="#w-menu-{$key_widget}" data-toggle="collapse">
                            <i class="add"></i>
                            <i class="remove"></i>
                        </span>
                    </div>
                {/if}
                <div class="collapse" id="w-menu-{$key_widget}">
                	<div class="widget-inner">
	                	{block name='widget-content'}
		                    
		                {/block}
	                </div>
                </div>
            </div>
        {else}
            {if isset($widget_heading) && !empty($widget_heading)}
                <div class="menu-title">
                    {block name='widget-title'}
                        {if isset($icon_image) && $icon_image}
                            {if isset($icon_lazyload) && $icon_lazyload && !$backoffice}
                                <span class="lazyload-wrapper" style="padding-bottom: {$icon_rate_image};">
                                    <span class="lazyload-icon"></span>
                                </span>
                                <img data-src="{$icon_image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload"/>
                            {else}
                                <img src="{$icon_image}" class="img-fluid"/>
                            {/if}    
                        {/if}
                        {if isset($link_title) && !empty($link_title)}
                            <a href="{$link_title}" class="link-title-menu">
                        {/if}
                            <span class="name-heading">{$widget_heading}</span>
                        {if isset($link_title) && !empty($link_title)}
                            </a>
                        {/if}
                    {/block}
                </div>
            {/if}
            <div class="widget-inner">
                {block name='widget-content'}
                    
                {/block}
            </div>
        {/if}
    </div>
</div>