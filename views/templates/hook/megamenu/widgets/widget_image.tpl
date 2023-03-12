{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var="path_widget_base" value="`$path_widget_base`widget.tpl"}
{extends file=$path_widget_base}

{block name='widget-content'}
    <div class="img-content">
        {if isset($link) && $link}
        <a href="{$link}" class="image" {(isset($is_open) && $is_open) ? 'target="_blank"' : ''}>
        {/if}
            {if isset($image) && $image}
                {if isset($lazyload) && $lazyload && !$backoffice}
                    <span class="lazyload-wrapper" style="padding-bottom: {$rate_image};">
                        <span class="lazyload-icon"></span>
                    </span>
                    <img data-src="{$image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload"
                        title="{((isset($alt) && $alt) ? $alt : '')}"
                        alt="{((isset($alt) && $alt) ? $alt : '')}"/>
                {else}
                    <img src="{$image}" class="img-fluid"
                        title="{((isset($alt) && $alt) ? $alt : '')}"
                        alt="{((isset($alt) && $alt) ? $alt : '')}"/>
                {/if}    
            {/if}
        {if isset($link) && $link}
        </a> 
        {/if}
    </div>
    <div class="html-content">
        {$html nofilter}
    </div>
{/block}