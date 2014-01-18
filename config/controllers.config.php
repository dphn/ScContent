<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return [
    'invokables' => [
        'ScController.User'
            => 'ScContent\Controller\UserController',

        'ScController.Installation.Requirements'
            => 'ScContent\Controller\Installation\RequirementsController',

        'ScController.Installation.Default'
            => 'ScContent\Controller\Installation\InstallationController',

        'ScController.Installation.Config'
            => 'ScContent\Controller\Installation\ConfigController',

        'ScController.Installation.Account'
            => 'ScContent\Controller\Installation\AccountController',

        'ScController.Back.Garbage'
            => 'ScContent\Controller\Back\GarbageController',

        'ScController.Back.Category'
            => 'ScContent\Controller\Back\CategoryController',

        'ScController.Back.Article'
            => 'ScContent\Controller\Back\ArticleController',

        'ScController.Back.File'
            => 'ScContent\Controller\Back\FileController',

        'ScController.Back.Theme'
            => 'ScContent\Controller\Back\ThemeController',

        'ScController.Back.Widget'
            => 'ScContent\Controller\Back\WidgetController',

        'ScController.Front.Content'
            => 'ScContent\Controller\Front\ContentController',
    ],
    'factories' => [
        // Factory use events
        'ScController.Back.Manager'
            => 'ScContent\Factory\Controller\Back\ContentManagerFactory',

        // Factory use events
        'ScController.Back.Layout'
            => 'ScContent\Factory\Controller\Back\LayoutFactory',
    ],
];
