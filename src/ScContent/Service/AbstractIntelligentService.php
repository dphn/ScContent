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

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AbstractIntelligentService extends AbstractService
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
    */
    protected $value = [];

    /**
     * @var string
     */
    protected $valueFormat = "'%s'";

    /**
     * @var array
     */
    protected $errorMessages = [];

    /**
     * @return boolean
     */
    public function hasMessages()
    {
        return ! empty($this->errors);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->errors;
    }

    /**
     * @param  string $format
     * @return void
     */
    public function setValueFormat($format)
    {
        $this->valueFormat = $format;
    }

    /**
     * @return string
     */
    public function getValueFormat()
    {
        return $this->valueFormat;
    }

    /**
     * @param  mixed
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return AbstractIntelligentService
     */
    protected function setValue()
    {
        $translator = $this->getTranslator();
        $argc = func_num_args();
        $argv = func_get_args();
        for ($i = 0; $i < $argc; $i ++) {
            if (! (is_numeric($argv[$i]) || is_string($argv[$i]))) {
                throw new InvalidArgumentException(sprintf(
                    "Invalid %s argument to the method 'setValue'. Invalid argument type '%s'.",
                    $i + 1, gettype($argv[$i])
                ));
            }
            $value = $translator->translate(
                $argv[$i],
                $this->getTranslatorTextDomain()
            );
            $argv[$i] = sprintf($this->valueFormat, $value);
        }
        $this->value = $argv;
        return $this;
    }

    /**
     * @param  string $key
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return AbstractIntelligentService
     */
    protected function error($key)
    {
        if (! isset($this->errorMessages[$key])) {
            throw new InvalidArgumentException(
                sprintf("Unknown message key '%s'.", $key)
            );
        }
        $translator = $this->getTranslator();
        $message = $translator->translate(
            $this->errorMessages[$key],
            $this->getTranslatorTextDomain()
        );
        $value = $this->value;
        if (! is_array($value)) {
            $value = [$value];
        }
        $this->errors[] = vsprintf($message, $value);
        return $this;
    }
}
