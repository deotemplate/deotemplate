<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

class DeoProductTag extends DeoShortCodeBase
{
    public $name = 'DeoProductTag';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Product Tags',
            'position' => 4,
            'desc' => $this->l('Show Product Tags at Frontend'),
            'image' => 'tag.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {
        $accordion_type = array(
            array(
                'value' => 'full',
                'text' => $this->l('Normal')
            ),
            array(
                'value' => 'accordion',
                'text' => $this->l('Accordion')
            ),
            array(
                'value' => 'accordion_small_screen',
                'text' => $this->l('Accordion at tablet (screen <= 768px)')
            ),
            array(
                'value' => 'accordion_mobile_screen',
                'text' => $this->l('Accordion at mobile (screen <= 576px)')
            ),
        );
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'lang' => 'true',
                'default' => '',
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'autoload_rte' => false,
                'values' => '',
                'class' => 'sub_title',
                'default' => '',
            ),
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Accordion Type'),
                'name'  => 'accordion_type',
                'options'   => array(
                    'query' => $accordion_type,
                    'id'    => 'value',
                    'name'  => 'text'
                ),
                'default' => 'full',
                'hint'  => $this->l('Select a Accordion Type'),
            ),
            array(
                'type' => 'text',
                'name' => 'displayed_tags',
                'label' => $this->l('Displayed tags'),
                'desc' => $this->l('Set the number of tags you would like to see displayed in this block. (default: 10)'),
                'lang' => 'false',
                'default' => '10'
            ),
            array(
                'type' => 'text',
                'name' => 'tag_levels',
                'label' => $this->l('Tag levels'),
                'desc' => $this->l('Set the number of different tag levels you would like to use. (default: 3) '),
                'lang' => 'false',
                'default' => '10'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Random display'),
                'name' => 'random_display',
                'class' => 'fixed-width-xs',
                'desc' => $this->l('If enabled, displays tags randomly. By default, random display is disabled and the most used tags are displayed first.'),
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
                )
            )
        );
        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        
        $id_lang = (int) Context::getContext()->language->id;
        $displayed_tags = isset($assign['formAtts']['displayed_tags']) ? (int)$assign['formAtts']['displayed_tags'] : 10;
        $tag_levels = isset($assign['formAtts']['tag_levels']) ? (int)$assign['formAtts']['tag_levels'] : 10;
        $random_display = isset($assign['formAtts']['random_display']) ? (int)$assign['formAtts']['random_display'] : 0;
        
        $tags = Tag::getMainTags($id_lang, $displayed_tags);

        $max = -1;
        $min = -1;
        foreach ($tags as $tag) {
            if ($tag['times'] > $max) {
                $max = $tag['times'];
            }
            if ($tag['times'] < $min || $min == -1) {
                $min = $tag['times'];
            }
        }

        if ($min == $max) {
            $coef = $max;
        } else {
            $coef = ($tag_levels - 1) / ($max - $min);
        }

        if (!count($tags)) {
            return $assign;
        }
        if ($random_display) {
            shuffle($tags);
        }
        foreach ($tags as &$tag) {
            $tag['class'] = 'tag_level'.(int)(($tag['times'] - $min) * $coef + 1);
        }

        $assign['formAtts']['tags'] = $tags;

        return $assign;
    }
}
