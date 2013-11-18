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
        'sc-service.datetime'
            => 'ScContent\Service\ScDateTime',
    ),
    'factories' => array(
        'sc-db.adapter'
            => 'ScContent\Factory\Db\AdapterServiceFactory',

        'sc-migration.schema'
            => 'ScContent\Factory\Migration\SchemaFactory',

        'sc-options.module'
            => 'ScContent\Factory\Options\ModuleFactory',

        'sc-service.dir'
            => 'ScContent\Factory\Service\DirFactory',

        'sc-service.l10n'
            => 'ScContent\Factory\Service\LocalizationFactory',

        'sc-service.file.transfer'
            => 'ScContent\Factory\Service\FileTransferFactory',

        'sc-service.file.types.catalog'
            => 'ScContent\Factory\Service\FileTypesCatalogFactory',

        'sc-service.installation.inspector'
            => 'ScContent\Factory\Service\Installation\InstallationInspectorFactory',

        'sc-service.installation.autoload'
            => 'ScContent\Factory\Service\Installation\AutoloadFactory',

        'sc-service.installation.uploads'
            => 'ScContent\Factory\Service\Installation\UploadsFactory',

        'sc-service.installation.migration'
            => 'ScContent\Factory\Service\Installation\MigrationFactory',

        'sc-service.installation.assets'
            => 'ScContent\Factory\Service\Installation\AssetsFactory',

        'sc-service.installation.config'
            => 'ScContent\Factory\Service\Installation\ConfigFactory',

        'sc-service.installation.layout'
            => 'ScContent\Factory\Service\Installation\LayoutFactory',

        'sc-service.back.content.list.options.provider'
            => 'ScContent\Factory\Service\Back\ContentListOptionsProviderFactory',

        'sc-service.back.content.list.provider'
            => 'ScContent\Factory\Service\Back\ContentListProviderFactory',

        'sc-service.back.category'
            => 'ScContent\Factory\Service\Back\CategoryFactory',

        'sc-service.back.article'
            => 'ScContent\Factory\Service\Back\ArticleFactory',

        'sc-service.back.file'
            => 'ScContent\Factory\Service\Back\FileFactory',

        'sc-service.back.garbage.collector'
            => 'ScContent\Factory\Service\Back\GarbageCollectorFactory',

        'sc-service.back.layout'
            => 'ScContent\Factory\Service\Back\LayoutFactory',

        'sc-mapper.installation.layout'
            => 'ScContent\Factory\Mapper\Installation\LayoutFactory',

        'sc-mapper.back.content'
            => 'ScContent\Factory\Mapper\Back\ContentFactory',

        'sc-mapper.back.content.list'
            => 'ScContent\Factory\Mapper\Back\ContentListMapperFactory',

        'sc-mapper.back.content.search'
            => 'ScContent\Factory\Mapper\Back\ContentSearchMapperFactory',

        'sc-mapper.back.content.list.reorder'
            => 'ScContent\Factory\Mapper\Back\ContentListReorderMapperFactory',

        'sc-mapper.back.content.list.move'
            => 'ScContent\Factory\Mapper\Back\ContentListMoveMapperFactory',

        'sc-mapper.back.content.list.toggle.trash'
            => 'ScContent\Factory\Mapper\Back\ContentListToggleTrashMapperFactory',

        'sc-mapper.back.content.list.delete'
            => 'ScContent\Factory\Mapper\Back\ContentListDeleteMapperFactory',

        'sc-mapper.back.content.list.clean'
            => 'ScContent\Factory\Mapper\Back\ContentListCleanMapperFactory',

        'sc-mapper.back.garbage'
            => 'ScContent\Factory\Mapper\Back\GarbageMapperFactory',

        'sc-mapper.back.layout.listener'
            => 'ScContent\Factory\Mapper\Back\LayoutListenerMapperFactory',

        'sc-mapper.back.layout.service'
            => 'ScContent\Factory\Mapper\Back\LayoutServiceMapperFactory',

        'sc-mapper.back.layout.reorder'
            => 'ScContent\Factory\Mapper\Back\LayoutReorderMapperFactory',

        'sc-mapper.back.layout.move'
            => 'ScContent\Factory\Mapper\Back\LayoutMoveMapperFactory',

        'sc-listener.theme.installation'
            => 'ScContent\Factory\Listener\Theme\InstallationFactory',

        'sc-listener.theme.backend'
            => 'ScContent\Factory\Listener\Theme\BackendFactory',

        'sc-listener.theme.frontend'
            => 'ScContent\Factory\Listener\Theme\FrontendFactory',

        'sc-listener.back.content.list.aggregate'
            => 'ScContent\Factory\Listener\Back\ContentListAggregateFactory',

        'sc-listener.back.content.list.reorder'
            => 'ScContent\Factory\Listener\Back\ContentListReorderFactory',

        'sc-listener.back.content.list.move'
            => 'ScContent\Factory\Listener\Back\ContentListMoveFactory',

        'sc-listener.back.content.list.move.to.trash'
            => 'ScContent\Factory\Listener\Back\ContentListMoveToTrashFactory',

        'sc-listener.back.content.list.recovery.from.trash'
            => 'ScContent\Factory\Listener\Back\ContentListRecoveryFromTrashFactory',

        'sc-listener.back.content.list.delete'
            => 'ScContent\Factory\Listener\Back\ContentListDeleteFactory',

        'sc-listener.back.content.list.clean'
            => 'ScContent\Factory\Listener\Back\ContentListCleanFactory',

        'sc-listener.back.layout'
            => 'ScContent\Factory\Listener\Back\LayoutFactory',

        'sc-listener.back.layout.reorder'
            => 'ScContent\Factory\Listener\Back\LayoutReorderFactory',

        'sc-listener.back.layout.move'
            => 'ScContent\Factory\Listener\Back\LayoutMoveFactory',

        'sc-listener.back.garbage'
            => 'ScContent\Factory\Listener\Back\GarbageFactory',
    ),
);
