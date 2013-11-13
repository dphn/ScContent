<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\View\Helper;

use ScContent\Service\ScDateTime,
    //
    Zend\I18n\View\Helper\DateFormat,
    Zend\View\Helper\AbstractHelper,
    //
    IntlDateFormatter;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class DateTime extends AbstractHelper
{
    /**
     * @var ScContent\Service\ScDateTime
     */
    protected $datetime;

    /**
     * @var Zend\I18n\View\Helper\DateFormat
     */
    protected $dateformat;

    /**
     * @param ScContent\Service\ScDateTime $datetime
     * @param Zend\I18n\View\Helper\DateFormat $dateformat
     */
    public function __construct(ScDateTime $datetime, DateFormat $dateformat)
    {
        $this->datetime = $datetime;
        $this->dateformat = $dateformat;
    }

    /**
     * @param integer $gmStamp
     * @return string
     */
    public function fromGm($gmStamp)
    {
        return $this->datetime->fromGm($gmStamp);
    }

    /**
     * @param integer $gmStamp
     * @return string
     */
    public function getDate($gmStamp)
    {
        $stamp = $this->datetime->fromGm($gmStamp);
        $datetime = $this->dateformat->__invoke(
            $stamp,
            IntlDateFormatter::SHORT,
            IntlDateFormatter::NONE
        );
        return $datetime;
    }

    /**
     * @param integer $gmStamp
     * @return string
     */
    public function getTime($gmStamp)
    {
        $stamp = $this->datetime->fromGm($gmStamp);
        $datetime = $this->dateformat->__invoke(
            $stamp,
            IntlDateFormatter::NONE,
            IntlDateFormatter::SHORT
        );
        return $datetime;
    }

    /**
     * @param integer $gmStamp
     * @return string
     */
    public function getDateTime($gmStamp)
    {
        $stamp = $this->datetime->fromGm($gmStamp);
        $datetime = $this->dateformat->__invoke(
            $stamp,
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::SHORT
        );
        return $datetime;
    }
}
