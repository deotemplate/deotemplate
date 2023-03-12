{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if (isset($eItem.config.type) && $eItem.config.type == 'code') || (isset($defaultItem) && $eItem.file == 'code')}
    {include file='./code.tpl' eItem=$eItem}
{elseif (isset($eItem.config.type) && $eItem.config.type == 'box') || (isset($defaultItem) && $eItem.file == 'box')}
    {include file='./box.tpl' eItem=$eItem}
{else}
    <div class="widget-row {(isset($defaultItem)) ? $eItem.file : $eItem.config.file} plist-element {if isset($defaultItem)}active{else}{($eItem.form.active && isset($eItem.form.active)) ? 'active' : 'deactive'}{/if}" data-element="{(isset($defaultItem)) ? $eItem.file : $eItem.config.file}" data-form="{(!isset($eItem.dataForm)) ? '' : $eItem.dataForm|escape:'html':'UTF-8'}">
        <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me to set layout' mod='deotemplate'}" class="widget-action waction-drag label-tooltip"><i class="icon-move"></i></a>
        <span class="widget-alias">
            {(isset($defaultItem)) ? $eItem.name : $eItem.config.name}
            {if isset($defaultItem) && $eItem.file == "product_cover_thumbnails"}
                <span class="thumb{if isset($eItem['data-form']['type']) && $eItem['data-form']['type'] == 'thumbnail'}{else} hide-thumb{/if}">{if isset($eItem['data-form']['type']) && $eItem['data-form']['type'] == 'thumbnail'}{$input.thumbnail_position[$eItem['data-form']['thumb']]}{/if}</span>
            {else if isset($eItem.config.file) && $eItem.config.file == "product_cover_thumbnails"}
                <span class="thumb{if isset($eItem.form['type']) && $eItem.form['type'] == 'thumbnail'}{else} hide-thumb{/if}">{if isset($eItem.form['type']) && $eItem.form['type'] == 'thumbnail'}{$input.thumbnail_position[$eItem.form['thumb']]}{/if}</span>
            {/if}
            {if isset($defaultItem) && $eItem.file == "product_cover_thumbnails"}
                <span class="size">{$eItem['data-form']['size']}</span>
            {else if isset($eItem.config.file) && $eItem.config.file == "product_cover_thumbnails"}
                <span class="size">{$eItem.form['size']}</span>
            {/if}
            {if isset($defaultItem) && $eItem.file == "product_more_info"}
                <span class="type">{$input.product_more_info[$eItem['data-form']['type']]}</span>
            {else if isset($eItem.config.file) && $eItem.config.file == "product_more_info"}
                <span class="type">{$input.product_more_info[$eItem.form['type']]}</span>
            {/if}
        </span>
        {* {if isset($eItem.config.icon)}<i class="{$eItem.config.icon}"></i>{/if} *}
        <div class="widget-controll-top pull-right">
            {if (isset($defaultItem) && isset($eItem.config)) || isset($eItem.config.config)}
                <a href="javascript:void(0)" data-config="{(isset($defaultItem)) ? $eItem.config : $eItem.config.config}" title="{l s='Configure Element' mod='deotemplate'}" class="element-config label-tooltip"><i class="icon-cog"></i></a>
            {/if}
            <a href="javascript:void(0)" title="{l s='Remove Element' mod='deotemplate'}" class="plist-eremove label-tooltip"><i class="icon-trash"></i></a>
            <a href="javascript:void(0)" title="{l s='Disable or Enable Element' mod='deotemplate'}" class="btn-status label-tooltip"><i class="{if isset($defaultItem)}icon-ok{else}{($eItem.form.active && isset($eItem.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i></a>
        </div>
        {* {if (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true)} *}
            <div class="pull-right">
                <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit code element' mod='deotemplate'}" class="plist-eedit label-tooltip" data-element="{(isset($defaultItem)) ? $eItem.file : $eItem.config.file}"><i class="icon-edit"></i></a>
            </div>
        {* {/if} *}
    </div>
{/if}

