{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="header-cover">
	{* <strong>{l s='Position' mod='deotemplate'} {$position|escape:'html':'UTF-8'}</strong> *}
	<div class="dropdown">
		<div class="hide box-edit-position">
			<div class="form-group">
				<label>{l s='Position name:' mod='deotemplate'}</label>
				<input class="edit-name" value="" type="text" placeholder="{l s='Enter position name ' mod='deotemplate'}"/>
			</div>
			<button type="button" class="btn btn-primary btn-save">{l s='Save' mod='deotemplate'}</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
		</div>
		
		<a class="btn btn-info" id="dropdown-{$position|lower|escape:'html':'UTF-8'}" role="button" data-toggle="dropdown" data-target="#">
			<i class="icon-columns"></i> 
			<span class="lbl-name">{l s='Position' mod='deotemplate'} {$position|escape:'html':'UTF-8'}:
				{if $default.name}{$default.name|escape:'html':'UTF-8'}{else}{l s=' Blank' mod='deotemplate'}{/if}
			</span>
			{if $listPositions} <span class="caret"></span>{/if}
		</a>
		<ul class="dropdown-menu list-position" role="menu" aria-labelledby="dLabel" 
			data-position="{$position|lower|escape:'html':'UTF-8'}" id="position-{$position|lower|escape:'html':'UTF-8'}"
			data-id="{$default.id|escape:'html':'UTF-8'}" data-blank-error="{l s=' Please choose or create new a position ' mod='deotemplate'}{$position|escape:'html':'UTF-8'}">
			<li>
				<a href="javascript:void(0)" class="add-new-position" data-id="0">
					<span>{l s='New ' mod='deotemplate'}{$position|escape:'html':'UTF-8'}</span>
				</a>
			</li>
			
			{if $listPositions}
				{foreach from=$listPositions item=val}
					{if isset($val.id_deotemplate_positions)}
						<li>
							<a href="javascript:void(0)" class="position-name" data-id="{$val.id_deotemplate_positions|escape:'html':'UTF-8'}">
								<span title="{$val.name|escape:'html':'UTF-8'}">{$val.name|escape:'html':'UTF-8'}</span>
								<i class="icon-edit label-tooltip" data-id="{$val.id_deotemplate_positions|escape:'html':'UTF-8'}" title="{l s='Edit name' mod='deotemplate'}"></i>
								<i class="icon-paste label-tooltip" data-id="{$val.id_deotemplate_positions|escape:'html':'UTF-8'}" title="{l s='Duplicate' mod='deotemplate'}" data-temp="{l s='Duplicate' mod='deotemplate'}"></i>
							</a>
						</li>
					{/if}
				{/foreach}
			{/if}
		</ul>
	</div>
	{if $position == 'Mobile'}
		<span class="desc">{l s='This position only show on mobile, tablet device and enable MOBILE MODE' mod='deotemplate'} <a href="{$url_mobile_mode}">{l s='here' mod='deotemplate'}</a></span>
	{elseif $position == 'Content' || $position == 'content'}
		<ul class="checkbox-sidebar">
			<li class="checkbox">
				<label>
					<input class="show-sidebar left-sidebar" data-value=".displayDeoTopLeftSidebar,.displayDeoBottomLeftSidebar" name="left-sidebar" value="1" type="checkbox" {if $left_sidebar}checked="checked"{/if}> 
					{l s='Show left sidebar' mod='deotemplate'}
				</label>
			</li>
			<li class="checkbox">
				<label>
					<input class="show-sidebar right-sidebar" data-value=".displayDeoTopRightSidebar,.displayDeoBottomRightSidebar" name="right-sidebar" value="1" type="checkbox" {if $right_sidebar}checked="checked"{/if}> 
					{l s='Show right sidebar' mod='deotemplate'}
				</label>
			</li>
		</ul>
	{/if}
</div>
<div class="position-area{if $position == 'Content' || $position == 'content'}{if $left_sidebar} active-left-sidebar{/if}{if $right_sidebar} active-right-sidebar{/if}{/if} row">
	{foreach from=$config key=hookKey item=hookData}
		<div class="hook-wrapper {$hookKey|escape:'html':'UTF-8'} {$hookData.class|escape:'html':'UTF-8'}" data-hook="{$hookKey|escape:'html':'UTF-8'}">
			<div class="hook-background">
				<div class="hook-top">
					<div class="pull-left hook-desc"></div>
					<div class="hook-info text-center">
						<a href="javascript:void(0)" tabindex="0" class="open-group label-tooltip" title="{$hookKey|escape:'html':'UTF-8'}" id="{$hookKey|escape:'html':'UTF-8'}" name="{$hookKey|escape:'html':'UTF-8'}">
							{$hookKey|escape:'html':'UTF-8'} <i class="icon-arrow-down"></i>
						</a>
					</div>
				</div>
				<div class="hook-content">
					{if isset($hookData.content)}
						{$hookData.content}
					{/if}
					<div class="hook-content-footer text-center">
						<a href="javascript:void(0)" tabindex="0" class="btn-new-widget-group" title="{l s='Add New Column In Row' mod='deotemplate'}" data-container="body" data-toggle="popover" data-placement="top" data-trigger="focus">
							<i class="icon-plus"></i> {l s='Add New Column In Row' mod='deotemplate'}
						</a>
						{if in_array($hookKey, array('displayDeoTopLeftSidebar','displayDeoBottomLeftSidebar','displayDeoTopRightSidebar','displayDeoBottomRightSidebar'))}
							<span>{l s='(Only support Widgets. Not support Modules)' mod='deotemplate'}</span>
						{/if}
					</div>
				</div>
			</div>
		</div>
	{/foreach}
</div>
