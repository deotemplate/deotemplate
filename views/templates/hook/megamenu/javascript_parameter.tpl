{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<script type="text/javascript">
	{literal}
	var current_link = "{/literal}{$current_link}{literal}";		
	var currentURL = window.location;
	currentURL = String(currentURL);
	currentURL = currentURL.replace("https://","").replace("http://","").replace("www.","").replace( /#\w*/, "" );
	current_link = current_link.replace("https://","").replace("http://","").replace("www.","");
	var deo_menu_txt = "{/literal}{l s='Menu' mod='deotemplate'}";{literal}
	var isHomeMenu = 0;
	{/literal}
</script>