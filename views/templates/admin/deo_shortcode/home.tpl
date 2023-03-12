{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="{if $deo_debug_mode}deo-debug-mode{/if}">
    {if isset($errorText) && $errorText}
    <div class="error alert alert-danger">
        {$errorText|escape:'html':'UTF-8'}
    </div>
    {/if}
    {if isset($errorSubmit) && $errorSubmit}
    <div class="error alert alert-danger">
        {$errorSubmit|escape:'html':'UTF-8'}
    </div>
    {/if}

    <div id="home_wrapper" class="default">
        <div class="position-cover row">
            {include file='./position.tpl'}
        </div>
    </div>

    <div id="deo_loading" class="deo-loading" style="display: none;">
        <div class="spinner">
            <div class="item-1"></div>
            <div class="item-2"></div>
            <div class="item-3"></div>
        </div>
    </div>
    {include file="$tplPath/deo_shortcode/home_form.tpl"}
    <script type="text/javascript">
    	{addJsDef imgModuleLink=$imgModuleLink}
    	{addJsDef deoAjaxShortCodeUrl=$ajaxShortCodeUrl}
    	{addJsDef deoAjaxHomeUrl=$ajaxHomeUrl}
    	{addJsDef deoImgController=$imgController}
    		
        $(document).ready(function(){
            var $deoHomeBuilder = $(document).deotemplate();
            $deoHomeBuilder.ajaxShortCodeUrl = deoAjaxShortCodeUrl;
            $deoHomeBuilder.ajaxHomeUrl = deoAjaxHomeUrl;
            $deoHomeBuilder.lang_id = '{$lang_id|escape:'html':'UTF-8'}';
            $deoHomeBuilder.imgController = deoImgController;        
            $deoHomeBuilder.process();
        });
    </script>
</div>