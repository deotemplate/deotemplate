<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class DeoAdvancedSearch extends DeoShortCodeBase
{
    public $name = 'DeoAdvancedSearch';

    public function getInfo()
    {
        return array(
            'label' => 'Search Advanced',
            'position' => 5,
            'desc' => $this->l('Search support category filter and result support show image and price.'),
            'image' => 'search.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }

    public function getConfigList()
    {
        $inputs_head = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'lang' => 'true',
                'default' => ''
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
            ),
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
        );

        $inputs_content = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Ajax Search'),
                'name' => 'ajaxsearch',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => '',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number product display with AJAX Search'),
                'name' => 'limitajaxsearch',
                'desc' => $this->l('Default is 100'),
                'default' => '100',
                'form_group_class' => '',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Search By Category'),
                'name' => 'searchcategory',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => '',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Maximum Depth Of Category'),
                'name' => 'depthcategory',
                'desc' => $this->l('Set the maximum depth of category sublevels displayed in this block (0 = infinite).'),
                'default' => '0',
                'form_group_class' => '',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Product Image'),
                'name' => 'showimage',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => '',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Product Price'),
                'name' => 'showprice',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => '',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Label Stock'),
                'name' => 'showstock',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Show label In Stock or Out of Stock if you Enable stock management and disable Allow ordering of out-of-stock.'),
                'form_group_class' => '',
            ),
        );

        $inputs = array_merge($inputs_head, $inputs_content);
        
        return $inputs;
    }

    private function getCategories($category,$maxdepth)
    {
        $range = '';
        if (Validate::isLoadedObject($category)) {
            // if ($maxdepth > 0) {
            // $maxdepth += $category->level_depth;
            // }
            $range = 'AND nleft >= ' . (int) $category->nleft . ' AND nright <= ' . (int) $category->nright;
        }

        $resultIds = array();
        $resultParents = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                        SELECT c.id_parent, c.id_category, c.level_depth, cl.name, cl.link_rewrite
                        FROM `' . _DB_PREFIX_ . 'category` c
                        INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = ' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('cl') . ')
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                        WHERE (c.`active` = 1 OR c.`id_category` = ' . (int) Configuration::get('PS_HOME_CATEGORY') . ')
                        AND c.`id_category` != ' . (int) Configuration::get('PS_ROOT_CATEGORY') . '
                        ' . ((int) $maxdepth != 0 ? ' AND `level_depth` <= ' . (int) $maxdepth : '') . '
                        ' . $range . '
                        AND c.id_category IN (
                                SELECT id_category
                                FROM `' . _DB_PREFIX_ . 'category_group`
                                WHERE `id_group` IN (' . pSQL(implode(', ', Customer::getGroupsStatic((int) Context::getContext()->customer->id))) . ')
                        )
                        ORDER BY `level_depth` ASC, ' . (Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`') . ' ' . (Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC'));
        foreach ($result as &$row) {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }

        return $this->getTree($resultParents, $resultIds, $maxdepth, ($category ? $category->id : null));
    }

    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
    {
        if (is_null($id_category)) {
            $id_category = Context::getContext()->shop->getCategory();
        }

        $children = array();

        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth)) {
            foreach ($resultParents[$id_category] as $subcat) {
                $children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
            }
        }

        if (isset($resultIds[$id_category])) {
            $link = Context::getContext()->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']);
            $name = $resultIds[$id_category]['name'];
            // $desc = $resultIds[$id_category]['description'];
            $level_depth = $resultIds[$id_category]['level_depth'];
        } else {
            // $link = $name = $desc = '';
            $link = $name = '';
        }

        return array(
            'id_category' => $id_category,
            'level_depth' => $level_depth,
            'link' => $link,
            'name' => $name,
            // 'desc'=> $desc,
            'children' => $children
        );
    }

    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        $category = new Category((int) Category::getRootCategory()->id, Context::getContext()->language->id);
        $depthcategory = (isset($assign['formAtts']['depthcategory']) && $assign['formAtts']['depthcategory']) ? $assign['formAtts']['depthcategory'] : 0;
        $assign['formAtts']['cates'] = $this->getCategories($category, $depthcategory);
        $assign['formAtts']['token'] = Tools::getToken(false);
        $assign['formAtts']['advanced_search_url'] = Context::getContext()->link->getModuleLink('deotemplate', 'advancedsearch', array(), Tools::usingSecureMode());

        if (Tools::getValue('advanced_search_query')) {
            $assign['formAtts']['advanced_search_query'] = (string) Tools::getValue('advanced_search_query');
            if (Tools::getValue('cate_id') && Tools::getValue('cate_id') != '' && $category_obj = new Category(Tools::getValue('cate_id'), Context::getContext()->language->id)) {
                $assign['formAtts']['selectedCate'] = (string) Tools::getValue('cate_id');
                $assign['formAtts']['selectedCateName'] = $category_obj->name;
            }
        }

        return $assign;
    }
}
