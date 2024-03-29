<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoCompareProduct extends ObjectModel
{
    public $id_compare;

    public $id_customer;

    public $date_add;

    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deofeature_compare',
        'primary' => 'id_compare',
        'fields' => array(
            'id_compare' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_customer' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
        ),
    );

    /**
     * Get all compare products of the customer
     * @param int $id_customer
     * @return array
     */
    public static function getDeoCompareProducts($id_compare)
    {
        $results = Db::getInstance()->executeS('
        SELECT DISTINCT `id_product`
        FROM `'._DB_PREFIX_.'deofeature_compare` c
        LEFT JOIN `'._DB_PREFIX_.'deofeature_compare_product` cp ON cp.`id_compare` = c.`id_compare` 
        WHERE cp.`id_compare` = '.(int)($id_compare));
        
        $compareProducts = array();

        if ($results) {
            foreach ($results as $result) {
                $compareProducts[] = (int)$result['id_product'];
            }
        }

        return $compareProducts;
    }


    /**
     * Add a compare product for the customer
     * @param int $id_customer, int $id_product
     * @return bool
     */
    public static function addDeoCompareProduct($id_compare, $id_product)
    {
        // Check if compare row exists
        $id_compare = Db::getInstance()->getValue('SELECT `id_compare`
            FROM `'._DB_PREFIX_.'deofeature_compare`
            WHERE `id_compare` = '.(int)$id_compare);

        if (!$id_compare) {
            $id_customer = false;
            if (Context::getContext()->customer) {
                $id_customer = Context::getContext()->customer->id;
            }
            $sql = Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'deofeature_compare` (`id_compare`, `id_customer`) VALUES (NULL, "'.($id_customer ? (int)$id_customer: '0').'")');
            if ($sql) {
                $id_compare = Db::getInstance()->getValue('SELECT MAX(`id_compare`) FROM `'._DB_PREFIX_.'deofeature_compare`');
                Context::getContext()->cookie->id_compare = $id_compare;
            }
        }

        return Db::getInstance()->execute('
            INSERT IGNORE INTO `'._DB_PREFIX_.'deofeature_compare_product` (`id_compare`, `id_product`, `date_add`, `date_upd`)
            VALUES ('.(int)($id_compare).', '.(int)($id_product).', NOW(), NOW())');
    }

    /**
     * Remove a compare product for the customer
     * @param int $id_compare
     * @param int $id_product
     * @return bool
     */
    public static function removeDeoCompareProduct($id_compare, $id_product)
    {
        return Db::getInstance()->execute('
            DELETE cp FROM `'._DB_PREFIX_.'deofeature_compare_product` cp, `'._DB_PREFIX_.'deofeature_compare` c
            WHERE cp.`id_compare`=c.`id_compare`
            AND cp.`id_product` = '.(int)$id_product.'
            AND c.`id_compare` = '.(int)$id_compare);
    }

    /**
     * Get the number of compare products of the customer
     * @param int $id_compare
     * @return int
     */
    public static function getNumberProducts($id_compare)
    {
        return (int)(Db::getInstance()->getValue('SELECT count(`id_compare`)
            FROM `'._DB_PREFIX_.'deofeature_compare_product`
            WHERE `id_compare` = '.(int)($id_compare)));
    }


    /**
     * Clean entries which are older than the period
     * @param string $period
     * @return void
     */
    public static function cleanDeoCompareProducts($period = null)
    {
        if ($period !== null) {
            Tools::displayParameterAsDeprecated('period');
        }

        Db::getInstance()->execute('
        DELETE cp, c FROM `'._DB_PREFIX_.'deofeature_compare_product` cp, `'._DB_PREFIX_.'deofeature_compare` c
        WHERE cp.date_upd < DATE_SUB(NOW(), INTERVAL 1 WEEK) AND c.`id_compare`=cp.`id_compare`');
    }

    /**
     * Get the id_compare by id_customer
     * @param int $id_customer
     * @return int $id_compare
     */
    public static function getIdCompareByIdCustomer($id_customer)
    {
        return (int)Db::getInstance()->getValue('SELECT `id_compare`
            FROM `'._DB_PREFIX_.'deofeature_compare`
            WHERE `id_customer`= '.(int)$id_customer);
    }
}
