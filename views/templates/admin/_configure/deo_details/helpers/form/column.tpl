{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div {if isset($defaultColumn)}id="default_column" class="column-row plist-element col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 active hide"{else}class="column-row {if isset($column.form.class)}{$column.form.class}{/if} {foreach $column.form key=ckey item=citem}{if $ckey != 'class' && $ckey != 'active'} col-{$ckey}-{$citem}{/if}{/foreach} plist-element {if isset($defaultColumn)}active{else}{($column.form.active && isset($column.form.active)) ? 'active' : 'deactive'}{/if}"{/if} data-element='column' data-form="{if isset($defaultColumn)}{$dataDefaultColumn|escape:'html':'UTF-8'}{else}{$column.dataForm|escape:'html':'UTF-8'}{/if}">
	<div class="cover-column">
		<div class="column-controll-top clearfix">
			<div class="column-controll-top-left pull-left">
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort column' mod='deotemplate'}" class="column-action caction-drag label-tooltip"><i class="icon-move"></i></a> Column
			</div>
			<div class="column-controll-top-right pull-right">
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Column' mod='deotemplate'}" class="column-action btn-edit-column label-tooltip" data-type="DeoColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Column' mod='deotemplate'}" class="column-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Disable or Enable Column' mod='deotemplate'}" class="column-action btn-status label-tooltip"><i class="{if isset($defaultColumn)}icon-ok{else}{($column.form.active && isset($column.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i></a>
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Column' mod='deotemplate'}" class="column-action plist-eremove label-tooltip" href="javascript:void(0)"><i class="icon-trash"></i> </a>
			</div>
			
			{* <div class="btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span>{l s='Column' mod='deotemplate'}</span> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu for-column-row">
					<li><a href="javascript:void(0)" title="{l s='Edit Column' mod='deotemplate'}" class="column-action btn-edit-column" data-type="DeoColumn" data-for=".column-row"><i class="icon-pencil"></i> {l s='Edit Column' mod='deotemplate'}</a></li>
					<li><a class="plist-eremove" href="javascript:void(0)"><i class="icon-trash"></i> {l s='Delete Column' mod='deotemplate'}</a></li>
					<li><a href="javascript:void(0)" title="{l s='Duplicate Column' mod='deotemplate'}" class="btn-duplicate"><i class="icon-paste"></i> {l s='Duplicate Column' mod='deotemplate'}</a></li>
					<li><a href="javascript:void(0)" title="{l s='Disable or Enable Column' mod='deotemplate'}" class="btn-status"><i class="{if isset($defaultColumn)}icon-ok{else}{($column.form.active && isset($column.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i> {l s='Disable or Enable Column' mod='deotemplate'}</a></li>
				</ul>
			</div> *}
		</div>
		<div class="column-content sort-content clearfix">
			{if !isset($defaultColumn)}
				{foreach $column.sub item=columnsub}
					{include file='./element.tpl' eItem=$columnsub}
				{/foreach}
			{/if}
		</div>
		<div class="column-controll-bottom">
			<span class="change-size-column">
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Reduce size' mod='deotemplate'}" class="column-action btn-change-colwidth" data-value="-1"><i class="icon-minus-sign-alt"></i></a>
				<div class="btn-group">
					<button type="button" class="btn" tabindex="-1" data-toggle="dropdown">
						<span class="width-val deo-w-6"></span>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						{foreach from=DeoSetting::returnWidthList() item=itemWidth}
							<li class="col-{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">
								<a class="change-colwidth" data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" href="javascript:void(0);" tabindex="-1">                                          
									<span data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" class="width-val deo-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</span>
								</a>
							</li>
						{/foreach}
					</ul>
				</div>
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Increase size' mod='deotemplate'}" class="column-action btn-change-colwidth" data-value="1"><i class="icon-plus-sign-alt"></i></a>
			</span>
		</div>
	</div>
</div>
