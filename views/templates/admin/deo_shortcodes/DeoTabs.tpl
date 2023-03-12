{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if !isset($isSubTab)}
	<div {if !isset($deoInfo)}id="default_DeoTabs"{/if} class="{if !isset($deoInfo)}new-shortcode {/if}widget-row clearfix DeoTabs{if isset($formAtts)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}{if isset($formAtts.class)} {$formAtts.class|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}{if isset($deoInfo.icon_class)} widget-icon{/if}" data-type="DeoTabs" data-class="{if isset($formAtts.class)}{$formAtts.class|escape:'html':'UTF-8'}{/if}" {if isset($kshort) && isset($deoInfo.config)}data-form="{$deoInfo.config|json_encode|escape:'html':'UTF-8'}"{/if}>
		<div class="float-center-control text-center">
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort group' mod='deotemplate'}" class="tab-action waction-drag label-tooltip"><i class="icon-move"></i> </a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Disable or Enable Tab' mod='deotemplate'}" class="tab-action btn-status label-tooltip{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}"><i class="{if isset($formAtts.active) && !$formAtts.active}icon-remove{else}icon-ok{/if}"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Setting Tabs' mod='deotemplate'}" class="tab-action btn-edit label-tooltip" data-type="DeoTabs"><i class="icon-cog"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Tabs' mod='deotemplate'}" class="tab-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Tabs' mod='deotemplate'}" class="tab-action btn-delete label-tooltip"><i class="icon-trash"></i></a>
		</div>
		<div class="widget-heading">
			<img class="w-img" src="{if isset($deoInfo.image) && $deoInfo.image}{$moduleDir|escape:'html':'UTF-8'}/deotemplate/views/img/icons/{$deoInfo.image}{else}{$moduleDir|escape:'html':'UTF-8'}deotemplate/logo.png{/if}" title="{l s='Widget' mod='deotemplate'}" alt="{l s='Widget' mod='deotemplate'}"/>
			<i class="icon w-icon{if isset($deoInfo) && isset($deoInfo.icon_class)} {$deoInfo.icon_class|escape:'html':'UTF-8'}{/if}"></i>
			<span class="widget-title">
				<span class="title-name">{if isset($formAtts.title) && $formAtts.title}{$formAtts.title|rtrim|escape:'html':'UTF-8'}{/if}</span>
				<span class="widget-name">{l s='Tabs' mod='deotemplate'}</span>
				<span class="widget-desc">{if isset($deoInfo.desc)}{$deoInfo.desc|truncate:120|escape:'html':'UTF-8'}{/if}</span>
			</span>
		</div>
		{if !isset($deoInfo) || isset($kshort)}
			{assign var=arr_id value=[]}
			{assign var=arr_id_form value=[]}
			{for $foo=1 to 3}
				{$arr_id[$foo] = DeoSetting::getRandomNumber()}
				{$id_form[$foo] = DeoSetting::getRandomNumber()}
			{/for}
   
			<ul class="widget-container-heading nav nav-tabs admin-tabs" role="tablist">
				{if isset($deoInfo)}
					{for $foo=1 to 2}
						<li class="{if $foo==1}active{/if}">
							<a href="#{$deoInfo.config_sub[$foo]['id']|escape:'html':'UTF-8'}" class="{$deoInfo.config_sub[$foo]['form_id']|escape:'html':'UTF-8'}" role="tab" data-toggle="tab" data-form="{$deoInfo.config_sub[$foo]|json_encode|escape:'html':'UTF-8'}">
							   {l s='Tab' mod='deotemplate'} {$foo|escape:'html':'UTF-8'}
							</a>
						</li>
					{/for}
				{else}
					{for $foo=1 to 3}
						<li class="{if $foo==1}active{/if}">
							<a href="#tab_{$arr_id[$foo]|escape:'html':'UTF-8'}" class="form_{$id_form[$foo]|escape:'html':'UTF-8'}" role="tab" data-toggle="tab">
								{l s='Tab' mod='deotemplate'} {$foo|escape:'html':'UTF-8'}
							</a>
						</li>
					{/for}
				{/if}
				<li class="tab-button"><a href="javascript:void(0)" class="btn-add-tab">
					<i class="icon-plus"></i> {l s='Add' mod='deotemplate'}</a>
				</li>
			</ul>
			
			<div class="tab-content widget-container-content">
				{if isset($deoInfo)}
					{for $foo=1 to 2}
						<div id="{$deoInfo.config_sub[$foo]['id']|escape:'html':'UTF-8'}" role="tabpanel" class="tab-pane{if $foo==1} active{/if} widget-wrapper-content wrapper-subwidget-content">
							<div class="text-center tab-content-control">
								<span>{l s='Tab' mod='deotemplate'}</span>
								<a href="javascript:void(0)" class="tabcontent-action btn-new-widget label-tooltip" title="{l s='Add widget' mod='deotemplate'}"><i class="icon-plus-sign"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Tab' mod='deotemplate'}" class="tabcontent-action btn-edit label-tooltip" data-type="DeoSubTabs"><i class="icon-edit"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Tab' mod='deotemplate'}" class="tabcontent-action btn-delete label-tooltip tab"><i class="icon-trash"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Tab' mod='deotemplate'}" class="tabcontent-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
							</div>
							<div class="subwidget-content">
								
							</div>
						</div>
					{/for}
				{else}
					{for $foo=1 to 3}
						<div id="tab_{$arr_id[$foo]|escape:'html':'UTF-8'}" role="tabpanel" class="tab-pane{if $foo==1} active{/if} widget-wrapper-content wrapper-subwidget-content">
							<div class="text-center tab-content-control">
								<span>{l s='Tab' mod='deotemplate'}</span>
								<a href="javascript:void(0)" class="tabcontent-action btn-new-widget label-tooltip" title="{l s='Add widget' mod='deotemplate'}"><i class="icon-plus-sign"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Tab' mod='deotemplate'}" class="tabcontent-action btn-edit label-tooltip" data-type="DeoSubTabs"><i class="icon-edit"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Tab' mod='deotemplate'}" class="tabcontent-action btn-delete label-tooltip tab"><i class="icon-trash"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Tab' mod='deotemplate'}" class="tabcontent-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
							</div>
							<div class="subwidget-content">
								
							</div>
						</div>
					{/for}
				{/if}
			</div>
			<div class="text-center tab-content-control">
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Setting Tabs' mod='deotemplate'}" class="tab-action btn-edit label-tooltip" data-type="DeoTabs"><i class="icon-cog"></i> {l s='Setting Tabs' mod='deotemplate'}</a>
			</div>
		{else}
			<ul class="widget-container-heading nav nav-tabs admin-tabs" role="tablist">
				{foreach from=$subTabContent key=key item=subTab}
					<li class="">
						<a href="#{$subTab.id|escape:'html':'UTF-8'}" class="{$subTab.form_id|escape:'html':'UTF-8'}" role="tab" data-toggle="tab">
							<span>{$subTab.title|escape:'html':'UTF-8'}</span>
						</a>
					</li>
				{/foreach}
				<li class="tab-button"><a href="javascript:void(0)" class="btn-add-tab"><i class="icon-plus"></i> {l s='Add' mod='deotemplate'}</a></li>
			</ul>

			<div class="tab-content">
				{$deo_html_content}{* HTML form , no escape necessary *}
			</div>
			<div class="text-center tab-content-control">
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Setting Tabs' mod='deotemplate'}" class="tab-action btn-edit label-tooltip" data-type="DeoTabs"><i class="icon-cog"></i> {l s='Setting Tabs' mod='deotemplate'}</a>
			</div>
		{/if}
	</div>
{else}
	<div id="{$tabID|escape:'html':'UTF-8'}" class="tab-pane widget-wrapper-content wrapper-subwidget-content">
		<div class="text-center tab-content-control">
			<span>{l s='Tab' mod='deotemplate'}</span>
			<a href="javascript:void(0)" class="tabcontent-action btn-new-widget label-tooltip" title="{l s='Add widget' mod='deotemplate'}"><i class="icon-plus-sign"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Tab' mod='deotemplate'}" class="tabcontent-action btn-edit label-tooltip" data-type="DeoSubTabs"><i class="icon-edit"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Tab' mod='deotemplate'}" class="tabcontent-action btn-delete label-tooltip tab"><i class="icon-trash"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Tab' mod='deotemplate'}" class="tabcontent-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
		</div>
		<div class="subwidget-content">
			{$deo_html_content}{* HTML form , no escape necessary *}
		</div>
	</div>
{/if}
