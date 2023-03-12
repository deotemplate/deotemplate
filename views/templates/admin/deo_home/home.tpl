{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div id="wrapper-page-builder" class="{if $deo_debug_mode}deo-debug-mode{/if}">
	{if isset($errorText) && $errorText}
		<div class="error alert alert-danger">
			{$errorText|escape:'html':'UTF-8'}
		</div>
	{/if}
	{if isset($errorSubmit) && $errorSubmit}
		<div class="error alert alert-danger">
			{$errorSubmit|escape:'html':'UTF-8'}
		</div>
	{/if}
	<div id="list-widgets">
		{include file='./shortcodeitem.tpl' col='col-md-6' sidebar=true}
	</div>
	<form id="form_data_profile" name="form_data_profile" action="{$ajaxHomeUrl}&id_deotemplate_profiles={$idProfile}" method="post">
		<input id="data_profile" type="hidden" value="" name="data_profile" />
		<input id="data_id_profile" type="hidden" value="" name="data_id_profile" />
		<input id="data_widgets_modules" type="hidden" value="" name="data_widgets_modules" />
		<input id="data_position" type="hidden" value="" name="data_position" />
		<input id="data_elements" type="hidden" value="" name="data_elements" />
		<input id="data_product_lists" type="hidden" value="" name="data_product_lists" />
		<input id="data_megamenu_group_active" type="hidden" value="" name="data_megamenu_group_active" />
		<input id="submitSaveAndStay" type="hidden" value="1" name="submitSaveAndStay" />
		<button class="hidden" type="submit">submit</button>
	</form>
	<div id="top_wrapper" class="clearfix">
		{* <a href="javascript:void(0)" class="btn btn-primary btn-form-toggle" title="{l s='Expand or Colapse' mod='deotemplate'}">
			<i class="icon-resize-small"></i>
		</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-default active" data-width="auto">{l s='Default' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-desktop" data-width="1500">{l s='Desktop' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-small-desktop" data-width="1200">{l s='Small Desktop' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-tablet" data-width="992">{l s='Tablet' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-small-tablet" data-width="768">{l s='Small Tablet' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-mobile" data-width="576">{l s= 'Mobile' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-small-mobile" data-width="480">{l s='Small Mobile' mod='deotemplate'}</a> *}
		<div class="pull-left control control-left">
			<a href="javascript:void(0)" data-toggle="tooltip" id="btn-show-list-widgets" title="{l s='Open/Close list widgets and modules' mod='deotemplate'}" class="btn btn-danger label-tooltip">
				<i class="icon-th"></i> {l s='List widgets' mod='deotemplate'}
			</a>
			<a href="{$url_customize_color}" data-toggle="tooltip" id="cusomize-color" title="{l s='Settings customize color for current home page' mod='deotemplate'}" target="_blank" class="btn btn-info label-tooltip">
				<i class="icon-cog"></i> {l s='Customize Color' mod='deotemplate'}
			</a>
			<a href="{$url_preview}" data-toggle="tooltip" id="preview-homepage" title="{l s='Preview Front End current home page' mod='deotemplate'}" target="_blank" class="btn btn-info label-tooltip">
				<i class="icon-eye-open"></i> {l s='Preview' mod='deotemplate'}
			</a>
			<a href="javascript:void(0)" data-toggle="tooltip" id="save-homepage" title="{l s='Save settings for current home page' mod='deotemplate'}" class="btn btn-success label-tooltip">
				<i class="icon-save"></i> {l s='Save Settings' mod='deotemplate'}
			</a>
			<a href="#position-mobile" data-toggle="tooltip" title="{l s='Scroll to Position Mobile' mod='deotemplate'}" class="label-tooltip nav-scroll">Mobile</a>
			<a href="#position-header" data-toggle="tooltip" title="{l s='Scroll to Position Header' mod='deotemplate'}" class="label-tooltip nav-scroll">Header</a>
			<a href="#position-content" data-toggle="tooltip" title="{l s='Scroll to Position Content' mod='deotemplate'}" class="label-tooltip nav-scroll">Content</a>
			<a href="#position-footer" data-toggle="tooltip" title="{l s='Scroll to Position Footer' mod='deotemplate'}" class="label-tooltip nav-scroll">Footer</a>
			<a href="#position-product" data-toggle="tooltip" title="{l s='Scroll to Position Product' mod='deotemplate'}" class="label-tooltip nav-scroll">Product</a>
		</div>
		{* <div class="control control-center">
		</div> *}
		<div class="pull-right control control-right">
			<div class="dropdown">
				<a href="javascript:void(0)" id="current_profile" class="btn btn-primary" title="{l s='Change Home Page' mod='deotemplate'}" role="button" data-toggle="dropdown" data-target="#" data-id='{$currentProfile.id_deotemplate_profiles|escape:'html':'UTF-8'}'>
					<i class="icon-file-text"></i> {l s='Layout:' mod='deotemplate'} {$currentProfile.name|escape:'html':'UTF-8'}{if $profilesList} <span class="caret"></span>{/if}
				</a>
				{if $profilesList}
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					{foreach from=$profilesList item=profile}
					<li><a class="btn btn-select-profile" href="{$ajaxHomeUrl|escape:'html':'UTF-8'}&id_deotemplate_profiles={$profile.id_deotemplate_profiles|escape:'html':'UTF-8'}">{$profile.name|escape:'html':'UTF-8'}</a></li>
					{/foreach}
				</ul>
				{/if}
			</div>
			
			<a href="javascript:void(0)" class="btn btn-primary btn-form-action btn-import" title="{l s='Import Data' mod='deotemplate'}" data-text="{l s='Import Form' mod='deotemplate'}"><i class="icon-cloud-upload"></i> {l s='Import' mod='deotemplate'}</a>
			<div class="dropdown">
				<a href="javascript:void(0)" class="btn btn-primary export_button" title="{l s='Export Data' mod='deotemplate'}" role="button" data-toggle="dropdown" data-target="#" href="/page.html">
					<i class="icon-cloud-download"></i> {l s='Export' mod='deotemplate'} <span class="caret"></span>
				</a>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dLabel">
					<li><a href="javascript:void(0)" class="btn export-from btn-export" data-type="all"><strong>{l s='Profile' mod='deotemplate'}</strong></a></li>
					{foreach from=$exportItems key=position item=hookData}
					<li><a href="javascript:void(0)" class="btn export-from btn-export" data-type="position" data-position="{$position|lower|escape:'html':'UTF-8'}"><strong>{l s='Position' mod='deotemplate'} {$position|escape:'html':'UTF-8'}</strong></a></li>
						{foreach from=$hookData item=hook}
					<li><a href="javascript:void(0)" class="btn export-from btn-export" data-type="{$hook|escape:'html':'UTF-8'}">-------- Hook {$hook|escape:'html':'UTF-8'}</a></li>
						{/foreach}
					{/foreach}
				</ul>
			</div>
		</div>
	</div>
	<div id="home_wrapper" class="default">
		<div class="position-cover clearfix" id="position-mobile" data-position-name="mobile" data-position-id={$currentPosition.mobile.id}>
		{include file='./position.tpl' position='Mobile' config=$positions.mobile listPositions=$listPositions.mobile default=$currentPosition.mobile}
		</div>
		<div class="position-cover clearfix" id="position-header" data-position-name="header" data-position-id={$currentPosition.header.id}>
		{include file='./position.tpl' position='Header' config=$positions.header listPositions=$listPositions.header default=$currentPosition.header}
		</div>
		<div class="position-cover clearfix" id="position-content" data-position-name="content" data-position-id={$currentPosition.content.id}>
		{include file='./position.tpl' position='Content' config=$positions.content listPositions=$listPositions.content default=$currentPosition.content}
		</div>
		<div class="position-cover clearfix" id="position-footer" data-position-name="footer" data-position-id={$currentPosition.footer.id}>
		{include file='./position.tpl' position='Footer' config=$positions.footer listPositions=$listPositions.footer default=$currentPosition.footer}
		</div>
		<div class="position-cover clearfix" id="position-product" data-position-name="product" data-position-id={$currentPosition.product.id}>
		{include file='./position.tpl' position='Product' config=$positions.product listPositions=$listPositions.product default=$currentPosition.product}
		</div>
		
	</div>
	{* <div id="bottom_wrapper">
		<a href="javascript:void(0)" class="btn btn-primary btn-form-toggle" title="{l s='Expand or Colapse' mod='deotemplate'}">
			<i class="icon-resize-small"></i>
		</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-default active" data-width="auto">{l s='Default' mod='deotemplate'}</a>
		<a href="javascript:void(0)" href="javascript:void(0)" class="btn btn-primary btn-fwidth width-desktop" data-width="1500">{l s='Desktop' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-small-desktop" data-width="1200">{l s='Small Desktop' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-tablet" data-width="992">{l s='Tablet' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-small-tablet" data-width="768">{l s='Small Tablet' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-mobile" data-width="576">{l s= 'Mobile' mod='deotemplate'}</a>
		<a href="javascript:void(0)" class="btn btn-primary btn-fwidth width-small-mobile" data-width="480">{l s='Small Mobile' mod='deotemplate'}</a>
		
		<div class="pull-right control-right">
			<div class="dropdown">
				<a href="javascript:void(0)" class="btn btn-primary" role="button" data-toggle="dropdown" data-target="#" data-id='{$currentProfile.id_deotemplate_profiles|escape:'html':'UTF-8'}'>
				  <i class="icon-file-text"></i> {l s='Current Profile:' mod='deotemplate'} {$currentProfile.name|escape:'html':'UTF-8'}{if $profilesList}<span class="caret"></span>{/if}
				</a>
				{if $profilesList}
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					{foreach from=$profilesList item=profile}
					<li><a href="javascript:void(0)" class="btn btn-select-profile" href="{$ajaxHomeUrl|escape:'html':'UTF-8'}&id_deotemplate_profiles={$profile.id_deotemplate_profiles|escape:'html':'UTF-8'}">{$profile.name|escape:'html':'UTF-8'}</a></li>
					{/foreach}
				</ul>
				{/if}
			</div>
			
			<a href="javascript:void(0)" class="btn btn-primary btn-form-action btn-import" data-text="{l s='Import Form' mod='deotemplate'}"><i class="icon-cloud-upload"></i> {l s='Import' mod='deotemplate'}</a>
			<div class="dropdown dropup">
				<a href="javascript:void(0)" class="btn btn-primary export_button" role="button" data-toggle="dropdown" data-target="#">
				  <i class="icon-cloud-download"></i> {l s='Export Data' mod='deotemplate'} <span class="caret"></span>
				</a>

				<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dLabel">
					<li><a href="javascript:void(0)" class="btn export-from btn-export" data-type="all"><strong>{l s='Profile' mod='deotemplate'}</strong></a></li>
					{foreach from=$exportItems key=position item=hookData}
					<li><a href="javascript:void(0)" class="btn export-from btn-export" data-type="position" data-position="{$position|lower|escape:'html':'UTF-8'}"><strong>{l s='Position' mod='deotemplate'} {$position|escape:'html':'UTF-8'}</strong></a></li>
						{foreach from=$hookData item=hook}
					<li><a href="javascript:void(0)" class="btn export-from btn-export" data-type="{$hook|escape:'html':'UTF-8'}">-------- Hook {$hook|escape:'html':'UTF-8'}</a></li>
						{/foreach}
					{/foreach}
				</ul>
			</div>
		</div>
	</div> *}
	<div id="deo_loading" class="deo-loading" style="display: none;">
		<div class="spinner">
			<div class="item-1"></div>
			<div class="item-2"></div>
			<div class="item-3"></div>
		</div>
	</div>
	{include file="./home_form.tpl"}
	<script type="text/javascript">
		{addJsDef imgModuleLink=$imgModuleLink}
		{addJsDef deoAjaxShortCodeUrl=$ajaxShortCodeUrl}
		{addJsDef deoAjaxHomeUrl=$ajaxHomeUrl}
		{addJsDef deoImgController=$imgController}

		var checkSaveMultithreading={$checkSaveMultithreading};	
		var checkSaveSubmit={$checkSaveSubmit};	
		$(document).ready(function(){
			var $deoHomeBuilder = $(document).deotemplate();
			$deoHomeBuilder.ajaxShortCodeUrl = deoAjaxShortCodeUrl;
			$deoHomeBuilder.ajaxHomeUrl = deoAjaxHomeUrl;
			$deoHomeBuilder.lang_id = '{$lang_id|escape:'html':'UTF-8'}';
			$deoHomeBuilder.imgController = deoImgController;
			$deoHomeBuilder.profileId = '{$idProfile|escape:'html':'UTF-8'}';
			$deoHomeBuilder.process();
		});
	</script>
</div>