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

class DeoBlog extends ObjectModel
{
    /** @var string Name */
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $content;
    public $description;
    public $image;
    public $image_link;
    public $use_image_link;
    public $rate_image;
    public $link_rewrite;
    public $id_deoblog_category;
    public $position;
    public $active;
    public $id_deoblog;
    public $date_add;
    public $date_upd;
    public $views = 0;
    public $id_employee;
    /**
     * @see ObjectModel::$definition
     */
    // add author name
    public $author_name;
    
    public static $definition = array(
        'table' => 'deoblog',
        'primary' => 'id_deoblog',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'id_deoblog_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'use_image_link' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'image_link' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'rate_image' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 25),
            'position' => array('type' => self::TYPE_INT),
            'id_employee' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL),
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'views' => array('type' => self::TYPE_INT, 'size' => 11),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'author_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            # Lang fields
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128, 'required' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'size' => 128),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
        ),
    );
    protected $webserviceParameters = array(
        'objectNodeName' => 'content',
        'objectsNodeName' => 'content_management_system',
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
        $this->loadDataShop();
    }

    public function loadDataShop()
    {
        if ($this->def['multishop'] == true) {
            $sql = 'SELECT * FROM ' ._DB_PREFIX_.$this->def['table'] . '_shop WHERE ' .$this->def['primary'] . ' =' .(int)$this->id;
            $this->data_shop = Db::getInstance()->getRow($sql);
            
            if (isset($this->data_shop['active'])) {
                $this->active = $this->data_shop['active'];
            }
        }
    }

    public static function findByRewrite($parrams)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $id = 0;
        if (isset($parrams['link_rewrite']) && $parrams['link_rewrite']) {
            $sql = 'SELECT bl.id_deoblog FROM '._DB_PREFIX_.'deoblog_lang bl';
            $sql .= ' INNER JOIN '._DB_PREFIX_.'deoblog_shop bs on bl.id_deoblog=bs.id_deoblog AND id_shop='.$id_shop;
            //$sql .= ' WHERE id_lang = ' . $id_lang ." AND link_rewrite = '".$parrams['link_rewrite']."'";
            $sql .= " AND link_rewrite = '".pSQL($parrams['link_rewrite'])."'";
            if ($row = Db::getInstance()->getRow($sql)) {
                $id = $row['id_deoblog'];
            }
        }
        return new DeoBlog($id, $id_lang);
    }
    public function add($autodate = true, $null_values = false)
    {
        $this->position = self::getLastPosition((int)$this->id_deoblog_category);

        $id_shop = DeoHelper::getIDShop();
        $res = parent::add($autodate, $null_values);

        $sql = 'INSERT INTO `'._DB_PREFIX_.'deoblog_shop` (`id_shop`, `id_deoblog`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')';
        $res &= Db::getInstance()->execute($sql);
        $this->cleanPositions($this->id_deoblog_category);
        return $res;
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            return $this->cleanPositions($this->id_deoblog_category);
        }
        return false;
    }

    public function updateField($id, $fields)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'deoblog` SET ';
        $last_key = current(array_keys($fields));
        foreach ($fields as $field => $value) {
            $sql .= "`".bqSql($field)."` = '".pSQL($value)."'";
            if ($field != $last_key) {
                # validate module
                $sql .= ',';
            }
        }

        $sql .= ' WHERE `id_deoblog`='.(int)$id;
        return Db::getInstance()->execute($sql);
    }

    public function delete()
    {
        if (parent::delete()) {
            # BLOG_SHOP
            $sql = 'DELETE FROM `'._DB_PREFIX_.'deoblog_shop` '
                    .'WHERE `id_deoblog` IN ('.(int)$this->id.')';
            Db::getInstance()->execute($sql);
            
            //delete comment
            $result_comment = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_deoblog_comment` as id FROM `'._DB_PREFIX_.'deoblog_comment` WHERE `id_deoblog` = '.(int)$this->id.'');
            foreach ($result_comment as $value) {
                $comment = new DeoBlogComment($value['id']);
                $comment->delete();
            }

            $id_shop = (int)Context::getContext()->shop->id;
            require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
            DeoBlogImage::removeGenerateImageBlog($this->id, $id_shop);
        
            return $this->cleanPositions($this->id_deoblog_category);
        }
        return false;
    }

    /**
     * @param Array $condition ( default array )
     * @param Boolean $is_active ( default false )
     */
    public static function getListBlogs($id_category, $id_lang, $page_number, $nb_products, $order_by, $order_way, $condition = array(), $is_active = false, $id_shop = null)
    {
        # module validation
        if (!$id_shop) {
            $id_shop = DeoHelper::getIDShop();
        }

        if (empty($id_lang)) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if ($page_number < 1 && !is_null($page_number)) {
            $page_number = 1;
        }
        if ($nb_products < 1 && !is_null($nb_products)) {
            $nb_products = 10;
        }


        $where = '';

        if ($id_category) {
            # validate module
            $where .= ' AND b.id_deoblog_category='.(int)$id_category;
        }
        if ($id_shop) {
            # validate module
            $where .= ' AND s.id_shop='.(int)$id_shop;
        }
        
        if (isset($condition['type'])) {
            switch ($condition['type']) {
                case 'author':
                    if (isset($condition['id_employee'])) {
                        $where .= ' AND id_employee='.(int)$condition['id_employee'].' AND (author_name = "" OR author_name is null)';
                    } else {
                        $where .= ' AND author_name LIKE "%'.pSQL($condition['author_name']).'%"';
                    }
                    break;

                case 'tag':
                    $tmp = explode(',', $condition['tag']);

                    if (!empty($tmp) && count($tmp) > 1) {
                        $t = array();
                        foreach ($tmp as $tag) {
                            # validate module
                            $t[] = 'l.meta_keywords LIKE "%'.pSQL(trim($tag)).'%"';
                        }
                        $where .= ' AND ('.implode(' OR ', $t).') ';
                    } else {
                        # validate module
                        $where .= ' AND l.meta_keywords LIKE "%'.pSQL($condition['tag']).'%"';
                    }
                    break;
                case 'samecat':
                    $where .= ' AND b.id_deoblog!='.(int)$condition['id_deoblog'];
                    break;
            }
        }

        if ($is_active) {
            # validate module
            $where .= ' AND b.active=1';
        }
        $query = '
        SELECT b.`id_deoblog`, b.`id_deoblog_category`, b.`rate_image`, b.`image_link`, b.`use_image_link`, b.`image`, b.`id_employee`, b.`author_name`, b.`date_add`, b.`views`, l.`link_rewrite`, l.`meta_keywords`, l.`description`, l.`meta_title` as title, blc.`link_rewrite` as category_link_rewrite , blc.`title` as category_title
        FROM  '._DB_PREFIX_.'deoblog b
        LEFT JOIN '._DB_PREFIX_.'deoblog_lang l ON (b.id_deoblog = l.id_deoblog) and  l.id_lang='.(int)$id_lang.' 
        LEFT JOIN '._DB_PREFIX_.'deoblog_shop s ON  (b.id_deoblog = s.id_deoblog) and s.id_shop='.(int)$id_shop.' 
        LEFT JOIN '._DB_PREFIX_.'deoblog_category bc ON  bc.id_deoblog_category = b.id_deoblog_category '.' 
        LEFT JOIN '._DB_PREFIX_.'deoblog_category_lang blc ON blc.id_deoblog_category=bc.id_deoblog_category and blc.id_lang='.(int)$id_lang
                .' '.Shop::addSqlAssociation('blog', 'b').'
        WHERE l.id_lang = '.(int)$id_lang.$where.'
         ';

        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_deoblog' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'b';
        } else if ($order_by == 'title') {
            $order_by_prefix = 'b';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if (Tools::strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';      // $order_way Validate::isOrderWay()
        $order = 'ORDER BY '.(isset($order_by_prefix) ? '`'.pSQL($order_by_prefix).'`.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way);
        $query .= (isset($order)) ? $order : '';

        if (!is_null($nb_products) && !is_null($page_number)){
            $query .= ' LIMIT '.(int)(($page_number - 1) * $nb_products).', '.(int)$nb_products;
        }
                
        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $data;
    }

    /**
     * @param Array $condition ( default array )
     * @param Boolean $is_active ( default false )
     */
    public static function countBlogs($id_category, $id_lang, $condition = array(), $is_active = false, $id_shop = null)
    {
        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }
        if (empty($id_lang)) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $where = '';
        if ($id_category) {
            # validate module
            $where .= ' AND b.id_deoblog_category='.(int)$id_category;
        }
        if ($is_active) {
            # validate module
            $where .= ' AND b.active=1';
        }
        if ($id_shop) {
            # validate module
            $where .= ' AND s.id_shop='.(int)$id_shop;
        }
        if (isset($condition['type'])) {
            switch ($condition['type']) {
                case 'author':
                    if (isset($condition['id_employee'])) {
                        $where .= ' AND id_employee='.(int)$condition['id_employee'].' AND (author_name = "" OR author_name is null)';
                    } else {
                        $where .= ' AND author_name LIKE "%'.pSQL($condition['author_name']).'%"';
                    }
                    break;

                case 'tag':
                    $tmp = explode(',', $condition['tag']);

                    if (!empty($tmp) && count($tmp) > 1) {
                        $t = array();
                        foreach ($tmp as $tag) {
                            # validate module
                            $t[] = 'l.meta_keywords LIKE "%'.pSQL(trim($tag)).'%"';
                        }
                        $where .= ' AND  '.implode(' OR ', $t).' ';
                    } else {
                        # validate module
                        $where .= ' AND l.meta_keywords LIKE "%'.pSQL($condition['tag']).'%"';
                    }
                    break;
                case 'samecat':
                    $where .= ' AND b.id_deoblog!='.(int)$condition['id_deoblog'];
                    break;
            }
        }
        $query = '
        SELECT  b.id_deoblog
        FROM  '._DB_PREFIX_.'deoblog b
        LEFT JOIN '._DB_PREFIX_.'deoblog_lang l ON (b.id_deoblog = l.id_deoblog) and  l.id_lang='.(int)$id_lang
                .' LEFT JOIN '._DB_PREFIX_.'deoblog_shop s ON  (b.id_deoblog = s.id_deoblog) '
                .' LEFT JOIN '._DB_PREFIX_.'deoblog_category bc ON  bc.id_deoblog_category = b.id_deoblog_category '
                .' LEFT JOIN '._DB_PREFIX_.'deoblog_category_lang blc ON blc.id_deoblog_category=bc.id_deoblog_category and blc.id_lang='.(int)$id_lang
                .'
        WHERE l.id_lang = '.(int)$id_lang.$where.'
        GROUP BY b.id_deoblog
         ';

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return count($data);
    }

    public static function listblog($id_lang = null, $id_block = false, $active = true, $id_shop = null)
    {
        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }

        if (empty($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT c.id_deoblog, l.meta_title
        FROM  '._DB_PREFIX_.'blog c
        JOIN '._DB_PREFIX_.'blog_lang l ON (c.id_deoblog = l.id_deoblog)
                JOIN '._DB_PREFIX_.'deoblog_lang s ON (c.id_deoblog = s.id_deoblog)
        '.Shop::addSqlAssociation('blog', 'c').'
        '.(($id_block) ? 'JOIN '._DB_PREFIX_.'block_blog b ON (c.id_deoblog = b.id_deoblog)' : '').'
        WHERE s.id_shop = '.(int)$id_shop.' AND l.id_lang = '.(int)$id_lang.(($id_block) ? ' AND b.id_block = '.(int)$id_block : '').($active ? ' AND c.`active` = 1 ' : '').'
        GROUP BY c.id_deoblog
        ORDER BY c.`position`');
    }

    public function updatePosition($way, $position)
    {
        $sql = 'SELECT cp.`id_deoblog`, cp.`position`, cp.`id_deoblog_category`
            FROM `'._DB_PREFIX_.'blog` cp
            WHERE cp.`id_deoblog_category` = '.(int)$this->id_deoblog_category.'
            ORDER BY cp.`position` ASC';
        if (!$res = Db::getInstance()->executeS($sql)) {
            return false;
        }

        foreach ($res as $blog) {
            if ((int)$blog['id_deoblog'] == (int)$this->id) {
                $moved_blog = $blog;
            }
        }

        if (!isset($moved_blog) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'blog`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way ? '> '.(int)$moved_blog['position'].' AND `position` <= '.(int)$position : '< '.(int)$moved_blog['position'].' AND `position` >= '.(int)$position).'
            AND `id_deoblog_category`='.(int)$moved_blog['id_deoblog_category']) && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'blog`
            SET `position` = '.(int)$position.'
            WHERE `id_deoblog` = '.(int)$moved_blog['id_deoblog'].'
            AND `id_deoblog_category`='.(int)$moved_blog['id_deoblog_category']));
    }

    public static function cleanPositions($id_category)
    {
        $sql = '
        SELECT `id_deoblog`
        FROM `'._DB_PREFIX_.'deoblog`
        WHERE `id_deoblog_category` = '.(int)$id_category.'
        ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `'._DB_PREFIX_.'deoblog`
                    SET `position` = '.(int)$i.'
                    WHERE `id_deoblog_category` = '.(int)$id_category.'
                        AND `id_deoblog` = '.(int)$result[$i]['id_deoblog'];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getLastPosition($id_category)
    {
        $sql = '
        SELECT MAX(position) + 1
        FROM `'._DB_PREFIX_.'deoblog`
        WHERE `id_deoblog_category` = '.(int)$id_category;

        return (Db::getInstance()->getValue($sql));
    }

    public static function getblogPages($id_lang = null, $id_deoblog_category = null, $active = true, $id_shop = null)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('blog', 'c');
        if ($id_lang) {
            $sql->innerJoin('blog_lang', 'l', 'c.id_deoblog = l.id_deoblog AND l.id_lang = '.(int)$id_lang);
        }

        if ($id_shop) {
            $sql->innerJoin('blog_shop', 'cs', 'c.id_deoblog = cs.id_deoblog AND cs.id_shop = '.(int)$id_shop);
        }

        if ($active) {
            $sql->where('c.active = 1');
        }

        if ($id_deoblog_category) {
            $sql->where('c.id_deoblog_category = '.(int)$id_deoblog_category);
        }

        $sql->orderBy('position');

        return Db::getInstance()->executeS($sql);
    }

    public static function getUrlRewriteInformations($id_deoblog)
    {
        $sql = 'SELECT l.`id_lang`, c.`link_rewrite`
                FROM `'._DB_PREFIX_.'deoblog_lang` AS c
                LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
                WHERE c.`id_deoblog` = '.(int)$id_deoblog.'
                AND l.`active` = 1';

        return Db::getInstance()->executeS($sql);
    }
}
