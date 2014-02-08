<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Service\AbstractService,
    ScContent\Entity\Back\Theme,
    ScContent\Entity\Widget,
    ScContent\Options\ModuleOptions,
    ScContent\Mapper\Back\LayoutServiceMapper,
    ScContent\Exception\RuntimeException,
    ScContent\Exception\IoCException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutService extends AbstractService
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Mapper\Back\LayoutServiceMapper
     */
    protected $layoutMapper;

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
            throw new IoCException(
                'The module options were not set.'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param ScContent\Mapper\Back\LayoutServiceMapper $mapper
     * @return void
     */
    public function setLayoutMapper(LayoutServiceMapper $mapper)
    {
        $this->layoutMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\LayoutServiceMapper
     */
    public function getLayoutMapper()
    {
        if (! $this->layoutMapper instanceof LayoutServiceMapper) {
            throw new IoException(
                'The layout mapper was not set.'
            );
        }
        return $this->layoutMapper;
    }

    /**
     * @return array
     */
    public function getControlSet()
    {
        $moduleOptions = $this->getModuleOptions();
        $widgets = $moduleOptions->getWidgets();
        $controlSet = [];
        foreach ($widgets as $name => $widget) {
            if (! isset($widget['options']['unique'])
                || ! $widget['options']['unique']
            ) {
                $controlSet[$name] = isset($widget['display_name'])
                                   ? $widget['display_name']
                                   : $name;
            }
        }
        return $controlSet;
    }

    /**
     * @param string $theme Theme name
     * @param string $name Widget name
     * @throws ScContent\Exception\RuntimeException
     * @return ScContent\Entity\Widget
     */
    public function addWidget($theme, $name)
    {
        $translator = $this->getTranslator();
        $moduleOptions = $this->getModuleOptions();
        $mapper = $this->getLayoutMapper();

        if (! $moduleOptions->themeExists($theme)) {
            throw new RuntimeException(sprintf(
                $translator->translate("Unknown theme '%s'."),
                $theme
            ));
        }

        if (! $this->isThemeEnabled($theme)) {
            throw new RuntimeException(sprintf(
                $translator->translate("Theme '%s' was not enabled."),
                $moduleOptions->getThemeDisplayName($theme)
            ));
        }

        if (! $moduleOptions->widgetExists($name)) {
            throw new RuntimeException(sprintf(
                $translator->translate("Unknown widget '%s'."),
                $name
            ));
        }

        $widgetConfig = $moduleOptions->getWidgetByName($name);
        if (isset($widgetConfig['options']['unique'])
            && $widgetConfig['options']['unique']
        ) {
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Unable to add widget. The widget '%s' is unique."
                ),
                $name
            ));
        }

        $widget = new Widget();
        $widget->exchangeArray($widgetConfig);
        $widget->setTheme($theme);
        $widget->setRegion('none');
        $widget->setName($name);

        $mapper->install($widget);

        return $widget;
    }

    /**
     * @param integer $id
     * @return void
     */
    public function deleteWidget($id)
    {
        $mapper = $this->getLayoutMapper();
        $translator = $this->getTranslator();
        $moduleOptions = $this->getModuleOptions();

        $widgetMeta = $mapper->findMetaById($id);
        if (empty($widgetMeta)) {
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "The widget with identifier '%s' was not found."
                ),
                $id
            ));
        }

        $widget = $moduleOptions->getWidgetByName($widgetMeta['name']);
        if (isset($widget['options']['unique'])
            && $widget['options']['unique']
        ) {
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Unable to delete widget. The widget '%s' is unique."
                ),
                $moduleOptions->getWidgetDisplayName($widgetMeta['name'])
            ));
        }
        $mapper->deleteItem($id);
    }

    /**
     * @param string $name
     * @throw ScContent\Exception\RuntimeException
     * @return ScContent\Entity\Back\Theme
     */
    public function getTheme($name = null)
    {
        $translator = $this->getTranslator();
        $moduleOptions = $this->getModuleOptions();
        $theme = new Theme();
        if (empty($name)) {
            $name = $moduleOptions->getFrontendThemeName();
        }

        if (! $moduleOptions->themeExists($name)) {
            throw new RuntimeException(sprintf(
                $translator->translate("Unknown theme '%s'."),
                $name
            ));
        }

        if (! $this->isThemeEnabled($name)) {
            throw new RuntimeException(sprintf(
                $translator->translate("Theme '%s' was not enabled."),
                $moduleOptions->getThemeDisplayName($name)
            ));
        }
        $options = $moduleOptions->getThemeByName($name);
        $theme->exchangeArray($options);
        $theme->setName($name);
        return $theme;
    }

    /**
     * @param string $theme
     * @return boolean
     */
    public function isThemeEnabled($theme)
    {
        $mapper = $this->getLayoutMapper();
        $themes = $mapper->findExistingThemes();
        return in_array($theme, $themes);
    }

    /**
     * @param string $theme
     * @return ScContent\Entity\Back\Regions
     */
    public function getRegions($theme)
    {
        $mapper = $this->getLayoutMapper();
        $regions = $mapper->findRegions($theme);
        return $regions;
    }
}
