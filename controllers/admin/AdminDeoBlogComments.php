<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogComment.php');

class AdminDeoBlogCommentsController extends ModuleAdminController
{
    protected $max_image_size = 1048576;
    protected $position_identifier = 'id_deoblog';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'deoblog_comment';
        $this->identifier = 'id_deoblog_comment';
        $this->className = 'DeoBlogComment';
        $this->lang = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        if (Tools::getValue('id_deoblog')) {
            # validate module
            $this->_where = ' AND id_deoblog='.(int)Tools::getValue('id_deoblog');
        }
        parent::__construct();
        
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));

        $this->fields_list = array(
            'id_deoblog_comment' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'id_deoblog' => array('title' => $this->l('Blog ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'user' => array('title' => $this->l('User')),
            'comment' => array('title' => $this->l('Comment')),
            'date_add' => array('title' => $this->l('Date Added'),'type' => 'datetime'),
            'active' => array('title' => $this->l('Displayed'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => false)
        );
    }

    public function initPageHeaderToolbar()
    {
        $link = $this->context->link;

        if (Tools::getValue('id_deoblog')) {
            $this->page_header_toolbar_btn['back-blog'] = array(
                'href' => $link->getAdminLink('AdminDeoBlogs').'&updatedeoblog&id_deoblog='.Tools::getValue('id_deoblog'),
                'desc' => $this->l('Back To The Blog'),
                'icon' => 'icon-blog icon-3x process-icon-blog'
            );
        }

        $this->page_header_toolbar_btn = array();

        $this->page_header_toolbar_btn['list-blogs'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogs'),
            'desc' => $this->l('Blogs'),
            'icon' => 'process-icon-widget icon-file-text',
            'target' => '_blank',
        );

        $this->page_header_toolbar_btn['list-categories'] = array(
            'href' => $this->context->link->getAdminLink('AdminDeoBlogCategories'),
            'desc' => $this->l('Categories'),
            'icon' => 'process-icon-widget icon-folder-open',
            'target' => '_blank',
        );

        return parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            if (Validate::isLoadedObject($this->object)) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $blog = new DeoBlog($this->object->id_deoblog, $this->context->language->id);

        $this->multiple_fieldsets = true;
        $this->object->blog_title = $blog->meta_title;

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Blog Form'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Comment ID'),
                    'name' => 'id_deoblog_comment',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Blog Title'),
                    'name' => 'blog_title',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('User'),
                    'name' => 'user',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email'),
                    'name' => 'email',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Blog Content'),
                    'name' => 'comment',
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Displayed:'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'saveandstay',
                    'type' => 'submit',
                    'title' => $this->l('Save and stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save-and-stay'
                )
            )
        );
        return parent::renderForm();
    }

    public function renderList()
    {
        $this->toolbar_title = $this->l('Comments Management');
        $this->toolbar_btn['new'] = null;
        $this->initToolbar();

        return parent::renderList();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('saveandstay')) {
            parent::validateRules();

            if (count($this->errors)) {
                $this->display = 'edit';
                return false;
            }

            if ($id_deoblog_comment = (int)Tools::getValue('id_deoblog_comment')) {
                $comment = new DeoBlogComment($id_deoblog_comment);
                $this->copyFromPost($comment, 'comment');

                if (!$comment->update()) {
                    $this->errors[] = $this->l('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.Tools::getValue('id_deoblog_comment').'&conf=4&update'.$this->table.'&token='.Tools::getValue('token'));
                }
            } else {
                $this->errors[] = $this->l('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            }
        } else {
            parent::postProcess();
        }
    }

    /**
     * Override function copyFromPost from AdminController
     * Copy data values from $_POST to object.
     */
    protected function copyFromPost(&$object, $table)
    {
        parent::copyFromPost($object, $table);
        if ((int) DeoHelper::getConfig('DEBUG_MODE')){
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            $class_vars = get_class_vars(get_class($object));
            $fields = array();
            if (isset($class_vars['definition']['fields'])) {
                $fields = $class_vars['definition']['fields'];
            }

            foreach ($fields as $field => $params) {
                if (array_key_exists('lang', $params) && $params['lang']) {
                    foreach (Language::getIDs(false) as $id_lang) {
                        if (Tools::isSubmit($field . '_' . (int) $id_lang)) {
                            $object->{$field}[(int) $id_lang] = (Tools::getValue($field . '_' . (int) $id_lang) == '' && Tools::getValue($field . '_' . (int) $id_lang_default) != '') ? Tools::getValue($field . '_' . (int) $id_lang_default) : Tools::getValue($field . '_' . (int) $id_lang);
                        }
                    }
                }
            }
        }
    }
}
