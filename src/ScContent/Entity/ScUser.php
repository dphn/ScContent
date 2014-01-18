<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity;

use ZfcUser\Entity\User,
    //
    DateTime,
    Locale;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ScUser extends User implements ScUserInterface
{
    /**
     * @var string
     */
    protected $locale = 'en_GB';

    /**
     * @var string
     */
    protected $timezone = 'UTC';

    /**
     * @var integer
     */
    protected $registered = 0;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $datetime = new DateTime();
        $this->timezone = $datetime->getTimezone()->getName();
        $this->registered = $datetime->getTimeStamp() - $datetime->getOffset();

        $defaultLocale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if (! $defaultLocale) {
            $defaultLocale = Locale::getDefault();
        }
        $this->locale = $defaultLocale;
    }

    /**
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $timezone
     * @return void
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param integer
     * @return void
     */
    public function setRegistered($gmStamp)
    {
        $this->registered = $gmStamp;
    }

    /**
     * @return integer
     */
    public function getRegistered()
    {
        return $this->registered;
    }
}
