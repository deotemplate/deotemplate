{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="category-wrapper">
    <div class="panel panel-default">
        <h3 class="panel-title">{l s='Categories Sitemap'  mod='deotemplate'}</h3>
        <div class="panel-content">
            {$tree nofilter}{* HTML form , no escape necessary *}
            <hr>
            {l s='* Drag and drop to sort category.'  mod='deotemplate'}
        </div>
    </div>
</div>
{literal}
<script type="text/javascript">
    $("#category-wrapper ol").DeoMegaMenuList({action:action, addnew:addnew});
</script>
{/literal}