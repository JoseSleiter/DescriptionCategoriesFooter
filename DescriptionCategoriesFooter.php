<?php

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

class DescriptionCategoriesFooter extends Module
{
    public static $definition = array(
        'table' => 'ps_category_lang',
        'fields' => array(
            '_footer_title', '_footer_description'
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
            $this->name . self::$definition['fields'][0],
            TranslateType::class,
            [
                'type' => FormattedTextareaType::class,
                'locales' => $languages,
                'hideTabs' => false,
                'label' => $this->l('Description Footer'),
                'required' => false,
                'data' => $category->DescriptionCategoriesFooter_footer_description
            ]
        );
        foreach ($languages as $lang) {
            $params['data'][$this->name . self::$definition['fields'][0]][$lang['id_lang']] = $category->DescriptionCategoriesFooter_newfield1[$lang['id_lang']];
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

    /**
     * Fonction qui va effectuer la mise à jour
     * @param array $data
     */
    protected function updateData(array $data, $params)
    {
        // var_dump($data[$this->name . self::$definition['fields'][0]]);
        // exit();
        $category = new Category((int)$params['id']);
        $category->DescriptionCategoriesFooter_footer_description  = $data[$this->name . self::$definition['fields'][0]];
        $category->update();
    }

    /**
     * Modifications sql du module
     * @return boolean
     */
    private function _installSql()
    {
        $sqlInstall = 'ALTER TABLE ' . _DB_PREFIX_ . self::$definition['table'] . ' ADD ' . $this->name . self::$definition['fields'][0] . ' VARCHAR(255) NULL';

        return Db::getInstance()->execute($sqlInstall);
    }

    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    private function _unInstallSql()
    {
        $sqlUnInstall = 'ALTER TABLE  ' . _DB_PREFIX_ . self::$definition['table'] . ' DROP ' . $this->name . self::$definition['fields'][0];

        return Db::getInstance()->execute($sqlUnInstall);
    }
}
// FormattedTextareaType::class