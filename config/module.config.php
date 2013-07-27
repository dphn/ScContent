<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',                
        ),                
    ),
    'router' => array(
        'routes' => array(
            'sc' => array(
                'type' => 'literal',
                'priority' => 100,
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'sc-content',
                        'action' => 'index',              
                    ),                
                ),                
            ),                
        ),            
    ),
);