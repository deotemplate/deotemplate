{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<li class="blog-list-item clearfix">
    <div class="blog-image">
        <a class="products-block-image" title="{$blog.title}" href="{$blog.link}">
        	{if $deo_lazyload}
				<span class="lazyload-wrapper" style="padding-bottom: {$blog.rate_image};">
					<span class="lazyload-icon"></span>
				</span>
            	<img alt="{$blog.title}" data-src="{$blog.image}" class="img-fluid lazyload">
            {else}
            	<img alt="{$blog.title}" src="{$blog.image}" class="img-fluid" loading="lazy">
            {/if}
        </a>
    </div>
    <div class="blog-content">
    	<h3 class="blog-title"><a title="{$blog.title}" href="{$blog.link}">{$blog.title}</a></h3>
    	<div class="blog-meta">
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
			<span class="views">
				<i class="icon-views"></i> 
				<span class="meta-value">{$blog.views|intval} {l s='view(s)' mod='deotemplate'}</span>
			</span>
			{if isset($blog.comment_count) && DeoHelper::getConfig('BLOG_ITEM_COMMENT_ENGINE') == 'local'}
				<span class="comment">
					<i class="icon-comment"></i> 
					<span class="meta-value">{$blog.comment_count|intval} {l s='comment(s)' mod='deotemplate'}</span> 
				</span>
			{/if}
    	</div>
    </div>
</li> 