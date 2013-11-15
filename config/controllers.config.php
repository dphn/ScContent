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
        'sc-controller.installation.requirements'
            => 'ScContent\Controller\Installation\RequirementsController',

        'sc-controller.installation.default'
            => 'ScContent\Controller\Installation\InstallationController',

        'sc-controller.installation.config'
            => 'ScContent\Controller\Installation\ConfigController',

        'sc-controller.back.garbage'
            => 'ScContent\Controller\Back\GarbageController',

        'sc-controller.back.category'
            => 'ScContent\Controller\Back\CategoryController',

        'sc-controller.back.article'
            => 'ScContent\Controller\Back\ArticleController',

        'sc-controller.back.file'
            => 'ScContent\Controller\Back\FileController',

        'sc-controller.back.theme'
            => 'ScContent\Controller\Back\ThemeController',

        'sc-controller.front.end'
            => 'ScContent\Controller\Front\FrontController',

        'sc-controller.front.widget.content'
            => 'ScContent\Controller\Front\ContentController',

        'sc-controller.front.widget.login'
            => 'ScContent\Controller\Front\LoginController',
    ),
    'factories' => array(
        // Factory use events
        'sc-controller.back.manager'
            => 'ScContent\Factory\Controller\Back\ContentManagerFactory',

        // Factory use events
        'sc-controller.back.layout'
            => 'ScContent\Factory\Controller\Back\LayoutFactory',
    ),
);
