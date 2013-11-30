<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 *
 *
 * You can use this feature to automate
 * the installation of YOUR APPLICATION.
 *
 * You can change this options directly or create global options:
 * [
 *     'sc' => [
 *
 *         'app_autoload_dir' => 'your_dir',
 *
 *         // ...
 *
 *         'installation' => [
 *             // ... your options here ...
 *         ],
 *     ],
 * ],
 */

return [
    /* Directories. Paths relative to the application root directory.
     */
    'app_autoload_dir' => '/config/autoload',
    'app_uploads_dir'  => '/public/uploads',
    'app_public_dir'   => '/public',

    // Address relative to applications basePath
    'app_uploads_src'  => 'uploads',

    'installation' => [
        'layout'   => 'sc-default/layout/installation/index',
        'template' => 'sc-default/template/installation/index',
        'title'    => 'ScContent - Installation',
        'header'   => 'Installing a module ScContent',
        'redirect_on_success' => 'zfcuser/register',
        'steps' => [
            /* ________________________________________________________________
             */
            'Pre-test' => [
                'title' => 'Check the system requirements',
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
                'title' => 'Setting the configuration',
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
                                    'source_file' => '/data/installation/zfcuser.sc.v-0.1.3.002.local.php.dist',
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
                            'source_file' => '/data/installation/sccontent.sc.v-0.1.3.003.global.php.dist',
                            'old_files_mask' => 'sccontent.sc.v-*',
                        ],
                    ],
                ],
            ],
            /* ________________________________________________________________
             */
            2 => [
                'title' => 'Database migration',
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
                'title' => 'Assets installation',
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
                                    'version' => 'sc.v-0.1.3.002.version',
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
            4 => [
                'title' => 'Widgets installation',
                'info' => 'Register widgets for the current theme and existing content.',
                'chain' => [
                    /* Record in the database all the widgets, specified in the
                     * configuration, for the existing content.
                     */
                    'layout' => [
                        'validator' => 'ScValidator.Installation.Layout',
                        'service' => 'ScService.Installation.Layout',
                    ],
                ],
            ],

        ],
    ],
];
