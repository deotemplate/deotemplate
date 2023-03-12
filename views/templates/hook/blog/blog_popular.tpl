{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($blogs) AND !empty($blogs)}
    <section id="blogPopularBlog" class="block deo-blog-sidebar hidden-sm-down">
        <div class="box-title">
            <p class='title_block h4'><span>{l s='Popular Blog' mod='deotemplate'}</span></p>
        </div>
        <div class="block_content products-block">
            <ul class="list-blogs">
                {foreach from=$blogs item="blog" name=blog}
                    {include file="module:deotemplate/views/templates/hook/blog/blog_item.tpl"}
                {/foreach}
            </ul>
        </div>
    </section>
{/if}