{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $fb_enable && $fb_app_id != ''}
    {literal}
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '{/literal}{$fb_app_id}{literal}',
                cookie     : true,  // enable cookies to allow the server to access 
                xfbml      : true,  // parse social plugins on this page
                version    : 'v2.9', // use graph api version 2.8
                scope: 'email, user_birthday',
            });
        };

        // Load the SDK asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id; js.defer = " ";
            js.src = "//connect.facebook.net/{/literal}{$lang_locale|escape:'html':'UTF-8'}{literal}/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    {/literal}
{/if}
{if $google_enable && $google_client_id != ''}
    <script type="text/javascript">
        var google_client_id= "{$google_client_id|escape:'html':'UTF-8'}";
    </script>
    <script type="text/javascript" src="https://apis.google.com/js/api:client.js" defer></script>
{/if}