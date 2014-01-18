<?php

namespace ScContent\Listener;

use Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\View\Model\ViewModel,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent;

class SantizeOutputListener extends AbstractListenerAggregate
{
    /**
     * @param Zend\EventManager\EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH,
            [$this, 'onFinish'],
            5000
        );
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function onFinish(MvcEvent $event)
    {
        if ($event->getResult() instanceof ViewModel) {
            $response = $event->getResponse();
            $response->setContent(
                $this->santizeHtml($response->getContent())
            );
            return;
        }
    }

    /**
     * @param string $html
     * @return string
     */
    protected function santizeHtml($html)
    {
        preg_match_all('!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!', $html, $pre);
        $html = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $html);
        $search = [
            '/<!--.*?-->/',           // remove html comments
            '/\r?\n\s*\r?\n/m',    // remove empty lines
        ];
        $replace = [
            '',
            "\n",
        ];
        $html = preg_replace($search, $replace, $html);
        if (! empty($pre[0])) {
            foreach ($pre[0] as $tag) {
                $html = preg_replace('!#pre#!', $tag, $html,1);
            }
        }
        return $html;
    }
}
