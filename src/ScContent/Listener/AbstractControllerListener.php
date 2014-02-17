<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener;

use ScContent\Service\AbstractIntelligentService,
    ScContent\Exception\InvalidArgumentException,
    //
    Zend\Mvc\Controller\AbstractActionController,
    Zend\EventManager\EventInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractControllerListener extends AbstractIntelligentService
{
    /**
     * @var string
     */
    protected $redirectRoute = 'sc-admin/content-manager';

    /**
     * @var string
     */
    protected $redirectErrorRoute = 'sc-admin/content-manager';

    /**
     * @var array
     */
    protected $redirectRouteParams = [];

    /**
     * @param  string $route
     * @return void
     */
    public function setRedirectRoute($route)
    {
        $this->redirectRoute = $route;
    }

    /**
     * @return string
     */
    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    /**
     * @param  string $route
     * @return void
     */
    public function setRedirectErrorRoute($route)
    {
        $this->redirectErrorRoute = $route;
    }

    /**
     * @return string
     */
    public function getRedirectErrorRoute()
    {
        return $this->redirectErrorRoute;
    }

    /**
     * @param  array $params
     * @return void
     */
    public function setRedirectRouteParams($params)
    {
        $this->redirectRouteParams = $params;
    }

    /**
     * @return array
     */
    public function getRedirectRouteParams()
    {
        return $this->redirectRouteParams;
    }

    /**
     * @param  \Zend\EventManager\EventInterface $event
     * @return null|\Zend\Stdlib\Response
     */
    abstract public function process(EventInterface $event);

    /**
     * @param  \Zend\EventManager\EventInterface $event
     * @param  string $route optional
     * @param  array $params optional
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return \Zend\Http\Response
     */
    protected function redirect(
        EventInterface $event,
        $route = '',
        $params = []
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
        if (empty($params)) {
            $params = $this->getRedirectRouteParams();
        }
        return $target->redirect()
            ->toRoute($route, $params)
            ->setStatusCode(303);
    }
}
