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
    'invokables' => [
        'ScService.DateTime'
            => 'ScContent\Service\ScDateTime',

        'ScListener.GuardExceptionStrategy'
            => 'ScContent\Listener\GuardExceptionStrategy',
    ],
    'abstract_factories' => [
        'ScContent\Factory\Migration\MigrationSchemaAbstractFactory',
    ],
    'factories' => [
        'ScNavigation.Backend'
            => 'ScContent\Factory\Navigation\BackendNavigationFactory',

        'ScDb.Adapter'
            => 'ScContent\Factory\Db\AdapterServiceFactory',

        'ScOptions.ModuleOptions'
            => 'ScContent\Factory\Options\ModuleFactory',

        'ScOptions.InstallationOptions'
            => 'ScContent\Factory\Options\InstallationFactory',

        'ScService.Dir'
            => 'ScContent\Factory\Service\DirFactory',

        'ScService.Localization'
            => 'ScContent\Factory\Service\LocalizationFactory',

        'ScService.FileTransfer'
            => 'ScContent\Factory\Service\FileTransferFactory',

        'ScService.FileTypesCatalog'
            => 'ScContent\Factory\Service\FileTypesCatalogFactory',

        'ScService.IdentityProvider'
            => 'ScContent\Factory\Service\IdentityProviderFactory',

        'ScService.RoleProvider'
            => 'ScContent\Factory\Service\RoleProviderFactory',

        'ScService.Installation.Autoload'
            => 'ScContent\Factory\Service\Installation\AutoloadFactory',

        'ScService.Installation.Uploads'
            => 'ScContent\Factory\Service\Installation\UploadsFactory',

        'ScService.Installation.Migration'
            => 'ScContent\Factory\Service\Installation\MigrationFactory',

        'ScService.Installation.Assets'
            => 'ScContent\Factory\Service\Installation\AssetsFactory',

        'ScService.Installation.Config'
            => 'ScContent\Factory\Service\Installation\ConfigFactory',

        'ScService.Installation.Layout'
            => 'ScContent\Factory\Service\Installation\LayoutFactory',

        'ScService.Installation.Roles'
            => 'ScContent\Factory\Service\Installation\RolesFactory',

        'ScService.Installation.Account'
            => 'ScContent\Factory\Service\Installation\AccountFactory',

        'ScService.Front.ContentService'
            => 'ScContent\Factory\Service\Front\ContentFactory',

        'ScService.Back.ContentListOptionsProvider'
            => 'ScContent\Factory\Service\Back\ContentListOptionsProviderFactory',

        'ScService.Back.ContentListProvider'
            => 'ScContent\Factory\Service\Back\ContentListProviderFactory',

        'ScService.Back.Category'
            => 'ScContent\Factory\Service\Back\CategoryFactory',

        'ScService.Back.Article'
            => 'ScContent\Factory\Service\Back\ArticleFactory',

        'ScService.Back.File'
            => 'ScContent\Factory\Service\Back\FileFactory',

        'ScService.Back.GarbageCollector'
            => 'ScContent\Factory\Service\Back\GarbageCollectorFactory',

        'ScService.Back.Layout'
            => 'ScContent\Factory\Service\Back\LayoutFactory',

        'ScService.Back.WidgetConfiguration'
            => 'ScContent\Factory\Service\Back\WidgetConfigurationFactory',

        'ScService.Back.WidgetVisibility'
            => 'ScContent\Factory\Service\Back\WidgetVisibilityFactory',

        'ScService.Theme.FrontendRegionsProxy'
            => 'ScContent\Factory\Service\Theme\FrontendRegionsProxyFactory',

        'ScMapper.Roles'
            => 'ScContent\Factory\Mapper\RolesMapperFactory',

        'ScMapper.Registration'
            => 'ScContent\Factory\Mapper\RegistrationMapperFactory',

        'ScMapper.Installation.Layout'
            => 'ScContent\Factory\Mapper\Installation\LayoutFactory',

        'ScMapper.Front.Content'
            => 'ScContent\Factory\Mapper\Front\ContentFactory',

        'ScMapper.Theme.FrontendLayoutMapper'
            => 'ScContent\Factory\Mapper\Theme\FrontendLayoutFactory',

        'ScMapper.Back.Content'
            => 'ScContent\Factory\Mapper\Back\ContentFactory',

        'ScMapper.Back.ContentList'
            => 'ScContent\Factory\Mapper\Back\ContentListMapperFactory',

        'ScMapper.Back.ContentSearch'
            => 'ScContent\Factory\Mapper\Back\ContentSearchMapperFactory',

        'ScMapper.Back.ContentListReorder'
            => 'ScContent\Factory\Mapper\Back\ContentListReorderMapperFactory',

        'ScMapper.Back.ContentListMove'
            => 'ScContent\Factory\Mapper\Back\ContentListMoveMapperFactory',

        'ScMapper.Back.ContentListToggleTrash'
            => 'ScContent\Factory\Mapper\Back\ContentListToggleTrashMapperFactory',

        'ScMapper.Back.ContentListDelete'
            => 'ScContent\Factory\Mapper\Back\ContentListDeleteMapperFactory',

        'ScMapper.Back.ContentListClean'
            => 'ScContent\Factory\Mapper\Back\ContentListCleanMapperFactory',

        'ScMapper.Back.Garbage'
            => 'ScContent\Factory\Mapper\Back\GarbageMapperFactory',

        'ScMapper.Back.LayoutListener'
            => 'ScContent\Factory\Mapper\Back\LayoutListenerMapperFactory',

        'ScMapper.Back.LayoutService'
            => 'ScContent\Factory\Mapper\Back\LayoutServiceMapperFactory',

        'ScMapper.Back.LayoutReorder'
            => 'ScContent\Factory\Mapper\Back\LayoutReorderMapperFactory',

        'ScMapper.Back.LayoutMove'
            => 'ScContent\Factory\Mapper\Back\LayoutMoveMapperFactory',

        'ScMapper.Back.Widget'
            => 'ScContent\Factory\Mapper\Back\WidgetMapperFactory',

        'ScMapper.Back.WidgetVisibilityList'
            => 'ScContent\Factory\Mapper\Back\WidgetVisibilityListMapperFactory',

        'ScMapper.Back.WidgetVisibilitySearch'
            => 'ScContent\Factory\Mapper\Back\WidgetVisibilitySearchMapperFactory',

        'ScMapper.Back.WidgetVisibilityChange'
            => 'ScContent\Factory\Mapper\Back\WidgetVisibilityChangeMapperFactory',

        'ScListener.UnauthorizedStrategy'
            => 'ScContent\Factory\Listener\UnauthorizedStrategyFactory',

        'ScListener.Installation.Inspector'
            => 'ScContent\Factory\Listener\Installation\InstallationInspectorFactory',

        'ScListener.ThumbnailListener'
            => 'ScContent\Factory\Listener\ThumbnailListenerFactory',

        'ScListener.Theme.InstallationStrategy'
            => 'ScContent\Factory\Listener\Theme\InstallationStrategyFactory',

        'ScListener.Theme.BackendStrategy'
            => 'ScContent\Factory\Listener\Theme\BackendStrategyFactory',

        'ScListener.Theme.FrontendStrategy'
            => 'ScContent\Factory\Listener\Theme\FrontendStrategyFactory',

        'ScListener.Theme.CommonStrategy'
            => 'ScContent\Factory\Listener\Theme\CommonStrategyFactory',

        'ScListener.Front.Registration'
            => 'ScContent\Factory\Listener\Front\RegistrationListenerFactory',

        'ScListener.Back.ContentListAggregate'
            => 'ScContent\Factory\Listener\Back\ContentListAggregateFactory',

        'ScListener.Back.ContentListReorder'
            => 'ScContent\Factory\Listener\Back\ContentListReorderFactory',

        'ScListener.Back.ContentListMove'
            => 'ScContent\Factory\Listener\Back\ContentListMoveFactory',

        'ScListener.Back.ContentListMoveToTrash'
            => 'ScContent\Factory\Listener\Back\ContentListMoveToTrashFactory',

        'ScListener.Back.ContentListRecoveryFromTrash'
            => 'ScContent\Factory\Listener\Back\ContentListRecoveryFromTrashFactory',

        'ScListener.Back.ContentListDelete'
            => 'ScContent\Factory\Listener\Back\ContentListDeleteFactory',

        'ScListener.Back.ContentListClean'
            => 'ScContent\Factory\Listener\Back\ContentListCleanFactory',

        'ScListener.Back.Layout'
            => 'ScContent\Factory\Listener\Back\LayoutFactory',

        'ScListener.Back.LayoutReorder'
            => 'ScContent\Factory\Listener\Back\LayoutReorderFactory',

        'ScListener.Back.LayoutMove'
            => 'ScContent\Factory\Listener\Back\LayoutMoveFactory',

        'ScListener.Back.Garbage'
            => 'ScContent\Factory\Listener\Back\GarbageFactory',

        'ScListener.Back.WidgetVisibilityChange'
            => 'ScContent\Factory\Listener\Back\WidgetVisibilityChangeFactory',
    ],
];
