{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{*{if isset($formAtts.sub_title) && $formAtts.sub_title}
    <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
{/if}*}
{if isset($formAtts.tpl_file) && !empty($formAtts.tpl_file)}
	{include file=$formAtts.tpl_file}
{/if}

{if isset($formAtts.error_file) && !empty($formAtts.error_file)}
	{$formAtts.error_message nofilter}{* HTML form , no escape necessary *}
{/if}
