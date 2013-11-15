<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Service\AbstractService,
    ScContent\Entity\Back\Theme,
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
            $name = $moduleOptions->getBackendThemeName();
        }

        if (! $moduleOptions->themeExists($name)) {
            throw new RuntimeException(sprintf(
                $translator->translate("Unknown theme '%s'."),
                $moduleOptions->getThemeDisplayName($name)
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
