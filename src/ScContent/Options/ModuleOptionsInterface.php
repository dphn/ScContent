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
interface ModuleOptionsInterface extends
    ModuleThemeInterface,
    ModuleWidgetInterface,
    ServiceBackContentInterface,
    ServiceDirInterface
{
    /**
     * @param array $db
     */
    function setDb($db);

    /**
     * @return array
     */
    function getDb();

    /**
     * @param  string $class
     * @return void
     */
    function setFileTypesCatalogClass($class);

    /**
     * @return string
     */
    function getFileTypesCatalogClass();
}
