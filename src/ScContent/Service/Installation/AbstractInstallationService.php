<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Installation;

use ScContent\Service\AbstractService,
    ScContent\Exception\InvalidArgumentException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractInstallationService extends AbstractService
{
    /**
     * @var mixed
     */
    protected $value = '';

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var array
     */
    protected $errorMessages = array();

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->errors;
    }

    /**
     * @param mixed $options optional
     * @return boolean
     */
    abstract public function process($options);

    /**
     * @param mixed $value
     * @return ScContent\Service\Installation\AbstractInstallationService
     */
    protected function setValue($value)
    {
        if ($this->hasTranslator() && $this->isTranslatorEnabled()) {
            $value = $this->getTranslator()->translate(
                $value,
                $this->getTranslatorTextDomain()
            );
        }
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $key
     * @throws ScContent\Exception\InvalidArgumentException
     * @return ScContent\Service\Installation\AbstractInstallationService
     */
    protected function error($key)
    {
        if (! isset($this->errorMessages[$key])) {
            throw new InvalidArgumentException(
                sprintf("Unknown message key '%s'.", $key)
            );
        }
        $error = $this->errorMessages[$key];
        if ($this->hasTranslator() && $this->isTranslatorEnabled()) {
            $error = $this->getTranslator()->translate(
                $error,
                $this->getTranslatorTextDomain()
            );
        }
        $this->errors[] = sprintf($error, '<code>' . $this->value . '</code>');
        return $this;
    }
}
