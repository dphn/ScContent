<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Mapper;

use Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class NestingValidator extends AbstractValidator
{
    /**
     * @const string
     */
    const NestingError = 'Nesting Error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NestingError => 'Invalid nesting types.'
    ];

    /**
     * Usage:
     * <code>
     *     isValid(array('source' => $source, 'destination' => $destination))
     * </code>
     * or
     * <code>
     *     isValid($source, $destination)
     * </code>
     *
     * @param mixed array | string $options
     * @param string $destination optional
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options = [])
    {
        if (! is_array($options)) {
            if (func_num_args() < 2) {
                throw new InvalidArgumentException(
                    "Missing 'destination' options."
                );
            }
            $temp = func_get_args();
            $source = $temp[0];
            $destination = $temp[1];
        } else {
            if (! array_key_exists('source', $options)) {
                throw new InvalidArgumentException(
                    "Missing 'source' option."
                );
            }
            if (! array_key_exists('destination', $options)) {
                throw new InvalidArgumentException(
                    "Missing 'destination' option."
                );
            }
            $source = $options['source'];
            $destination = $options['destination'];
        }
        if ('category' == $destination) {
            return true;
        }
        if ('article' == $destination && 'category' != $source) {
            return true;
        }
        $this->error(self::NestingError);
        return false;
    }
}
