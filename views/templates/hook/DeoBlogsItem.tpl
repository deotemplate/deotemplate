{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{function name=blog_info}
	{if isset($formAtts.show_author) && $formAtts.show_author}
		<span class="author">
			<i class="icon-author"></i><span>{$blog.author|escape:'html':'UTF-8'}</span>
		</span>
		<i class="spacing"></i>
	{/if}		
	{if isset($formAtts.show_created_date) && $formAtts.show_created_date}
		<span class="created">
			<i class="icon-created"></i>
			<time class="date" datetime="{strtotime($blog.date_add)|date_format:"%Y"}">
				<span class="day">
					{assign var='blog_day' value=strtotime($blog.date_add)|date_format:"%d"}
					{l s=$blog_day mod='deotemplate'}
				</span>
				<span class="month">						
					{assign var='blog_month' value=strtotime($blog.date_add)|date_format:"%b"}
					{l s=$blog_month mod='deotemplate'}
				</span>
				<span class="year">
					{assign var='blog_year' value=strtotime($blog.date_add)|date_format:"%Y"}						
					{l s=$blog_year mod='deotemplate'}
				</span>
			</time>
		</span>
		<i class="spacing"></i>
	{/if}
	{if isset($formAtts.show_category) && $formAtts.show_category}
		<span class="cat"> 
			<i class="icon-list"></i>
			<a href="{$blog.category_link}{*full url can not escape*}" title="{$blog.category_title|escape:'html':'UTF-8'}">{$blog.category_title|escape:'html':'UTF-8'}</a>
		</span>
		<i class="spacing"></i>
	{/if}
	{if isset($formAtts.show_comment) && $formAtts.show_comment && isset($blog.comment_count)}
		<span class="nbcomment">
			<i class="icon-comment"></i><span>{$blog.comment_count|intval} {l s='comment(s)' mod='deotemplate'}</span>
		</span>
		<i class="spacing"></i>
	{/if}

	{if isset($formAtts.show_views) && $formAtts.show_views}
		<span class="views">
			<i class="icon-views"></i><span>{$blog.views|intval} {l s='view(s)' mod='deotemplate'}</span>
		</span>	
		<i class="spacing"></i>
	{/if}
{/function}

<div class="blog-container" itemscope itemtype="https://schema.org/Blog">
    <div class="left-block">
        <div class="blog-image-container">
            <a class="blog_img_link" href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}" itemprop="url">
				{if isset($formAtts.show_image) && $formAtts.show_image && isset($blog.image) && $blog.image}
					{if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}
						<span class="lazyload-wrapper" style="padding-bottom: {(isset($formAtts.rate_image) && $formAtts.rate_image) ? $formAtts.rate_image : $blog.rate_image};">
							<span class="lazyload-icon"></span>
						</span>
						{* <img class="img-fluid {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "owlcarousel"}lazyOwl{/if}" {if isset($formAtts.carousel_type) &&  $formAtts.carousel_type == "owlcarousel"}data-src{elseif isset($formAtts.carousel_type) && $formAtts.carousel_type == "slickcarousel"}data-lazy{/if}="{$blog.image}" 
							src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$blog.title nofilter}" title="{$blog.title nofilter}" itemprop="image" 
						/> *}
						<img class="img-fluid" data-lazy="{$blog.image}" 
							src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$blog.title nofilter}" title="{$blog.title nofilter}" itemprop="image" loading="lazy"
						/>
					{else}
						<img class="img-fluid" src="{$blog.image}" alt="{$blog.title nofilter}" title="{$blog.title nofilter}" itemprop="image" loading="lazy"
						/>
					{/if}
				{/if}
            </a>
        </div>
		<div class="blog-meta">
			{blog_info}
		</div>
    </div>
    <div class="right-block">
    	{if isset($formAtts.show_title) && $formAtts.show_title}
        	<h3 class="blog-title" itemprop="name"><a href="{$blog.link}{*full url can not escape*}" title="{$blog.title|escape:'html':'UTF-8'}">{$blog.title|strip_tags:'UTF-8'|truncate:80:'...'}</a></h3>
        {/if}
		
		<div class="blog-meta">
			{blog_info}
		</div>

		{if isset($formAtts.show_desc) && $formAtts.show_desc}
	        <div class="blog-desc" itemprop="description">
	            {$blog.description|strip_tags:'UTF-8'|truncate:250:'...'}
	        </div>
        {/if}
       	
       	{if isset($formAtts.show_read_more) && $formAtts.show_read_more}
	        <a class="btn-more btn" href="{$blog.link}">
	            {l s='Read more' mod='deotemplate'}
	        </a>
	    {/if}
       
    </div>
</div>

