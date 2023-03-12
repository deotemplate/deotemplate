{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
	{if isset($formAtts.lib_error) && $formAtts.lib_error}
		<div class="alert alert-warning deo-widget-error">{$formAtts.lib_error}</div>
	{/if}
{else}
	{if isset($formAtts.active) && $formAtts.active == 1}
		<div  id="countdown-{$formAtts.form_id}" class="{(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				<div class="box-title">
			{/if}
				{if isset($formAtts.title) && !empty($formAtts.title)}
					<h4 class="title_block">{$formAtts.title nofilter}{* HTML form , no escape necessary *}</h4>
				{/if}
				{if isset($formAtts.sub_title) && $formAtts.sub_title}
					<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
				{/if}
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				</div>
			{/if}
			{if isset($formAtts.description) && !empty($formAtts.description)}
				<div class="description">{$formAtts.description nofilter}{* HTML form , no escape necessary *}</div>
			{/if}

			<div class="deo-countdown pro" data-text-year="{l s='years' mod='deotemplate'}" data-text-week="{l s='weeks' mod='deotemplate'}" data-text-day="{l s='days' mod='deotemplate'}" data-text-hour="{l s='hours' mod='deotemplate'}" data-text-min="{l s='mins' mod='deotemplate'}" data-text-sec="{l s='secs' mod='deotemplate'}" data-text-finish="{l s='Expired' mod='deotemplate'}" data-time-from="{(isset($formAtts.time_from)) ? $formAtts.time_from : ''}" data-time-to="{(isset($formAtts.time_to)) ? $formAtts.time_to : ''}"></div>

			{if isset($formAtts.link) && $formAtts.link}
				<p class="deo-countdown-link">
					{if isset($formAtts.new_tab) && $formAtts.new_tab == 1}
						<a href="{$formAtts.link}" target="_blank">{$formAtts.link_label}</a>
					{/if}	
					{if isset($formAtts.new_tab) && $formAtts.new_tab == 0}
						<a href="{$formAtts.link}">{$formAtts.link_label}</a>
					{/if}			
				</p>
			{/if}
		</div>
	{/if}
{/if}