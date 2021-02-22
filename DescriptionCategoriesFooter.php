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
        $this->author = 'me';
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
