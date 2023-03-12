{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<ol class="level{$level}">
    {foreach from=$data item=$menu}
        <li id="list_{$menu.id_deoblog_category}">
            <input type="checkbox" value="{$menu.randkey}" name="chk_cat[]" id="chk-{$menu.id_deoblog_category}" {if $menu.id_deoblog_category|array_search:$select !== false}checked="checked"{/if}/>
            <label for="chk-{$menu.id_deoblog_category}">{$menu.title} (ID:{$menu.id_deoblog_category})</label>
            {if (int)$menu.id_deoblog_category != (int)$parent}
                {$model_deoblog_category->genTreeForPageBuilder($menu.id_deoblog_category, $level + 1, $select)}
            {/if}
        </li>
    {/foreach}
</ol>
