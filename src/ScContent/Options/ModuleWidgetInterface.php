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
interface ModuleWidgetInterface
{
    /**
     * @param array $widgets
     */
    function setWidgets($widgets);

    /**
     * @return array
     */
    function getWidgets();

    /**
     * @param string $name
     * @retunr boolean
     */
    function widgetExists($name);
}
