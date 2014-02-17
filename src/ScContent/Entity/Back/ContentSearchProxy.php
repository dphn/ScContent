<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Back;

use ScContent\Entity\AbstractEntity,
    ScContent\Service\Localization,
    ScContent\Exception\RuntimeException,
    //
    IntlDateFormatter,
    Traversable;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentSearchProxy extends AbstractEntity
{
    /**
     * @var \ScContent\Service\Localization
     */
    protected $l10n;

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var string
     */
    protected $textSource = 'title';

    /**
     * @var string
     */
    protected $dateType = 'unknown';

    /**
     * @var string
     */
    protected $dateStart = '';

    /**
     * @var string
     */
    protected $dateEnd = '';

    /**
     * @var string
     */
    protected $modificationType = 'created';

    /**
     * @var string
     */
    protected $userName = '';

    /**
     * @var string
     */
    protected $userSource = 'username';

    /**
     * @var string
     */
    protected $userType = 'author';

    /**
     * @var integer
     */
    protected $calculatedDateStart = 0;

    /**
     * @var integer
     */
    protected $calculatedDateEnd = 0;

    /**
     * Constructor
     *
     * @param \ScContent\Service\Localization
     * @param null|array|\Traversable $data optional default null
     */
    public function __construct(Localization $l10n, $data = null)
    {
        $this->l10n = $l10n;
        if(is_array($data) || $data instanceof Traversable) {
            $this->exchangeArray($data);
        }
    }

    /**
     * Reset all options.
     *
     * @return void
     */
    public function clean()
    {
        $data = get_class_vars(__CLASS__);
        $this->exchangeArray($data);
    }

    /**
     * @return boolean
     */
    public function hasText()
    {
        return ! empty($this->text);
    }

    /**
     * @param  string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param  string $source
     * @return void
     */
    public function setTextSource($source)
    {
        $this->textSource = $source;
    }

    /**
     * @return string
     */
    public function getTextSource()
    {
        return $this->textSource;
    }

    /**
     * @return boolean
     */
    public function hasDate()
    {
        if ($this->dateType == 'unknown'
            || ($this->dateType == 'range'
                && (!$this->dateStart || !$this->dateEnd)
                )
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param  string $type
     * @return void
     */
    public function setDateType($type)
    {
        $this->dateType = $type;
    }

    /**
     * @return string
     */
    public function getDateType()
    {
        return $this->dateType;
    }

    /**
     * @param  string $start
     * @return void
     */
    public function setDateStart($start)
    {
        $this->dateStart = $start;
    }

    /**
     * @return string
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param  string $end
     * @return void
     */
    public function setDateEnd($end)
    {
        $this->dateEnd = $end;
    }

    /**
     * @return string
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param  string $type
     * @return void
     */
    public function setModificationType($type)
    {
        $this->modificationType = $type;
    }

    /**
     * @return string
     */
    public function getModificationType()
    {
        return $this->modificationType;
    }

    /**
     * @return boolean
     */
    public function hasUserName()
    {
        return ! empty($this->userName);
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setUserName($name)
    {
        $this->userName = $name;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param  string $source
     * @return void
     */
    public function setUserSource($source)
    {
        $this->userSource = $source;
    }

    /**
     * @return string
     */
    public function getUserSource()
    {
        return $this->userSource;
    }

    /**
     * @param  string $type
     * @return void
     */
    public function setUserType($type)
    {
        $this->userType = $type;
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        if (! $this->hasText()
                && ! $this->hasDate()
                && ! $this->hasUserName()
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return integer
     */
    public function calculateDateStart()
    {
        if ($this->calculatedDateStart) {
            return $this->calculatedDateStart;
        }
        $dateStart = 0;
        $datetime = $this->l10n->getDateTime();
        switch ($this->dateType) {
            case 'week':
            case 'month':
                $dateStart = strtotime('last ' . $this->dateType);
                break;
            case 'range':
                $formatter = new IntlDateFormatter(
                    $this->l10n->getLocale(),
                    IntlDateFormatter::SHORT,
                    IntlDateFormatter::NONE
                );
                $dateStart = $formatter->parse($this->dateStart);
                break;
            default:
                throw new RuntimeException(
                    'Start date can not be calculated.'
                );
                break;
        }
        $this->calculatedDateStart = $dateStart - $datetime->offset();
        return $this->calculatedDateStart;
    }

    /**
     * @return integer
     */
    public function calculateDateEnd()
    {
        if ($this->calculatedDateEnd) {
            return $this->calculatedDateEnd;
        }
        $dateEnd = 0;
        $datetime = $this->l10n->getDateTime();
        switch ($this->dateType) {
            case 'week':
            case 'month':
                $dateEnd = strtotime('today');
                break;
            case 'range':
                $formatter = new IntlDateFormatter(
                    $this->l10n->getLocale(),
                    IntlDateFormatter::SHORT,
                    IntlDateFormatter::NONE
                );
                $dateEnd = $formatter->parse($this->dateEnd);
                break;
            default:
                throw new RuntimeException(
                    'End date can not be calculated.'
                );
                break;
        }
        $dateEnd += $datetime::DayRatio;
        $this->calculatedDateEnd = $dateEnd - $datetime->offset();
        return $this->calculatedDateEnd;
    }
}
