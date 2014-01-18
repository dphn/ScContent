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

use ScContent\Listener\AbstractListener,
    ScContent\Entity\AbstractContent,
    ScContent\Mapper\Back\LayoutListenerMapper,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\EventManager\EventInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Layout extends AbstractListener
{
    /**
     * @var ScContent\Mapper\Back\LayoutListenerMapper
     */
    protected $layoutMapper;

    /**
     * @param ScContent\Mapper\Back\LayoutListenerMapper $mapper
     * @return void
     */
    public function setLayoutMapper(LayoutListenerMapper $mapper)
    {
        $this->layoutMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\LayoutListenerMapper
     */
    public function getLayoutMapper()
    {
        if (! $this->layoutMapper instanceof LayoutListenerMapper) {
            throw new IoCException(
                'The layout mapper was not set.'
            );
        }
        return $this->layoutMapper;
    }

    /**
     * Creates a new layout for the contents in the database.
     * The visibility rules of widgets (the 'enabled' flag) are created
     * on the basis of the rules of the parent content.
     *
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function contentCreated(EventInterface $event)
    {
        $translator = $this->getTranslator();
        $mapper = $this->getLayoutMapper();
        if (is_null($event->getParam('tid'))) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Unknown transaction identifier. Missing event param 'tid'."
                )
            );
        }
        if (! $event->getParam('content')) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate(
                    "Missing event param '%s'."
                ),
                'content'
            ));
        }
        $content = $event->getParam('content');
        if (! $content instanceof AbstractContent) {
            throw new InvalidArgumentException(
                $translator->translate(
                    'Layout can be updated only for specific types of standard content.'
                )
            );
        }
        if ($content->getType() != 'category'
            && $content->getType() != 'article'
        ) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate(
                    "Invalid content type '%s'."
                ),
                $content->getType()
            ));
        }
        $mapper->registerContent(
            $content->getId(),
            $event->getParam('tid')
        );
    }

    /**
     * Whenever you relocate the content, is changing the rules of visibility
     * for widgets (the 'enabled' flag) based on the new parent content rules.
     *
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function contentRelocated(EventInterface $event)
    {
        $translator = $this->getTranslator();
        $mapper = $this->getLayoutMapper();
        if (is_null($event->getParam('tid'))) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Unknown transaction identifier. Missing event param 'tid'."
                )
            );
        }
        if (is_null($event->getParam('content'))) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate(
                    "Missing event param '%s'."
                ),
                'content'
            ));
        }
        $mapper->unregisterContent(
            $event->getParam('content'),
            false,
            $event->getParam('tid')
        );
    }

    /**
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function contentRemoved(EventInterface $event)
    {
        $translator = $this->getTranslator();
        $mapper = $this->getLayoutMapper();
        if (is_null($event->getParam('tid'))) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Unknown transaction identifier. Missing event param 'tid'."
                )
            );
        }
        if (is_null($event->getParam('content'))) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate(
                    "Missing event param '%s'."
                ),
                'content'
            ));
        }
        $mapper->unregisterContent(
            $event->getParam('content'),
            true,
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
        $mapper = $this->getLayoutMapper();
        if (is_null($event->getParam('tid'))) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Unknown transaction identifier. Missing event param 'tid'."
                )
            );
        }
        $mapper->unregisterCleanedContent($event->getParam('tid'));
    }
}
