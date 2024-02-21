<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductExtraContentFinder;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class DeoFeatureProduct extends ProductControllerCore
{
    
    protected function assignPriceAndTax()
    {
        $id_customer = (isset($this->context->customer) ? (int) $this->context->customer->id : 0);
        $id_group = (int) Group::getCurrent()->id;
        $id_country = $id_customer ? (int) Customer::getCurrentCountry($id_customer) : (int) Tools::getCountry();

        // Tax
        $tax = (float) $this->product->getTaxesRate(new Address((int) $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $this->context->smarty->assign('tax_rate', $tax);

        $product_price_with_tax = Product::getPriceStatic($this->product->id, true, null, 6);
        if (Product::$_taxCalculationMethod == PS_TAX_INC) {
            $product_price_with_tax = Tools::ps_round($product_price_with_tax, 2);
        }

        $id_currency = (int) $this->context->cookie->id_currency;
        $id_product = (int) $this->product->id;
        $id_product_attribute = Tools::getValue('id_product_attribute', null);
        $id_shop = $this->context->shop->id;

        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute, false, (int) $this->context->customer->id);
        foreach ($quantity_discounts as &$quantity_discount) {
            if ($quantity_discount['id_product_attribute']) {
                $combination = new Combination((int) $quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int) $this->context->language->id);
                foreach ($attributes as $attribute) {
                    $quantity_discount['attributes'] = $attribute['name'].' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int) $quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
            }
        }

        $product_price = $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false);
        $this->quantity_discounts = $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float) $tax, $this->product->ecotax);

        $this->context->smarty->assign(array(
            'no_tax' => Tax::excludeTaxeOption() || !$tax,
            'tax_enabled' => Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'),
            'customer_group_without_tax' => Group::getPriceDisplayMethod($this->context->customer->id_default_group),
        ));
    }
    
    public function getTemplateVarProductExtend($id_product, $id_product_attribute = null)
    {
        if ($id_product) {
            $this->product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
        }

        if (!Validate::isLoadedObject($this->product)) {
            return false;
        }
        $productSettings = $this->getProductPresentationSettings();
        // Hook displayProductExtraContent
        $extraContentFinder = new ProductExtraContentFinder();

        $product = $this->objectPresenter->present($this->product);
        $product['id_product'] = (int) $this->product->id;
        $product['out_of_stock'] = (int) $this->product->out_of_stock;
        $product['new'] = (int) $this->product->new;
        $product['id_product_attribute'] = ($id_product_attribute == null) ? Product::getDefaultAttribute((int)$id_product) : (int) $id_product_attribute;
        $product['minimal_quantity'] = $this->getProductMinimalQuantity($product);
        $product['quantity_wanted'] = $this->getRequiredQuantity($product);
        $product['extraContent'] = $extraContentFinder->addParams(array('product' => $this->product))->present();

        $product_full = Product::getProductProperties($this->context->language->id, $product, $this->context);

        $product_full = $this->addProductCustomizationData($product_full);

        $product_full['show_quantities'] = (bool) (
            Configuration::get('PS_DISPLAY_QTIES')
            && Configuration::get('PS_STOCK_MANAGEMENT')
            && $this->product->quantity > 0
            && $this->product->available_for_order
            && !Configuration::isCatalogMode()
        );
        $product_full['quantity_label'] = ($this->product->quantity > 1) ? $this->trans('Items', array(), 'Shop.Theme.Catalog') : $this->trans('Item', array(), 'Shop.Theme.Catalog');
        $product_full['quantity_discounts'] = $this->quantity_discounts;

        if ($product_full['unit_price_ratio'] > 0) {
            $unitPrice = ($productSettings->include_taxes) ? $product_full['price'] : $product_full['price_tax_exc'];
            $product_full['unit_price'] = $unitPrice / $product_full['unit_price_ratio'];
        }

        $group_reduction = GroupReduction::getValueForProduct($this->product->id, (int) Group::getCurrent()->id);
        if ($group_reduction === false) {
            $group_reduction = Group::getReduction((int) $this->context->cookie->id_customer) / 100;
        }
        $product_full['customer_group_discount'] = $group_reduction;
        $presenter = $this->getProductPresenter();

        return $presenter->present(
            $productSettings,
            $product_full,
            $this->context->language
        );
    }

    public function getAtributeList($id_product, $id_product_attribute = null){
        $groups = array();
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;

        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
                    a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, pa.`id_product_attribute`,
                    IFNULL(stock.`quantity`, 0) as quantity, ag.`group_type`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Product::sqlStock('pa', 'pa').'
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_shop` ash ON (a.`id_attribute` = ash.`id_attribute` AND ash.`id_shop` = '.(int) $id_shop.')';

        $sql .= Shop::addSqlAssociation('attribute', 'a').'
                WHERE pa.`id_product` = ' . (int) $id_product.'
                    AND al.`id_lang` = ' . (int) $id_lang.'
                    AND agl.`id_lang` = ' . (int) $id_lang;
        
        if (isset($id_product_attribute)){
            $sql .= ' AND pac.`id_product_attribute` = '. (int) $id_product_attribute;
        }              

        $sql .= ' GROUP BY id_attribute_group, id_product_attribute
                  ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';

        if (isset($id_product_attribute)){
            $groups = Db::getInstance()->executeS($sql);
            
            return $groups;
        }  

        $attributes_groups = Db::getInstance()->executeS($sql);
        
        if (is_array($attributes_groups) && $attributes_groups){
            foreach ($attributes_groups as $k => $row){
                if (!isset($groups[$row['id_attribute_group']])){
                    $groups[$row['id_attribute_group']] = array(
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                    );
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];

                if ($row['group_type'] == 'color') {
                    $texture = '';
                    if (Tools::isEmpty($row['attribute_color']) && @filemtime(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')) {
                        $texture = $this->context->link->getMediaLink(_THEME_COL_DIR_.$row['id_attribute'].'.jpg');
                    }
                    $groups[$row['id_attribute_group']]['colors'][$row['id_attribute']] = array(
                        'type' => $texture ? 1 : 0,
                        'value' => $texture ? : $row['attribute_color'],
                    );
                }
            }
            
        }

        return $groups;
    }

    public function getCombinations($product){
        $deotemplate = new DeoTemplate();
        $id_product = $product['id_product'];
        $attributes = $deotemplate->getAttributesResume($id_product, $this->context->language->id);
        if (!empty($attributes)) {
            $whitelist = $deotemplate->getProductAttributeWhitelist();
            foreach ($attributes as $k_attributes => $v_attributes) {
                if ($v_attributes['id_product_attribute'] == $product['id_product_attribute']) {
                    $product['attribute_designation'] = $v_attributes['attribute_designation'];
                }
                foreach ($whitelist as $v_whitelist) {
                    if (isset($product[$v_whitelist])) {
                        $attributes[$k_attributes][$v_whitelist] = $product[$v_whitelist];
                    }
                }

                if ($deotemplate->shouldEnableAddToCartButton($attributes[$k_attributes])) {
                    $attributes[$k_attributes]['add_to_cart_url'] = $deotemplate->getAddToCartURL($attributes[$k_attributes]);
                } else {
                    $attributes[$k_attributes]['add_to_cart_url'] = null;
                }
            }
            $product['combinations'] = $attributes;
        }

        return $product;
    }
}
