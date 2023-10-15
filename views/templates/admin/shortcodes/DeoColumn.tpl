{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<p><input type="text" name="controller_pages" value="{$controller}" class="em_text"/></p>
<p><select size="25" name="controller_pages_select" class="em_list" multiple="multiple">

<option disabled="disabled">{$_core_}</option>
{foreach from=$controllers key=k item=page}
    {if in_array($page, $arr_controllers)}
        <option value="{$page}" selected="selected">{$page}</option>
    {else}
        <option value="{$page}">{$page}</option>
    {/if}
{/foreach}

{foreach from=$modules_controllers_type key=type item=label}
    <option disabled="disabled">________________________________________ {$label} ________________________________________</option>
    {assign var=all_modules_controllers value=$controllers_modules.$type}
    {foreach $all_modules_controllers key=module item=modules_controllers}
        {foreach $modules_controllers item=cont}
            {assign var=key value="module-`$module`-`$cont`"}
            {if in_array($key, $arr_controllers)}
                <option value="module-{$module}-{$cont}" selected="selected">module_{$module}_{$cont}</option>
            {else}
                <option value="module-{$module}-{$cont}">module_{$module}_{$cont}</option>
            {/if}
        {/foreach}
    {/foreach}
{/foreach}
</select></p>