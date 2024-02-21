<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }
include_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');

class DeoBlogImage
{
	public static function generateImageCategory($id_deoblog_category, $image, $image_link, $use_image_link, $id_shop){
        if (!is_dir(_PS_IMG_DIR_.'deotemplate/blog')) {
            @mkdir(_PS_IMG_DIR_.'deotemplate/blog', 0777, true);
        }

        $img_url = _PS_IMG_DIR_.'deotemplate/blog/'.$id_shop.'/blog-category/'.$id_deoblog_category;
        // create folder path if not exist
        if (!is_dir($img_url)) {
            @mkdir($img_url, 0777, true);
        }

        if ($use_image_link){
            $orginal_file = $image_link;

            if (!DeoBlogHelper::checkUrlFileExist($image_link)){
                return false;
            }

            $infor_image = pathinfo($image_link);
            Tools::copy($orginal_file, $img_url.'/'.$id_deoblog_category.'.'.$infor_image['extension']);
        }else{
            $orginal_file = DeoHelper::getThemeDir().'assets/img/modules/deotemplate/'.$image;

            if (!file_exists($orginal_file)){
                return false;
            }

            if (filetype($orginal_file) == 'file') {
                $infor_image = pathinfo($image);
                Tools::copy($orginal_file, $img_url.'/'.$id_deoblog_category.'.'.$infor_image['extension']);
            }
        }

        return true;
    }

    public static function generateImageBlog($id_deoblog, $image, $image_link, $use_image_link, $id_shop){
        require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogConfiguration.php');
        $blog_configuration = new DeoBlogConfiguration();

        if (!is_dir(_PS_IMG_DIR_.'deotemplate/blog')){
            @mkdir(_PS_IMG_DIR_.'deotemplate/blog', 0777, true);
        }

        $img_url = _PS_IMG_DIR_.'deotemplate/blog/'.$id_shop.'/blog/'.$id_deoblog;
        // create folder path if not exist
        if (!is_dir($img_url)) {
            @mkdir($img_url, 0777, true);
        }

        if ($use_image_link){
            $orginal_file = $image_link;

            if (!DeoBlogHelper::checkUrlFileExist($image_link)){
                return false;
            }

            $infor_image = pathinfo($image_link);
            Tools::copy($orginal_file, $img_url.'/'.$id_deoblog.'.'.$infor_image['extension']);

            // gen image other size
            if (isset($blog_configuration->image_size)){
                $image_size = $blog_configuration->image_size;
                foreach ($image_size as $key => $image) {
                    $name_image = $id_deoblog.'_'.$key;
                    ImageManager::resize($img_url.'/'.$id_deoblog.'.'.$infor_image['extension'], $img_url.'/'.$name_image.'.'.$infor_image['extension'], $image->width, $image->height);
                }
            }
        }else{
            $orginal_file = DeoHelper::getThemeDir().'assets/img/modules/deotemplate/'.$image;
            if (!file_exists($orginal_file)){
                return false;
            }

            if (filetype($orginal_file) == 'file') {
                $infor_image = pathinfo($image);
                // $name_image = $infor_image['basename'];
                // print_r($infor_image);
                // die();
                
                Tools::copy($orginal_file, $img_url.'/'.$id_deoblog.'.'.$infor_image['extension']);

                // gen image other size
                if (isset($blog_configuration->image_size)){
                    $image_size = $blog_configuration->image_size;
                    foreach ($image_size as $key => $image) {
                        $name_image = $id_deoblog.'_'.$key;
                        ImageManager::resize($img_url.'/'.$id_deoblog.'.'.$infor_image['extension'], $img_url.'/'.$name_image.'.'.$infor_image['extension'], $image->width, $image->height);
                    }
                }
            }
        }

        return true;
    }

    public static function getUrlImageCategory($id_deoblog_category, $image, $id_shop = null){
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $image_path = _PS_IMG_.'deotemplate/blog/'.$id_shop.'/blog-category/'.$id_deoblog_category;
        $infor_image = pathinfo($image);

        return $image_path.'/'.$id_deoblog_category.'.'.$infor_image['extension'];
    }

    public static function getUrlImageBlog($id_deoblog, $image, $id_shop = null){
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $image_path = _PS_IMG_.'deotemplate/blog/'.$id_shop.'/blog/'.$id_deoblog;
        $infor_image = pathinfo($image);

        return $image_path.'/'.$id_deoblog.'.'.$infor_image['extension'];
    }

    public function getImageBlogBySize($url,$size_name){
        if ($size_name){
            $infor_image = pathinfo($url);
            $url = $infor_image['dirname'].'/'.$infor_image['filename'].'_'.$size_name.'.'.$infor_image['extension'];
        }
        
        return $url;
    }

    public static function removeGenerateImageCategory($id_deoblog_category, $id_shop){
        $img_url = _PS_IMG_DIR_.'deotemplate/blog/'.$id_shop.'/blog-category/'.$id_deoblog_category;

        if (is_dir($img_url)) {
            DeoHelper::deleteDirectory($img_url);
            return true;
        }

        return false;
    }

    public static function removeGenerateImageBlog($id_deoblog, $id_shop){
        $img_url = _PS_IMG_DIR_.'deotemplate/blog/'.$id_shop.'/blog/'.$id_deoblog;

        if (is_dir($img_url)) {
            DeoHelper::deleteDirectory($img_url);
            return true;
        }
        
        return false;
    }

    public static function regenerateImage(){
        $id_shop = DeoHelper::getIDShop();

        require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogCategory.php');
        $category = new DeoBlogCategory();
        $categories = $category->getAllChild();
        // print_r($categories);
        foreach ($categories as $category) {
            if ($category['image'] != '') {
                self::generateImageCategory($category['id_category'], $category['image'], $category['image_link'], $category['use_image_link'], $id_shop);
            }
        }

        require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlog.php');
        $blog = new DeoBlog();
        $blogs = $blog->getListBlogs(null, null, null, null, null, null, null, true, null);
        foreach ($blogs as $blog) {
            if ($blog['image'] != '') {
                self::generateImageBlog($blog['id_deoblog'], $blog['image'], $blog['image_link'], $blog['use_image_link'], $id_shop, false);
            }
        }
    }
}