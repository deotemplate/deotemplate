{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($smarty.cookies.paneltool_demo_cookie)} 
	{assign var="paneltool_demo_cookie" value=$smarty.cookies.paneltool_demo_cookie}
{else}
	{assign var="paneltool_demo_cookie" value=0}
{/if}

{if isset($panelTool) && $panelTool}
	<div id="deo-paneltool" class="hidden-md-down{if $paneltool_demo_cookie == 0} active{/if}" data-cname="{$deo_cookie_theme}">
		<a href="javascript:void(0)" title="{l s='Theme customize' mod='deotemplate'}" class="paneltool-title">{l s='Theme customize' mod='deotemplate'}</a>
		<div class="paneltool-content">
			<ul class="nav nav-tabs " role="tablist">
				<li class="nav-item">
					<a href="#theme-panel" class="nav-link panelbutton theme-panel-tab-title active" role="tab" data-tab="theme-panel" data-toggle="tab" aria-expanded="true">
						<i class="deo-custom-icons"></i>
						<span>{l s='General' mod='deotemplate'}</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="#demo-homepage" class="nav-link panelbutton demo-homepage-tab-title" role="tab" data-tab="demo-homepage" data-toggle="tab">
						<i class="deo-custom-icons"></i>
						<span>{l s='Home pages' mod='deotemplate'}</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="#demo-product-list" class="nav-link panelbutton demo-product-list-tab-title" role="tab" data-tab="demo-product-list" data-toggle="tab">
						<i class="deo-custom-icons"></i>
						<span>{l s='Product list' mod='deotemplate'}</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="#demo-product-page" class="nav-link panelbutton demo-product-page-tab-title" role="tab" data-tab="demo-product-page" data-toggle="tab">
						<i class="deo-custom-icons"></i>
						<span>{l s='Product page' mod='deotemplate'}</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="#demo-checkout-page" class="nav-link panelbutton demo-checkout-page-tab-title" role="tab" data-tab="demo-checkout-page" data-toggle="tab">
						<i class="deo-custom-icons"></i>
						<span>{l s='One page Checkout' mod='deotemplate'}</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="#theme-customize" class="nav-link panelbutton theme-customize-tab-title" role="tab" data-tab="theme-customize" data-toggle="tab">
						<i class="deo-custom-icons"></i>
						<span>{l s='Customize color' mod='deotemplate'}</span>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="paneltool theme-panel tab-pane active in" id="theme-panel">
					<div class="panelcontent block-panelcontent">
						<div class="panelinner">
							{if !Configuration::get('PS_SMARTY_CACHE')}
							<!-- Lazyload -->
							<label class="control-label">{l s='Lazyload' mod='deotemplate'}</label>
							<div class="group-input clearfix">
								<div class="control-content">
									<span class="switch deo-switch">
										<input type="radio" name="deo_lazyload" class="deo_lazyload" id="deo_lazyload_on" value="1" {if $deo_lazyload}checked="checked"{/if}>
										<label for="deo_lazyload_on">{l s='Enable' mod='deotemplate'}</label>
										<input type="radio" name="deo_lazyload" class="deo_lazyload" id="deo_lazyload_off" value="0" {if !$deo_lazyload}checked="checked"{/if}>
										<label for="deo_lazyload_off">{l s='Disable' mod='deotemplate'}</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
								<span class="desc">{l s='Effect lazyload on your site and show effect image fake when lazyload' mod='deotemplate'}</span>
							</div>
							<div class="line-space"></div>
							{/if}
							<!-- Mobile Friendly -->
							<label class="control-label">{l s='Mobile Friendly' mod='deotemplate'}</label>
							<div class="group-input clearfix">
								<div class="control-content">
									<span class="switch deo-switch">
										<input type="radio" name="mobile_friendly" class="mobile_friendly" id="mobile_friendly_on" value="1" {if $deo_mobile_friendly}checked="checked"{/if}>
										<label for="mobile_friendly_on">{l s='Enable' mod='deotemplate'}</label>
										<input type="radio" name="mobile_friendly" class="mobile_friendly" id="mobile_friendly_off" value="0" {if !$deo_mobile_friendly}checked="checked"{/if}>
										<label for="mobile_friendly_off">{l s='Disable' mod='deotemplate'}</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
								<span class="desc">{l s='Display friendly mobile on mobile and tablet device. Header will be simplify for mobile and tablet device and you can setup own content for mobile and tablet on Back Office' mod='deotemplate'}</span>
							</div>
							<div class="line-space"></div>
							<!-- Float Header -->
							<label class="control-label">{l s='Sticky Header' mod='deotemplate'}</label>
							<div class="group-input clearfix">
								<div class="control-content">
									<span class="switch deo-switch">
										<input type="radio" name="stickey_menu" class="stickey_menu" id="stickey_menu_on" value="1" {if $deo_stickey_menu}checked="checked"{/if}>
										<label for="stickey_menu_on">{l s='Enable' mod='deotemplate'}</label>
										<input type="radio" name="stickey_menu" class="stickey_menu" id="stickey_menu_off" value="0" {if !$deo_stickey_menu}checked="checked"{/if}>
										<label for="stickey_menu_off">{l s='Disable' mod='deotemplate'}</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
								<span class="desc">{l s='Keep header on top screen when scroll page' mod='deotemplate'}</span>
							</div>
							<div class="line-space"></div>
							<!-- Fonts -->
							<label class="control-label">{l s='Fonts' mod='deotemplate'}<a href="javascript:void(0)" class="reset-to-default reset-font">{l s='Reset' mod='deotemplate'}</a></label>
							<div class="group-input clearfix ">
								<label class="control-label label-small">{l s='Primary Font' mod='deotemplate'}</label>
								<div class="control-content">
									<div class="input-group">
										<input type="text" class="form-control deo-custom-font primary-font" name="primary-font" data-maxitems="8" autocomplete="off" aria-autocomplete="list" value="{$deo_primary_custom_font}">
			                    		<span class="input-group-btn more-font-family" title="{l s='Font suggestions' mod='deotemplate'}" data-toggle="deo-tooltip" data-position="top"></span>
			                    	</div>
									<label class="error-msg">{l s='Not empty!' mod='deotemplate'}</label>
								</div>
							</div>
							<div class="group-input clearfix">
								<label class="control-label label-small">{l s='Second Font' mod='deotemplate'}</label>
								<div class="control-content">
									<div class="input-group">
										<input type="text" class="form-control deo-custom-font second-font" name="second-font" data-maxitems="8" autocomplete="off" aria-autocomplete="list" value="{$deo_second_custom_font}">
										<span class="input-group-btn more-font-family" title="{l s='Font suggestions' mod='deotemplate'}" data-toggle="deo-tooltip" data-position="top"></span>
									</div>
									<label class="error-msg">{l s='Not empty!' mod='deotemplate'}</label>
								</div>
							</div>
							<span class="desc">{l s='Find more fonts at:' mod='deotemplate'} <a href="https://fonts.google.com/" target="_blank">Google Fonts</a><br>{l s='Example: Lato, Roboto, Open Sans...' mod='deotemplate'}</span>
							<a href="javascript:void(0)" title="{l s='Apply custom font' mod='deotemplate'}" class="apply-custom-font">
								<i class="deo-icon-loading"></i>
								<span>{l s='Apply custom font' mod='deotemplate'}</span>	
							</a>
							<div class="line-space"></div>
							<!-- Theme skin section -->
							{if $skins}
								<div class="group-input clearfix">
									<label class="control-label">{l s='Color Theme' mod='deotemplate'}<a href="javascript:void(0)" class="reset-to-default reset-color">{l s='Reset' mod='deotemplate'}</a></label>
									<div class="control-content">
										<div class="color-available deo-skins">
											<div class="control-label label-sub">- {l s='Color available' mod='deotemplate'}</div>
											<a href="javascript:void(0)" data-theme-skin-id="default" title="{l s='Default' mod='deotemplate'}" class="skin-default deo-theme-skin{if $deo_skin_default=='default' || (isset($deo_skin_default) && $deo_skin_default == false)} current-theme-skin{/if}" data-toggle="deo-tooltip" data-position="top">
												<label>{l s='Default' mod='deotemplate'}</label>
											</a>
											{foreach $skins as $skin}
												<a href="javascript:void(0)" data-theme-skin-id="{$skin.id}" title="{$skin.name}" data-theme-skin-css="{$skin.css}" class="deo-theme-skin{if isset($skin.icon) && $skin.icon} theme-skin-type-image{/if}{if $deo_skin_default==$skin.id} current-theme-skin{/if}" data-toggle="deo-tooltip" data-position="top">
													{if isset($skin.icon) && $skin.icon}
														<img src="{$skin.icon}" width="20" height="20" alt="{$skin.name}" />
													{else}
														<label>{$skin.name}</label>
													{/if}
												</a>
											{/foreach}
										</div>
										<div class="color-custom deo-skins">
											<div class="control-label label-sub">- {l s='Color custom' mod='deotemplate'}</div>
											<div class="select-color-custom">
												<div class="group-input clearfix">
													<label class="control-label label-small label-input">{l s='Primary color' mod='deotemplate'}</label>
													<div class="control-content">
														<div class="input-group colorpicker-skin colorpicker-element">
															<input type="text" class="color-picker form-control primary-color" name="primary-color" value="{$deo_primary_custom_color_skin}">
															<span class="input-group-btn">
																<span class="input-group-text colorpicker-input-addon"><i></i></span>
															</span>
														</div>
													</div>
													<label class="error-msg">{l s='Not empty!' mod='deotemplate'}</label>
												</div>
												<div class="group-input clearfix">
													<label class="control-label label-small label-input">{l s='Second color' mod='deotemplate'}</label>
													<div class="control-content">
														<div class="input-group colorpicker-skin colorpicker-element">
															<input type="text" class="color-picker form-control second-color" name="second-color" value="{$deo_second_custom_color_skin}">
															<span class="input-group-btn">
																<span class="input-group-text colorpicker-input-addon"><i></i></span>
															</span>
														</div>
													</div>
													<label class="error-msg">{l s='Not empty!' mod='deotemplate'}</label>
												</div>
											</div>
											<a href="javascript:void(0)" data-theme-skin-id="custom-skin" title="{l s='Apply custom color' mod='deotemplate'}" class="skin-custom deo-theme-skin{if $deo_skin_default=='custom-skin'} current-theme-skin{/if}">
												<label>{l s='Apply custom color' mod='deotemplate'}</label>
											</a>
										</div>
										<div class="custom-skin">{l s='Contact us to create your own color' mod='deotemplate'}</div>
									</div>
								</div>
							{/if}
						</div>
					</div>
				</div>
				<div class="paneltool demo-homepage tab-pane" id="demo-homepage">
					<div class="panelcontent block-panelcontent">
						<div class="panelinner">
							<!-- Show Profile -->
							{hook h="pagebuilderConfig" configName="profile"}
							<div class="line-space"></div>
							<label class="control-label">{l s='Customize Layout' mod='deotemplate'}</label>
							{hook h="pagebuilderConfig" configName="header"}
							{hook h="pagebuilderConfig" configName="content"}
							{hook h="pagebuilderConfig" configName="footer"}
							{* {hook h="pagebuilderConfig" configName="product"} *}
							{hook h="pagebuilderConfig" configName="product_list_builder"}
							<div class="line-space"></div>
							<label class="control-label">{l s='Blog Layouts' mod='deotemplate'}</label>
							<div class="group-input group-blogs clearfix">
								{if Tools::getIsset('blog_style')}
									{assign var="blog_style" value=Tools::getValue('blog_style')}
								{/if}
								<div class="control-content">
									{foreach $blog_styles as $item}
										<a class="deo_config{if isset($blog_style) && $blog_style == $item} active{/if} blog-demo" data-enable_js="false" href="{$blog_link->getFontBlogLink(array('blog_style' => $item))}">{$item}</a>
									{/foreach}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="paneltool demo-product-list tab-pane" id="demo-product-list">
					<div class="panelcontent block-panelcontent">
						<div class="panelinner">
							<label class="control-label">{l s='Infinite Scroll Product List' mod='deotemplate'}</label>
							<div class="group-input clearfix">
								<div class="control-content">
									<span class="switch deo-switch">
										<input type="radio" name="infinite_scroll" class="infinite_scroll" id="infinite_scroll_on" value="1" {if $deo_infinite_scroll}checked="checked"{/if}>
										<label for="infinite_scroll_on">{l s='Enable' mod='deotemplate'}</label>
										<input type="radio" name="infinite_scroll" class="infinite_scroll" id="infinite_scroll_off" value="0" {if !$deo_infinite_scroll}checked="checked"{/if}>
										<label for="infinite_scroll_off">{l s='Disable' mod='deotemplate'}</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
								<span class="desc">{l s='Use Infinite Scroll in Product List Pages (category, search, brand, manufacture...)' mod='deotemplate'}</span>
							</div>
							<div class="line-space"></div>

							{if isset($product_lists) && count($product_lists) > 1}
								{if Tools::getIsset('id_category')}
									{assign var="id_category" value=Tools::getValue('id_category')}
								{else}
									{assign var="id_category" value=Configuration::get('PS_HOME_CATEGORY')}
								{/if}
								{if Tools::getIsset('plist_key')}
									{assign var="plist_key" value=Tools::getValue('plist_key')}
								{elseif isset($product_list_default)}
									{assign var="plist_key" value=$product_list_default}
								{/if}
								<label class="control-label">{l s='Product List Layout' mod='deotemplate'}</label>
								<div class="group-input layout">
									<div class="control-content">
										{foreach $product_lists as $item}								
											<a class="deo_config{if isset($plist_key) && $plist_key == $item['plist_key']} active{/if} product-detail-demo" data-enable_js="false" href="{$link_deo->getCategoryLink($id_category, null, null, null, null, false)}?plist_key={$item['plist_key']}">{$item['name']}</a>
										{/foreach}
									</div>
								</div>
							{/if}
						</div>
					</div>
				</div>
				<div class="paneltool demo-product-page tab-pane" id="demo-product-page">
					<div class="panelcontent block-panelcontent">
						<div class="panelinner">
							{if isset($product_pages) && count($product_pages) > 1}
								{if Tools::getIsset('id_product')}
									{assign var="id_product" value=Tools::getValue('id_product')}
								{else}
									{assign var="id_product" value=1}
								{/if}
								{if Tools::getIsset('layout')}
									{assign var="layout" value=Tools::getValue('layout')}
								{elseif isset($detail_default)}
									{assign var="layout" value=$detail_default}
								{/if}
								<label class="control-label">{l s='Product Page Layout' mod='deotemplate'}</label>
								<div class="group-input layout">
									<div class="control-content">
										{foreach $product_pages as $item}								
											<a class="deo_config{if isset($layout) && $layout == $item['plist_key']} active{/if} product-detail-demo" data-enable_js="false" href="{$link_deo->getProductLink($id_product, null, null, null, null, null, Product::getDefaultAttribute($id_product), false, false, false, ['layout' => $item['plist_key']])}">{$item['name']}</a>
										{/foreach}
									</div>
								</div>
							{/if}
						</div>
					</div>
				</div>

				<div class="paneltool demo-checkout-page tab-pane" id="demo-checkout-page">
					<div class="panelcontent block-panelcontent">
						<div class="panelinner">
							{if isset($onepagecheckout_pages) && count($onepagecheckout_pages) > 1}
								{if Tools::getIsset('layout_opc')}
									{assign var="layout_opc" value=Tools::getValue('layout_opc')}
								{elseif isset($onepagecheckout_default)}
									{assign var="layout_opc" value=$onepagecheckout_default}
								{/if}
								<label class="control-label">{l s='One Page Checkout Layout' mod='deotemplate'}</label>
								<div class="group-input layout">
									<div class="control-content">
										{foreach $onepagecheckout_pages as $item}								
											<a class="deo_config{if isset($layout_opc) && $layout_opc == $item['plist_key']} active{/if} product-checkout-demo" data-enable_js="false" href="{Context::getContext()->link->getPageLink('order', null, null, array('layout_opc' => $item['plist_key']))}&checkout_with_opc">{$item['name']}</a>
										{/foreach}
									</div>
								</div>
							{/if}
						</div>
					</div>
				</div>

				{if isset($customize) && $customize}
					<div class="paneltool theme-customize tab-pane" id="theme-customize">
						<div class="panelcontent block-panelcontent">
							<div class="panelinner">
								<div class="custom-color">{l s='Contact us if you want to more customization' mod='deotemplate'}</div>
								{foreach from=$customize item=position key=key name="posts"}
									<div class="group-inputs">
										<label class="control-label">{$position.title}<a href="javascript:void(0)" class="reset-to-default reset-customize">{l s='Reset all' mod='deotemplate'}</a></label>
										{foreach from=$position.inputs item=item key=key_input}
											<label class="control-label label-small label-input">{$item.label}</label>
											<div class="group-input clearfix">
												<div class="control-content">
													{if isset($item.type) && $item.type == 'color' || $item.type == 'background-color' || $item.type == 'border-color'}
														<div class="input-group colorpicker-element colorpicker-customize-css">
															<input type="text" class="color-picker form-control" name="{$item.name}" value="{$item.value}" data-name="{$item.name}" data-type="{$item.type}" data-default="{$item.default}" data-selector="{$item.selector}" data-responsive="{$item.responsive}" data-special="{$item.special}" data-media="{$item.media}">
															<span class="input-group-btn">
																<span class="input-group-text colorpicker-input-addon"><i></i></span>
															</span>
														</div>
													{else}
														<input type="text" class="text-input form-control" name="{$item.name}" value="{$item.value}" data-name="{$item.name}" data-type="{$item.type}" data-default="{$item.default}" data-selector="{$item.selector}" data-responsive="{$item.responsive}" data-special="{$item.special}" data-media="{$item.media}">
													{/if}
													{if isset($item.desc) && $item.desc}
														<p class="desc">{$item.desc}</p>
													{/if}
												</div>
											</div>
										{/foreach}
										{if !$smarty.foreach.posts.last}
											<div class="line-space"></div>
										{/if}
									</div>
								{/foreach}
							</div>
						</div>
					</div>
				{/if}
			</div>
		</div>
	</div>
	{include file="module:deotemplate/views/templates/hook/demo.tpl"}
{/if}
