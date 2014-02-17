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

use Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class CommonStrategy extends AbstractThemeStrategy
{
    /**
     * @var string
     */
    protected static $side = 'frontend';

    /**
     * @param  \Zend\Mvc\MvcEvent $event
     * @return FrontendStrategy
     */
    public function update(MvcEvent $event)
    {
        $this->injectLayoutTemplate($event);
    }
}
