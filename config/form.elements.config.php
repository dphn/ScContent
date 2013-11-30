<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return [
    'invokables' => [
        'ScForm.Back.Category'
            => 'ScContent\Form\Back\CategoryForm',

        'ScForm.Back.Article'
            => 'ScContent\Form\Back\ArticleForm',

        'ScForm.Back.FileEdit'
            => 'ScContent\Form\Back\FileEditForm',

        'ScForm.Back.ContentSearch'
            => 'ScContent\Form\Back\ContentSearchForm',
    ],
    'factories' => [
        'ScForm.Back.FileAdd'
            => 'ScContent\Factory\Form\Back\FileAddFactory',

        'ScForm.Installation.Database'
            => 'ScContent\Factory\Form\Installation\DatabaseFormFactory',
    ],
];
