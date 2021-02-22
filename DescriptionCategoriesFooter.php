<?php

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;

class descriptioncategoriesfooter extends Module
{
    public static $definition = array(
        'table' => 'category_lang',
        'fields' => array(
            array('type' => 'TEXT', 'name' => '_footer_description', 'separetor' => ','),
            array('type' => 'TEXT', 'name' => '_footer_title', 'separetor' => ''),
        )
    );

    /**
     * DescriptionCategoriesFooter constructor
     * Instanciation du module
     */
    public function __construct()
    {
        $this->name = 'descriptioncategoriesfooter';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'sleiter.js@gmail.com';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Description Categories Footer in Admin');
        $this->description = $this->l('Module for admin form hooks for ps 1.7.6 and > ');
    }

    /**
     * Installation du module
     * @return bool
     */
    public function install()
    {
        if (
            !parent::install()
            || !$this->registerHook([
                'actionCategoryFormBuilderModifier',
                'actionAfterCreateCategoryFormHandler',
                'actionAfterUpdateCategoryFormHandler',
                'actionAdminCategoriesFormModifier',
                'actionAdminCategoriesControllerSaveAfter',
            ])
            || !$this->_installSql()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * Modification du formulaire de la catégorie
     * @param array $params
     */
    public function hookActionCategoryFormBuilderModifier(array $params)
    {

        $category = new Category((int)$params['id']);
        $languages = Language::getLanguages(true);
        $formBuilder = $params['form_builder'];
        $formBuilder->add(
            $this->name . self::$definition['fields'][0]['name'],
            TranslateType::class,
            [
                'type' => FormattedTextareaType::class,
                'locales' => $languages,
                'hideTabs' => false,
                'label' => $this->l('Description Footer'),
                'required' => false,
                'data' => $category->descriptioncategoriesfooter_footer_description
            ]
        );
        foreach ($languages as $lang) {
            $params['data'][$this->name . self::$definition['fields'][0]['name']][$lang['id_lang']] = $category->descriptioncategoriesfooter_footer_description[$lang['id_lang']];
        }
        $formBuilder->setData($params['data'], $params);
    }

    /**
     * Action effectuée après la création d'une catégorie
     * @param array $params
     */
    public function hookActionAfterCreateCategoryFormHandler(array $params)
    {
        $this->updateData($params['form_data'], $params);
    }

    /**
     * Action effectuée après la mise à jour d'une catégorie
     * @param array $params
     */
    public function hookActionAfterUpdateCategoryFormHandler(array $params)
    {
        $this->updateData($params['form_data'], $params);
    }


    public function hookActionAdminCategoriesFormModifier($params)
    {

        $category = new Category((int)Tools::getValue('id_category'));

        $params['fields'][0]['form']['input'][] =  array(
            'type' => 'textarea',
            'autoload_rte' => true,
            'rows' => "10",
            'cols' => "45",
            'lang' => true,
            'label' => $this->l('Description Footer'),
            'name' => $this->name . self::$definition['fields'][0]['name'],
        );

        //Modification des propriétés d'un champ déjà existant
        // foreach ( $params['fields'][0]['form']['input'] as &$field ){

        //   if ( $field['name'] == 'meta_description'){
        //         $field['maxlength'] = '255';
        //         $field['maxchar'] = '255';        
        //         $field['hint'] = 'Modified by a module';
        //     }
        // }

        //Création d'un nouveau fieldset
        // $params['fields'][$this->name] = array(
        //     'form' => array(
        //         'legend' => array(
        //             'title' => $this->l('Sample Category Fieldset'),
        //             'icon' => 'icon-tags',
        //         ),
        //         'description' => $this->l('New sample fieldset'),
        //         'input' => array(
        //             array(
        //                 'type' => 'text',
        //                 'label' => $this->l('Custom field New Fieldset 1'),
        //                 'name' => $this->name.'_newfieldset1',
        //             ),
        //             array(
        //                 'type' => 'text',
        //                 'label' => $this->l('Custom field New Fieldset 2'),
        //                 'name' => $this->name.'_newfieldset2',
        //             ),
        //         )
        //     )
        // );
        //Pour remonter les valeurs des champs
        // $params['fields_value'][$this->name.'_newfield1'] = 'Custom value 1';
        // $params['fields_value'][$this->name.'_newfieldset1'] = 'Custom value fieldset 1';
        // $params['fields_value'][$this->name.'_newfieldset2'] = 'Custom value fieldset 2';

        // foreach ($languages as $lang) {
        //   $params['fields_value'][$this->getFieldName(self::$definition['fields'][0])][$lang['id_lang']] = Tools::htmlentitiesDecodeUTF8($category->descriptioncategoriesfooter_footer_description[$lang['id_lang']]);

        // }
        $params['fields_value'][$this->getFieldName(self::$definition['fields'][0])] = $category->descriptioncategoriesfooter_footer_description;
    }


    public function hookActionAdminCategoriesControllerSaveAfter($params)
    {
        $languages = Language::getLanguages(true);
        foreach ($languages as $lang) {
            $params['fields_value'][$this->getFieldName(self::$definition['fields'][0])][$lang['id_lang']] = Tools::htmlentitiesUTF8(Tools::getValue($this->getFieldName(self::$definition['fields'][0]) . '_' . $lang['id_lang']));
        }
        // $fiedlset2 = Tools::getValue($this->name.'_newfieldset2');
    }


    private function getFieldName($field)
    {
        return $this->name . $field['name'];
    }

    /**
     * Fonction qui va effectuer la mise à jour
     * @param array $data
     */
    protected function updateData(array $data, $params)
    {
        $category = new Category((int)$params['id']);
        $category->descriptioncategoriesfooter_footer_description  = $data[$this->name . self::$definition['fields'][0]['name']];
        $category->update();
    }

    /**
     * Modifications sql du module
     * @return boolean
     */
    private function _installSql()
    {
        $fields = '';
        foreach (self::$definition['fields'] as $name_field) {
            $fields .= ' ADD ' . $this->getFieldName($name_field) . ' ' .  $name_field['type'] . ' DEFAULT NULL ' . $name_field['separetor'];
        }
        $sqlInstall = 'ALTER TABLE ' . _DB_PREFIX_ . self::$definition['table'] . $fields . ' ';
        Db::getInstance()->execute($sqlInstall);
        return true;
    }

    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    private function _unInstallSql()
    {
        $fields = '';
        foreach (self::$definition['fields'] as $name_field) {
            $fields .= ' DROP ' . $this->getFieldName($name_field) . ' ' . $name_field['separetor'];
        }
        $sqlUnInstall = 'ALTER TABLE  ' . _DB_PREFIX_ . self::$definition['table'] . $fields;
        Db::getInstance()->execute($sqlUnInstall);
        return true;
    }
}
