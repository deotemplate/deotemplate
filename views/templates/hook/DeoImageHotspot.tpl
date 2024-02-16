{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="image-{$formAtts.form_id}" class="block imagehotspot {(isset($formAtts.class)) ? $formAtts.class : ''} {(isset($formAtts.class) && $formAtts.class) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		<div class="box-title">
	{/if}
		{if isset($formAtts.title) && $formAtts.title}
			<h4 class="title_block">{$formAtts.title}</h4>
		{/if}
		{if isset($formAtts.sub_title) && $formAtts.sub_title}
			<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
		{/if}
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		</div>
	{/if}
	<div class="imagehotspot-container">
		{if isset($formAtts.image) && $formAtts.image}
			<div class="imagehotspot-image">
				{if isset($formAtts.lazyload) && $formAtts.lazyload}
					<span class="lazyload-wrapper" style="padding-bottom: {$formAtts.rate_image};">
						<span class="lazyload-icon"></span>
					</span>
					<img data-src="{$formAtts.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload {(isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation) ? 'has-animation' : ''}"
						{if isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation} data-animation="{$formAtts.animation}" {/if}
						{if $formAtts.animation_delay != '' && $formAtts.animation_delay} data-animation-delay="{$formAtts.animation_delay}" {/if}
						title="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}"
						alt="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}" loading="lazy"/>
				{else}
					<img src="{$formAtts.image}" class="img-fluid {(isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation) ? 'has-animation' : ''}"
						{if isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation} data-animation="{$formAtts.animation}" {/if}
						{if $formAtts.animation_delay != '' && $formAtts.animation_delay} data-animation-delay="{$formAtts.animation_delay}" {/if}
						title="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}"
						alt="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}" loading="lazy"/>
				{/if}
			</div>
			{if isset($formAtts.items) && $formAtts.items}
				{foreach from=$formAtts.items item=item}
					<div id="hotspot_{$formAtts.form_id}_{$item.id}" class="hotspot {$item.location} {$item.trigger} {if isset($item.class) && $item.class}{$item.class}{/if}" data-id="{$item.id}" style="top: {$item.top};left: {$item.left};">
						<a href="javascript:void(0)" class="hotspot-title" {if isset($item.hpcolor) && $item.hpcolor}style="background: {$item.hpcolor}"{/if}></a>
						<div class="overlay-popup"></div>
						<div class="hotspot-content" {if isset($item.width) && $item.width}style="width: {$item.width}"{/if}>
							<span class="arrow" {if isset($item.backcolor) && $item.backcolor}style="background: {$item.backcolor};"{/if}></span>
							<span class="close"></span>
							{if isset($item.type) && $item.type == 'product'}
								<div class="product-hotspot {$item.location} {$item.productClassWidget}" {if isset($item.backcolor) && $item.backcolor}style="background: {$item.backcolor};"{/if}>
									{if isset($item.product) && $item.product}
										{if $item.profile == 'default'}
											{include file="catalog/_partials/miniatures/product.tpl" product=$item.product}
										{else}
											{hook h='displayDeoProfileProduct' product=$item.product profile=$item.profile}
										{/if}
									{else}
										<p class="alert alert-danger">{l s='Product not found.' mod='deotemplate'}</p>
									{/if}
								</div>
							{else}
								<div class="template" {if isset($item.backcolor) && $item.backcolor}style="background: {$item.backcolor};"{/if}>
									{if isset($item.image) && $item.image}
										<div class="image">
											{if isset($item.link) && $item.link}
												<a href="{$item.link}">
											{/if}
												{if isset($formAtts.lazyload) && $formAtts.lazyload}
													<span class="lazyload-wrapper" style="padding-bottom: {$item.rate_image};">
														<span class="lazyload-icon"></span>
													</span>
													<img data-src="{$item.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$item.title}" class="img-fluid lazyload" loading="lazy"/>
												{else}
													<img src="{$item.image}" alt="{$item.title}" class="img-fluid" loading="lazy"/>
												{/if}
											{if isset($item.link) && $item.link}
												</a>
											{/if}
										</div>
									{/if}
									{if isset($item.title) && $item.title || isset($item.description) && $item.description}
										<div class="content">
											{if isset($item.title) && $item.title}
												<h3 class="title">
													{if isset($item.link) && $item.link}
														<a href="{$item.link}">
													{/if}
														{$item.title nofilter}
													{if isset($item.link) && $item.link}
														</a>
													{/if}
												</h3>
											{/if}  
											{if isset($item.description) && $item.description}
												<div class="description">{$item.description nofilter}</div>
											{/if}
										</div>
									{/if}
								</div>
							{/if}
						</div>
					</div>
				{/foreach}
			{/if}
		{/if}
	</div>
	{if isset($formAtts.description) && $formAtts.description}
		<div class='imagehotspot-description'>
			{($formAtts.description) ? $formAtts.description:''}{* HTML form , no escape necessary *}
		</div>
	{/if}
</div>
