<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use Symfony\Component\Translation\TranslatorInterface;

// use Search;
// use Tools;
if (file_exists(_PS_MODULE_DIR_ . 'deotemplate/classes/AdvancedSearch/DeoAdvancedSearchModel.php')) {
    require_once(_PS_MODULE_DIR_ . 'deotemplate/classes/AdvancedSearch/DeoAdvancedSearchModel.php');
}

class DeoAdvancedSearchProvider implements ProductSearchProviderInterface
{

    private $translator;
    private $category;
    private $sortOrderFactory;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    public function runQuery(ProductSearchContext $context, ProductSearchQuery $query)
    {
        $products = array();
        $count = 0;

        if (($string = $query->getSearchString())) {
            // $class = new DeoAdvancedSearchModel;
            $queryString = Tools::replaceAccentedChars(urldecode($string));
            $result = DeoAdvancedSearchModel::find($context->getIdLang(), $queryString, $query->getPage(), $query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(), $query->getSortOrder()->toLegacyOrderWay(), false, false, null, $query->getIdCategory());
            $products = $result['result'];
            $count = $result['total'];

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,
                // deprecated since 1.7.x
                'expr' => $queryString,
            ));
        } elseif (($tag = $query->getSearchTag())) {
            // $class = new DeoAdvancedSearchModel;
            $queryString = urldecode($tag);

            $products = DeoAdvancedSearchModel::searchTag($context->getIdLang(), $queryString, false, $query->getPage(), $query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(true), $query->getSortOrder()->toLegacyOrderWay(), false, null);

            $count = DeoAdvancedSearchModel::searchTag($context->getIdLang(), $queryString, true, $query->getPage(), $query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(true), $query->getSortOrder()->toLegacyOrderWay(), false, null);

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,
                // deprecated since 1.7.x
                'expr' => $queryString,
            ));
        }

        $result = new ProductSearchResult();
        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                $this->sortOrderFactory->getDefaultSortOrders()
            );
        }

        return $result;
    }
}
