{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if !isset($isSubTab)}
	{* {if isset($deoInfo)}
		{print_r($deoInfo)}
	{/if} *}
	<div id="{if !isset($deoInfo)}default_DeoAccordions{elseif isset($formAtts)}{$formAtts.form_id|escape:'html':'UTF-8'}{/if}" class="{if !isset($deoInfo)}new-shortcode {/if}widget-row clearfix DeoAccordions{if isset($formAtts)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}{if isset($formAtts.class)} {$formAtts.class|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}{if isset($deoInfo.icon_class)} widget-icon{/if}" data-type="DeoAccordions" data-class="{if isset($formAtts.class)}{$formAtts.class|escape:'html':'UTF-8'}{/if}" {if isset($kshort) && isset($deoInfo.config)}data-form="{$deoInfo.config|json_encode|escape:'html':'UTF-8'}"{/if}>
		<div class="float-center-control text-center">
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort accordion' mod='deotemplate'}" class="accordions-action waction-drag label-tooltip"><i class="icon-move"></i> </a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Disable or Enable Accordions' mod='deotemplate'}" class="accordions-action btn-status label-tooltip{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}"><i class="{if isset($formAtts.active) && !$formAtts.active}icon-remove{else}icon-ok{/if}"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Setting Accordion' mod='deotemplate'}" class="accordions-action btn-edit label-tooltip" data-type="DeoAccordions"><i class="icon-cog"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Accordions' mod='deotemplate'}" class="accordions-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Accordions' mod='deotemplate'}" class="accordions-action btn-delete label-tooltip"><i class="icon-trash"></i></a>
		</div>
		<div class="widget-heading">
			<img class="w-img" src="{if isset($deoInfo.image) && $deoInfo.image}{$moduleDir|escape:'html':'UTF-8'}/deotemplate/views/img/icons/{$deoInfo.image}{else}{$moduleDir|escape:'html':'UTF-8'}deotemplate/logo.png{/if}" title="{l s='Widget' mod='deotemplate'}" alt="{l s='Widget' mod='deotemplate'}"/>
			<i class="icon w-icon{if isset($deoInfo) && isset($deoInfo.icon_class)} {$deoInfo.icon_class|escape:'html':'UTF-8'}{/if}"></i>
			<span class="widget-title">
				<span class="title-name">{if isset($formAtts.title) && $formAtts.title}{$formAtts.title|rtrim|escape:'html':'UTF-8'}{/if}</span>
				<span class="widget-name">{l s='Accordions' mod='deotemplate'}</span>
				<span class="widget-desc">{if isset($deoInfo.desc)}{$deoInfo.desc|truncate:120|escape:'html':'UTF-8'}{/if}</span>
			</span>
		</div>
		<div class="panel-group" id="{if isset($formAtts.id)}{$formAtts.id|escape:'html':'UTF-8'}{elseif isset($deoInfo.config.id)}{$deoInfo.config.id|escape:'html':'UTF-8'}{else}accordion{/if}">

			{if !isset($formAtts.form_id)}
				{if isset($deoInfo)}
					{foreach from=$deoInfo.config_sub key=key item=item }
						<div class="panel panel-default accordion-panel">
							<div class="panel-heading widget-container-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#{$item['id']|escape:'html':'UTF-8'}" class="{$item['form_id']|escape:'html':'UTF-8'}" data-form="{$item|json_encode|escape:'html':'UTF-8'}">Accordion {$key|escape:'html':'UTF-8'}</a>
								</h4>
							</div>
							<div class="text-center accordion-content-control">
								<span>{l s='Accordion' mod='deotemplate'}</span>
								<a href="javascript:void(0)" class="tabcontent-action accordion btn-new-widget label-tooltip" title="{l s='Add widget' mod='deotemplate'}"><i class="icon-plus-sign"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-edit label-tooltip" data-type="DeoSubAccordions"><i class="icon-edit"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-delete label-tooltip"><i class="icon-trash"></i></a>
							</div>
							<div id="{$item['id']|escape:'html':'UTF-8'}" class="panel-collapse collapse in widget-container-content">
								<div class="panel-body">
									<div class="subwidget-content wrapper-subwidget-content">

									</div>
								</div>
							</div>
							<i class="icon-toogle"></i>
						</div>  
					{/foreach} 	
				{else}
					{for $foo=1 to 2}
						{assign var="id" value=DeoSetting::getRandomNumber()}
						{assign var="id_form" value=DeoSetting::getRandomNumber()}
						<div class="panel panel-default accordion-panel">
							<div class="panel-heading widget-container-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse_{$id|escape:'html':'UTF-8'}" class="form_{$id_form|escape:'html':'UTF-8'}">Accordion {$foo|escape:'html':'UTF-8'}</a>
								</h4>
							</div>
							<div class="text-center accordion-content-control">
								<span>{l s='Accordion' mod='deotemplate'}</span>
								<a href="javascript:void(0)" class="tabcontent-action accordion btn-new-widget label-tooltip" title="{l s='Add widget' mod='deotemplate'}"><i class="icon-plus-sign"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-edit label-tooltip" data-type="DeoSubAccordions"><i class="icon-edit"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
								<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-delete label-tooltip"><i class="icon-trash"></i></a>
							</div>
							<div id="collapse_{$id|escape:'html':'UTF-8'}" class="panel-collapse collapse in widget-container-content">
								<div class="panel-body">
									<div class="subwidget-content wrapper-subwidget-content">

									</div>
								</div>
							</div>
							<i class="icon-toogle"></i>
						</div>  
					{/for}
				{/if}
			{else}
				{$deo_html_content}
			{/if}
			<div class="text-center accordion-content-control">
				<a href="javascript:void(0)" class="btn-add-accordion"><i class="icon-plus"></i> {l s='Add Accordion' mod='deotemplate'}</a>
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Setting Accordion' mod='deotemplate'}" class="accordions-action btn-edit label-tooltip " data-type="DeoAccordions"><i class="icon-cog"></i> {l s='Setting Accordions' mod='deotemplate'}</a>
			</div>
		</div>
	</div>
{else}
	<div class="panel panel-default accordion-panel">
		<div class="panel-heading widget-container-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" href="#{$formAtts.id|escape:'html':'UTF-8'}" class="{$formAtts.form_id|escape:'html':'UTF-8'}">{$formAtts.title|escape:'html':'UTF-8'}</a>
			</h4>
		</div>
		<div class="text-center accordion-content-control">
			<span>{l s='Accordion' mod='deotemplate'}</span>
			<a href="javascript:void(0)" class="tabcontent-action accordion btn-new-widget label-tooltip" title="{l s='Add widget' mod='deotemplate'}"><i class="icon-plus-sign"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-edit label-tooltip" data-type="DeoSubAccordions"><i class="icon-edit"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Accordions' mod='deotemplate'}" class="tabcontent-action accordions btn-delete label-tooltip"><i class="icon-trash"></i></a>
		</div>
		<div id="{$formAtts.id|escape:'html':'UTF-8'}" class="panel-collapse collapse in widget-wrapper-content widget-container-content">
			<div class="panel-body">
				<div class="subwidget-content wrapper-subwidget-content">
					{$deo_html_content}
				</div>
			</div>
		</div>
		<i class="icon-toogle"></i>
	</div> 
{/if}