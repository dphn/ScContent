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
        'sc-validator.installation.phpini'
            => 'ScContent\Validator\Installation\Phpini',

        'sc-validator.installation.phpextension'
            => 'ScContent\Validator\Installation\Phpextension',

        'sc-validator.content.list.search.date.range'
            => 'ScContent\Validator\ContentList\SearchDateRange',

        'sc-validator.db.connection'
            => 'ScContent\Validator\Db\Connection',

        'sc-validator.file.name'
            => 'ScContent\Validator\File\FileName',

        'sc-validator.mapper.nesting'
            => 'ScContent\Validator\Mapper\NestingValidator',
    ),
    'factories' => array(
        'sc-validator.installation.autoload'
            => 'ScContent\Factory\Validator\Installation\AutoloadFactory',

        'sc-validator.installation.config'
            => 'ScContent\Factory\Validator\Installation\ConfigFactory',

        'sc-validator.installation.uploads'
            => 'ScContent\Factory\Validator\Installation\UploadsFactory',

        'sc-validator.installation.migration'
            => 'ScContent\Factory\Validator\Installation\MigrationFactory',

        'sc-validator.installation.assets'
            => 'ScContent\Factory\Validator\Installation\AssetsFactory',

        'sc-validator.installation.layout'
            => 'ScContent\Factory\Validator\Installation\LayoutFactory',

        'sc-validator.file.type'
            => 'ScContent\Factory\Validator\FileTypeFactory',
    ),
);
