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

use ScContent\Options\ModuleOptions,
    ScContent\Mapper\Back\LayoutServiceMapper,
    ScContent\Exception\IoCException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutService
{
    /**
     * @var ScContent\Mapper\Back\LayoutServiceMapper
     */
    protected $layoutMapper;

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
     * @param string $theme Theme name
     * @return boolean
     */
    public function isThemeRegistered($theme)
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
