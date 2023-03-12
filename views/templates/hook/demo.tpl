{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{* {if isset($smarty.cookies.popup_demo_cookie)} 
	{assign var="demo_cookie_popup" value=$smarty.cookies.popup_demo_cookie}
{else}
	{assign var="demo_cookie_popup" value=0}
{/if}
<div id="demo_cookie" style="left: 0px;bottom: 0px;width: 100%;height: auto;background: rgba(0,0,0,0.7);" class="DeoPopup deo-popup simple-popup{if $demo_cookie_popup == 1} hidden-popup{/if}" data-active="true" data-simple_popup="true" data-show_desktop="true" data-show_tablet="true" data-show_mobile="true" data-closebtn="false" data-show_btn_open_popup="false" data-time_wait="3000" data-time_close="false" data-time_life="7" data-hide_popup_when_close="true" data-time_show_again="120000" data-width="100%" data-height="auto" data-wrapcss="popup-paneltool  DeoPopup" data-effect="fadescale" data-padding="0" data-margin="0" data-bg_color_overlay_popup="rgba(0,0,0,0.3)" data-top_simple="auto" data-bottom_simple="0px" data-left_simple="0px" data-right_simple="0px" data-position_popup="false" data-show_homepage="" data-overlay_popup="false" data-bg_data="background-color:rgba(0,0,0,0.8);background-repeat:no-repeat;background-position:center;background-size:cover;" data-lazyload="false" data-bg_img="">
	<div class="wrapper-popup">
		<div class="content-popup">
			<div class="block cookie white-text DeoHtml ">
			   <div class="block_content">
			   		<div>{l s='If you use developer tools to check responsive on this template, please refresh browser when you change device to get really view.' mod='deotemplate'}</div>
			   		<p><a href="javascript:void(0)" class="btn btn-default close-popup">{l s='Close' mod='deotemplate'}</a></p>
			   	</div>
			</div>
		</div>
		<div class="bg-popup">
			<div class="bg-popup-inner"></div>
		</div>
	</div>
</div> *}
