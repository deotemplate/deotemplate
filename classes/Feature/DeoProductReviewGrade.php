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

class DeoProductReviewGrade extends ObjectModel
{
    public $id;

    public $id_deofeature_product_review;
    
    public $id_deofeature_product_review_criterion;
    
    public $grade;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deofeature_product_review_grade',
        'primary' => 'id_deofeature_product_review_grade',
        'fields' => array(
            'id_deofeature_product_review' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_deofeature_product_review_criterion' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'grade' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
        )
    );
}
