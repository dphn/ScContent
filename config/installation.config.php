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
 * array(
 *     'sc' => array(
 *         'app_autoload_dir' => 'your_dir',
 *         // ...
 *         'installation' => array(
 *             // ... your options here ...
 *         ),
 *     ),
 * ),
 */

return array(
    /* Directories. Paths relative to the application root directory.
     */
    'app_autoload_dir' => '/config/autoload',
    'app_uploads_dir'  => '/public/uploads',
    'app_public_dir'   => '/public',

    // Address relative to applications basePath
    'app_uploads_src'  => 'uploads',

    'installation' => array(
        'layout'   => 'sc-default/layout/installation/index',
        'template' => 'sc-default/template/installation/index',
        'title'    => 'ScContent - Installation',
        'header'   => 'Installing a module ScContent',
        'redirect_on_success' => 'zfcuser/register',
        'steps' => array(
            '1: Pre-test' => array(
                'title' => 'Check the system requirements',
                'info' => 'Check the configuration settings and installed PHP extensions.',
                'chain' => array(
                    /* Checks the settings in the php.ini. If the values ​​do not
                     * meet the requirements, the installation terminates.
                     */
                    'phpini' => array(
                        'validator' => 'sc-validator.installation.phpini',
                        'controller'   => 'sc-controller.installation.requirements',
                        'action' => 'configuration',
                        'batch' => array(
                            array(
                                'name' => 'safe_mode',
                                'validation_type' => 'expect',
                                'validation_value' => array('Off', false),
                                'failure_message' => 'Safe mode must be disabled.',
                            ),
                            array(
                                'name' => 'magic_quotes_gpc',
                                'validation_type' => 'expect',
                                'validation_value' => array('Off', false),
                                'failure_message' => "Directive 'magic_quotes_gpc' should be disabled.",
                            ),
                            array(
                                'name' => 'magic_quotes_runtime',
                                'validation_type' => 'expect',
                                'validation_value' => array('Off', false),
                                'failure_message' => "Directive 'magic_quotes_runtime' should be disabled.",
                            ),
                            array(
                                'name' => 'magic_quotes_sybase',
                                'validation_type' => 'expect',
                                'validation_value' => array('Off', false),
                                'failure_message' => "Directive 'magic_quotes_sybase' should be disabled.",
                            ),
                            array(
                                'name' => 'memory_limit',
                                'validation_type' => 'greater_then',
                                'validation_value' => '64',
                                'no_limit' => '-1',
                                'failure_message' => 'Available memory must be greater than 64M.',
                            ),
                        ),
                    ),
                    /* Checks for the following php extensions.
                     * If php extension not exists, terminate installation.
                     */
                    'phpextension' => array(
                        'validator' => 'sc-validator.installation.phpextension',
                        'controller' => 'sc-controller.installation.requirements',
                        'action' => 'extension',
                        'batch' => array(
                            array(
                                'name' => 'fileinfo',
                                'information' => '<a href="http://www.php.net/manual/en/fileinfo.installation.php" target="_blank">http://www.php.net/manual/en/fileinfo.installation.php</a>',
                            ),
                        ),
                    ),
                ),
            ),
            1 => array(
                'title' => 'Setting the configuration',
                'info' => 'Installing the module configuration and setup parameters of the database connection.',
                'chain' => array(
                   /* Copies the various configuration files without changes
                    * from the specified module directory to the application
                    * autoload directory.
                    *
                    * If the file extension has a suffix *.dist, this
                    * suffix will removed.
                    */
                    'autoload' => array(
                        'validator' => 'sc-validator.installation.autoload',
                        'service'   => 'sc-service.installation.autoload',
                        'batch' => array(
                            'items' => array(
                                array(
                                    'source_module' => 'ScContent',
                                    'source_file' => '/data/installation/zfcuser.sc.v-0.1.3.002.local.php.dist',
                                ),
                            ),
                        ),
                    ),
                    /* Internal ScContent config installer.
                     * Updates the configuration settings for connecting to the
                     * database and saves the configuration in the configuration
                     * startup folder.
                     */
                    'scconfig' => array(
                        'validator' => 'sc-validator.installation.config',
                        'controller' => 'sc-controller.installation.config',
                        'action' => 'index',
                        'batch' => array(
                            'source_file' => '/data/installation/sccontent.sc.v-0.1.3.002.global.php.dist',
                        ),
                    ),
                ),
            ),
            2 => array(
                'title' => 'Database migration',
                'info' => 'Create and populate tables in the database.',
                'chain' => array(
                    /* Checks for each item presence of tables in the database.
                     * If the tables are not available, starts the process of
                     * the database migrating.
                     *
                     * The default 'sc-migration.schema' is alias
                     * to 'ScContent\Migration\Schema'
                     */
                    'migration' => array(
                        'validator' => 'sc-validator.installation.migration',
                        'service' => 'sc-service.installation.migration',
                        'batch' => array(
                            'items' => array(
                                array(
                                    'schema' => 'sc-migration.schema',
                                    'tables' => array(
                                        'sc_content',
                                        'sc_search',
                                        'sc_garbage',
                                        'sc_widgets',
                                        'sc_layout',
                                        'sc_users',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            3 => array(
                'title' => 'Assets installation',
                'info' => 'Installation of resources and the creation of the directory to files uploads.',
                'chain' => array(
                    /* Create uploads directory.
                     */
                    'uploads' => array(
                        'validator' => 'sc-validator.installation.uploads',
                        'service'   => 'sc-service.installation.uploads',
                    ),
                    /* Installs assets by extracting specified zip archive.
                     * To use this installer, specify the namespace of
                     * your module and relative path to the zip - archive.
                     */
                    'assets' => array(
                        'validator' => 'sc-validator.installation.assets',
                        'service'   => 'sc-service.installation.assets',
                        'batch' => array(
                            'items' => array(
                                array(
                                    /* 'validate_if_exists' - the path relative
                                     * to the "public" directory.
                                     */
                                    'validate_if_exists' => 'sc-default',
                                    /* 'version' - directory must contain a
                                     * "version" file with this name.
                                     */
                                    'version' => 'sc.v-0.1.3.002.version',
                                    'source' => array(
                                        'source_module' => 'ScContent',
                                        'source_zip' => '/data/public.zip',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            4 => array(
                'title' => 'Widgets installation',
                'info' => 'Register widgets for the current theme and existing content.',
                'chain' => array(
                    /* Record in the database all the widgets, specified in the
                     * configuration, for the existing content.
                     */
                    'layout' => array(
                        'validator' => 'sc-validator.installation.layout',
                        'service' => 'sc-service.installation.layout',
                    ),
                ),
            ),
        ),
    ),
);
