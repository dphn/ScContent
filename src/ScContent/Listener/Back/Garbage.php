<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Back;

use ScContent\Listener\AbstractListener,
    ScContent\Mapper\Back\GarbageMapper,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\EventManager\EventInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Garbage extends AbstractListener
{
    /**
     * @var ScContent\Mapper\Back\GarbageMapper
     */
    protected $garbageMapper;

    /**
     * @param ScContent\Mapper\Back\GarbageMapper $mapper
     * @return void
     */
    public function setGarbageMapper(GarbageMapper $mapper)
    {
        $this->garbageMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\GarbageMapper
     */
    public function getGarbageMapper()
    {
        if (! $this->garbageMapper instanceof GarbageMapper) {
            throw new IoCException(
                'The garbage mapper was not set.'
            );
        }
        return $this->garbageMapper;
    }

    /**
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function contentRemoved(EventInterface $event)
    {
        $translator = $this->getTranslator();
        $mapper = $this->getGarbageMapper();
        if (is_null($event->getParam('tid'))) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Unknown transaction identifier. Missing event param 'tid'."
                )
            );
        }
        if (is_null($event->getParam('content'))) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Missing event param 'content'."
                )
            );
        }
        $mapper->registerRemovedGarbage(
            $event->getParam('content'),
            $event->getParam('tid')
        );
    }

    /**
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function contentCleaned(EventInterface $event)
    {
        $translator = $this->getTranslator();
        $mapper = $this->getGarbageMapper();
        if (is_null($event->getParam('tid'))) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Unknown transaction identifier. Missing event param 'tid'."
                )
            );
        }
        $mapper->registerCleanedGarbage($event->getParam('tid'));
    }
}
