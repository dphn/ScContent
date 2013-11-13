<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'invokables' => array(
        'sc-form.back.category'
            => 'ScContent\Form\Back\Category',

        'sc-form.back.article'
            => 'ScContent\Form\Back\Article',

        'sc-form.back.file.edit'
            => 'ScContent\Form\Back\FileEdit',

        'sc-form.back.content.search'
            => 'ScContent\Form\Back\ContentSearch',
    ),
    'factories' => array(
        'sc-form.back.file.add'
            => 'ScContent\Factory\Form\Back\FileAddFactory',

        'sc-form.installation.database'
            => 'ScContent\Factory\Form\Installation\DatabaseFormFactory',
    ),
);
