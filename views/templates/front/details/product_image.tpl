{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='page_content_container'}
  <section class="page-content" id="content">
    {block name='page_content'}
      <div class="images-container">
        {block name='product_cover_thumbnails'}
          {include file='catalog/_partials/product-cover.tpl'}
          {include file='catalog/_partials/product-images.tpl'}
        {/block}
        {hook h='displayAfterProductThumbs'}
      </div>
    {/block}
  </section>
{/block}