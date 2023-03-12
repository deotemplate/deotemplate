<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'deotemplate/classes/Feature/DeoWishList.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Feature/DeoFeatureProduct.php');

class DeoTemplateViewWishlistModuleFrontController extends ModuleFrontController
{
    public $php_self;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initContent()
    {
        $this->php_self = 'viewwishlist';

        parent::initContent();
        if (!(int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST')) {
            return Tools::redirect('index.php?controller=404');
        }
        $token = Tools::getValue('token');

        if ($token) {
            $wishlist = DeoWishList::getByToken($token);
            $wishlists = DeoWishList::getByIdCustomer((int)$wishlist['id_customer']);
            if (count($wishlists) > 1) {
                foreach ($wishlists as $key => $wishlists_item) {
                    if ($wishlists_item['id_wishlist'] == $wishlist['id_wishlist']) {
                        unset($wishlists[$key]);
                    }
                }
            } else {
                $wishlists = array();
            }

            $products = array();
            $wishlist_product = DeoWishList::getSimpleProductByIdWishlist((int)$wishlist['id_wishlist']);
            $product_object = new DeoFeatureProduct();
            if (count($wishlist_product) > 0) {
                foreach ($wishlist_product as $wishlist_product_item) {
                    $list_product_tmp = array();
                    $list_product_tmp['wishlist_info'] = $wishlist_product_item;
                    $list_product_tmp['product_info'] = $product_object->getTemplateVarProductExtend($wishlist_product_item['id_product'], $wishlist_product_item['id_product_attribute']);
                    $list_product_tmp['product_info']['wishlist_quantity'] = $wishlist_product_item['quantity'];
                    $products[] = $list_product_tmp;
                }
            }
            DeoWishList::incCounter((int)$wishlist['id_wishlist']);
            $this->context->smarty->assign(
                array(
                    'current_wishlist' => $wishlist,
                    'wishlists' => $wishlists,
                    'products' => $products,
                    'view_wishlist_url' => $this->context->link->getModuleLink('deotemplate','viewwishlist'),
                    'show_button_cart' => (int) DeoHelper::getConfig('ENABLE_AJAX_CART'),
                    'is_rewrite_active' => (bool)Configuration::get('PS_REWRITING_SETTINGS'),
                )
            );
        }
        $this->setTemplate('module:deotemplate/views/templates/front/feature/wishlist_view.tpl');
    }

    // add meta title, meta description, meta keywords
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['meta']['title'] = Configuration::get('PS_SHOP_NAME').' - '.$this->module->l('View Wishlist', 'viewwishlist');
        $page['meta']['keywords'] = $this->module->l('view-wishlist', 'viewwishlist');
        $page['meta']['description'] = $this->module->l('view Wishlist', 'viewwishlist');
        $page['body_classes']['deo-view-wishlist-page'] = true;

        return $page;
    }

    // add breadcrumb
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('My Account', 'viewwishlist'),
            'url' => $this->context->link->getPageLink('my-account', true),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('My Wishlist', 'viewwishlist'),
            'url' => $this->context->link->getModuleLink('deotemplate','mywishlist'),
        );

        return $breadcrumb;
    }

    // get layout
    public function getLayout()
    {
        $entity = 'module-deofeature-'.$this->php_self;
        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);
        if ($overridden_layout = Hook::exec('overrideLayoutTemplate', array(
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
            ))) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }
        return $layout;
    }
}
