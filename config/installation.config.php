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
    'installation' => [
        'layout'   => 'sc-default/layout/installation/index',
        'template' => 'sc-default/template/installation/index',
        'title'    => 'Installing a module ScContent',
        'redirect_on_success' => 'sc-admin/content-manager',
        'brand' => '<img alt="Sc" src="data:image/png;base64,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" />',
        'steps' => [
            /* ________________________________________________________________
             */
            'Pre-test' => [
                'header' => 'Check the system requirements.',
                'info' => 'Check the configuration settings and installed PHP extensions.',
                'chain' => [
                    /* Checks the settings in the php.ini. If the values ​​do not
                     * meet the requirements, the installation terminates.
                     */
                    'phpini' => [
                        'validator' => 'ScValidator.Installation.PhpIni',
                        'controller'   => 'ScController.Installation.Requirements',
                        'action' => 'configuration',
                        'batch' => [
                            [
                                'name' => 'safe_mode',
                                'validation_type' => 'expect',
                                'validation_value' => ['Off', false],
                                'failure_message' => 'Safe mode must be disabled.',
                            ],
                            [
                                'name' => 'magic_quotes_gpc',
                                'validation_type' => 'expect',
                                'validation_value' => ['Off', false],
                                'failure_message' => "Directive 'magic_quotes_gpc' should be disabled.",
                            ],
                            [
                                'name' => 'magic_quotes_runtime',
                                'validation_type' => 'expect',
                                'validation_value' => ['Off', false],
                                'failure_message' => "Directive 'magic_quotes_runtime' should be disabled.",
                            ],
                            [
                                'name' => 'magic_quotes_sybase',
                                'validation_type' => 'expect',
                                'validation_value' => ['Off', false],
                                'failure_message' => "Directive 'magic_quotes_sybase' should be disabled.",
                            ],
                            [
                                'name' => 'memory_limit',
                                'validation_type' => 'greater_then',
                                'validation_value' => '64',
                                'no_limit' => '-1',
                                'failure_message' => 'Available memory must be greater than 64M.',
                            ],
                        ],
                    ],
                    /* Checks for the following php extensions.
                     * If php extension not exists, terminate installation.
                     */
                    'phpextension' => [
                        'validator' => 'ScValidator.Installation.PhpExtension',
                        'controller' => 'ScController.Installation.Requirements',
                        'action' => 'extension',
                        'batch' => [
                            [
                                'name' => 'fileinfo',
                                'information' => '<a href="http://www.php.net/manual/en/fileinfo.installation.php" target="_blank">http://www.php.net/manual/en/fileinfo.installation.php</a>',
                            ],
                        ],
                    ],
                ],
            ],
            /* ________________________________________________________________
             */
            1 => [
                'header' => 'Setting the configuration.',
                'info' => 'Installing the module configuration and setup parameters of the database connection.',
                'chain' => [
                   /* Copies the various configuration files without changes
                    * from the specified module directory to the application
                    * autoload directory.
                    *
                    * If the file extension has a suffix *.dist, this
                    * suffix will removed.
                    */
                    'autoload' => [
                        'validator' => 'ScValidator.Installation.Autoload',
                        'service'   => 'ScService.Installation.Autoload',
                        'batch' => [
                            'items' => [
                                [
                                    'source_module' => 'ScContent',
                                    'source_file' => '/data/installation/zfcuser.sc.v-0.1.3.004.local.php.dist',
                                    'old_files_mask' => 'zfcuser.sc.v-*',
                                ],
                            ],
                        ],
                    ],
                    /* Internal ScContent config installer.
                     * Updates the configuration settings for connecting to the
                     * database and saves the configuration in the configuration
                     * startup folder.
                     */
                    'scconfig' => [
                        'validator' => 'ScValidator.Installation.Config',
                        'controller' => 'ScController.Installation.Config',
                        'action' => 'index',
                        'batch' => [
                            'source_file' => '/data/installation/sccontent.sc.v-0.1.3.006.global.php.dist',
                            'old_files_mask' => 'sccontent.sc.v-*',
                        ],
                    ],
                ],
            ],
            /* ________________________________________________________________
             */
            2 => [
                'header' => 'Database migration.',
                'info' => 'Create and populate tables in the database.',
                'chain' => [
                    /* Checks for each item presence of tables in the database.
                     * If the tables are not available, starts the process of
                     * the database migrating.
                     *
                     * The default 'ScMigration.Schema' is alias
                     * to 'ScContent\Migration\Schema'
                     */
                    'migration' => [
                        'validator' => 'ScValidator.Installation.Migration',
                        'service' => 'ScService.Installation.Migration',
                        'batch' => [
                            'items' => [
                                [
                                    'schema' => 'ScContent.Migration.Schema',
                                    'tables' => [
                                        'sc_content',
                                        'sc_search',
                                        'sc_garbage',
                                        'sc_widgets',
                                        'sc_layout',
                                        'sc_users',
                                        'sc_roles',
                                        'sc_roles_linker',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            /* ________________________________________________________________
             */
            3 => [
                'header' => 'Assets installation.',
                'info' => 'Installation of resources and the creation of the directory to files uploads.',
                'chain' => [
                    /* Create uploads directory.
                     */
                    'uploads' => [
                        'validator' => 'ScValidator.Installation.Uploads',
                        'service'   => 'ScService.Installation.Uploads',
                    ],
                    /* Installs assets by extracting specified zip archive.
                     * To use this installer, specify the namespace of
                     * your module and relative path to the zip - archive.
                     */
                    'assets' => [
                        'validator' => 'ScValidator.Installation.Assets',
                        'service'   => 'ScService.Installation.Assets',
                        'batch' => [
                            'items' => [
                                [
                                    /* 'validate_if_exists' - the path relative
                                     * to the "public" directory.
                                     */
                                    'validate_if_exists' => 'sc-default',
                                    /* 'version' - directory must contain a
                                     * "version" file with this name.
                                     */
                                    'version' => 'sc.v-0.1.3.008.version',
                                    'source' => [
                                        'source_module' => 'ScContent',
                                        'source_zip' => '/data/public.zip',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            /* ________________________________________________________________
             */
            '4' => [
                'header' => 'Widgets installation.',
                'info' => 'Register widgets for the current theme and existing content.',
                'chain' => [
                    'layout' => [
                        'validator' => 'ScValidator.Installation.Layout',
                        'service' => 'ScService.Installation.Layout',
                    ],
                ],
            ],
            /* ________________________________________________________________
             */
            '5' => [
                'header' => 'Create your account.',
                'info' => 'Create an access control system and administrator account.',
                'chain' => [
                    'roles' => [
                        'validator' => 'ScValidator.Installation.Roles',
                        'service' => 'ScService.Installation.Roles',
                        'batch' => [
                            [
                                'role_id'    => 'guest',
                                'is_default' => true,
                                'parent_id'  => null,
                                'route' => 'home',
                            ],
                            [
                                'role_id'    => 'user',
                                'is_default' => false,
                                'parent_id'  => 'guest',
                                'route' => 'home',
                            ],
                            [
                                'role_id'    => 'admin',
                                'is_default' => false,
                                'parent_id'  => null,
                                'route' => 'sc-admin/content-manager',
                            ],
                        ],
                    ],
                    'account' => [
                        'validator' => 'ScValidator.Installation.Account',
                        'controller' => 'ScController.Installation.Account',
                        'action' => 'index',
                    ],
                    'guard' => [
                        'validator' => 'ScValidator.Installation.Autoload',
                        'service'   => 'ScService.Installation.Autoload',
                        'batch' => [
                            'source_module' => 'ScContent',
                            'source_file' => '/data/installation/bjyauthorize.sc.v-0.1.3.005.local.php.dist',
                            'old_files_mask' => 'bjyauthorize.sc.v-*',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
