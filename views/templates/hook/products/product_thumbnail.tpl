{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='product_thumbnail'}
  <a href="{$product.canonical_url}" class="thumbnail product-thumbnail" data-second_image="{$second_image}" data-deo_size="{$deo_size}" data-labelflag="{$labelflag}">
    {if $product.default_image}
      {if (isset($formAtts.lazyload) && $formAtts.lazyload) || (isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload) || (!isset($formAtts) && $deo_lazyload)}
        <span class="lazyload-wrapper" style="padding-bottom: {DeoHelper::calculateRateImage($product.default_image.bySize[$deo_size].width,$product.default_image.bySize[$deo_size].height)};">
          <span class="lazyload-icon"></span>
        </span>
        <img
          class="img-fluid {if (isset($formAtts.lazyload) && $formAtts.lazyload) || !isset($formAtts)}lazyload{/if}"
          {if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}data-lazy{else}data-src{/if} = "{$product.default_image.bySize[$deo_size].url}"
          src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
          alt = "{if !empty($product.default_image.legend)}{$product.default_image.legend}{else}{$product.name}{/if}"
          data-full-size-image-url = "{$product.default_image.large.url}"
          data-image-type="{$deo_size}"
          loading="lazy"
        >
      {else}
        <img
          class="img-fluid"
          src = "{$product.default_image.bySize[$deo_size].url}"
          alt = "{if !empty($product.default_image.legend)}{$product.default_image.legend}{else}{$product.name}{/if}"
          data-full-size-image-url = "{$product.default_image.large.url}"
          data-image-type="{$deo_size}" 
          loading="lazy"
        >
      {/if}
      {if isset($second_image) && $second_image}
        {include file="module:deotemplate/views/templates/hook/products/_partials/second_image.tpl" deo_size=$deo_size}
      {/if}
    {else}
      {if (isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload) || (!isset($formAtts) && $deo_lazyload)}
        <span class="lazyload-wrapper" style="padding-bottom: {DeoHelper::calculateRateImage($urls.no_picture_image.bySize[$deo_size].width,$urls.no_picture_image.bySize[$deo_size].height)};">
          <span class="lazyload-icon"></span>
        </span>
        <img
          class="img-fluid {if (isset($formAtts.lazyload) && $formAtts.lazyload) || !isset($formAtts)}lazyload{/if}"
          {if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}data-lazy{else}data-src{/if} = "{$urls.no_picture_image.bySize[$deo_size].url}"
          src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
          alt = "{if !empty($product.default_image.legend)}{$product.default_image.legend}{else}{$product.name}{/if}" 
          loading="lazy"
        >
      {else}
        <img
          class="img-fluid"
          src = "{$urls.no_picture_image.bySize[$deo_size].url}"
          alt = "{if !empty($product.default_image.legend)}{$product.default_image.legend}{else}{$product.name}{/if}" 
          loading="lazy"
        >
      {/if}
    {/if}
    {if $labelflag == 'all'}
      {include file="module:deotemplate/views/templates/hook/products/product_flags.tpl"}
    {elseif $labelflag == 'newdiscount'}
      {include file="module:deotemplate/views/templates/hook/products/product_flags_new_discount.tpl"}
      {include file="module:deotemplate/views/templates/hook/products/label_new_discount.tpl"}
    {elseif $labelflag == 'newsale'}
      {include file="module:deotemplate/views/templates/hook/products/product_flags_new_sale.tpl"}
      {include file="module:deotemplate/views/templates/hook/products/label_new_sale.tpl"}
    {/if}
  </a>
{/block}