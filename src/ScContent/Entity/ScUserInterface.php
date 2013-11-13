<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
interface ScUserInterface
{
    /**
     * @param string $locale
     * @return void
     */
    function setLocale($locale);

    /**
     * @return string
     */
    function getLocale();

    /**
     * @param string $timezone
     * @return void
     */
    function setTimezone($timezone);

    /**
     * @return string
     */
    function getTimezone();

    /**
     * @param integer $gmStamp
     * @return void
     */
    function setRegistered($gmStamp);

    /**
     * @return integer
     */
    function getRegistered();
}
