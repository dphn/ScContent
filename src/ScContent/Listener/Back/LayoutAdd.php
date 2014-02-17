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

use ScContent\Listener\AbstractControllerListener,
    //
    Zend\EventManager\EventInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutAdd extends AbstractControllerListener
{
    /**
     * @var string
     */
    protected $redirectRoute = 'sc-admin/layout/add';

    /**
     * @param  \Zend\EventManager\EventInterface $event
     * @return \Zend\Stdlib\Response
     */
    public function process(EventInterface $event)
    {
        $params = [];
        if ($event->getParam('theme')) {
            $params['theme'] = $event->getParam('theme');
        }
        if ($event->getParam('name')) {
            $params['name'] = $event->getParam('name');
        }
        $this->setRedirectRouteParams($params);
        return $this->redirect($event);
    }
}
