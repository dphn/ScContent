<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Theme;

use ScContent\Controller\AbstractBack,
    ScContent\Controller\AbstractFront,
    ScContent\Controller\AbstractInstallation,
    //
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThemeResolver
{
    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function process(MvcEvent $event)
    {
        $app = $event->getApplication();
        $sm = $app->getServiceManager();

        $target = $event->getTarget();
        switch (true) {
        	case $target instanceof AbstractBack:
        	    $theme = $sm->get('ScListener.Theme.Backend');
        	    $theme->update($event);
        	    break;
        	case $target instanceof AbstractFront:
        	    $theme = $sm->get('ScListener.Theme.Frontend');
        	    $theme->update($event);
        	    break;
        	case $target instanceof AbstractInstallation:
        	    $theme = $sm->get('ScListener.Theme.Installation');
        	    $theme->update($event);
        	    break;
        }
    }
}
