{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if isset($formAtts.simple_popup) && $formAtts.simple_popup}
	{assign var="key_cookie_popup" value="popup_`$formAtts.form_id`"}
	{if isset($smarty.cookies.$key_cookie_popup)} 
		{assign var="cookie_popup" value=$smarty.cookies.$key_cookie_popup}
	{else}
		{assign var="cookie_popup" value=0}
	{/if}
{/if}
<div id="{$formAtts.form_id}" class="deo-popup{if isset($formAtts.simple_popup) && $formAtts.simple_popup} simple-popup{if $cookie_popup == 1} hidden-popup{/if} {(isset($formAtts.class) && $formAtts.class) ? $formAtts.class : ''}{(isset($formAtts.sub_title) && $formAtts.sub_title) ? ' has-sub-title' : ''}{/if}" 
	{if isset($formAtts.simple_popup) && $formAtts.simple_popup}
		style="width: {(isset($formAtts.width) && $formAtts.width !== '') ? $formAtts.width : 500};height: {(isset($formAtts.height) && $formAtts.height !== '') ? $formAtts.height : 500};
			{if isset($formAtts.position_popup_simple) && $formAtts.position_popup_simple == 0}
				top: 50%;left: 50%;
				-webkit-transform: translate(-50%,-50%);
				-moz-transform: translate(-50%,-50%);
				-ms-transform: translate(-50%,-50%);
				 -o-transform: translate(-50%,-50%);
					transform: translate(-50%,-50%);
			{else}
				top: {(isset($formAtts.top_simple) && $formAtts.top_simple !== '') ? $formAtts.top_simple : '50%'};left: {(isset($formAtts.left_simple) && $formAtts.left_simple !== '') ? $formAtts.left_simple : '50%'};bottom: {(isset($formAtts.bottom_simple) && $formAtts.bottom_simple !== '') ? $formAtts.bottom_simple : 'auto'};right: {(isset($formAtts.right_simple) && $formAtts.right_simple !== '') ? $formAtts.right_simple : 'auto'};
			{/if}"
		data-position_popup_simple="{(isset($formAtts.position_popup_simple) && $formAtts.position_popup_simple == 0) ? 'true' : 'false'}" 
		data-left_simple="{(isset($formAtts.left_simple) && $formAtts.left_simple !== '') ? $formAtts.left_simple : '50%'}"
		data-right_simple="{(isset($formAtts.right_simple) && $formAtts.right_simple !== '') ? $formAtts.right_simple : 'auto'}"
		data-top_simple="{(isset($formAtts.top_simple) && $formAtts.top_simple !== '') ? $formAtts.top_simple : '50%'}"
		data-bottom_simple="{(isset($formAtts.bottom_simple) && $formAtts.bottom_simple !== '') ? $formAtts.bottom_simple : 'auto'}"
	{else}
		style="display: none;" 
		data-top="{(isset($formAtts.top) && $formAtts.top !== '') ? $formAtts.top : '0.5'}"
		data-left="{(isset($formAtts.left) && $formAtts.left !== '') ? $formAtts.left : '0.5'}"
		data-position_popup="{(isset($formAtts.position_popup) && $formAtts.position_popup == 0) ? 'true' : 'false'}" 
	{/if}
	data-active="{(isset($formAtts.active) && $formAtts.active) ? 'true' : 'false'}" 
	data-simple_popup="{(isset($formAtts.simple_popup) && $formAtts.simple_popup) ? 'true' : 'false'}"
	data-show_desktop="{(isset($formAtts.show_desktop) && $formAtts.show_desktop) ? 'true' : 'false'}" 
	data-show_tablet="{(isset($formAtts.show_tablet) && $formAtts.show_tablet) ? 'true' : 'false'}" 
	data-show_mobile="{(isset($formAtts.show_mobile) && $formAtts.show_mobile) ? 'true' : 'false'}" 
	data-closeBtn="{(isset($formAtts.show_btn_close) && $formAtts.show_btn_close) ? 'true' : 'false'}" 
	data-show_btn_open_popup="{(isset($formAtts.show_btn_open_popup) && $formAtts.show_btn_open_popup) ? 'true' : 'false'}" 
	data-time_wait="{(isset($formAtts.time_wait) && $formAtts.time_wait !== '') ? $formAtts.time_wait : (isset($formAtts.simple_popup) && $formAtts.simple_popup) ? 0 : 3000}" 
	data-time_close="{(isset($formAtts.time_close) && $formAtts.time_close !== '') ? $formAtts.time_close : 'false'}" 
	data-time_life="{(isset($formAtts.time_life) && $formAtts.time_life !== '') ? $formAtts.time_life : 99999999}" 
	data-hide_popup_when_close="{(isset($formAtts.hide_popup_when_close) && $formAtts.hide_popup_when_close) ? 'true' : 'false'}" 
	data-time_show_again="{(isset($formAtts.time_show_again) && $formAtts.time_show_again) ? $formAtts.time_show_again : 'false'}" 
	data-width="{(isset($formAtts.width) && $formAtts.width !== '') ? $formAtts.width : 500}" 
	data-height="{(isset($formAtts.height) && $formAtts.height !== '') ? $formAtts.height : 500}" 
	data-wrapcss="{(isset($formAtts.class) && $formAtts.class) ? $formAtts.class : ''}{(isset($formAtts.sub_title) && $formAtts.sub_title) ? ' has-sub-title' : ''}" 
	data-effect="{(isset($formAtts.effect) && $formAtts.effect) ? $formAtts.effect : ''}" 
	data-padding="{(isset($formAtts.padding) && $formAtts.padding !== '') ? $formAtts.padding : 15}" 
	data-margin="{(isset($formAtts.margin) && $formAtts.margin !== '') ? $formAtts.margin : 15}" 
	data-bg_color_overlay_popup="{(isset($formAtts.bg_color_overlay_popup) && $formAtts.bg_color_overlay_popup) ? $formAtts.bg_color_overlay_popup : '#000000'}" 
	data-show_homepage="{if $page.page_name != 'index'}{(isset($formAtts.show_homepage) && $formAtts.show_homepage) ? 'true' : 'false'}{/if}" 
	data-overlay_popup="{(isset($formAtts.overlay_popup) && $formAtts.overlay_popup) ? 'true' : 'false'}" 
	data-bg_data="{(isset($formAtts.bg_data) && $formAtts.bg_data) ? $formAtts.bg_data : ''}" 
	data-lazyload="{(isset($formAtts.lazyload) && $formAtts.lazyload) ? 'true' : 'false'}" 
	data-bg_img="{(isset($formAtts.bg_img) && $formAtts.bg_img) ? $formAtts.bg_img : ''}" 
>
	<div class="wrapper-popup">
		<div class="content-popup">
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				<div class="box-title">
			{/if}
				{if isset($formAtts.title) && $formAtts.title}
					<h4 class="title_block">{$formAtts.title nofilter}</h4>
				{/if}
				{if isset($formAtts.sub_title) && $formAtts.sub_title}
					<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
				{/if}
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				</div>
			{/if}
			{$deo_html_content nofilter}{* HTML form , no escape necessary *}
			{if isset($formAtts.show_message_again) && $formAtts.show_message_again}
				<label class="control-checkbox-popup label-inherit">
					<input type="checkbox" name="show_message_again" class="show_message_again">
					<span class="checkbox"><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
					<span class="text-checkbox">{l s='Do not show again' mod='deotemplate'}</span>
				</label>
			{/if}
		</div>
		{if isset($formAtts.simple_popup) && $formAtts.simple_popup}
			<a href="javascript:void(0)" class="deo-close-popup" title="{l s='Close' mod='deotemplate'}"><i class="deo-icon icon-close"></i><span>{l s='Close' mod='deotemplate'}</span></a>
		{/if}
		<div class='bg-popup'>
			<div class="bg-popup-inner"></div>
		</div>
	</div>
</div>
{if isset($formAtts.simple_popup) && $formAtts.simple_popup && isset($formAtts.overlay_popup) && $formAtts.overlay_popup}
	<div id="{$formAtts.form_id}-bg-overlay-popup" class="bg-overlay-popup{if $cookie_popup == 1} hidden-overlay-popup{/if}" style="background: {(isset($formAtts.bg_color_overlay_popup) && $formAtts.bg_color_overlay_popup) ? $formAtts.bg_color_overlay_popup : '#000000'};"></div>
{/if}
{if isset($formAtts.show_btn_open_popup) && $formAtts.show_btn_open_popup}
	<a href="javascript:void(0)" class="btn btn-outline deo-show-popup" data-popup="{$formAtts.form_id}">
		<span class="deo-icon-loading-button"></span>
		<span class="text">{$formAtts.text_btn_popup nofilter}</span>
	</a>
{/if}