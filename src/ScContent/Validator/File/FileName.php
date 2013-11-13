<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\File;

use Zend\Validator\AbstractValidator,
    Zend\Validator\Exception\InvalidArgumentException,
    Zend\Stdlib\ErrorHandler;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileName extends AbstractValidator
{
    /**
     * @var integer
     */
    protected $min = 1;

    /**
     * @var integer
     */
    protected $max = 128;

    /**
     * @var string
     */
    protected $pattern = '/^[a-z0-9]{1}[a-z0-9\.\_-\s]*$/i';

    /**#@+
     * @const string
     */
    const IllegalCharacters = 'Illegal characters';
    const ToLong            = 'To long';
    const ToShort           = 'To short';
    /**#@-*/

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::IllegalCharacters
            => "The file name '%value%' contains illegal characters.",

        self::ToLong
            => "The file name '%value%' is too long.",

        self::ToShort
            => "The file name '%value%' is too short.",
    );

    /**
     * @param integer $min
     * @return ScContent\Validator\File\FileName
     */
    public function setMin($min)
    {
        $this->min = (int) $min;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param integer $max
     * @return ScContent\Validator\File\FileName
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param string $pattern
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return ScContent\Validator\File\FileName
     */
    public function setPattern($pattern)
    {
        ErrorHandler::start();
        $this->pattern = (string) $pattern;
        $status = preg_match($this->pattern, "Test.txt");
        $e = ErrorHandler::stop();

        if (false === $status) {
            throw new InvalidArgumentException(
                "Internal error parsing the pattern '{$this->pattern}'",
                null,
                $e
            );
        }

        return $this;
    }

    /**
     * @param  string | array $value File name
     * @param  array $file  File data from \Zend\File\Transfer\Transfer optional
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return bool
     */
    public function isValid($value, $file = null)
    {
        if (is_string($value) && is_array($file)) {
            // Legacy Zend\Transfer API support
            $filename = $file['name'];
        } elseif (is_array($value)) {
            if (! isset($value['name'])) {
                throw new InvalidArgumentException(
                    'Value array must be in $_FILES format'
                );
            }
            $filename = $value['name'];
        } else {
            $filename = basename($value);
        }
        $this->setValue($filename);

        if (! preg_match($this->pattern, $filename)) {
            $this->error(self::IllegalCharacters);
            return false;
        }

        if (strlen($filename) > $this->max) {
            $this->error(self::ToLong);
            return false;
        }

        if (strlen($filename) < $this->min) {
            $this->error(self::ToShort);
            return false;
        }
        return true;
    }
}
