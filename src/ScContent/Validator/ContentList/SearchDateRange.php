<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\ContentList;

use Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class SearchDateRange extends AbstractValidator
{
    /**#@+
     * @const string
     */
    const MissingOptions   = 'Missing options';
    const MissingStartDate = 'Missing start date';
    const MissingEndDate   = 'Missing end date';
    /**#@-*/

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::MissingOptions
            => "Options for the date range search are not specified.",

        self::MissingStartDate
            => "The start date for the search is not specified.",

        self::MissingEndDate
            => "The end date for the search is not specified.",
    ];

    /**
     * @param  string $value
     * @param  array $conext
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if ('range' != $value) {
            return true;
        }
        if (! is_array($context) || empty($context)) {
            $this->error(self::MissingOptions);
            return false;
        }
        if (! array_key_exists('date_start', $context)
            || ! array_key_exists('date_end', $context)
        ) {
            $this->error(self::MissingOptions);
            return false;
        }
        if (empty($context['date_start']) && empty($context['date_end'])) {
            $this->error(self::MissingOptions);
            return false;
        }
        if (empty($context['date_start'])) {
            $this->error(self::MissingStartDate);
            return false;
        }
        if (empty($context['date_end'])) {
            $this->error(self::MissingEndDate);
            return false;
        }
        return true;
    }
}
