<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoBlogComment extends ObjectModel
{
    /** @var string Name */
    public $user;
    public $comment;
    public $active;
    public $id_deoblog;
    public $date_add;
    public $email;
    public $id_shop;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deoblog_comment',
        'primary' => 'id_deoblog_comment',
        'multishop' => true,
        'fields' => array(
            'id_deoblog' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'user' => array('type' => self::TYPE_STRING, 'required' => false),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128, 'required' => true),
            'comment' => array('type' => self::TYPE_STRING, 'required' => true),
            'active' => array('type' => self::TYPE_BOOL),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
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

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = DeoHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'deoblog_comment_shop` (`id_shop`, `id_deoblog_comment`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        return $res;
    }

    public static function countComments($id_deoblog = 0, $is_active = false)
    {
        $query = ' SELECT count(c.`id_deoblog_comment`) as total FROM '._DB_PREFIX_.'deoblog_comment c 
        LEFT JOIN `'._DB_PREFIX_.'deoblog_comment_shop` cs ON cs.`id_deoblog_comment` = c.`id_deoblog_comment`
        WHERE cs.id_shop='.(int) Context::getContext()->shop->id;

        if ($id_deoblog > 0) {
            # validate module
            $query .= ' AND id_deoblog='.(int)Context::getContext()->shop->id;
        }
        if ($is_active) {
            # validate module
            $query .= ' AND active=1 ';
        }

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        return $data[0]['total'];
    }

    public static function getComments($id_deoblog, $limit, $id_lang, $order = null, $by = null)
    {
        # validate module
        !is_null($limit) ? true : $limit = 10;
        unset($id_deoblog);
        unset($order);
        unset($by);
 
        $query = ' SELECT c.* FROM '._DB_PREFIX_.'deoblog_comment c';
        // $query .= ' LEFT JOIN '._DB_PREFIX_.'deoblog_lang b ON c.id_deoblog=b.id_deoblog AND b.id_lang='.(int)$id_lang;
        $query .= ' LEFT JOIN `'._DB_PREFIX_.'deoblog_comment_shop` cs ON cs.`id_deoblog_comment` = c.`id_deoblog_comment`';
        $query .= ' WHERE cs.id_shop='.(int) Context::getContext()->shop->id;
        $query .= ' LIMIT '.(int)$limit;

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $data;
    }

    public function getList($id_deoblog, $id_lang, $page_number = 0, $nb_products = 10, $order_by = null, $order_way = null)
    {
        if (empty($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        if ($page_number < 1) {
            $page_number = 1;
        }

        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_deoblog' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'c';
        } else if ($order_by == 'title') {
            $order_by_prefix = 'c';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if (Tools::strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        $query =  'SELECT c.* FROM '._DB_PREFIX_.'deoblog_comment c 
        LEFT JOIN `'._DB_PREFIX_.'deoblog_comment_shop` cs ON cs.`id_deoblog_comment` = c.`id_deoblog_comment` 
        WHERE cs.id_shop='.(int) Context::getContext()->shop->id.'
        AND active=1 AND id_deoblog='.(int)$id_deoblog;
        
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';      // $order_way Validate::isOrderWay()
        $query .= '  ORDER BY '.(isset($order_by_prefix) ? '`'.pSQL($order_by_prefix).'`.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way)
                .' LIMIT '.(int)(($page_number - 1) * $nb_products).', '.(int)$nb_products; # validate module

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $data;
    }
}
