<?php

return [
    'view_manager' => [
        'template_map' => [
            // @todo
        ],
        'template_path_stack' => [
            SCCONTENT_BASE_DIR . DS . 'view',
        ],
    ],
    'translator' => [
        'locale' => Locale::getDefault(),
        'translation_file_patterns' => [[
            'type' => 'phpArray',
            'base_dir' => SCCONTENT_BASE_DIR . DS . 'language',
            'pattern' => '%s.php',
        ]],
    ],
    'router' => [
        'routes' => [
            /* The route to the home page.
             */
            'sc' => [
                'type' => 'literal',
                'priority' => 100,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => 'ScController.Front.Frontend',
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
