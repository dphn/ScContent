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
            'base_dir'  => SCCONTENT_BASE_DIR . DS . 'language',
            'pattern'  => '%s.php',
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
                        'controller' => 'sc-controller.front.end',
                        'action'     => 'index',              
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
                        'controller' => 'sc-controller.installation.default',
                        'action'     => 'index',
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
                                'controller' => 'sc-controller.back.manager',
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
                                'controller' => 'sc-controller.back.manager',
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
                                        'controller' => 'sc-controller.back.category',
                                        'action' => 'add',                
                                    ),              
                                ),
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/edit[/:id]',
                                    'defaults' => array(
                                        'controller' => 'sc-controller.back.category',
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
                                        'controller' => 'sc-controller.back.article',
                                        'action' => 'add',                
                                    ),                
                                ),                
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/edit[/:id]',
                                    'defaults' => array(
                                        'controller' => 'sc-controller.back.article',
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
                                        'controller' => 'sc-controller.back.file',
                                        'action' => 'add',  
                                    ),                
                                ),                
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/edit[/:id]',
                                    'defaults' => array(
                                        'controller' => 'sc-controller.back.file',
                                        'action' => 'edit',                
                                    ),                
                                ),                
                            ),
                            'delete' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/delete[/:random]',
                                    'defaults' => array(
                                        'controller' => 'sc-controller.back.garbage',
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
                                'controller' => 'sc-controller.back.theme',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'layout' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/layout[/:theme]',
                            'defaults' => array(
                                'controller' => 'sc-controller.back.layout',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),             
            ),
        ),            
    ),
);
