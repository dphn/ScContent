<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Back;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
use ScContent\Listener\AbstractListener,
    ScContent\Service\Back\ContentListOptionsProvider as OptionsProvider,
    ScContent\Mapper\Back\ContentListOperationAbstract as Mapper,
    //
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException,
    ScContent\Exception\IoCException,
    //
    Zend\Mvc\Controller\AbstractActionController,
    Zend\EventManager\EventInterface;

abstract class ContentListAbstractListener extends AbstractListener
{
    /**
     * @var ScContent\Service\Back\ContentListOptionsProvider
     */
    protected $optionsProvider;

    /**
     * @var ScContent\Mapper\Back\ContentListOperationAbstract
     */
    protected $mapper;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var array
     */
    protected $value = array();

    /**
     * @var array
     */
    protected $errorMessages = array();

    /**
     * @var string
     */
    protected $redirectRoute = 'sc-admin/content-manager';

    /**
     * @param ScContent\Service\Back\ContentListOptionsProvider $provider
     * @return ContentListAbstractListener
     */
    public function setOptionsProvider(OptionsProvider $provider)
    {
        $this->optionsProvider = $provider;
        return $this;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Service\Back\ContentListOptionsProvider
     */
    public function getOptionsProvider()
    {
        if (! $this->optionsProvider instanceof OptionsProvider) {
            throw new IoCException(
                'The options provider was not set.'
            );
        }
        return $this->optionsProvider;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\ContentListOperationAbstract
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof Mapper) {
            throw new IoCException(
                'The mapper was not set.'
            );
        }
        return $this->mapper;
    }

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
     * @param mixed
     * @return ContentListAbstractListener
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
                $argv[$i] = $translator->translate($arv[$i]);
            }
        }
        $this->value = $argv;
        return $this;
    }

    /**
     * @param string $key
     * @throws ScContent\Exception\InvalidArgumentException
     * @return ContentListAbstractListener
     */
    protected function error($key)
    {
        if (! isset($this->errorMessages[$key])) {
            throw new InvalidArgumentException(
                sprintf("Unknown message key '%s'.", $key)
            );
        }
        $translator = $this->getTranslator();
        $message = $translator->translate($this->errorMessages[$key]);
        $value = $this->value;
        if (! is_array($value)) {
            $value = array($value);
        }
        $this->errors[] = vsprintf($message, $value);
        return $this;
    }

    /**
     * @param Zend\EventManager\EventInterface $event
     */
    abstract public function process(EventInterface $event);

    /**
     * @param Zend\EventManager\EventInterface $event
     * @param string $route optional
     * @param array $params optional
     * @throws ScContent\Exception\InvalidArgumentException
     * @return Zend\Http\Response
     */
    protected function redirect(
        EventInterface $event,
        $route = '',
        $params = array()
    ) {
        $target = $event->getTarget();
        if (! $target instanceof AbstractActionController) {
            throw new InvalidArgumentException(
                'The event target must be an instance of the AbstractActionController.'
            );
        }
        if ($this->hasMessages()) {
            $storage = $target->flashMessenger();
            foreach ($this->getMessages() as $message) {
                $storage->addMessage($message);
            }
        }
        if (empty($route)) {
            $route = $this->redirectRoute;
        }
        return $target->redirect()
            ->toRoute($route, $params)
            ->setStatusCode(303);
    }
}
