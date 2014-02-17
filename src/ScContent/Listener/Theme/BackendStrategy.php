<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Theme;

use ScContent\Controller\AbstractBack,
    ScContent\Exception\DomainException,
    //
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class BackendStrategy extends AbstractThemeStrategy
{
    /**
     * @var string
     */
    protected static $side = 'backend';

    /**
     * @param  \Zend\Mvc\MvcEvent
     * @throws \ScContent\Exception\DomainException
     * @return void
     */
    public function update(MvcEvent $event)
    {
        $target = $event->getTarget();
        if (! $target instanceof AbstractBack) {
            throw new DomainException(sprintf(
                "Backend theme strategy is not applicable to current target '%s'.",
                is_object($target) ? get_class($target) : gettype($target)
            ));
        }
        $this->injectContentTemplate($event)->injectLayoutTemplate($event);
    }
}
