<?php

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
                'options' => [
                    'immutable' => true,
                    'display_name' => 'Content',
                    'description' => 'Editable content. For editing use the content manager.',
                ],
            ],
        ],
        'themes' => [
            'sc-default' => [
                'display_name' => 'ScContent Default',
                'description'  => 'The default theme with several regions.',
                'screenshot'   => 'sc-default/img/theme.png',
                'zfcuser_template_path' => $this->getDir() . str_replace('/', DS, '/view/sc-default'),
                'errors' => [
                    'layout' => 'sc-default/layout/frontend/index',
                    'template' => [
                        'exception' => 'sc-default/template/error/index',
                        '404'       => 'sc-default/template/error/404',
                    ],
                ],
                /* Frontend
                 */
                'frontend' => [
                    'layout' => 'sc-default/layout/frontend/index',
                    'regions' => [
                        'header' => [
                            'display_name' => 'Header',
                            'partial' => 'sc-default/layout/frontend/region/header',
                            'contains' => [],
                        ],
                        'content_top' => [
                            'display_name' => 'Content Top',
                            'partial' => 'sc-default/layout/frontend/region/content-top',
                            'contains' => [],
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
                                'login'
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
            'sc' => [
                'type' => 'segment',
                'priority' => 100,
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
                'priority' => -1,
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
                ],
            ],
        ],
    ],
];
