<?php

return array(
    'view_manager' => array(
        'template_map' => array(
            // @todo
        ),
        'template_path_stack' => array(
            SCCONTENT_BASE_DIR . DS . 'view',
        ),
    ),
    'translator' => array(
        'locale' => Locale::getDefault(),
        'translation_file_patterns' => array(array(
            'type' => 'phpArray',
            'base_dir' => SCCONTENT_BASE_DIR . DS . 'language',
            'pattern' => '%s.php',
        )),
    ),
    'router' => array(
        'routes' => array(
            /* The route to the home page.
             */
            'sc' => array(
                'type' => 'literal',
                'priority' => 100,
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'ScController.Front.Frontend',
                        'action' => 'index',
                    ),
                ),
            ),
            /* Installation.
             */
            'sc-install' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/install[/:process]',
                    'defaults' => array(
                        'controller' => 'ScController.Installation.Default',
                        'action' => 'index',
                    ),
                ),
            ),
            /* The virtual route '/admin' with low priority.
             * It is used to create the child routes for managing widgets.
             * For example, to edit article using the route '/admin/article/edit'.
             */
            'sc-admin' => array(
                'type' => 'literal',
                'priority' => -1,
                'options' => array(
                    'route' => '/admin',
                ),
                'child_routes' => array(
                    /* Displays the content list.
                     */
                    'content-manager' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/content[/:pane][/:type][/:root][/:filter][/:parent][/:page][/:order_by]',
                            'defaults' => array(
                                'controller' => 'ScController.Back.Manager',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Search for content.
                     */
                    'content-search' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/search[/:pane][/:root][/:filter][/:page][/:order_by]',
                            'defaults' => array(
                                'controller' => 'ScController.Back.Manager',
                                'action' => 'search',
                            ),
                        )
                    ),
                    /* Category
                     */
                    'category' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/category',
                        ),
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/add[/:parent]',
                                    'defaults' => array(
                                        'controller' => 'ScController.Back.Category',
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/edit[/:id]',
                                    'defaults' => array(
                                        'controller' => 'ScController.Back.Category',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    /* Article
                     */
                    'article' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/article',
                        ),
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/add[/:parent]',
                                    'defaults' => array(
                                        'controller' => 'ScController.Back.Article',
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/edit[/:id]',
                                    'defaults' => array(
                                        'controller' => 'ScController.Back.Article',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    /* File
                     */
                    'file' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/file',
                        ),
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/add[/:parent]',
                                    'defaults' => array(
                                        'controller' => 'ScController.Back.File',
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/edit[/:id]',
                                    'defaults' => array(
                                        'controller' => 'ScController.Back.File',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/delete[/:random]',
                                    'defaults' => array(
                                        'controller' => 'ScController.Back.Garbage',
                                        'action' => 'collect',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'themes' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/themes',
                            'defaults' => array(
                                'controller' => 'ScController.Back.Theme',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'layout' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/layout[/:theme]',
                            'defaults' => array(
                                'controller' => 'ScController.Back.Layout',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
