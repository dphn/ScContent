<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Back;

use ScContent\Exception\IoCException,
    //
    Zend\EventManager\ListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListAggregate implements ListenerAggregateInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        if (! $this->serviceLocator instanceof ServiceLocatorInterface) {
            throw new IoCException(
                'The Service Locator was not set.'
            );
        }
        return $this->serviceLocator;
    }

    /**
     * @param  \Zend\EventManager\EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $sm = $this->getServiceLocator();
        $this->listeners[] = $events->attach(
            'reorder',
            function($e) use ($sm) {
                $listener = $sm->get('ScListener.Back.ContentListReorder');
                return $listener->process($e);
            }
        );
        $this->listeners[] = $events->attach(
            'move',
            function($e) use ($sm) {
                $listener = $sm->get('ScListener.Back.ContentListMove');
                return $listener->process($e);
            }
        );
        $this->listeners[] = $events->attach(
            'trash',
            function($e) use ($sm) {
                $listener = $sm->get('ScListener.Back.ContentListMoveToTrash');
                return $listener->process($e);
            }
        );
        $this->listeners[] = $events->attach(
            'recovery',
            function($e) use ($sm) {
                $listener = $sm->get('ScListener.Back.ContentListRecoveryFromTrash');
                return $listener->process($e);
            }
        );
        $this->listeners[] = $events->attach(
            'delete',
            function($e) use ($sm) {
                $listener = $sm->get('ScListener.Back.ContentListDelete');
                return $listener->process($e);
            }
        );
        $this->listeners[] = $events->attach(
            'clean',
            function($e) use ($sm) {
                $listener = $sm->get('ScListener.Back.ContentListClean');
                return $listener->process($e);
            }
        );
    }

    /**
     * @param  \Zend\EventManager\EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}
