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

use Zend\View\Model\ModelInterface as ViewModel,
    Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\Mvc\MvcEvent,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class GuardExceptionStrategy extends AbstractListenerAggregate
{
    /**
     * @var string
     */
    const Error = 'guard-exception';

    /**
     * @var boolean
     */
    protected $enabled = false;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @param Zend\EventManager\EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            PHP_INT_MAX
        );
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'onDispatchError'],
            -5000
        );
    }

    /**
     * @param boolean $flag optional default true
     * @return void
     */
    public function setEnabled($flag = true, Exception $e = null)
    {
        $this->enabled = (bool) $flag;
        if (! is_null($e) && is_null($this->getException())) {
            $this->exception = $e;
        }
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return null | Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function onDispatch(MvcEvent $event)
    {
        if (! $this->isEnabled()) {
            return;
        }
        $application = $event->getApplication();
        $eventManager = $application->getEventManager();

        $event->setError(static::Error);

        $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function onDispatchError(MvcEvent $event)
    {
        $error = $event->getError();
        if ($error !== static::Error) {
            return;
        }
        $model = $event->getResult();
        if (! $model instanceof ViewModel) {
            return;
        }

        $exception = $this->getException();

        if ($exception instanceof Exception) {
            $model->reason = $this->getException()->getMessage();
            $model->exception = $exception;
        }
    }
}
