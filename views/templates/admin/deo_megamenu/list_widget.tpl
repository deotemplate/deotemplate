{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<option value=""></option>
{foreach from=$widgets item=w}
    <option value="{$w['key_widget']}">{$w['name']}</option>
{/foreach}
        