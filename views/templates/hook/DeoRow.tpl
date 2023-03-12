{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($formAtts.has_container) && $formAtts.has_container}
	<div class="wrapper{if isset($formAtts.lazyload) && $formAtts.lazyload && isset($formAtts.bg_config) && $formAtts.bg_config == "fullwidth"} lazyload lazyload-bg{/if}{if isset($formAtts.parallax) && $formAtts.parallax} deo-parallax{/if}{if isset($formAtts.bg_config_type) && $formAtts.bg_config == "fullwidth"}{if isset($formAtts.bg_config_type) &&  $formAtts.bg_config_type == 'video_link'} has-bg-video bg-video-link{elseif isset($formAtts.bg_config_type) &&  $formAtts.bg_config_type == 'video_youtube'} has-bg-video bg-video-youtube{/if}{/if}" 
		{if isset($formAtts.bg_config) && $formAtts.bg_config == "fullwidth"}
			style="{if isset($formAtts.bg_data) && $formAtts.bg_data}{$formAtts.bg_data}{/if}"
			{if isset($formAtts.bg_img) && $formAtts.bg_img && isset($formAtts.lazyload) && $formAtts.lazyload}data-bgset="{$formAtts.bg_img nofilter}"{/if}
			{if isset($formAtts.parallax) && $formAtts.parallax}{$formAtts.parallax}{/if}
			{* remove for boxed *}
			{$formAtts.bg_style=""}
			{$formAtts.bg_data=""}
			{$formAtts.parallax=""}
		{/if}
		>
		{if isset($formAtts.lazyload) && $formAtts.lazyload && isset($formAtts.bg_config) && $formAtts.bg_config == "fullwidth"}
			<span class="lazyload-wrapper lazyload-background">
				<span class="lazyload-icon"></span>
			</span>   
			{$formAtts.lazyload=false}
		{/if}
		<div class="{if isset($formAtts.has_container) && $formAtts.has_container}container{/if}{if isset($formAtts.container) && $formAtts.container}{$formAtts.container}{/if}{if isset($formAtts.parallax) && $formAtts.parallax} deo-parallax{/if}">
{/if}
			<div{if isset($formAtts.id) && $formAtts.id} id="{$formAtts.id}"{/if}
				class="{(isset($formAtts.class)) ? $formAtts.class : ''} {(isset($formAtts.animation) && $formAtts.animation != 'none') ? ' has-animation' : ''} {$formAtts.bg_class}{if isset($formAtts.sub_title) && $formAtts.sub_title} has-sub-title{/if}{if isset($formAtts.lazyload) && $formAtts.lazyload} lazyload lazyload-bg{/if}{if (isset($formAtts.bg_config_type) && $formAtts.bg_config == "boxed") || isset($formAtts.has_container) && !$formAtts.has_container}{if isset($formAtts.bg_config_type) && $formAtts.bg_config_type == 'video_link'} has-bg-video bg-video-link{elseif isset($formAtts.bg_config_type) && $formAtts.bg_config_type == 'video_youtube'} has-bg-video bg-video-youtube{/if}{/if}"
				{if isset($formAtts.animation) && $formAtts.animation != 'none'} 
					data-animation="{$formAtts.animation}" 
					{if isset($formAtts.animation_delay) && $formAtts.animation_delay != ''} 
						data-animation-delay="{$formAtts.animation_delay}" 
					{/if}
					{if isset($formAtts.animation_duration) && $formAtts.animation_duration != ''} 
						data-animation-duration="{$formAtts.animation_duration}" 
					{/if}
					{if isset($formAtts.animation_iteration_count) && $formAtts.animation_iteration_count != ''}
						data-animation-iteration-count="{$formAtts.animation_iteration_count}" 
					{/if}
					{if isset($formAtts.animation_infinite) && $formAtts.animation_infinite != ''} 
						data-animation-infinite="{$formAtts.animation_infinite}" 
					{/if}
				{/if}
				{if (isset($formAtts.bg_img) && $formAtts.bg_img) && (isset($formAtts.lazyload) && $formAtts.lazyload)  && (isset($formAtts.has_container) && isset($formAtts.bg_config) && ($formAtts.bg_config == "boxed" && $formAtts.has_container) || ($formAtts.bg_config == "fullwidth" && !$formAtts.has_container))}
					data-bgset="{$formAtts.bg_img nofilter}"
				{/if}
				{if isset($formAtts.parallax) && $formAtts.parallax}{$formAtts.parallax nofilter}{/if}
				style="{if isset($formAtts.css_style) && $formAtts.css_style}{$formAtts.css_style nofilter}{/if} {if isset($formAtts.bg_style) && $formAtts.bg_style}{$formAtts.bg_style nofilter}{/if}"
			>
				{if isset($formAtts.lazyload) && $formAtts.lazyload}
					<span class="lazyload-wrapper lazyload-background">
						<span class="lazyload-icon"></span>
					</span>   
				{/if}
				{if isset($formAtts.bg_config_type) && $formAtts.bg_config_type == 'video_link' && isset($formAtts.bg_video_link) && $formAtts.bg_video_link}
					<div class="bg-video-link">
						<video style="max-width: 100%;max-height:100%;" autoplay muted loop>
							<source src="{$formAtts.bg_video_link}" type="{if $formAtts.bg_video_link|strstr:".mp4"}video/mp4{elseif $formAtts.bg_video_link|strstr:".ogg"}video/ogg{elseif $formAtts.bg_video_link|strstr:".webm"}video/webm{/if}">
						</video>
					</div>
				{elseif isset($formAtts.bg_config_type) && isset($formAtts.bg_video_youtube) && $formAtts.bg_config_type == 'video_youtube' && $formAtts.bg_video_youtube}
					<div class="bg-video-youtube">
						{$formAtts.bg_video_youtube nofilter}{* HTML form , no escape necessary *}
					</div>
				{/if}
				{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
					<div class="box-title box-title-deo-group">
				{/if}
					{if isset($formAtts.title) && $formAtts.title}
						<h3 class="title_block title-deo-group">{$formAtts.title nofilter}</h3>
					{/if}
					{if isset($formAtts.sub_title) && $formAtts.sub_title}
						<div class="sub-title-widget sub-title-deo-group">{$formAtts.sub_title nofilter}</div>
					{/if}
				{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
					</div>
				{/if}
				{if isset($formAtts.content_html)}
					{$formAtts.content_html nofilter}
				{else}
					{$deo_html_content nofilter}
				{/if}
			</div>
{if isset($formAtts.has_container) && $formAtts.has_container}
		</div>
	</div>
{/if}