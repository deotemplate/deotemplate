{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if count($deo_config_data)}
    <div class="group-input group-{$deo_config} clearfix">
    	{if $deo_config == 'profile'}
            <label class="control-label">{l s='Homepage' mod='deotemplate'}</label>
            <div class="control-content">
                {foreach from=$deo_config_data item=data}
                    {if isset($data.friendly_url) && $data.friendly_url != '' && ($deo_controller == 'index' || $deo_controller == 'home')}
                        {assign var="enable_js" value=false}
                        {assign var="url" value="`$deo_current_url``$data.friendly_url`.html"}
                    {* {else if ($deo_controller == 'index' && $deo_config == 'profile')}
                        {print_r($data)}
                        {assign var="enable_js" value=false}
                        {assign var="url" value="`$deo_current_url``$data.friendly_url`.html"}
                        {assign var="data_url" value=$deo_current_url} *}
                    {else}
                        {assign var="enable_js" value=false}
                        {assign var="url" value="`$deo_current_url`?`$deo_type`=`$data.id`"}
                    {/if}
                    {if $deo_config == 'profile'}
                        <a class="deo_config{if $data.active} active{/if}" data-type="{$deo_type}" data-id="{$data.id}" data-enable_js="{$enable_js}" {if isset($data_url)}data-url="{$data_url}"{/if} href="{$url}">{$data.name}</a>
                    {/if}
                {/foreach}
            </div>
        {else}
        	<label class="control-label label-small">
        		{if $deo_config == 'header'}
        			{l s='Header' mod='deotemplate'}
        		{else if $deo_config == 'footer'}
        			{l s='Footer' mod='deotemplate'}
        		{else if $deo_config == 'product'}
        			{l s='Product' mod='deotemplate'}
        		{else if $deo_config == 'content'}
        			{l s='Content' mod='deotemplate'}
        		{else if $deo_config == 'product_list_builder'}
        			{l s='Product List' mod='deotemplate'}
        		{/if}
        	</label>
        	<div class="control-content">
                <div class="deo-dropdown">
                    <div class="dropdown-button">
                        <div class="selected-value">{l s='Choose Value' mod='deotemplate'}</div>
                        <div class="arrows"></div>
                    </div>
                    <div class="dropdown-content">
                        {foreach from=$deo_config_data item=data}
                            <label for="{$deo_config}-{$data.id}" class="option{if $data.active} checked{/if} label-inherit">
                                <input type="radio" class="deo-radio-dropdown change-paneltool" name="{$deo_config}" value="{$data.id}" {if $data.active}checked="checked"{/if} data-name="{$data.name}" id="{$deo_config}-{$data.id}">
                                <span class="opt-val">{$data.name}</span>
                            </label>
                        {/foreach}
                    </div>
                </div>
        	</div>
        {/if}
    </div>
{/if}