<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoProductReview extends ObjectModel
{
    public $id;

    /** @var integer Product's id */
    public $id_product;

    /** @var integer Customer's id */
    public $id_customer;

    /** @var integer Guest's id */
    public $id_guest;

    /** @var integer Customer name */
    public $customer_name;

    /** @var string Title */
    public $title;

    /** @var string Content */
    public $content;

    /** @var integer Grade */
    public $grade;

    /** @var boolean Validate */
    public $validate = 0;

    public $deleted = 0;

    /** @var string Object creation date */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deofeature_product_review',
        'primary' => 'id_deofeature_product_review',
        'multishop' => true,
        'fields' => array(
            'id_product' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_guest' =>        array('type' => self::TYPE_INT),
            'customer_name' =>    array('type' => self::TYPE_STRING),
            'title' =>            array('type' => self::TYPE_STRING),
            'content' =>        array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 65535, 'required' => true),
            'grade' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'validate' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' =>        array('type' => self::TYPE_BOOL),
            'date_add' =>        array('type' => self::TYPE_DATE),
        )
    );

    /**
     * Get reviews by IdProduct
     *
     * @return array Reviews
     */
    public static function getByProduct($id_product, $p = 1, $n = null, $id_customer = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }
        $validate = (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MODERATE');
        $p = (int)$p;
        $n = (int)$n;
        if ($p <= 1) {
            $p = 1;
        }
        if ($n != null && $n <= 0) {
            $n = 5;
        }

        $cache_id = 'DeoProductReview::getByProduct_'.(int)$id_product.'-'.(int)$p.'-'.(int)$n.'-'.(int)$id_customer.'-'.(bool)$validate;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT pc.`id_deofeature_product_review`,
            (SELECT count(*) FROM `'._DB_PREFIX_.'deofeature_product_review_usefulness` pcu WHERE pcu.`id_deofeature_product_review` = pc.`id_deofeature_product_review` AND pcu.`usefulness` = 1) as total_useful,
            (SELECT count(*) FROM `'._DB_PREFIX_.'deofeature_product_review_usefulness` pcu WHERE pcu.`id_deofeature_product_review` = pc.`id_deofeature_product_review`) as total_advice, '.
            ((int)$id_customer ? '(SELECT count(*) FROM `'._DB_PREFIX_.'deofeature_product_review_usefulness` pcuc WHERE pcuc.`id_deofeature_product_review` = pc.`id_deofeature_product_review` AND pcuc.id_customer = '.(int)$id_customer.') as customer_advice, ' : '').
            ((int)$id_customer ? '(SELECT count(*) FROM `'._DB_PREFIX_.'deofeature_product_review_report` pcrc WHERE pcrc.`id_deofeature_product_review` = pc.`id_deofeature_product_review` AND pcrc.id_customer = '.(int)$id_customer.') as customer_report, ' : '').'
            if (c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) customer_name, pc.`content`, pc.`grade`, pc.`date_add`, pc.title
              FROM `'._DB_PREFIX_.'deofeature_product_review` pc
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = pc.`id_customer`
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
            WHERE pc.`id_product` = '.(int)($id_product).($validate == '1' ? ' AND pc.`validate` = 1' : '').' AND  prs.`id_shop` = '.Context::getContext()->shop->id.'
            ORDER BY pc.`date_add` DESC
            '.($n ? 'LIMIT '.(int)(($p - 1) * $n).', '.(int)($n) : ''));
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Return customer's review
     *
     * @return arrayReviews
     */
    public static function getByCustomer($id_product, $id_customer, $get_last = false, $id_guest = false)
    {
        $cache_id = 'DeoProductReview::getByCustomer_'.(int)$id_product.'-'.(int)$id_customer.'-'.(bool)$get_last.'-'.(int)$id_guest;
        if (!Cache::isStored($cache_id)) {
            $results = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'deofeature_product_review` pc
                LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
                WHERE pc.`id_product` = '.(int)$id_product.'
                AND '.(!$id_guest ? 'pc.`id_customer` = '.(int)$id_customer : 'pc.`id_guest` = '.(int)$id_guest).' AND prs.`id_shop` = '.Context::getContext()->shop->id.'
                ORDER BY pc.`date_add` DESC '
                .($get_last ? 'LIMIT 1' : ''));

            if ($get_last && count($results)) {
                $results = array_shift($results);
            }

            Cache::store($cache_id, $results);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get Grade By product
     *
     * @return array Grades
     */
    public static function getGradeByProduct($id_product, $id_lang)
    {
        if (!Validate::isUnsignedId($id_product) || !Validate::isUnsignedId($id_lang)) {
            return false;
        }
        $validate = (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MODERATE');

        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT pc.`id_deofeature_product_review`, pcg.`grade`, pccl.`name`, pcc.`id_deofeature_product_review_criterion`
            FROM `'._DB_PREFIX_.'deofeature_product_review` pc
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_grade` pcg ON (pcg.`id_deofeature_product_review` = pc.`id_deofeature_product_review`)
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_criterion` pcc ON (pcc.`id_deofeature_product_review_criterion` = pcg.`id_deofeature_product_review_criterion`)
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_criterion_lang` pccl ON (pccl.`id_deofeature_product_review_criterion` = pcg.`id_deofeature_product_review_criterion`)
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
            WHERE pc.`id_product` = '.(int)$id_product.'
            AND prs.`id_shop` = '.Context::getContext()->shop->id.' AND pccl.`id_lang` = '.(int)$id_lang.($validate == '1' ? ' AND pc.`validate` = 1' : '')));
    }

    public static function getRatings($id_product)
    {
        $validate = (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MODERATE');

        $sql = 'SELECT (SUM(pc.`grade`) / COUNT(pc.`grade`)) AS avg,
                MIN(pc.`grade`) AS min,
                MAX(pc.`grade`) AS max
            FROM `'._DB_PREFIX_.'deofeature_product_review` pc
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
            WHERE pc.`id_product` = '.(int)$id_product.'
            AND prs.`id_shop` = '.Context::getContext()->shop->id.' 
            AND pc.`deleted` = 0'. ($validate == '1' ? ' AND pc.`validate` = 1' : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getAverageGrade($id_product)
    {
        $validate = (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MODERATE');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT (SUM(pc.`grade`) / COUNT(pc.`grade`)) AS grade
        FROM `'._DB_PREFIX_.'deofeature_product_review` pc 
        LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
        WHERE pc.`id_product` = '.(int)$id_product.'
        AND prs.`id_shop` = '.Context::getContext()->shop->id.' 
        AND pc.`deleted` = 0'.
        ($validate == '1' ? ' AND pc.`validate` = 1' : ''));
    }

    public static function getAveragesByProduct($id_product, $id_lang)
    {
        /* Get all grades */
        $grades = DeoProductReview::getGradeByProduct((int)$id_product, (int)$id_lang);
        $total = DeoProductReview::getGradedReviewNumber((int)$id_product);
        if (!count($grades) || (!$total)) {
            return array();
        }

        /* Addition grades for each criterion */
        $criterionsGradeTotal = array();
        $count_grades = count($grades);
        for ($i = 0; $i < $count_grades; ++$i) {
            if (array_key_exists($grades[$i]['id_deofeature_product_review_criterion'], $criterionsGradeTotal) === false) {
                $criterionsGradeTotal[$grades[$i]['id_deofeature_product_review_criterion']] = (int)($grades[$i]['grade']);
            } else {
                $criterionsGradeTotal[$grades[$i]['id_deofeature_product_review_criterion']] += (int)($grades[$i]['grade']);
            }
        }

        /* Finally compute the averages */
        $averages = array();
        foreach ($criterionsGradeTotal as $key => $criterionGradeTotal) {
            $averages[(int)($key)] = (int)($total) ? ((int)($criterionGradeTotal) / (int)($total)) : 0;
        }
        return $averages;
    }

    /**
     * Return number of reviews and average grade by products
     *
     * @return array Info
     */
    public static function getReviewNumber($id_product)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }
        $validate = (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MODERATE');
        $cache_id = 'DeoProductReview::getReviewNumber_'.(int)$id_product.'-'.$validate;
        if (!Cache::isStored($cache_id)) {
            $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(pc.`id_deofeature_product_review`) AS "nbr"
            FROM `'._DB_PREFIX_.'deofeature_product_review` pc 
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
            WHERE `id_product` = '.(int)($id_product).' AND prs.`id_shop` = '.Context::getContext()->shop->id.($validate == '1' ? ' AND `validate` = 1' : ''));
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Return number of reviews and average grade by products
     *
     * @return array Info
     */
    public static function getGradedReviewNumber($id_product)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }
        $validate = (int) DeoHelper::getConfig('PRODUCT_REVIEWS_MODERATE');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT COUNT(pc.`id_product`) AS nbr
            FROM `'._DB_PREFIX_.'deofeature_product_review` pc
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
            WHERE `id_product` = '.(int)($id_product).' AND  prs.`id_shop` = '.Context::getContext()->shop->id.($validate == '1' ? ' AND `validate` = 1' : '').'
            AND `grade` > 0');
        return (int)($result['nbr']);
    }

    /**
     * Get reviews by Validation
     *
     * @return array Reviews
     */
    public static function getByValidate($validate = '0', $deleted = false)
    {
        $sql  = '
            SELECT pc.`id_deofeature_product_review`, pc.`id_product`, if (c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) customer_name, pc.`title`, pc.`content`, pc.`grade`, pc.`date_add`, pl.`name`
            FROM `'._DB_PREFIX_.'deofeature_product_review` pc
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = pc.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review`
            WHERE pc.`validate` = '.(int)$validate.' AND prs.`id_shop` = '.Context::getContext()->shop->id;

        $sql .= ' ORDER BY pc.`date_add` DESC';
        
        // validate module
        unset($deleted);

        return (Db::getInstance()->executeS($sql));
    }

    /**
     * Get all reviews
     *
     * @return array Reviews
     */
    public static function getAll()
    {
        return (Db::getInstance()->executeS('
            SELECT pc.`id_deofeature_product_review`, pc.`id_product`, if (c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) customer_name, pc.`content`, pc.`grade`, pc.`date_add`, pl.`name`
            FROM `'._DB_PREFIX_.'deofeature_product_review` pc
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = pc.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review` 
            WHERE pcs.`id_shop` = '.Context::getContext()->shop->id.' 
            ORDER BY pc.`date_add` DESC'));
    }

    /**
     * Validate a comment
     *
     * @return boolean succeed
     */
    public function validate($validate = '1')
    {
        if (!Validate::isUnsignedId($this->id)) {
            return false;
        }

        $success = (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'deofeature_product_review` SET
            `validate` = '.(int)$validate.'
            WHERE `id_deofeature_product_review` = '.(int)$this->id));

        Hook::exec('actionObjectDeoProductReviewValidateAfter', array('object' => $this));
        return $success;
    }

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = DeoHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_shop` (`id_shop`, `id_deofeature_product_review`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        return $res;
    }

    /**
     * Delete a comment, grade and report data
     *
     * @return boolean succeed
     */
    public function delete()
    {
        DeoProductReview::deleteGrades($this->id);
        DeoProductReview::deleteReports($this->id);
        DeoProductReview::deleteUsefulness($this->id);
        parent::delete();
    }

    /**
     * Delete Grades
     *
     * @return boolean succeed
     */
    public static function deleteGrades($id_deofeature_product_review)
    {
        if (!Validate::isUnsignedId($id_deofeature_product_review)) {
            return false;
        }
        return (Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'deofeature_product_review_grade`
            WHERE `id_deofeature_product_review` = '.(int)$id_deofeature_product_review));
    }

    /**
     * Delete Reports
     *
     * @return boolean succeed
     */
    public static function deleteReports($id_deofeature_product_review)
    {
        if (!Validate::isUnsignedId($id_deofeature_product_review)) {
            return false;
        }
        return (Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'deofeature_product_review_report`
            WHERE `id_deofeature_product_review` = '.(int)$id_deofeature_product_review));
    }

    /**
     * Delete usefulness
     *
     * @return boolean succeed
     */
    public static function deleteUsefulness($id_deofeature_product_review)
    {
        if (!Validate::isUnsignedId($id_deofeature_product_review)) {
            return false;
        }

        return (Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.'deofeature_product_review_usefulness`
        WHERE `id_deofeature_product_review` = '.(int)$id_deofeature_product_review));
    }

    /**
     * Report comment
     *
     * @return boolean
     */
    public static function reportReview($id_deofeature_product_review, $id_customer)
    {
        return (Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_report` (`id_deofeature_product_review`, `id_customer`)
            VALUES ('.(int)$id_deofeature_product_review.', '.(int)$id_customer.')'));
    }

    /**
     * Comment already report
     *
     * @return boolean
     */
    public static function isAlreadyReport($id_deofeature_product_review, $id_customer)
    {
        return (bool)Db::getInstance()->getValue('SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'deofeature_product_review_report`
            WHERE `id_customer` = '.(int)$id_customer.'
            AND `id_deofeature_product_review` = '.(int)$id_deofeature_product_review);
    }

    /**
     * Set comment usefulness
     *
     * @return boolean
     */
    public static function setReviewUsefulness($id_deofeature_product_review, $usefulness, $id_customer)
    {
        return (Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'deofeature_product_review_usefulness` (`id_deofeature_product_review`, `usefulness`, `id_customer`)
            VALUES ('.(int)$id_deofeature_product_review.', '.(int)$usefulness.', '.(int)$id_customer.')'));
    }

    /**
     * Usefulness already set
     *
     * @return boolean
     */
    public static function isAlreadyUsefulness($id_deofeature_product_review, $id_customer)
    {
        return (bool)Db::getInstance()->getValue('SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'deofeature_product_review_usefulness`
            WHERE `id_customer` = '.(int)$id_customer.'
            AND `id_deofeature_product_review` = '.(int)$id_deofeature_product_review);
    }

    /**
     * Get reported reviews
     *
     * @return array Reviews
     */
    public static function getReportedReviews()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT DISTINCT(pc.`id_deofeature_product_review`), pc.`id_product`, if (c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) customer_name, pc.`content`, pc.`grade`, pc.`date_add`, pl.`name`, pc.`title`
            FROM `'._DB_PREFIX_.'deofeature_product_review_report` pcr
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review` pc
                ON pcr.id_deofeature_product_review = pc.id_deofeature_product_review
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = pc.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = pc.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.' AND pl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'deofeature_product_review_shop` prs ON prs.`id_deofeature_product_review` = pc.`id_deofeature_product_review` 
            WHERE prs.`id_shop` = '.Context::getContext()->shop->id.' 
            ORDER BY pc.`date_add` DESC');
    }
}
