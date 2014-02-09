<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Installation;

use ScContent\Mapper\Installation\LayoutMapper,
    ScContent\Entity\WidgetInterface,
    ScContent\Options\ModuleOptions,
    //
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

    /**#@+
     * @const string
     */
    const UnknownThemeNotEnabled      = 'Unknown theme was not enabled';
    const MissingRegionsSpecification = 'Missing specification of regions';
    const FailedEnableTheme           = 'Failed to enable theme';
    /**#@-*/

    /**
     * @var string[string] Messages
     *                     <code>(string) message [(string) message identifier]</code>
     */
    protected $errorMessages = [
        self::UnknownThemeNotEnabled
            => 'Unknown theme %s was not enabled. Missing configuration of theme.',

        self::MissingRegionsSpecification
            => 'Failed to enable theme %s. Missing specification of the regions.',

        self::FailedEnableTheme
            => 'An unexpected error occurred. Failed to enable theme %s.',

        self::UnableDisableActiveTheme
            => 'Unable to disable active theme %s.',

        self::DisableNotEnabledTheme
            => 'Unable to disable theme %s. Theme is not enabled.',

        self::FailedDisableTheme
            => 'An unexpected error occurred. Failed to disable theme %s.',
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
     * @param ScContent\Entity\WidgetInterface $entity
     * @return void
     */
    public function setWidgetEntity(WidgetInterface $entity)
    {
        $this->widgetEntity = $entity;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Entity\WidgetInterface
     */
    public function getWidgetEntity()
    {
        if (! $this->widgetEntity instanceof WidgetInterface) {
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
        return $this->enableTheme($frontendThemeName);
    }

    /**
     * @param string $theme Theme name
     * @return boolean
     */
    public function enableTheme($theme)
    {
        $options = $this->getModuleOptions();
        if (! $options->themeExists($theme)) {
            $this->setValue($theme)->error(self::UnknownThemeNotEnabled);
            return false;
        }
        $mapper = $this->getLayoutMapper();
        $widgets = $options->getWidgets();
        $availableWidgets = array_keys($widgets);
        $widgetPrototype = $this->getWidgetEntity();
        $widgetPrototype->setTheme($theme);

        $regions = $this->getRegions($theme);
        if (empty($regions)) {
            $this->setValue($theme)->error(self::MissingRegionsSpecification);
            return false;
        }
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
            $this->setValue($theme)->error(self::FailedEnableTheme);
            return false;
        }
        return true;
    }

    /**
     * @return string[]
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
     * @return string[string] Regions
     *                        <code>(string) region name [(string) widget name]</code>
     */
    public function getRegions($themeName)
    {
        $translator = $this->getTranslator();
        $options = $this->getModuleOptions();
        $theme = $options->getThemeByName($themeName);
        $map = [];

        if (! isset($theme['frontend']['regions'])
            || ! is_array($theme['frontend']['regions'])
            || empty($theme['frontend']['regions'])
        ) {
            return $map;
        }
        $regions = $theme['frontend']['regions'];
        $widgets = array_keys($options->getWidgets());

        foreach ($regions as $regionName => $regionOptions) {
            if (isset($regionOptions['contains'])
                && is_array($regionOptions['contains'])
            ) {
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
