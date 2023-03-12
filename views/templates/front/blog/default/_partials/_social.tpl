{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="deo-blog-social-share">
	{* <div class="social-heading"> <b>{l s='Like This' mod='deotemplate'}</b> </div> *}
	<div class="social-wrap">
		<!-- Twitter Button -->
		<div class="itemTwitterButton">
			<a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal"></a>
			<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
		</div>
		 
		<!-- Facebook Button -->
		<div class="itemFacebookButton">
			<div id="fb-root"></div>
			<script type="text/javascript">
				(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
			<div class="fb-like" data-send="false" data-width="200" data-show-faces="true"></div>
		</div>
	</div>	 
</div>




