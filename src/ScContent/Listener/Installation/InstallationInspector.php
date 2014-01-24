<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Installation;

use ScContent\Listener\GuardExceptionStrategy,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    //
    Zend\Validator\ValidatorPluginManager,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationInspector extends AbstractListenerAggregate
{
    /**
     * @var Zend\Validator\ValidatorPluginManager
     */
    protected $validatorManager;

    /**
     * @var ScContent\Listener\GuardExceptionStrategy
     */
    protected $guardExceptionStrategy;

    /**
     * @var array
     */
    protected $queue = [];

    /**
     * @var array
     */
    protected $current = [];

   /**
    * @param Zend\Validator\ValidatorPluginManager $validatorManager
    * @return void
    */
    public function setValidatorManager(ValidatorPluginManager $manager)
    {
        $this->validatorManager = $manager;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Validator\ValidatorPluginManager
     */
    public function getValidatorManager()
    {
        if (! $this->validatorManager instanceof ValidatorPluginManager) {
            throw new IoCException(
                'The validator manager was not set.'
            );
        }
        return $this->validatorManager;
    }

    /**
     * @param ScContent\Listener\GuardExceptionStrategy $strategy
     * @return void
     */
    public function setGuardExceptionStrategy(GuardExceptionStrategy $strategy)
    {
        $this->guardExceptionStrategy = $strategy;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Listener\GuardExceptionStrategy
     */
    public function getGuardExceptionStrategy()
    {
        if (! $this->guardExceptionStrategy instanceof GuardExceptionStrategy) {
            throw new IoCException(
                'The guard exception strategy was not set.'
            );
        }
        return $this->guardExceptionStrategy;
    }

    /**
     * @param Zend\EventManager\EventManagerInterface
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'inspect'],
            -PHP_INT_MAX
        );
    }

    /**
     * @api
     *
     * @param array $options
     * @return InstallationInspector
     */
    public function setup($options)
    {
        if (! is_array($options) || empty($options)) {
            return $this;
        }
        $this->queue[] = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getCurrentSetup()
    {
        return $this->current;
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function inspect(MvcEvent $event)
    {
        $validatorManager = $this->getValidatorManager();
        $guardExceptionStrategy = $this->getGuardExceptionStrategy();
        while (! empty($this->queue)) {
            $controller = 'ScController.Installation.Default';
            $action = 'index';
            $options = $this->current = array_shift($this->queue);

            if (! isset($options['steps']) || ! is_array($options['steps'])) {
                throw new InvalidArgumentException(
                    "Missing configuration options 'steps'."
                );
            }

            foreach ($options['steps'] as $stepNumber => &$step) {
                if (! isset($step['chain']) || ! is_array($step['chain'])) {
                    throw new InvalidArgumentException(sprintf(
                        "Missing configuration option 'chain' for step '%s'.",
                        $stepNumber
                    ));
                }
                foreach ($step['chain'] as $memberName => &$member) {
                    if (! isset($member['validator'])) {
                        throw new InvalidArgumentException(sprintf(
                            "For step '%s' chain element '%s' validator is not specified.",
                            $stepNumber, $memberName
                        ));
                    }
                    if (! isset($member['service']) &&
                        ! (isset($member['controller']) && isset($member['action']))
                    ) {
                        throw new InvalidArgumentException(sprintf(
                            "For step '%s' member '%s' must be specified 'service' or 'controller' and 'action'.",
                            $stepNumber, $memberName
                        ));
                    }
                    $isValid = true;
                    $validator = $validatorManager->get($member['validator']);

                    $batch = null;
                    if (isset($member['batch'])) {
                        $batch = &$member['batch'];
                    }
                    if (isset($batch['items']) && is_array($batch['items'])) {
                        foreach ($batch['items'] as &$item) {
                            if (! $validator->isValid($item)) {
                                if (isset($member['controller'])
                                     && isset($member['action'])
                                ) {
                                    $controller = $member['controller'];
                                    $action = $member['action'];
                                }
                                $isValid = false;
                                break;
                            }
                        }
                    } elseif (! $validator->isValid($batch)) {
                        if (isset($member['controller'])
                             && isset($member['action'])
                        ) {
                            $controller = $member['controller'];
                            $action = $member['action'];
                        }
                        $isValid = false;
                    }
                    if (! $isValid) {
                        $guardExceptionStrategy->setEnabled(false);
                        $redirect = $event->getRequest()->getRequestUri();
                        $routeMatch = $event->getRouteMatch();
                        $routeMatch->setParam('redirect', $redirect)
                            ->setParam('controller', $controller)
                            ->setParam('action', $action)
                            ->setParam('member', $memberName)
                            ->setParam('step', $stepNumber);

                        $event->setRouteMatch($routeMatch);
                        return;
                    }
                }
            }
        }
    }
}
