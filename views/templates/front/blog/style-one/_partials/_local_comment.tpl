{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="deo-comment-local">
	{if $configures->get('BLOG_ITEM_SHOW_LIST_COMMENT', 1) == 1}
		<h3>{l s='Comments' mod='deotemplate'}</h3>
		{if isset($comments) && count($comments) > 0}
			<div class="comments clearfix">
				{foreach from=$comments item=comment name=comment} {$default=''}
					<div class="comment-item" id="comment{$comment.id_deoblog_comment}">
						<div class="comment-avata">
							
						</div>
						<div class="comment-wrap">
							<div class="comment-meta">
								<span class="comment-infor">
									<span class="comment-posted-by">{l s='By:' mod='deotemplate'}<span> {$comment.user}</span></span>
									<span class="comment-created"><span> {strtotime($comment.date_add)|date_format:"%A, %B %e, %Y"}</span></span>
								</span>
								<span class="comment-link"><a href="{$blog_link}#comment{$comment.id_deoblog_comment|intval}">{l s='Comment Link' mod='deotemplate'}</a></span>
							</div>
							<div class="comment-content">
								{$comment.comment|nl2br nofilter}{* HTML form , no escape necessary *}
							</div>
						</div>
					</div>
				{/foreach}
				<div class="top-pagination-content clearfix bottom-line">
					{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_pagination.tpl"}
				</div>
			</div>
		{else}
			<p class="alert alert-success">{l s='No comment at this time!' mod='deotemplate'}</p>
		{/if}
	{/if}
		
	{if $configures->get('BLOG_ITEM_SHOW_FORM_COMMENT', 1) == 1}
		<h3 class="title-comment">{l s='Leave your comment' mod='deotemplate'}</h3>
		<form class="form-horizontal clearfix" method="post" id="comment-form" action="{$blog_link}" onsubmit="return false;">
			<div class="form-group row">
				<div  class="col-lg-3">
					<label class="control-label" for="inputFullName">{l s='Full Name' mod='deotemplate'}</label>
				</div>
				<div class="col-lg-9">
					<input type="text" name="user" placeholder="{l s='Enter your full name' mod='deotemplate'}" id="inputFullName" class="form-control">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-3">
					<label class="control-label" for="inputEmail">{l s='Email' mod='deotemplate'}</label>
				</div>
				<div class="col-lg-9">
					<input type="text" name="email"  placeholder="{l  s='Enter your email' mod='deotemplate'}" id="inputEmail" class="form-control">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-3">
					<label class="control-label" for="inputComment">{l  s='Comment' mod='deotemplate'}</label>
				</div>
				<div class="col-lg-9">
					<textarea type="text" name="comment" rows="6"  placeholder="{l  s='Enter your comment' mod='deotemplate'}" id="inputComment" class="form-control"></textarea>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-3">
					<label class="control-label" for="inputEmail">{l s='Captcha' mod='deotemplate'}</label>
				</div>
				<div class="col-lg-8 col-md-8 deo-captcha">
					 <img src="{$captcha_image}" class="comment-capcha-image" align="left"/>
					<input class="form-control" type="text" name="captcha" value="" size="10"  />
				</div>
			</div>
			<input type="hidden" name="id_deoblog" value="{$id_deoblog|intval}">
			<div class="form-group row">
				<div class="col-lg-9 col-lg-offset-3">
					<button class="btn btn-secondary btn-outline btn-submit-comment-wrapper" name="submitcomment" type="submit">
						<span class="deo-icon-loading-button"></span>
						<span class="text">{l s='Submit' mod='deotemplate'}</span>
					</button>
				</div>
			</div>
		</form>
	{/if}
</div>