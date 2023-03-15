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

class DeoTemplateBlogModuleFrontController extends ModuleFrontController
{
    public $php_self;
    protected $template_path = '';
    // public $rewrite;
    
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->template_path = _PS_MODULE_DIR_.'deotemplate/views/templates/front/';
        
        $this->blog_configuration = new DeoBlogConfiguration();
        
        if ($this->blog_configuration->get('BLOG_URL_USE_ID', 1)) {
            // URL HAVE ID
            $this->blog = new DeoBlog(Tools::getValue('id'), $this->context->language->id);
        } else {
            // REMOVE ID FROM URL
            $url_rewrite = explode('/', $_SERVER['REQUEST_URI']) ;
            $url_last_item = count($url_rewrite) - 1;
            $url_rewrite = rtrim($url_rewrite[$url_last_item], 'html');
            $url_rewrite = rtrim($url_rewrite, '\.');    // result : product.html -> product.
            $this->blog = DeoBlog::findByRewrite(array('link_rewrite' => $url_rewrite));
        }

        $id_category = $this->blog_configuration->get('ID_BLOG_CATEGORY_ROOT', null);
        $this->category = new DeoBlogCategory($this->blog->id_deoblog_category, $this->context->language->id);
        
        $this->template = ($this->category->template == '') ? $this->blog_configuration->get('BLOG_DEFAULT_TEMPLATE') : $this->category->template;
        $this->template = (Tools::getValue('blog_style')) ? Tools::getValue('blog_style') : $this->template;
    }

    public function captcha()
    {
        include_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoCaptcha.php');
        $captcha = new DeoCaptcha();
        $this->context->cookie->leocaptch = $captcha->getCode();
        $captcha->showImage();
    }

    /**
     * @param object &$object Object
     * @param string $table Object table
     * @ DONE
     */
    protected function copyFromPost(&$object, $table, $post = array())
    {
        foreach ($post as $key => $value) {
            if (is_array($object)){
                if (key_exists($key, $object) && $key != 'id_'.$table) {
                    /* Do not take care of password field if empty */
                    if ($key == 'passwd' && Tools::getValue('id_'.$table) && empty($value)) {
                        continue;
                    }
                    if ($key == 'passwd' && !empty($value)) {
                        /* Automatically encrypt password in MD5 */
                        $value = Tools::encrypt($value);
                    }
                    $object->{$key} = $value;
                }
            }else{
                /* Classical fields */
                $object->{$key} = $value;
            }
        }
        
        /* Multilingual fields */
        $rules = call_user_func(array(get_class($object), 'getValidationRules'), get_class($object));
        if (count($rules['validateLang'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (array_keys($rules['validateLang']) as $field) {
                    $field_name = $field.'_'.(int)($language['id_lang']);
                    $value = Tools::getValue($field_name);
                    if (isset($value)) {
                        # validate module
                        $object->{$field}[(int)($language['id_lang'])] = $value;
                    }
                }
            }
        }
    }

    /**
     * Save user comment
     */
    protected function comment()
    {
        $post = array();
        $post['user'] = Tools::getValue('user');
        $post['email'] = Tools::getValue('email');
        $post['comment'] = Tools::getValue('comment');
        $post['captcha'] = Tools::getValue('captcha');
        $post['id_deoblog'] = Tools::getValue('id_deoblog');
        $post['submitcomment'] = Tools::getValue('submitcomment');

        if (!empty($post)) {
            $comment = new DeoBlogComment();
            $captcha = Tools::getValue('captcha');
            $this->copyFromPost($comment, 'deoblog_comment', $post);

            $error = new stdClass();
            $error->error = true;

            if (isset($this->context->cookie->leocaptch) && $captcha && $captcha == $this->context->cookie->leocaptch) {
                if ($comment->validateFields(false) && $comment->validateFieldsLang(false)) {
                    $comment->save();
                    $error->message = $this->module->l('Thanks for your comment, it will be published soon!!!', 'blog');
                    $error->error = false;
                } else {
                    # validate module
                    $error->message = $this->module->l('An error occurred while sending the comment. Please recorrect data in fields!!!', 'blog');
                }
            } else {
                # validate module
                $error->message = $this->module->l('An error with captcha code, please try to recorrect!!!', 'blog');
            }

            die(json_encode($error));
        }
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        
        $this->php_self = 'blog';
        
        if (Tools::getValue('captchaimage')) {
            $this->captcha();
            exit();
        }

        /* Load Css and JS File */
        DeoBlogHelper::loadMedia($this->context, $this);

        $image = new DeoBlogImage();

        parent::initContent();

        if (Tools::isSubmit('submitcomment')) {
            # validate module
            $this->comment();
        }
        
        $template = $this->template;

        $helper = DeoBlogHelper::getInstance();


        $this->category = $helper->formatCategoryBlog($this->category);
        
        if (!$this->blog->id_deoblog) {
            $vars = array(
                'error' => true,
            );
            $this->context->smarty->assign($vars);

            return $this->setTemplate('module:deotemplate/views/templates/front/blog/'.$template.'/blog.tpl');
        }

        $comment = false;
        if (DeoHelper::getConfig('BLOG_ITEM_COMMENT_ENGINE') == 'local') {
            $comment = DeoBlogComment::getComments(null, null, null, null, null, null, null);
        }
        $this->blog = $helper->formatBlog($this->blog, false, $comment);

        $this->template_path .= $template.'/';
        $module_tpl = $this->template_path;

        $url = _PS_BASE_URL_;
        if (Tools::usingSecureMode()) {
            # validate module
            $url = _PS_BASE_URL_SSL_;
        }

        $id_shop = $this->context->shop->id;

        $captcha_image = $helper->getBlogLink(get_object_vars($this->blog), array('captchaimage' => 1));
        $blog_link = $helper->getBlogLink(get_object_vars($this->blog));

        $this->blog->category_link = $helper->getBlogCatLink($this->category);
        $this->blog->category_title = $this->category->title;

        $this->blog->views = $this->blog->views + 1;
        $this->blog->updateField($this->blog->id, array('views' => $this->blog->views));

        $limit = 5;
        $blog_same_category = DeoBlog::getListBlogs($this->category->id_deoblog_category, $this->context->language->id, 0, $limit, 'date_add', 'DESC', array('type' => 'samecat', 'id_deoblog' => $this->blog->id_deoblog), true);
        foreach ($blog_same_category as $key => $blog) {
            $blog['link'] = $helper->getBlogLink($blog);
            $blog_same_category[$key] = $blog;
        }

        $blog_related_tag = array();
        if ($this->blog->meta_keywords) {
            $blog_related_tag = DeoBlog::getListBlogs(false, $this->context->language->id, 0, $limit, 'id_deoblog', 'DESC', array('type' => 'tag', 'tag' => $this->blog->meta_keywords), true);
            foreach ($blog_related_tag as $key => $blog) {
                $blog['link'] = $helper->getBlogLink($blog);
                $blog_related_tag[$key] = $blog;
            }
        }

        /* Comments */
        $vars_comment = array();
        if ($this->blog_configuration->get('BLOG_ITEM_COMMENT_ENGINE', 'local') == 'local') {
            $count_comment = DeoBlogComment::countComments($this->blog->id_deoblog, true);
           
            $blog_link = $helper->getBlogLink(get_object_vars($this->blog));
            $limit = (int)$this->blog_configuration->get('BLOG_ITEM_LIMIT_COMMENTS', 10);
            $n = $limit;
            $p = abs((int)(Tools::getValue('p', 1)));

            $comment = new DeoBlogComment();
            $comments = $comment->getList($this->blog->id_deoblog, $this->context->language->id, $p, $limit);

            $nb_blogs = $count_comment;
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

            $this->context->smarty->assign(array(
                'pages_nb' => $pages_nb,
                'nb_items' => $count_comment,
                'p' => (int)$p,
                'n' => (int)$n,
                'requestPage' => $blog_link,
                'requestNb' => $blog_link,
                'start' => $start,
                'comments' => $comments,
                'range' => $range,
                'stop' => $stop
            ));
        }

        $this->context->smarty->assign(array(
            'image' => $image,
            'blog' => $this->blog,
            'blog_same_category' => $blog_same_category,
            'blog_related_tag' => $blog_related_tag,
            'configures' => $this->blog_configuration,
            'id_deoblog' => $this->blog->id_deoblog,
            'is_active' => $this->blog->active,
            'productrelated' => array(),
            'module_tpl' => $module_tpl,
            'captcha_image' => $captcha_image,
            'blog_link' => $blog_link,
            'lazyload' => DeoHelper::getLazyload(),
            'template' => $template,
        ));

        $this->setTemplate('module:deotemplate/views/templates/front/blog/'.$template.'/blog.tpl');
    }
    
    // add meta
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['meta']['title'] = Tools::ucfirst($this->blog->meta_title).' - '.Configuration::get('PS_SHOP_NAME');
        $page['meta']['keywords'] = $this->blog->meta_keywords;
        $page['meta']['description'] = $this->blog->meta_description;
        $page['body_classes']['deo-blog-detail-page'] = true;

        return $page;
    }
    // add breadcrumb
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $helper = DeoBlogHelper::getInstance();
        $link = $helper->getFontBlogLink();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Blog', 'blog'),
            'url' => $link,
        );
        
        $category_link = $helper->getBlogCatLink($this->category);
        $breadcrumb['links'][] = array(
            'title' => $this->category->title,
            'url' => $category_link,
        );
        
        $breadcrumb['links'][] = array(
            'title' => Tools::ucfirst($this->blog->meta_title),
            'url' => $helper->getBlogLink(get_object_vars($this->blog)),
        );

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
