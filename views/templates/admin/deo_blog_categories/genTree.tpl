{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<ol class="level{$level}{$t}">
    {foreach from=$data item=$menu}
        <li id="list_{$menu.id_deoblog_category}" class="{if $param_id_deoblog_category == $menu.id_deoblog_category}selected{/if}">
        <div>
            {* <input type="checkbox" name="menubox[]" value="{$menu['id_deoblog_category']}" class="quickselect" title="{l s='Select to delete' mod='deotemplate'}"> *}
            {$menu.title} (ID:{$menu.id_deoblog_category})
            {* <span class="quickedit" rel="id_{$menu.id_deoblog_category}"> {l s='Edit' mod='deotemplate'}</span>
            <span class="quickdel" rel="id_{$menu.id_deoblog_category}"> {l s='Delete' mod='deotemplate'}</span> *}
        </div>
        {if $menu.id_deoblog_category != $parent}
            {$model_deoblog_category->genTree($menu.id_deoblog_category, $level + 1)}
        {/if}
        </li>
    {/foreach}
</ol>