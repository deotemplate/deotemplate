{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

 <div id="video-{$formAtts.form_id}" class="deo-video {(isset($formAtts.video_type) && $formAtts.video_type == 'youtube') ? 'youtube-video' : 'normal-video'}{(isset($formAtts.fake_content) && $formAtts.fake_content) ? ' show-fake-content' : ''}{(isset($formAtts.popup_video) && $formAtts.popup_video) ? ' popup-video' : ''} {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		<div class="box-title">
	{/if}
		{if isset($formAtts.title) && !empty($formAtts.title)}
			<h4 class="title_block">{$formAtts.title}</h4>
		{/if}
		{if isset($formAtts.sub_title) && $formAtts.sub_title}
			<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
		{/if}
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		</div>
	{/if}
	 <div class="block_content">
		{if isset($formAtts.video_type) && $formAtts.video_type == 'normal'} 
			<div class="content-video">
				{if isset($formAtts.link_video) && $formAtts.link_video}
					<video width="{$formAtts.width}" height="{$formAtts.height}" style="max-width: 100%;max-height:100%;" {if isset($formAtts.controls) && $formAtts.controls}controls{/if} {if isset($formAtts.autoplay) && $formAtts.autoplay}autoplay{/if} {if isset($formAtts.mute) && $formAtts.mute || isset($formAtts.autoplay) && $formAtts.autoplay}muted{/if} {if isset($formAtts.loop) && $formAtts.loop}loop{/if}>
						<source src="{$formAtts.link_video}" type="{if $formAtts.link_video|strstr:".mp4"}video/mp4{elseif $formAtts.link_video|strstr:".ogg"}video/ogg{elseif $formAtts.link_video|strstr:".webm"}video/webm{/if}">
					</video>
				{else}
					<p class="alert alert-warning">{l s='Field video is empty' mod='deotemplate'}</p>
				{/if}
			</div>
		{else}
			{if isset($formAtts.content_html) && $formAtts.content_html}
				<div class="content-video">
					{if isset($formAtts.content_html) && $formAtts.content_html}
						{$formAtts.content_html nofilter}{* HTML form , no escape necessary *}
					{else}
						<p class="alert alert-warning">{l s='Field video is empty' mod='deotemplate'}</p>
					{/if}
				</div>
			{/if}
		{/if}
		{if isset($formAtts.image) && $formAtts.image}
			<div class="image-video">
				{if isset($formAtts.lazyload) && $formAtts.lazyload}
					<span class="lazyload-wrapper" style="padding-bottom: {$formAtts.rate_image};">
						<span class="lazyload-icon"></span>
					</span>
					<img data-src="{$formAtts.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload {(isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation) ? 'has-animation' : ''}"
						{if isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation} data-animation="{$formAtts.animation}" {/if}
						{if $formAtts.animation_delay != '' && $formAtts.animation_delay} data-animation-delay="{$formAtts.animation_delay}" {/if}
						title="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}"
						alt="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}"/>
				{else}
					<img src="{$formAtts.image}" class="img-fluid {(isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation) ? 'has-animation' : ''}"
						{if isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation} data-animation="{$formAtts.animation}" {/if}
						{if $formAtts.animation_delay != '' && $formAtts.animation_delay} data-animation-delay="{$formAtts.animation_delay}" {/if}
						title="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}"
						alt="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}" loading="lazy"/>
				{/if} 
				<span class="video-play"></span> 
			</div>  
		{/if}
		{if isset($formAtts.description) && $formAtts.description}
			<div class="description">
				{($formAtts.description) ? $formAtts.description:'' nofilter}{* HTML form , no escape necessary *}
			</div>
		{/if}
	</div>
</div>