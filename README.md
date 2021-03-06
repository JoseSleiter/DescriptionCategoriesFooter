# DescriptionCategoriesFooter Module
## _Module to prestashop 1.7_

[![Build Status](https://camo.githubusercontent.com/214b531310e0cb9198ad7c911e9126cbc8c489c00ab695c12688e9d810ce4083/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f7068702d253345253344253230372e312d3838393242462e7376673f7374796c653d666c61742d737175617265)](https://php.net/)

It is prestashop 1.7 compatible.
Stack PHP, SQL, POOr.

- Type some Markdown on the left
- See HTML in the right

## Features

- Import a HTML hook in the categories footer
- Add backoffice configuration into Category section

DescriptionCategoriesFooter is a module to prestashop 1.7 that make posible you can create a personality msg in CODE HTML section into footer to each category.
By [Jose Sleiter] writes on the [repo]

## Installation

DescriptionCategoriesFooter requires [PHP](https://php.net/) v7+ to run.

Do you remember add your var in \classes\Category.php

```sh
public $descriptioncategoriesfooter_name_var
```

Then into some file add in $definition static var, your personality var

```sh
    'descriptioncategoriesfooter_name_var' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
```

Then add new hook and the next code into themes\your_theme\templates\catalog\listing\product-list.tpl 
```sh
    {if !empty($category.descriptioncategoriesfooter_footer_description)}  
      <div class="block-category card card-block">
        <div class="block-category-inner">
          <div id="category-description" class="text-muted">    
            
            {$category.descriptioncategoriesfooter_footer_description nofilter}
       
          </div>
        </div>        
      </div>
    {/if}
```

## License
MIT


   [repo]: <https://github.com/JoseSleiter/DescriptionCategoriesFooter>
   [jose sleiter]: <https://www.linkedin.com/in/jose-sleiter-rios-905447165/>
   [symonfy]: <https://symfony.com/doc/current/reference/forms/types.html>
   [prestashop]: <https://devdocs.prestashop.com/1.7/development/components/form/types-reference/>
   [help1]: <https://www.h-hennes.fr/blog/2017/06/21/prestashop-ajouter-des-champs-dans-un-formulaire-dadministration/>
   [help2]: <https://www.h-hennes.fr/blog/2019/08/05/prestashop-1-7-ajouter-des-champs-dans-un-formulaire-dadministration/>

