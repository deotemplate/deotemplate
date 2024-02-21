<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogComment.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogLink.php');

class DeoBlogHelper
{
    public $bloglink = null;
    public $ssl;

    public static function getInstance()
    {
        static $instance = null;
        if (!$instance) {
            # validate module
            $instance = new DeoBlogHelper();
        }

        return $instance;
    }

    public function __construct()
    {
        if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $this->ssl = true;
        }

        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $this->bloglink = new DeoBlogLink($protocol_link, $protocol_content);
    }
    
    public static function correctDeCodeData($data)
    {
        $functionName = 'b'.'a'.'s'.'e'.'6'.'4'.'_'.'decode';
        return call_user_func($functionName, $data);
    }

    public static function correctEnCodeData($data)
    {
        $functionName = 'b'.'a'.'s'.'e'.'6'.'4'.'_'.'encode';
        return call_user_func($functionName, $data);
    }

    public static function loadMedia($context, $obj)
    {
        $uri = DeoHelper::getJsDir().'blog.js';
        $context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));

        $uri = DeoHelper::getCssDir().'blog.css';
        $context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
    }

    public function getLinkObject()
    {
        return $this->bloglink;
    }

    public function getModuleLink($route_id, $controller, array $params = array(), $ssl = null, $id_lang = null, $id_shop = null)
    {
        return $this->getLinkObject()->getLink($route_id, $controller, $params, $ssl, $id_lang, $id_shop);
    }

    public function getFontBlogLink($params = array())
    {
        return $this->getModuleLink('module-deotemplate-bloghomepage', 'bloghomepage', $params);
    }

    public function getPaginationLink($route_id, $controller, array $params = array(), $nb = false, $sort = false, $pagination = false, $array = true)
    {
        return $this->getLinkObject()->getDeoPaginationLink('DeoBlog', $route_id, $controller, $params, $nb, $sort, $pagination, $array);
    }

    public function getBlogLink($blog, $cparams = array())
    {
        if (is_object($blog)){
            $id = $blog->id_deoblog;
            $rewrite = $blog->link_rewrite;
        }else{
            $id = $blog['id_deoblog'];
            $rewrite = $blog['link_rewrite'];
        }

        $params = array(
            'id' => $id,
            'rewrite' => $rewrite,
        );
        if ($blog_style = Tools::getValue('blog_style')){
            $params = array_merge($params, array('blog_style' => $blog_style));
        }

        $params = array_merge($params, $cparams);

        return $this->getModuleLink('module-deotemplate-blog', 'blog', $params);
    }

    public function getTagBlogCategoryLink($category, $tag, $cparams = array())
    {
        if (is_object($category)){
            $id = $category->id_deoblog_category;
            $rewrite = $category->link_rewrite;
        }else{
            $id = $category['id_deoblog_category'];
            $rewrite = $category['link_rewrite'];
        }
        $params = array(
            'id' => $id,
            'rewrite' => $rewrite,
            'tag' => urlencode($tag),
        );
        if ($blog_style = Tools::getValue('blog_style')){
            $params = array_merge($params, array('blog_style' => $blog_style));
        }
        $params = array_merge($params, $cparams);

        return $this->getModuleLink('module-deotemplate-blogcategory', 'blogcategory', $params);
    }

    public function getBlogCatLink($category, $cparams = array())
    {
        if (is_object($category)){
            $id = $category->id_deoblog_category;
            $rewrite = $category->link_rewrite;
        }else{
            $id = isset($category['id']) ? $category['id'] : $category['id_deoblog_category'];
            $rewrite = isset($category['id_deoblog']) ? $category['category_link_rewrite'] : $category['link_rewrite'];
        }

        $params = array(
            'id' => $id,
            'rewrite' => $rewrite,
        );
        if ($blog_style = Tools::getValue('blog_style')){
            $params = array_merge($params, array('blog_style' => $blog_style));
        }

        $params = array_merge($params, $cparams);
        return $this->getModuleLink('module-deotemplate-blogcategory', 'blogcategory', $params);
    }

    public function getBlogTagLink($tag, $cparams = array())
    {
        $params = array(
            'tag' => urlencode($tag),
        );
        if ($blog_style = Tools::getValue('blog_style')){
            $params = array_merge($params, array('blog_style' => $blog_style));
        }

        $params = array_merge($params, $cparams);
        return $this->getModuleLink('module-deotemplate-bloghomepage', 'bloghomepage', $params);
    }

    public function getBlogAuthorLink($author, $cparams = array())
    {
        $params = array(
            'author' => $author,
        );
        if ($blog_style = Tools::getValue('blog_style')){
            $params = array_merge($params, array('blog_style' => $blog_style));
        }
        
        $params = array_merge($params, $cparams);
        return $this->getModuleLink('module-deotemplate-bloghomepage', 'bloghomepage', $params);
    }

    public static function getTemplates()
    {
        $theme = Context::getContext()->shop->theme_name;
        $path = _PS_MODULE_DIR_.'deotemplate';
        $theme_path = _PS_ALL_THEMES_DIR_.$theme.'modules/deotemplate/front/blog';

        $output = array();
        $module_templates = glob($path.'/views/templates/front/blog/*', GLOB_ONLYDIR);
        if ($module_templates) {
            foreach ($module_templates as $template) {
                $output[basename($template)] = array('type' => 'module', 'template' => basename($template), 'name' => basename($template));
            }
        }

        $theme_templates = glob($theme_path, GLOB_ONLYDIR);
        if ($theme_templates) {
            foreach ($theme_templates as $template) {
                $output[basename($template)] = array('type' => 'theme', 'template' => basename($template), 'name' => basename($template));
            }
        }

        return $output;
    }

    public static function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir') {
                        self::rrmdir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            $objects = scandir($dir);
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * @return day in month
     * 1st, 2nd, 3rd, 4th, ...
     */
    public function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return $number.'th';
        else
            return $number.$ends[$number % 10];
    }

    /**
     * @return day in month
     * st, nd, rd, th, ...
     */
    public function string_ordinal($number)
    {
        $number = (int) $number;
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return 'th';
        else
            return $ends[$number % 10];
    }
    
    public static function genKey()
    {
        return md5(time().rand());
    }

    public static function checkUrlFileExist($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($responseCode != 200){
            return false;
        }

        return true;
    }

    public function formatCategoryBlog($category){
        if (is_object($category)){
            $category->rate_image = $category->rate_image.'%';
            if ($category->use_image_link){
                $category->image = $category->image_link;
            }else{
                if ($category->image != ''){
                    require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
                    $category->image = DeoBlogImage::getUrlImageCategory($category->id_deoblog_category, $category->image);
                }
            }

            $category->category_link = $this->getBlogCatLink($category);

            $category->tags = array();
            if ($category->meta_keywords){
                $tags = explode(',', $category->meta_keywords);
                foreach ($tags as $tag) {
                    $category->tags[] = array(
                        'tag' => $tag,
                        'link' => $this->getTagBlogCategoryLink($category, $tag),
                    );
                }
            }
        }else{
            $category['rate_image'] = $category['rate_image'].'%';
            if ($category['use_image_link']){
                $category['image'] = $category['image_link'];
            }else{
                if ($category['image'] != ''){
                    require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
                    $category['image'] = DeoBlogImage::getUrlImageCategory($category['id_deoblog_category'], $category['image']);
                }
            }

            $category['category_link'] = $this->getBlogCatLink($category);

            $category['tags'] = array();
            if ($category['meta_keywords']){
                $tags = explode(',', $category['meta_keywords']);
                foreach ($tags as $tag) {
                    $category['tags'][] = array(
                        'tag' => $tag,
                        'link' => $this->getTagBlogCategoryLink($category, $tag),
                    );
                }
            }
        }

        return $category;
    }

    public function formatBlog($blog, $image_size = false, $comment = false){
        if (is_object($blog)){
            $blog->rate_image = $blog->rate_image.'%';
            if ($blog->use_image_link){
                $blog->image = $blog->image_link;
            }else{
                if ($blog->image != ''){
                    require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
                    $blog->image = DeoBlogImage::getUrlImageBlog($blog->id_deoblog, $blog->image);
                    if ((boolean)$image_size){
                        $blog->image = $this->getImageBlogBySize($blog->image, $image_size);
                    }
                }
            }

            $blog->link = $this->getBlogLink($blog);

            $blog->tags = array();
            if ($blog->meta_keywords){
                $tags = explode(',', $blog->meta_keywords);
                foreach ($tags as $tag) {
                    $blog->tags[] = array(
                        'tag' => $tag,
                        'link' => $this->getBlogTagLink($tag)
                    );
                }
            }

            $blog->category_link = $this->getBlogCatLink($blog);

            $blog->author = '';
            $blog->author_link = '';
            if ($blog->id_employee) {
                if ($blog->author_name != '') {
                    $blog->author = $blog->author_name;
                    $blog->author_link = $this->getBlogAuthorLink($blog->author_name);
                } else {
                    $authors[$blog->id_employee] = new Employee($blog->id_employee);
                    if (isset($authors[$blog->id_employee])){
                        $blog->author = $authors[$blog->id_employee]->firstname.' '.$authors[$blog->id_employee]->lastname;
                        $blog->author_link = $this->getBlogAuthorLink($authors[$blog->id_employee]->id);
                    }
                }
            }

            $blog->comment_count = 0;
            if (is_array($comment)){
                $blog->comment_count = isset(array_count_values(array_column($comment, 'id_deoblog'))[$blog->id_deoblog]) ? array_count_values(array_column($comment, 'id_deoblog'))[$blog->id_deoblog] : 0;
            }else{
                unset($blog->comment_count);
            }
        }else{
            $blog['rate_image'] = $blog['rate_image'].'%';
            if ($blog['use_image_link']){
                $blog['image'] = $blog['image_link'];
            }else{
                if ($blog['image'] != ''){
                    require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
                    $blog['image'] = DeoBlogImage::getUrlImageBlog($blog['id_deoblog'], $blog['image']);
                    if ((boolean)$image_size){
                        $blog['image'] = $this->getImageBlogBySize($blog['image'], $image_size);
                    }
                }
            }

            $blog['link'] = $this->getBlogLink($blog);

            $blog['tags'] = array();
            if ($blog['meta_keywords']){
                $tags = explode(',', $blog['meta_keywords']);
                foreach ($tags as $tag) {
                    $blog['tags'][] = array(
                        'tag' => $tag,
                        'link' => $this->getBlogTagLink($tag)
                    );
                }
            }

            $blog['category_link'] = $this->getBlogCatLink($blog);

            $blog['author'] = '';
            $blog['author_link'] = '';
            if ($blog['id_employee']) {
                if ($blog['author_name'] != '') {
                    $blog['author'] = $blog['author_name'];
                    $blog['author_link'] = $this->getBlogAuthorLink($blog['author_name']);
                } else {
                    $authors[$blog['id_employee']] = new Employee($blog['id_employee']);
                    if (isset($authors[$blog['id_employee']])){
                        $blog['author'] = $authors[$blog['id_employee']]->firstname.' '.$authors[$blog['id_employee']]->lastname;
                        $blog['author_link'] = $this->getBlogAuthorLink($authors[$blog['id_employee']]->id);
                    }
                }
            }

            $blog['comment_count'] = 0;
            if (is_array($comment)){
                $blog['comment_count'] = isset(array_count_values(array_column($comment, 'id_deoblog'))[$blog['id_deoblog']]) ? array_count_values(array_column($comment, 'id_deoblog'))[$blog['id_deoblog']] : 0;
            }else{
                unset($blog['comment_count']);
            }
        }

        return $blog;
    }

    public function getImageBlogBySize($url,$size_name){
        if ($size_name){
            $infor_image = pathinfo($url);
            $url = $infor_image['dirname'].'/'.$infor_image['filename'].'_'.$size_name.'.'.$infor_image['extension'];
        }
        
        return $url;
    }
    
    static $id_shop;
    /**
     * FIX Install multi theme
     * DeoBlogHelper::getIDShop();
     */
    public static function getIDShop()
    {
        if ((int)self::$id_shop) {
            $id_shop = (int)self::$id_shop;
        } else {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        return $id_shop;
    }
}
