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

use ScContent\Exception\InvalidArgumentException,
    //
    Zend\Validator\ValidatorPluginManager,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationInspector
{
    /**
     * @var Zend\Validator\ValidatorPluginManager
     */
    protected $validatorManager;

    /**
     * @var array
     */
    protected $queue = array();

    /**
     * Constructor
     *
     * @param Zend\Validator\ValidatorPluginManager $validatorManager
     */
    public function __construct(ValidatorPluginManager $validatorManager)
    {
        $this->validatorManager = $validatorManager;
    }

    /**
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
     * @param Zend\Mvc\MvcEvent $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function inspect(MvcEvent $event)
    {
        while (! empty($this->queue)) {
            $controller = 'ScController.Installation.Default';
            $action = 'index';
            $options = array_shift($this->queue);
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
                    $validator = $this->validatorManager->get($member['validator']);

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
