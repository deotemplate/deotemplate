{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<article class="blog-container">
	<div class="blog-meta">
		<h4 class="title">
			<a href="{$blog.link}" title="{$blog.title}">{$blog.title}</a>
		</h4>
	</div>
	<div class="blog-image">
		{if $blog.image}
			<a href="{$blog.link}" title="{$blog.title}" class="image">
				{if isset($lazyload) && $lazyload}
					<span class="lazyload-wrapper" style="padding-bottom: {$blog.rate_image};">
	                    <span class="lazyload-icon"></span>
	                </span>
	                <img data-src="{$blog.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload" title="{$blog.title}" alt="{$blog.title}"/>
				{else}
					<img src="{$blog.image}" title="{$blog.title}" alt="{$blog.title}" class="img-fluid"/>
				{/if}
			</a>
		{/if}
	</div>
	<div class="blog-infomations">
		{if isset($blog.author_link) && $blog.author_link && isset($blog.author) && $blog.author}
			<span class="author">
				<i class="icon-author"></i> 
				<a href="{$blog.author_link}" class="meta-value" title="{$blog.author}"> {$blog.author}</a>
			</span>
		{/if}
		<span class="category"> 
			<i class="icon-list"></i> 
			<a href="{$blog.category_link}" class="meta-value" title="{$blog.category_title}"> {$blog.category_title}</a>
		</span>
		<span class="created">
			<i class="icon-created"></i>  
			<time class="date meta-value" datetime="{strtotime($blog.date_add)|date_format:"%Y"}">
				<span class="day">
					{assign var='blog_day' value=strtotime($blog.date_add)|date_format:"%e"}	
					{l s=$blog_day mod='deotemplate'} <!-- day of month -->	
				</span>
				<span class="month">
					{assign var='blog_month' value=strtotime($blog.date_add)|date_format:"%b"}
					{l s=$blog_month mod='deotemplate'}		<!-- month-->
				</span>
				<span class="year">
					{assign var='blog_year' value=strtotime($blog.date_add)|date_format:"%Y"}		
					{l s=$blog_year mod='deotemplate'}	<!-- year -->
				</span>
			</time>
		</span>
		<span class="comment">
			<i class="icon-comment"></i> 
			<span class="meta-value">{$blog.comment_count|intval}{* {l s='comment' mod='deotemplate'} *}</span> 
		</span>
		<span class="views">
			<i class="icon-views"></i> 
			<span class="meta-value">{$blog.views|intval}{* {l s='views' mod='deotemplate'} *}</span>
		</span>
	</div>
	<div class="blog-desc">
		{$blog.description|strip_tags:'UTF-8'|truncate:160:'...' nofilter}{* HTML form , no escape necessary *}
	</div>
</article>

