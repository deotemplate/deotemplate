<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'deotemplate/classes/Feature/DeoCompareProduct.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Feature/DeoFeatureProduct.php');

class DeoTemplateCompareModuleFrontController extends ModuleFrontController
{
    public $php_self;

    /**
     * Display ajax content (this function is called instead of classic display, in ajax mode)
     */
    public function displayAjax()
    {
        if (!$this->isTokenValid() || !Tools::getValue('action') || !Tools::getValue('ajax') || !Tools::getValue('id_product')) {
            // Ooops! Token is not valid!
            die('Token is not valid, hack stop');
        };
        // Add or remove product with Ajax
        if (Tools::getValue('ajax') && Tools::getValue('id_product') && Tools::getValue('action')) {
            if (Tools::getValue('action') == 'add') {
                $id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: false;
                if (DeoCompareProduct::getNumberProducts($id_compare) < DeoHelper::getConfig('COMPARATOR_MAX_ITEM')) {
                    DeoCompareProduct::addDeoCompareProduct($id_compare, (int)Tools::getValue('id_product'));
                } else {
                    $this->ajaxDie('0');
                }
            } elseif (Tools::getValue('action') == 'remove') {
                if (isset($this->context->cookie->id_compare)) {
                    DeoCompareProduct::removeDeoCompareProduct((int)$this->context->cookie->id_compare, (int)Tools::getValue('id_product'));
                } else {
                    $this->ajaxDie('0');
                }
            } else {
                $this->ajaxDie('0');
            }
            $this->ajaxDie('1');
        }
        $this->ajaxDie('0');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $this->php_self = 'compare';
        
        if (Tools::getValue('ajax')) {
            return;
        }
        DeoCompareProduct::cleanDeoCompareProducts('week');
        $hasProduct = false;

        if (!DeoHelper::getConfig('COMPARATOR_MAX_ITEM') || !(int) DeoHelper::getConfig('ENABLE_PRODUCT_COMPARE')) {
            return Tools::redirect('index.php?controller=404');
        }

        $ids = null;
        
        if (isset($this->context->cookie->id_compare)) {
            $ids = DeoCompareProduct::getDeoCompareProducts($this->context->cookie->id_compare);
        }
        
        if ($ids) {
            if (count($ids) > 0) {
                if (count($ids) > DeoHelper::getConfig('COMPARATOR_MAX_ITEM')) {
                    $ids = array_slice($ids, 0, DeoHelper::getConfig('COMPARATOR_MAX_ITEM'));
                }

                $listProducts = array();
                $listFeatures = array();

                foreach ($ids as $k => &$id) {
                    $curProduct = new Product((int)$id, true, $this->context->language->id);
                    if (!Validate::isLoadedObject($curProduct) || !$curProduct->active || !$curProduct->isAssociatedToShop()) {
                        if (isset($this->context->cookie->id_compare)) {
                            DeoCompareProduct::removeDeoCompareProduct($this->context->cookie->id_compare, $id);
                        }
                        unset($ids[$k]);
                        continue;
                    }

                    foreach ($curProduct->getFrontFeatures($this->context->language->id) as $feature) {
                        $listFeatures[$curProduct->id][$feature['id_feature']] = $feature['value'];
                    }

                    $product_object = new DeoFeatureProduct();
                    $curProduct = $product_object->getTemplateVarProductExtend($id);
                    $listProducts[] = $curProduct;
                }
                
                if (count($listProducts) > 0) {
                    $width = 80 / count($listProducts);

                    $hasProduct = true;
                    $ordered_features = $this->getFeaturesForComparison($ids, $this->context->language->id);
                    $this->context->smarty->assign(array(
                        'ordered_features' => $ordered_features,
                        'product_features' => $listFeatures,
                        'products' => $listProducts,
                        'width' => $width,
                        // 'HOOK_COMPARE_EXTRA_INFORMATION' => Hook::exec('displayCompareExtraInformation', array('list_ids_product' => $ids)),
                        // 'HOOK_EXTRA_PRODUCT_COMPARISON' => Hook::exec('displayProductComparison', array('list_ids_product' => $ids)),
                        // 'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
                        'list_product' => $ids,
                    ));
                } elseif (isset($this->context->cookie->id_compare)) {
                    $object = new DeoCompareProduct((int)$this->context->cookie->id_compare);
                    if (Validate::isLoadedObject($object)) {
                        $object->delete();
                    }
                }
            }
        }
        $this->context->smarty->assign('hasProduct', $hasProduct);
        $this->setTemplate('module:deotemplate/views/templates/front/feature/products_compare.tpl');
    }
    
    public function getFeaturesForComparison($list_ids_product, $id_lang)
    {
        if (!Feature::isFeatureActive()) {
            return false;
        }

        $ids = '';
        foreach ($list_ids_product as $id) {
            $ids .= (int)$id.',';
        }

        $ids = rtrim($ids, ',');

        if (empty($ids)) {
            return false;
        }

        return Db::getInstance()->executeS('SELECT f.*, fl.*
            FROM `'._DB_PREFIX_.'feature` f
            LEFT JOIN `'._DB_PREFIX_.'feature_product` fp
                ON f.`id_feature` = fp.`id_feature`
                        LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl
                                ON f.`id_feature` = fl.`id_feature`
                        WHERE fp.`id_product` IN ('.$ids.')
                        AND `id_lang` = '.(int)$id_lang.'
                        GROUP BY f.`id_feature`
                        ORDER BY f.`position` ASC');
    }

    // add meta title, meta description, meta keywords
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['meta']['title'] = Configuration::get('PS_SHOP_NAME').' - '.$this->module->l('Products Comparison', 'compare');
        $page['meta']['keywords'] = $this->module->l('products-comparison', 'compare');
        $page['meta']['description'] = $this->module->l('Products Comparison', 'compare');
        $page['body_classes']['deo-compare-page'] = true;

        return $page;
    }

    // add breadcrumb
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Products Comparison', 'compare'),
            'url' => $this->context->link->getModuleLink('deotemplate','compare'),
        );

        return $breadcrumb;
    }

    // get layout
    public function getLayout()
    {
        $entity = 'module-deotemplate-'.$this->php_self;
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
