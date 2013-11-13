<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
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
     * @param string $name
     * @return void
     */
    function setFrontendThemeName($name);

    /**
     * @return string
     */
    function getFrontendThemeName();

    /**
     * @param string $name
     * @return void
     */
    function setBackendThemeName($name);

    /**
     * @return string
     */
    function getBackendThemeName();

    /**
     * @param array $themes
     * @return void
     */
    function setThemes($themes);

    /**
     * @return array
     */
    function getThemes();

    /**
     * @return array
     */
    function getFrontendTheme();

    /**
     * @return array
     */
    function getBackendTheme();
}
