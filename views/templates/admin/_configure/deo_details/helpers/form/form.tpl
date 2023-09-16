{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{extends file="helpers/form/form.tpl"}
{block name="label"}
	{if $input.type == 'DeoColumnClass' || $input.type == 'DeoRowClass'}
	
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
{block name="field"}
	{if $input.type == 'product_page_builder'}
		{* <div id="top_wrapper">
			<a class="btn btn-default btn-fwidth width-default active" data-width="auto">{l s='Default' mod='deotemplate'}</a>
			<a class="btn btn-default btn-fwidth width-desktop" data-width="1500">{l s='Desktop' mod='deotemplate'}</a>
			<a class="btn btn-default btn-fwidth width-small-desktop" data-width="1200">{l s='Small Desktop' mod='deotemplate'}</a>
			<a class="btn btn-default btn-fwidth width-tablet" data-width="992">{l s='Tablet' mod='deotemplate'}</a>
			<a class="btn btn-default btn-fwidth width-small-tablet" data-width="768">{l s='Small Tablet' mod='deotemplate'}</a>
			<a class="btn btn-default btn-fwidth width-mobile" data-width="576">{l s= 'Mobile' mod='deotemplate'}</a>
			<a class="btn btn-default btn-fwidth width-small-mobile" data-width="480">{l s='Small Mobile' mod='deotemplate'}</a>
		</div> *}
		<input id="main_class" type="hidden" name="main_class" value="{if isset($input.params.class)}{$input.params.class}{/if}" />

		<div class="col-lg-12 {$input.type|escape:'html':'UTF-8'} admin-pagebuilder-detail">
			<div class="row">
				{* start content *}
				<div id="home_wrapper" class="list-builder col-xxl-9-6 col-xl-9 col-lg-12 col-md-12 {if $input.deo_debug_mode}deo-debug-mode{/if}">
					<div class="panel panel-sm layout-container">
						<div class="detail-title panel-heading">{l s='Product Page Layout' mod='deotemplate'}</div>
						<div class="hook-content">
							{foreach $input.params['objectForm'] item=gridElement}
								{if $gridElement.name == 'code'}
									{include file='./code.tpl' code=$gridElement.code}
								{else if $gridElement.name == 'group'}
									{include file='./group.tpl' gridElement=$gridElement}
								{else}
									{include file='./element.tpl' eItem=$eItem}
								{/if}
							{/foreach}
						</div>
						<div class="hook-content-footer text-center">
							<a href="javascript:void(0)" class="btn-new-group" title="{l s='Add new group' mod='deotemplate'}">
								<i class="icon-plus"></i> {l s='Add new group' mod='deotemplate'}
							</a>
						</div>
					</div>
				</div>
				{* end content *}

				{* start element *}
				<div class="element-list col-xxl-2-4 col-xl-3 col-lg-12 col-md-12">
					<div class="row">
						{foreach from=$input.elements item=eItems}
							<div class="col-xxl-12 col-xl-12 col-lg-4 col-md-6 col-sm-6 col-xs-12">
								<div class="panel panel-sm clearfix">
									{foreach from=$eItems.group item=eItem}
										{if isset($eItem.type) and $eItem.type=="sperator"}
											<h4 class="title-group panel-heading">
												<i class="{(isset($eItem.icon)) ? $eItem.icon : 'icon-ticket'}"></i> {$eItem.name}
											</h4>
										{else}
											{include file='./element.tpl' eItem=$eItem defaultItem=1}
										{/if}
									{/foreach}
								</div>
							</div>
						{/foreach}
					</div>
				</div>
				{* end element *}
			</div>
		</div>

		<div class="modal detail-builder fade" id="modal_form"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content modal-lg">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"></h4>
						<script type="text/javascript">
							var title_group = "{l s='Configure Group' mod='deotemplate'}";
							var title_column = "{l s='Configure Column' mod='deotemplate'}";
							var title_image = "{l s='Configure Image' mod='deotemplate'}";
						</script>
					</div>
					<div class="modal-body"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
						<button type="button" class="btn btn-primary btn-savewidget">{l s='Save changes' mod='deotemplate'}</button>
					</div>
				</div>
			</div>
		</div>

		<div id="list-temp-element" style="display:none">
			<div id="product-image">
				<div class="form-group">
					<label class="control-label col-lg-5">{l s='Type' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="type" name="type">
							<option value="thumbnail">{l s='Thumbnail' mod='deotemplate'}</option>
							<option value="gallery">{l s='Gallery' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-5">{l s='Lazyload' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="lazyload" name="lazyload">
							<option value="1">{l s='Yes' mod='deotemplate'}</option>
							<option value="0">{l s='No' mod='deotemplate'}</option>                       
						</select>
					</div>
				</div>

				{* with thumb *}
				<div class="form-group config-image with-thumb">
					<label class="control-label col-lg-5">{l s='Thumb Position' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="thumb-position" name="thumb">
							{foreach $input.thumbnail_position  key=key item=item}
								<option value="{$key}"{($item == "bottom") ? " selected" : ""}>{$item}</option>
							{/foreach}
						</select>
					</div>
				</div>

				<div class="form-group config-image with-thumb thumb-position-none">
					<label class="control-label col-lg-5">{l s='Responsive' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="responsive" name="responsive">
							<option value="1">{l s='Yes' mod='deotemplate'}</option>
							<option value="0">{l s='No' mod='deotemplate'}</option>                       
						</select>
					</div>
				</div>

				<div class="form-group config-image with-thumb responsive-thumb">
					<label class="control-label col-lg-5">{l s='Responsive for other screen' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<input type="text" name="breakpoints" class="breakpoints" value="[[1200, 5],[992, 4],[768, 3], [576, 2]]"> 
						<p class="help-block">{l s='(Advance User) Example: [[1200, 5],[992, 4],[768, 3], [576, 2]]. The format is [x,y] whereby x=browser width and y=number of slides displayed' mod='deotemplate'}</p>
					</div>
				</div>

				<div class="form-group config-image with-thumb">
					<label class="control-label col-lg-5">{l s='Display Modal Popup' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select-modal" name="modal">
							<option value="1">{l s='Yes' mod='deotemplate'}</option>
							<option value="0">{l s='No' mod='deotemplate'}</option>                       
						</select>
					</div>
				</div>

				{* show all *}
				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Large Desktop' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_xxl">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Desktop' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_xl">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Small Desktop' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_lg">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Tablet' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_md">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Small Tablet' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_sm">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Mobile' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_xs">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Small Mobile' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_sp">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				<div class="form-group config-image show-all">
					<label class="control-label col-lg-5">{l s='Column Image On Small Desktop' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="select_column" name="column_lg">
							<option value="1">{l s='1 column' mod='deotemplate'}</option>
							<option value="2">{l s='2 column' mod='deotemplate'}</option>
							<option value="3">{l s='3 column' mod='deotemplate'}</option>
							<option value="4">{l s='4 column' mod='deotemplate'}</option>
							<option value="5">{l s='5 column' mod='deotemplate'}</option>
						</select>
					</div>
				</div>

				{* general *}
				<div class="form-group image-size">
					<label class="control-label col-lg-5">{l s='Image Size' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="size" name="size">
							{foreach $input.imageType item=value}
								<option value="{$value.name}"{($value.name == "home_default") ? " selected" : ""}>{$value.name} ({$value.width}px x {$value.height}px)</option>
							{/foreach}
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-lg-5">{l s='Zoom Type' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="zoom" name="zoom">
							<option value="none">{l s='None' mod='deotemplate'}</option>
							<option value="in">{l s='Zoom In' mod='deotemplate'}</option>                             
							<option value="out">{l s='Zoom Out' mod='deotemplate'}</option>
							<option value="out_scrooll">{l s='Zoom Out and Mouse Whell To Zoom' mod='deotemplate'}</option>                 
						</select>
						<p class="help-block">{l s='Warning: \'Scroll To Zoom\' doesn\'t work on Internet Explorer 10 or lower.' mod='deotemplate'}</p>
					</div>
				</div>
				
				<div class="form-group select-zoom-none">
					<label class="control-label col-lg-5">{l s='Zoom Position' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="position" name="position">
							<option value="right">{l s='Right' mod='deotemplate'}</option>
							<option value="left">{l s='Left' mod='deotemplate'}</option> 
							<option value="top">{l s='Top' mod='deotemplate'}</option>
							<option value="bottom">{l s='Bottom' mod='deotemplate'}</option> 								
						</select>
					</div>
				</div>
				
				<div class="form-group select-zoom-none">
					<label class="control-label col-lg-5">{l s='Zoom Window Width' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<input type="text" name="zoomWidth" value="400"> 
					</div>
				</div>
				
				<div class="form-group select-zoom-none">
					<label class="control-label col-lg-5">{l s='Zoom Window Height' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<input type="text" name="zoomHeight" value="400">					
					</div>
				</div>
			</div>

			<div id="product-more-info">
				<div class="form-group">
					<label class="control-label col-lg-5">{l s='Type' mod='deotemplate'}</label>
					<div class="col-lg-5">
						<select class="type" name="type">
							{foreach $input.product_more_info  key=key item=item}
								<option value="{$key}"{($item == "show_all") ? " selected" : ""}>{$item}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>

			<div id="group_config">
				<div class="form-group group-container row">
					<label class="control-label col-lg-3">{l s='Container' mod='deotemplate'}</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="container" id="container_on" value="1" checked="checked">
							<label for="container_on">{l s='Yes' mod='deotemplate'}</label>
							<input type="radio" name="container" id="container_off" value="0">
							<label for="container_off">{l s='No' mod='deotemplate'}</label>
							<a class="slide-button btn"></a>
						</span>
						<p class="help-block">{l s='You need set Hook displayHome at layout other page with page name Product is fullwidth at Hompage builder default to use this setting' mod='deotemplate'}<br>{l s='Note: Auto disable when layout of product page is Layout Sidebar (layout-left-column or layout-right-column sidebar) or Layout Both Columns (layout-both-columns)' mod='deotemplate'}</p>
					</div>
				</div>
				<div class="well">
					<div class="row">
						<label class="choose-class col-lg-12"><input type="checkbox" name="row" class="select-class" value="row"> {l s='Use class row' mod='deotemplate'}</label>
						<label class="control-label col-lg-2">{l s='Group Class:' mod='deotemplate'}</label>
						<div class="col-lg-10"><input type="text" class="element_class" value="" name="class"></div>
					</div><br/>
					{include file='./class.tpl'}
				</div>
			</div>

			<div id="column_config">
				<ul class="nav nav-tabs admin-tabs">
					<li class="nav-item active">
						<a href="#tab_general" class="nav-link" role="tab" data-toggle="tab">{l s='General' mod='deotemplate'}</a>
					</li>
					<li class="nav-item">
						<a href="#deo_row_style" class="nav-link" role="tab" data-toggle="tab">{l s='Responsive' mod='deotemplate'}</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_general">
						<div class="well">
							<div class="row">
							   <label class="control-label col-lg-2">{l s='Column Class:' mod='deotemplate'}</label>
								<div class="col-lg-10"><input type="text" class="element_class" value="" name="class"></div>
							</div><br/>
							{include file='./class.tpl'}
						</div>
					</div>
					<div class="tab-pane" id="deo_row_style">
						<div class="panel panel-default">
							<div class="panel-body">
								<p>{l s='Responsive: You can config width for each Devices' mod='deotemplate'}</p>
							</div>
							<table class="table">
								<thead>
									<tr>
										<th>{l s='Devices' mod='deotemplate'}</th>
										<th class="text-right">{l s='Width' mod='deotemplate'}</th>
									</tr>
								</thead>
								<tbody>
									{foreach $input.columnGrids as $gridKey=>$gridValue}
									<tr>
										<td>
											<span class="col-{$gridKey|escape:'html':'UTF-8'}"></span>
											{$gridValue|escape:'html':'UTF-8'}
										</td>
										<td class="text-right">
											<div class="btn-group">
												<button type="button" class="btn btn-default deo-btn-width dropdown-toggle" tabindex="-1" data-toggle="dropdown">
													<span class="width-val deo-w-12">12/12 - ( 100 % )</span><span class="caret"></span>
												</button>
												<ul class="dropdown-menu">
													{foreach from=$input.widthList item=itemWidth}
														<li>
															<a class="width-select width-select-{$gridKey}-{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" href="javascript:void(0);" tabindex="-1">
																<span data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" class="width-val deo-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</span>
															</a>
														</li>
													{/foreach}
												</ul>
												<input type='hidden' class="col-val" name='{$gridKey|escape:'html':'UTF-8'}' value="12"/>
											</div>
										</td>
									</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			{include file='./group.tpl' defaultGroup=1 dataDefaultGroup=$input.dataDefaultGroup}
			{include file='./column.tpl' defaultColumn=1 dataDefaultColumn=$input.dataDefaultColumn}
		</div>
	{/if}
	{$smarty.block.parent}
{/block}