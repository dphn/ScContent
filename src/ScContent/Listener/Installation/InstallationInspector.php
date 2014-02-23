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
    ScContent\Factory\Options\Installation\InstallationOptionsFactory,
    ScContent\Options\Installation\Installation,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    //
    Zend\Validator\ValidatorPluginManager,
    Zend\Session\Container,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationInspector extends AbstractListenerAggregate
{
    /**
     * @var \Zend\Validator\ValidatorPluginManager
     */
    protected $validatorManager;

    /**
     * @var \ScContent\Listener\GuardExceptionStrategy
     */
    protected $guardExceptionStrategy;

    /**
     * @var \ScContent\Options\Installation\Installation[string]
     */
    protected $queue = [];

    /**
     * @var \ScContent\Options\Installation\Installation
     */
    protected $current = [];

    /**
     * @var boolean
     */
    protected $installationGuardIsEnabled = false;

   /**
    * @param  \Zend\Validator\ValidatorPluginManager $validatorManager
    * @return void
    */
    public function setValidatorManager(ValidatorPluginManager $manager)
    {
        $this->validatorManager = $manager;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \Zend\Validator\ValidatorPluginManager
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
     * @param  \ScContent\Listener\GuardExceptionStrategy $strategy
     * @return void
     */
    public function setGuardExceptionStrategy(GuardExceptionStrategy $strategy)
    {
        $this->guardExceptionStrategy = $strategy;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Listener\GuardExceptionStrategy
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
     * @param  \Zend\EventManager\EventManagerInterface
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'inspect'],
            -PHP_INT_MAX
        );

        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'afterInspect'],
            -PHP_INT_MAX
        );
    }

    /**
     * @api
     *
     * @param  string $moduleName
     * @param  \ScContent\Options\Installation\Installation|array $options
     * @return InstallationInspector
     */
    public function setup($moduleName, $options)
    {
        if (is_array($options)) {
            $this->queue[] = InstallationOptionsFactory::make($moduleName, $options);
            return $this;
        }
        if ($options instanceof Installation) {
            $this->queue[] = $options;
            return $this;
        }
        throw new InvalidArgumentException(
            "Installation options should be an array or an instance of class 'ScContent\Options\Installation\Installation'."
        );
    }

    /**
     * @return \ScContent\Options\Installation\Installation
     */
    public function getCurrentSetup()
    {
        return $this->current;
    }

    /**
     * @param  \Zend\Mvc\MvcEvent $event
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function inspect(MvcEvent $event)
    {
        $validatorManager = $this->getValidatorManager();

        while (! empty($this->queue)) {
            $installation = $this->current = array_shift($this->queue);
            foreach ($installation as $stepName => $step) {
                foreach ($step as $memberName => $member) {
                    $validator = $validatorManager->get(
                        $member->getValidator()
                    );
                    foreach ($member as $batch) {
                        if (! $validator->isValid($batch)) {
                            $this->enableInstallationGuard(true);

                            $installation->setCurrentStepName($stepName);
                            $step->setCurrentMemberName($memberName);

                            $routeMatch = $event->getRouteMatch();
                            $routeMatch
                                ->setParam('controller', $member->getController())
                                ->setParam('action',     $member->getAction());

                            $event->setParam('installation', $installation);

                            $event->setRouteMatch($routeMatch);

                            return;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param  \Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function afterInspect(MvcEvent $event)
    {
        if (! $this->installationGuardIsEnabled) {
            return;
        }
        $application    = $event->getApplication();
        $serviceLocator = $application->getServiceManager();

        $zfcUserSessionContainer = new Container('Zend_Auth');
        $storage = $zfcUserSessionContainer->getManager()->getStorage();
        $storage[$zfcUserSessionContainer->getName()] = [];

        $installationGuardListener = $serviceLocator->get(
            'ScListener.Installation.Guard'
        );
        $installationGuardListener->process($event);
    }

    /**
     * @param  boolean $flag
     * @return void
     */
    protected function enableInstallationGuard($flag = true)
    {
        $guardExceptionStrategy = $this->getGuardExceptionStrategy();
        $guardExceptionStrategy->setEnabled(! $flag);
        $this->installationGuardIsEnabled = $flag;
    }
}
