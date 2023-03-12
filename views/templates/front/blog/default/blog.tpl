{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file=$layout}
{block name='content'}
	<section id="main">
		<article class="deo-blog-detail {$template}">
			{if isset($error)}
				<div class="alert alert-warning">{l s='Sorry, We are updating data, please come back later!!!!' mod='deotemplate'}</div>
			{else}
				{if $is_active}
					<h1 class="blog-title">{$blog->meta_title}</h1>
					<div class="blog-meta">
						{if $configures->get('BLOG_ITEM_SHOW_AUTHOR', 1)}
							<span class="author">
								<i class="icon-author"></i>
								<a href="{$blog->author_link}" class="meta-value" title="{$blog->author}">{$blog->author}</a>
							</span>
						{/if}

						{if $configures->get('BLOG_ITEM_SHOW_CATEGORY', 1)}
							<span class="category"> 
								<i class="icon-list"></i> 
								<a href="{$blog->category_link}" class="meta-value" title="{$blog->category_title}">{$blog->category_title}</a>
							</span>
						{/if}

						{if $configures->get('BLOG_ITEM_SHOW_CREATED', 1)}
							<span class="created">
								<i class="icon-created"></i>
								<time class="date meta-value" datetime="{strtotime($blog->date_add)|date_format:"%Y"}">
									<span class="day">
										{assign var='blog_day' value=strtotime($blog->date_add)|date_format:"%e"}	
										{l s=$blog_day mod='deotemplate'} <!-- day of month -->	
									</span>
									<span class="month">
										{assign var='blog_month' value=strtotime($blog->date_add)|date_format:"%b"}
										{l s=$blog_month mod='deotemplate'}		<!-- month-->
									</span>
									<span class="year">
										{assign var='blog_year' value=strtotime($blog->date_add)|date_format:"%Y"}		
										{l s=$blog_year mod='deotemplate'}	<!-- year -->
									</span>
								</time>
							</span>
						{/if}
						
						{if isset($blog->comment_count) && $configures->get('BLOG_ITEM_SHOW_COUNT_COMMENT', 1)}
							<span class="comment">
								<i class="icon-comment"></i> 
								<span class="meta-value">{$blog->comment_count|intval} {l s='comment(s)' mod='deotemplate'}</span> 
							</span>
						{/if}

						{if isset($blog->views) && $configures->get('BLOG_ITEM_SHOW_VIEWS', 1)}
							<span class="views">
								<i class="icon-views"></i> 
								<span class="meta-value">{$blog->views|intval} {l s='view(s)' mod='deotemplate'}</span>
							</span>
						{/if}
					</div>

					{if $configures->get('BLOG_ITEM_SHOW_DESCRIPTION', 1)}
						<div class="blog-short-description">
							{$blog->description nofilter}{* HTML form , no escape necessary *}
						</div>
					{/if}

					{if $blog->image && $configures->get('BLOG_ITEM_SHOW_IMAGE', 1)}
						<div class="blog-image">
							{assign var=image_src value=$image->getImageBlogBySize($blog->image, 'large')}
							{if isset($lazyload) && $lazyload}
								<span class="lazyload-wrapper" style="padding-bottom: {$blog->rate_image};">
				                    <span class="lazyload-icon"></span>
				                </span>
				                <img data-src="{$image_src}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload" title="{$blog->meta_title}" alt="{$blog->meta_title}"/>
							{else}
								<img src="{$image_src}" title="{$blog->meta_title}" alt="{$blog->meta_title}" class="img-fluid"/>
							{/if}
						</div>
					{/if}

					<div class="blog-description">
						{$blog->content nofilter}{* HTML form , no escape necessary *}
					</div>

					{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_social.tpl"}
					{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_tags.tpl" tags=$blog->tags}

					{if !empty($blog_same_category) || !empty($blog_related_tag)}
						<div class="deo-extra-blogs row">
							{if !empty($blog_same_category)}
								<div class="col-lg-6 col-md-6 col-xs-12">
									<h4>{l s='Same category' mod='deotemplate'}</h4>
									<ul class="same-category-blogs">
										{foreach from=$blog_same_category item=cblog name=cblog}
											<li><a href="{$cblog.link}">{$cblog.title}</a></li>
										{/foreach}
									</ul>
								</div>
							{/if}
							{if !empty($blog_related_tag)}
								<div class="col-lg-6 col-md-6 col-xs-12">
									<h4>{l s='Related blog' mod='deotemplate'}</h4>
									<ul class="deo-related-tags">
										{foreach from=$blog_related_tag item=cblog name=cblog}
											<li><a href="{$cblog.link}">{$cblog.title}</a></li>
										{/foreach}
									</ul>
								</div>
							{/if}
						</div>
					{/if}

					{if $configures->get('BLOG_ITEM_COMMENT_ENGINE') != 'none'}
						<div class="deo-blog-comment-block clearfix">
							{if $configures->get('BLOG_ITEM_COMMENT_ENGINE') == 'facebook'}
								{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_facebook_comment.tpl"}
							{else}
								{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_local_comment.tpl"}
							{/if}
						</div>
					{/if}
				{else}
					<div class="alert alert-warning">{l s='Sorry, This blog is not avariable. May be this was unpublished or deleted.' mod='deotemplate'}</div>
				{/if}
			{/if}
		</article>
	</section>
	
	<div class="hidden-xl-down hidden-xl-up datetime-translate">
		{l s='Sunday' mod='deotemplate'}
		{l s='Monday' mod='deotemplate'}
		{l s='Tuesday' mod='deotemplate'}
		{l s='Wednesday' mod='deotemplate'}
		{l s='Thursday' mod='deotemplate'}
		{l s='Friday' mod='deotemplate'}
		{l s='Saturday' mod='deotemplate'}
		
		{l s='January' mod='deotemplate'}
		{l s='February' mod='deotemplate'}
		{l s='March' mod='deotemplate'}
		{l s='April' mod='deotemplate'}
		{l s='May' mod='deotemplate'}
		{l s='June' mod='deotemplate'}
		{l s='July' mod='deotemplate'}
		{l s='August' mod='deotemplate'}
		{l s='September' mod='deotemplate'}
		{l s='October' mod='deotemplate'}
		{l s='November' mod='deotemplate'}
		{l s='December' mod='deotemplate'}		
	</div>
{/block}