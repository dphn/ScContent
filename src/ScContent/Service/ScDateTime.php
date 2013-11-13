<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service;

use IntlDateFormatter,
    DateTimeZone,
    DateTime;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ScDateTime implements ScDateTimeInterface
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var integer
     */
    protected $stamp = 0;

    /**
     * @var integer
     */
    protected $gmStamp = 0;

    /**#@+
     * @const integer
     */
    const MinuteRatio = 60;
    const HourRatio   = 3600;
    const DayRatio    = 86400;
    /**#@-*/

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateTime = new DateTime();
        $this->stamp = $this->dateTime->getTimeStamp();
        $this->gmStamp = $this->stamp - $this->dateTime->getOffset();
    }

    /**
     * @param string $name DateTimeZone identifier
     * @return string Old DateTimeZone identifier
     */
    public function setTimeZone($name)
    {
        date_default_timezone_set($name);
        $oldName = $this->getTimeZone();
        $this->dateTime->setTimeZone(new DateTimeZone($name));
        $this->stamp = $this->dateTime->getTimeStamp();
        $this->gmStamp = $this->stamp - $this->dateTime->getOffset();
        return $oldName;
    }

    /**
     * @return string Current DateTimeZone identifier
     */
    public function getTimeZone()
    {
        return $this->dateTime->getTimeZone()->getName();
    }

    /**
     * @param boolean $new optional If you need to get the updated value of timestamp
     * @return integer DateTime stamp (in Unix format) for current locale
     */
    public function stamp($new = false) {
        if ($new) {
            return $this->dateTime->getTimeStamp();
        }
        return $this->stamp;
    }

    /**
     * @return integer GMT offset of the current locale
     */
    public function offset() {
        return $this->dateTime->getOffset();
    }

    /**
     * @param boolean $new optional If you need to get the updated value of timestamp
     * @return GMT (in Unix format)
     */
    public function gmStamp($new = false) {
        if ($new) {
            return $this->dateTime->getTimeStamp() - $this->dateTime->getOffset();
        }
        return $this->gmStamp;
    }

    /**
     * @param integer $stamp optional GMT (in Unix format)
     * @param boolean $new optional If you need to get the updated value of timestamp
     * @return DataTime stamp (in Unix format) for current locale
     */
    public function fromGm($stamp = null, $new = false) {
        if (is_null($stamp)) {
            if ($new) {
                $stamp = $this->dateTime->getTimeStamp() - $this->dateTime->getOffset();
            } else {
                $stamp = $this->gmStamp;
            }
            return $stamp;
        }
        return $stamp + $this->dateTime->getOffset();
    }
}
