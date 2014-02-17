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
interface ServiceDirInterface
{
    /**
     * @param  string $dir
     * @return void
     */
    function setAppAutoloadDir($dir);

    /**
     * @return string
     */
    function getAppAutoloadDir();

    /**
     * @param  string $dir
     * @return void
     */
    function setAppPublicDir($dir);

    /**
     * @return string
     */
    function getAppPublicDir();

    /**
     * @param  string $dir
     * @return void
     */
    function setAppUploadsDir($dir);

    /**
     * @return string
     */
    function getAppUploadsDir();
}
