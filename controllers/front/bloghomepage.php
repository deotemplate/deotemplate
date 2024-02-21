<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogCategory.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogComment.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogConfiguration.php');

class DeoTemplateBloghomepageModuleFrontController extends ModuleFrontController
{
    public $php_self;
    protected $template_path = '';

    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();

        $this->blog_configuration = new DeoBlogConfiguration();
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->php_self = 'bloghomepage';

        $authors = array();

        $image = new DeoBlogImage();
        // $image->getImageBlogBySize($url,$size_name);

        /* Load Css and JS File */
        DeoBlogHelper::loadMedia($this->context, $this);

        parent::initContent();

        $helper = DeoBlogHelper::getInstance();
        $template = (Tools::getValue('blog_style')) ? Tools::getValue('blog_style') : $this->blog_configuration->get('BLOG_DEFAULT_TEMPLATE');
        $configures = $this->blog_configuration->configurations_template[$template];

        $comment = false;
        if (DeoHelper::getConfig('BLOG_ITEM_COMMENT_ENGINE') == 'local') {
            $comment = DeoBlogComment::getComments(null, null, null, null, null, null, null);
        }

        // print_r($comment);
        
        $author = Tools::getValue('author');
        $tag = trim(Tools::getValue('tag'));

        if ($author || $tag){
            $condition = array();
            if ($author) {
                $employee_obj = new Employee($author);
                if (isset($employee_obj) && $employee_obj->id != '') {
                    $condition = array(
                        'type' => 'author',
                        'id_employee' => $author,
                        'employee' => new Employee($author)
                    );
                }else{
                    $condition = array(
                        'type' => 'author',
                        'author_name' => $author,
                    );
                }
                $r = $helper->getPaginationLink('module-deotemplate-bloghomepage', 'bloghomepage', array('author' => $author));
            }

            if ($tag) {
                $condition = array(
                    'type' => 'tag',
                    'tag' => urldecode($tag)
                );
                $r = $helper->getPaginationLink('module-deotemplate-bloghomepage', 'bloghomepage', array('tag' => $tag));
            }

            $p = abs((int)(Tools::getValue('p', 1)));
            $n = (int)$configures->item_per_page;
            $blogs = DeoBlog::getListBlogs(null, $this->context->language->id, $p, $n, 'id_deoblog', 'DESC', $condition, true);
            $count = DeoBlog::countBlogs(null, $this->context->language->id, $condition, true);

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

            if (!isset($r)) {
                $r = $helper->getPaginationLink('module-deotemplate-bloghomepage', 'bloghomepage', array(), false, true);
            }

            foreach ($blogs as &$blog) {
                $blog = $helper->formatBlog($blog, false, $comment);
            }

            $this->context->smarty->assign(array(
                'image' => $image,
                'blogs' => $blogs,
                'template' => $template,
                'configures' => $configures,
                'filter' => $condition,
                'nb_items' => $count,
                'range' => $range,
                'start' => $start,
                'stop' => $stop,
                'pages_nb' => $pages_nb,
                'p' => (int)$p,
                'n' => (int)$n,
                'requestPage' => $r['requestUrl'],
                'requestNb' => $r,
            ));
        }else{
            $category = new DeoBlogCategory();
            $categories = $category->getAllChild();

            $blogs = DeoBlog::getListBlogs(null, null, null, null, null, null, null, true);
            $item_per_category = $configures->item_per_category;

            $blog_in_category = array();
            foreach ($categories as $key_category => &$category) {
                if ($category['is_root'] == 1){
                    unset($categories[$key_category]);
                    continue;
                }

                $category['blogs'] = array();
                $temp_count = 0; 
                foreach ($blogs as $key_blog => $blog) {
                    if ($temp_count >= $item_per_category){
                        break;
                    }

                    $blog = $helper->formatBlog($blog, false, $comment);
                    if ($blog['id_deoblog_category'] == $category['id_category']){
                        $category['blogs'][] = $blog;
                        unset($blogs[$key_blog]);
                        $temp_count += 1;
                    }
                }
                $category = $helper->formatCategoryBlog($category);
            }

            $this->context->smarty->assign(array(
                'image' => $image,
                'categories' => $categories,
                'template' => $template,
                'configures' => $configures,
            ));
        }

        $this->context->smarty->assign(array(
            'lazyload' => DeoHelper::getLazyload(),
        ));
        
        $this->setTemplate('module:deotemplate/views/templates/front/blog/bloghomepage.tpl');
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['meta']['title'] = $this->module->l('Blog', 'bloghomepage').' - '.Configuration::get('PS_SHOP_NAME');
        // $page['meta']['keywords'] = $this->category->meta_keywords;
        // $page['meta']['description'] = $this->category->meta_description;
        $page['body_classes']['deo-blog-home-page'] = true;

        return $page;
    }
    
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $link = DeoBlogHelper::getInstance()->getFontBlogLink();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Blog', 'bloghomepage'),
            'url' => $link,
        );

        $author = Tools::getValue('author');
        if ($author) {
            $breadcrumb['links'][] = array(
                'title' => $author,
                'url' => false,
            );
        }

        $tag = trim(Tools::getValue('tag'));
        if ($tag) {
            $breadcrumb['links'][] = array(
                'title' => $tag,
                'url' => false,
            );
        }

        return $breadcrumb;
    }
    
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
