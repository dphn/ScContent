<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Installation;

use ScContent\Mapper\Installation\LayoutMapper,
    ScContent\Entity\Installation\WidgetEntity,
    ScContent\Options\ModuleOptions,
    //
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutService extends AbstractInstallationService
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Mapper\Installation\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * @var ScContent\Entity\Installation\WidgetEntity
     */
    protected $widgetEntity;

    /**
     * @const string
     */
    const WidgetsInstallationFailed = 'Widgets installation failed';

    /**
     * @var array
     */
    protected $errorMessages = [
        self::WidgetsInstallationFailed => 'Widgets installation failed.'
    ];

    /**
     * @param ScContent\Options\ModuleOptions $options
     * @return void
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            throw new IoCException('The module options was not set.');
        }
        return $this->moduleOptions;
    }

    /**
     * @param ScContent\Mapper\Installation\LayoutMapper $mapper
     * @return void
     */
    public function setLayoutMapper(LayoutMapper $mapper)
    {
        $this->layoutMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Installation\LayoutMapper
     */
    public function getLayoutMapper()
    {
        if (! $this->layoutMapper instanceof LayoutMapper) {
            throw new IoCException('The layout mapper was not set.');
        }
        return $this->layoutMapper;
    }

    /**
     * @param ScContent\Entity\Installation\WidgetEntity $entity
     * @return void
     */
    public function setWidgetEntity(WidgetEntity $entity)
    {
        $this->widgetEntity = $entity;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Entity\Installation\WidgetEntity
     */
    public function getWidgetEntity()
    {
        if (! $this->widgetEntity instanceof WidgetEntity) {
            throw new IoCException('The widget entity was not set.');
        }
        return $this->widgetEntity;
    }

    /**
     * @param null $options Not used
     * @return boolean
     */
    public function process($options = null)
    {
        $options = $this->getModuleOptions();
        $frontendThemeName = $options->getFrontendThemeName();
        return $this->install($frontendThemeName);
    }

    /**
     * @param string $theme
     * @throws ScContent\Exception\InvalidArgumentException
     * @return boolean
     */
    public function install($theme)
    {
        $options = $this->getModuleOptions();
        $themes = $options->getThemes();
        if (! isset($themes[$theme])) {
            throw new InvalidArgumentException(sprintf(
                "Unknown theme '%s'.", $theme
            ));
        }
        $mapper = $this->getLayoutMapper();
        $widgets = $options->getWidgets();
        $availableWidgets = array_keys($widgets);
        $widgetPrototype = $this->getWidgetEntity();
        $widgetPrototype->setTheme($theme);
        $regions = $this->getRegions($theme);
        try {
            $installedWidgets = $mapper->findExistingWidgets(
                $theme,
                $availableWidgets
            );
            $notInstalledWidgets = array_diff(
                $availableWidgets,
                $installedWidgets
            );
            foreach ($notInstalledWidgets as $widgetName) {
                $widget = clone ($widgetPrototype);
                $widget->setName($widgetName);
                $widget->setRegion($regions[$widgetName]);
                $widget->exchangeArray($widgets[$widgetName]);
                @set_time_limit(30);
                $mapper->install($widget);
            }
        } catch (Exception $e) {
            $this->error(self::WidgetsInstallationFailed);
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getRegisteredThemes()
    {
        $mapper = $this->getLayoutMapper();
        return $mapper->findExistingThemes();
    }

    /**
     * Get the existing regions from the module options.
     *
     * @param string $themeName
     * @return array Array in format <code>array('widget_name' =>
     *         'region_name')</code>
     */
    public function getRegions($themeName)
    {
        $options = $this->getModuleOptions();
        $themes = $options->getThemes();
        $theme = $themes[$themeName];
        $regions = $theme['frontend']['regions'];
        $widgets = array_keys($options->getWidgets());

        $map = [];
        foreach ($regions as $regionName => $regionOptions) {
            if (isset($regionOptions['contains']) &&
                 is_array($regionOptions['contains'])) {
                $container = $regionOptions['contains'];
                foreach ($container as $widget) {
                    $map[$widget] = $regionName;
                }
            }
        }
        foreach ($widgets as $widget) {
            if (! array_key_exists($widget, $map)) {
                $map[$widget] = 'none';
            }
        }
        return $map;
    }
}
