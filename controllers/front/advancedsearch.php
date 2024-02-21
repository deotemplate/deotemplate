<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

if (file_exists(_PS_MODULE_DIR_ . 'deotemplate/classes/AdvancedSearch/DeoAdvancedSearchProvider.php')) {
    require_once(_PS_MODULE_DIR_ . 'deotemplate/classes/AdvancedSearch/DeoAdvancedSearchProvider.php');
}


use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
// use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
// use PrestaShop\PrestaShop\Adapter\Search\SearchProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;

// use LeoSearch\DeoAdvancedSearchProvider;

class DeoTemplateAdvancedSearchModuleFrontController extends ModuleFrontController
{

    public $php_self;
    public $instant_search;
    public $ajax_search;
    private $search_string;
    private $search_tag;
    private $search_cate;

    /**
     * Initialize search controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        $this->instant_search = Tools::getValue('instantSearch');
        $this->ajax_search = Tools::getValue('ajax_search');
        $this->search_string = Tools::getValue('advanced_search_query');
        $this->search_tag = Tools::getValue('tag');
        $this->search_cate = Tools::getValue('cate_id');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->php_self = 'advancedsearch';
        parent::initContent();
        if (Tools::getValue('ajax_search')) {
            return;
        }

        $search_products = $this->getProducts();
        $this->context->smarty->assign(array(
            'search_products' => $search_products,
            'advanced_search_query' => $this->search_string,
            'nbProducts' => $search_products['pagination']['total_items'],
        ));
        $this->setTemplate('module:deotemplate/views/templates/front/advancedsearch/advancedsearch.tpl');
    }

    public function displayAjax()
    {
        if ($this->ajax_search && !$this->isTokenValid()) {
            # validate module
            $this->ajaxDie(json_encode(array('products' => array())));
        }
        
        if ($this->ajax || $this->ajax_search) {
            $this->ajaxDie(json_encode($this->getAjaxProductSearchVariables()));
        }
    }

    protected function getAjaxProductSearchVariables()
    {
        $search = $this->getProducts();

        if (!$this->ajax_search) {
            $rendered_products_top = $this->render('catalog/_partials/products-top', array('listing' => $search));
            $rendered_products = $this->render('catalog/_partials/products', array('listing' => $search));
            $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', array('listing' => $search));

            $data = array(
                'rendered_products_top' => $rendered_products_top,
                'rendered_products' => $rendered_products,
                'rendered_products_bottom' => $rendered_products_bottom,
            );
        }else{
            return $search;
        }

        // update 1.7.2.4
        // foreach ($search as $key => $value) {
        //     if ($key === 'products') {
        //         $value = $this->prepareProductArrayForAjaxReturn($value);
        //     }
        //     $data[$key] = $value;
        // }

        return $data;
    }

    /**
     * Cleans the products array with only whitelisted properties.
     *
     * @return array
     */
    protected function prepareProductArrayForAjaxReturn(array $products)
    {
        $allowed_properties = array('id_product', 'price', 'reference', 'active', 'description_short', 'link',
            'link_rewrite', 'name', 'manufacturer_name', 'position', 'url', 'canonical_url', 'add_to_cart_url',
            'has_discount', 'discount_type', 'discount_percentage', 'discount_percentage_absolute', 'discount_amount',
            'price_amount', 'regular_price_amount', 'regular_price', 'discount_to_display', 'labels', 'main_variants',
            'unit_price', 'tax_name', 'rate', 'cover',
        );
        foreach ($products as $product_key => $product) {
            foreach ($product as $product_property => $data) {
                # VALIDATE MODULE
                unset($data);
                if (!in_array($product_property, $allowed_properties)) {
                    unset($products[$product_key][$product_property]);
                }
            }
        }
        return $products;
    }

    protected function getProducts()
    {
        $searchProvider = new DeoAdvancedSearchProvider(
            $this->context->getTranslator()
        );

        $context = new ProductSearchContext($this->context);
        
        if ($this->ajax_search && Tools::getValue('limit')) {
            $products_per_page = Tools::getValue('limit');
        } else {
            $products_per_page = Configuration::get('PS_PRODUCTS_PER_PAGE');
        }

        // fix to display filter default Sort by
        if (version_compare(_PS_VERSION_, '1.7.3.0', '>')) {
            $sort_oder_by = 'position';
            $sort_oder_way = 'desc';
        } else {
            $sort_oder_by = Tools::getProductsOrder('by');
            $sort_oder_way = Tools::getProductsOrder('way');
        }

        $query = new ProductSearchQuery();
        $query
            ->setSortOrder(new SortOrder('product', $sort_oder_by, $sort_oder_way))
            ->setSearchString($this->search_string)
            ->setSearchTag($this->search_tag)
            ->setResultsPerPage($products_per_page)
            ->setPage(max((int) Tools::getValue('page'), 1));

        if ($this->search_cate && $this->search_cate != '') {
            $query->setIdCategory((int) $this->search_cate);
        }

        // set the sort order if provided in the URL
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::newFromString($encodedSortOrder));
        }

        $result = $searchProvider->runQuery($context, $query);

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());

        $products_for_template = array();

        foreach ($result->getProducts() as $rawProduct) {
            $product_temp = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
            # FIX 1.7.5.0
            if (is_object($product_temp) && method_exists($product_temp, 'jsonSerialize')) {
                $product_temp = $product_temp->jsonSerialize();
            }
            if ($this->ajax_search && !(int) Configuration::get('PS_CATALOG_MODE') && (int) Configuration::get('PS_STOCK_MANAGEMENT') && ! (int) Configuration::get('PS_ORDER_OUT_OF_STOCK')) {
                $product_temp['deo_label'] = ($product_temp['quantity_all_versions'] > 0) ? $this->module->l('In stock', 'advancedsearch') : $this->module->l('Out of stock', 'advancedsearch');
            }
            
            $products_for_template[] = $product_temp;
        }

        if ($this->ajax_search) {
            $searchVariables = array(
                'products' => $products_for_template,
            );
            return $searchVariables;
        }

        // render the facets
        if ($searchProvider instanceof FacetsRendererInterface) {
            // with the provider if it wants to
            $rendered_facets = $searchProvider->renderFacets($context, $result);
            $rendered_active_filters = $searchProvider->renderActiveFilters($context, $result);
        } else {
            // with the core
            $rendered_facets = $this->renderFacets($result);
            $rendered_active_filters = $this->renderActiveFilters($result);
        }

        $pagination = $this->getTemplateVarPagination($query, $result);
        $sort_orders = $this->getTemplateVarSortOrders(
            $result->getAvailableSortOrders(),
            $query->getSortOrder()->toString()
        );

        $sort_selected = false;
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if (isset($order['current']) && true === $order['current']) {
                    $sort_selected = $order['label'];
                    break;
                }
            }
        }
        // print_r($products_for_template);

        $searchVariables = array(
            'products' => $products_for_template,
            'pagination' => $pagination,
            'sort_orders' => $sort_orders,
            'sort_selected' => $sort_selected,
            'rendered_facets' => $rendered_facets,
            'rendered_active_filters' => $rendered_active_filters,
            'js_enabled' => $this->ajax,
            'current_url' => $this->updateQueryString(array('q' => $result->getEncodedFacets())),
        );

        Hook::exec('actionProductSearchComplete', $searchVariables);

        return $searchVariables;
    }

    protected function getTemplateVarPagination(ProductSearchQuery $query, ProductSearchResult $result)
    {
        $pagination = new Pagination();
        $pagination
                ->setPage($query->getPage())
                ->setPagesCount((int) ceil($result->getTotalProductsCount() / $query->getResultsPerPage()));

        $totalItems = $result->getTotalProductsCount();
        $itemsShownFrom = ($query->getResultsPerPage() * ($query->getPage() - 1)) + 1;
        $itemsShownTo = $query->getResultsPerPage() * $query->getPage();
        return array(
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            // 'pages' => array_map(function ($link) {
            //    $link['url'] = $this->updateQueryString(array(
            //        'page' => $link['page'],
            //    ));

            //    return $link;
            // }, $pagination->buildLinks()),
            'pages' => $this->deoGetLink($pagination),
            'should_be_displayed' => (count($pagination->buildLinks()) > 3)
        );
    }
    
    public function deoGetLink($pagination)
    {
    
        $links = $pagination->buildLinks();
        foreach ($links as &$link) {
            $link['url'] = $this->updateQueryString(array('page' => $link['page']));
        }

        return $links;
    }

    protected function renderFacets(ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(array($this, 'prepareFacetForTemplate'), $facetCollection->getFacets());

        $activeFilters = array();
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/facets', array(
            'facets' => $facetsVar,
            'js_enabled' => $this->ajax,
            'activeFilters' => $activeFilters,
            'sort_order' => $result->getCurrentSortOrder()->toString(),
            'clear_all_link' => $this->updateQueryString(array('q' => null, 'page' => null))
        ));
    }

    /**
     * Renders an array of active filters.
     *
     * @param array $facets
     *
     * @return string the HTML of the facets
     */
    protected function renderActiveFilters(ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(array($this, 'prepareFacetForTemplate'), $facetCollection->getFacets());

        $activeFilters = array();
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/active_filters', array(
            'activeFilters' => $activeFilters,
            'clear_all_link' => $this->updateQueryString(array('q' => null, 'page' => null))
        ));
    }

    // get layout
    public function getLayout()
    {
        $entity = 'module-deotemplate-' . $this->php_self;

        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);

        if ($overridden_layout = Hook::exec('overrideLayoutTemplate', array(
            'default_layout' => $layout,
            'entity' => $entity,
            'locale' => $this->context->language->locale,
            'controller' => $this))) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }

        return $layout;
    }

    protected function getTemplateVarSortOrders(array $sortOrders, $currentSortOrderURLParameter)
    {
        return array_map(function ($sortOrder) use ($currentSortOrderURLParameter) {
            $order = $sortOrder->toArray();
            $order['current'] = $order['urlParameter'] === $currentSortOrderURLParameter;
            $order['url'] = $this->updateQueryString(array(
                'order' => $order['urlParameter'],
                'page' => null,
            ));

            return $order;
        }, $sortOrders);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Search', 'advancedsearch'),
            'url' => '',
        );

        return $breadcrumb;
    }
    
    public function isTokenValid()
    {
        if (!Configuration::get('PS_TOKEN_ENABLE')) {
            return true;
        }

        return strcasecmp(Tools::getToken(false), Tools::getValue('token')) == 0;
    }
}
