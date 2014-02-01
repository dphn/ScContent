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
    'sc' => [
        /* Why these 'sc' options here? Why not 'config/autoload'?
         *
         * If the module is disabled, these options will not be present in the
         * global configuration. It is important, because uses
         * both the database and configuration.
         * Take this into consideration when writing a "theme module".
         */
        'widgets' => [
            'content' => [
                'display_name' => 'Content',
                'description' => 'Editable content. For editing use the content manager.',
                'options' => [
                    'immutable' => true,
                    'unique' => true,
                ],
            ],
        ],
        'themes' => [
            'sc-default' => [
                // Your theme can provide only frontend templates and layouts.
                'provides_backend' => true,

                'display_name' => 'ScContent Default',
                'description'  => 'The default theme with several regions.',
                'theme_images' => '/sc-default/img',
                'screenshot'   => 'sc-default/img/theme.png',
                'access_denied_template' => 'sc-default/template/frontend/user/deny',
                'zfcuser_template_path' => $this->getDir() . str_replace('/', DS, '/view/sc-default'),
                'errors' => [
                    /* You can use different layouts for frontend errors and
                     * backend errors, simply use the placeholder {side}:
                     * 'my-theme/layout/error/{side}'
                     */
                    'layout' => 'sc-default/layout/frontend/index',

                    /* You can use different templates for frontend errors and
                     * backend errors, simply use the placeholder {side}:
                    * 'my-theme/template/error/{side}/index'
                    * 'my-theme/template/error/{side}/404'
                    */
                    'template' => [
                        'exception' => 'sc-default/template/error/index',
                        '404'       => 'sc-default/template/error/404',
                    ],
                ],
                /* Backend
                 */
                'backend' => [
                    // optionally, by default will automatically be calculated as my-theme/template/backend
                    'templates' => 'sc-default/template/backend',
                    // optionally, by default will automatically be calculated as my-theme/layout/backend
                    'layouts' => 'sc-default/layout/backend',
                    // optionally, by default 'index'
                    'default_layout' => 'index',
                ],
                /* Frontend
                 */
                'frontend' => [
                    // optionally, by default will automatically be calculated as my-theme/template/frontend
                    'templates' => 'sc-default/template/frontend',
                    // optionally, by default will automatically be calculated as my-theme/layout/frontend
                    'layouts' => 'sc-default/layout/frontend',
                    // optionally, by default 'index'
                    'default_layout' => 'index',
                    'regions' => [
                        'header' => [
                            'display_name' => 'Header',
                            'partial' => 'sc-default/layout/frontend/region/header',
                            'contains' => [
                                'site_title', 'banner', 'search'
                            ],
                        ],
                        'content_top' => [
                            'display_name' => 'Content Top',
                            'partial' => 'sc-default/layout/frontend/region/content-top',
                            'contains' => [
                            ],
                        ],
                        'content_middle' => [
                            'display_name' => 'Content Middle',
                            'partial' => 'sc-default/layout/frontend/region/content-middle',
                            'contains' => [
                                'content'
                            ],
                        ],
                        'content_bottom' => [
                            'display_name' => 'Content Bottom',
                            'partial' => 'sc-default/layout/frontend/region/content-bottom'
                        ],
                        'aside' => [
                            'display_name' => 'Aside',
                            'partial' => 'sc-default/layout/frontend/region/aside',
                            'contains' => [
                                'example',
                                'login',
                            ],
                        ],
                        'footer' => [
                            'display_name' => 'Footer',
                            'partial' => 'sc-default/layout/frontend/region/footer',
                            'contains' => [],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'translator' => 'MvcTranslator',
            'Zend\Db\Adapter\Adapter' => 'ScDb.Adapter',
        ],
    ],
    'view_manager' => [
        'doctype'                  => 'HTML5',
        'display_not_found_reason' => DEBUG_MODE,
        'display_exceptions'       => DEBUG_MODE,
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [],
        'template_path_stack' => [
            $this->getDir() . DS . 'view',
        ],
    ],
    'translator' => [
        'locale' => \Locale::getDefault(),
        'translation_file_patterns' => [[
            'type'     => 'Gettext',
            'base_dir' => $this->getDir() . DS . 'language',
            'pattern'  => '%s.mo',
        ]],
    ],
    'router' => [
        'routes' => [
            '#' => [
                'type' => 'literal',
                'options' => [
                    'route' => '#',
                ],
            ],
            'zfcuser' => [
                'type' => 'literal',
                'priority' => 1010,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => 'ScController.User',
                        'action' => 'index',
                    ],
                ],
            ],
            'home' => [
                'type' => 'segment',
                'priority' => 1000,
                'options' => [
                    'route' => '/[:content-name]',
                    'defaults' => [
                        'controller' => 'ScController.Front.Content',
                        'action' => 'index',
                    ],
                ],
            ],
            /* Installation.
             */
            'sc-install' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/install[/:process]',
                    'defaults' => [
                        'controller' => 'ScController.Installation.Default',
                        'action' => 'index',
                    ],
                ],
            ],
            /* The virtual route '/admin' with low priority.
             * It is used to create the child routes for managing widgets.
             * For example, to edit article using the route '/admin/article/edit'.
             */
            'sc-admin' => [
                'type' => 'literal',
                'priority' => 1000,
                'options' => [
                    'route' => '/admin',
                ],
                'child_routes' => [
                    /* Displays the content list.
                     */
                    'content-manager' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/content[/:pane][/:type][/:root][/:filter][/:parent][/:page][/:order_by]',
                            'defaults' => [
                                'controller' => 'ScController.Back.Manager',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    /* Search for content.
                     */
                    'content-search' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/search[/:pane][/:root][/:filter][/:page][/:order_by]',
                            'defaults' => [
                                'controller' => 'ScController.Back.Manager',
                                'action' => 'search',
                            ],
                        ],
                    ],
                    /* Category
                     */
                    'category' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/category',
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/add[/:parent]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.Category',
                                        'action' => 'add',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/edit[/:id]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.Category',
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    /* Article
                     */
                    'article' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/article',
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/add[/:parent]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.Article',
                                        'action' => 'add',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/edit[/:id]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.Article',
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    /* File
                     */
                    'file' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/file',
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/add[/:parent]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.File',
                                        'action' => 'add',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/edit[/:id]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.File',
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/delete[/:random]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.Garbage',
                                        'action' => 'collect',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    /* Themes
                     */
                    'themes' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/themes',
                            'defaults' => [
                                'controller' => 'ScController.Back.Theme',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    /* Layout
                     */
                    'layout' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/layout[/:theme]',
                            'defaults' => [
                                'controller' => 'ScController.Back.Layout',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    /* Widget
                     */
                    'widget' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/widget',
                        ],
                        'child_routes' => [
                            'configure' => [
                                'type' => 'segment',
                                    'options' => [
                                    'route' => '/configure[/:id]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.Widget',
                                        'action' => 'configure',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/edit[/:id]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.Widget',
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'visibility' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/visibility[/:widget_id][/:content_id][/:filter][/:page][/:order_by]',
                                    'defaults' => [
                                        'controller' => 'ScController.Back.WidgetVisibility',
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
