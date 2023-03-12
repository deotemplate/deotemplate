<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogCategory.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogComment.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogConfiguration.php');

class DeoTemplateBlogCategoryModuleFrontController extends ModuleFrontController
{
    public $php_self;
    protected $template_path = '';

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();

        // $this->template_path = _PS_MODULE_DIR_.'deotemplate/views/templates/front/';
        // $this->image_path = _PS_IMG_.'deotemplate/blog/'.$this->context->shop->id;
        // $this->blog_path = $this->image_path.'/blog';
        // $this->category_path = $this->image_path.'/blog-category';

        $this->blog_configuration = new DeoBlogConfiguration();


        if ($this->blog_configuration->get('BLOG_URL_USE_ID', 1)) {
            // URL HAVE ID
            $id_category = (int)Tools::getValue('id');
            $this->category = new DeoBlogCategory($id_category, $this->context->language->id);
        } else {
            // REMOVE ID FROM URL
            $url_rewrite = explode('/', $_SERVER['REQUEST_URI']) ;
            $url_last_item = count($url_rewrite) - 1;
            $url_rewrite = rtrim($url_rewrite[$url_last_item], 'html');
            $url_rewrite = rtrim($url_rewrite, '\.');    // result : product.html -> product.
            $this->category = DeoBlogCategory::findByRewrite(array('link_rewrite' => $url_rewrite));
        }

        $this->template = ($this->category->template == '') ? $this->blog_configuration->get('BLOG_DEFAULT_TEMPLATE') : $this->category->template;
        $this->template = (Tools::getValue('blog_style')) ? Tools::getValue('blog_style') : $this->template;

        $this->configures = $this->blog_configuration->configurations_template[$this->template];
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        /* Load Css and JS File */
        DeoBlogHelper::loadMedia($this->context, $this);

        $this->php_self = 'blogcategory';

        parent::initContent();

        $comment = false;
        if (DeoHelper::getConfig('BLOG_ITEM_COMMENT_ENGINE') == 'local') {
            $comment = DeoBlogComment::getComments(null, null, null, null, null, null, null);
        }

        $image = new DeoBlogImage();
        $helper = DeoBlogHelper::getInstance();

        $this->category = $helper->formatCategoryBlog($this->category);
        
        $limit = (int)$this->configures->item_per_page;
        $n = $limit;
        $p = abs((int)(Tools::getValue('p', 1)));
        
        $template = $this->template;

        if ($this->category->id_deoblog_category && $this->category->active) {
            $id_shop = $this->context->shop->id;

            $params = array(
                'rewrite' => $this->category->link_rewrite,
                'id' => $this->category->id_deoblog_category
            );

            $tag = trim(Tools::getValue('tag'));
            if ($tag) {
                $condition = array(
                    'type' => 'tag',
                    'tag' => urldecode($tag)
                );
                $r = $helper->getPaginationLink('module-deotemplate-blogcategory', 'blogcategory', $params, array('tag' => $tag));
                $blogs = DeoBlog::getListBlogs($this->category->id_deoblog_category, $this->context->language->id, $p, $limit, 'id_deoblog', 'DESC', $condition, true);
                $count = DeoBlog::countBlogs($this->category->id_deoblog_category, $this->context->language->id, $condition, true);
                $this->context->smarty->assign(array(
                    'filter' => $condition,
                ));
            }else{
                $blogs = DeoBlog::getListBlogs($this->category->id_deoblog_category, $this->context->language->id, $p, $limit, 'id_deoblog', 'DESC', array(), true);
                $count = DeoBlog::countBlogs($this->category->id_deoblog_category, $this->context->language->id, true);
            }

            foreach ($blogs as $key => &$blog) {
                $blog = $helper->formatBlog($blog, false, $comment);
            }

            $nb_blogs = $count;
            $range = 2; /* how many pages around page selected */
            if ($p > (($nb_blogs / $n) + 1)) {
                Tools::redirect(preg_replace('/[&?]p=\d+/', '', $_SERVER['REQUEST_URI']));
            }
            $pages_nb = ceil($nb_blogs / (int)($n));
            $start = (int)($p - $range);
            if ($start < 1) {
                $start = 1;
            }
            $stop = (int)($p + $range);
            if ($stop > $pages_nb) {
                $stop = (int)($pages_nb);
            }

            /* breadcrumb */
            if (!isset($r)) {
                $r = $helper->getPaginationLink('module-deotemplate-blogcategory', 'blogcategory', $params, false, true);
            }
            $all_cats = array();
            self::parentCategories($this->category, $all_cats);
            // print_r($this->configures);
            $this->context->smarty->assign(array(
                'image' => $image,
                'configures' => $this->configures,
                'range' => $range,
                'blogs' => $blogs,
                'category' => $this->category,
                'start' => $start,
                'stop' => $stop,
                'pages_nb' => $pages_nb,
                'nb_items' => $count,
                'p' => (int)$p,
                'n' => (int)$n,
                'requestPage' => $r['requestUrl'],
                'requestNb' => $r,
                'template' => $template,
            ));
        }else{
            $this->context->smarty->assign(array(
                'active' => '0',
                'category' => $this->category
            ));
        }
        
        $this->context->smarty->assign(array(
            'lazyload' => DeoHelper::getLazyload(),
        ));

        $this->setTemplate('module:deotemplate/views/templates/front/blog/'.$template.'/category.tpl');
    }

    public static function parentCategories($current, &$return)
    {
        if ($current->id_parent) {
            $obj = new DeoBlogCategory($current->id_parent, Context::getContext()->language->id);
            self::parentCategories($obj, $return);
        }
        $return[] = $current;
    }
    
    // add meta
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['meta']['title'] = Tools::ucfirst($this->category->title).' - '.Configuration::get('PS_SHOP_NAME');
        $page['meta']['keywords'] = $this->category->meta_keywords;
        $page['meta']['description'] = $this->category->meta_description;
        $page['body_classes']['deo-blog-category-page'] = true;
        
        return $page;
    }
    
    // add breadcrumb
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $helper = DeoBlogHelper::getInstance();
        $link = $helper->getFontBlogLink();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Blog', 'blogcategory'),
            'url' => $link,
        );

        $this->category_link = $helper->getBlogCatLink($this->category);
        $breadcrumb['links'][] = array(
            'title' => $this->category->title,
            'url' => $this->category_link,
        );

        $tag = trim(Tools::getValue('tag'));
        if ($tag) {
            $breadcrumb['links'][] = array(
                'title' => $tag,
                'url' => false,
            );
        }

        return $breadcrumb;
    }
    
    // get layout
    public function getLayout()
    {
        $entity = 'module-deotemplate-'.$this->php_self;
        
        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);
        
        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            array(
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
            )
        )) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }

        return $layout;
    }
}
