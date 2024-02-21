<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

include_once(_PS_MODULE_DIR_. 'deotemplate/classes/Feature/DeoProductReviewCriterion.php');
include_once(_PS_MODULE_DIR_. 'deotemplate/classes/Feature/DeoProductReview.php');

class DeoTemplateReviewModuleFrontController extends ModuleFrontController
{
    /**
    * @see FrontController::initContent()
    */
   
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    public function displayAjax()
    {
        if (Tools::getValue('action') == 'get-new-review') {
            // $result = array();
            if ((int) DeoHelper::getConfig('PRODUCT_REVIEWS_MODERATE')) {
                $reviews = DeoProductReview::getByValidate(0, false);
            } else {
                $reviews = array();
            }

            die(json_encode(array(
                'number_review' => count($reviews)
            )));
        }

        if (!$this->isTokenValid() || !Tools::getValue('action')) {
            $result = '';
            die($result);
        };

        if (Tools::getValue('action') == 'render-modal-review') {
            $result = $this->renderModalReview(Tools::getValue('id_product'), Tools::getValue('is_logged'));
            die($result);
        };

        if (Tools::getValue('action') == 'add-new-review') {
            $array_result = array();
            $result = true;
            $id_guest = 0;
            $context = Context::getContext();

            $id_customer = $context->customer->id;
            if (!$id_customer) {
                $id_guest = $context->cookie->id_guest;
            }

            $id_deofeature_product_review = Tools::getValue('id_deofeature_product_review');
            $new_review_title = Tools::getValue('new_review_title');
            $new_review_content = Tools::getValue('new_review_content');
            $new_review_customer_name = Tools::getValue('new_review_customer_name');
            $criterion = Tools::getValue('criterion');
            $errors = array();
            // Validation
            if (!Validate::isInt($id_deofeature_product_review)) {
                $errors[] = $this->module->l('Product ID is incorrect', 'review');
            }
            if (!$new_review_title || !Validate::isGenericName($new_review_title)) {
                $errors[] = $this->module->l('Title is incorrect', 'review');
            }
            if (!$new_review_content || !Validate::isMessage($new_review_content)) {
                $errors[] = $this->module->l('Comment is incorrect', 'review');
            }
            if (!$id_customer && (!Tools::isSubmit('new_review_customer_name') || !$new_review_customer_name || !Validate::isGenericName($new_review_customer_name))) {
                $errors[] = $this->module->l('Customer name is incorrect', 'review');
            }
            if (!$context->customer->id && !(int) DeoHelper::getConfig('PRODUCT_REVIEWS_ALLOW_GUESTS')) {
                $errors[] = $this->module->l('You must be connected in order to send a review', 'review');
            }
            if (!count($criterion)) {
                $errors[] = $this->module->l('You must give a rating', 'review');
            }

            $product = new Product($id_deofeature_product_review);
            if (!$product->id) {
                $errors[] = $this->module->l('Product not found', 'review');
            }

            if (!count($errors)) {
                $customer_review = DeoProductReview::getByCustomer($id_deofeature_product_review, $id_customer, true, $id_guest);
                if (!$customer_review || ($customer_review && (strtotime($customer_review['date_add']) + DeoHelper::getConfig('PRODUCT_REVIEWS_MINIMAL_TIME')) < time())) {
                    $review = new DeoProductReview();
                    $review->content = strip_tags($new_review_content);
                    $review->id_product = (int) $id_deofeature_product_review;
                    $review->id_customer = (int) $id_customer;
                    $review->id_guest = $id_guest;
                    $review->customer_name = $new_review_customer_name;
                    if (!$review->customer_name) {
                        $review->customer_name = pSQL($context->customer->firstname . ' ' . $context->customer->lastname);
                    }
                    $review->title = $new_review_title;
                    $review->grade = 0;
                    $review->validate = 0;
                    $review->save();

                    $grade_sum = 0;
                    foreach ($criterion as $id_deofeature_product_review_criterion => $grade) {
                        $grade_sum += $grade;
                        $product_review_criterion = new DeoProductReviewCriterion($id_deofeature_product_review_criterion);
                        if ($product_review_criterion->id) {
                            $product_review_criterion->addGrade($review->id, $grade);
                        }
                    }

                    if (count($criterion) >= 1) {
                        $review->grade = $grade_sum / count($criterion);
                        // Update Grade average of comment
                        $review->save();
                    }
                    $result = true;
                    Tools::clearCache($context->smarty, $this->module->getTemplatePath('feature/deo_list_product_review.tpl'));
                } else {
                    $result = false;
                    $errors[] = $this->module->l('Please wait before posting another comment', 'review') . ' ' . DeoHelper::getConfig('PRODUCT_REVIEWS_MINIMAL_TIME') . ' ' . $this->module->l('seconds before posting a new review', 'review');
                }
            } else {
                $result = false;
            }

            $array_result['result'] = $result;
            $array_result['errors'] = $errors;
            if ($result) {
                $array_result['sucess_mess'] = $this->module->l('Your comment has been added. Thank you!', 'review');
            }
            die(json_encode($array_result));
        }

        if (Tools::getValue('action') == 'add-review-usefull') {
            $id_deofeature_product_review = Tools::getValue('id_deofeature_product_review');
            $is_usefull = Tools::getValue('is_usefull');

            if (DeoProductReview::isAlreadyUsefulness($id_deofeature_product_review, Context::getContext()->cookie->id_customer)) {
                die('0');
            }

            if (DeoProductReview::setReviewUsefulness((int) $id_deofeature_product_review, (bool) $is_usefull, Context::getContext()->cookie->id_customer)) {
                die('1');
            }

            die('0');
        }

        if (Tools::getValue('action') == 'add-review-report') {
            $id_deofeature_product_review = Tools::getValue('id_deofeature_product_review');

            if (DeoProductReview::isAlreadyReport($id_deofeature_product_review, Context::getContext()->cookie->id_customer)) {
                die('0');
            }

            if (DeoProductReview::reportReview((int) $id_deofeature_product_review, Context::getContext()->cookie->id_customer)) {
                die('1');
            }

            die('0');
        }
    }

    // render modal review popup
    public function renderModalReview($id_product, $is_logged)
    {
        $context = Context::getContext();
        $product = new Product((int) $id_product, false, $context->language->id, $context->shop->id);
        // echo '<pre>';
        // print_r($product);die();
        $image = Product::getCover((int) $id_product);
        $cover_image = $context->link->getImageLink($product->link_rewrite, $image['id_image'], ImageType::getFormattedName('medium'));
        // print_r($cover_image);die();
        $context->smarty->assign(array(
            'product_modal_review' => $product,
            'criterions' => DeoProductReviewCriterion::getByProduct((int) $id_product, $context->language->id),
            'productcomment_cover_image' => $cover_image,
            'allow_guests' => (int) DeoHelper::getConfig('PRODUCT_REVIEWS_ALLOW_GUESTS'),
            'is_logged' => (int) $is_logged,
        ));

        $output = $this->module->fetch('module:deotemplate/views/templates/front/feature/modal_review.tpl');

        return $output;
    }
}

