<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service;

use ScContent\Service\ScDatetimeInterface,
    ScContent\Entity\ScUserInterface,
    //
    Zend\I18n\View\Helper\DateFormat,
    Zend\I18n\Translator\Translator,
    Zend\Session\Container,
    //
    IntlDateFormatter,
    Locale;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Localization
{
    /**
     * @var Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * @var Zend\I18n\View\Helper\DateFormat
     */
    protected $dateformat;

    /**
     * @var ScContent\Service\ScDatetimeInterface
     */
    protected $datetime;

    /**
     * @var Zend\Session\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @param Zend\I18n\Translator\Translator  $translator
     * @param Zend\I18n\View\Helper\DateFormat $dateformat
     * @param ScContent\Service\ScDatetimeInterface $datetime
     */
    public function __construct(
        Translator $translator,
        DateFormat $dateformat,
        ScDatetimeInterface $datetime
    ) {
        $this->translator = $translator;
        $this->dateformat = $dateformat;
        $this->datetime = $datetime;

        $container = new Container('sc_l10n');
        if (isset($container->locale)) {
            Locale::setDefault($container->locale);
            $this->translator->setLocale($container->locale);
            $this->dateformat->setLocale($container->locale);
        } else {
            $locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (is_null($locale)) {
                $locale = Locale::getDefault();
            }
            $this->translator->setLocale($locale)->setFallbackLocale(
                Locale::getDefault()
            );
            $this->dateformat->setLocale($locale);
        }
        if (isset($container->timezone)) {
            $this->datetime->setTimeZone($container->timezone);
            $this->dateformat->setTimezone($container->timezone);
        } else {
            $this->dateformat->setTimezone($this->datetime->getTimeZone());
        }
        $this->container = $container;
    }

    /**
     * @param ScContent\Entity\ScUserInterface $user
     * @return void
     */
    public function save(ScUserInterface $user)
    {
        $container = $this->container;
        $container->locale = $user->getLocale();
        $container->timezone = $user->getTimezone();

        $this->translator->setLocale($user->getLocale());
        $this->datetime->setTimeZone($user->getTimezone());
        $this->dateformat->setLocale($user->getLocale());
        $this->dateformat->setTimezone($user->getTimezone());
    }

    /**
     * @return Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return ScContent\Service\ScDatetimeInterface
     */
    public function getDateTime()
    {
        return $this->datetime;
    }

    /**
     * @return Zend\I18n\View\Helper\DateFormat
     */
    public function getDateFormat()
    {
        return $this->dateformat;
    }

    /**
     * @param integer $dateType
     * @param integer $timeType
     * @return string
     */
    public function getDateTimePattern(
        $dateType = IntlDateFormatter::SHORT,
        $timeType = IntlDateFormatter::SHORT
    ) {
        $formatter = new IntlDateFormatter(
            $this->getLocale(),
            $dateType,
            $timeType
        );
        return $formatter->getPattern();
    }

    /**
     * @param integer $dateType
     * @return string
     */
    public function getDatePattern($dateType = IntlDateFormatter::SHORT)
    {
        $formatter = new IntlDateFormatter(
            $this->getLocale(),
            $dateType,
            IntlDateFormatter::NONE
        );
        return $formatter->getPattern();
    }

    /**
     * @param integer $timeType
     * @return string
     */
    public function getTimePattern($timeType = IntlDateFormatter::SHORT)
    {
        $formatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            $timeType
        );
        return $formatter->getPattern();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        $container = $this->container;
        if (isset($container->locale)) {
            return $container->locale;
        }
        return Locale::getDefault();
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return Locale::getRegion($this->getLocale());
    }

    /**
     * @return string
     */
    public function getPrimaryLanguage()
    {
        return Locale::getPrimaryLanguage($this->getLocale());
    }
}
