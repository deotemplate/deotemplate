{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($tags) AND !empty($tags)}
    <section id="tags_blog_block" class="block deo-blog-tags hidden-sm-down">
        <div class="box-title">
            <p class='title_block h4'><span>{l s='Tags Post' mod='deotemplate'}</span></p>
        </div>
        <div class="block_content deo-blog-tags clearfix">
            {foreach from=$tags item="tag"}
                <a href="{$tag.link}" class="tag">{$tag.tag}</a>
            {/foreach}
        </div>
    </section>
{/if}