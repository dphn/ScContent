<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Options;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
interface ModuleThemeInterface
{
    /**
     * @param  string $name
     * @return void
     */
    function setFrontendThemeName($name);

    /**
     * @return string
     */
    function getFrontendThemeName();

    /**
     * @param  string $name
     * @return void
     */
    function setBackendThemeName($name);

    /**
     * @return string
     */
    function getBackendThemeName();

    /**
     * @param  array $themes
     * @return void
     */
    function setThemes($themes);

    /**
     * @return array
     */
    function getThemes();

    /**
     * @param  string $name
     * @throws \ScContent\Exception\DomainException
     * @return array
     */
    function getThemeByName($name);

    /**
     * @param  string $name
     * @return boolean
     */
    function themeExists($name);

    /**
     * @param  string $theme
     * @param  string $name
     * @return boolean
     */
    function regionExists($theme, $name);

    /**
     * @param  string $name
     * @throws \ScContent\Exception\DomainException
     * @return string
     */
    function getThemeDisplayName($name);

    /**
     * @throws \ScContent\Exception\DomainException
     * @return array
     */
    function getFrontendTheme();

    /**
     * @throws \ScContent\Exception\DomainException
     * @return array
     */
    function getBackendTheme();
}
