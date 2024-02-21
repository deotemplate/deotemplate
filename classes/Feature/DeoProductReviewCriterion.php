<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

class DeoProductReviewCriterion extends ObjectModel
{
    public $id;
    public $id_deofeature_product_review_criterion_type;
    public $name;
    public $active = true;
    
    const MODULE_NAME = 'deotemplate';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deofeature_product_review_criterion',
        'primary' => 'id_deofeature_product_review_criterion',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'id_deofeature_product_review_criterion_type' =>    array('type' => self::TYPE_INT),
            'active' =>                                array('type' => self::TYPE_BOOL),
            // Lang fields
            'name' =>                                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        )
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
                INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_criterion_shop` (`id_shop`, `id_deofeature_product_review_criterion`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        return $res;
    }


    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }
        if ($this->id_deofeature_product_review_criterion_type == 2) {
            if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'deofeature_product_review_criterion_category WHERE id_deofeature_product_review_criterion='.(int)$this->id)) {
                return false;
            }
        } elseif ($this->id_deofeature_product_review_criterion_type == 3) {
            if (!Db::getInstance()->execute('
                    DELETE FROM '._DB_PREFIX_.'deofeature_product_review_criterion_product
                    WHERE id_deofeature_product_review_criterion='.(int)$this->id)) {
                return false;
            }
        }

        return Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'deofeature_product_review_grade`
            WHERE `id_deofeature_product_review_criterion` = '.(int)$this->id);
    }

    public function update($nullValues = false)
    {
        // print_r('kkk');die();
        $previousUpdate = new self((int)$this->id);
        if (!parent::update($nullValues)) {
            return false;
        }
        if ($previousUpdate->id_deofeature_product_review_criterion_type != $this->id_deofeature_product_review_criterion_type) {
            if ($previousUpdate->id_deofeature_product_review_criterion_type == 2) {
                return Db::getInstance()->execute('
                    DELETE FROM '._DB_PREFIX_.'deofeature_product_review_criterion_category
                    WHERE id_deofeature_product_review_criterion = '.(int)$previousUpdate->id);
            } elseif ($previousUpdate->id_deofeature_product_review_criterion_type == 3) {
                return Db::getInstance()->execute('
                    DELETE FROM '._DB_PREFIX_.'deofeature_product_review_criterion_product
                    WHERE id_deofeature_product_review_criterion = '.(int)$previousUpdate->id);
            }
        }
        return true;
    }

    /**
     * Link a review Criterion to a product
     *
     * @return boolean succeed
     */
    public function addProduct($id_product)
    {
        if (!Validate::isUnsignedId($id_product)) {
            die(Tools::displayError());
        }
        return Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_criterion_product` (`id_deofeature_product_review_criterion`, `id_product`)
            VALUES('.(int)$this->id.','.(int)$id_product.')
        ');
    }

    /**
     * Link a review Criterion to a category
     *
     * @return boolean succeed
     */
    public function addCategory($id_category)
    {
        if (!Validate::isUnsignedId($id_category)) {
            die(Tools::displayError());
        }
        return Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_criterion_category` (`id_deofeature_product_review_criterion`, `id_category`)
            VALUES('.(int)$this->id.','.(int)$id_category.')');
    }

    /**
     * Add grade to a criterion
     *
     * @return boolean succeed
     */
    public function addGrade($id_deofeature_product_review, $grade)
    {
        if (!Validate::isUnsignedId($id_deofeature_product_review)) {
            die(Tools::displayError());
        }
        if ($grade < 0) {
            $grade = 0;
        } elseif ($grade > 10) {
            $grade = 10;
        }
        return (Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_grade`
            (`id_deofeature_product_review`, `id_deofeature_product_review_criterion`, `grade`) VALUES(
            '.(int)($id_deofeature_product_review).',
            '.(int)$this->id.',
            '.(int)($grade).')'));
    }

    /**
     * Get criterion by Product
     *
     * @return array Criterion
     */
    public static function getByProduct($id_product, $id_lang)
    {
        if (!Validate::isUnsignedId($id_product) || !Validate::isUnsignedId($id_lang)) {
            die(Tools::displayError());
        }
        $alias = 'p';
        $table = '';
        // check if version > 1.5 to add shop association
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $table = '_shop';
            $alias = 'ps';
        }

        $cache_id = 'DeoProductReviewCriterion::getByProduct_'.(int)$id_product.'-'.(int)$id_lang;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
                SELECT pcc.`id_deofeature_product_review_criterion`, pccl.`name`
                FROM `'._DB_PREFIX_.'deofeature_product_review_criterion` pcc 
                LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_criterion_lang` pccl ON (pcc.id_deofeature_product_review_criterion = pccl.id_deofeature_product_review_criterion)
                LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_criterion_product` pccp ON (pcc.`id_deofeature_product_review_criterion` = pccp.`id_deofeature_product_review_criterion` AND pccp.`id_product` = '.(int)$id_product.')
                LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_criterion_category` pccc ON (pcc.`id_deofeature_product_review_criterion` = pccc.`id_deofeature_product_review_criterion`)
                LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_criterion_shop` prcs ON prcs.`id_deofeature_product_review_criterion` = pcc.`id_deofeature_product_review_criterion` 
                LEFT JOIN `'._DB_PREFIX_.'product'.$table.'` '.$alias.' ON ('.$alias.'.id_category_default = pccc.id_category AND '.$alias.'.id_product = '.(int)$id_product.')
                WHERE pccl.`id_lang` = '.(int)($id_lang).' 
                AND prcs.`id_shop` = '.Context::getContext()->shop->id.'
                AND (
                    pccp.id_product IS NOT NULL
                    OR ps.id_product IS NOT NULL
                    OR pcc.id_deofeature_product_review_criterion_type = 1
                )
                AND pcc.active = 1
                GROUP BY pcc.id_deofeature_product_review_criterion
            ');
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get Criterions
     *
     * @return array Criterions
     */
    public static function getCriterions($id_lang, $type = false, $active = false)
    {
        if (!Validate::isUnsignedId($id_lang)) {
            die(Tools::displayError());
        }
        
        $sql = '
            SELECT pcc.`id_deofeature_product_review_criterion`, pcc.id_deofeature_product_review_criterion_type, pccl.`name`, pcc.active
            FROM `'._DB_PREFIX_.'deofeature_product_review_criterion` pcc 
            JOIN `'._DB_PREFIX_.'deofeature_product_review_criterion_lang` pccl ON (pcc.id_deofeature_product_review_criterion = pccl.id_deofeature_product_review_criterion)
            LEFT JOIN `'._DB_PREFIX_.'product'.$table.'` '.$alias.' ON ('.$alias.'.id_category_default = pccc.id_category AND '.$alias.'.id_product = '.(int)$id_product.') 
            WHERE pccl.`id_lang` = '.(int)$id_lang.($active ? ' AND active = 1' : '').($type ? ' AND id_deofeature_product_review_criterion_type = '.(int)$type : '').' AND prcs.`id_shop` = '.Context::getContext()->shop->id.' 
            ORDER BY pccl.`name` ASC';
        $criterions = Db::getInstance()->executeS($sql);

        $types = self::getTypes();
        foreach ($criterions as $key => $data) {
            $criterions[$key]['type_name'] = $types[$data['id_deofeature_product_review_criterion_type']];
        }

        return $criterions;
    }

    public function getProducts()
    {
        $res = Db::getInstance()->executeS('
            SELECT pccp.id_product, pccp.id_deofeature_product_review_criterion
            FROM `'._DB_PREFIX_.'deofeature_product_review_criterion_product` pccp
            WHERE pccp.id_deofeature_product_review_criterion = '.(int)$this->id);
        $products = array();
        if ($res) {
            foreach ($res as $row) {
                $products[] = (int)$row['id_product'];
            }
        }
        return $products;
    }

    public function getCategories()
    {
        $res = Db::getInstance()->executeS('
            SELECT pccc.id_category, pccc.id_deofeature_product_review_criterion
            FROM `'._DB_PREFIX_.'deofeature_product_review_criterion_category` pccc
            WHERE pccc.id_deofeature_product_review_criterion = '.(int)$this->id);
        $criterions = array();
        if ($res) {
            foreach ($res as $row) {
                $criterions[] = (int)$row['id_category'];
            }
        }
        return $criterions;
    }

    public function deleteCategories()
    {
        return Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'deofeature_product_review_criterion_category`
            WHERE `id_deofeature_product_review_criterion` = '.(int)$this->id);
    }

    public function deleteProducts()
    {
        return Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'deofeature_product_review_criterion_product`
            WHERE `id_deofeature_product_review_criterion` = '.(int)$this->id);
    }
    
    /**
     * Get translation for a given module text
     *
     * Note: $specific parameter is mandatory for library files.
     * Otherwise, translation key will not match for Module library
     * when module is loaded with eval() Module::getModulesOnDisk()
     *
     * @param string $string String to translate
     * @param boolean|string $specific filename to use in translation key
     * @return string Translation
     */
    public static function l($string, $specific = false)
    {
        return Translate::getModuleTranslation(self::MODULE_NAME, $string, ($specific) ? $specific : self::MODULE_NAME);
    }
    
    public static function getTypes()
    {
        return array(
            1 => self::l('Valid for the entire catalog'),
            2 => self::l('Restricted to some categories'),
            3 => self::l('Restricted to some products')
        );
    }
}
